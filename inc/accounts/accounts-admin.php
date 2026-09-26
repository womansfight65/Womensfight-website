<?php
/**
 * WF Simple Accounts — wp-admin screens: Overview, Add Entry,
 * Transactions, Monthly Report. Plain WP-admin styling (a small
 * dedicated stylesheet, loaded only on these screens) — the public
 * site's dark theme is never loaded here.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wfa_register_admin_menu() {
	add_menu_page(
		'WF Simple Accounts',
		'Accounts',
		wfa_accounts_capability(),
		'wfa-accounts',
		'wfa_render_overview_page',
		'dashicons-money-alt',
		58
	);
	add_submenu_page( 'wfa-accounts', 'Overview', 'Overview', wfa_accounts_capability(), 'wfa-accounts', 'wfa_render_overview_page' );
	add_submenu_page( 'wfa-accounts', 'Add Entry', 'Add Entry', wfa_accounts_capability(), 'wfa-add-entry', 'wfa_render_add_entry_page' );
	add_submenu_page( 'wfa-accounts', 'Transactions', 'Transactions', wfa_accounts_capability(), 'wfa-transactions', 'wfa_render_transactions_page' );
	add_submenu_page( 'wfa-accounts', 'Monthly Report', 'Monthly Report', wfa_accounts_capability(), 'wfa-monthly-report', 'wfa_render_monthly_report_page' );
}
add_action( 'admin_menu', 'wfa_register_admin_menu' );

function wfa_enqueue_admin_assets( $hook_suffix ) {
	if ( false === strpos( $hook_suffix, 'wfa-' ) ) {
		return;
	}
	wp_enqueue_style(
		'wfa-accounts-admin',
		get_template_directory_uri() . '/inc/accounts/assets/accounts-admin.css',
		array(),
		WFA_ACCOUNTS_DB_VERSION
	);
}
add_action( 'admin_enqueue_scripts', 'wfa_enqueue_admin_assets' );

/**
 * The month/year picker form used at the top of all three
 * month-scoped screens (Overview, Transactions, Monthly Report).
 */
function wfa_render_month_picker( $page, $current_month ) {
	echo '<form method="get" class="wfa-month-form">';
	echo '<input type="hidden" name="page" value="' . esc_attr( $page ) . '">';
	echo '<label for="wfa_month">মাস নির্বাচন করুন:</label> ';
	echo '<input type="month" id="wfa_month" name="wfa_month" value="' . esc_attr( $current_month ) . '">';
	echo ' <button type="submit" class="button">দেখুন</button>';
	echo '</form>';
}

function wfa_current_month_from_request() {
	$month = isset( $_GET['wfa_month'] ) ? sanitize_text_field( wp_unslash( $_GET['wfa_month'] ) ) : '';
	if ( ! preg_match( '/^\d{4}-\d{2}$/', $month ) ) {
		$month = current_time( 'Y-m' );
	}
	return $month;
}

function wfa_render_message_banner() {
	if ( empty( $_GET['wfa_msg'] ) ) {
		return;
	}
	$messages = array(
		'wfa_added'         => 'নতুন Entry যোগ করা হয়েছে।',
		'wfa_updated'       => 'Entry আপডেট করা হয়েছে।',
		'wfa_trashed'       => 'Entry Trash-এ পাঠানো হয়েছে।',
		'wfa_restored'      => 'Entry ফিরিয়ে আনা হয়েছে।',
		'wfa_deleted'       => 'Entry স্থায়ীভাবে মুছে ফেলা হয়েছে।',
		'wfa_budget_saved'  => 'Ads Budget সংরক্ষণ করা হয়েছে।',
	);
	$key = sanitize_key( wp_unslash( $_GET['wfa_msg'] ) );
	if ( isset( $messages[ $key ] ) ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $messages[ $key ] ) . '</p></div>';
	}
}

/* ---------------------------------------------------------------------
 * Overview
 * ------------------------------------------------------------------- */
function wfa_render_overview_page() {
	wfa_accounts_require_access();
	$month = wfa_current_month_from_request();
	$t     = wfa_accounts_get_monthly_totals( $month );

	echo '<div class="wrap wfa-wrap">';
	echo '<h1>WF Simple Accounts — Overview</h1>';
	wfa_render_message_banner();
	wfa_render_month_picker( 'wfa-accounts', $month );

	$budget_pct = $t['ads_budget'] > 0 ? min( 100, round( ( $t['ads_spend'] / $t['ads_budget'] ) * 100 ) ) : 0;
	$over       = $t['remaining_ads_budget'] < 0;

	echo '<div class="wfa-cards">';
	echo '<div class="wfa-card"><span>Monthly Ads Budget</span><b>' . esc_html( wfa_accounts_money( $t['ads_budget'] ) ) . '</b></div>';
	echo '<div class="wfa-card"><span>Actual Ads Spend</span><b>' . esc_html( wfa_accounts_money( $t['ads_spend'] ) ) . '</b></div>';
	echo '<div class="wfa-card' . ( $over ? ' wfa-card-warn' : '' ) . '"><span>Remaining Ads Budget</span><b>' . esc_html( wfa_accounts_money( $t['remaining_ads_budget'] ) ) . '</b></div>';
	echo '<div class="wfa-card"><span>Total Income</span><b>' . esc_html( wfa_accounts_money( $t['income'] ) ) . '</b></div>';
	echo '<div class="wfa-card"><span>General Expense</span><b>' . esc_html( wfa_accounts_money( $t['general_expense'] ) ) . '</b></div>';
	echo '<div class="wfa-card"><span>Employee Salary</span><b>' . esc_html( wfa_accounts_money( $t['salary'] ) ) . '</b></div>';
	echo '<div class="wfa-card"><span>Total Expense</span><b>' . esc_html( wfa_accounts_money( $t['total_expense'] ) ) . '</b></div>';
	echo '<div class="wfa-card wfa-card-' . ( $t['net_profit'] >= 0 ? 'good' : 'warn' ) . '"><span>Net Profit</span><b>' . esc_html( wfa_accounts_money( $t['net_profit'] ) ) . '</b></div>';
	echo '</div>';

	echo '<h2>Ads Budget ব্যবহার</h2>';
	echo '<div class="wfa-progress"><div class="wfa-progress-bar' . ( $over ? ' wfa-progress-over' : '' ) . '" style="width:' . esc_attr( $budget_pct ) . '%;"></div></div>';
	echo '<p class="wfa-hint">' . esc_html( $budget_pct ) . '% ব্যবহৃত' . ( $over ? ' — বাজেট ছাড়িয়ে গেছে' : '' ) . '</p>';

	echo '<p class="wfa-actions">';
	echo '<a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=wfa-add-entry' ) ) . '">নতুন Entry যোগ করুন</a> ';
	echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=wfa-transactions&wfa_month=' . $month ) ) . '">সব Transaction দেখুন</a> ';
	echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=wfa-monthly-report&wfa_month=' . $month ) ) . '">Monthly Report দেখুন</a>';
	echo '</p>';

	echo '</div>';
}

/* ---------------------------------------------------------------------
 * Add / Edit Entry (shared form; ?edit=ID switches to edit mode)
 * ------------------------------------------------------------------- */
function wfa_render_add_entry_page() {
	wfa_accounts_require_access();
	global $wpdb;

	$edit_id = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;
	$row     = null;
	if ( $edit_id > 0 ) {
		$table = wfa_accounts_transactions_table();
		$row   = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $edit_id ) );
	}

	$types    = wfa_accounts_types();
	$methods  = wfa_accounts_payment_methods();
	$today    = current_time( 'Y-m-d' );
	$this_mo  = current_time( 'Y-m' );

	echo '<div class="wrap wfa-wrap">';
	echo '<h1>' . ( $row ? 'Edit Entry' : 'Add Entry' ) . '</h1>';
	wfa_render_message_banner();

	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="wfa-form">';
	echo '<input type="hidden" name="action" value="wfa_save_transaction">';
	echo '<input type="hidden" name="wfa_edit_id" value="' . esc_attr( $row ? $row->id : 0 ) . '">';
	wp_nonce_field( 'wfa_save_transaction', 'wfa_txn_nonce' );

	echo '<table class="form-table"><tbody>';

	echo '<tr><th><label for="wfa_entry_date">Date</label></th><td><input type="date" id="wfa_entry_date" name="wfa_entry_date" value="' . esc_attr( $row ? $row->entry_date : $today ) . '" required></td></tr>';

	echo '<tr><th><label for="wfa_type">Transaction Type</label></th><td><select id="wfa_type" name="wfa_type" required>';
	foreach ( $types as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '"' . selected( $row ? $row->type : '', $key, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></td></tr>';

	echo '<tr><th><label for="wfa_category">Category</label></th><td><input type="text" id="wfa_category" name="wfa_category" class="regular-text" placeholder="যেমন: Facebook Ads, Office Rent, ইত্যাদি" value="' . esc_attr( $row ? $row->category : '' ) . '"></td></tr>';

	echo '<tr><th><label for="wfa_amount">Amount (৳)</label></th><td><input type="number" step="0.01" min="0" id="wfa_amount" name="wfa_amount" class="regular-text" required value="' . esc_attr( $row ? $row->amount : '' ) . '"></td></tr>';

	echo '<tr><th><label for="wfa_payment_method">Payment Method</label></th><td><select id="wfa_payment_method" name="wfa_payment_method">';
	echo '<option value="">সিলেক্ট করুন</option>';
	foreach ( $methods as $m ) {
		echo '<option' . selected( $row ? $row->payment_method : '', $m, false ) . '>' . esc_html( $m ) . '</option>';
	}
	echo '</select></td></tr>';

	echo '<tr><th><label for="wfa_note">Short Note</label></th><td><textarea id="wfa_note" name="wfa_note" class="large-text" rows="3">' . esc_textarea( $row ? $row->note : '' ) . '</textarea></td></tr>';

	echo '</tbody></table>';
	submit_button( $row ? 'Update Entry' : 'Add Entry' );
	echo '</form>';

	echo '<hr>';
	echo '<h2>এই মাসের Ads Budget সেট করুন</h2>';
	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="wfa-form wfa-budget-form">';
	echo '<input type="hidden" name="action" value="wfa_set_budget">';
	wp_nonce_field( 'wfa_set_budget', 'wfa_budget_nonce' );
	echo '<table class="form-table"><tbody>';
	echo '<tr><th><label for="wfa_budget_month">Month</label></th><td><input type="month" id="wfa_budget_month" name="wfa_budget_month" value="' . esc_attr( $this_mo ) . '"></td></tr>';
	echo '<tr><th><label for="wfa_budget_amount">Monthly Ads Budget (৳)</label></th><td><input type="number" step="0.01" min="0" id="wfa_budget_amount" name="wfa_budget_amount" class="regular-text" value="' . esc_attr( wfa_accounts_get_monthly_budget( $this_mo ) ) . '"></td></tr>';
	echo '</tbody></table>';
	submit_button( 'Budget সংরক্ষণ করুন', 'secondary' );
	echo '</form>';

	echo '</div>';
}

/* ---------------------------------------------------------------------
 * Transactions list — active + a Trash view for recovery
 * ------------------------------------------------------------------- */
function wfa_render_transactions_page() {
	wfa_accounts_require_access();
	global $wpdb;

	$month = wfa_current_month_from_request();
	$view  = isset( $_GET['wfa_view'] ) && 'trash' === $_GET['wfa_view'] ? 'trash' : 'active';
	$table = wfa_accounts_transactions_table();
	$types = wfa_accounts_types();

	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE month_year = %s AND status = %s ORDER BY entry_date DESC, id DESC",
			$month,
			'trash' === $view ? 'trashed' : 'active'
		)
	);

	$export_url = wp_nonce_url( admin_url( 'admin-post.php?action=wfa_export_csv&wfa_month=' . $month ), 'wfa_export_csv' );

	echo '<div class="wrap wfa-wrap">';
	echo '<h1>Transactions</h1>';
	wfa_render_message_banner();
	wfa_render_month_picker( 'wfa-transactions', $month );

	echo '<p class="wfa-actions">';
	echo '<a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=wfa-add-entry' ) ) . '">নতুন Entry</a> ';
	echo '<a class="button" href="' . esc_url( $export_url ) . '">CSV Export করুন</a> ';
	if ( 'trash' === $view ) {
		echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=wfa-transactions&wfa_month=' . $month ) ) . '">সক্রিয় তালিকায় ফিরুন</a>';
	} else {
		echo '<a class="button" href="' . esc_url( admin_url( 'admin.php?page=wfa-transactions&wfa_month=' . $month . '&wfa_view=trash' ) ) . '">Trash দেখুন</a>';
	}
	echo '</p>';

	echo '<table class="widefat striped">';
	echo '<thead><tr><th>Date</th><th>Type</th><th>Category</th><th>Amount</th><th>Payment Method</th><th>Note</th><th>Created By</th><th>Action</th></tr></thead><tbody>';

	if ( $rows ) {
		foreach ( $rows as $row ) {
			$user = get_userdata( $row->created_by );
			echo '<tr>';
			echo '<td>' . esc_html( $row->entry_date ) . '</td>';
			echo '<td>' . esc_html( isset( $types[ $row->type ] ) ? $types[ $row->type ] : $row->type ) . '</td>';
			echo '<td>' . esc_html( $row->category ) . '</td>';
			echo '<td>' . esc_html( wfa_accounts_money( $row->amount ) ) . '</td>';
			echo '<td>' . esc_html( $row->payment_method ) . '</td>';
			echo '<td>' . esc_html( $row->note ) . '</td>';
			echo '<td>' . esc_html( $user ? $user->display_name : '—' ) . '</td>';
			echo '<td>';
			if ( 'trash' === $view ) {
				$restore_url = wp_nonce_url( admin_url( 'admin-post.php?action=wfa_restore_transaction&id=' . $row->id ), 'wfa_restore_transaction_' . $row->id );
				$perm_url    = wp_nonce_url( admin_url( 'admin-post.php?action=wfa_permanent_delete_transaction&id=' . $row->id ), 'wfa_permanent_delete_' . $row->id );
				echo '<a href="' . esc_url( $restore_url ) . '">Restore</a> | ';
				echo '<a href="' . esc_url( $perm_url ) . '" class="wfa-danger" onclick="return confirm(\'এই Entry স্থায়ীভাবে মুছে যাবে, ফেরত আনা যাবে না। নিশ্চিত?\');">স্থায়ীভাবে মুছুন</a>';
			} else {
				$delete_url = wp_nonce_url( admin_url( 'admin-post.php?action=wfa_delete_transaction&id=' . $row->id ), 'wfa_delete_transaction_' . $row->id );
				echo '<a href="' . esc_url( admin_url( 'admin.php?page=wfa-add-entry&edit=' . $row->id ) ) . '">Edit</a> | ';
				echo '<a href="' . esc_url( $delete_url ) . '" class="wfa-danger" onclick="return confirm(\'এই Entry Trash-এ পাঠানো হবে। নিশ্চিত?\');">Delete</a>';
			}
			echo '</td>';
			echo '</tr>';
		}
	} else {
		echo '<tr><td colspan="8">এই মাসে কোনো Entry নেই।</td></tr>';
	}

	echo '</tbody></table>';
	echo '</div>';
}

/* ---------------------------------------------------------------------
 * Monthly Report — same totals as Overview, plus a category breakdown
 * per type for more detail.
 * ------------------------------------------------------------------- */
function wfa_render_monthly_report_page() {
	wfa_accounts_require_access();
	$month = wfa_current_month_from_request();
	$t     = wfa_accounts_get_monthly_totals( $month );
	$types = wfa_accounts_types();

	echo '<div class="wrap wfa-wrap">';
	echo '<h1>Monthly Report</h1>';
	wfa_render_message_banner();
	wfa_render_month_picker( 'wfa-monthly-report', $month );

	echo '<table class="widefat striped wfa-summary-table"><tbody>';
	echo '<tr><th>Monthly Ads Budget</th><td>' . esc_html( wfa_accounts_money( $t['ads_budget'] ) ) . '</td></tr>';
	echo '<tr><th>Actual Ads Spend</th><td>' . esc_html( wfa_accounts_money( $t['ads_spend'] ) ) . '</td></tr>';
	echo '<tr><th>Remaining Ads Budget</th><td>' . esc_html( wfa_accounts_money( $t['remaining_ads_budget'] ) ) . '</td></tr>';
	echo '<tr><th>Total Income</th><td>' . esc_html( wfa_accounts_money( $t['income'] ) ) . '</td></tr>';
	echo '<tr><th>General Expense</th><td>' . esc_html( wfa_accounts_money( $t['general_expense'] ) ) . '</td></tr>';
	echo '<tr><th>Employee Salary</th><td>' . esc_html( wfa_accounts_money( $t['salary'] ) ) . '</td></tr>';
	echo '<tr><th>Total Expense</th><td>' . esc_html( wfa_accounts_money( $t['total_expense'] ) ) . '</td></tr>';
	echo '<tr><th><strong>Net Profit</strong></th><td><strong>' . esc_html( wfa_accounts_money( $t['net_profit'] ) ) . '</strong></td></tr>';
	echo '</tbody></table>';

	foreach ( $types as $key => $label ) {
		$breakdown = wfa_accounts_get_category_breakdown( $month, $key );
		if ( ! $breakdown ) {
			continue;
		}
		echo '<h2>' . esc_html( $label ) . ' — Category অনুযায়ী</h2>';
		echo '<table class="widefat striped" style="max-width:520px;"><tbody>';
		foreach ( $breakdown as $b ) {
			echo '<tr><td>' . esc_html( $b->category ? $b->category : '(কোনো Category না)' ) . '</td><td>' . esc_html( wfa_accounts_money( $b->subtotal ) ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}

	$export_url = wp_nonce_url( admin_url( 'admin-post.php?action=wfa_export_csv&wfa_month=' . $month ), 'wfa_export_csv' );
	echo '<p class="wfa-actions"><a class="button" href="' . esc_url( $export_url ) . '">এই মাসের CSV Export করুন</a></p>';

	echo '</div>';
}
