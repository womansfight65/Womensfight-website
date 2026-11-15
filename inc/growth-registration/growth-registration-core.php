<?php
/**
 * Growth Partnership Program registrations — fully isolated module.
 *
 * Own CPT (`wf_growth_reg`), own admin-post handler, own dashboard menu
 * — completely separate from the regular Contact Messages CPT, so these
 * registrations show up in their own list in wp-admin. Does not touch
 * the existing contact form or any other module.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function womensfight_register_growth_reg_cpt() {
	register_post_type(
		'wf_growth_reg',
		array(
			'labels'          => array(
				'name'          => 'Growth Registrations',
				'singular_name' => 'Growth Registration',
				'menu_name'     => 'Growth Registrations',
				'all_items'     => 'All Registrations',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-awards',
			'menu_position'   => 31,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'womensfight_register_growth_reg_cpt' );

function womensfight_growth_reg_fields() {
	return array(
		'gr_name'     => 'নাম',
		'gr_whatsapp' => 'WhatsApp',
		'gr_business' => 'Business Name',
		'gr_package'  => 'Selected Package',
		'gr_message'  => 'Message',
	);
}

function womensfight_handle_growth_reg_submit() {
	if (
		! isset( $_POST['womensfight_growth_reg_nonce'] ) ||
		! wp_verify_nonce( wp_unslash( $_POST['womensfight_growth_reg_nonce'] ), 'womensfight_growth_reg' )
	) {
		wp_die( 'Security check failed. দয়া করে পেজ রিফ্রেশ করে আবার চেষ্টা করুন।' );
	}

	// Honeypot: real visitors never fill this hidden field.
	if ( ! empty( $_POST['gr_website'] ) ) {
		$redirect = isset( $_POST['gr_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['gr_redirect'] ) ) : home_url( '/' );
		wp_safe_redirect( add_query_arg( 'growth_registered', '1', $redirect ) );
		exit;
	}

	$data = array();
	foreach ( womensfight_growth_reg_fields() as $key => $label ) {
		$data[ $key ] = isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '';
	}

	if ( '' === trim( $data['gr_name'] ) || '' === trim( $data['gr_whatsapp'] ) ) {
		$redirect = isset( $_POST['gr_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['gr_redirect'] ) ) : home_url( '/' );
		wp_safe_redirect( add_query_arg( 'growth_registered', '0', $redirect ) );
		exit;
	}

	$title = $data['gr_name'] . ' — ' . $data['gr_package'];

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'wf_growth_reg',
			'post_title'  => $title,
			'post_status' => 'publish',
		)
	);

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		foreach ( $data as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
	}

	$redirect = isset( $_POST['gr_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['gr_redirect'] ) ) : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'growth_registered', '1', $redirect ) . '#wf-growth-reg' );
	exit;
}
add_action( 'admin_post_womensfight_submit_growth_reg', 'womensfight_handle_growth_reg_submit' );
add_action( 'admin_post_nopriv_womensfight_submit_growth_reg', 'womensfight_handle_growth_reg_submit' );

function womensfight_growth_reg_columns( $columns ) {
	return array(
		'cb'          => $columns['cb'],
		'title'       => 'Name — Package',
		'gr_whatsapp' => 'WhatsApp',
		'gr_business' => 'Business',
		'gr_package'  => 'Package',
		'date'        => $columns['date'],
	);
}
add_filter( 'manage_wf_growth_reg_posts_columns', 'womensfight_growth_reg_columns' );

function womensfight_growth_reg_column_content( $column, $post_id ) {
	if ( in_array( $column, array( 'gr_whatsapp', 'gr_business', 'gr_package' ), true ) ) {
		echo esc_html( get_post_meta( $post_id, $column, true ) );
	}
}
add_action( 'manage_wf_growth_reg_posts_custom_column', 'womensfight_growth_reg_column_content', 10, 2 );

function womensfight_register_growth_reg_detail_box() {
	add_meta_box( 'womensfight_growth_reg_details', 'Registration Details', 'womensfight_render_growth_reg_detail_box', 'wf_growth_reg', 'normal', 'high' );
}
add_action( 'add_meta_boxes_wf_growth_reg', 'womensfight_register_growth_reg_detail_box' );

function womensfight_render_growth_reg_detail_box( $post ) {
	echo '<table class="widefat striped"><tbody>';
	foreach ( womensfight_growth_reg_fields() as $key => $label ) {
		echo '<tr><th style="width:220px;">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( get_post_meta( $post->ID, $key, true ) ) ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

/**
 * Append the registration modal/form (and success message) to the
 * Price page's own content, right where its "#wf-growth-reg" anchor
 * div sits — scoped strictly to that one page slug, so no other page
 * is touched. admin_url()/get_permalink()/wp_nonce_field() only work
 * as real PHP here, not inside the static nowdoc content file.
 */
function womensfight_append_growth_reg_form( $content ) {
	if ( ! is_page( 'price' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	if ( false === strpos( $content, 'id="wf-growth-reg"' ) ) {
		return $content;
	}

	ob_start();

	if ( isset( $_GET['growth_registered'] ) ) {
		if ( '1' === $_GET['growth_registered'] ) {
			echo '<section class="tight wrap"><div class="wf-cform-success" style="padding:22px;"><p>ধন্যবাদ। আপনার রেজিস্ট্রেশন আমরা পেয়েছি — শীঘ্রই যোগাযোগ করব।</p></div></section>';
		} else {
			echo '<section class="tight wrap"><div class="wf-cform-success" style="padding:22px;"><p>নাম ও WhatsApp নম্বর দেওয়া জরুরি — আবার চেষ্টা করুন।</p></div></section>';
		}
	}
	?>
	<div class="wf-modal-overlay" id="wfGrowthRegOverlay" onclick="if(event.target===this){wfCloseGrowthReg();}">
		<div class="wf-modal">
			<button type="button" class="wf-modal-close" onclick="wfCloseGrowthReg();" aria-label="Close">&times;</button>
			<div class="wf-cform">
				<h3 style="margin:0 0 6px;">Growth Partnership রেজিস্ট্রেশন</h3>
				<p class="wf-dash-hint">নিচের তথ্য দিন, আমরা দ্রুত যোগাযোগ করব।</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<input type="hidden" name="action" value="womensfight_submit_growth_reg">
					<input type="hidden" name="gr_redirect" value="<?php echo esc_url( get_permalink() ); ?>">
					<input type="hidden" name="gr_package" id="wfGrowthPackageField" value="">
					<div style="position:absolute; left:-9999px;"><label for="gr_website">Website</label><input type="text" id="gr_website" name="gr_website" tabindex="-1" autocomplete="off"></div>
					<?php wp_nonce_field( 'womensfight_growth_reg', 'womensfight_growth_reg_nonce' ); ?>
					<div><label>Selected Package</label><input type="text" id="wfGrowthPackageDisplay" readonly></div>
					<div class="frow">
						<div><label for="gr_name">নাম</label><input type="text" id="gr_name" name="gr_name" placeholder="আপনার নাম" required></div>
						<div><label for="gr_whatsapp">WhatsApp</label><input type="tel" id="gr_whatsapp" name="gr_whatsapp" placeholder="01XXXXXXXXX" required></div>
					</div>
					<div><label for="gr_business">Business Name</label><input type="text" id="gr_business" name="gr_business" placeholder="আপনার ব্যবসার নাম"></div>
					<div><label for="gr_message">Message (ঐচ্ছিক)</label><textarea id="gr_message" name="gr_message" rows="3" placeholder="আপনার ব্যবসা সম্পর্কে সংক্ষেপে লিখুন"></textarea></div>
					<button class="btn btn-primary" type="submit">রেজিস্ট্রেশন সাবমিট করুন</button>
				</form>
			</div>
		</div>
	</div>
	<script>
	function wfOpenGrowthReg(pkg) {
		document.getElementById('wfGrowthPackageField').value = pkg;
		document.getElementById('wfGrowthPackageDisplay').value = pkg;
		document.getElementById('wfGrowthRegOverlay').classList.add('open');
		document.body.style.overflow = 'hidden';
	}
	function wfCloseGrowthReg() {
		document.getElementById('wfGrowthRegOverlay').classList.remove('open');
		document.body.style.overflow = '';
	}
	</script>
	<?php
	return $content . ob_get_clean();
}
add_filter( 'the_content', 'womensfight_append_growth_reg_form' );
