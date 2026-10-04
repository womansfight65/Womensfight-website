<?php
/**
 * WF-Invoice — private, login-gated internal dashboard at /wf-invoice/.
 *
 * Mirrors the Lead CRM's pattern (page-crm-lead.php): no get_header()/
 * get_footer(), WordPress's own login (wp_signon) gated by the
 * WFI_CAP_ACCESS capability, and its own scoped CSS/JS. The ?v=...
 * query var switches between views — plain server-rendered pages
 * (not AJAX/SPA) for a simple, easy-to-maintain multi-page app.
 *
 * Fully isolated: functions.php only has one require_once line for
 * inc/invoice/invoice-core.php.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$login_error = '';

if ( isset( $_POST['wfi_login_submit'] ) ) {
	if ( ! isset( $_POST['wfi_login_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wfi_login_nonce'] ), 'wfi_login' ) ) {
		$login_error = 'Security check failed, আবার চেষ্টা করুন।';
	} else {
		$creds = array(
			'user_login'    => isset( $_POST['wfi_username'] ) ? sanitize_text_field( wp_unslash( $_POST['wfi_username'] ) ) : '',
			'user_password' => isset( $_POST['wfi_password'] ) ? $_POST['wfi_password'] : '',
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

if ( isset( $_GET['wfi_logout'] ) ) {
	wp_logout();
	wp_safe_redirect( get_permalink() );
	exit;
}

$logged_in  = is_user_logged_in();
$can_access = wfi_user_can_access();
$view       = isset( $_GET['v'] ) ? sanitize_key( wp_unslash( $_GET['v'] ) ) : 'dashboard';

/* The print view is a bare A4 sheet — no shell at all — so it's
   handled completely separately before any HTML is output. */
if ( $can_access && 'print' === $view ) {
	$id  = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	$inv = wfi_get_invoice( $id );
	if ( ! $inv ) {
		wp_die( 'Invoice পাওয়া যায়নি।' );
	}
	$client   = wfi_get_client( $inv->client_id );
	$items    = wfi_get_invoice_items( $id );
	$settings = wfi_get_settings();
	?><!DOCTYPE html>
	<html <?php language_attributes(); ?>>
	<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex,nofollow">
	<title><?php echo esc_html( $inv->invoice_number ); ?> — WF-Invoice</title>
	<link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri() ); ?>">
	<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/inc/invoice/assets/invoice.css' ); ?>">
	</head>
	<body class="wfi-body">
	<div class="wfi-print-bar wfi-no-print">
		<button type="button" class="btn btn-primary" onclick="window.print()">Print / Download PDF</button>
		<a class="btn btn-ghost" href="<?php echo esc_url( wfi_page_url( 'invoice', array( 'id' => $id ) ) ); ?>">Back</a>
	</div>
	<?php wfi_render_invoice_sheet( $inv, $client, $items, $settings ); ?>
	</body>
	</html>
	<?php
	exit;
}

$logo_icon = esc_url( get_template_directory_uri() . '/assets/img/logo-icon.png' );
$logo_full = esc_url( get_template_directory_uri() . '/assets/img/logo-full.png' );
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>WF-Invoice — Women's Fight</title>
<link rel="icon" href="<?php echo $logo_icon; ?>">
<link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri() ); ?>">
<link rel="stylesheet" href="<?php echo esc_url( get_template_directory_uri() . '/inc/invoice/assets/invoice.css' ); ?>">
</head>
<body class="wfi-body">

<?php if ( $can_access ) : ?>

	<div class="wfi-shell">
		<aside class="wfi-sidebar" id="wfi-sidebar">
			<div class="brand"><img src="<?php echo $logo_full; ?>" alt="Women's Fight"> WF-Invoice</div>
			<nav>
				<?php
				$nav = array(
					'dashboard'      => 'Dashboard',
					'clients'        => 'Clients',
					'create-invoice' => 'Create Invoice',
					'invoices'       => 'Invoices',
					'payments'       => 'Payments',
					'reports'        => 'Reports',
					'settings'       => 'Settings',
				);
				foreach ( $nav as $key => $label ) {
					$active = $view === $key ? ' active' : '';
					echo '<a class="' . esc_attr( ltrim( $active ) ) . '" href="' . esc_url( wfi_page_url( $key ) ) . '">' . esc_html( $label ) . '</a>';
				}
				?>
				<a href="<?php echo esc_url( add_query_arg( 'wfi_logout', '1', get_permalink() ) ); ?>">লগআউট</a>
			</nav>
		</aside>

		<div class="wfi-main">
			<div class="wfi-topbar">
				<button type="button" class="wfi-mobile-toggle" id="wfi-toggle">☰</button>
				<div class="who">
					<span><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
				</div>
			</div>
			<div class="wfi-content">
				<?php
				switch ( $view ) {
					case 'clients':
						wfi_render_clients_list();
						break;
					case 'client':
						wfi_render_client_view();
						break;
					case 'create-invoice':
						wfi_render_create_invoice();
						break;
					case 'invoice':
						wfi_render_invoice_view();
						break;
					case 'invoices':
						wfi_render_invoices_history();
						break;
					case 'payments':
						wfi_render_payments_list();
						break;
					case 'reports':
						wfi_render_reports();
						break;
					case 'settings':
						wfi_render_settings();
						break;
					default:
						wfi_render_dashboard();
				}
				?>
			</div>
		</div>
	</div>

	<script>
	document.getElementById('wfi-toggle').addEventListener('click', function () {
		document.getElementById('wfi-sidebar').classList.toggle('open');
	});
	</script>
	<script src="<?php echo esc_url( get_template_directory_uri() . '/inc/invoice/assets/invoice.js' ); ?>"></script>

<?php elseif ( $logged_in ) : ?>

	<div class="wfi-login-wrap">
		<div class="wfi-login-card">
			<h1>অনুমতি নেই</h1>
			<p class="sub">আপনার একাউন্টে WF-Invoice দেখার অনুমতি নেই। প্রয়োজনে Administrator-এর সাথে যোগাযোগ করুন।</p>
			<a class="wfi-hint" href="<?php echo esc_url( add_query_arg( 'wfi_logout', '1', get_permalink() ) ); ?>">অন্য একাউন্টে লগইন করুন →</a>
		</div>
	</div>

<?php else : ?>

	<div class="wfi-login-wrap">
		<div class="wfi-login-card">
			<h1>WF-Invoice</h1>
			<p class="sub">শুধুমাত্র অনুমোদিত Team Member-দের জন্য।</p>
			<?php if ( $login_error ) : ?>
				<div class="wfi-login-error"><?php echo esc_html( $login_error ); ?></div>
			<?php endif; ?>
			<form method="post">
				<label for="wfi_username">Username বা Email</label>
				<input type="text" id="wfi_username" name="wfi_username" required autofocus>
				<label for="wfi_password">Password</label>
				<input type="password" id="wfi_password" name="wfi_password" required>
				<?php wp_nonce_field( 'wfi_login', 'wfi_login_nonce' ); ?>
				<button type="submit" name="wfi_login_submit" value="1" class="btn btn-primary btn-block">লগইন করুন</button>
			</form>
		</div>
	</div>

<?php endif; ?>

</body>
</html>
