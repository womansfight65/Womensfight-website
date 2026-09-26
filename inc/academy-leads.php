<?php
/**
 * Women's Fight Academy — Lead System (fully separate from the main
 * agency's Client Projects / Lead Form system).
 *
 * Everything Academy-related lives in this ONE file: its own custom
 * post type, its own form handler, its own admin list, its own CSV
 * export, its own dashboard. No shared functions, no shared post type,
 * no shared meta keys with womensfight_lead_fields() / wf_project.
 * functions.php only requires this file — nothing else touches it.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------
 * Custom post type: Academy Leads
 * ------------------------------------------------------------------- */
function wfa_register_lead_cpt() {
	register_post_type(
		'wfa_lead',
		array(
			'labels'          => array(
				'name'          => 'Academy Leads',
				'singular_name' => 'Academy Lead',
				'menu_name'     => 'Academy Leads',
				'all_items'     => 'সব Lead',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-welcome-learn-more',
			'menu_position'   => 27,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'wfa_register_lead_cpt' );

/**
 * The fields collected by the Academy Counseling form.
 */
function wfa_lead_fields() {
	return array(
		'wfa_name'     => 'শিক্ষার্থীর নাম',
		'wfa_mobile'   => 'Mobile Number',
		'wfa_whatsapp' => 'WhatsApp Number',
		'wfa_status'   => 'বর্তমান অবস্থা',
		'wfa_course'   => 'পছন্দের Course',
		'wfa_location' => 'পছন্দের Training Location',
		'wfa_batch'    => 'পছন্দের Batch Time',
		'wfa_goal'     => 'লক্ষ্য',
		'wfa_message'  => 'সংক্ষিপ্ত Message',
	);
}

/* ---------------------------------------------------------------------
 * Form submission — saves a wfa_lead post, then redirects back with
 * ?wfa_submitted=1 so page-womens-fight-academy.php can show the
 * thank-you message (same redirect-based pattern the rest of the site
 * uses, kept independent here).
 * ------------------------------------------------------------------- */
function wfa_handle_academy_form_submit() {
	if (
		! isset( $_POST['wfa_form_nonce'] ) ||
		! wp_verify_nonce( wp_unslash( $_POST['wfa_form_nonce'] ), 'wfa_submit_academy_form' )
	) {
		wp_die( 'Security check failed. দয়া করে পেজ রিফ্রেশ করে আবার চেষ্টা করুন।' );
	}

	$data = array();
	foreach ( wfa_lead_fields() as $key => $label ) {
		$data[ $key ] = isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '';
	}

	if ( '' !== $data['wfa_name'] ) {
		$title = $data['wfa_name'];
	} elseif ( '' !== $data['wfa_mobile'] ) {
		$title = $data['wfa_mobile'];
	} else {
		$title = 'Academy Lead — ' . current_time( 'Y-m-d H:i' );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'wfa_lead',
			'post_title'  => $title,
			'post_status' => 'publish',
		)
	);

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		foreach ( $data as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
	}

	$redirect = isset( $_POST['wfa_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['wfa_redirect'] ) ) : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'wfa_submitted', '1', $redirect ) . '#wfa-counseling-form' );
	exit;
}
add_action( 'admin_post_wfa_submit_academy_form', 'wfa_handle_academy_form_submit' );
add_action( 'admin_post_nopriv_wfa_submit_academy_form', 'wfa_handle_academy_form_submit' );

/* ---------------------------------------------------------------------
 * Admin list table columns
 * ------------------------------------------------------------------- */
function wfa_lead_columns( $columns ) {
	return array(
		'cb'           => $columns['cb'],
		'title'        => 'নাম / টাইটেল',
		'wfa_mobile'   => 'Mobile',
		'wfa_whatsapp' => 'WhatsApp',
		'wfa_status'   => 'অবস্থা',
		'wfa_course'   => 'Course',
		'wfa_batch'    => 'Batch',
		'date'         => $columns['date'],
	);
}
add_filter( 'manage_wfa_lead_posts_columns', 'wfa_lead_columns' );

function wfa_lead_column_content( $column, $post_id ) {
	if ( in_array( $column, array( 'wfa_mobile', 'wfa_whatsapp', 'wfa_status', 'wfa_course', 'wfa_batch' ), true ) ) {
		echo esc_html( get_post_meta( $post_id, $column, true ) );
	}
}
add_action( 'manage_wfa_lead_posts_custom_column', 'wfa_lead_column_content', 10, 2 );

/* ---------------------------------------------------------------------
 * Read-only detail box on each lead's edit screen
 * ------------------------------------------------------------------- */
function wfa_register_detail_box() {
	add_meta_box( 'wfa_lead_details', 'Lead Details', 'wfa_render_detail_box', 'wfa_lead', 'normal', 'high' );
}
add_action( 'add_meta_boxes_wfa_lead', 'wfa_register_detail_box' );

function wfa_render_detail_box( $post ) {
	echo '<table class="widefat striped"><tbody>';
	foreach ( wfa_lead_fields() as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th style="width:220px;">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

/* ---------------------------------------------------------------------
 * CSV export — its own action name, its own nonce, separate from the
 * main site's lead/contact CSV exports.
 * ------------------------------------------------------------------- */
function wfa_maybe_export_csv() {
	if ( ! isset( $_GET['wfa_export_csv'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	check_admin_referer( 'wfa_export_csv' );

	$posts  = get_posts( array( 'post_type' => 'wfa_lead', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC' ) );
	$fields = wfa_lead_fields();

	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=academy-leads-' . gmdate( 'Y-m-d' ) . '.csv' );

	echo "\xEF\xBB\xBF"; // UTF-8 BOM, so Excel/Bangla text renders correctly.
	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array_merge( array( 'Date' ), array_values( $fields ) ) );
	foreach ( $posts as $p ) {
		$row = array( get_the_date( 'Y-m-d H:i', $p->ID ) );
		foreach ( array_keys( $fields ) as $key ) {
			$row[] = get_post_meta( $p->ID, $key, true );
		}
		fputcsv( $out, $row );
	}
	fclose( $out );
	exit;
}
add_action( 'admin_init', 'wfa_maybe_export_csv' );

/* ---------------------------------------------------------------------
 * Academy Dashboard — a separate admin screen (under the Academy Leads
 * menu, not mixed with the agency's own admin screens) with quick
 * totals, a course/status breakdown, and the most recent leads.
 * ------------------------------------------------------------------- */
function wfa_register_dashboard_page() {
	add_submenu_page(
		'edit.php?post_type=wfa_lead',
		'Academy Dashboard',
		'Dashboard',
		'manage_options',
		'wfa-dashboard',
		'wfa_render_dashboard'
	);
}
add_action( 'admin_menu', 'wfa_register_dashboard_page' );

function wfa_render_dashboard() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$all = get_posts( array( 'post_type' => 'wfa_lead', 'posts_per_page' => -1 ) );

	$by_course = array();
	$by_status = array();
	foreach ( $all as $p ) {
		$course = get_post_meta( $p->ID, 'wfa_course', true );
		$course = '' !== $course ? $course : 'অজানা';
		$status = get_post_meta( $p->ID, 'wfa_status', true );
		$status = '' !== $status ? $status : 'অজানা';

		$by_course[ $course ] = isset( $by_course[ $course ] ) ? $by_course[ $course ] + 1 : 1;
		$by_status[ $status ] = isset( $by_status[ $status ] ) ? $by_status[ $status ] + 1 : 1;
	}

	$export_url = wp_nonce_url( admin_url( 'edit.php?post_type=wfa_lead&wfa_export_csv=1' ), 'wfa_export_csv' );
	$all_url    = admin_url( 'edit.php?post_type=wfa_lead' );

	echo '<div class="wrap">';
	echo '<h1>Women\'s Fight Academy — Dashboard</h1>';
	echo '<p><a href="' . esc_url( $export_url ) . '" class="button button-primary">CSV Export করুন</a> ';
	echo '<a href="' . esc_url( $all_url ) . '" class="button">সব Lead দেখুন</a></p>';

	echo '<div style="background:#fff; border:1px solid #ccd0d4; border-radius:6px; padding:20px; max-width:220px; margin-bottom:24px;">';
	echo '<div style="font-size:2.2rem; font-weight:700; line-height:1;">' . intval( count( $all ) ) . '</div>';
	echo '<div style="color:#646970; margin-top:4px;">Total Academy Lead</div>';
	echo '</div>';

	echo '<div style="display:flex; gap:24px; flex-wrap:wrap;">';

	echo '<div style="min-width:280px;"><h2>Course অনুযায়ী</h2><table class="widefat striped"><tbody>';
	if ( $by_course ) {
		foreach ( $by_course as $course => $count ) {
			echo '<tr><td>' . esc_html( $course ) . '</td><td>' . intval( $count ) . '</td></tr>';
		}
	} else {
		echo '<tr><td>এখনো কোনো Lead নেই</td></tr>';
	}
	echo '</tbody></table></div>';

	echo '<div style="min-width:280px;"><h2>বর্তমান অবস্থা অনুযায়ী</h2><table class="widefat striped"><tbody>';
	if ( $by_status ) {
		foreach ( $by_status as $status => $count ) {
			echo '<tr><td>' . esc_html( $status ) . '</td><td>' . intval( $count ) . '</td></tr>';
		}
	} else {
		echo '<tr><td>এখনো কোনো Lead নেই</td></tr>';
	}
	echo '</tbody></table></div>';

	echo '</div>';

	echo '<h2 style="margin-top:28px;">সাম্প্রতিক Lead</h2>';
	echo '<table class="widefat striped"><thead><tr><th>নাম</th><th>Mobile</th><th>Course</th><th>অবস্থা</th><th>তারিখ</th></tr></thead><tbody>';
	$recent = array_slice( $all, 0, 10 );
	if ( $recent ) {
		foreach ( $recent as $p ) {
			echo '<tr>';
			echo '<td><a href="' . esc_url( get_edit_post_link( $p->ID ) ) . '">' . esc_html( get_the_title( $p->ID ) ) . '</a></td>';
			echo '<td>' . esc_html( get_post_meta( $p->ID, 'wfa_mobile', true ) ) . '</td>';
			echo '<td>' . esc_html( get_post_meta( $p->ID, 'wfa_course', true ) ) . '</td>';
			echo '<td>' . esc_html( get_post_meta( $p->ID, 'wfa_status', true ) ) . '</td>';
			echo '<td>' . esc_html( get_the_date( 'Y-m-d H:i', $p->ID ) ) . '</td>';
			echo '</tr>';
		}
	} else {
		echo '<tr><td colspan="5">এখনো কোনো Lead জমা হয়নি।</td></tr>';
	}
	echo '</tbody></table>';

	echo '</div>';
}
