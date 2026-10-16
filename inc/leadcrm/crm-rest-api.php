<?php
/**
 * Lead CRM — REST API for the Android "One-Tap Lead Forward" app.
 *
 * Fully isolated: a brand-new route only, no changes to the existing
 * AJAX handlers (crm-handlers.php), the Lead CRM dashboard UI, or the
 * database schema. Uses WordPress's built-in Application Passwords
 * for authentication (no new auth system), the same WFA_CRM_CAP
 * capability check the AJAX endpoints already use, and the existing
 * wfa_crm_leads_table() / wfa_crm_lead_types() / wfa_crm_sources().
 *
 * Endpoint: POST /wp-json/wfa-crm/v1/leads
 * Auth:     HTTP Basic Auth with a WordPress Application Password
 *           (Users → Profile → Application Passwords), for a user
 *           that holds the CRM capability. Requires HTTPS.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Turns "01712345678", "8801712345678" or "+8801712345678" into the
 * canonical "01712345678" form, or '' if it isn't a valid BD mobile
 * number once normalized.
 */
function wfa_crm_rest_normalize_bd_phone( $raw ) {
	$digits = preg_replace( '/\D/', '', (string) $raw );

	if ( 0 === strpos( $digits, '880' ) ) {
		$digits = substr( $digits, 3 );
	}

	if ( ! preg_match( '/^01[3-9]\d{8}$/', $digits ) ) {
		return '';
	}

	return $digits;
}

/**
 * The 3 historical formats the same number might already be stored
 * as, so the duplicate check catches leads saved before/without
 * normalization too.
 */
function wfa_crm_rest_phone_variants( $normalized ) {
	return array(
		$normalized,
		'880' . $normalized,
		'+880' . $normalized,
	);
}

function wfa_crm_rest_rate_limited( $user_id ) {
	$key     = 'wfa_crm_rest_rl_' . $user_id;
	$count   = (int) get_transient( $key );
	$limit   = 20; // requests per minute, per user — generous for one-tap forwarding, tight enough to block runaway loops.
	if ( $count >= $limit ) {
		return true;
	}
	set_transient( $key, $count + 1, MINUTE_IN_SECONDS );
	return false;
}

function wfa_crm_rest_permission_check( $request ) {
	if ( ! is_ssl() ) {
		return new WP_Error( 'wfa_crm_https_required', 'HTTPS আবশ্যক।', array( 'status' => 400 ) );
	}
	if ( ! is_user_logged_in() ) {
		return new WP_Error( 'wfa_crm_unauthorized', 'Authentication প্রয়োজন।', array( 'status' => 401 ) );
	}
	if ( ! current_user_can( WFA_CRM_CAP ) ) {
		return new WP_Error( 'wfa_crm_forbidden', 'অনুমতি নেই।', array( 'status' => 403 ) );
	}
	if ( wfa_crm_rest_rate_limited( get_current_user_id() ) ) {
		return new WP_Error( 'wfa_crm_rate_limited', 'অনেকগুলো রিকোয়েস্ট, একটু পর আবার চেষ্টা করুন।', array( 'status' => 429 ) );
	}
	return true;
}

function wfa_crm_rest_create_lead( WP_REST_Request $request ) {
	global $wpdb;

	$name = isset( $request['name'] ) ? sanitize_text_field( $request['name'] ) : '';
	if ( '' === $name ) {
		return new WP_Error( 'wfa_crm_invalid_name', 'নাম আবশ্যক।', array( 'status' => 400 ) );
	}

	$phone = wfa_crm_rest_normalize_bd_phone( isset( $request['phone'] ) ? $request['phone'] : '' );
	if ( '' === $phone ) {
		return new WP_Error( 'wfa_crm_invalid_phone', 'সঠিক বাংলাদেশি মোবাইল নম্বর দিন।', array( 'status' => 400 ) );
	}

	$lead_type = isset( $request['lead_type'] ) ? sanitize_key( $request['lead_type'] ) : '';
	if ( ! array_key_exists( $lead_type, wfa_crm_lead_types() ) ) {
		return new WP_Error( 'wfa_crm_invalid_lead_type', 'Lead Type (academy / agency) সঠিকভাবে দিন।', array( 'status' => 400 ) );
	}

	// Source must be one of the CRM's existing allowed values. There is
	// no literal "Phone Call" entry in wfa_crm_sources(), so a request
	// with no source, or one that doesn't match an allowed value,
	// falls back to "Other" rather than inventing a new source.
	$allowed_sources = wfa_crm_sources();
	$source          = isset( $request['source'] ) ? sanitize_text_field( $request['source'] ) : '';
	if ( ! in_array( $source, $allowed_sources, true ) ) {
		$source = 'Other';
	}

	$whatsapp          = isset( $request['whatsapp'] ) ? sanitize_text_field( $request['whatsapp'] ) : '';
	$email             = isset( $request['email'] ) ? sanitize_email( $request['email'] ) : '';
	$campaign          = isset( $request['campaign'] ) ? sanitize_text_field( $request['campaign'] ) : '';
	$course_interested = isset( $request['course_interested'] ) ? sanitize_text_field( $request['course_interested'] ) : '';
	$counselor_id      = isset( $request['counselor_id'] ) ? absint( $request['counselor_id'] ) : 0;

	$table    = wfa_crm_leads_table();
	$variants = wfa_crm_rest_phone_variants( $phone );

	// Duplicate check is scoped to the same lead_type — a number
	// already on an Academy lead is not a duplicate of a new Agency
	// lead for the same person, and vice versa (the two pipelines are
	// kept deliberately separate elsewhere in this CRM).
	$existing = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT id, lead_code FROM {$table} WHERE lead_type = %s AND phone IN (%s,%s,%s) ORDER BY id DESC LIMIT 1",
			$lead_type,
			$variants[0],
			$variants[1],
			$variants[2]
		)
	);

	if ( $existing ) {
		return rest_ensure_response(
			array(
				'success'   => true,
				'duplicate' => true,
				'lead_id'   => (int) $existing->id,
				'lead_code' => $existing->lead_code,
				'message'   => 'এই নম্বরে আগে থেকেই একটা Lead আছে, নতুন Lead তৈরি করা হয়নি।',
			)
		);
	}

	$data = array(
		'lead_code'         => wfa_crm_next_lead_code(),
		'lead_type'         => $lead_type,
		'name'              => $name,
		'phone'             => $phone,
		'whatsapp'          => $whatsapp,
		'email'             => $email,
		'source'            => $source,
		'platform'          => '',
		'campaign'          => $campaign,
		'ad_set'            => '',
		'ad'                => '',
		'course_interested' => $course_interested,
		'counselor_id'      => $counselor_id,
		'status'            => 'new',
		'lead_date'         => current_time( 'Y-m-d' ),
		'created_by'        => get_current_user_id(),
		'created_at'        => current_time( 'mysql' ),
	);

	$result = $wpdb->insert(
		$table,
		$data,
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s' )
	);

	if ( false === $result ) {
		// Never log $wpdb->last_error here — on a constraint failure it
		// can echo back submitted data (name/phone), and this is a
		// request log, not an internal admin screen.
		return new WP_Error( 'wfa_crm_insert_failed', 'Lead তৈরি করা যায়নি।', array( 'status' => 500 ) );
	}

	return rest_ensure_response(
		array(
			'success'   => true,
			'duplicate' => false,
			'lead_id'   => (int) $wpdb->insert_id,
			'lead_code' => $data['lead_code'],
		)
	);
}

add_action(
	'rest_api_init',
	function () {
		register_rest_route(
			'wfa-crm/v1',
			'/leads',
			array(
				'methods'             => 'POST',
				'callback'            => 'wfa_crm_rest_create_lead',
				'permission_callback' => 'wfa_crm_rest_permission_check',
				'args'                => array(
					'name'      => array( 'required' => true, 'type' => 'string' ),
					'phone'     => array( 'required' => true, 'type' => 'string' ),
					'lead_type' => array( 'required' => true, 'type' => 'string' ),
				),
			)
		);
	}
);
