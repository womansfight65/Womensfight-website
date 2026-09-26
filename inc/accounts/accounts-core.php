<?php
/**
 * WF Simple Accounts — core: table creation/versioning, constants, and
 * shared calculation helpers. Every number shown in wp-admin is computed
 * here, server-side, straight from the database — never trusted from
 * browser JavaScript.
 *
 * Two dedicated tables, both new, neither touching any existing
 * WordPress or theme table:
 * - {$wpdb->prefix}wfa_transactions   — every Income/Expense/Ads Spend/
 *   Salary entry.
 * - {$wpdb->prefix}wfa_monthly_budget — one row per month holding that
 *   month's planned Ads Budget (a target, not a transaction).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WFA_ACCOUNTS_DB_VERSION', '1.0' );

function wfa_accounts_transactions_table() {
	global $wpdb;
	return $wpdb->prefix . 'wfa_transactions';
}

function wfa_accounts_budget_table() {
	global $wpdb;
	return $wpdb->prefix . 'wfa_monthly_budget';
}

/**
 * Creates (or upgrades) the two tables using dbDelta — safe to run on
 * every admin request; it only ever creates missing tables/columns, it
 * never drops or alters existing data. Gated by a stored version option
 * so the (trivial) dbDelta call only actually runs after a real code
 * change, not on every page load.
 */
function wfa_accounts_maybe_upgrade_db() {
	if ( get_option( 'wfa_accounts_db_version' ) === WFA_ACCOUNTS_DB_VERSION ) {
		return;
	}

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();

	$transactions_table = wfa_accounts_transactions_table();
	$budget_table        = wfa_accounts_budget_table();

	$sql = "CREATE TABLE {$transactions_table} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		entry_date DATE NOT NULL,
		month_year CHAR(7) NOT NULL,
		type VARCHAR(30) NOT NULL,
		category VARCHAR(120) NOT NULL DEFAULT '',
		amount DECIMAL(12,2) NOT NULL DEFAULT 0,
		payment_method VARCHAR(40) NOT NULL DEFAULT '',
		note TEXT NULL,
		status VARCHAR(20) NOT NULL DEFAULT 'active',
		created_by BIGINT UNSIGNED NOT NULL DEFAULT 0,
		created_at DATETIME NOT NULL,
		updated_by BIGINT UNSIGNED NULL,
		updated_at DATETIME NULL,
		PRIMARY KEY  (id),
		KEY month_year (month_year),
		KEY type (type),
		KEY status (status)
	) {$charset_collate};

	CREATE TABLE {$budget_table} (
		month_year CHAR(7) NOT NULL,
		ads_budget DECIMAL(12,2) NOT NULL DEFAULT 0,
		updated_by BIGINT UNSIGNED NULL,
		updated_at DATETIME NULL,
		PRIMARY KEY  (month_year)
	) {$charset_collate};";

	dbDelta( $sql );

	update_option( 'wfa_accounts_db_version', WFA_ACCOUNTS_DB_VERSION );
}
add_action( 'admin_init', 'wfa_accounts_maybe_upgrade_db' );

/**
 * Transaction type => Bangla-friendly label. The single source of truth
 * for the type dropdown, filters, and report grouping.
 */
function wfa_accounts_types() {
	return array(
		'income'          => 'Income',
		'general_expense' => 'General Expense',
		'ads_spend'       => 'Ads Spend',
		'salary'          => 'Salary',
	);
}

function wfa_accounts_payment_methods() {
	return array( 'Cash', 'Bank', 'Mobile Banking (bKash/Nagad)', 'Other' );
}

/**
 * All server-side monthly totals for one "YYYY-MM" month, used by the
 * Overview and Monthly Report screens. Every figure is a real SUM()
 * query against active (non-trashed) rows — this is the one place the
 * headline calculations exist, so Overview and Report can never disagree.
 */
function wfa_accounts_get_monthly_totals( $month_year ) {
	global $wpdb;
	$table = wfa_accounts_transactions_table();

	$sums = array();
	foreach ( array_keys( wfa_accounts_types() ) as $type ) {
		$sums[ $type ] = (float) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COALESCE(SUM(amount),0) FROM {$table} WHERE type = %s AND month_year = %s AND status = 'active'",
				$type,
				$month_year
			)
		);
	}

	$total_expense = $sums['ads_spend'] + $sums['general_expense'] + $sums['salary'];
	$net_profit    = $sums['income'] - $total_expense;
	$ads_budget    = wfa_accounts_get_monthly_budget( $month_year );
	$remaining     = $ads_budget - $sums['ads_spend'];

	return array(
		'income'          => $sums['income'],
		'general_expense' => $sums['general_expense'],
		'ads_spend'       => $sums['ads_spend'],
		'salary'          => $sums['salary'],
		'total_expense'   => $total_expense,
		'net_profit'      => $net_profit,
		'ads_budget'      => $ads_budget,
		'remaining_ads_budget' => $remaining,
	);
}

function wfa_accounts_get_monthly_budget( $month_year ) {
	global $wpdb;
	$table = wfa_accounts_budget_table();
	$value = $wpdb->get_var(
		$wpdb->prepare( "SELECT ads_budget FROM {$table} WHERE month_year = %s", $month_year )
	);
	return null === $value ? 0.0 : (float) $value;
}

function wfa_accounts_set_monthly_budget( $month_year, $amount ) {
	global $wpdb;
	$table = wfa_accounts_budget_table();
	$wpdb->query(
		$wpdb->prepare(
			"INSERT INTO {$table} (month_year, ads_budget, updated_by, updated_at) VALUES (%s, %f, %d, %s)
			 ON DUPLICATE KEY UPDATE ads_budget = VALUES(ads_budget), updated_by = VALUES(updated_by), updated_at = VALUES(updated_at)",
			$month_year,
			$amount,
			get_current_user_id(),
			current_time( 'mysql' )
		)
	);
}

/**
 * Category-wise subtotals for one type within a month — used by the
 * Monthly Report screen's breakdown tables.
 */
function wfa_accounts_get_category_breakdown( $month_year, $type ) {
	global $wpdb;
	$table = wfa_accounts_transactions_table();
	return $wpdb->get_results(
		$wpdb->prepare(
			"SELECT category, SUM(amount) AS subtotal FROM {$table}
			 WHERE type = %s AND month_year = %s AND status = 'active'
			 GROUP BY category ORDER BY subtotal DESC",
			$type,
			$month_year
		)
	);
}

/**
 * Format a number as Bangladeshi Taka for display — server-formatted,
 * plain text, never re-computed client-side.
 */
function wfa_accounts_money( $amount ) {
	return '৳' . number_format( (float) $amount, 2 );
}

/**
 * This file is the single entry point functions.php requires — it in
 * turn loads its two sibling files, so the rest of the theme only ever
 * needs to know about this one file.
 */
require_once __DIR__ . '/accounts-handlers.php';
require_once __DIR__ . '/accounts-admin.php';
