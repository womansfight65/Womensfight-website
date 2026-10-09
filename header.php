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
<symbol id="i-sm-facebook" viewBox="0 0 24 24"><path fill="currentColor" d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06C2 17.08 5.66 21.24 10.44 22v-7.03H7.9v-2.91h2.54V9.85c0-2.51 1.49-3.9 3.77-3.9 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.78-1.63 1.57v1.88h2.78l-.45 2.91h-2.33V22C18.34 21.24 22 17.08 22 12.06Z"/></symbol>
<symbol id="i-sm-instagram" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2c2.7 0 3.06.01 4.12.06 1.06.05 1.79.22 2.43.47.66.26 1.22.6 1.77 1.15.5.5.84 1.03 1.1 1.72.26.64.43 1.37.48 2.43.05 1.06.06 1.42.06 4.17s-.01 3.06-.06 4.12c-.05 1.06-.22 1.79-.47 2.43a4.6 4.6 0 0 1-1.15 1.77 4.6 4.6 0 0 1-1.72 1.1c-.64.26-1.37.43-2.43.48-1.06.05-1.42.06-4.17.06s-3.06-.01-4.12-.06c-1.06-.05-1.79-.22-2.43-.47a4.6 4.6 0 0 1-1.77-1.15 4.6 4.6 0 0 1-1.1-1.72c-.26-.64-.43-1.37-.48-2.43C2.01 15.06 2 14.7 2 12s.01-3.06.06-4.12c.05-1.06.22-1.79.47-2.43.26-.66.6-1.22 1.15-1.77A4.6 4.6 0 0 1 5.4 2.58c.64-.26 1.37-.43 2.43-.48C8.9 2.01 9.3 2 12 2Zm0 1.8c-2.66 0-2.97.01-4.02.06-.97.05-1.5.2-1.85.34-.46.18-.8.4-1.15.75-.35.35-.57.69-.75 1.15-.14.35-.3.88-.34 1.85-.05 1.05-.06 1.36-.06 4.02s.01 2.97.06 4.02c.05.97.2 1.5.34 1.85.18.46.4.8.75 1.15.35.35.69.57 1.15.75.35.14.88.3 1.85.34 1.05.05 1.36.06 4.02.06s2.97-.01 4.02-.06c.97-.05 1.5-.2 1.85-.34.46-.18.8-.4 1.15-.75.35-.35.57-.69.75-1.15.14-.35.3-.88.34-1.85.05-1.05.06-1.36.06-4.02s-.01-2.97-.06-4.02c-.05-.97-.2-1.5-.34-1.85a3 3 0 0 0-.75-1.15 3 3 0 0 0-1.15-.75c-.35-.14-.88-.3-1.85-.34C14.97 3.81 14.66 3.8 12 3.8Zm0 3.2a5 5 0 1 1 0 10 5 5 0 0 1 0-10Zm0 1.8a3.2 3.2 0 1 0 0 6.4 3.2 3.2 0 0 0 0-6.4Zm5.2-2.95a1.17 1.17 0 1 1 0 2.34 1.17 1.17 0 0 1 0-2.34Z"/></symbol>
<symbol id="i-sm-youtube" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="4" fill="currentColor"/><path fill="#fff" d="M10 8.3v7.4l6.4-3.7Z"/></symbol>
<symbol id="i-sm-tiktok" viewBox="0 0 24 24"><path fill="currentColor" d="M16.6 2h-3.3v13.9c0 1.5-1.2 2.7-2.7 2.7a2.7 2.7 0 0 1-2.7-2.7c0-1.5 1.2-2.7 2.7-2.7.3 0 .6.05.86.14V9.9a6 6 0 0 0-.86-.06A6 6 0 0 0 4.6 15.9a6 6 0 0 0 6 6 6 6 0 0 0 6-6V8.3a8.3 8.3 0 0 0 4.8 1.5V6.5a5 5 0 0 1-4.8-4.5Z"/></symbol>
<symbol id="i-sm-telegram" viewBox="0 0 24 24"><path fill="currentColor" d="m21.5 4-18.3 7c-.8.3-.8 1.5 0 1.8l4.6 1.5 1.8 5.4c.3.8 1.3.9 1.8.2l2.4-3 4.6 3.4c.7.5 1.7.1 1.9-.8l3-14.5c.2-1-.9-1.7-1.8-1Zm-3.2 3.6-8.3 7.3-.3 3-1.4-4.3 8.6-6.6c.3-.2.7.2.4.6Z"/></symbol>
<symbol id="i-sm-x" viewBox="0 0 24 24"><path fill="currentColor" d="m4 3 7.1 9.1L4.1 21h2.5l5.8-6.6L17 21h3.9l-7.5-9.6L20.1 3h-2.5l-5.3 6.1L8.9 3H4Z"/></symbol>
<symbol id="i-sm-linkedin" viewBox="0 0 24 24"><path fill="currentColor" d="M5.3 3.5A2.1 2.1 0 1 0 5.3 7.7 2.1 2.1 0 0 0 5.3 3.5ZM3.5 9h3.6v11.5H3.5ZM10 9h3.5v1.6h.05c.5-.9 1.7-1.9 3.5-1.9 3.7 0 4.4 2.4 4.4 5.6v6.2h-3.6v-5.5c0-1.3 0-3-1.8-3s-2.1 1.4-2.1 2.9v5.6H10Z"/></symbol>
<symbol id="i-sm-spotify" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" fill="currentColor"/><path fill="none" stroke="#fff" stroke-width="1.8" stroke-linecap="round" d="M6.5 9.8c3.2-1 7.2-.7 9.8.9M6.8 13c2.6-.7 5.9-.5 8 .8M7.2 16c2-.5 4.5-.3 6.1.6"/></symbol>
<symbol id="i-sm-snapchat" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2.5c2.9 0 4.6 2.2 4.5 4.9 0 .5-.07 1.1-.1 1.6.3.17.9.47 1.35.64.6.22.95.37.95.85 0 .3-.2.5-.5.65-.3.16-.8.3-1.2.45-.17.06-.3.2-.27.4.04.3.3.75.65 1.15.7.8 1.75 1.2 1.75 1.65 0 .5-1.1.75-2.05.9-.15.02-.25.17-.3.4-.08.35-.25.85-.85.85-.5 0-.9-.17-1.55-.17-.75 0-1.25.85-3.38.85s-2.63-.85-3.38-.85c-.65 0-1.05.17-1.55.17-.6 0-.77-.5-.85-.85-.05-.23-.15-.38-.3-.4-.95-.15-2.05-.4-2.05-.9 0-.45 1.05-.85 1.75-1.65.35-.4.6-.85.65-1.15.03-.2-.1-.34-.27-.4-.4-.15-.9-.3-1.2-.45-.3-.15-.5-.35-.5-.65 0-.48.35-.63.95-.85.45-.17 1.05-.47 1.35-.64-.03-.5-.1-1.1-.1-1.6C7.4 4.7 9.1 2.5 12 2.5Z"/></symbol>
<symbol id="i-sm-discord" viewBox="0 0 24 24"><path fill="currentColor" d="M19.3 5.3A17.6 17.6 0 0 0 15 4l-.3.6c1.6.4 2.4.9 3.3 1.6-2.8-1.3-6.1-1.3-8.9-.3A9 9 0 0 1 9.3 4L9 4c-1.5.3-2.9.8-4.3 1.3C2.3 9 1.6 12.6 1.9 16.2c1.8 1.3 3.5 2.1 5.2 2.6l.7-1.2c-.6-.2-1.3-.5-1.9-.9l.4-.3c3.3 1.5 7.1 1.5 10.4 0l.4.3c-.6.4-1.2.7-1.9.9l.7 1.2c1.7-.5 3.4-1.3 5.2-2.6.4-4-.6-7.6-2.8-10.9ZM8.7 14.3c-.9 0-1.6-.9-1.6-1.9 0-1.1.7-2 1.6-2s1.7.9 1.6 2c0 1-.7 1.9-1.6 1.9Zm6.6 0c-.9 0-1.6-.9-1.6-1.9 0-1.1.7-2 1.6-2s1.7.9 1.6 2c0 1-.7 1.9-1.6 1.9Z"/></symbol>
</defs>
</svg>

<header class="nav">
  <div class="wrap nav-inner">
    <a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>">
      <img class="logo-full" src="<?php echo $logo_full; ?>" alt="Women's Fight — Ramganj, Lakshmipur" width="800" height="245" decoding="async" fetchpriority="high">
      <img class="logo-icon" src="<?php echo $logo_icon; ?>" alt="Women's Fight — Ramganj, Lakshmipur" width="193" height="193" decoding="async">
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
