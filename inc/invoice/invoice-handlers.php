<?php
/**
 * WF-Invoice — form handlers (admin-post.php actions). Plain POST
 * forms + redirects, not AJAX — keeps the module simple and matches
 * the rest of the site's non-SPA form handling.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wfi_guard() {
	if ( ! wfi_user_can_access() ) {
		wp_die( 'অনুমতি নেই।' );
	}
}

function wfi_page_url( $view = 'dashboard', $extra = array() ) {
	$page = get_page_by_path( 'wf-invoice' );
	$base = $page ? get_permalink( $page ) : home_url( '/wf-invoice/' );
	return add_query_arg( array_merge( array( 'v' => $view ), $extra ), $base );
}

function wfi_redirect_back( $view, $extra = array() ) {
	wp_safe_redirect( wfi_page_url( $view, $extra ) );
	exit;
}

function wfi_handle_logo_upload( $field ) {
	if ( empty( $_FILES[ $field ] ) || empty( $_FILES[ $field ]['name'] ) ) {
		return '';
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$allowed = array( 'jpg', 'jpeg', 'png', 'gif', 'webp' );
	$ext     = strtolower( pathinfo( $_FILES[ $field ]['name'], PATHINFO_EXTENSION ) );
	if ( ! in_array( $ext, $allowed, true ) ) {
		return '';
	}

	$attachment_id = media_handle_upload( $field, 0 );
	if ( is_wp_error( $attachment_id ) ) {
		return '';
	}
	return wp_get_attachment_url( $attachment_id );
}

/* ---------------------------------------------------------------------
 * Client: save (create or update)
 * ------------------------------------------------------------------- */
function wfi_handle_save_client() {
	wfi_guard();
	if ( ! isset( $_POST['wfi_client_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wfi_client_nonce'] ), 'wfi_save_client' ) ) {
		wp_die( 'Security check failed।' );
	}

	global $wpdb;
	$client_id = isset( $_POST['client_id'] ) ? absint( $_POST['client_id'] ) : 0;

	$data = array(
		'business_name'  => isset( $_POST['business_name'] ) ? sanitize_text_field( wp_unslash( $_POST['business_name'] ) ) : '',
		'contact_person' => isset( $_POST['contact_person'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_person'] ) ) : '',
		'mobile'         => isset( $_POST['mobile'] ) ? sanitize_text_field( wp_unslash( $_POST['mobile'] ) ) : '',
		'whatsapp'       => isset( $_POST['whatsapp'] ) ? sanitize_text_field( wp_unslash( $_POST['whatsapp'] ) ) : '',
		'email'          => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
		'address'        => isset( $_POST['address'] ) ? sanitize_textarea_field( wp_unslash( $_POST['address'] ) ) : '',
		'website'        => isset( $_POST['website'] ) ? esc_url_raw( wp_unslash( $_POST['website'] ) ) : '',
		'facebook_url'   => isset( $_POST['facebook_url'] ) ? esc_url_raw( wp_unslash( $_POST['facebook_url'] ) ) : '',
		'notes'          => isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '',
	);

	$logo_url = wfi_handle_logo_upload( 'logo' );

	if ( $client_id ) {
		if ( $logo_url ) {
			$data['logo_url'] = $logo_url;
		}
		$data['updated_at'] = current_time( 'mysql' );
		$formats = array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' );
		if ( $logo_url ) {
			$formats[] = '%s';
		}
		$formats[] = '%s';
		$wpdb->update( wfi_clients_table(), $data, array( 'id' => $client_id ), $formats, array( '%d' ) );
	} else {
		$data['client_code'] = wfi_next_client_code();
		$data['logo_url']    = $logo_url;
		$data['created_at']  = current_time( 'mysql' );
		$wpdb->insert(
			wfi_clients_table(),
			$data,
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
		);
		$client_id = $wpdb->insert_id;
	}

	wfi_redirect_back( 'client', array( 'id' => $client_id ) );
}
add_action( 'admin_post_wfi_save_client', 'wfi_handle_save_client' );

function wfi_handle_delete_client() {
	wfi_guard();
	if ( ! wfi_user_can_delete() ) {
		wp_die( 'অনুমতি নেই।' );
	}
	$client_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'wfi_delete_client_' . $client_id ) ) {
		wp_die( 'Security check failed।' );
	}
	global $wpdb;
	$has_invoices = (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . wfi_invoices_table() . ' WHERE client_id = %d', $client_id ) );
	if ( $has_invoices > 0 ) {
		wfi_redirect_back( 'clients', array( 'err' => 'has_invoices' ) );
	}
	$wpdb->delete( wfi_clients_table(), array( 'id' => $client_id ), array( '%d' ) );
	wfi_redirect_back( 'clients' );
}
add_action( 'admin_post_wfi_delete_client', 'wfi_handle_delete_client' );

/* ---------------------------------------------------------------------
 * Invoice: save (create or update, with items)
 * ------------------------------------------------------------------- */
function wfi_handle_save_invoice() {
	wfi_guard();
	if ( ! isset( $_POST['wfi_invoice_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wfi_invoice_nonce'] ), 'wfi_save_invoice' ) ) {
		wp_die( 'Security check failed।' );
	}

	global $wpdb;
	$invoice_id = isset( $_POST['invoice_id'] ) ? absint( $_POST['invoice_id'] ) : 0;
	$client_id  = isset( $_POST['client_id'] ) ? absint( $_POST['client_id'] ) : 0;
	if ( ! $client_id ) {
		wfi_redirect_back( 'create-invoice', array( 'err' => 'no_client' ) );
	}

	$service_names = isset( $_POST['item_name'] ) ? (array) wp_unslash( $_POST['item_name'] ) : array();
	$descriptions  = isset( $_POST['item_desc'] ) ? (array) wp_unslash( $_POST['item_desc'] ) : array();
	$quantities    = isset( $_POST['item_qty'] ) ? (array) wp_unslash( $_POST['item_qty'] ) : array();
	$unit_prices   = isset( $_POST['item_price'] ) ? (array) wp_unslash( $_POST['item_price'] ) : array();

	$items = array();
	$subtotal = 0;
	foreach ( $service_names as $i => $name ) {
		$name = sanitize_text_field( $name );
		if ( '' === $name ) {
			continue;
		}
		$qty    = isset( $quantities[ $i ] ) ? (float) $quantities[ $i ] : 0;
		$price  = isset( $unit_prices[ $i ] ) ? (float) $unit_prices[ $i ] : 0;
		$amount = round( $qty * $price, 2 );
		$subtotal += $amount;
		$items[] = array(
			'service_name' => $name,
			'description'  => isset( $descriptions[ $i ] ) ? sanitize_textarea_field( $descriptions[ $i ] ) : '',
			'quantity'     => $qty,
			'unit_price'   => $price,
			'amount'       => $amount,
			'sort_order'   => $i,
		);
	}

	if ( empty( $items ) ) {
		wfi_redirect_back( 'create-invoice', array( 'err' => 'no_items' ) );
	}

	$discount     = isset( $_POST['discount'] ) ? (float) $_POST['discount'] : 0;
	$tax          = isset( $_POST['tax'] ) ? (float) $_POST['tax'] : 0;
	$other_charge = isset( $_POST['other_charge'] ) ? (float) $_POST['other_charge'] : 0;
	$grand_total  = round( $subtotal - $discount + $tax + $other_charge, 2 );
	$paid_amount  = isset( $_POST['paid_amount'] ) ? (float) $_POST['paid_amount'] : 0;
	$due_amount   = round( $grand_total - $paid_amount, 2 );

	if ( $due_amount <= 0 ) {
		$status = 'paid';
		$due_amount = 0;
	} elseif ( $paid_amount > 0 ) {
		$status = 'partial';
	} else {
		$status = 'due';
	}

	$invoice_data = array(
		'client_id'        => $client_id,
		'invoice_date'     => isset( $_POST['invoice_date'] ) ? sanitize_text_field( wp_unslash( $_POST['invoice_date'] ) ) : current_time( 'Y-m-d' ),
		'due_date'         => isset( $_POST['due_date'] ) && $_POST['due_date'] ? sanitize_text_field( wp_unslash( $_POST['due_date'] ) ) : null,
		'reference_number' => isset( $_POST['reference_number'] ) ? sanitize_text_field( wp_unslash( $_POST['reference_number'] ) ) : '',
		'subtotal'         => $subtotal,
		'discount'         => $discount,
		'tax'              => $tax,
		'other_charge'     => $other_charge,
		'grand_total'      => $grand_total,
		'paid_amount'      => $paid_amount,
		'due_amount'       => $due_amount,
		'status'           => $status,
		'payment_method'   => isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : '',
		'notes'            => isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '',
		'terms'            => isset( $_POST['terms'] ) ? sanitize_textarea_field( wp_unslash( $_POST['terms'] ) ) : '',
	);

	if ( $invoice_id ) {
		$invoice_data['updated_at'] = current_time( 'mysql' );
		$wpdb->update(
			wfi_invoices_table(),
			$invoice_data,
			array( 'id' => $invoice_id ),
			array( '%d', '%s', '%s', '%s', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%s', '%s', '%s', '%s', '%s' ),
			array( '%d' )
		);
		$wpdb->delete( wfi_invoice_items_table(), array( 'invoice_id' => $invoice_id ), array( '%d' ) );
	} else {
		$invoice_data['invoice_number'] = wfi_next_invoice_number();
		$invoice_data['created_by']     = get_current_user_id();
		$invoice_data['created_at']     = current_time( 'mysql' );
		$wpdb->insert(
			wfi_invoices_table(),
			$invoice_data,
			array( '%d', '%s', '%s', '%s', '%f', '%f', '%f', '%f', '%f', '%f', '%f', '%s', '%s', '%s', '%s', '%s', '%d', '%s' )
		);
		$invoice_id = $wpdb->insert_id;
	}

	foreach ( $items as $item ) {
		$item['invoice_id'] = $invoice_id;
		$item['created_at'] = current_time( 'mysql' );
		$wpdb->insert(
			wfi_invoice_items_table(),
			$item,
			array( '%s', '%s', '%f', '%f', '%f', '%d', '%d', '%s' )
		);
	}

	/* If an initial paid amount was entered on a brand-new invoice,
	   record it as the first payment too, so Payment History stays
	   consistent with the invoice's paid_amount from day one. */
	if ( $paid_amount > 0 && empty( $_POST['invoice_id'] ) ) {
		$wpdb->insert(
			wfi_payments_table(),
			array(
				'payment_number' => wfi_next_payment_number(),
				'invoice_id'     => $invoice_id,
				'client_id'      => $client_id,
				'amount'         => $paid_amount,
				'payment_method' => $invoice_data['payment_method'],
				'transaction_id' => '',
				'payment_date'   => $invoice_data['invoice_date'],
				'received_by'    => get_current_user_id(),
				'notes'          => 'Initial payment at invoice creation',
				'created_at'     => current_time( 'mysql' ),
			),
			array( '%s', '%d', '%d', '%f', '%s', '%s', '%s', '%d', '%s', '%s' )
		);
	}

	wfi_redirect_back( 'invoice', array( 'id' => $invoice_id ) );
}
add_action( 'admin_post_wfi_save_invoice', 'wfi_handle_save_invoice' );

function wfi_handle_cancel_invoice() {
	wfi_guard();
	$invoice_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'wfi_cancel_invoice_' . $invoice_id ) ) {
		wp_die( 'Security check failed।' );
	}
	global $wpdb;
	$wpdb->update(
		wfi_invoices_table(),
		array( 'status' => 'cancelled', 'updated_at' => current_time( 'mysql' ) ),
		array( 'id' => $invoice_id ),
		array( '%s', '%s' ),
		array( '%d' )
	);
	wfi_redirect_back( 'invoice', array( 'id' => $invoice_id ) );
}
add_action( 'admin_post_wfi_cancel_invoice', 'wfi_handle_cancel_invoice' );

/* ---------------------------------------------------------------------
 * Payment: record a new payment against an invoice
 * ------------------------------------------------------------------- */
function wfi_handle_add_payment() {
	wfi_guard();
	if ( ! isset( $_POST['wfi_payment_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wfi_payment_nonce'] ), 'wfi_add_payment' ) ) {
		wp_die( 'Security check failed।' );
	}
	global $wpdb;
	$invoice_id = isset( $_POST['invoice_id'] ) ? absint( $_POST['invoice_id'] ) : 0;
	$invoice    = wfi_get_invoice( $invoice_id );
	if ( ! $invoice ) {
		wp_die( 'Invoice পাওয়া যায়নি।' );
	}

	$amount = isset( $_POST['amount'] ) ? (float) $_POST['amount'] : 0;
	if ( $amount <= 0 ) {
		wfi_redirect_back( 'invoice', array( 'id' => $invoice_id, 'err' => 'bad_amount' ) );
	}

	$wpdb->insert(
		wfi_payments_table(),
		array(
			'payment_number' => wfi_next_payment_number(),
			'invoice_id'     => $invoice_id,
			'client_id'      => $invoice->client_id,
			'amount'         => $amount,
			'payment_method' => isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : '',
			'transaction_id' => isset( $_POST['transaction_id'] ) ? sanitize_text_field( wp_unslash( $_POST['transaction_id'] ) ) : '',
			'payment_date'   => isset( $_POST['payment_date'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_date'] ) ) : current_time( 'Y-m-d' ),
			'received_by'    => get_current_user_id(),
			'notes'          => isset( $_POST['notes'] ) ? sanitize_textarea_field( wp_unslash( $_POST['notes'] ) ) : '',
			'created_at'     => current_time( 'mysql' ),
		),
		array( '%s', '%d', '%d', '%f', '%s', '%s', '%s', '%d', '%s', '%s' )
	);

	$new_paid = round( $invoice->paid_amount + $amount, 2 );
	$wpdb->update(
		wfi_invoices_table(),
		array( 'paid_amount' => $new_paid ),
		array( 'id' => $invoice_id ),
		array( '%f' ),
		array( '%d' )
	);
	wfi_recompute_invoice_status( $invoice_id );

	wfi_redirect_back( 'invoice', array( 'id' => $invoice_id ) );
}
add_action( 'admin_post_wfi_add_payment', 'wfi_handle_add_payment' );

/* ---------------------------------------------------------------------
 * Settings
 * ------------------------------------------------------------------- */
function wfi_handle_save_settings() {
	wfi_guard();
	if ( ! current_user_can( 'administrator' ) ) {
		wp_die( 'শুধুমাত্র Administrator Settings পরিবর্তন করতে পারবেন।' );
	}
	if ( ! isset( $_POST['wfi_settings_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['wfi_settings_nonce'] ), 'wfi_save_settings' ) ) {
		wp_die( 'Security check failed।' );
	}

	$fields = array( 'company_name', 'company_address', 'company_mobile', 'company_whatsapp', 'company_email', 'company_website', 'invoice_prefix', 'client_prefix', 'currency_symbol', 'default_terms', 'signature_name', 'payment_info' );
	$data   = array();
	foreach ( $fields as $f ) {
		$data[ $f ] = isset( $_POST[ $f ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $f ] ) ) : '';
	}

	$logo_url = wfi_handle_logo_upload( 'company_logo' );
	if ( $logo_url ) {
		$data['logo_url'] = $logo_url;
	}

	wfi_update_settings( $data );
	wfi_redirect_back( 'settings', array( 'saved' => '1' ) );
}
add_action( 'admin_post_wfi_save_settings', 'wfi_handle_save_settings' );
