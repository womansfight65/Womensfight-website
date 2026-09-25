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

/**
 * This theme's pages hold complete, pre-built HTML (nested divs, anchors
 * wrapping headings, etc — see inc/content/*.php), not blog-style prose.
 * WordPress's default wpautop filter inserts <p>/<br> at blank lines and
 * breaks that nested markup apart (e.g. splitting a .svc-card link into
 * two separate boxes). Skip wpautop for pages only, so normal blog posts
 * (if any are ever added) keep the usual auto-paragraph behavior.
 */
function womensfight_skip_wpautop_on_pages( $content ) {
	if ( 'page' === get_post_type() ) {
		return $content;
	}
	return wpautop( $content );
}
remove_filter( 'the_content', 'wpautop' );
add_filter( 'the_content', 'womensfight_skip_wpautop_on_pages', 10 );

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

	$screen        = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$page_sync_url = admin_url( 'themes.php?page=womensfight-page-sync' );
	if ( $screen && 'themes' === $screen->id ) {
		echo '<div class="notice notice-info"><p><strong>Women\'s Fight থিম:</strong> কোনো একটা পেজ (যেমন হোম) ভাঙা বা ফাঁকা দেখালে ';
		echo '<a href="' . esc_url( $page_sync_url ) . '">Page Sync পেজে গিয়ে শুধু ওই পেজটাই আলাদাভাবে সিঙ্ক করুন</a> — বাকি পেজ অক্ষত থাকবে। অথবা ';
		echo '<a href="' . esc_url( $url ) . '">সবগুলো একসাথে সিঙ্ক করতে এখানে ক্লিক করুন</a> — এটাও নিরাপদ, বিদ্যমান কনটেন্ট মুছবে না।</p></div>';
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

/* ---------------------------------------------------------------------
 * Per-page sync — resets a single page back to its theme default
 * content, without touching any other page. Unlike
 * womensfight_install_pages_and_menu() (which only fills pages that are
 * still empty), this always overwrites the target page's content,
 * because "sync this page" is an explicit request to reset a page that
 * currently looks broken or wrong.
 * ------------------------------------------------------------------- */
function womensfight_sync_one_page( $slug ) {
	$definitions = womensfight_page_definitions();
	if ( ! isset( $definitions[ $slug ] ) ) {
		return false;
	}

	$content_file = get_template_directory() . '/inc/content/' . $slug . '.php';
	$content      = file_exists( $content_file ) ? include $content_file : '';
	if ( '' === $content ) {
		return false;
	}

	// Resolve ##LINK:slug## tokens against every other page that already
	// exists, so this page's internal links keep working even though only
	// this one page is being resynced.
	$slug_to_id = array();
	foreach ( $definitions as $s => $t ) {
		$p = get_page_by_path( $s );
		if ( $p ) {
			$slug_to_id[ $s ] = $p->ID;
		}
	}

	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		$id = $existing->ID;
	} else {
		$id = wp_insert_post(
			array(
				'post_title'  => $definitions[ $slug ],
				'post_name'   => $slug,
				'post_status' => 'publish',
				'post_type'   => 'page',
			)
		);
		if ( ! $id || is_wp_error( $id ) ) {
			return false;
		}
	}
	$slug_to_id[ $slug ] = $id;

	$logo_icon   = esc_url( get_template_directory_uri() . '/assets/img/logo-icon.png' );
	$new_content = preg_replace_callback(
		'/##LINK:([a-z0-9-]+)##/',
		function ( $m ) use ( $slug_to_id ) {
			return isset( $slug_to_id[ $m[1] ] ) ? esc_url( get_permalink( $slug_to_id[ $m[1] ] ) ) : '#';
		},
		$content
	);
	$new_content = str_replace( '##LOGO_ICON##', $logo_icon, $new_content );

	wp_update_post(
		array(
			'ID'           => $id,
			'post_title'   => $definitions[ $slug ],
			'post_content' => $new_content,
			'post_status'  => 'publish',
		)
	);

	// Elementor data keeps its usual safety check (only filled if empty),
	// so real Elementor work on this page is never wiped by a sync.
	womensfight_save_elementor_data( $id, $new_content );

	if ( 'home' === $slug ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $id );
	}

	return $id;
}

function womensfight_maybe_run_single_page_sync() {
	if ( ! isset( $_GET['womensfight_sync_slug'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$slug = sanitize_title( wp_unslash( $_GET['womensfight_sync_slug'] ) );
	check_admin_referer( 'womensfight_sync_' . $slug );
	womensfight_sync_one_page( $slug );
	wp_safe_redirect( admin_url( 'themes.php?page=womensfight-page-sync&womensfight_synced=' . $slug ) );
	exit;
}
add_action( 'admin_init', 'womensfight_maybe_run_single_page_sync' );

function womensfight_register_page_sync_screen() {
	add_theme_page(
		'Women\'s Fight — পেজ সিঙ্ক',
		'Page Sync',
		'manage_options',
		'womensfight-page-sync',
		'womensfight_render_page_sync_screen'
	);
}
add_action( 'admin_menu', 'womensfight_register_page_sync_screen' );

function womensfight_render_page_sync_screen() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( ! empty( $_GET['womensfight_synced'] ) ) {
		$synced_slug  = sanitize_title( wp_unslash( $_GET['womensfight_synced'] ) );
		$definitions  = womensfight_page_definitions();
		$synced_title = isset( $definitions[ $synced_slug ] ) ? $definitions[ $synced_slug ] : $synced_slug;
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $synced_title ) . ' পেজ সফলভাবে সিঙ্ক হয়েছে।</p></div>';
	}

	echo '<div class="wrap">';
	echo '<h1>Women\'s Fight — পেজ সিঙ্ক</h1>';

	$theme        = wp_get_theme();
	$deploy_files = array(
		'functions.php' => get_template_directory() . '/functions.php',
		'style.css'     => get_template_directory() . '/style.css',
		'contact.php'   => get_template_directory() . '/inc/content/contact.php',
	);
	$commit_hash  = '';
	$head_file    = get_template_directory() . '/.git/HEAD';
	if ( is_readable( $head_file ) ) {
		$head = trim( (string) file_get_contents( $head_file ) );
		if ( 0 === strpos( $head, 'ref: ' ) ) {
			$ref_file = get_template_directory() . '/.git/' . trim( substr( $head, 5 ) );
			if ( is_readable( $ref_file ) ) {
				$commit_hash = substr( trim( (string) file_get_contents( $ref_file ) ), 0, 7 );
			}
		} elseif ( $head ) {
			$commit_hash = substr( $head, 0, 7 );
		}
	}

	echo '<div class="notice notice-info" style="padding:12px 16px;"><p style="margin:.3em 0;"><strong>এই সার্ভারে এখন যে কোড চলছে:</strong></p>';
	echo '<p style="margin:.3em 0;">Theme Version: <code>' . esc_html( $theme->get( 'Version' ) ) . '</code>';
	if ( $commit_hash ) {
		echo ' &middot; Git commit: <code>' . esc_html( $commit_hash ) . '</code>';
	}
	echo '</p>';
	foreach ( $deploy_files as $label => $path ) {
		if ( file_exists( $path ) ) {
			echo '<p style="margin:.3em 0;">' . esc_html( $label ) . ' সর্বশেষ আপডেট হয়েছে: <code>' . esc_html( date_i18n( 'Y-m-d H:i:s', filemtime( $path ) ) ) . '</code></p>';
		}
	}
	echo '<p style="margin:.3em 0; color:var(--ink-faint,#787490);">GitHub-এ push করা নতুন commit-এর timestamp/hash-এর সাথে উপরের তথ্য মিলিয়ে দেখুন — মিললে বুঝবেন সর্বশেষ push এই সার্ভারে পৌঁছেছে।</p>';
	echo '</div>';

	echo '<p>নিচের যেকোনো একটা পেজের পাশে <strong>এই পেজ সিঙ্ক করুন</strong> চাপলে শুধু <em>সেই একটা পেজই</em> থিমের ডিফল্ট ডিজাইন দিয়ে রিসেট হবে — বাকি সব পেজ অপরিবর্তিত থাকবে। কোনো একটা পেজ ভাঙা বা ফাঁকা দেখালে এটা ব্যবহার করুন।</p>';

	echo '<table class="widefat striped" style="max-width:900px;"><thead><tr><th>পেজ</th><th>স্ট্যাটাস</th><th>অ্যাকশন</th></tr></thead><tbody>';

	foreach ( womensfight_page_definitions() as $slug => $title ) {
		$existing = get_page_by_path( $slug );

		if ( $existing ) {
			$status = 'publish' === $existing->post_status ? 'প্রকাশিত' : 'অপ্রকাশিত';
			if ( '' === trim( wp_strip_all_tags( $existing->post_content ) ) ) {
				$status .= ' — ফাঁকা';
			}
			$links = ' &middot; <a href="' . esc_url( get_permalink( $existing->ID ) ) . '" target="_blank" rel="noopener">দেখুন</a>' .
				' &middot; <a href="' . esc_url( get_edit_post_link( $existing->ID ) ) . '">এডিট</a>';
		} else {
			$status = 'তৈরি হয়নি';
			$links  = '';
		}

		$sync_url = wp_nonce_url(
			admin_url( 'themes.php?page=womensfight-page-sync&womensfight_sync_slug=' . $slug ),
			'womensfight_sync_' . $slug
		);

		echo '<tr>';
		echo '<td><strong>' . esc_html( $title ) . '</strong><br><code>/' . esc_html( $slug ) . '/</code></td>';
		echo '<td>' . esc_html( $status ) . $links . '</td>';
		echo '<td><a href="' . esc_url( $sync_url ) . '" class="button button-secondary" onclick="return confirm(&#039;এই পেজটা থিমের মূল ডিজাইন দিয়ে রিসেট হবে, বর্তমান কনটেন্ট মুছে যাবে। এগিয়ে যাবেন?&#039;);">এই পেজ সিঙ্ক করুন</a></td>';
		echo '</tr>';
	}

	echo '</tbody></table>';

	$sync_all_url = wp_nonce_url( admin_url( 'themes.php?womensfight_run_setup=1' ), 'womensfight_run_setup' );
	echo '<p style="margin-top:24px;"><a href="' . esc_url( $sync_all_url ) . '" class="button button-primary" onclick="return confirm(&#039;সব পেজ + মেনু একসাথে সিঙ্ক হবে (ফাঁকা পেজগুলো ভরা হবে)। এগিয়ে যাবেন?&#039;);">সব পেজ + মেনু একসাথে সিঙ্ক করুন</a></p>';
	echo '</div>';
}
