<?php
/**
 * The header for Women's Fight Agency theme.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$logo_full = esc_url( get_template_directory_uri() . '/assets/img/logo-full.png' );
$logo_icon = esc_url( get_template_directory_uri() . '/assets/img/logo-icon.png' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" href="<?php echo $logo_icon; ?>">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<svg style="position:absolute;width:0;height:0;overflow:hidden;" aria-hidden="true">
<defs>
<symbol id="i-megaphone" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10v4h3l5 4V6l-5 4H3z"/><path d="M15 8.5a3.5 3.5 0 0 1 0 7"/></symbol>
<symbol id="i-share" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="6" cy="12" r="2.4"/><circle cx="18" cy="6" r="2.4"/><circle cx="18" cy="18" r="2.4"/><path d="M8.2 10.8 15.8 7.2M8.2 13.2l7.6 3.6"/></symbol>
<symbol id="i-search" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></symbol>
<symbol id="i-code" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18"/><path d="m9 13-2 2 2 2M15 13l2 2-2 2"/></symbol>
<symbol id="i-cursor" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="m4 4 7 16 2-6 6-2Z"/><path d="m14 14 6 6"/></symbol>
<symbol id="i-film" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8h18v11a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1Z"/><path d="m3 8 2-4h3l-2 4M9 8l2-4h3l-2 4M15 8l2-4h3l-2 4"/></symbol>
<symbol id="i-brain" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="7" width="10" height="10" rx="2"/><path d="M9 3v4M15 3v4M9 17v4M15 17v4M3 9h4M3 15h4M17 9h4M17 15h4"/></symbol>
<symbol id="i-chart" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M10 20V4M16 20v-7"/><path d="M2 20h20"/></symbol>
<symbol id="i-grid" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></symbol>
<symbol id="i-gift" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9h18v4H3Z"/><path d="M5 13v7a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-7"/><path d="M12 9v12"/><path d="M12 9C9 9 8 6.5 9.5 5S13 4 12 9ZM12 9c3 0 4-2.5 2.5-4S11 4 12 9Z"/></symbol>
<symbol id="i-mail" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></symbol>
<symbol id="i-phone" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2Z"/></symbol>
<symbol id="i-pin" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12Z"/><circle cx="12" cy="9" r="2.6"/></symbol>
<symbol id="i-users" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="3"/><path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6"/><circle cx="17.5" cy="9" r="2.4"/><path d="M15.5 14.2c2.5.4 4.5 2.6 4.5 5.8"/></symbol>
<symbol id="i-check" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></symbol>
<symbol id="i-bolt" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2 4 14h6l-1 8 9-12h-6Z"/></symbol>
<symbol id="i-shield" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 5v6c0 5 3.5 9 8 11 4.5-2 8-6 8-11V5Z"/><path d="m9 12 2 2 4-4"/></symbol>
<symbol id="i-target" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><circle cx="12" cy="12" r="4.2"/><circle cx="12" cy="12" r="1"/></symbol>
<symbol id="i-bulb" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6M10 21h4"/><path d="M12 3a6 6 0 0 0-3.5 10.9c.5.4.8 1 .8 1.6v.5h5.4v-.5c0-.6.3-1.2.8-1.6A6 6 0 0 0 12 3Z"/></symbol>
<symbol id="i-heart" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7-4.4-9.5-8.8C.8 8 2 4.5 5.3 3.7 7.6 3.1 10 4 12 6.4 14 4 16.4 3.1 18.7 3.7 22 4.5 23.2 8 21.5 11.2 19 15.6 12 20 12 20Z"/></symbol>
<symbol id="i-layers" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 13 9 5 9-5"/></symbol>
<symbol id="i-doc" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h9l5 5v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M14 3v5h5"/><path d="M8 13h8M8 17h5"/></symbol>
<symbol id="i-cart" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="20" r="1.3"/><circle cx="18" cy="20" r="1.3"/><path d="M3 4h2l2.4 12.5a2 2 0 0 0 2 1.5h7.6a2 2 0 0 0 2-1.6L21 8H6.2"/></symbol>
<symbol id="i-mobile" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></symbol>
<symbol id="i-edit" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></symbol>
<symbol id="i-split" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M6 4v6a4 4 0 0 0 4 4h4a4 4 0 0 1 4 4v2"/><circle cx="6" cy="4" r="1.6"/><circle cx="18" cy="20" r="1.6"/></symbol>
<symbol id="i-form" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="4" width="14" height="17" rx="2"/><rect x="9" y="2" width="6" height="4" rx="1"/><path d="M9 11h6M9 15h6"/></symbol>
<symbol id="i-mask" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9 9c.5-1 1.5-1 2 0M13 9c.5-1 1.5-1 2 0"/><path d="M8.5 14c1.5 2 5.5 2 7 0"/></symbol>
<symbol id="i-gear" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/></symbol>
<symbol id="i-chat" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v11H8l-4 4Z"/><path d="M8 9h8M8 12h5"/></symbol>
<symbol id="i-route" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19c4-1 4-8 8-8s4-7 8-8"/><circle cx="4" cy="19" r="1.6"/><circle cx="20" cy="3" r="1.6"/></symbol>
</defs>
</svg>

<header class="nav">
  <div class="wrap nav-inner">
    <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
      <img class="logo-full" src="<?php echo $logo_full; ?>" alt="Women's Fight">
      <img class="logo-icon" src="<?php echo $logo_icon; ?>" alt="Women's Fight">
    </a>

    <?php
    if ( has_nav_menu( 'primary' ) ) {
        wp_nav_menu(
            array(
                'theme_location' => 'primary',
                'container'      => false,
                'menu_class'     => 'links',
                'menu_id'        => 'navLinks',
                'depth'          => 2,
            )
        );
    } else {
        echo '<nav class="links" id="navLinks"><span style="color:var(--ink-faint);font-size:.85rem;">মেনু সেট আপ করা হয়নি — Appearance → Menus দেখুন।</span></nav>';
    }
    ?>

    <div class="nav-cta">
      <a class="btn btn-primary" href="<?php echo esc_url( womensfight_page_url( 'contact' ) ); ?>">যোগাযোগ করুন</a>
      <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu"><span></span></button>
    </div>
  </div>
</header>
