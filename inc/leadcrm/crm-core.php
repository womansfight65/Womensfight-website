<?php
/**
 * Lead CRM — core: tables, role/capability, constants, query helpers.
 *
 * Fully separate module (own tables, own front-end login page, own
 * AJAX handlers) — does not touch Client Projects (wf_project), the
 * Academy's old simple lead list (wfa_lead, inc/academy-leads.php), or
 * WF Simple Accounts' own tables. The only connection to Accounts is
 * a single reusable helper call (wfa_accounts_add_transaction) used
 * when an Admission's fee/commission is recorded — see crm-handlers.php.
 *
 * Four tables, all new:
 * - {$wpdb->prefix}wfa_crm_leads         — one row per lead
 * - {$wpdb->prefix}wfa_crm_conversations — append-only contact history
 * - {$wpdb->prefix}wfa_crm_followups     — follow-up schedule
 * - {$wpdb->prefix}wfa_crm_admissions    — one row per converted lead,
 *   with fee + commission (Life Support IT Institute) details
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WFA_CRM_DB_VERSION', '1.0' );
define( 'WFA_CRM_CAP', 'wfa_access_crm' );

function wfa_crm_leads_table() {
	global $wpdb;
	return $wpdb->prefix . 'wfa_crm_leads';
}
function wfa_crm_conversations_table() {
	global $wpdb;
	return $wpdb->prefix . 'wfa_crm_conversations';
}
function wfa_crm_followups_table() {
	global $wpdb;
	return $wpdb->prefix . 'wfa_crm_followups';
}
function wfa_crm_admissions_table() {
	global $wpdb;
	return $wpdb->prefix . 'wfa_crm_admissions';
}

/**
 * Table creation/upgrade — one dbDelta() call per table (a combined
 * call previously caused a real, hard-to-notice bug in the Accounts
 * module: the second CREATE TABLE silently failed to apply). Safe to
 * run on every request; only ever creates, never alters/drops existing
 * data, and is gated by a stored version so it's a no-op once current.
 */
function wfa_crm_maybe_upgrade_db() {
	if ( get_option( 'wfa_crm_db_version' ) === WFA_CRM_DB_VERSION ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	dbDelta(
		"CREATE TABLE " . wfa_crm_leads_table() . " (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			lead_code VARCHAR(20) NOT NULL,
			name VARCHAR(190) NOT NULL DEFAULT '',
			phone VARCHAR(40) NOT NULL DEFAULT '',
			whatsapp VARCHAR(40) NOT NULL DEFAULT '',
			email VARCHAR(190) NOT NULL DEFAULT '',
			source VARCHAR(60) NOT NULL DEFAULT '',
			platform VARCHAR(60) NOT NULL DEFAULT '',
			campaign VARCHAR(120) NOT NULL DEFAULT '',
			ad_set VARCHAR(120) NOT NULL DEFAULT '',
			ad VARCHAR(120) NOT NULL DEFAULT '',
			course_interested VARCHAR(120) NOT NULL DEFAULT '',
			counselor_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			status VARCHAR(30) NOT NULL DEFAULT 'new',
			lead_date DATE NOT NULL,
			created_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL,
			updated_by BIGINT UNSIGNED NULL,
			updated_at DATETIME NULL,
			PRIMARY KEY  (id),
			KEY status (status),
			KEY counselor_id (counselor_id),
			KEY source (source),
			KEY lead_date (lead_date)
		) {$charset_collate};"
	);

	dbDelta(
		"CREATE TABLE " . wfa_crm_conversations_table() . " (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			lead_id BIGINT UNSIGNED NOT NULL,
			conv_date DATE NOT NULL,
			conv_time TIME NOT NULL,
			channel VARCHAR(30) NOT NULL DEFAULT '',
			counselor_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			note TEXT NULL,
			outcome VARCHAR(60) NOT NULL DEFAULT '',
			created_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY lead_id (lead_id)
		) {$charset_collate};"
	);

	dbDelta(
		"CREATE TABLE " . wfa_crm_followups_table() . " (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			lead_id BIGINT UNSIGNED NOT NULL,
			next_date DATE NOT NULL,
			next_time TIME NULL,
			reason VARCHAR(190) NOT NULL DEFAULT '',
			priority VARCHAR(20) NOT NULL DEFAULT 'medium',
			counselor_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			status VARCHAR(20) NOT NULL DEFAULT 'pending',
			created_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NULL,
			PRIMARY KEY  (id),
			KEY lead_id (lead_id),
			KEY next_date (next_date),
			KEY status (status)
		) {$charset_collate};"
	);

	dbDelta(
		"CREATE TABLE " . wfa_crm_admissions_table() . " (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			lead_id BIGINT UNSIGNED NOT NULL,
			student_name VARCHAR(190) NOT NULL DEFAULT '',
			student_phone VARCHAR(40) NOT NULL DEFAULT '',
			course VARCHAR(120) NOT NULL DEFAULT '',
			source VARCHAR(60) NOT NULL DEFAULT '',
			campaign VARCHAR(120) NOT NULL DEFAULT '',
			counselor_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
			fee_collected DECIMAL(12,2) NOT NULL DEFAULT 0,
			commission_type VARCHAR(20) NOT NULL DEFAULT 'fixed',
			commission_value DECIMAL(12,2) NOT NULL DEFAULT 0,
			commission_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
			commission_status VARCHAR(20) NOT NULL DEFAULT 'pending',
			commission_expense_id BIGINT UNSIGNED NULL,
			fee_income_id BIGINT UNSIGNED NULL,
			admitted_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
			admitted_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY lead_id (lead_id),
			KEY commission_status (commission_status)
		) {$charset_collate};"
	);

	update_option( 'wfa_crm_db_version', WFA_CRM_DB_VERSION );
}
add_action( 'init', 'wfa_crm_maybe_upgrade_db' );

/**
 * A dedicated "CRM User" role with exactly one real capability
 * (WFA_CRM_CAP) — a counselor logging in can reach ONLY the CRM
 * dashboard, nothing else in wp-admin, because they have no other
 * capability at all. Administrators get the same capability added
 * directly so they can access it too without a role change.
 */
function wfa_crm_maybe_register_role() {
	if ( ! get_role( 'wfa_crm_user' ) ) {
		add_role( 'wfa_crm_user', 'CRM User', array( 'read' => true, WFA_CRM_CAP => true ) );
	}
	$admin = get_role( 'administrator' );
	if ( $admin && ! $admin->has_cap( WFA_CRM_CAP ) ) {
		$admin->add_cap( WFA_CRM_CAP );
	}
}
add_action( 'init', 'wfa_crm_maybe_register_role' );

function wfa_crm_user_can_access() {
	return is_user_logged_in() && current_user_can( WFA_CRM_CAP );
}

/* ---------------------------------------------------------------------
 * Constants shared by the UI, handlers and filters
 * ------------------------------------------------------------------- */
function wfa_crm_statuses() {
	return array(
		'new'               => 'New',
		'contacted'         => 'Contacted',
		'no_response'       => 'No Response',
		'interested'        => 'Interested',
		'followup'          => 'Follow-up',
		'admission_pending' => 'Admission Pending',
		'admitted'          => 'Admitted',
		'not_interested'    => 'Not Interested',
		'lost'              => 'Lost',
	);
}

function wfa_crm_channels() {
	return array(
		'phone_call'       => 'Phone Call',
		'whatsapp'         => 'WhatsApp',
		'messenger'        => 'Messenger',
		'instagram'        => 'Instagram',
		'tiktok'           => 'TikTok',
		'physical_meeting' => 'Physical Meeting',
		'internal_note'    => 'Internal Note',
	);
}

function wfa_crm_sources() {
	return array( 'Facebook Ads', 'Instagram', 'WhatsApp', 'Messenger', 'TikTok', 'Website Form', 'Walk-in', 'Referral', 'Other' );
}

function wfa_crm_courses() {
	return array( 'Computer Operator Course', 'Graphic Design', 'Digital Marketing', 'Not sure yet' );
}

function wfa_crm_priorities() {
	return array( 'low' => 'Low', 'medium' => 'Medium', 'high' => 'High' );
}

function wfa_crm_followup_statuses() {
	return array( 'pending' => 'Pending', 'completed' => 'Completed', 'rescheduled' => 'Rescheduled' );
}

function wfa_crm_commission_types() {
	return array( 'percentage' => 'Percentage', 'fixed' => 'Fixed' );
}

/**
 * Human-readable, sequential lead code (LD-00001, LD-00002, ...) shown
 * to staff instead of the raw auto-increment id.
 */
function wfa_crm_next_lead_code() {
	global $wpdb;
	$table = wfa_crm_leads_table();
	$max   = (int) $wpdb->get_var( "SELECT MAX(id) FROM {$table}" );
	return sprintf( 'LD-%05d', $max + 1 );
}

/**
 * Counselors = WordPress users holding the CRM capability (the
 * "CRM User" role, plus Administrators) — reused everywhere a
 * Counselor dropdown is needed, so the list never drifts.
 */
function wfa_crm_counselor_choices() {
	$users = array();
	foreach ( get_users( array( 'fields' => array( 'ID', 'display_name' ) ) ) as $u ) {
		$user = get_userdata( $u->ID );
		if ( $user && $user->has_cap( WFA_CRM_CAP ) ) {
			$users[ $u->ID ] = $u->display_name;
		}
	}
	return $users;
}

/* ---------------------------------------------------------------------
 * Query helpers
 * ------------------------------------------------------------------- */
function wfa_crm_get_stats() {
	global $wpdb;
	$table = wfa_crm_leads_table();
	$today = current_time( 'Y-m-d' );
	$ft    = wfa_crm_followups_table();

	return array(
		'total'              => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ),
		'new'                => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE status = %s", 'new' ) ),
		'today'              => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE lead_date = %s", $today ) ),
		'followups_today'    => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$ft} WHERE next_date = %s AND status = 'pending'", $today ) ),
		'followups_overdue'  => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$ft} WHERE next_date < %s AND status = 'pending'", $today ) ),
		'interested'         => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE status = %s", 'interested' ) ),
		'admission_pending'  => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE status = %s", 'admission_pending' ) ),
		'admitted'           => (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE status = %s", 'admitted' ) ),
	);
}

/**
 * Filtered, searched lead list for the main table. All filters are
 * optional; every value is passed through $wpdb->prepare().
 */
function wfa_crm_query_leads( $args = array() ) {
	global $wpdb;
	$table = wfa_crm_leads_table();

	$defaults = array(
		'search'     => '',
		'date_from'  => '',
		'date_to'    => '',
		'source'     => '',
		'course'     => '',
		'counselor'  => 0,
		'status'     => '',
		'limit'      => 200,
	);
	$args = wp_parse_args( $args, $defaults );

	$where  = array( '1=1' );
	$params = array();

	if ( '' !== $args['search'] ) {
		$where[]  = '(name LIKE %s OR phone LIKE %s OR whatsapp LIKE %s OR lead_code LIKE %s)';
		$like     = '%' . $wpdb->esc_like( $args['search'] ) . '%';
		$params[] = $like;
		$params[] = $like;
		$params[] = $like;
		$params[] = $like;
	}
	if ( '' !== $args['date_from'] ) {
		$where[]  = 'lead_date >= %s';
		$params[] = $args['date_from'];
	}
	if ( '' !== $args['date_to'] ) {
		$where[]  = 'lead_date <= %s';
		$params[] = $args['date_to'];
	}
	if ( '' !== $args['source'] ) {
		$where[]  = 'source = %s';
		$params[] = $args['source'];
	}
	if ( '' !== $args['course'] ) {
		$where[]  = 'course_interested = %s';
		$params[] = $args['course'];
	}
	if ( (int) $args['counselor'] > 0 ) {
		$where[]  = 'counselor_id = %d';
		$params[] = (int) $args['counselor'];
	}
	if ( '' !== $args['status'] ) {
		$where[]  = 'status = %s';
		$params[] = $args['status'];
	}

	$sql = "SELECT * FROM {$table} WHERE " . implode( ' AND ', $where ) . ' ORDER BY id DESC LIMIT %d';
	$params[] = (int) $args['limit'];

	return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
}

function wfa_crm_get_lead( $lead_id ) {
	global $wpdb;
	$table = wfa_crm_leads_table();
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $lead_id ) );
}

function wfa_crm_get_conversations( $lead_id ) {
	global $wpdb;
	$table = wfa_crm_conversations_table();
	return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE lead_id = %d ORDER BY conv_date DESC, conv_time DESC, id DESC", $lead_id ) );
}

function wfa_crm_get_followups( $lead_id ) {
	global $wpdb;
	$table = wfa_crm_followups_table();
	return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE lead_id = %d ORDER BY next_date ASC, id DESC", $lead_id ) );
}

function wfa_crm_get_admission_by_lead( $lead_id ) {
	global $wpdb;
	$table = wfa_crm_admissions_table();
	return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE lead_id = %d", $lead_id ) );
}

function wfa_crm_counselor_name( $user_id ) {
	if ( ! $user_id ) {
		return '—';
	}
	$u = get_userdata( $user_id );
	return $u ? $u->display_name : '—';
}

/**
 * This file is the single entry point functions.php requires — it
 * loads its sibling files itself.
 */
require_once __DIR__ . '/crm-render.php';
require_once __DIR__ . '/crm-handlers.php';
