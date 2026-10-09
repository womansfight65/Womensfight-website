<?php
/**
 * SMM Service Request — backend for the /smm-service/ landing page.
 *
 * Fully isolated module: one new post type (wf_smm_request, viewed in
 * wp-admin exactly like Contact Messages/Demo Requests — not a custom
 * dashboard), one admin-post.php handler. Does not touch Leads, CRM,
 * Accounts, or Invoice. This is a lead-capture form only — no pricing,
 * no checkout, no automatic order placement anywhere in this file.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wf_smm_platforms() {
	return array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'tiktok'    => 'TikTok',
		'telegram'  => 'Telegram',
		'x'         => 'X (Twitter)',
		'linkedin'  => 'LinkedIn',
		'spotify'   => 'Spotify',
		'snapchat'  => 'Snapchat',
		'discord'   => 'Discord',
	);
}

/**
 * Services shown per platform — only what the business actually
 * offers today. Edit this list any time; the page JS reads it from
 * the same array (passed to it as JSON), so nothing else needs to
 * change when a service is added or removed.
 */
function wf_smm_services() {
	return array(
		'facebook'  => array( 'Page Promotion', 'Post Promotion', 'Video Promotion', 'Content Engagement' ),
		'instagram' => array( 'Profile Promotion', 'Post Promotion', 'Reels Promotion' ),
		'youtube'   => array( 'Channel Promotion', 'Video Promotion' ),
		'tiktok'    => array( 'Profile Promotion', 'Video Promotion' ),
		'telegram'  => array( 'Channel Promotion', 'Post Engagement' ),
		'x'         => array( 'Profile Promotion', 'Post Engagement' ),
		'linkedin'  => array( 'Profile Promotion', 'Post Promotion' ),
		'spotify'   => array( 'Profile/Playlist Promotion' ),
		'snapchat'  => array( 'Profile Promotion' ),
		'discord'   => array( 'Server Growth', 'Member Engagement' ),
	);
}

/* ---------------------------------------------------------------------
 * Post type — "SMM Requests" in wp-admin, same pattern as Contact
 * Messages / Demo Requests.
 * ------------------------------------------------------------------- */
function wf_smm_register_cpt() {
	register_post_type(
		'wf_smm_request',
		array(
			'labels'          => array(
				'name'          => 'SMM Requests',
				'singular_name' => 'SMM Request',
				'menu_name'     => 'SMM Requests',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-megaphone',
			'menu_position'   => 30,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'wf_smm_register_cpt' );

function wf_smm_columns( $columns ) {
	return array(
		'cb'           => $columns['cb'],
		'title'        => 'নাম / টাইটেল',
		'wf_mobile'    => 'Mobile',
		'wf_platform'  => 'Platform',
		'wf_service'   => 'Service',
		'wf_quantity'  => 'Quantity',
		'date'         => $columns['date'],
	);
}
add_filter( 'manage_wf_smm_request_posts_columns', 'wf_smm_columns' );

function wf_smm_column_content( $column, $post_id ) {
	$map = array(
		'wf_mobile'   => 'wf_smm_mobile',
		'wf_platform' => 'wf_smm_platform_label',
		'wf_service'  => 'wf_smm_service',
		'wf_quantity' => 'wf_smm_quantity',
	);
	if ( isset( $map[ $column ] ) ) {
		echo esc_html( get_post_meta( $post_id, $map[ $column ], true ) );
	}
}
add_action( 'manage_wf_smm_request_posts_custom_column', 'wf_smm_column_content', 10, 2 );

function wf_smm_register_detail_box() {
	add_meta_box( 'wf_smm_details', 'Request Details', 'wf_smm_render_detail_box', 'wf_smm_request', 'normal', 'high' );
}
add_action( 'add_meta_boxes_wf_smm_request', 'wf_smm_register_detail_box' );

function wf_smm_render_detail_box( $post ) {
	$fields = array(
		'wf_smm_name'           => 'নাম',
		'wf_smm_mobile'         => 'মোবাইল নম্বর',
		'wf_smm_platform_label' => 'Platform',
		'wf_smm_service'        => 'Service',
		'wf_smm_link'           => 'Link',
		'wf_smm_quantity'       => 'Quantity',
		'wf_smm_extra'          => 'অতিরিক্ত তথ্য',
	);
	echo '<table class="widefat"><tbody>';
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th style="width:180px;">' . esc_html( $label ) . '</th><td>' . ( 'wf_smm_link' === $key && $value ? '<a href="' . esc_url( $value ) . '" target="_blank" rel="noopener">' . esc_html( $value ) . '</a>' : esc_html( $value ) ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

/* ---------------------------------------------------------------------
 * Form handler
 * ------------------------------------------------------------------- */
function wf_smm_valid_bd_mobile( $mobile ) {
	$digits = preg_replace( '/\D/', '', $mobile );
	return (bool) preg_match( '/^(?:88)?01[3-9]\d{8}$/', $digits );
}

function wf_smm_handle_submit() {
	// Honeypot — a real visitor never fills this hidden field.
	if ( ! empty( $_POST['wf_smm_website'] ) ) {
		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : home_url( '/' ) );
		exit;
	}

	// Time-trap — a form submitted in under 2 seconds is almost
	// certainly a bot, not a person reading and filling the form.
	$rendered_at = isset( $_POST['wf_smm_ts'] ) ? absint( $_POST['wf_smm_ts'] ) : 0;
	if ( $rendered_at && ( time() - $rendered_at ) < 2 ) {
		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : home_url( '/' ) );
		exit;
	}

	if (
		! isset( $_POST['wf_smm_nonce'] ) ||
		! wp_verify_nonce( wp_unslash( $_POST['wf_smm_nonce'] ), 'wf_smm_request' )
	) {
		wp_die( 'Security check failed। দয়া করে পেজ রিফ্রেশ করে আবার চেষ্টা করুন।' );
	}

	$redirect = isset( $_POST['wf_smm_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['wf_smm_redirect'] ) ) : home_url( '/' );

	$name     = isset( $_POST['wf_smm_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wf_smm_name'] ) ) : '';
	$mobile   = isset( $_POST['wf_smm_mobile'] ) ? sanitize_text_field( wp_unslash( $_POST['wf_smm_mobile'] ) ) : '';
	$platform = isset( $_POST['wf_smm_platform'] ) ? sanitize_key( wp_unslash( $_POST['wf_smm_platform'] ) ) : '';
	$service  = isset( $_POST['wf_smm_service'] ) ? sanitize_text_field( wp_unslash( $_POST['wf_smm_service'] ) ) : '';
	$link     = isset( $_POST['wf_smm_link'] ) ? esc_url_raw( wp_unslash( $_POST['wf_smm_link'] ) ) : '';
	$quantity = isset( $_POST['wf_smm_quantity'] ) ? sanitize_text_field( wp_unslash( $_POST['wf_smm_quantity'] ) ) : '';
	$extra    = isset( $_POST['wf_smm_extra'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wf_smm_extra'] ) ) : '';

	$errors = array();
	if ( '' === $name ) {
		$errors[] = 'name';
	}
	if ( ! wf_smm_valid_bd_mobile( $mobile ) ) {
		$errors[] = 'mobile';
	}
	$platforms = wf_smm_platforms();
	if ( ! isset( $platforms[ $platform ] ) ) {
		$errors[] = 'platform';
	}
	if ( '' === $service ) {
		$errors[] = 'service';
	}
	if ( '' === $link || ! filter_var( $link, FILTER_VALIDATE_URL ) ) {
		$errors[] = 'link';
	}
	if ( ! ctype_digit( (string) $quantity ) || (int) $quantity < 1 ) {
		$errors[] = 'quantity';
	}

	if ( $errors ) {
		wp_safe_redirect( add_query_arg( 'smm_error', implode( ',', $errors ), $redirect ) . '#smm-request-form' );
		exit;
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'wf_smm_request',
			'post_title'  => $name . ' — ' . $platforms[ $platform ],
			'post_status' => 'publish',
		)
	);

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		update_post_meta( $post_id, 'wf_smm_name', $name );
		update_post_meta( $post_id, 'wf_smm_mobile', $mobile );
		update_post_meta( $post_id, 'wf_smm_platform', $platform );
		update_post_meta( $post_id, 'wf_smm_platform_label', $platforms[ $platform ] );
		update_post_meta( $post_id, 'wf_smm_service', $service );
		update_post_meta( $post_id, 'wf_smm_link', $link );
		update_post_meta( $post_id, 'wf_smm_quantity', $quantity );
		update_post_meta( $post_id, 'wf_smm_extra', $extra );
	}

	wp_safe_redirect( add_query_arg( 'smm_submitted', '1', $redirect ) . '#smm-request-form' );
	exit;
}
add_action( 'admin_post_wf_smm_submit_request', 'wf_smm_handle_submit' );
add_action( 'admin_post_nopriv_wf_smm_submit_request', 'wf_smm_handle_submit' );
