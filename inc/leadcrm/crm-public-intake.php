<?php
/**
 * Public website → Lead CRM intake.
 *
 * Mirrors the public /contact/ form submission (admin-post.php, action
 * womensfight_submit_contact_form) into a new "new" lead in the CRM, so
 * counselors see it in /crm-lead/ without re-typing it by hand. Hooked
 * at priority 5 — before womensfight_handle_contact_form_submit() (the
 * original handler, priority 10) redirects and exits — so the existing
 * Contact Messages (wf_contact_msg) saving keeps working exactly as
 * before; this is purely additive.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wfa_crm_capture_public_contact_lead() {
	if (
		! isset( $_POST['womensfight_contact_form_nonce'] ) ||
		! wp_verify_nonce( wp_unslash( $_POST['womensfight_contact_form_nonce'] ), 'womensfight_contact_form' )
	) {
		return;
	}

	$name    = isset( $_POST['cf_name'] ) ? sanitize_text_field( wp_unslash( $_POST['cf_name'] ) ) : '';
	$phone   = isset( $_POST['cf_mobile'] ) ? sanitize_text_field( wp_unslash( $_POST['cf_mobile'] ) ) : '';
	$email   = isset( $_POST['cf_email'] ) ? sanitize_email( wp_unslash( $_POST['cf_email'] ) ) : '';
	$company = isset( $_POST['cf_company'] ) ? sanitize_text_field( wp_unslash( $_POST['cf_company'] ) ) : '';
	$service = isset( $_POST['cf_service'] ) ? sanitize_text_field( wp_unslash( $_POST['cf_service'] ) ) : '';
	$message = isset( $_POST['cf_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['cf_message'] ) ) : '';

	if ( '' === $name && '' === $phone && '' === $email ) {
		return;
	}

	global $wpdb;

	$inserted = $wpdb->insert(
		wfa_crm_leads_table(),
		array(
			'lead_code'         => wfa_crm_next_lead_code(),
			'name'              => '' !== $name ? $name : 'Website Contact',
			'phone'             => $phone,
			'whatsapp'          => '',
			'email'             => $email,
			'source'            => 'Website Contact Form',
			'platform'          => '',
			'campaign'          => $company,
			'ad_set'            => '',
			'ad'                => '',
			'course_interested' => $service,
			'counselor_id'      => 0,
			'status'            => 'new',
			'lead_date'         => current_time( 'Y-m-d' ),
			'created_by'        => 0,
			'created_at'        => current_time( 'mysql' ),
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s' )
	);

	if ( $inserted && '' !== $message ) {
		$wpdb->insert(
			wfa_crm_conversations_table(),
			array(
				'lead_id'      => (int) $wpdb->insert_id,
				'conv_date'    => current_time( 'Y-m-d' ),
				'conv_time'    => current_time( 'H:i:s' ),
				'channel'      => 'Website Form',
				'counselor_id' => 0,
				'note'         => $message,
				'outcome'      => 'Auto-captured from contact form',
				'created_by'   => 0,
				'created_at'   => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%d', '%s' )
		);
	}
}
add_action( 'admin_post_womensfight_submit_contact_form', 'wfa_crm_capture_public_contact_lead', 5 );
add_action( 'admin_post_nopriv_womensfight_submit_contact_form', 'wfa_crm_capture_public_contact_lead', 5 );
