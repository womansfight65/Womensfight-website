<?php
/**
 * Women's Fight Agency theme setup.
 *
 * This theme creates real, editable WordPress Pages (Home, the five
 * service pages, Case Study, Portfolio, Action Plan, Package, AI Agent
 * and Contact Us) plus a matching navigation menu the first time it is
 * activated. Because the content lives in normal WordPress Pages, it can
 * be edited with the block editor immediately.
 *
 * Every page is also pre-marked as an Elementor document (see
 * womensfight_elementor_widget_data() and its use below), with its real
 * content already placed inside a single, full-width Elementor "HTML"
 * widget. That means opening any page and clicking "Edit with Elementor"
 * (once the free Elementor plugin is installed — the theme now asks
 * WordPress to prompt for that on activation) shows the actual live page
 * immediately instead of a blank canvas, and the page can then be
 * rearranged, extended with normal Elementor widgets, or have that HTML
 * widget's code edited directly from Elementor's own code panel.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------------------------------------------------------------------
 * Theme setup
 * ------------------------------------------------------------------- */
function womensfight_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );
	add_theme_support( 'align-wide' );
	register_nav_menus(
		array(
			'primary' => __( 'Primary Menu', 'womens-fight' ),
		)
	);
}
add_action( 'after_setup_theme', 'womensfight_setup' );

function womensfight_assets() {
	wp_enqueue_style(
		'womensfight-google-fonts',
		'https://fonts.googleapis.com/css2?family=Noto+Serif+Bengali:wght@500;600;700&family=Hind+Siliguri:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);

	wp_enqueue_style(
		'womensfight-style',
		get_stylesheet_uri(),
		array( 'womensfight-google-fonts' ),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_script(
		'womensfight-main',
		get_template_directory_uri() . '/assets/js/main.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'womensfight_assets' );

/**
 * Look up a page's URL by its slug, safely. Returns '#' if the page
 * doesn't exist yet (e.g. setup hasn't run).
 */
function womensfight_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : '#';
}

/**
 * Build the Elementor element tree for a page: one full-width section,
 * one 100%-width column, one "HTML" widget holding the page's real
 * markup. This is the same shape Elementor itself would save, so the
 * page opens pre-filled in the Elementor editor instead of blank, and
 * new Elementor widgets can be dragged in above, below or around it.
 */
function womensfight_elementor_widget_data( $html ) {
	return array(
		array(
			'id'       => substr( md5( uniqid( 'wf_sec_', true ) ), 0, 7 ),
			'elType'   => 'section',
			'settings' => array(
				'content_width' => 'full',
				'gap'           => 'no',
			),
			'elements' => array(
				array(
					'id'       => substr( md5( uniqid( 'wf_col_', true ) ), 0, 7 ),
					'elType'   => 'column',
					'settings' => array(
						'_column_size' => 100,
					),
					'elements' => array(
						array(
							'id'         => substr( md5( uniqid( 'wf_wid_', true ) ), 0, 7 ),
							'elType'     => 'widget',
							'widgetType' => 'html',
							'settings'   => array(
								'html' => $html,
							),
							'elements'   => array(),
						),
					),
					'isInner'  => false,
				),
			),
			'isInner'  => false,
		),
	);
}

/**
 * Mark a page as an Elementor document and store its content as
 * Elementor data, mirroring what Elementor itself writes on save. Only
 * runs for pages that have no Elementor data yet, so it never overwrites
 * a page the user has since rebuilt or rearranged inside Elementor.
 */
function womensfight_save_elementor_data( $post_id, $html ) {
	if ( get_post_meta( $post_id, '_elementor_data', true ) ) {
		return;
	}
	update_post_meta( $post_id, '_elementor_data', wp_slash( wp_json_encode( womensfight_elementor_widget_data( $html ) ) ) );
	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
	update_post_meta( $post_id, '_elementor_version', '3.7.0' );
}

/* ---------------------------------------------------------------------
 * Page definitions — slug => [ title, content file ]
 * Content files live in inc/content/{slug}.php and each `return`s an
 * HTML string. Internal links inside that HTML use the placeholder
 * token ##LINK:slug## which is swapped for the real page URL once every
 * page has been created (see womensfight_install_pages_and_menu()).
 * ------------------------------------------------------------------- */
function womensfight_page_definitions() {
	return array(
		'home'                 => 'Home',
		'digital-marketing'    => 'ডিজিটাল মার্কেটিং',
		'website-development'  => 'ওয়েবসাইট ডেভেলপমেন্ট',
		'landing-page'         => 'ল্যান্ডিং পেজ',
		'video-production'     => 'ভিডিও প্রোডাকশন',
		'ai-agency'            => 'AI Agency',
		'case-study'           => 'Case Study',
		'portfolio'            => 'Portfolio',
		'action-plan'          => 'Action Plan',
		'package'              => 'Package',
		'ai-agent'             => 'AI Agent',
		'contact'              => 'Contact Us',
	);
}

function womensfight_menu_structure() {
	return array(
		array( 'title' => 'Home', 'slug' => 'home' ),
		array(
			'title'    => 'Service',
			'children' => array(
				array( 'title' => 'Digital Marketing', 'slug' => 'digital-marketing' ),
				array( 'title' => 'Website Development', 'slug' => 'website-development' ),
				array( 'title' => 'Landing Page', 'slug' => 'landing-page' ),
				array( 'title' => 'Video Production', 'slug' => 'video-production' ),
				array( 'title' => 'AI Agency', 'slug' => 'ai-agency' ),
			),
		),
		array( 'title' => 'Case Study', 'slug' => 'case-study' ),
		array( 'title' => 'Portfolio', 'slug' => 'portfolio' ),
		array( 'title' => 'Action Plan', 'slug' => 'action-plan' ),
		array( 'title' => 'Package', 'slug' => 'package' ),
		array( 'title' => 'AI Agent', 'slug' => 'ai-agent' ),
		array( 'title' => 'Contact Us', 'slug' => 'contact' ),
	);
}

/* ---------------------------------------------------------------------
 * One-time install: create the pages + menu + homepage setting.
 * Safe to run more than once — it skips anything that already exists.
 * ------------------------------------------------------------------- */
function womensfight_install_pages_and_menu() {

	$slug_to_id = array();
	$logo_icon  = esc_url( get_template_directory_uri() . '/assets/img/logo-icon.png' );

	foreach ( womensfight_page_definitions() as $slug => $title ) {
		$existing = get_page_by_path( $slug );
		if ( $existing ) {
			$slug_to_id[ $slug ] = $existing->ID;

			// Some hosting providers' one-click WordPress installers
			// pre-create a placeholder page at a common slug (most often
			// "home") before this theme ever runs. get_page_by_path()
			// finds that placeholder and, without this check, the real
			// content below would be skipped forever, leaving that page
			// permanently empty. Fill it in — but only if it is genuinely
			// empty and nobody has since built it out in Elementor, so a
			// page with real content or real Elementor work is never
			// touched.
			$is_empty = '' === trim( wp_strip_all_tags( $existing->post_content ) );
			$has_elementor_work = (bool) get_post_meta( $existing->ID, '_elementor_data', true );

			if ( $is_empty && ! $has_elementor_work ) {
				$content_file = get_template_directory() . '/inc/content/' . $slug . '.php';
				$content      = file_exists( $content_file ) ? include $content_file : '';
				if ( '' !== $content ) {
					wp_update_post(
						array(
							'ID'           => $existing->ID,
							'post_title'   => $existing->post_title ? $existing->post_title : $title,
							'post_content' => $content,
							'post_status'  => 'publish',
						)
					);
				}
			} elseif ( 'publish' !== $existing->post_status ) {
				// A page we depend on (e.g. as the front page) shouldn't
				// silently sit in Draft/Trash — that makes it vanish from
				// the live site even though it still technically "exists".
				wp_update_post(
					array(
						'ID'          => $existing->ID,
						'post_status' => 'publish',
					)
				);
			}
			continue;
		}

		$content_file = get_template_directory() . '/inc/content/' . $slug . '.php';
		$content      = file_exists( $content_file ) ? include $content_file : '';

		$id = wp_insert_post(
			array(
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_content' => $content,
				'post_status'  => 'publish',
				'post_type'    => 'page',
			)
		);

		if ( $id && ! is_wp_error( $id ) ) {
			$slug_to_id[ $slug ] = $id;
		}
	}

	// Second pass: swap ##LINK:slug## tokens for real permalinks now that
	// every page id is known, and ##LOGO_ICON## for the logo file URL.
	foreach ( $slug_to_id as $slug => $id ) {
		$post = get_post( $id );
		if ( ! $post ) {
			continue;
		}
		$content = $post->post_content;

		$new_content = preg_replace_callback(
			'/##LINK:([a-z0-9-]+)##/',
			function ( $m ) use ( $slug_to_id ) {
				return isset( $slug_to_id[ $m[1] ] ) ? esc_url( get_permalink( $slug_to_id[ $m[1] ] ) ) : '#';
			},
			$content
		);
		$new_content = str_replace( '##LOGO_ICON##', $logo_icon, $new_content );

		if ( $new_content !== $content ) {
			wp_update_post(
				array(
					'ID'           => $id,
					'post_content' => $new_content,
				)
			);
		}

		womensfight_save_elementor_data( $id, $new_content );
	}

	// Set the Home page as the site's front page.
	if ( isset( $slug_to_id['home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $slug_to_id['home'] );
	}

	// Build the navigation menu if it doesn't already exist.
	$menu_name   = 'Women\'s Fight Primary Menu';
	$menu_object = wp_get_nav_menu_object( $menu_name );

	if ( ! $menu_object ) {
		$menu_id = wp_create_nav_menu( $menu_name );

		foreach ( womensfight_menu_structure() as $item ) {
			if ( isset( $item['children'] ) ) {
				$parent_id = wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $item['title'],
						'menu-item-url'    => '#',
						'menu-item-status' => 'publish',
					)
				);
				foreach ( $item['children'] as $child ) {
					if ( ! isset( $slug_to_id[ $child['slug'] ] ) ) {
						continue;
					}
					wp_update_nav_menu_item(
						$menu_id,
						0,
						array(
							'menu-item-title'     => $child['title'],
							'menu-item-object-id' => $slug_to_id[ $child['slug'] ],
							'menu-item-object'    => 'page',
							'menu-item-type'      => 'post_type',
							'menu-item-parent-id' => $parent_id,
							'menu-item-status'    => 'publish',
						)
					);
				}
			} elseif ( isset( $slug_to_id[ $item['slug'] ] ) ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'     => $item['title'],
						'menu-item-object-id' => $slug_to_id[ $item['slug'] ],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
					)
				);
			}
		}

		$locations               = get_theme_mod( 'nav_menu_locations' );
		$locations               = is_array( $locations ) ? $locations : array();
		$locations['primary']    = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );
	}

	update_option( 'womensfight_setup_done', 1 );
	set_transient( 'womensfight_setup_just_ran', 1, MINUTE_IN_SECONDS );
}
add_action( 'after_switch_theme', 'womensfight_install_pages_and_menu' );

/**
 * Fallback: if the pages/menu were never created (for example the theme
 * was uploaded and activated before this version, or a host skipped the
 * activation hook), show an admin notice with a one-click button to run
 * setup manually. Once setup has run at least once, the same button stays
 * available in a lower-key form on the Themes screen — re-running it is
 * always safe (see womensfight_install_pages_and_menu()) and is the way
 * to repair a page a hosting installer left empty, or the front page if
 * it ever ends up unpublished, without needing to deactivate/reactivate
 * the theme.
 */
function womensfight_admin_setup_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$url = wp_nonce_url( admin_url( 'themes.php?womensfight_run_setup=1' ), 'womensfight_run_setup' );

	if ( ! get_option( 'womensfight_setup_done' ) ) {
		echo '<div class="notice notice-info"><p><strong>Women\'s Fight থিম:</strong> সাইটের পেজ ও মেনু এখনো তৈরি হয়নি। ';
		echo '<a href="' . esc_url( $url ) . '" class="button button-primary">এখনই সেটআপ করুন</a></p></div>';
		return;
	}

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && 'themes' === $screen->id ) {
		echo '<div class="notice notice-info"><p><strong>Women\'s Fight থিম:</strong> কোনো পেজ (যেমন হোম) ফাঁকা দেখালে ';
		echo '<a href="' . esc_url( $url ) . '">এখানে ক্লিক করে পেজ ও মেনু আবার সিঙ্ক করুন</a> — এটা নিরাপদ, বিদ্যমান কনটেন্ট মুছবে না।</p></div>';
	}
}
add_action( 'admin_notices', 'womensfight_admin_setup_notice' );

/**
 * One-time, friendly confirmation shown right after setup finishes
 * (whether that happened automatically on theme activation, or via the
 * manual button above), explaining in plain terms how to start editing.
 */
function womensfight_setup_success_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! get_transient( 'womensfight_setup_just_ran' ) ) {
		return;
	}
	delete_transient( 'womensfight_setup_just_ran' );

	$elementor_active = did_action( 'elementor/loaded' );
	$edit_home_url    = admin_url( 'post.php?post=' . (int) get_option( 'page_on_front' ) . '&action=edit' );

	echo '<div class="notice notice-success is-dismissible"><p><strong>Women\'s Fight থিম:</strong> সাইটের সবগুলো পেজ ও মেনু তৈরি হয়ে গেছে। ';
	echo 'Pages → All Pages থেকে যেকোনো পেজ খুলে সরাসরি লেখা/ছবি বদলাতে পারবেন। ';
	if ( $elementor_active ) {
		echo 'Elementor প্লাগইনও চালু আছে — পেজ খুলে "Edit with Elementor" চাপলে বর্তমান ডিজাইন নিয়েই এডিটর খুলবে, খালি থাকবে না।';
	} else {
		echo 'সম্পূর্ণ ড্র্যাগ-অ্যান্ড-ড্রপে এডিট করতে চাইলে Elementor প্লাগইন ইনস্টল করুন (Appearance পেজে ইনস্টলের অপশন পাবেন) — ইনস্টলের পর যেকোনো পেজে "Edit with Elementor" চাপলে বর্তমান ডিজাইন নিয়েই এডিটর খুলবে।';
	}
	echo ' <a href="' . esc_url( $edit_home_url ) . '">হোম পেজ এখনই খুলুন →</a></p></div>';
}
add_action( 'admin_notices', 'womensfight_setup_success_notice' );

function womensfight_maybe_run_manual_setup() {
	if ( ! isset( $_GET['womensfight_run_setup'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	check_admin_referer( 'womensfight_run_setup' );
	womensfight_install_pages_and_menu();
	wp_safe_redirect( admin_url( 'themes.php' ) );
	exit;
}
add_action( 'admin_init', 'womensfight_maybe_run_manual_setup' );

/**
 * Force a single page's content back to whatever inc/content/{slug}.php
 * currently returns, overwriting any live edits to that page. Unlike
 * womensfight_install_pages_and_menu() (which never touches a page that
 * already has content, to protect hand-made edits), this is an explicit,
 * deliberate "reset this page to the theme's latest design" action.
 */
function womensfight_refresh_page_from_code( $slug ) {
	$page = get_page_by_path( $slug );
	if ( ! $page ) {
		return array( 'ok' => false, 'reason' => 'no_page_found_for_slug' );
	}

	$content_file = get_template_directory() . '/inc/content/' . $slug . '.php';
	if ( ! file_exists( $content_file ) ) {
		return array( 'ok' => false, 'reason' => 'content_file_missing', 'path' => $content_file );
	}
	$content = include $content_file;

	// Resolve ##LINK:slug## / ##LOGO_ICON## against every page that
	// currently exists, the same way the installer does.
	$slug_to_id = array();
	foreach ( array_keys( womensfight_page_definitions() ) as $other_slug ) {
		$other = get_page_by_path( $other_slug );
		if ( $other ) {
			$slug_to_id[ $other_slug ] = $other->ID;
		}
	}
	$content = preg_replace_callback(
		'/##LINK:([a-z0-9-]+)##/',
		function ( $m ) use ( $slug_to_id ) {
			return isset( $slug_to_id[ $m[1] ] ) ? esc_url( get_permalink( $slug_to_id[ $m[1] ] ) ) : '#';
		},
		$content
	);
	$content = str_replace( '##LOGO_ICON##', esc_url( get_template_directory_uri() . '/assets/img/logo-icon.png' ), $content );

	$update_result = wp_update_post(
		array(
			'ID'           => $page->ID,
			'post_content' => $content,
			'post_status'  => 'publish',
		),
		true // return WP_Error on failure instead of 0
	);

	update_post_meta( $page->ID, '_elementor_data', wp_slash( wp_json_encode( womensfight_elementor_widget_data( $content ) ) ) );
	update_post_meta( $page->ID, '_elementor_edit_mode', 'builder' );
	update_post_meta( $page->ID, '_elementor_template_type', 'wp-page' );
	update_post_meta( $page->ID, '_elementor_version', '3.7.0' );

	// Re-fetch straight from the database (bypassing any object cache) to
	// see what actually landed, not just what we attempted to write.
	clean_post_cache( $page->ID );
	$refetched      = get_post( $page->ID );
	$stored_content = $refetched ? $refetched->post_content : '';

	return array(
		'ok'                  => ! is_wp_error( $update_result ),
		'error'               => is_wp_error( $update_result ) ? $update_result->get_error_message() : '',
		'page_id'             => $page->ID,
		'attempted_length'    => strlen( $content ),
		'stored_length'       => strlen( $stored_content ),
		'stored_has_wordmark' => false !== strpos( $stored_content, 'hero-brand-text' ),
		'stored_has_svg'      => false !== strpos( $stored_content, '<svg' ),
		'stored_snippet'      => mb_substr( wp_strip_all_tags( $stored_content ), 0, 120 ),
	);
}

/**
 * Admin notice + one-click action: reset any page to the theme's current
 * inc/content/{slug}.php. Shown on the Themes screen next to the general
 * sync notice. This deliberately overwrites that page's content, so it's
 * a separate, explicitly-labelled form rather than part of the safe
 * general sync (which never touches a page that already has content).
 */
function womensfight_refresh_page_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! get_option( 'womensfight_setup_done' ) ) {
		return;
	}
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( ! $screen || 'themes' !== $screen->id ) {
		return;
	}
	echo '<div class="notice notice-warning"><p><strong>Women\'s Fight থিম:</strong> কোনো পেজে থিমের সর্বশেষ কোড (inc/content/) থেকে ডিজাইন/কনটেন্ট আনতে চাইলে পেজ বেছে নিয়ে রিফ্রেশ করুন — ';
	echo 'সতর্কতা: এটা সেই পেজে wp-admin থেকে করা যেকোনো ম্যানুয়াল এডিট মুছে দেবে।</p>';
	echo '<form method="get" action="' . esc_url( admin_url( 'themes.php' ) ) . '" style="display:flex;gap:8px;align-items:center;">';
	wp_nonce_field( 'womensfight_refresh_page', '_wpnonce', true, true );
	echo '<select name="womensfight_refresh_slug">';
	foreach ( womensfight_page_definitions() as $slug => $title ) {
		echo '<option value="' . esc_attr( $slug ) . '">' . esc_html( $title . ' (' . $slug . ')' ) . '</option>';
	}
	echo '</select> ';
	echo '<button type="submit" class="button">এই পেজ কোড থেকে রিফ্রেশ করুন</button>';
	echo '</form></div>';
}
add_action( 'admin_notices', 'womensfight_refresh_page_notice' );

function womensfight_maybe_refresh_page() {
	if ( ! isset( $_GET['womensfight_refresh_slug'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	check_admin_referer( 'womensfight_refresh_page' );

	$slug = sanitize_title( wp_unslash( $_GET['womensfight_refresh_slug'] ) );
	if ( ! array_key_exists( $slug, womensfight_page_definitions() ) ) {
		wp_safe_redirect( admin_url( 'themes.php' ) );
		exit;
	}

	$result = womensfight_refresh_page_from_code( $slug );
	set_transient( 'womensfight_refresh_result', $result, MINUTE_IN_SECONDS );
	set_transient( 'womensfight_refresh_slug', $slug, MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'themes.php?womensfight_refreshed=1' ) );
	exit;
}
add_action( 'admin_init', 'womensfight_maybe_refresh_page' );

/**
 * Shows exactly what the refresh action actually did — not just "it ran"
 * but what ended up stored in the database once re-read fresh, so a
 * mismatch (a caching layer, a security plugin stripping tags, etc.) is
 * visible immediately instead of guessed at.
 */
function womensfight_refresh_done_notice() {
	if ( ! current_user_can( 'manage_options' ) || ! isset( $_GET['womensfight_refreshed'] ) ) {
		return;
	}
	$r    = get_transient( 'womensfight_refresh_result' );
	$slug = get_transient( 'womensfight_refresh_slug' );
	delete_transient( 'womensfight_refresh_result' );
	delete_transient( 'womensfight_refresh_slug' );

	if ( ! is_array( $r ) ) {
		echo '<div class="notice notice-error"><p><strong>Women\'s Fight থিম:</strong> রিফ্রেশ রেজাল্ট পাওয়া যায়নি (transient ফেল করেছে) — আবার চেষ্টা করুন।</p></div>';
		return;
	}
	if ( ! $r['ok'] ) {
		echo '<div class="notice notice-error"><p><strong>Women\'s Fight থিম:</strong> রিফ্রেশ ব্যর্থ হয়েছে। কারণ: <code>' . esc_html( $r['reason'] ?? $r['error'] ?? 'unknown' ) . '</code></p></div>';
		return;
	}

	$class = ( $r['attempted_length'] === $r['stored_length'] ) ? 'notice-success' : 'notice-error';

	echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p><strong>Women\'s Fight থিম — রিফ্রেশ ডায়াগনস্টিক (' . esc_html( $slug ? $slug : '?' ) . '):</strong></p>';
	echo '<ul style="list-style:disc;margin-left:20px;">';
	echo '<li>Page ID: ' . (int) $r['page_id'] . '</li>';
	echo '<li>যা লিখতে চেয়েছি (attempted): ' . (int) $r['attempted_length'] . ' bytes</li>';
	echo '<li>ডাটাবেসে আসলে যা আছে (stored, fresh re-read): ' . (int) $r['stored_length'] . ' bytes</li>';
	if ( 'home' === $slug ) {
		echo '<li>Stored content-এ wordmark class আছে কিনা: ' . ( $r['stored_has_wordmark'] ? 'হ্যাঁ' : '<strong>না</strong>' ) . '</li>';
	}
	echo '<li>Stored content-এ &lt;svg&gt; টিকে আছে কিনা: ' . ( $r['stored_has_svg'] ? 'হ্যাঁ' : '<strong>না (KSES filter সন্দেহজনক)</strong>' ) . '</li>';
	echo '<li>Stored content-এর প্রথম অংশ (প্লেইন টেক্সট): <code>' . esc_html( $r['stored_snippet'] ) . '</code></li>';
	echo '</ul>';
	if ( 'notice-error' === $class ) {
		echo '<p>এই স্ক্রিনশটটা Claude-কে দিন — এখান থেকেই আসল কারণ বোঝা যাবে।</p>';
	}
	echo '</div>';
}
add_action( 'admin_notices', 'womensfight_refresh_done_notice' );
