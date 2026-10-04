<?php
/**
 * WF-Invoice — core: tables, role/capability, settings, numbering,
 * query helpers.
 *
 * Fully separate module (own tables, own front-end login page at
 * /wf-invoice/, own AJAX handlers) — does not touch Lead CRM, WF
 * Simple Accounts, or the Academy's lead list. Four new tables:
 * - {$wpdb->prefix}wfi_clients
 * - {$wpdb->prefix}wfi_invoices
 * - {$wpdb->prefix}wfi_invoice_items
 * - {$wpdb->prefix}wfi_payments
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WFI_DB_VERSION', '1.0' );
define( 'WFI_CAP_ACCESS', 'wfi_access' );
define( 'WFI_CAP_DELETE', 'wfi_delete' );

function wfi_clients_table() {
	global $wpdb;
	return $wpdb->prefix . 'wfi_clients';
}
function wfi_invoices_table() {
	global $wpdb;
	return $wpdb->prefix . 'wfi_invoices';
}
function wfi_invoice_items_table() {
	global $wpdb;
	return $wpdb->prefix . 'wfi_invoice_items';
}
function wfi_payments_table() {
	global $wpdb;
	return $wpdb->prefix . 'wfi_payments';
}

function wfi_maybe_upgrade_db() {
	if ( get_option( 'wfi_db_version' ) === WFI_DB_VERSION ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	dbDelta(
		"CREATE TABLE " . wfi_clients_table() . " (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			client_code VARCHAR(20) NOT NULL,
			business_name VARCHAR(190) NOT NULL DEFAULT '',
			contact_person VARCHAR(190) NOT NULL DEFAULT '',
			logo_url VARCHAR(500) NOT NULL DEFAULT '',
			mobile VARCHAR(40) NOT NULL DEFAULT '',
			whatsapp VARCHAR(40) NOT NULL DEFAULT '',
			email VARCHAR(190) NOT NULL DEFAULT '',
			address TEXT NULL,
			website VARCHAR(255) NOT NULL DEFAULT '',
			facebook_url VARCHAR(255) NOT NULL DEFAULT '',
			notes TEXT NULL,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NULL,
			PRIMARY KEY  (id),
			KEY client_code (client_code)
		) {$charset_collate};"
	);

	dbDelta(
		"CREATE TABLE " . wfi_invoices_table() . " (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			invoice_number VARCHAR(40) NOT NULL,
			client_id BIGINT UNSIGNED NOT NULL,
			invoice_date DATE NOT NULL,
			due_date DATE NULL,
			reference_number VARCHAR(100) NOT NULL DEFAULT '',
			subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
			discount DECIMAL(12,2) NOT NULL DEFAULT 0,
			tax DECIMAL(12,2) NOT NULL DEFAULT 0,
			other_charge DECIMAL(12,2) NOT NULL DEFAULT 0,
			grand_total DECIMAL(12,2) NOT NULL DEFAULT 0,
			paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
			due_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
			status VARCHAR(20) NOT NULL DEFAULT 'due',
			payment_method VARCHAR(30) NOT NULL DEFAULT '',
			notes TEXT NULL,
			terms TEXT NULL,
			created_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL,
			updated_at DATETIME NULL,
			PRIMARY KEY  (id),
			KEY client_id (client_id),
			KEY status (status),
			KEY invoice_number (invoice_number)
		) {$charset_collate};"
	);

	dbDelta(
		"CREATE TABLE " . wfi_invoice_items_table() . " (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			invoice_id BIGINT UNSIGNED NOT NULL,
			service_name VARCHAR(190) NOT NULL DEFAULT '',
			description TEXT NULL,
			quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
			unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
			amount DECIMAL(12,2) NOT NULL DEFAULT 0,
			sort_order INT NOT NULL DEFAULT 0,
			created_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY invoice_id (invoice_id)
		) {$charset_collate};"
	);

	dbDelta(
		"CREATE TABLE " . wfi_payments_table() . " (
			id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
			payment_number VARCHAR(30) NOT NULL,
			invoice_id BIGINT UNSIGNED NOT NULL,
			client_id BIGINT UNSIGNED NOT NULL,
			amount DECIMAL(12,2) NOT NULL DEFAULT 0,
			payment_method VARCHAR(30) NOT NULL DEFAULT '',
			transaction_id VARCHAR(100) NOT NULL DEFAULT '',
			payment_date DATE NOT NULL,
			received_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
			notes TEXT NULL,
			created_at DATETIME NOT NULL,
			PRIMARY KEY  (id),
			KEY invoice_id (invoice_id),
			KEY client_id (client_id)
		) {$charset_collate};"
	);

	update_option( 'wfi_db_version', WFI_DB_VERSION );
}
add_action( 'init', 'wfi_maybe_upgrade_db' );

/**
 * Role/capability — a dedicated "Invoice User" role (full access) plus
 * Administrators get both access + delete automatically. A lighter
 * "Invoice Staff" role gets access without delete rights.
 */
function wfi_maybe_register_roles() {
	if ( ! get_role( 'wfi_user' ) ) {
		add_role( 'wfi_user', 'WF-Invoice User', array( 'read' => true, WFI_CAP_ACCESS => true, WFI_CAP_DELETE => true ) );
	}
	if ( ! get_role( 'wfi_staff' ) ) {
		add_role( 'wfi_staff', 'WF-Invoice Staff', array( 'read' => true, WFI_CAP_ACCESS => true ) );
	}
	$admin = get_role( 'administrator' );
	if ( $admin && ! $admin->has_cap( WFI_CAP_ACCESS ) ) {
		$admin->add_cap( WFI_CAP_ACCESS );
		$admin->add_cap( WFI_CAP_DELETE );
	}
}
add_action( 'init', 'wfi_maybe_register_roles' );

function wfi_user_can_access() {
	return is_user_logged_in() && current_user_can( WFI_CAP_ACCESS );
}
function wfi_user_can_delete() {
	return is_user_logged_in() && current_user_can( WFI_CAP_DELETE );
}

/* ---------------------------------------------------------------------
 * Settings (company profile, prefixes, currency, defaults) — stored as
 * one option array so Settings page is a single simple form.
 * ------------------------------------------------------------------- */
function wfi_default_settings() {
	return array(
		'company_name'    => "Women's Fight",
		'company_address' => 'City Plaza, Lift-3, Ramganj, Lakshmipur',
		'company_mobile'  => '+880 1748-133740',
		'company_whatsapp'=> '+880 1748-133740',
		'company_email'   => 'Womansfight65@gmail.com',
		'company_website' => 'https://womensfight.com',
		'logo_url'        => get_template_directory_uri() . '/assets/img/logo-full.png',
		'invoice_prefix'  => 'WF-INV',
		'client_prefix'   => 'WF-C',
		'currency_symbol' => '৳',
		'default_terms'   => "সকল পেমেন্ট চুক্তি অনুযায়ী প্রদান করতে হবে।\nইনভয়েস ইস্যুর ৭ দিনের মধ্যে Due পেমেন্ট পরিশোধ করুন।",
		'signature_name'  => "Women's Fight",
		'payment_info'    => "Bkash/Nagad: +880 1748-133740\nBank: (bank details here)",
	);
}

function wfi_get_settings() {
	$saved = get_option( 'wfi_settings', array() );
	return wp_parse_args( $saved, wfi_default_settings() );
}

function wfi_update_settings( $data ) {
	$current = wfi_get_settings();
	update_option( 'wfi_settings', wp_parse_args( $data, $current ) );
}

function wfi_money( $amount ) {
	$s = wfi_get_settings();
	return $s['currency_symbol'] . number_format( (float) $amount, 2 );
}

/* ---------------------------------------------------------------------
 * Sequential code/number generators
 * ------------------------------------------------------------------- */
function wfi_next_client_code() {
	global $wpdb;
	$s      = wfi_get_settings();
	$prefix = $s['client_prefix'];
	$max    = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT MAX(CAST(SUBSTRING(client_code, %d) AS UNSIGNED)) FROM " . wfi_clients_table() . " WHERE client_code LIKE %s",
			strlen( $prefix ) + 2,
			$wpdb->esc_like( $prefix ) . '-%'
		)
	);
	return $prefix . '-' . str_pad( $max + 1, 4, '0', STR_PAD_LEFT );
}

function wfi_next_invoice_number() {
	global $wpdb;
	$s      = wfi_get_settings();
	$prefix = $s['invoice_prefix'];
	$year   = current_time( 'Y' );
	$like   = $prefix . '-' . $year . '-%';
	$max    = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT MAX(CAST(SUBSTRING_INDEX(invoice_number, '-', -1) AS UNSIGNED)) FROM " . wfi_invoices_table() . " WHERE invoice_number LIKE %s",
			$wpdb->esc_like( $like ) . '%'
		)
	);
	return $prefix . '-' . $year . '-' . str_pad( $max + 1, 4, '0', STR_PAD_LEFT );
}

function wfi_next_payment_number() {
	global $wpdb;
	$max = (int) $wpdb->get_var( "SELECT MAX(CAST(SUBSTRING(payment_number, 6) AS UNSIGNED)) FROM " . wfi_payments_table() . " WHERE payment_number LIKE 'WF-P-%'" );
	return 'WF-P-' . str_pad( $max + 1, 5, '0', STR_PAD_LEFT );
}

/* ---------------------------------------------------------------------
 * Constants
 * ------------------------------------------------------------------- */
function wfi_statuses() {
	return array(
		'paid'      => 'Paid',
		'partial'   => 'Partial',
		'due'       => 'Due',
		'cancelled' => 'Cancelled',
	);
}
function wfi_payment_methods() {
	return array( 'Cash', 'Bank Transfer', 'bKash', 'Nagad', 'Rocket', 'Card', 'Other' );
}

/* ---------------------------------------------------------------------
 * Query/get helpers
 * ------------------------------------------------------------------- */
function wfi_get_client( $id ) {
	global $wpdb;
	return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . wfi_clients_table() . ' WHERE id = %d', $id ) );
}
function wfi_query_clients( $search = '' ) {
	global $wpdb;
	$table = wfi_clients_table();
	if ( '' !== $search ) {
		$like = '%' . $wpdb->esc_like( $search ) . '%';
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE business_name LIKE %s OR mobile LIKE %s OR client_code LIKE %s ORDER BY id DESC", $like, $like, $like ) );
	}
	return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id DESC" );
}

function wfi_get_invoice( $id ) {
	global $wpdb;
	return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . wfi_invoices_table() . ' WHERE id = %d', $id ) );
}
function wfi_get_invoice_items( $invoice_id ) {
	global $wpdb;
	return $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . wfi_invoice_items_table() . ' WHERE invoice_id = %d ORDER BY sort_order ASC, id ASC', $invoice_id ) );
}
function wfi_get_invoice_payments( $invoice_id ) {
	global $wpdb;
	return $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . wfi_payments_table() . ' WHERE invoice_id = %d ORDER BY payment_date DESC, id DESC', $invoice_id ) );
}
function wfi_get_client_invoices( $client_id ) {
	global $wpdb;
	return $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . wfi_invoices_table() . ' WHERE client_id = %d ORDER BY id DESC', $client_id ) );
}
function wfi_get_client_payments( $client_id ) {
	global $wpdb;
	return $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . wfi_payments_table() . ' WHERE client_id = %d ORDER BY payment_date DESC, id DESC', $client_id ) );
}

function wfi_query_invoices( $args = array() ) {
	global $wpdb;
	$table = wfi_invoices_table();
	$ctable = wfi_clients_table();

	$defaults = array(
		'search'         => '',
		'status'         => '',
		'date_from'      => '',
		'date_to'        => '',
		'limit'          => 300,
	);
	$args = wp_parse_args( $args, $defaults );

	$where  = array( '1=1' );
	$params = array();

	if ( '' !== $args['search'] ) {
		$where[]  = '(i.invoice_number LIKE %s OR c.business_name LIKE %s OR c.mobile LIKE %s)';
		$like     = '%' . $wpdb->esc_like( $args['search'] ) . '%';
		$params[] = $like;
		$params[] = $like;
		$params[] = $like;
	}
	if ( '' !== $args['status'] ) {
		$where[]  = 'i.status = %s';
		$params[] = $args['status'];
	}
	if ( '' !== $args['date_from'] ) {
		$where[]  = 'i.invoice_date >= %s';
		$params[] = $args['date_from'];
	}
	if ( '' !== $args['date_to'] ) {
		$where[]  = 'i.invoice_date <= %s';
		$params[] = $args['date_to'];
	}

	$sql = "SELECT i.*, c.business_name, c.mobile AS client_mobile FROM {$table} i LEFT JOIN {$ctable} c ON c.id = i.client_id WHERE " . implode( ' AND ', $where ) . ' ORDER BY i.id DESC LIMIT %d';
	$params[] = (int) $args['limit'];

	return $wpdb->get_results( $wpdb->prepare( $sql, $params ) );
}

function wfi_get_dashboard_stats() {
	global $wpdb;
	$table = wfi_invoices_table();
	return array(
		'total_clients'     => (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . wfi_clients_table() ),
		'total_invoices'    => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status != 'cancelled'" ),
		'total_amount'      => (float) $wpdb->get_var( "SELECT COALESCE(SUM(grand_total),0) FROM {$table} WHERE status != 'cancelled'" ),
		'total_paid'        => (float) $wpdb->get_var( "SELECT COALESCE(SUM(paid_amount),0) FROM {$table} WHERE status != 'cancelled'" ),
		'total_due'         => (float) $wpdb->get_var( "SELECT COALESCE(SUM(due_amount),0) FROM {$table} WHERE status != 'cancelled'" ),
		'paid_invoices'     => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'paid'" ),
		'partial_invoices'  => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'partial'" ),
		'due_invoices'      => (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status = 'due'" ),
	);
}

/**
 * Recompute an invoice's status from paid vs grand_total. Called after
 * any payment is added or an invoice is saved.
 */
function wfi_recompute_invoice_status( $invoice_id ) {
	global $wpdb;
	$table = wfi_invoices_table();
	$inv   = wfi_get_invoice( $invoice_id );
	if ( ! $inv || 'cancelled' === $inv->status ) {
		return;
	}
	$due = round( $inv->grand_total - $inv->paid_amount, 2 );
	if ( $due <= 0 ) {
		$status = 'paid';
		$due    = 0;
	} elseif ( $inv->paid_amount > 0 ) {
		$status = 'partial';
	} else {
		$status = 'due';
	}
	$wpdb->update(
		$table,
		array( 'due_amount' => $due, 'status' => $status, 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => $invoice_id ),
		array( '%f', '%s', '%s' ),
		array( '%d' )
	);
}

require_once __DIR__ . '/invoice-render.php';
require_once __DIR__ . '/invoice-handlers.php';
