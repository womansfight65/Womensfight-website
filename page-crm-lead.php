<?php
/**
 * Lead CRM — private, login-gated internal dashboard at /crm-lead/.
 *
 * Deliberately does NOT use get_header()/get_footer() — this is an
 * internal team tool, not a public marketing page, so the site nav,
 * WhatsApp float, Meta Pixel/GA4 (wp_head) etc. are all skipped. It
 * only enqueues the main stylesheet (for shared color tokens/fonts)
 * plus its own scoped CSS/JS from inc/leadcrm/assets/.
 *
 * Access: WordPress's own login system (wp_signon), gated by the
 * WFA_CRM_CAP capability (see inc/leadcrm/crm-core.php) — granted to
 * the "CRM User" role and to Administrators. A logged-in user without
 * that capability sees "access denied", never the dashboard.
 *
 * Fully isolated like the Academy and Accounts modules: functions.php
 * only has the one require_once line for inc/leadcrm/crm-core.php.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$login_error = '';

if ( isset( $_POST['crm_login_submit'] ) ) {
	if ( ! isset( $_POST['crm_login_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['crm_login_nonce'] ), 'wfa_crm_login' ) ) {
		$login_error = 'Security check failed, আবার চেষ্টা করুন।';
	} else {
		$creds = array(
			'user_login'    => isset( $_POST['crm_username'] ) ? sanitize_text_field( wp_unslash( $_POST['crm_username'] ) ) : '',
			'user_password' => isset( $_POST['crm_password'] ) ? $_POST['crm_password'] : '',
			'remember'      => true,
		);
		$user = wp_signon( $creds, is_ssl() );
		if ( is_wp_error( $user ) ) {
			$login_error = 'ভুল Username বা Password।';
		} else {
			wp_safe_redirect( get_permalink() );
			exit;
		}
	}
}

if ( isset( $_GET['crm_logout'] ) ) {
	wp_logout();
	wp_safe_redirect( get_permalink() );
	exit;
}

$logged_in   = is_user_logged_in();
$can_access  = wfa_crm_user_can_access();
$logo_icon   = esc_url( get_template_directory_uri() . '/assets/img/logo-icon.png' );
$logo_full   = esc_url( get_template_directory_uri() . '/assets/img/logo-full.png' );
$style_uri   = get_stylesheet_uri();
$crm_css_uri = esc_url( get_template_directory_uri() . '/inc/leadcrm/assets/crm.css' );
$crm_js_uri  = esc_url( get_template_directory_uri() . '/inc/leadcrm/assets/crm.js' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>Lead CRM — Women's Fight</title>
<link rel="icon" href="<?php echo $logo_icon; ?>">
<link rel="stylesheet" href="<?php echo esc_url( $style_uri ); ?>">
<link rel="stylesheet" href="<?php echo $crm_css_uri; ?>">
</head>
<body class="wfa-crm-body">

<?php if ( $can_access ) : ?>

	<div class="wfa-crm-topbar">
		<div class="brand"><img src="<?php echo $logo_full; ?>" alt="Women's Fight" height="30"> Lead CRM</div>
		<div class="who">
			<span><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
			<a href="<?php echo esc_url( add_query_arg( 'crm_logout', '1', get_permalink() ) ); ?>">লগআউট</a>
		</div>
	</div>

	<div class="crm-wrap">
		<?php wfa_crm_render_dashboard(); ?>
	</div>

	<div class="crm-drawer-overlay" id="crm-drawer-overlay">
		<div class="crm-drawer" id="crm-drawer"></div>
	</div>

	<script>
	var wfaCrm = {
		ajaxUrl: <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>,
		nonce: <?php echo wp_json_encode( wp_create_nonce( 'wfa_crm_nonce' ) ); ?>
	};
	</script>
	<script src="<?php echo $crm_js_uri; ?>"></script>

<?php elseif ( $logged_in ) : ?>

	<div class="crm-login-wrap">
		<div class="crm-login-card">
			<h1>অনুমতি নেই</h1>
			<p class="sub">আপনার একাউন্টে Lead CRM দেখার অনুমতি নেই। প্রয়োজনে Administrator-এর সাথে যোগাযোগ করুন।</p>
			<a class="crm-hint" href="<?php echo esc_url( add_query_arg( 'crm_logout', '1', get_permalink() ) ); ?>">অন্য একাউন্টে লগইন করুন →</a>
		</div>
	</div>

<?php else : ?>

	<div class="crm-login-wrap">
		<div class="crm-login-card">
			<h1>Lead CRM</h1>
			<p class="sub">শুধুমাত্র অনুমোদিত Team Member-দের জন্য।</p>
			<?php if ( $login_error ) : ?>
				<div class="crm-login-error"><?php echo esc_html( $login_error ); ?></div>
			<?php endif; ?>
			<form method="post">
				<label for="crm_username">Username বা Email</label>
				<input type="text" id="crm_username" name="crm_username" required autofocus>
				<label for="crm_password">Password</label>
				<input type="password" id="crm_password" name="crm_password" required>
				<?php wp_nonce_field( 'wfa_crm_login', 'crm_login_nonce' ); ?>
				<button type="submit" name="crm_login_submit" value="1" class="btn btn-primary btn-block">লগইন করুন</button>
			</form>
		</div>
	</div>

<?php endif; ?>

</body>
</html>
