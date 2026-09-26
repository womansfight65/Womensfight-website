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
		'womensfight-style',
		get_stylesheet_uri(),
		array(),
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
 * Google Fonts, loaded non-render-blocking. wp_enqueue_style() always
 * prints a plain blocking <link rel="stylesheet">, which is what was
 * costing ~3.4s of render-blocking time in PageSpeed — this is the
 * standard preload-as-style trick (media="print" until onload swaps it
 * to "all"), with preconnect hints and a <noscript> fallback.
 */
function womensfight_google_fonts_preload() {
	$href = 'https://fonts.googleapis.com/css2?family=Noto+Serif+Bengali:wght@500;600;700&family=Hind+Siliguri:wght@400;500;600;700;800&display=swap';
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	echo '<link rel="preload" as="style" href="' . esc_url( $href ) . '">' . "\n";
	echo '<link rel="stylesheet" href="' . esc_url( $href ) . '" media="print" onload="this.media=&#039;all&#039;;this.onload=null;">' . "\n";
	echo '<noscript><link rel="stylesheet" href="' . esc_url( $href ) . '"></noscript>' . "\n";
}
add_action( 'wp_head', 'womensfight_google_fonts_preload', 1 );

/**
 * Meta (Facebook) Pixel — loaded in <head> on every page, tracking the
 * standard PageView event. Pixel ID: 1458605546179763.
 */
function womensfight_meta_pixel() {
	?>
<!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '1458605546179763');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=1458605546179763&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->
	<?php
}
add_action( 'wp_head', 'womensfight_meta_pixel' );

/**
 * Google Analytics 4 (gtag.js) — loaded in <head> on every page.
 * Measurement ID: G-ETB0CP1VVV.
 */
function womensfight_google_analytics() {
	?>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-ETB0CP1VVV"></script>
<script>
window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());
gtag('config', 'G-ETB0CP1VVV');
</script>
<!-- End Google tag (gtag.js) -->
	<?php
}
add_action( 'wp_head', 'womensfight_google_analytics' );

/**
 * Look up a page's URL by its slug, safely. Returns '#' if the page
 * doesn't exist yet (e.g. setup hasn't run).
 */
function womensfight_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : '#';
}

/**
 * URL for a file in assets/img, with a cache-busting ?v= query string
 * based on the file's last-modified time. Without this, browsers and
 * hosting-level caches (e.g. Hostinger/LiteSpeed) can keep serving an
 * old cached copy of an image after its content changes on disk, since
 * the filename itself stays the same from one push to the next.
 */
function womensfight_asset_url( $filename ) {
	$path = get_template_directory() . '/assets/img/' . $filename;
	$url  = get_template_directory_uri() . '/assets/img/' . $filename;
	if ( file_exists( $path ) ) {
		$url .= '?v=' . filemtime( $path );
	}
	return $url;
}

/**
 * srcset string for the hero photo — 400px/700px versions for mobile
 * and tablet, the full 1103px original for desktop, so phones don't
 * download the same large file the desktop layout needs.
 */
function womensfight_hero_srcset() {
	return womensfight_asset_url( 'hero-team-400.webp' ) . ' 400w, ' .
		womensfight_asset_url( 'hero-team-700.webp' ) . ' 700w, ' .
		womensfight_asset_url( 'hero-team.webp' ) . ' 1103w';
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
 * Elementor data, mirroring what Elementor itself writes on save.
 *
 * By default this only runs for pages that have no Elementor data yet,
 * so it never overwrites a page the user has since rebuilt or rearranged
 * inside Elementor. Pass $force = true to reset it anyway — used by the
 * explicit "sync this page" action, since once Elementor is active it
 * renders _elementor_data instead of post_content on the front end, so a
 * content update that only touches post_content would otherwise never
 * actually appear on the live page.
 */
function womensfight_save_elementor_data( $post_id, $html, $force = false ) {
	if ( ! $force && get_post_meta( $post_id, '_elementor_data', true ) ) {
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
		'product'              => 'Product',
		'ai-agent'             => 'AI Agent',
		'contact'              => 'Contact Us',
		'lead-form'            => 'Lead Form',
		'project'              => 'Client Dashboard',
		'demo-library'         => 'Demo Website Library',
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
	$hero_img   = esc_url( womensfight_asset_url( 'hero-team.webp' ) );

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
		$new_content = str_replace( '##HERO_IMG##', $hero_img, $new_content );
		$new_content = str_replace( '##HERO_SRCSET##', womensfight_hero_srcset(), $new_content );

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
	$hero_img    = esc_url( womensfight_asset_url( 'hero-team.webp' ) );
	$new_content = preg_replace_callback(
		'/##LINK:([a-z0-9-]+)##/',
		function ( $m ) use ( $slug_to_id ) {
			return isset( $slug_to_id[ $m[1] ] ) ? esc_url( get_permalink( $slug_to_id[ $m[1] ] ) ) : '#';
		},
		$content
	);
	$new_content = str_replace( '##LOGO_ICON##', $logo_icon, $new_content );
	$new_content = str_replace( '##HERO_IMG##', $hero_img, $new_content );
	$new_content = str_replace( '##HERO_SRCSET##', womensfight_hero_srcset(), $new_content );

	wp_update_post(
		array(
			'ID'           => $id,
			'post_title'   => $definitions[ $slug ],
			'post_content' => $new_content,
			'post_status'  => 'publish',
		)
	);

	// Force-reset Elementor data too: "sync this page" is an explicit
	// request to reset this one page, and Elementor renders _elementor_data
	// (not post_content) on the front end once it's active, so without
	// this the content update above would silently never appear live.
	womensfight_save_elementor_data( $id, $new_content, true );

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

/* =======================================================================
 * Customer Information Form — a simple, all-fields-optional lead form.
 * Submissions are stored as a private "wf_lead" post type (visible +
 * CSV-exportable from wp-admin), and successful submission fires
 * GTM dataLayer / Meta Pixel / GA4 lead events exactly once, on the
 * confirmation page load — never on a mere button click.
 * ===================================================================== */

/**
 * wf_lead is legacy — Lead Form submissions now go straight into the
 * Projects pipeline (see womensfight_handle_customer_form_submit()).
 * Kept registered (show_ui/show_in_menu off) purely so old historical
 * entries stay valid in the database instead of becoming orphaned;
 * nothing writes to it anymore and it no longer shows in wp-admin.
 */
function womensfight_register_lead_cpt() {
	register_post_type(
		'wf_lead',
		array(
			'labels'          => array(
				'name'          => 'Customer Submissions',
				'singular_name' => 'Customer Submission',
				'menu_name'     => 'Customer Submissions',
				'all_items'     => 'All Submissions',
			),
			'public'          => false,
			'show_ui'         => false,
			'show_in_menu'    => false,
			'menu_icon'       => 'dashicons-clipboard',
			'menu_position'   => 26,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'womensfight_register_lead_cpt' );

/**
 * The fields collected by the form, in order: meta_key => display label.
 * Shared by the form markup, the submit handler, the admin list columns
 * and the CSV export, so every place stays in sync automatically.
 */
function womensfight_lead_fields() {
	return array(
		'wf_name'               => 'নাম',
		'wf_mobile'             => 'Mobile Number',
		'wf_whatsapp'           => 'WhatsApp Number',
		'wf_email'              => 'Email',
		'wf_business'           => 'Business Name',
		'wf_service'            => 'Service',
		'wf_budget'             => 'Budget',
		'wf_location'           => 'Business Location',
		'wf_fb_link'            => 'Facebook Page / Website Link',
		'wf_message'            => 'Message / Requirement',
		// Facebook Ads only
		'wf_ad_post_link'       => 'Ads Post Link',
		'wf_ad_budget'          => 'Ad Budget',
		'wf_target_location'    => 'Target Location',
		'wf_campaign_objective' => 'Campaign Objective',
		// Website Development only
		'wf_ref_website'        => 'Reference Website',
		'wf_pages_needed'       => 'Number of Pages',
		'wf_domain_status'      => 'Domain/Hosting Status',
		// Graphic Design only
		'wf_design_type'        => 'Design Type',
		'wf_brand_guideline'    => 'Brand Guideline Available',
		'wf_deliverable_count'  => 'Number of Designs Needed',
		// Video Editing only
		'wf_footage_link'       => 'Raw Footage Link',
		'wf_video_duration'     => 'Video Duration',
		'wf_style_reference'    => 'Style Reference Link',
	);
}

/**
 * Which of the fields above belong to which service — drives both the
 * show/hide JS on the form and which fields the admin detail box
 * bothers to print (skipping the ones irrelevant to that project's
 * service instead of showing a wall of blank rows).
 */
function womensfight_service_specific_fields() {
	return array(
		'Facebook Ads'         => array( 'wf_ad_post_link', 'wf_ad_budget', 'wf_target_location', 'wf_campaign_objective' ),
		'Website Development'  => array( 'wf_ref_website', 'wf_pages_needed', 'wf_domain_status' ),
		'Graphic Design'       => array( 'wf_design_type', 'wf_brand_guideline', 'wf_deliverable_count' ),
		'Video Editing'        => array( 'wf_footage_link', 'wf_video_duration', 'wf_style_reference' ),
	);
}

function womensfight_lead_services() {
	return array(
		'Facebook Ads',
		'Digital Marketing',
		'Website Development',
		'Google Ads',
		'Graphic Design',
		'Video Editing',
		'Branding',
		'Other',
	);
}

/**
 * [wf_customer_form] — renders the form, or, right after a successful
 * submit (detected via the ?wf_submitted=1 redirect flag), the thank-you
 * message plus the one-time tracking snippet.
 */
function womensfight_render_customer_form() {
	ob_start();

	if ( isset( $_GET['wf_submitted'] ) && '1' === $_GET['wf_submitted'] ) {
		?>
		<div class="wf-cform-success" id="wf-cform">
			<p>ধন্যবাদ। আপনার তথ্য আমরা পেয়েছি। আমাদের টিম খুব দ্রুত আপনার সাথে যোগাযোগ করবে।</p>
		</div>
		<script>
		(function(){
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push({ event: 'lead_submit' });
			if ( typeof fbq === 'function' ) { fbq('track', 'Lead'); }
			if ( typeof gtag === 'function' ) { gtag('event', 'generate_lead'); }
		})();
		</script>
		<?php
		return ob_get_clean();
	}

	$services = womensfight_lead_services();
	?>
	<form class="wf-cform" id="wf-cform" method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<input type="hidden" name="action" value="womensfight_submit_customer_form">
		<input type="hidden" name="wf_redirect" value="<?php echo esc_url( get_permalink() ); ?>">
		<?php wp_nonce_field( 'womensfight_customer_form', 'womensfight_customer_form_nonce' ); ?>

		<div class="frow">
			<div><label for="wf_name">নাম</label><input type="text" id="wf_name" name="wf_name" placeholder="আপনার নাম"></div>
			<div><label for="wf_mobile">Mobile Number</label><input type="tel" id="wf_mobile" name="wf_mobile" placeholder="01XXXXXXXXX"></div>
		</div>
		<div class="frow">
			<div><label for="wf_whatsapp">WhatsApp Number</label><input type="tel" id="wf_whatsapp" name="wf_whatsapp" placeholder="01XXXXXXXXX"></div>
			<div><label for="wf_email">Email</label><input type="email" id="wf_email" name="wf_email" placeholder="you@example.com"></div>
		</div>
		<div class="frow">
			<div><label for="wf_business">Business Name</label><input type="text" id="wf_business" name="wf_business" placeholder="আপনার ব্যবসার নাম"></div>
			<div>
				<label for="wf_service">কোন Service নিতে চান</label>
				<select id="wf_service" name="wf_service" onchange="wfToggleServiceFields(this.value);">
					<option value="">সিলেক্ট করুন</option>
					<?php foreach ( $services as $service ) : ?>
						<option value="<?php echo esc_attr( $service ); ?>"><?php echo esc_html( $service ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div id="wf_service_other_wrap" style="display:none;">
			<label for="wf_service_other">Service-টা কী লিখে দিন</label>
			<input type="text" id="wf_service_other" name="wf_service_other" placeholder="আপনার প্রয়োজনীয় Service লিখুন">
		</div>
		<div class="frow">
			<div><label for="wf_budget">Budget</label><input type="text" id="wf_budget" name="wf_budget" placeholder="যেমন: ১০,০০০ - ২০,০০০ টাকা"></div>
			<div><label for="wf_location">Business Location</label><input type="text" id="wf_location" name="wf_location" placeholder="শহর / এলাকা"></div>
		</div>
		<div><label for="wf_fb_link">Facebook Page / Website Link</label><input type="url" id="wf_fb_link" name="wf_fb_link" placeholder="https://facebook.com/..."></div>

		<div id="wf_svc_Facebook Ads" class="wf-svc-fields" style="display:none;">
			<div class="frow">
				<div><label for="wf_ad_post_link">যে পোস্টে Ads চালাবেন তার Link</label><input type="url" id="wf_ad_post_link" name="wf_ad_post_link" placeholder="https://facebook.com/.../posts/..."></div>
				<div><label for="wf_ad_budget">Ad Budget</label><input type="text" id="wf_ad_budget" name="wf_ad_budget" placeholder="যেমন: ৫,০০০ টাকা/মাস"></div>
			</div>
			<div class="frow">
				<div><label for="wf_target_location">Target Location</label><input type="text" id="wf_target_location" name="wf_target_location" placeholder="কোন এলাকার অডিয়েন্স টার্গেট করবেন"></div>
				<div>
					<label for="wf_campaign_objective">Campaign Objective</label>
					<select id="wf_campaign_objective" name="wf_campaign_objective">
						<option value="">সিলেক্ট করুন</option>
						<option>Awareness</option>
						<option>Traffic</option>
						<option>Leads</option>
						<option>Messages</option>
						<option>Sales</option>
					</select>
				</div>
			</div>
		</div>

		<div id="wf_svc_Website Development" class="wf-svc-fields" style="display:none;">
			<div><label for="wf_ref_website">Reference Website (যদি থাকে)</label><input type="url" id="wf_ref_website" name="wf_ref_website" placeholder="https://example.com"></div>
			<div class="frow">
				<div><label for="wf_pages_needed">কতগুলো Page লাগবে</label><input type="text" id="wf_pages_needed" name="wf_pages_needed" placeholder="যেমন: ৫টি পেজ"></div>
				<div>
					<label for="wf_domain_status">Domain/Hosting আছে কিনা</label>
					<select id="wf_domain_status" name="wf_domain_status">
						<option value="">সিলেক্ট করুন</option>
						<option>হ্যাঁ, আছে</option>
						<option>নেই, লাগবে</option>
					</select>
				</div>
			</div>
		</div>

		<div id="wf_svc_Graphic Design" class="wf-svc-fields" style="display:none;">
			<div class="frow">
				<div>
					<label for="wf_design_type">কী ধরনের Design</label>
					<select id="wf_design_type" name="wf_design_type">
						<option value="">সিলেক্ট করুন</option>
						<option>লোগো</option>
						<option>ব্যানার</option>
						<option>সোশ্যাল মিডিয়া পোস্ট</option>
						<option>ব্রোশিওর</option>
						<option>অন্যান্য</option>
					</select>
				</div>
				<div><label for="wf_deliverable_count">কতগুলো Design লাগবে</label><input type="text" id="wf_deliverable_count" name="wf_deliverable_count" placeholder="যেমন: ১০টি"></div>
			</div>
			<div>
				<label for="wf_brand_guideline">Brand Guideline আছে কিনা</label>
				<select id="wf_brand_guideline" name="wf_brand_guideline">
					<option value="">সিলেক্ট করুন</option>
					<option>হ্যাঁ, আছে</option>
					<option>নেই</option>
				</select>
			</div>
		</div>

		<div id="wf_svc_Video Editing" class="wf-svc-fields" style="display:none;">
			<div><label for="wf_footage_link">Raw Footage Link (যদি থাকে)</label><input type="url" id="wf_footage_link" name="wf_footage_link" placeholder="Google Drive / Dropbox লিংক"></div>
			<div class="frow">
				<div><label for="wf_video_duration">ভিডিও কত মিনিটের</label><input type="text" id="wf_video_duration" name="wf_video_duration" placeholder="যেমন: ৩ মিনিট"></div>
				<div><label for="wf_style_reference">Style Reference Link</label><input type="url" id="wf_style_reference" name="wf_style_reference" placeholder="উদাহরণস্বরূপ কোনো ভিডিও লিংক"></div>
			</div>
		</div>

		<div><label for="wf_message">Message / Requirement</label><textarea id="wf_message" name="wf_message" rows="4" placeholder="আপনার প্রয়োজন সম্পর্কে লিখুন (ঐচ্ছিক)"></textarea></div>
		<div><label for="wf_image">ছবি যুক্ত করুন (ঐচ্ছিক)</label><input type="file" id="wf_image" name="wf_image" accept="image/*"></div>

		<button class="btn btn-primary" type="submit">Submit করুন</button>
	</form>
	<script>
	function wfToggleServiceFields(selected) {
		document.getElementById('wf_service_other_wrap').style.display = (selected === 'Other') ? 'block' : 'none';
		document.querySelectorAll('.wf-svc-fields').forEach(function(el){ el.style.display = 'none'; });
		var target = document.getElementById('wf_svc_' + selected);
		if (target) { target.style.display = 'block'; }
	}
	</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'wf_customer_form', 'womensfight_render_customer_form' );

/**
 * Handles the form POST: every field is optional (sanitized, never
 * required), saved as a wf_project post (status "lead") + postmeta,
 * then redirects back to the same page with ?wf_submitted=1 so the
 * shortcode above can show the thank-you message and fire the
 * tracking events exactly once.
 */
function womensfight_handle_customer_form_submit() {
	if (
		! isset( $_POST['womensfight_customer_form_nonce'] ) ||
		! wp_verify_nonce( wp_unslash( $_POST['womensfight_customer_form_nonce'] ), 'womensfight_customer_form' )
	) {
		wp_die( 'Security check failed. দয়া করে পেজ রিফ্রেশ করে আবার চেষ্টা করুন।' );
	}

	$data = array();
	foreach ( womensfight_lead_fields() as $key => $label ) {
		$data[ $key ] = isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '';
	}

	// If "Other" was picked, use the customer's own typed-in service name.
	if ( 'Other' === $data['wf_service'] && ! empty( $_POST['wf_service_other'] ) ) {
		$data['wf_service'] = sanitize_text_field( wp_unslash( $_POST['wf_service_other'] ) );
	}

	if ( '' !== $data['wf_name'] ) {
		$title = $data['wf_name'];
	} elseif ( '' !== $data['wf_mobile'] ) {
		$title = $data['wf_mobile'];
	} elseif ( '' !== $data['wf_email'] ) {
		$title = $data['wf_email'];
	} else {
		$title = 'Submission — ' . current_time( 'Y-m-d H:i' );
	}

	// Lead Form submissions now seed the Projects pipeline directly (as
	// "wf_p_*" meta on a wf_project post, status "lead") instead of the
	// older standalone wf_lead post type — see womensfight_project_statuses().
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'wf_project',
			'post_title'  => $title,
			'post_status' => 'publish',
		)
	);

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		foreach ( $data as $key => $value ) {
			update_post_meta( $post_id, str_replace( 'wf_', 'wf_p_', $key ), $value );
		}
		update_post_meta( $post_id, 'wf_p_status', 'lead' );
		update_post_meta( $post_id, 'wf_p_payment_status', 'unpaid' );

		if ( ! empty( $_FILES['wf_image']['name'] ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			$attachment_id = media_handle_upload( 'wf_image', $post_id );
			if ( ! is_wp_error( $attachment_id ) ) {
				update_post_meta( $post_id, 'wf_p_image_id', $attachment_id );
			}
		}
	}

	$redirect = isset( $_POST['wf_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['wf_redirect'] ) ) : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'wf_submitted', '1', $redirect ) . '#wf-cform' );
	exit;
}
add_action( 'admin_post_womensfight_submit_customer_form', 'womensfight_handle_customer_form_submit' );
add_action( 'admin_post_nopriv_womensfight_submit_customer_form', 'womensfight_handle_customer_form_submit' );

/**
 * wp-admin list table columns for Customer Submissions, so the important
 * fields are visible at a glance without opening each entry.
 */
function womensfight_lead_columns( $columns ) {
	$new = array(
		'cb'          => $columns['cb'],
		'title'       => 'নাম / টাইটেল',
		'wf_mobile'   => 'Mobile',
		'wf_whatsapp' => 'WhatsApp',
		'wf_email'    => 'Email',
		'wf_business' => 'Business',
		'wf_service'  => 'Service',
		'wf_image'    => 'ছবি',
		'date'        => $columns['date'],
	);
	return $new;
}
add_filter( 'manage_wf_lead_posts_columns', 'womensfight_lead_columns' );

function womensfight_lead_column_content( $column, $post_id ) {
	if ( in_array( $column, array( 'wf_mobile', 'wf_whatsapp', 'wf_email', 'wf_business', 'wf_service' ), true ) ) {
		echo esc_html( get_post_meta( $post_id, $column, true ) );
		return;
	}
	if ( 'wf_image' === $column ) {
		$image_id = get_post_meta( $post_id, 'wf_image_id', true );
		if ( $image_id ) {
			echo wp_get_attachment_image( $image_id, array( 50, 50 ) );
		} else {
			echo '—';
		}
	}
}
add_action( 'manage_wf_lead_posts_custom_column', 'womensfight_lead_column_content', 10, 2 );

/**
 * A read-only "Submission Details" box on each Customer Submission's edit
 * screen, showing every field (plus the uploaded image, if any) in one
 * place — since the post type has no content editor, this is otherwise
 * only visible one field at a time via the list-table columns or CSV.
 */
function womensfight_register_lead_detail_box() {
	add_meta_box(
		'womensfight_lead_details',
		'Submission Details',
		'womensfight_render_lead_detail_box',
		'wf_lead',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_wf_lead', 'womensfight_register_lead_detail_box' );

function womensfight_render_lead_detail_box( $post ) {
	echo '<table class="widefat striped"><tbody>';
	foreach ( womensfight_lead_fields() as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th style="width:220px;">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
	}

	$image_id = get_post_meta( $post->ID, 'wf_image_id', true );
	echo '<tr><th>ছবি</th><td>';
	if ( $image_id ) {
		echo wp_get_attachment_image( $image_id, 'medium' );
		echo '<br><a href="' . esc_url( wp_get_attachment_url( $image_id ) ) . '" target="_blank" rel="noopener">পূর্ণ সাইজে দেখুন</a>';
	} else {
		echo '<em>কোনো ছবি দেওয়া হয়নি</em>';
	}
	echo '</td></tr>';
	echo '</tbody></table>';
}

/**
 * CSV export — a button on the Customer Submissions list screen that
 * downloads every submission as a spreadsheet-ready CSV file.
 */
add_action( 'admin_notices', function() {
	$screen = get_current_screen();
	if ( $screen && 'edit-wf_lead' === $screen->id ) {
		$url = wp_nonce_url( admin_url( 'edit.php?post_type=wf_lead&wf_export_csv=1' ), 'womensfight_export_leads' );
		echo '<p><a href="' . esc_url( $url ) . '" class="button button-primary">সব সাবমিশন CSV হিসেবে Export করুন</a></p>';
	}
} );

function womensfight_maybe_export_leads_csv() {
	if ( ! isset( $_GET['wf_export_csv'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! isset( $_GET['post_type'] ) || 'wf_lead' !== $_GET['post_type'] ) {
		return;
	}
	check_admin_referer( 'womensfight_export_leads' );

	$posts = get_posts(
		array(
			'post_type'      => 'wf_lead',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=customer-submissions-' . gmdate( 'Y-m-d' ) . '.csv' );

	$out = fopen( 'php://output', 'w' );
	fputs( $out, "\xEF\xBB\xBF" ); // UTF-8 BOM so Bangla text opens correctly in Excel.

	$fields = womensfight_lead_fields();
	fputcsv( $out, array_merge( array( 'Submitted At' ), array_values( $fields ), array( 'Image URL' ) ) );

	foreach ( $posts as $post ) {
		$row = array( get_the_date( 'Y-m-d H:i', $post ) );
		foreach ( array_keys( $fields ) as $key ) {
			$row[] = get_post_meta( $post->ID, $key, true );
		}
		$image_id = get_post_meta( $post->ID, 'wf_image_id', true );
		$row[]    = $image_id ? wp_get_attachment_url( $image_id ) : '';
		fputcsv( $out, $row );
	}

	fclose( $out );
	exit;
}
add_action( 'admin_init', 'womensfight_maybe_export_leads_csv' );

/* =======================================================================
 * Contact Page Form — separate from the Lead Form / Customer
 * Submissions above on purpose: same "always fresh, never baked into
 * static content" architecture, but its own fields, its own post type
 * (wf_contact_msg) and its own wp-admin screen, so the two never mix.
 * ===================================================================== */

function womensfight_register_contact_cpt() {
	register_post_type(
		'wf_contact_msg',
		array(
			'labels'          => array(
				'name'          => 'Contact Messages',
				'singular_name' => 'Contact Message',
				'menu_name'     => 'Contact Messages',
				'all_items'     => 'All Messages',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-email-alt',
			'menu_position'   => 27,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'womensfight_register_contact_cpt' );

function womensfight_contact_fields() {
	return array(
		'cf_name'    => 'নাম',
		'cf_company' => 'কোম্পানির নাম',
		'cf_service' => 'কোন সার্ভিস প্রয়োজন',
		'cf_mobile'  => 'মোবাইল নম্বর',
		'cf_email'   => 'ইমেইল',
		'cf_message' => 'প্রজেক্ট সম্পর্কে',
	);
}

function womensfight_contact_services() {
	return array(
		'ডিজিটাল মার্কেটিং',
		'ওয়েবসাইট ডেভেলপমেন্ট',
		'ল্যান্ডিং পেজ',
		'ভিডিও প্রোডাকশন',
		'AI Agency',
		'AI Agent (অডিয়েন্স ক্যালকুলেটর)',
		'অন্যান্য',
	);
}

function womensfight_render_contact_form() {
	ob_start();

	if ( isset( $_GET['cf_submitted'] ) && '1' === $_GET['cf_submitted'] ) {
		?>
		<div class="wf-cform-success" id="contactForm">
			<p>ধন্যবাদ। আপনার তথ্য আমরা পেয়েছি। আমাদের টিম খুব দ্রুত আপনার সাথে যোগাযোগ করবে।</p>
		</div>
		<script>
		(function(){
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push({ event: 'lead_submit' });
			if ( typeof fbq === 'function' ) { fbq('track', 'Lead'); }
			if ( typeof gtag === 'function' ) { gtag('event', 'generate_lead'); }
		})();
		</script>
		<?php
		return ob_get_clean();
	}

	$services = womensfight_contact_services();
	?>
	<form class="consult-form" id="contactForm" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
		<input type="hidden" name="action" value="womensfight_submit_contact_form">
		<input type="hidden" name="cf_redirect" value="<?php echo esc_url( get_permalink() ); ?>">
		<?php wp_nonce_field( 'womensfight_contact_form', 'womensfight_contact_form_nonce' ); ?>

		<div class="frow">
			<div><label for="cf_name">নাম</label><input id="cf_name" name="cf_name" placeholder="আপনার পূর্ণ নাম"></div>
			<div><label for="cf_company">কোম্পানির নাম</label><input id="cf_company" name="cf_company" placeholder="আপনার প্রতিষ্ঠানের নাম"></div>
		</div>
		<div class="frow">
			<div>
				<label for="cf_service">কোন সার্ভিস প্রয়োজন?</label>
				<select id="cf_service" name="cf_service">
					<?php foreach ( $services as $service ) : ?>
						<option value="<?php echo esc_attr( $service ); ?>"><?php echo esc_html( $service ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div><label for="cf_mobile">মোবাইল নম্বর</label><input id="cf_mobile" name="cf_mobile" type="tel" placeholder="+880 1XXX-XXXXXX"></div>
		</div>
		<div><label for="cf_email">ইমেইল</label><input id="cf_email" name="cf_email" type="email" placeholder="you@example.com"></div>
		<div><label for="cf_message">আপনার প্রজেক্ট সম্পর্কে বলুন</label><textarea id="cf_message" name="cf_message" placeholder="আপনি কী অর্জন করতে চান?"></textarea></div>

		<button class="btn btn-primary" type="submit">কনসালটেশন রিকোয়েস্ট করুন</button>
	</form>
	<?php
	return ob_get_clean();
}

function womensfight_handle_contact_form_submit() {
	if (
		! isset( $_POST['womensfight_contact_form_nonce'] ) ||
		! wp_verify_nonce( wp_unslash( $_POST['womensfight_contact_form_nonce'] ), 'womensfight_contact_form' )
	) {
		wp_die( 'Security check failed. দয়া করে পেজ রিফ্রেশ করে আবার চেষ্টা করুন।' );
	}

	$data = array();
	foreach ( womensfight_contact_fields() as $key => $label ) {
		$data[ $key ] = isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '';
	}

	if ( '' !== $data['cf_name'] ) {
		$title = $data['cf_name'];
	} elseif ( '' !== $data['cf_mobile'] ) {
		$title = $data['cf_mobile'];
	} elseif ( '' !== $data['cf_email'] ) {
		$title = $data['cf_email'];
	} else {
		$title = 'Message — ' . current_time( 'Y-m-d H:i' );
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'wf_contact_msg',
			'post_title'  => $title,
			'post_status' => 'publish',
		)
	);

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		foreach ( $data as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
	}

	$redirect = isset( $_POST['cf_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['cf_redirect'] ) ) : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'cf_submitted', '1', $redirect ) . '#contactForm' );
	exit;
}
add_action( 'admin_post_womensfight_submit_contact_form', 'womensfight_handle_contact_form_submit' );
add_action( 'admin_post_nopriv_womensfight_submit_contact_form', 'womensfight_handle_contact_form_submit' );

function womensfight_contact_columns( $columns ) {
	return array(
		'cb'         => $columns['cb'],
		'title'      => 'নাম / টাইটেল',
		'cf_mobile'  => 'Mobile',
		'cf_email'   => 'Email',
		'cf_company' => 'Company',
		'cf_service' => 'Service',
		'date'       => $columns['date'],
	);
}
add_filter( 'manage_wf_contact_msg_posts_columns', 'womensfight_contact_columns' );

function womensfight_contact_column_content( $column, $post_id ) {
	if ( in_array( $column, array( 'cf_mobile', 'cf_email', 'cf_company', 'cf_service' ), true ) ) {
		echo esc_html( get_post_meta( $post_id, $column, true ) );
	}
}
add_action( 'manage_wf_contact_msg_posts_custom_column', 'womensfight_contact_column_content', 10, 2 );

function womensfight_register_contact_detail_box() {
	add_meta_box(
		'womensfight_contact_details',
		'Message Details',
		'womensfight_render_contact_detail_box',
		'wf_contact_msg',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_wf_contact_msg', 'womensfight_register_contact_detail_box' );

function womensfight_render_contact_detail_box( $post ) {
	echo '<table class="widefat striped"><tbody>';
	foreach ( womensfight_contact_fields() as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th style="width:220px;">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
	}
	echo '</tbody></table>';
}

add_action( 'admin_notices', function() {
	$screen = get_current_screen();
	if ( $screen && 'edit-wf_contact_msg' === $screen->id ) {
		$url = wp_nonce_url( admin_url( 'edit.php?post_type=wf_contact_msg&wf_export_csv=1' ), 'womensfight_export_contact' );
		echo '<p><a href="' . esc_url( $url ) . '" class="button button-primary">সব মেসেজ CSV হিসেবে Export করুন</a></p>';
	}
} );

function womensfight_maybe_export_contact_csv() {
	if ( ! isset( $_GET['wf_export_csv'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! isset( $_GET['post_type'] ) || 'wf_contact_msg' !== $_GET['post_type'] ) {
		return;
	}
	check_admin_referer( 'womensfight_export_contact' );

	$posts = get_posts(
		array(
			'post_type'      => 'wf_contact_msg',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	nocache_headers();
	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=contact-messages-' . gmdate( 'Y-m-d' ) . '.csv' );

	$out = fopen( 'php://output', 'w' );
	fputs( $out, "\xEF\xBB\xBF" );

	$fields = womensfight_contact_fields();
	fputcsv( $out, array_merge( array( 'Submitted At' ), array_values( $fields ) ) );

	foreach ( $posts as $post ) {
		$row = array( get_the_date( 'Y-m-d H:i', $post ) );
		foreach ( array_keys( $fields ) as $key ) {
			$row[] = get_post_meta( $post->ID, $key, true );
		}
		fputcsv( $out, $row );
	}

	fclose( $out );
	exit;
}
add_action( 'admin_init', 'womensfight_maybe_export_contact_csv' );

/* =======================================================================
 * Local SEO — targets "Ramganj" / "Lakshmipur" agency searches:
 * per-page <title>/meta description and a LocalBusiness schema block.
 * ===================================================================== */

/**
 * Per-page-slug meta descriptions, each naturally including the target
 * local keywords. Falls back to the home page's description.
 */
function womensfight_seo_meta_descriptions() {
	return array(
		'home'                 => "Ramganj, Lakshmipur-ভিত্তিক ক্রিয়েটিভ ও ডিজিটাল মার্কেটিং এজেন্সি Women's Fight — ফেসবুক মার্কেটিং, ওয়েবসাইট ডেভেলপমেন্ট, ব্র্যান্ডিং ও AI সল্যুশন, ফ্রি কনসালটেশনসহ।",
		'digital-marketing'    => 'Ramganj, Lakshmipur-এ সেরা Digital Marketing সার্ভিস — Facebook Ads, পেইড ও অর্গানিক ক্যাম্পেইন Women&rsquo;s Fight-এর সাথে।',
		'website-development'  => 'Ramganj, Lakshmipur থেকে প্রফেশনাল Website Development — দ্রুত, সুন্দর ও ব্যবসার জন্য কার্যকর ওয়েবসাইট।',
		'landing-page'         => 'Ramganj, Lakshmipur-এ হাই-কনভার্সন Landing Page ডিজাইন — ক্যাম্পেইন বা প্রোডাক্ট লঞ্চের জন্য।',
		'video-production'     => 'Ramganj, Lakshmipur থেকে Video Production সার্ভিস — TVC, OVC ও কর্পোরেট ভিডিও, কনসেপ্ট থেকে ফাইনাল কাট।',
		'ai-agency'            => 'Ramganj, Lakshmipur-এর AI Agency — AI-চালিত অটোমেশন, চ্যাটবট ও কনটেন্ট টুল দিয়ে মার্কেটিং দ্রুততর করুন।',
		'case-study'           => "Women's Fight-এর রিয়েল ক্যাম্পেইন Case Study — Ramganj, Lakshmipur ও এর বাইরের ব্র্যান্ডদের সাফল্যের গল্প।",
		'portfolio'            => "Ramganj, Lakshmipur-ভিত্তিক এজেন্সি Women's Fight-এর কাজের Portfolio দেখুন।",
		'action-plan'          => 'আপনার ব্র্যান্ডের জন্য কাস্টম Action Plan — Ramganj, Lakshmipur-এর Women&rsquo;s Fight এজেন্সির সাথে।',
		'package'              => 'Ramganj, Lakshmipur-এ ডিজিটাল মার্কেটিং Package ও প্রাইসিং — আপনার বাজেট অনুযায়ী সল্যুশন।',
		'product'              => "Women's Fight-এর SaaS প্রোডাক্ট — মাসিক সাবস্ক্রিপশনে মার্কেটিং ও অটোমেশন টুল।",
		'ai-agent'             => 'ফ্রি AI Agent দিয়ে আপনার লোকেশন অনুযায়ী অডিয়েন্স হিসাব করুন — Ramganj, Lakshmipur-এর Women&rsquo;s Fight এজেন্সি থেকে।',
		'contact'              => "Women's Fight-এর সাথে যোগাযোগ করুন — City Plaza, Ramganj, Lakshmipur। ফ্রি কনসালটেশন বুক করুন।",
		'lead-form'            => 'Ramganj, Lakshmipur-এর Women&rsquo;s Fight এজেন্সিকে আপনার তথ্য জানান — আমরা দ্রুত যোগাযোগ করব।',
	);
}

function womensfight_meta_description() {
	$descriptions = womensfight_seo_meta_descriptions();
	$slug         = is_page() ? get_post_field( 'post_name', get_queried_object_id() ) : '';
	$description  = isset( $descriptions[ $slug ] ) ? $descriptions[ $slug ] : $descriptions['home'];
	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'womensfight_meta_description', 1 );

/**
 * The Client Dashboard is a private, token-only page — keep it out of
 * search engines entirely.
 */
function womensfight_noindex_private_pages() {
	if ( is_page( 'project' ) ) {
		echo '<meta name="robots" content="noindex,nofollow">' . "\n";
	}
}
add_action( 'wp_head', 'womensfight_noindex_private_pages', 1 );

/**
 * Canonical URL — one clean, definitive URL per page, so search
 * engines don't treat ?query-string variants or trailing-slash quirks
 * as separate duplicate pages.
 */
function womensfight_canonical_url() {
	$url = is_front_page() ? home_url( '/' ) : get_permalink();
	if ( $url ) {
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'womensfight_canonical_url', 1 );

/**
 * Open Graph + Twitter Card tags — without these, sharing a link (or
 * Facebook crawling it for an ad) shows no title/image/description, or
 * a generic broken preview. Reuses the same title/description mapping
 * as the SEO tags above, plus a site-wide fallback image (the full
 * logo) since individual pages don't have their own share image yet.
 */
function womensfight_open_graph_tags() {
	if ( ! is_page() && ! is_front_page() ) {
		return;
	}
	if ( is_page( 'project' ) ) {
		return;
	}

	$title       = is_front_page() ? "Women's Fight — Ramganj, Lakshmipur-এর সেরা ডিজিটাল মার্কেটিং এজেন্সি" : get_the_title( get_queried_object_id() ) . " — Women's Fight";
	$descriptions = womensfight_seo_meta_descriptions();
	$slug        = is_page() ? get_post_field( 'post_name', get_queried_object_id() ) : '';
	$description = isset( $descriptions[ $slug ] ) ? $descriptions[ $slug ] : $descriptions['home'];
	$url         = is_front_page() ? home_url( '/' ) : get_permalink();
	$image       = esc_url( womensfight_asset_url( 'og-share.jpg' ) );

	echo '<meta property="og:type" content="website">' . "\n";
	echo '<meta property="og:site_name" content="Women\'s Fight">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta property="og:description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $url ) . '">' . "\n";
	echo '<meta property="og:image" content="' . $image . '">' . "\n";
	echo '<meta property="og:image:width" content="1200">' . "\n";
	echo '<meta property="og:image:height" content="630">' . "\n";
	echo '<meta property="og:locale" content="bn_BD">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:title" content="' . esc_attr( $title ) . '">' . "\n";
	echo '<meta name="twitter:description" content="' . esc_attr( $description ) . '">' . "\n";
	echo '<meta name="twitter:image" content="' . $image . '">' . "\n";
}
add_action( 'wp_head', 'womensfight_open_graph_tags', 1 );

/**
 * Browser-tab / search-result title, with the target location worked in
 * naturally — separate from the on-page H1 and nav labels, which stay
 * unchanged.
 */
function womensfight_seo_title( $title ) {
	if ( is_front_page() ) {
		return "Women's Fight — Ramganj, Lakshmipur-এর সেরা ডিজিটাল মার্কেটিং এজেন্সি";
	}
	if ( is_page() ) {
		return get_the_title( get_queried_object_id() ) . ' — Ramganj, Lakshmipur | Women&rsquo;s Fight';
	}
	return $title;
}
add_filter( 'pre_get_document_title', 'womensfight_seo_title' );

/**
 * LocalBusiness structured data (JSON-LD) — tells Google directly that
 * this is a local agency based in Ramganj, Lakshmipur, which is the main
 * technical signal for appearing in local map-pack results for that area.
 */
function womensfight_local_business_schema() {
	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'ProfessionalService',
		'name'       => "Women's Fight Agency",
		'image'      => esc_url( get_template_directory_uri() . '/assets/img/logo-full.png' ),
		'url'        => home_url( '/' ),
		'telephone'  => '+8801748133740',
		'email'      => 'Womansfight65@gmail.com',
		'address'    => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'City Plaza, Lift-3',
			'addressLocality' => 'Ramganj',
			'addressRegion'   => 'Lakshmipur',
			'addressCountry'  => 'BD',
		),
		'areaServed' => array( 'Ramganj', 'Lakshmipur', 'Bangladesh' ),
		'priceRange' => '৳৳',
		'sameAs'     => array(
			'https://www.facebook.com/womensfight',
			'https://www.instagram.com/womensfight',
			'https://www.tiktok.com/@womensfight',
			'https://www.youtube.com/@womensfight',
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
add_action( 'wp_head', 'womensfight_local_business_schema' );

/* =======================================================================
 * Projects — STEP 1 PREVIEW ONLY.
 * A new "Project" post type for the Lead → Client → Payment →
 * Onboarding → In Progress → Review → Approved → Completed pipeline
 * discussed with the client. Nothing on the public site links to this
 * yet — it's just the wp-admin foundation, with one demo entry so it's
 * visible immediately without needing a real form submission.
 * ===================================================================== */

function womensfight_register_project_cpt() {
	register_post_type(
		'wf_project',
		array(
			'labels'          => array(
				'name'          => 'Client Projects',
				'singular_name' => 'Client Project',
				'menu_name'     => 'Client Projects',
				'all_items'     => 'All Projects',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-portfolio',
			'menu_position'   => 28,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'womensfight_register_project_cpt' );

function womensfight_project_statuses() {
	return array(
		'lead'        => 'Lead',
		'client'      => 'Client',
		'payment'     => 'Payment',
		'onboarding'  => 'Onboarding',
		'in_progress' => 'In Progress',
		'review'      => 'Review',
		'approved'    => 'Approved',
		'completed'   => 'Completed',
	);
}

function womensfight_project_payment_statuses() {
	return array(
		'unpaid'  => 'Unpaid',
		'partial' => 'Partial',
		'paid'    => 'Paid',
	);
}

/**
 * Every project's private client-dashboard access key. Generated once
 * (on first access) and stored as postmeta, never a WordPress login —
 * the URL itself, built from this token, is the client's only key.
 */
function womensfight_get_project_token( $post_id ) {
	$token = get_post_meta( $post_id, 'wf_p_token', true );
	if ( ! $token ) {
		$token = wp_generate_password( 24, false, false );
		update_post_meta( $post_id, 'wf_p_token', $token );
	}
	return $token;
}

function womensfight_get_project_dashboard_url( $post_id ) {
	return add_query_arg( 'token', womensfight_get_project_token( $post_id ), womensfight_page_url( 'project' ) );
}

/**
 * One demo entry so the new "Projects" screen shows something real
 * right away. Runs once — skipped forever after the first time it finds
 * (or creates) a project titled "Test Project".
 */
function womensfight_seed_demo_project() {
	if ( get_option( 'womensfight_demo_project_seeded' ) ) {
		return;
	}
	$existing = get_page_by_title( 'Test Project', OBJECT, 'wf_project' );
	if ( ! $existing ) {
		$post_id = wp_insert_post(
			array(
				'post_type'   => 'wf_project',
				'post_title'  => 'Test Project',
				'post_status' => 'publish',
			)
		);
		if ( $post_id && ! is_wp_error( $post_id ) ) {
			update_post_meta( $post_id, 'wf_p_name', 'Test Client' );
			update_post_meta( $post_id, 'wf_p_mobile', '01700000000' );
			update_post_meta( $post_id, 'wf_p_email', 'test@example.com' );
			update_post_meta( $post_id, 'wf_p_business', 'Demo Business' );
			update_post_meta( $post_id, 'wf_p_service', 'Facebook Ads' );
			update_post_meta( $post_id, 'wf_p_status', 'lead' );
			update_post_meta( $post_id, 'wf_p_payment_status', 'unpaid' );
		}
	}
	update_option( 'womensfight_demo_project_seeded', 1 );
}
add_action( 'init', 'womensfight_seed_demo_project', 20 );

function womensfight_project_columns( $columns ) {
	return array(
		'cb'         => $columns['cb'],
		'title'      => 'ক্লায়েন্ট / প্রজেক্ট',
		'wf_service' => 'Service',
		'wf_status'  => 'Status',
		'wf_payment' => 'Payment',
		'date'       => $columns['date'],
	);
}
add_filter( 'manage_wf_project_posts_columns', 'womensfight_project_columns' );

function womensfight_project_column_content( $column, $post_id ) {
	$statuses          = womensfight_project_statuses();
	$payment_statuses  = womensfight_project_payment_statuses();
	if ( 'wf_service' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'wf_p_service', true ) );
	} elseif ( 'wf_status' === $column ) {
		$status = get_post_meta( $post_id, 'wf_p_status', true );
		echo esc_html( isset( $statuses[ $status ] ) ? $statuses[ $status ] : $status );
	} elseif ( 'wf_payment' === $column ) {
		$payment = get_post_meta( $post_id, 'wf_p_payment_status', true );
		echo esc_html( isset( $payment_statuses[ $payment ] ) ? $payment_statuses[ $payment ] : $payment );
	}
}
add_action( 'manage_wf_project_posts_custom_column', 'womensfight_project_column_content', 10, 2 );

/**
 * Edit-screen meta box: contact/service info (read-only reference for
 * now) plus the two dropdowns — Status and Payment Status — that move a
 * project through the pipeline. Saved via a normal post-save nonce.
 */
function womensfight_register_project_detail_box() {
	add_meta_box(
		'womensfight_project_details',
		'Project Details',
		'womensfight_render_project_detail_box',
		'wf_project',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes_wf_project', 'womensfight_register_project_detail_box' );

function womensfight_render_project_detail_box( $post ) {
	wp_nonce_field( 'womensfight_save_project', 'womensfight_project_nonce' );

	$info_fields = array(
		'wf_p_name'     => 'নাম',
		'wf_p_mobile'   => 'Mobile',
		'wf_p_whatsapp' => 'WhatsApp',
		'wf_p_email'    => 'Email',
		'wf_p_business' => 'Business',
		'wf_p_service'  => 'Service',
		'wf_p_budget'   => 'Budget',
		'wf_p_location' => 'Business Location',
		'wf_p_fb_link'  => 'Facebook Page / Website Link',
		'wf_p_message'  => 'Message / Requirement',
	);

	// Append only the fields belonging to this project's actual service,
	// instead of a wall of blank rows for every other service's fields.
	$service     = get_post_meta( $post->ID, 'wf_p_service', true );
	$service_map = womensfight_service_specific_fields();
	$all_labels  = womensfight_lead_fields();
	if ( isset( $service_map[ $service ] ) ) {
		foreach ( $service_map[ $service ] as $field_key ) {
			$info_fields[ str_replace( 'wf_', 'wf_p_', $field_key ) ] = isset( $all_labels[ $field_key ] ) ? $all_labels[ $field_key ] : $field_key;
		}
	}

	$dashboard_url = womensfight_get_project_dashboard_url( $post->ID );
	echo '<div style="background:#f0f6fc; border:1px solid #c3dcf0; border-radius:6px; padding:12px 14px; margin-bottom:14px;">';
	echo '<strong>Client Dashboard Link</strong> (এই লিংক client-কে WhatsApp/Email করে দিন):<br>';
	echo '<input type="text" readonly value="' . esc_url( $dashboard_url ) . '" style="width:100%; margin-top:6px;" onclick="this.select();">';
	echo '</div>';

	echo '<table class="widefat striped"><tbody>';
	foreach ( $info_fields as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th style="width:220px;">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( $value ) ) . '</td></tr>';
	}

	$image_id = get_post_meta( $post->ID, 'wf_p_image_id', true );
	echo '<tr><th>ছবি</th><td>';
	if ( $image_id ) {
		echo wp_get_attachment_image( $image_id, 'medium' );
	} else {
		echo '<em>কোনো ছবি দেওয়া হয়নি</em>';
	}
	echo '</td></tr>';

	$status  = get_post_meta( $post->ID, 'wf_p_status', true );
	$payment = get_post_meta( $post->ID, 'wf_p_payment_status', true );

	echo '<tr><th>Status</th><td><select name="wf_p_status">';
	foreach ( womensfight_project_statuses() as $value => $label ) {
		echo '<option value="' . esc_attr( $value ) . '"' . selected( $status, $value, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></td></tr>';

	echo '<tr><th>Payment Status</th><td><select name="wf_p_payment_status">';
	foreach ( womensfight_project_payment_statuses() as $value => $label ) {
		echo '<option value="' . esc_attr( $value ) . '"' . selected( $payment, $value, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></td></tr>';

	$approved = get_post_meta( $post->ID, 'wf_p_client_approved', true );
	echo '<tr><th>Client Approval</th><td>';
	if ( $approved ) {
		echo '✅ Approved on ' . esc_html( get_post_meta( $post->ID, 'wf_p_approved_at', true ) );
	} else {
		echo '<em>এখনো client অ্যাপ্রুভ করেননি</em>';
	}
	echo '</td></tr>';
	echo '</tbody></table>';

	// Onboarding info, submitted by the client on the dashboard.
	$onboarding_submitted = get_post_meta( $post->ID, 'wf_p_onboarding_submitted', true );
	echo '<h4 style="margin-top:18px;">Onboarding তথ্য</h4>';
	if ( $onboarding_submitted ) {
		$onboarding_fields = array(
			'wf_p_onboarding_address'      => 'ঠিকানা',
			'wf_p_onboarding_assets_link'  => 'Brand Assets Link',
			'wf_p_onboarding_access_notes' => 'Access/Login তথ্য',
			'wf_p_onboarding_notes'        => 'অতিরিক্ত নোট',
		);
		echo '<table class="widefat striped"><tbody>';
		foreach ( $onboarding_fields as $key => $label ) {
			echo '<tr><th style="width:220px;">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( get_post_meta( $post->ID, $key, true ) ) ) . '</td></tr>';
		}
		echo '</tbody></table>';
	} else {
		echo '<p><em>Client এখনো Onboarding ফর্ম পূরণ করেননি।</em></p>';
	}

	// Deliverable files — admin uploads here, client sees/downloads them
	// on the dashboard.
	echo '<h4 style="margin-top:18px;">Deliverable Files</h4>';
	$deliverables = get_post_meta( $post->ID, 'wf_p_deliverable', false );
	if ( $deliverables ) {
		echo '<ul>';
		foreach ( $deliverables as $attachment_id ) {
			echo '<li><a href="' . esc_url( wp_get_attachment_url( $attachment_id ) ) . '" target="_blank" rel="noopener">' . esc_html( basename( get_attached_file( $attachment_id ) ) ) . '</a></li>';
		}
		echo '</ul>';
	} else {
		echo '<p><em>এখনো কোনো ফাইল আপলোড করা হয়নি।</em></p>';
	}
	echo '<form method="post" enctype="multipart/form-data" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
	echo '<input type="hidden" name="action" value="womensfight_upload_deliverable">';
	echo '<input type="hidden" name="project_id" value="' . esc_attr( $post->ID ) . '">';
	wp_nonce_field( 'womensfight_upload_deliverable_' . $post->ID );
	echo '<input type="file" name="wf_deliverable[]" multiple> ';
	echo '<button type="submit" class="button">ফাইল আপলোড করুন</button>';
	echo '</form>';

	// Revision requests submitted by the client — read-only reference.
	echo '<h4 style="margin-top:18px;">Revision Requests</h4>';
	$revisions = get_post_meta( $post->ID, 'wf_p_revision', false );
	if ( $revisions ) {
		echo '<table class="widefat striped"><tbody>';
		foreach ( array_reverse( $revisions ) as $revision ) {
			echo '<tr><th style="width:140px;">' . esc_html( $revision['date'] ) . '</th><td>' . nl2br( esc_html( $revision['note'] ) );
			if ( ! empty( $revision['file'] ) ) {
				echo '<br><a href="' . esc_url( wp_get_attachment_url( $revision['file'] ) ) . '" target="_blank" rel="noopener">সংযুক্ত ফাইল দেখুন</a>';
			}
			echo '</td></tr>';
		}
		echo '</tbody></table>';
	} else {
		echo '<p><em>এখনো কোনো Revision Request আসেনি।</em></p>';
	}
}

function womensfight_save_project_meta( $post_id ) {
	if (
		! isset( $_POST['womensfight_project_nonce'] ) ||
		! wp_verify_nonce( wp_unslash( $_POST['womensfight_project_nonce'] ), 'womensfight_save_project' )
	) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	if ( isset( $_POST['wf_p_status'] ) && array_key_exists( wp_unslash( $_POST['wf_p_status'] ), womensfight_project_statuses() ) ) {
		update_post_meta( $post_id, 'wf_p_status', sanitize_key( wp_unslash( $_POST['wf_p_status'] ) ) );
	}
	if ( isset( $_POST['wf_p_payment_status'] ) && array_key_exists( wp_unslash( $_POST['wf_p_payment_status'] ), womensfight_project_payment_statuses() ) ) {
		update_post_meta( $post_id, 'wf_p_payment_status', sanitize_key( wp_unslash( $_POST['wf_p_payment_status'] ) ) );
	}
}
add_action( 'save_post_wf_project', 'womensfight_save_project_meta' );

/* =======================================================================
 * Client Dashboard — private token-link access (no login required).
 * Onboarding form, deliverable file list, revision requests and final
 * approval all live on page-project.php (/project/?token=...), looked
 * up fresh on every request via the token below (never a bare post ID,
 * so a client can't guess another client's project by changing a number
 * in the URL).
 * ===================================================================== */

function womensfight_get_project_by_token( $token ) {
	$token = sanitize_text_field( $token );
	if ( '' === $token ) {
		return null;
	}
	$posts = get_posts(
		array(
			'post_type'      => 'wf_project',
			'meta_key'       => 'wf_p_token',
			'meta_value'     => $token,
			'posts_per_page' => 1,
		)
	);
	return $posts ? $posts[0] : null;
}

function womensfight_handle_onboarding_submit() {
	$token   = isset( $_POST['wf_token'] ) ? sanitize_text_field( wp_unslash( $_POST['wf_token'] ) ) : '';
	$project = womensfight_get_project_by_token( $token );
	if ( ! $project ) {
		wp_die( 'প্রজেক্ট খুঁজে পাওয়া যায়নি। লিংকটি চেক করুন।' );
	}
	if (
		! isset( $_POST['womensfight_onboarding_nonce'] ) ||
		! wp_verify_nonce( wp_unslash( $_POST['womensfight_onboarding_nonce'] ), 'womensfight_onboarding_' . $token )
	) {
		wp_die( 'Security check failed. দয়া করে পেজ রিফ্রেশ করে আবার চেষ্টা করুন।' );
	}

	$fields = array(
		'wf_p_onboarding_address'      => 'sanitize_textarea_field',
		'wf_p_onboarding_assets_link'  => 'esc_url_raw',
		'wf_p_onboarding_access_notes' => 'sanitize_textarea_field',
		'wf_p_onboarding_notes'        => 'sanitize_textarea_field',
	);
	foreach ( $fields as $key => $sanitizer ) {
		$value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
		update_post_meta( $project->ID, $key, call_user_func( $sanitizer, $value ) );
	}
	update_post_meta( $project->ID, 'wf_p_onboarding_submitted', '1' );

	wp_safe_redirect( add_query_arg( 'submitted', 'onboarding', womensfight_get_project_dashboard_url( $project->ID ) ) . '#wf-dash' );
	exit;
}
add_action( 'admin_post_womensfight_submit_onboarding', 'womensfight_handle_onboarding_submit' );
add_action( 'admin_post_nopriv_womensfight_submit_onboarding', 'womensfight_handle_onboarding_submit' );

function womensfight_handle_revision_submit() {
	$token   = isset( $_POST['wf_token'] ) ? sanitize_text_field( wp_unslash( $_POST['wf_token'] ) ) : '';
	$project = womensfight_get_project_by_token( $token );
	if ( ! $project ) {
		wp_die( 'প্রজেক্ট খুঁজে পাওয়া যায়নি। লিংকটি চেক করুন।' );
	}
	if (
		! isset( $_POST['womensfight_revision_nonce'] ) ||
		! wp_verify_nonce( wp_unslash( $_POST['womensfight_revision_nonce'] ), 'womensfight_revision_' . $token )
	) {
		wp_die( 'Security check failed. দয়া করে পেজ রিফ্রেশ করে আবার চেষ্টা করুন।' );
	}

	$note = isset( $_POST['wf_revision_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wf_revision_note'] ) ) : '';
	if ( '' !== $note ) {
		$file_id = 0;
		if ( ! empty( $_FILES['wf_revision_file']['name'] ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
			require_once ABSPATH . 'wp-admin/includes/file.php';
			require_once ABSPATH . 'wp-admin/includes/media.php';
			$attachment_id = media_handle_upload( 'wf_revision_file', $project->ID );
			if ( ! is_wp_error( $attachment_id ) ) {
				$file_id = $attachment_id;
			}
		}
		add_post_meta(
			$project->ID,
			'wf_p_revision',
			array(
				'note' => $note,
				'file' => $file_id,
				'date' => current_time( 'Y-m-d H:i' ),
			)
		);
	}

	wp_safe_redirect( add_query_arg( 'submitted', 'revision', womensfight_get_project_dashboard_url( $project->ID ) ) . '#wf-dash' );
	exit;
}
add_action( 'admin_post_womensfight_submit_revision', 'womensfight_handle_revision_submit' );
add_action( 'admin_post_nopriv_womensfight_submit_revision', 'womensfight_handle_revision_submit' );

function womensfight_handle_final_approval() {
	$token   = isset( $_POST['wf_token'] ) ? sanitize_text_field( wp_unslash( $_POST['wf_token'] ) ) : '';
	$project = womensfight_get_project_by_token( $token );
	if ( ! $project ) {
		wp_die( 'প্রজেক্ট খুঁজে পাওয়া যায়নি। লিংকটি চেক করুন।' );
	}
	if (
		! isset( $_POST['womensfight_approval_nonce'] ) ||
		! wp_verify_nonce( wp_unslash( $_POST['womensfight_approval_nonce'] ), 'womensfight_approval_' . $token )
	) {
		wp_die( 'Security check failed. দয়া করে পেজ রিফ্রেশ করে আবার চেষ্টা করুন।' );
	}

	update_post_meta( $project->ID, 'wf_p_client_approved', '1' );
	update_post_meta( $project->ID, 'wf_p_approved_at', current_time( 'Y-m-d H:i' ) );
	update_post_meta( $project->ID, 'wf_p_status', 'approved' );

	wp_safe_redirect( add_query_arg( 'submitted', 'approved', womensfight_get_project_dashboard_url( $project->ID ) ) . '#wf-dash' );
	exit;
}
add_action( 'admin_post_womensfight_submit_approval', 'womensfight_handle_final_approval' );
add_action( 'admin_post_nopriv_womensfight_submit_approval', 'womensfight_handle_final_approval' );

/**
 * Admin-only: upload deliverable file(s) for a project from its edit
 * screen (mini upload form in the meta box below). Attachment IDs are
 * appended to the project's wf_p_deliverable postmeta (one row per
 * file), which the client dashboard lists as downloads.
 */
function womensfight_handle_deliverable_upload() {
	$project_id = isset( $_POST['project_id'] ) ? (int) $_POST['project_id'] : 0;
	if ( ! $project_id || ! current_user_can( 'edit_post', $project_id ) ) {
		wp_die( 'Permission denied.' );
	}
	check_admin_referer( 'womensfight_upload_deliverable_' . $project_id );

	if ( ! empty( $_FILES['wf_deliverable']['name'][0] ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$files = $_FILES['wf_deliverable'];
		foreach ( $files['name'] as $i => $name ) {
			if ( empty( $name ) ) {
				continue;
			}
			$_FILES['wf_deliverable_single'] = array(
				'name'     => $files['name'][ $i ],
				'type'     => $files['type'][ $i ],
				'tmp_name' => $files['tmp_name'][ $i ],
				'error'    => $files['error'][ $i ],
				'size'     => $files['size'][ $i ],
			);
			$attachment_id = media_handle_upload( 'wf_deliverable_single', $project_id );
			if ( ! is_wp_error( $attachment_id ) ) {
				add_post_meta( $project_id, 'wf_p_deliverable', $attachment_id );
			}
		}
	}

	wp_safe_redirect( admin_url( 'post.php?post=' . $project_id . '&action=edit' ) );
	exit;
}
add_action( 'admin_post_womensfight_upload_deliverable', 'womensfight_handle_deliverable_upload' );

/* =======================================================================
 * Demo Website Library — a catalog the admin manages entirely from
 * wp-admin (no code needed to add a demo), shown on a public
 * /demo-library/ page with category filters. "Choose This Design"
 * opens a small form whose submissions land in a separate "Demo
 * Requests" post type, kept apart from Leads/Contact/Projects.
 * ===================================================================== */

function womensfight_register_demo_cpt() {
	register_post_type(
		'wf_demo',
		array(
			'labels'          => array(
				'name'          => 'Demo Websites',
				'singular_name' => 'Demo Website',
				'menu_name'     => 'Demo Websites',
				'all_items'     => 'All Demos',
				'add_new_item'  => 'Add New Demo',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-screenoptions',
			'menu_position'   => 29,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'womensfight_register_demo_cpt' );

function womensfight_demo_categories() {
	return array( 'Agency', 'Restaurant', 'E-commerce', 'Portfolio', 'Education', 'Real Estate', 'Local Business' );
}

function womensfight_register_demo_detail_box() {
	add_meta_box( 'womensfight_demo_details', 'Demo Details', 'womensfight_render_demo_detail_box', 'wf_demo', 'normal', 'high' );
}
add_action( 'add_meta_boxes_wf_demo', 'womensfight_register_demo_detail_box' );

function womensfight_render_demo_detail_box( $post ) {
	wp_nonce_field( 'womensfight_save_demo', 'womensfight_demo_nonce' );

	$project_id  = get_post_meta( $post->ID, 'wf_d_project_id', true );
	$category    = get_post_meta( $post->ID, 'wf_d_category', true );
	$description = get_post_meta( $post->ID, 'wf_d_description', true );
	$live_url    = get_post_meta( $post->ID, 'wf_d_live_url', true );
	$screenshot  = get_post_meta( $post->ID, 'wf_d_screenshot_id', true );
	?>
	<table class="widefat" style="margin-bottom:14px;">
		<tbody>
			<tr>
				<th style="width:180px;">Project ID</th>
				<td><input type="text" name="wf_d_project_id" value="<?php echo esc_attr( $project_id ); ?>" class="regular-text" placeholder="যেমন: WF-001"></td>
			</tr>
			<tr>
				<th>Category</th>
				<td>
					<select name="wf_d_category">
						<?php foreach ( womensfight_demo_categories() as $cat ) : ?>
							<option value="<?php echo esc_attr( $cat ); ?>" <?php selected( $category, $cat ); ?>><?php echo esc_html( $cat ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Live Preview URL</th>
				<td><input type="url" name="wf_d_live_url" value="<?php echo esc_attr( $live_url ); ?>" class="regular-text" placeholder="https://..."></td>
			</tr>
			<tr>
				<th>Short Description</th>
				<td><textarea name="wf_d_description" rows="3" class="large-text"><?php echo esc_textarea( $description ); ?></textarea></td>
			</tr>
		</tbody>
	</table>

	<p><strong>Screenshot</strong></p>
	<?php if ( $screenshot ) : ?>
		<div style="margin-bottom:8px;"><?php echo wp_get_attachment_image( $screenshot, 'medium' ); ?></div>
	<?php endif; ?>
	<input type="file" name="wf_d_screenshot" accept="image/*">
	<p class="description">নতুন ছবি দিলে আগেরটা replace হয়ে যাবে। খালি রাখলে আগেরটাই থাকবে।</p>
	<?php
}

function womensfight_save_demo_meta( $post_id ) {
	if ( ! isset( $_POST['womensfight_demo_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['womensfight_demo_nonce'] ), 'womensfight_save_demo' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['wf_d_project_id'] ) ) {
		update_post_meta( $post_id, 'wf_d_project_id', sanitize_text_field( wp_unslash( $_POST['wf_d_project_id'] ) ) );
	}
	if ( isset( $_POST['wf_d_category'] ) && in_array( wp_unslash( $_POST['wf_d_category'] ), womensfight_demo_categories(), true ) ) {
		update_post_meta( $post_id, 'wf_d_category', sanitize_text_field( wp_unslash( $_POST['wf_d_category'] ) ) );
	}
	if ( isset( $_POST['wf_d_live_url'] ) ) {
		update_post_meta( $post_id, 'wf_d_live_url', esc_url_raw( wp_unslash( $_POST['wf_d_live_url'] ) ) );
	}
	if ( isset( $_POST['wf_d_description'] ) ) {
		update_post_meta( $post_id, 'wf_d_description', sanitize_textarea_field( wp_unslash( $_POST['wf_d_description'] ) ) );
	}

	if ( ! empty( $_FILES['wf_d_screenshot']['name'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		$attachment_id = media_handle_upload( 'wf_d_screenshot', $post_id );
		if ( ! is_wp_error( $attachment_id ) ) {
			update_post_meta( $post_id, 'wf_d_screenshot_id', $attachment_id );
		}
	}
}
add_action( 'save_post_wf_demo', 'womensfight_save_demo_meta' );

function womensfight_demo_columns( $columns ) {
	return array(
		'cb'            => $columns['cb'],
		'title'         => 'Demo Name',
		'wf_project_id' => 'Project ID',
		'wf_category'   => 'Category',
		'date'          => $columns['date'],
	);
}
add_filter( 'manage_wf_demo_posts_columns', 'womensfight_demo_columns' );

function womensfight_demo_column_content( $column, $post_id ) {
	if ( 'wf_project_id' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'wf_d_project_id', true ) );
	} elseif ( 'wf_category' === $column ) {
		echo esc_html( get_post_meta( $post_id, 'wf_d_category', true ) );
	}
}
add_action( 'manage_wf_demo_posts_custom_column', 'womensfight_demo_column_content', 10, 2 );

/**
 * [wf_demo_library] rendering helper — every published demo, newest
 * first, as plain data the template can loop over.
 */
function womensfight_get_demo_list() {
	return get_posts(
		array(
			'post_type'      => 'wf_demo',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
}

/* --- Demo Requests ("Choose This Design" submissions) --- */

function womensfight_register_demo_request_cpt() {
	register_post_type(
		'wf_demo_request',
		array(
			'labels'          => array(
				'name'          => 'Demo Requests',
				'singular_name' => 'Demo Request',
				'menu_name'     => 'Demo Requests',
				'all_items'     => 'All Requests',
			),
			'public'          => false,
			'show_ui'         => true,
			'show_in_menu'    => true,
			'menu_icon'       => 'dashicons-yes-alt',
			'menu_position'   => 30,
			'supports'        => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap'    => true,
		)
	);
}
add_action( 'init', 'womensfight_register_demo_request_cpt' );

function womensfight_demo_request_fields() {
	return array(
		'dr_name'         => 'নাম',
		'dr_whatsapp'     => 'WhatsApp',
		'dr_business'     => 'Business Name',
		'dr_demo_name'    => 'Selected Demo',
		'dr_project_id'   => 'Project ID',
		'dr_requirement'  => 'Requirement / Changes',
	);
}

function womensfight_handle_demo_request_submit() {
	if (
		! isset( $_POST['womensfight_demo_request_nonce'] ) ||
		! wp_verify_nonce( wp_unslash( $_POST['womensfight_demo_request_nonce'] ), 'womensfight_demo_request' )
	) {
		wp_die( 'Security check failed. দয়া করে পেজ রিফ্রেশ করে আবার চেষ্টা করুন।' );
	}

	$data = array();
	foreach ( womensfight_demo_request_fields() as $key => $label ) {
		$data[ $key ] = isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '';
	}

	$title = '' !== $data['dr_name'] ? $data['dr_name'] . ' — ' . $data['dr_demo_name'] : 'Demo Request — ' . current_time( 'Y-m-d H:i' );

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'wf_demo_request',
			'post_title'  => $title,
			'post_status' => 'publish',
		)
	);

	if ( $post_id && ! is_wp_error( $post_id ) ) {
		foreach ( $data as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
	}

	$redirect = isset( $_POST['dr_redirect'] ) ? esc_url_raw( wp_unslash( $_POST['dr_redirect'] ) ) : home_url( '/' );
	wp_safe_redirect( add_query_arg( 'demo_submitted', '1', $redirect ) . '#wf-demo-library' );
	exit;
}
add_action( 'admin_post_womensfight_submit_demo_request', 'womensfight_handle_demo_request_submit' );
add_action( 'admin_post_nopriv_womensfight_submit_demo_request', 'womensfight_handle_demo_request_submit' );

function womensfight_demo_request_columns( $columns ) {
	return array(
		'cb'            => $columns['cb'],
		'title'         => 'Client / Demo',
		'dr_whatsapp'   => 'WhatsApp',
		'dr_business'   => 'Business',
		'dr_demo_name'  => 'Selected Demo',
		'date'          => $columns['date'],
	);
}
add_filter( 'manage_wf_demo_request_posts_columns', 'womensfight_demo_request_columns' );

function womensfight_demo_request_column_content( $column, $post_id ) {
	if ( in_array( $column, array( 'dr_whatsapp', 'dr_business', 'dr_demo_name' ), true ) ) {
		echo esc_html( get_post_meta( $post_id, $column, true ) );
	}
}
add_action( 'manage_wf_demo_request_posts_custom_column', 'womensfight_demo_request_column_content', 10, 2 );

function womensfight_register_demo_request_detail_box() {
	add_meta_box( 'womensfight_demo_request_details', 'Request Details', 'womensfight_render_demo_request_detail_box', 'wf_demo_request', 'normal', 'high' );
}
add_action( 'add_meta_boxes_wf_demo_request', 'womensfight_register_demo_request_detail_box' );

function womensfight_render_demo_request_detail_box( $post ) {
	echo '<table class="widefat striped"><tbody>';
	foreach ( womensfight_demo_request_fields() as $key => $label ) {
		echo '<tr><th style="width:220px;">' . esc_html( $label ) . '</th><td>' . nl2br( esc_html( get_post_meta( $post->ID, $key, true ) ) ) . '</td></tr>';
	}
	echo '</tbody></table>';
}
