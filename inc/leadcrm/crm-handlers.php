<?php
/**
 * Lead CRM — AJAX handlers. Every handler:
 * - requires is_user_logged_in() + current_user_can(WFA_CRM_CAP)
 * - verifies the 'wfa_crm_nonce' nonce
 * - sanitizes all input, uses $wpdb->prepare() / $wpdb->insert()-update()
 *   with carefully counted format arrays (see the comment in
 *   accounts-core.php about the bug this guards against)
 * - returns wp_send_json_success()/wp_send_json_error()
 *
 * Registered only as wp_ajax_* (never wp_ajax_nopriv_*) — a logged-out
 * visitor gets WordPress's standard "0" response, nothing else.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wfa_crm_ajax_guard() {
	if ( ! wfa_crm_user_can_access() ) {
		wp_send_json_error( array( 'message' => 'অনুমতি নেই।' ), 403 );
	}
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), 'wfa_crm_nonce' ) ) {
		wp_send_json_error( array( 'message' => 'Security check failed, পেজ রিফ্রেশ করুন।' ), 403 );
	}
}

/* ---------------------------------------------------------------------
 * Dashboard + drawer fetch (read-only, but still capability+nonce gated
 * since lead data is private)
 * ------------------------------------------------------------------- */
function wfa_crm_ajax_get_dashboard() {
	wfa_crm_ajax_guard();

	$args = array(
		'search'    => isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '',
		'date_from' => isset( $_POST['date_from'] ) ? sanitize_text_field( wp_unslash( $_POST['date_from'] ) ) : '',
		'date_to'   => isset( $_POST['date_to'] ) ? sanitize_text_field( wp_unslash( $_POST['date_to'] ) ) : '',
		'source'    => isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '',
		'course'    => isset( $_POST['course'] ) ? sanitize_text_field( wp_unslash( $_POST['course'] ) ) : '',
		'counselor' => isset( $_POST['counselor'] ) ? absint( $_POST['counselor'] ) : 0,
		'status'    => isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '',
	);

	ob_start();
	wfa_crm_render_stats_cards();
	$stats_html = ob_get_clean();

	ob_start();
	wfa_crm_render_leads_table( wfa_crm_query_leads( $args ) );
	$table_html = ob_get_clean();

	wp_send_json_success( array( 'stats' => $stats_html, 'table' => $table_html ) );
}
add_action( 'wp_ajax_wfa_crm_get_dashboard', 'wfa_crm_ajax_get_dashboard' );

function wfa_crm_ajax_get_drawer() {
	wfa_crm_ajax_guard();
	$lead_id = isset( $_POST['lead_id'] ) ? absint( $_POST['lead_id'] ) : 0;
	ob_start();
	wfa_crm_render_lead_drawer( $lead_id );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_wfa_crm_get_drawer', 'wfa_crm_ajax_get_drawer' );

function wfa_crm_ajax_get_new_lead_form() {
	wfa_crm_ajax_guard();
	ob_start();
	wfa_crm_render_new_lead_form();
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_wfa_crm_get_new_lead_form', 'wfa_crm_ajax_get_new_lead_form' );

/* ---------------------------------------------------------------------
 * Create lead
 * ------------------------------------------------------------------- */
function wfa_crm_ajax_create_lead() {
	wfa_crm_ajax_guard();
	global $wpdb;

	$name = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$phone = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
	if ( '' === $name || '' === $phone ) {
		wp_send_json_error( array( 'message' => 'নাম ও Phone আবশ্যক।' ) );
	}

	$data = array(
		'lead_code'         => wfa_crm_next_lead_code(),
		'name'              => $name,
		'phone'             => $phone,
		'whatsapp'          => isset( $_POST['whatsapp'] ) ? sanitize_text_field( wp_unslash( $_POST['whatsapp'] ) ) : '',
		'email'             => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
		'source'            => isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '',
		'platform'          => '',
		'campaign'          => isset( $_POST['campaign'] ) ? sanitize_text_field( wp_unslash( $_POST['campaign'] ) ) : '',
		'ad_set'            => '',
		'ad'                => '',
		'course_interested' => isset( $_POST['course_interested'] ) ? sanitize_text_field( wp_unslash( $_POST['course_interested'] ) ) : '',
		'counselor_id'      => isset( $_POST['counselor_id'] ) ? absint( $_POST['counselor_id'] ) : 0,
		'status'            => 'new',
		'lead_date'         => current_time( 'Y-m-d' ),
		'created_by'        => get_current_user_id(),
		'created_at'        => current_time( 'mysql' ),
	);

	$result = $wpdb->insert(
		wfa_crm_leads_table(),
		$data,
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s' )
	);

	if ( false === $result ) {
		wp_send_json_error( array( 'message' => 'Lead তৈরি করা যায়নি। ' . $wpdb->last_error ) );
	}

	wp_send_json_success( array( 'lead_id' => (int) $wpdb->insert_id ) );
}
add_action( 'wp_ajax_wfa_crm_create_lead', 'wfa_crm_ajax_create_lead' );

/* ---------------------------------------------------------------------
 * Save lead info (drawer's Section 1 form)
 * ------------------------------------------------------------------- */
function wfa_crm_ajax_save_lead_info() {
	wfa_crm_ajax_guard();
	global $wpdb;

	$lead_id = isset( $_POST['lead_id'] ) ? absint( $_POST['lead_id'] ) : 0;
	if ( ! $lead_id ) {
		wp_send_json_error( array( 'message' => 'Lead খুঁজে পাওয়া যায়নি।' ) );
	}

	$data = array(
		'name'              => isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
		'phone'             => isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '',
		'whatsapp'          => isset( $_POST['whatsapp'] ) ? sanitize_text_field( wp_unslash( $_POST['whatsapp'] ) ) : '',
		'email'             => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
		'course_interested' => isset( $_POST['course_interested'] ) ? sanitize_text_field( wp_unslash( $_POST['course_interested'] ) ) : '',
		'source'            => isset( $_POST['source'] ) ? sanitize_text_field( wp_unslash( $_POST['source'] ) ) : '',
		'platform'          => isset( $_POST['platform'] ) ? sanitize_text_field( wp_unslash( $_POST['platform'] ) ) : '',
		'campaign'          => isset( $_POST['campaign'] ) ? sanitize_text_field( wp_unslash( $_POST['campaign'] ) ) : '',
		'ad_set'            => isset( $_POST['ad_set'] ) ? sanitize_text_field( wp_unslash( $_POST['ad_set'] ) ) : '',
		'ad'                => isset( $_POST['ad'] ) ? sanitize_text_field( wp_unslash( $_POST['ad'] ) ) : '',
		'lead_date'         => isset( $_POST['lead_date'] ) ? sanitize_text_field( wp_unslash( $_POST['lead_date'] ) ) : current_time( 'Y-m-d' ),
		'counselor_id'      => isset( $_POST['counselor_id'] ) ? absint( $_POST['counselor_id'] ) : 0,
		'updated_by'        => get_current_user_id(),
		'updated_at'        => current_time( 'mysql' ),
	);

	$wpdb->update(
		wfa_crm_leads_table(),
		$data,
		array( 'id' => $lead_id ),
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s' ),
		array( '%d' )
	);

	ob_start();
	wfa_crm_render_lead_drawer( $lead_id );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_wfa_crm_save_lead_info', 'wfa_crm_ajax_save_lead_info' );

/* ---------------------------------------------------------------------
 * Change status
 * ------------------------------------------------------------------- */
function wfa_crm_ajax_change_status() {
	wfa_crm_ajax_guard();
	global $wpdb;

	$lead_id = isset( $_POST['lead_id'] ) ? absint( $_POST['lead_id'] ) : 0;
	$status  = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';
	if ( ! $lead_id || ! array_key_exists( $status, wfa_crm_statuses() ) ) {
		wp_send_json_error( array( 'message' => 'ভুল তথ্য।' ) );
	}

	$wpdb->update(
		wfa_crm_leads_table(),
		array( 'status' => $status, 'updated_by' => get_current_user_id(), 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => $lead_id ),
		array( '%s', '%d', '%s' ),
		array( '%d' )
	);

	ob_start();
	wfa_crm_render_lead_drawer( $lead_id );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_wfa_crm_change_status', 'wfa_crm_ajax_change_status' );

/* ---------------------------------------------------------------------
 * Conversation
 * ------------------------------------------------------------------- */
function wfa_crm_ajax_add_conversation() {
	wfa_crm_ajax_guard();
	global $wpdb;

	$lead_id = isset( $_POST['lead_id'] ) ? absint( $_POST['lead_id'] ) : 0;
	$note    = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';
	if ( ! $lead_id || '' === $note ) {
		wp_send_json_error( array( 'message' => 'Note আবশ্যক।' ) );
	}

	$wpdb->insert(
		wfa_crm_conversations_table(),
		array(
			'lead_id'      => $lead_id,
			'conv_date'    => current_time( 'Y-m-d' ),
			'conv_time'    => current_time( 'H:i:s' ),
			'channel'      => isset( $_POST['channel'] ) ? sanitize_key( wp_unslash( $_POST['channel'] ) ) : 'internal_note',
			'counselor_id' => get_current_user_id(),
			'note'         => $note,
			'outcome'      => isset( $_POST['outcome'] ) ? sanitize_text_field( wp_unslash( $_POST['outcome'] ) ) : '',
			'created_by'   => get_current_user_id(),
			'created_at'   => current_time( 'mysql' ),
		),
		array( '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s' )
	);

	// Contacting a brand-new lead is a natural, expected status nudge —
	// but never overrides a status staff already moved further along.
	$lead = wfa_crm_get_lead( $lead_id );
	if ( $lead && 'new' === $lead->status ) {
		$wpdb->update( wfa_crm_leads_table(), array( 'status' => 'contacted' ), array( 'id' => $lead_id ), array( '%s' ), array( '%d' ) );
	}

	ob_start();
	wfa_crm_render_lead_drawer( $lead_id );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_wfa_crm_add_conversation', 'wfa_crm_ajax_add_conversation' );

/* ---------------------------------------------------------------------
 * Follow-up
 * ------------------------------------------------------------------- */
function wfa_crm_ajax_add_followup() {
	wfa_crm_ajax_guard();
	global $wpdb;

	$lead_id   = isset( $_POST['lead_id'] ) ? absint( $_POST['lead_id'] ) : 0;
	$next_date = isset( $_POST['next_date'] ) ? sanitize_text_field( wp_unslash( $_POST['next_date'] ) ) : '';
	if ( ! $lead_id || ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $next_date ) ) {
		wp_send_json_error( array( 'message' => 'সঠিক তারিখ দিন।' ) );
	}

	$wpdb->insert(
		wfa_crm_followups_table(),
		array(
			'lead_id'      => $lead_id,
			'next_date'    => $next_date,
			'next_time'    => isset( $_POST['next_time'] ) && $_POST['next_time'] ? sanitize_text_field( wp_unslash( $_POST['next_time'] ) ) : null,
			'reason'       => isset( $_POST['reason'] ) ? sanitize_text_field( wp_unslash( $_POST['reason'] ) ) : '',
			'priority'     => isset( $_POST['priority'] ) ? sanitize_key( wp_unslash( $_POST['priority'] ) ) : 'medium',
			'counselor_id' => get_current_user_id(),
			'status'       => 'pending',
			'created_by'   => get_current_user_id(),
			'created_at'   => current_time( 'mysql' ),
		),
		array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s' )
	);

	$lead = wfa_crm_get_lead( $lead_id );
	if ( $lead && in_array( $lead->status, array( 'new', 'contacted' ), true ) ) {
		$wpdb->update( wfa_crm_leads_table(), array( 'status' => 'followup' ), array( 'id' => $lead_id ), array( '%s' ), array( '%d' ) );
	}

	ob_start();
	wfa_crm_render_lead_drawer( $lead_id );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_wfa_crm_add_followup', 'wfa_crm_ajax_add_followup' );

function wfa_crm_ajax_complete_followup() {
	wfa_crm_ajax_guard();
	global $wpdb;

	$fu_id = isset( $_POST['followup_id'] ) ? absint( $_POST['followup_id'] ) : 0;
	$ft    = wfa_crm_followups_table();
	$lead_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT lead_id FROM {$ft} WHERE id = %d", $fu_id ) );
	if ( ! $lead_id ) {
		wp_send_json_error( array( 'message' => 'Follow-up খুঁজে পাওয়া যায়নি।' ) );
	}

	$wpdb->update( $ft, array( 'status' => 'completed', 'updated_at' => current_time( 'mysql' ) ), array( 'id' => $fu_id ), array( '%s', '%s' ), array( '%d' ) );

	ob_start();
	wfa_crm_render_lead_drawer( $lead_id );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_wfa_crm_complete_followup', 'wfa_crm_ajax_complete_followup' );

/* ---------------------------------------------------------------------
 * Convert to Admission — keeps the lead record, creates a linked
 * admission row, carries over core info, moves status to Admitted, and
 * (via wfa_accounts_add_transaction) records the collected fee as
 * Income in WF Simple Accounts right away.
 * ------------------------------------------------------------------- */
function wfa_crm_ajax_convert_admission() {
	wfa_crm_ajax_guard();
	global $wpdb;

	$lead_id = isset( $_POST['lead_id'] ) ? absint( $_POST['lead_id'] ) : 0;
	$lead    = wfa_crm_get_lead( $lead_id );
	if ( ! $lead ) {
		wp_send_json_error( array( 'message' => 'Lead খুঁজে পাওয়া যায়নি।' ) );
	}
	if ( wfa_crm_get_admission_by_lead( $lead_id ) ) {
		wp_send_json_error( array( 'message' => 'এই Lead ইতিমধ্যে Admission-এ convert হয়েছে।' ) );
	}

	$fee              = isset( $_POST['fee_collected'] ) ? max( 0, round( (float) $_POST['fee_collected'], 2 ) ) : 0;
	$fee_method       = isset( $_POST['fee_payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['fee_payment_method'] ) ) : '';
	$commission_type  = isset( $_POST['commission_type'] ) ? sanitize_key( wp_unslash( $_POST['commission_type'] ) ) : 'fixed';
	$commission_value = isset( $_POST['commission_value'] ) ? max( 0, round( (float) $_POST['commission_value'], 2 ) ) : 0;

	$commission_amount = 'percentage' === $commission_type
		? round( $fee * ( $commission_value / 100 ), 2 )
		: $commission_value;

	// Fee collected is real income the moment a student is admitted —
	// recorded in Accounts right away rather than waiting.
	$fee_income_id = null;
	if ( $fee > 0 && function_exists( 'wfa_accounts_add_transaction' ) ) {
		$fee_income_id = wfa_accounts_add_transaction(
			array(
				'type'           => 'income',
				'category'       => 'Course Fee — ' . $lead->course_interested,
				'amount'         => $fee,
				'payment_method' => $fee_method,
				'note'           => 'Academy Admission — Lead ' . $lead->lead_code . ', Student ' . $lead->name,
			)
		);
	}

	$wpdb->insert(
		wfa_crm_admissions_table(),
		array(
			'lead_id'            => $lead_id,
			'student_name'       => $lead->name,
			'student_phone'      => $lead->phone,
			'course'             => $lead->course_interested,
			'source'             => $lead->source,
			'campaign'           => $lead->campaign,
			'counselor_id'       => $lead->counselor_id,
			'fee_collected'      => $fee,
			'commission_type'    => $commission_type,
			'commission_value'   => $commission_value,
			'commission_amount'  => $commission_amount,
			'commission_status'  => 'pending',
			'fee_income_id'      => $fee_income_id,
			'admitted_by'        => get_current_user_id(),
			'admitted_at'        => current_time( 'mysql' ),
		),
		array( '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%f', '%s', '%f', '%f', '%s', '%d', '%d', '%s' )
	);

	$wpdb->update(
		wfa_crm_leads_table(),
		array( 'status' => 'admitted', 'updated_by' => get_current_user_id(), 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => $lead_id ),
		array( '%s', '%d', '%s' ),
		array( '%d' )
	);

	ob_start();
	wfa_crm_render_lead_drawer( $lead_id );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_wfa_crm_convert_admission', 'wfa_crm_ajax_convert_admission' );

/**
 * Mark the admission's commission (to Life Support IT Institute) as
 * paid — records it as an Expense in WF Simple Accounts.
 */
function wfa_crm_ajax_pay_commission() {
	wfa_crm_ajax_guard();
	global $wpdb;

	$lead_id   = isset( $_POST['lead_id'] ) ? absint( $_POST['lead_id'] ) : 0;
	$admission = wfa_crm_get_admission_by_lead( $lead_id );
	if ( ! $admission || 'pending' !== $admission->commission_status ) {
		wp_send_json_error( array( 'message' => 'কিছু করার নেই।' ) );
	}

	$method = isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : '';

	$expense_id = function_exists( 'wfa_accounts_add_transaction' ) ? wfa_accounts_add_transaction(
		array(
			'type'           => 'general_expense',
			'category'       => 'Life Support IT Commission',
			'amount'         => $admission->commission_amount,
			'payment_method' => $method,
			'note'           => 'Admission commission — Lead ' . ( wfa_crm_get_lead( $lead_id )->lead_code ?? '' ) . ', Student ' . $admission->student_name,
		)
	) : null;

	$wpdb->update(
		wfa_crm_admissions_table(),
		array( 'commission_status' => 'paid', 'commission_expense_id' => $expense_id ),
		array( 'id' => $admission->id ),
		array( '%s', '%d' ),
		array( '%d' )
	);

	ob_start();
	wfa_crm_render_lead_drawer( $lead_id );
	wp_send_json_success( array( 'html' => ob_get_clean() ) );
}
add_action( 'wp_ajax_wfa_crm_pay_commission', 'wfa_crm_ajax_pay_commission' );
