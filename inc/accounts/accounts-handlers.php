<?php
/**
 * WF Simple Accounts — form handlers (add/edit/delete/restore transaction,
 * set monthly budget, CSV export). Every handler:
 * - requires current_user_can( wfa_accounts_capability() )
 * - verifies a nonce
 * - sanitizes/validates input
 * - uses $wpdb->prepare() for every query
 * - redirects back (POST/Redirect/GET) so refreshing never re-submits
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The capability every Accounts screen and handler checks. Centralized
 * here so Step 2 (an "Accounts Manager" role) only needs a change in
 * one place, not a hunt through every function.
 */
function wfa_accounts_capability() {
	return 'manage_options';
}

function wfa_accounts_require_access() {
	if ( ! current_user_can( wfa_accounts_capability() ) ) {
		wp_die( 'এই পাতা দেখার অনুমতি আপনার নেই।' );
	}
}

/**
 * Add or update a transaction. Presence of a valid, existing wfa_id
 * decides update vs insert — the form itself is shared between both
 * (see accounts-admin.php's Add/Edit Entry screen).
 */
function wfa_handle_save_transaction() {
	wfa_accounts_require_access();
	if ( ! isset( $_POST['wfa_txn_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wfa_txn_nonce'] ), 'wfa_save_transaction' ) ) {
		wp_die( 'Security check failed। দয়া করে পেজ রিফ্রেশ করে আবার চেষ্টা করুন।' );
	}

	global $wpdb;
	$table = wfa_accounts_transactions_table();

	$entry_date = isset( $_POST['wfa_entry_date'] ) ? sanitize_text_field( wp_unslash( $_POST['wfa_entry_date'] ) ) : '';
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $entry_date ) ) {
		$entry_date = current_time( 'Y-m-d' );
	}
	$month_year = substr( $entry_date, 0, 7 );

	$type = isset( $_POST['wfa_type'] ) ? sanitize_key( wp_unslash( $_POST['wfa_type'] ) ) : '';
	if ( ! array_key_exists( $type, wfa_accounts_types() ) ) {
		wp_die( 'ভুল Transaction Type।' );
	}

	$category       = isset( $_POST['wfa_category'] ) ? sanitize_text_field( wp_unslash( $_POST['wfa_category'] ) ) : '';
	$amount         = isset( $_POST['wfa_amount'] ) ? (float) $_POST['wfa_amount'] : 0;
	$amount         = max( 0, round( $amount, 2 ) );
	$payment_method = isset( $_POST['wfa_payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['wfa_payment_method'] ) ) : '';
	$note           = isset( $_POST['wfa_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['wfa_note'] ) ) : '';

	$edit_id = isset( $_POST['wfa_edit_id'] ) ? absint( $_POST['wfa_edit_id'] ) : 0;

	if ( $edit_id > 0 ) {
		$wpdb->update(
			$table,
			array(
				'entry_date'     => $entry_date,
				'month_year'     => $month_year,
				'type'           => $type,
				'category'       => $category,
				'amount'         => $amount,
				'payment_method' => $payment_method,
				'note'           => $note,
				'updated_by'     => get_current_user_id(),
				'updated_at'     => current_time( 'mysql' ),
			),
			array( 'id' => $edit_id ),
			array( '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%d', '%s' ),
			array( '%d' )
		);
		$message = 'wfa_updated';
	} else {
		$wpdb->insert(
			$table,
			array(
				'entry_date'     => $entry_date,
				'month_year'     => $month_year,
				'type'           => $type,
				'category'       => $category,
				'amount'         => $amount,
				'payment_method' => $payment_method,
				'note'           => $note,
				'status'         => 'active',
				'created_by'     => get_current_user_id(),
				'created_at'     => current_time( 'mysql' ),
			),
			array( '%s', '%s', '%s', '%s', '%f', '%s', '%s', '%d', '%s' )
		);
		$message = 'wfa_added';
	}

	wp_safe_redirect(
		add_query_arg(
			array( 'page' => 'wfa-transactions', 'wfa_month' => $month_year, 'wfa_msg' => $message ),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_wfa_save_transaction', 'wfa_handle_save_transaction' );

/**
 * Soft-delete — sets status to "trashed" instead of removing the row,
 * so a mis-click can be recovered from the Trash view.
 */
function wfa_handle_delete_transaction() {
	wfa_accounts_require_access();
	$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	check_admin_referer( 'wfa_delete_transaction_' . $id );

	global $wpdb;
	$table = wfa_accounts_transactions_table();
	$month_year = $wpdb->get_var( $wpdb->prepare( "SELECT month_year FROM {$table} WHERE id = %d", $id ) );

	$wpdb->update(
		$table,
		array( 'status' => 'trashed', 'updated_by' => get_current_user_id(), 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => $id ),
		array( '%s', '%d', '%s' ),
		array( '%d' )
	);

	wp_safe_redirect(
		add_query_arg(
			array( 'page' => 'wfa-transactions', 'wfa_month' => $month_year, 'wfa_msg' => 'wfa_trashed' ),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_wfa_delete_transaction', 'wfa_handle_delete_transaction' );

/**
 * Restore a trashed transaction back to active.
 */
function wfa_handle_restore_transaction() {
	wfa_accounts_require_access();
	$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	check_admin_referer( 'wfa_restore_transaction_' . $id );

	global $wpdb;
	$table = wfa_accounts_transactions_table();
	$month_year = $wpdb->get_var( $wpdb->prepare( "SELECT month_year FROM {$table} WHERE id = %d", $id ) );

	$wpdb->update(
		$table,
		array( 'status' => 'active', 'updated_by' => get_current_user_id(), 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => $id ),
		array( '%s', '%d', '%s' ),
		array( '%d' )
	);

	wp_safe_redirect(
		add_query_arg(
			array( 'page' => 'wfa-transactions', 'wfa_month' => $month_year, 'wfa_view' => 'trash', 'wfa_msg' => 'wfa_restored' ),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_wfa_restore_transaction', 'wfa_handle_restore_transaction' );

/**
 * Permanently delete a trashed transaction — separate action, separate
 * confirmation, only ever reachable from the Trash view (never a plain
 * "Delete" button), exactly to keep this destructive step deliberate.
 */
function wfa_handle_permanent_delete_transaction() {
	wfa_accounts_require_access();
	$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	check_admin_referer( 'wfa_permanent_delete_' . $id );

	global $wpdb;
	$table = wfa_accounts_transactions_table();
	$month_year = $wpdb->get_var( $wpdb->prepare( "SELECT month_year FROM {$table} WHERE id = %d AND status = 'trashed'", $id ) );
	if ( $month_year ) {
		$wpdb->delete( $table, array( 'id' => $id, 'status' => 'trashed' ), array( '%d', '%s' ) );
	}

	wp_safe_redirect(
		add_query_arg(
			array( 'page' => 'wfa-transactions', 'wfa_month' => $month_year, 'wfa_view' => 'trash', 'wfa_msg' => 'wfa_deleted' ),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_wfa_permanent_delete_transaction', 'wfa_handle_permanent_delete_transaction' );

/**
 * Set/update the current month's Ads Budget target.
 */
function wfa_handle_set_budget() {
	wfa_accounts_require_access();
	if ( ! isset( $_POST['wfa_budget_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wfa_budget_nonce'] ), 'wfa_set_budget' ) ) {
		wp_die( 'Security check failed। দয়া করে পেজ রিফ্রেশ করে আবার চেষ্টা করুন।' );
	}

	$month_year = isset( $_POST['wfa_budget_month'] ) ? sanitize_text_field( wp_unslash( $_POST['wfa_budget_month'] ) ) : '';
	if ( ! preg_match( '/^\d{4}-\d{2}$/', $month_year ) ) {
		$month_year = current_time( 'Y-m' );
	}
	$amount = isset( $_POST['wfa_budget_amount'] ) ? max( 0, round( (float) $_POST['wfa_budget_amount'], 2 ) ) : 0;

	wfa_accounts_set_monthly_budget( $month_year, $amount );

	wp_safe_redirect(
		add_query_arg(
			array( 'page' => 'wfa-accounts', 'wfa_month' => $month_year, 'wfa_msg' => 'wfa_budget_saved' ),
			admin_url( 'admin.php' )
		)
	);
	exit;
}
add_action( 'admin_post_wfa_set_budget', 'wfa_handle_set_budget' );

/**
 * CSV export for one month, all active transactions. UTF-8 BOM included
 * so Bangla text opens correctly in Excel (same pattern as the rest of
 * the site's exports).
 */
function wfa_handle_export_csv() {
	wfa_accounts_require_access();
	check_admin_referer( 'wfa_export_csv' );

	$month_year = isset( $_GET['wfa_month'] ) ? sanitize_text_field( wp_unslash( $_GET['wfa_month'] ) ) : current_time( 'Y-m' );
	if ( ! preg_match( '/^\d{4}-\d{2}$/', $month_year ) ) {
		$month_year = current_time( 'Y-m' );
	}

	global $wpdb;
	$table = wfa_accounts_transactions_table();
	$rows  = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$table} WHERE month_year = %s AND status = 'active' ORDER BY entry_date ASC, id ASC",
			$month_year
		)
	);

	$types = wfa_accounts_types();

	header( 'Content-Type: text/csv; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=wf-accounts-' . $month_year . '.csv' );

	echo "\xEF\xBB\xBF";
	$out = fopen( 'php://output', 'w' );
	fputcsv( $out, array( 'Date', 'Type', 'Category', 'Amount', 'Payment Method', 'Note', 'Created By', 'Created At' ) );
	foreach ( $rows as $row ) {
		$user = get_userdata( $row->created_by );
		fputcsv(
			$out,
			array(
				$row->entry_date,
				isset( $types[ $row->type ] ) ? $types[ $row->type ] : $row->type,
				$row->category,
				$row->amount,
				$row->payment_method,
				$row->note,
				$user ? $user->display_name : '',
				$row->created_at,
			)
		);
	}
	fclose( $out );
	exit;
}
add_action( 'admin_post_wfa_export_csv', 'wfa_handle_export_csv' );
