<?php
/**
 * WF-Invoice — HTML rendering for every view of the single-page
 * dashboard shell (page-wf-invoice.php switches between these by
 * ?v=... — plain server-rendered views, no AJAX/SPA, to keep the
 * module simple to maintain).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wfi_status_badge( $status ) {
	$labels = wfi_statuses();
	$label  = isset( $labels[ $status ] ) ? $labels[ $status ] : $status;
	return '<span class="wfi-badge wfi-badge-' . esc_attr( $status ) . '">' . esc_html( $label ) . '</span>';
}

/* ---------------------------------------------------------------------
 * Dashboard
 * ------------------------------------------------------------------- */
function wfi_render_dashboard() {
	$s = wfi_get_dashboard_stats();
	echo '<h1 class="wfi-page-title">Dashboard</h1>';
	echo '<div class="wfi-cards">';
	$cards = array(
		array( 'Total Clients', intval( $s['total_clients'] ), '' ),
		array( 'Total Invoices', intval( $s['total_invoices'] ), '' ),
		array( 'Total Invoice Amount', wfi_money( $s['total_amount'] ), '' ),
		array( 'Total Paid', wfi_money( $s['total_paid'] ), 'good' ),
		array( 'Total Due', wfi_money( $s['total_due'] ), 'warn' ),
		array( 'Paid Invoices', intval( $s['paid_invoices'] ), 'good' ),
		array( 'Partial Invoices', intval( $s['partial_invoices'] ), '' ),
		array( 'Due Invoices', intval( $s['due_invoices'] ), 'warn' ),
	);
	foreach ( $cards as $c ) {
		$cls = $c[2] ? ' wfi-card-' . $c[2] : '';
		echo '<div class="wfi-card' . esc_attr( $cls ) . '"><span>' . esc_html( $c[0] ) . '</span><b>' . $c[1] . '</b></div>';
	}
	echo '</div>';

	echo '<div class="wfi-section"><div class="wfi-section-head"><h2>সাম্প্রতিক Invoice</h2><a class="btn btn-ghost" href="' . esc_url( wfi_page_url( 'invoices' ) ) . '">সব দেখুন</a></div>';
	wfi_render_invoice_table( wfi_query_invoices( array( 'limit' => 10 ) ) );
	echo '</div>';
}

/* ---------------------------------------------------------------------
 * Shared: invoice list table
 * ------------------------------------------------------------------- */
function wfi_render_invoice_table( $invoices ) {
	echo '<div class="wfi-table-wrap"><table class="wfi-table">';
	echo '<thead><tr><th>Invoice No.</th><th>Client</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Action</th></tr></thead><tbody>';
	if ( $invoices ) {
		foreach ( $invoices as $inv ) {
			echo '<tr>';
			echo '<td>' . esc_html( $inv->invoice_number ) . '</td>';
			echo '<td>' . esc_html( $inv->business_name ) . '</td>';
			echo '<td>' . esc_html( $inv->invoice_date ) . '</td>';
			echo '<td>' . esc_html( wfi_money( $inv->grand_total ) ) . '</td>';
			echo '<td>' . esc_html( wfi_money( $inv->paid_amount ) ) . '</td>';
			echo '<td>' . esc_html( wfi_money( $inv->due_amount ) ) . '</td>';
			echo '<td>' . wfi_status_badge( $inv->status ) . '</td>';
			echo '<td><a class="btn btn-ghost" href="' . esc_url( wfi_page_url( 'invoice', array( 'id' => $inv->id ) ) ) . '">দেখুন</a></td>';
			echo '</tr>';
		}
	} else {
		echo '<tr><td colspan="8">কোনো Invoice পাওয়া যায়নি।</td></tr>';
	}
	echo '</tbody></table></div>';
}

/* ---------------------------------------------------------------------
 * Clients: list
 * ------------------------------------------------------------------- */
function wfi_render_clients_list() {
	$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$clients = wfi_query_clients( $search );

	echo '<div class="wfi-section-head"><h1 class="wfi-page-title">Clients</h1><a class="btn btn-primary" href="' . esc_url( wfi_page_url( 'client' ) ) . '">+ নতুন Client</a></div>';

	echo '<form method="get" class="wfi-filters"><input type="hidden" name="v" value="clients">';
	echo '<input type="text" name="s" value="' . esc_attr( $search ) . '" placeholder="নাম, মোবাইল বা Client ID খুঁজুন">';
	echo '<button type="submit" class="btn btn-ghost">খুঁজুন</button></form>';

	echo '<div class="wfi-table-wrap"><table class="wfi-table"><thead><tr><th>Client ID</th><th>Business Name</th><th>Mobile</th><th>Email</th><th>Action</th></tr></thead><tbody>';
	if ( $clients ) {
		foreach ( $clients as $c ) {
			echo '<tr>';
			echo '<td>' . esc_html( $c->client_code ) . '</td>';
			echo '<td>' . esc_html( $c->business_name ) . '</td>';
			echo '<td>' . esc_html( $c->mobile ) . '</td>';
			echo '<td>' . esc_html( $c->email ) . '</td>';
			echo '<td><a class="btn btn-ghost" href="' . esc_url( wfi_page_url( 'client', array( 'id' => $c->id ) ) ) . '">প্রোফাইল</a></td>';
			echo '</tr>';
		}
	} else {
		echo '<tr><td colspan="5">কোনো Client পাওয়া যায়নি।</td></tr>';
	}
	echo '</tbody></table></div>';
}

/* ---------------------------------------------------------------------
 * Client: add/edit form + profile (one view, "client")
 * ------------------------------------------------------------------- */
function wfi_render_client_view() {
	$id     = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	$client = $id ? wfi_get_client( $id ) : null;

	echo '<h1 class="wfi-page-title">' . ( $client ? esc_html( $client->business_name ) : 'নতুন Client' ) . '</h1>';

	if ( $client ) {
		$invoices = wfi_get_client_invoices( $id );
		$total_billed = 0; $total_paid = 0; $total_due = 0; $last_payment = '';
		foreach ( $invoices as $inv ) {
			if ( 'cancelled' === $inv->status ) { continue; }
			$total_billed += $inv->grand_total;
			$total_paid   += $inv->paid_amount;
			$total_due    += $inv->due_amount;
		}
		$payments = wfi_get_client_payments( $id );
		if ( $payments ) { $last_payment = $payments[0]->payment_date; }

		echo '<div class="wfi-client-profile">';
		if ( $client->logo_url ) {
			echo '<img class="wfi-client-logo" src="' . esc_url( $client->logo_url ) . '" alt="">';
		}
		echo '<div><h2>' . esc_html( $client->business_name ) . '</h2><p class="wfi-hint">' . esc_html( $client->client_code ) . ' · ' . esc_html( $client->contact_person ) . '</p>';
		echo '<p class="wfi-hint">' . esc_html( $client->mobile ) . ' · ' . esc_html( $client->email ) . '</p></div>';
		echo '</div>';

		echo '<div class="wfi-cards">';
		echo '<div class="wfi-card"><span>Total Invoices</span><b>' . count( $invoices ) . '</b></div>';
		echo '<div class="wfi-card"><span>Total Billed</span><b>' . esc_html( wfi_money( $total_billed ) ) . '</b></div>';
		echo '<div class="wfi-card wfi-card-good"><span>Total Paid</span><b>' . esc_html( wfi_money( $total_paid ) ) . '</b></div>';
		echo '<div class="wfi-card wfi-card-warn"><span>Total Due</span><b>' . esc_html( wfi_money( $total_due ) ) . '</b></div>';
		echo '<div class="wfi-card"><span>Last Payment</span><b>' . ( $last_payment ? esc_html( $last_payment ) : '—' ) . '</b></div>';
		echo '</div>';

		echo '<div class="wfi-section"><h2>Invoice History</h2>';
		wfi_render_invoice_table( array_map( function( $inv ) use ( $client ) { $inv->business_name = $client->business_name; return $inv; }, $invoices ) );
		echo '</div>';
	}

	echo '<div class="wfi-section"><h2>' . ( $client ? 'তথ্য Edit করুন' : 'Client তথ্য' ) . '</h2>';
	echo '<form method="post" enctype="multipart/form-data" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="wfi-form">';
	echo '<input type="hidden" name="action" value="wfi_save_client">';
	if ( $client ) {
		echo '<input type="hidden" name="client_id" value="' . esc_attr( $client->id ) . '">';
	}
	wp_nonce_field( 'wfi_save_client', 'wfi_client_nonce' );

	$v = function( $field ) use ( $client ) { return $client ? $client->$field : ''; };
	echo '<div class="wfi-grid2">';
	echo '<div><label>Business Name *</label><input type="text" name="business_name" required value="' . esc_attr( $v( 'business_name' ) ) . '"></div>';
	echo '<div><label>Contact Person</label><input type="text" name="contact_person" value="' . esc_attr( $v( 'contact_person' ) ) . '"></div>';
	echo '<div><label>Mobile Number *</label><input type="text" name="mobile" required value="' . esc_attr( $v( 'mobile' ) ) . '"></div>';
	echo '<div><label>WhatsApp Number</label><input type="text" name="whatsapp" value="' . esc_attr( $v( 'whatsapp' ) ) . '"></div>';
	echo '<div><label>Email</label><input type="email" name="email" value="' . esc_attr( $v( 'email' ) ) . '"></div>';
	echo '<div><label>Website</label><input type="url" name="website" value="' . esc_attr( $v( 'website' ) ) . '"></div>';
	echo '<div><label>Facebook Page URL</label><input type="url" name="facebook_url" value="' . esc_attr( $v( 'facebook_url' ) ) . '"></div>';
	echo '<div><label>Client Logo</label><input type="file" name="logo" accept="image/*"></div>';
	echo '</div>';
	echo '<div><label>Address</label><textarea name="address">' . esc_textarea( $v( 'address' ) ) . '</textarea></div>';
	echo '<div><label>Notes</label><textarea name="notes">' . esc_textarea( $v( 'notes' ) ) . '</textarea></div>';
	echo '<button type="submit" class="btn btn-primary">Client Save করুন</button>';
	echo '</form></div>';
}

/* ---------------------------------------------------------------------
 * Create Invoice
 * ------------------------------------------------------------------- */
function wfi_render_create_invoice() {
	$settings = wfi_get_settings();
	$clients  = wfi_query_clients();

	$edit_id = isset( $_GET['edit'] ) ? absint( $_GET['edit'] ) : 0;
	$inv     = $edit_id ? wfi_get_invoice( $edit_id ) : null;
	$items   = $inv ? wfi_get_invoice_items( $edit_id ) : array();

	echo '<h1 class="wfi-page-title">' . ( $inv ? 'Edit Invoice — ' . esc_html( $inv->invoice_number ) : 'Create Invoice' ) . '</h1>';

	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="wfi-form" id="wfi-invoice-form">';
	echo '<input type="hidden" name="action" value="wfi_save_invoice">';
	if ( $inv ) {
		echo '<input type="hidden" name="invoice_id" value="' . esc_attr( $inv->id ) . '">';
	}
	wp_nonce_field( 'wfi_save_invoice', 'wfi_invoice_nonce' );

	echo '<div class="wfi-section"><h2>Client</h2>';
	echo '<div class="wfi-grid2">';
	echo '<div><label>Client সিলেক্ট করুন *</label><select name="client_id" required>';
	echo '<option value="">সিলেক্ট করুন</option>';
	foreach ( $clients as $c ) {
		$sel = $inv && (int) $inv->client_id === (int) $c->id ? ' selected' : '';
		echo '<option value="' . esc_attr( $c->id ) . '"' . $sel . '>' . esc_html( $c->business_name . ' — ' . $c->mobile ) . '</option>';
	}
	echo '</select></div>';
	echo '<div><label>নতুন Client?</label><a class="btn btn-ghost" href="' . esc_url( wfi_page_url( 'client' ) ) . '">+ নতুন Client তৈরি করুন</a></div>';
	echo '</div></div>';

	echo '<div class="wfi-section"><h2>Invoice Information</h2><div class="wfi-grid2">';
	echo '<div><label>Invoice Date</label><input type="date" name="invoice_date" value="' . esc_attr( $inv ? $inv->invoice_date : current_time( 'Y-m-d' ) ) . '"></div>';
	echo '<div><label>Due Date</label><input type="date" name="due_date" value="' . esc_attr( $inv ? $inv->due_date : '' ) . '"></div>';
	echo '<div><label>Reference Number</label><input type="text" name="reference_number" value="' . esc_attr( $inv ? $inv->reference_number : '' ) . '"></div>';
	echo '<div><label>Created By</label><input type="text" value="' . esc_attr( wp_get_current_user()->display_name ) . '" disabled></div>';
	echo '</div></div>';

	echo '<div class="wfi-section"><h2>Invoice Items</h2>';
	echo '<div class="wfi-items-wrap"><table class="wfi-table wfi-items-table" id="wfi-items-table">';
	echo '<thead><tr><th>Service/Product</th><th>Description</th><th>Qty</th><th>Unit Price</th><th>Amount</th><th></th></tr></thead>';
	echo '<tbody id="wfi-items-body"></tbody></table></div>';
	echo '<button type="button" class="btn btn-ghost" id="wfi-add-row">+ Add Row</button>';
	echo '</div>';

	echo '<div class="wfi-section wfi-totals-section"><h2>Calculation</h2>';
	echo '<div class="wfi-totals">';
	echo '<div class="wfi-totals-row"><span>Subtotal</span><b id="wfi-t-subtotal">0.00</b></div>';
	echo '<div class="wfi-totals-row"><label>Discount</label><input type="number" step="0.01" name="discount" id="wfi-discount" value="' . esc_attr( $inv ? $inv->discount : 0 ) . '"></div>';
	echo '<div class="wfi-totals-row"><label>Tax</label><input type="number" step="0.01" name="tax" id="wfi-tax" value="' . esc_attr( $inv ? $inv->tax : 0 ) . '"></div>';
	echo '<div class="wfi-totals-row"><label>Other Charge</label><input type="number" step="0.01" name="other_charge" id="wfi-other" value="' . esc_attr( $inv ? $inv->other_charge : 0 ) . '"></div>';
	echo '<div class="wfi-totals-row wfi-grand"><span>Grand Total</span><b id="wfi-t-grand">0.00</b></div>';
	echo '<div class="wfi-totals-row"><label>Paid Amount</label><input type="number" step="0.01" name="paid_amount" id="wfi-paid" value="' . esc_attr( $inv ? $inv->paid_amount : 0 ) . '"></div>';
	echo '<div class="wfi-totals-row wfi-due"><span>Due Amount</span><b id="wfi-t-due">0.00</b></div>';
	echo '</div></div>';

	echo '<div class="wfi-section"><h2>Payment Information</h2><div class="wfi-grid2">';
	echo '<div><label>Payment Method</label><select name="payment_method"><option value="">সিলেক্ট করুন</option>';
	foreach ( wfi_payment_methods() as $m ) {
		$sel = $inv && $inv->payment_method === $m ? ' selected' : '';
		echo '<option' . $sel . '>' . esc_html( $m ) . '</option>';
	}
	echo '</select></div>';
	echo '</div></div>';

	echo '<div class="wfi-section"><h2>Notes & Terms</h2>';
	echo '<div><label>Notes</label><textarea name="notes">' . esc_textarea( $inv ? $inv->notes : '' ) . '</textarea></div>';
	echo '<div><label>Terms & Conditions</label><textarea name="terms">' . esc_textarea( $inv ? $inv->terms : $settings['default_terms'] ) . '</textarea></div>';
	echo '</div>';

	echo '<button type="submit" class="btn btn-primary btn-lg">Invoice Save করুন</button>';
	echo '</form>';

	if ( $items ) {
		$js_items = array();
		foreach ( $items as $it ) {
			$js_items[] = array(
				'name'  => $it->service_name,
				'desc'  => $it->description,
				'qty'   => $it->quantity,
				'price' => $it->unit_price,
			);
		}
		echo '<script>window.wfiExistingItems = ' . wp_json_encode( $js_items ) . ';</script>';
	}

	/* Template row for JS to clone — kept outside the <form> flow, in a
	   <template> tag so it never submits by accident. */
	echo '<template id="wfi-row-template"><tr class="wfi-item-row">';
	echo '<td><input type="text" name="item_name[]" class="wfi-i-name" required></td>';
	echo '<td><input type="text" name="item_desc[]" class="wfi-i-desc"></td>';
	echo '<td><input type="number" step="0.01" name="item_qty[]" class="wfi-i-qty" value="1"></td>';
	echo '<td><input type="number" step="0.01" name="item_price[]" class="wfi-i-price" value="0"></td>';
	echo '<td class="wfi-i-amount">0.00</td>';
	echo '<td class="wfi-i-actions"><button type="button" class="wfi-row-dup" title="Duplicate">⧉</button><button type="button" class="wfi-row-del" title="Delete">✕</button></td>';
	echo '</tr></template>';
}

/* ---------------------------------------------------------------------
 * Invoice view + printable layout
 * ------------------------------------------------------------------- */
function wfi_render_invoice_view() {
	$id  = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
	$inv = wfi_get_invoice( $id );
	if ( ! $inv ) {
		echo '<p>Invoice পাওয়া যায়নি।</p>';
		return;
	}
	$client   = wfi_get_client( $inv->client_id );
	$items    = wfi_get_invoice_items( $id );
	$payments = wfi_get_invoice_payments( $id );
	$settings = wfi_get_settings();

	echo '<div class="wfi-section-head wfi-no-print"><h1 class="wfi-page-title">' . esc_html( $inv->invoice_number ) . ' ' . wfi_status_badge( $inv->status ) . '</h1>';
	echo '<div class="wfi-actions">';
	echo '<a class="btn btn-ghost" href="' . esc_url( wfi_page_url( 'print', array( 'id' => $id ) ) ) . '" target="_blank">Preview / Print / PDF</a>';
	if ( 'cancelled' !== $inv->status ) {
		echo '<a class="btn btn-ghost" href="' . esc_url( wfi_page_url( 'create-invoice', array( 'edit' => $id ) ) ) . '">Edit</a>';
		if ( wfi_user_can_delete() ) {
			echo '<a class="btn btn-ghost" onclick="return confirm(\'Invoice বাতিল করবেন?\')" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=wfi_cancel_invoice&id=' . $id ), 'wfi_cancel_invoice_' . $id ) ) . '">Cancel Invoice</a>';
		}
	}
	echo '</div></div>';

	echo '<div class="wfi-invoice-meta wfi-no-print"><div><span>Client</span><b>' . esc_html( $client ? $client->business_name : '—' ) . '</b></div>';
	echo '<div><span>Date</span><b>' . esc_html( $inv->invoice_date ) . '</b></div>';
	echo '<div><span>Grand Total</span><b>' . esc_html( wfi_money( $inv->grand_total ) ) . '</b></div>';
	echo '<div><span>Paid</span><b>' . esc_html( wfi_money( $inv->paid_amount ) ) . '</b></div>';
	echo '<div><span>Due</span><b class="wfi-due-amt">' . esc_html( wfi_money( $inv->due_amount ) ) . '</b></div></div>';

	if ( 'cancelled' !== $inv->status && $inv->due_amount > 0 ) {
		echo '<div class="wfi-section wfi-no-print"><h2>Payment Update</h2>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="wfi-form wfi-row-form">';
		echo '<input type="hidden" name="action" value="wfi_add_payment"><input type="hidden" name="invoice_id" value="' . esc_attr( $id ) . '">';
		wp_nonce_field( 'wfi_add_payment', 'wfi_payment_nonce' );
		echo '<div><label>Amount</label><input type="number" step="0.01" name="amount" required placeholder="' . esc_attr( $inv->due_amount ) . '"></div>';
		echo '<div><label>Method</label><select name="payment_method">';
		foreach ( wfi_payment_methods() as $m ) { echo '<option>' . esc_html( $m ) . '</option>'; }
		echo '</select></div>';
		echo '<div><label>Transaction ID</label><input type="text" name="transaction_id"></div>';
		echo '<div><label>Date</label><input type="date" name="payment_date" value="' . esc_attr( current_time( 'Y-m-d' ) ) . '"></div>';
		echo '<div><label>Note</label><input type="text" name="notes"></div>';
		echo '<button type="submit" class="btn btn-primary">Payment যোগ করুন</button>';
		echo '</form></div>';
	}

	if ( $payments ) {
		echo '<div class="wfi-section wfi-no-print"><h2>Payment History</h2><div class="wfi-table-wrap"><table class="wfi-table"><thead><tr><th>Payment ID</th><th>Amount</th><th>Method</th><th>Transaction ID</th><th>Date</th></tr></thead><tbody>';
		foreach ( $payments as $p ) {
			echo '<tr><td>' . esc_html( $p->payment_number ) . '</td><td>' . esc_html( wfi_money( $p->amount ) ) . '</td><td>' . esc_html( $p->payment_method ) . '</td><td>' . esc_html( $p->transaction_id ) . '</td><td>' . esc_html( $p->payment_date ) . '</td></tr>';
		}
		echo '</tbody></table></div></div>';
	}
}

/**
 * The bare printable invoice sheet — reused by both the normal view
 * (embedded for quick glance, hidden with wfi-no-print on real print)
 * and the dedicated ?v=print route which outputs ONLY this (no shell).
 */
function wfi_render_invoice_sheet( $inv, $client, $items, $settings ) {
	echo '<div class="wfi-sheet">';
	echo '<div class="wfi-sheet-head">';
	echo '<div class="wfi-sheet-brand">';
	if ( $settings['logo_url'] ) {
		echo '<img src="' . esc_url( $settings['logo_url'] ) . '" alt="" class="wfi-sheet-logo">';
	}
	echo '<div><h2>' . esc_html( $settings['company_name'] ) . '</h2><p>WF-Invoice</p>';
	echo '<p class="wfi-hint">' . nl2br( esc_html( $settings['company_address'] ) ) . '</p>';
	echo '<p class="wfi-hint">' . esc_html( $settings['company_mobile'] ) . ' · ' . esc_html( $settings['company_email'] ) . '</p>';
	echo '</div></div>';
	echo '<div class="wfi-sheet-invoice-info"><h1>INVOICE</h1>';
	echo '<p><b>' . esc_html( $inv->invoice_number ) . '</b></p>';
	echo '<p>Date: ' . esc_html( $inv->invoice_date ) . '</p>';
	echo '<p>Due: ' . esc_html( $inv->due_date ? $inv->due_date : '—' ) . '</p>';
	echo '<p>Status: ' . wfi_status_badge( $inv->status ) . '</p>';
	echo '</div></div>';

	echo '<div class="wfi-sheet-bill-to"><h3>Bill To</h3>';
	if ( $client ) {
		if ( $client->logo_url ) { echo '<img src="' . esc_url( $client->logo_url ) . '" alt="" class="wfi-sheet-client-logo">'; }
		echo '<p><b>' . esc_html( $client->business_name ) . '</b></p>';
		echo '<p>' . esc_html( $client->mobile ) . '</p>';
		echo '<p>' . esc_html( $client->email ) . '</p>';
		echo '<p>' . esc_html( $client->address ) . '</p>';
	}
	echo '</div>';

	echo '<table class="wfi-sheet-table"><thead><tr><th>SL</th><th>Service/Product</th><th>Description</th><th>Qty</th><th>Unit Price</th><th>Amount</th></tr></thead><tbody>';
	$sl = 1;
	foreach ( $items as $it ) {
		echo '<tr><td>' . intval( $sl++ ) . '</td><td>' . esc_html( $it->service_name ) . '</td><td>' . esc_html( $it->description ) . '</td><td>' . esc_html( $it->quantity ) . '</td><td>' . esc_html( wfi_money( $it->unit_price ) ) . '</td><td>' . esc_html( wfi_money( $it->amount ) ) . '</td></tr>';
	}
	echo '</tbody></table>';

	echo '<div class="wfi-sheet-totals">';
	echo '<div><span>Subtotal</span><b>' . esc_html( wfi_money( $inv->subtotal ) ) . '</b></div>';
	echo '<div><span>Discount</span><b>' . esc_html( wfi_money( $inv->discount ) ) . '</b></div>';
	echo '<div><span>Tax</span><b>' . esc_html( wfi_money( $inv->tax ) ) . '</b></div>';
	echo '<div><span>Other Charges</span><b>' . esc_html( wfi_money( $inv->other_charge ) ) . '</b></div>';
	echo '<div class="wfi-sheet-grand"><span>Grand Total</span><b>' . esc_html( wfi_money( $inv->grand_total ) ) . '</b></div>';
	echo '<div><span>Paid</span><b>' . esc_html( wfi_money( $inv->paid_amount ) ) . '</b></div>';
	echo '<div class="wfi-sheet-due"><span>Due</span><b>' . esc_html( wfi_money( $inv->due_amount ) ) . '</b></div>';
	echo '</div>';

	echo '<div class="wfi-sheet-footer">';
	echo '<div><h4>Payment Information</h4><p>' . nl2br( esc_html( $settings['payment_info'] ) ) . '</p></div>';
	echo '<div><h4>Terms & Conditions</h4><p>' . nl2br( esc_html( $inv->terms ? $inv->terms : $settings['default_terms'] ) ) . '</p></div>';
	if ( $inv->notes ) {
		echo '<div><h4>Notes</h4><p>' . nl2br( esc_html( $inv->notes ) ) . '</p></div>';
	}
	echo '<div class="wfi-sheet-sign"><div class="wfi-sign-box"><span>Authorized Signature</span><p>' . esc_html( $settings['signature_name'] ) . '</p></div>';
	echo '<div class="wfi-sign-box"><span>Client Signature</span></div></div>';
	echo '<p class="wfi-sheet-contact">' . esc_html( $settings['company_website'] ) . ' · ' . esc_html( $settings['company_email'] ) . ' · ' . esc_html( $settings['company_mobile'] ) . '</p>';
	echo '</div>';
	echo '</div>';
}

/* ---------------------------------------------------------------------
 * Invoices (history) with filters
 * ------------------------------------------------------------------- */
function wfi_render_invoices_history() {
	$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
	$status = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '';
	$range  = isset( $_GET['range'] ) ? sanitize_key( wp_unslash( $_GET['range'] ) ) : '';

	$date_from = '';
	$date_to   = '';
	if ( 'today' === $range ) {
		$date_from = $date_to = current_time( 'Y-m-d' );
	} elseif ( 'week' === $range ) {
		$date_from = date( 'Y-m-d', strtotime( '-7 days', current_time( 'timestamp' ) ) );
		$date_to   = current_time( 'Y-m-d' );
	} elseif ( 'month' === $range ) {
		$date_from = current_time( 'Y-m-01' );
		$date_to   = current_time( 'Y-m-d' );
	} elseif ( isset( $_GET['from'] ) && isset( $_GET['to'] ) ) {
		$date_from = sanitize_text_field( wp_unslash( $_GET['from'] ) );
		$date_to   = sanitize_text_field( wp_unslash( $_GET['to'] ) );
	}

	echo '<h1 class="wfi-page-title">Invoice History</h1>';
	echo '<form method="get" class="wfi-filters"><input type="hidden" name="v" value="invoices">';
	echo '<input type="text" name="s" value="' . esc_attr( $search ) . '" placeholder="Invoice No, Client বা মোবাইল খুঁজুন">';
	echo '<select name="status"><option value="">সব Status</option>';
	foreach ( wfi_statuses() as $k => $l ) {
		echo '<option value="' . esc_attr( $k ) . '"' . selected( $status, $k, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select>';
	echo '<select name="range"><option value="">সব সময়</option>';
	foreach ( array( 'today' => 'Today', 'week' => 'This Week', 'month' => 'This Month' ) as $k => $l ) {
		echo '<option value="' . esc_attr( $k ) . '"' . selected( $range, $k, false ) . '>' . esc_html( $l ) . '</option>';
	}
	echo '</select>';
	echo '<button type="submit" class="btn btn-ghost">Filter করুন</button></form>';

	wfi_render_invoice_table( wfi_query_invoices( array( 'search' => $search, 'status' => $status, 'date_from' => $date_from, 'date_to' => $date_to ) ) );
}

/* ---------------------------------------------------------------------
 * Payments (all payments list)
 * ------------------------------------------------------------------- */
function wfi_render_payments_list() {
	global $wpdb;
	echo '<h1 class="wfi-page-title">Payments</h1>';
	$rows = $wpdb->get_results(
		'SELECT p.*, c.business_name, i.invoice_number FROM ' . wfi_payments_table() . ' p
		 LEFT JOIN ' . wfi_clients_table() . ' c ON c.id = p.client_id
		 LEFT JOIN ' . wfi_invoices_table() . ' i ON i.id = p.invoice_id
		 ORDER BY p.id DESC LIMIT 300'
	);
	echo '<div class="wfi-table-wrap"><table class="wfi-table"><thead><tr><th>Payment ID</th><th>Invoice</th><th>Client</th><th>Amount</th><th>Method</th><th>Date</th></tr></thead><tbody>';
	if ( $rows ) {
		foreach ( $rows as $p ) {
			echo '<tr><td>' . esc_html( $p->payment_number ) . '</td><td><a href="' . esc_url( wfi_page_url( 'invoice', array( 'id' => $p->invoice_id ) ) ) . '">' . esc_html( $p->invoice_number ) . '</a></td><td>' . esc_html( $p->business_name ) . '</td><td>' . esc_html( wfi_money( $p->amount ) ) . '</td><td>' . esc_html( $p->payment_method ) . '</td><td>' . esc_html( $p->payment_date ) . '</td></tr>';
		}
	} else {
		echo '<tr><td colspan="6">কোনো Payment পাওয়া যায়নি।</td></tr>';
	}
	echo '</tbody></table></div>';
}

/* ---------------------------------------------------------------------
 * Reports
 * ------------------------------------------------------------------- */
function wfi_render_reports() {
	global $wpdb;
	$table = wfi_invoices_table();
	$today = current_time( 'Y-m-d' );
	$month_start = current_time( 'Y-m-01' );

	echo '<h1 class="wfi-page-title">Reports</h1><div class="wfi-cards">';
	$today_sales = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(grand_total),0) FROM {$table} WHERE invoice_date = %s AND status != 'cancelled'", $today ) );
	$month_sales = (float) $wpdb->get_var( $wpdb->prepare( "SELECT COALESCE(SUM(grand_total),0) FROM {$table} WHERE invoice_date >= %s AND status != 'cancelled'", $month_start ) );
	$collected   = (float) $wpdb->get_var( "SELECT COALESCE(SUM(paid_amount),0) FROM {$table} WHERE status != 'cancelled'" );
	$due         = (float) $wpdb->get_var( "SELECT COALESCE(SUM(due_amount),0) FROM {$table} WHERE status != 'cancelled'" );
	$clients_n   = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . wfi_clients_table() );
	$invoices_n  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status != 'cancelled'" );

	foreach ( array(
		array( "Today's Sales", wfi_money( $today_sales ) ),
		array( 'Monthly Sales', wfi_money( $month_sales ) ),
		array( 'Total Collected', wfi_money( $collected ) ),
		array( 'Total Due', wfi_money( $due ) ),
		array( 'Clients', $clients_n ),
		array( 'Invoices', $invoices_n ),
	) as $c ) {
		echo '<div class="wfi-card"><span>' . esc_html( $c[0] ) . '</span><b>' . $c[1] . '</b></div>';
	}
	echo '</div>';

	echo '<div class="wfi-section"><h2>Payment Method Breakdown</h2>';
	$methods = $wpdb->get_results( 'SELECT payment_method, COUNT(*) AS cnt, SUM(amount) AS total FROM ' . wfi_payments_table() . ' GROUP BY payment_method ORDER BY total DESC' );
	echo '<div class="wfi-table-wrap"><table class="wfi-table"><thead><tr><th>Method</th><th>Count</th><th>Total</th></tr></thead><tbody>';
	if ( $methods ) {
		foreach ( $methods as $m ) {
			echo '<tr><td>' . esc_html( $m->payment_method ? $m->payment_method : '—' ) . '</td><td>' . intval( $m->cnt ) . '</td><td>' . esc_html( wfi_money( $m->total ) ) . '</td></tr>';
		}
	} else {
		echo '<tr><td colspan="3">কোনো Payment নেই।</td></tr>';
	}
	echo '</tbody></table></div></div>';

	echo '<div class="wfi-section"><h2>Client-wise Revenue</h2>';
	$client_rev = $wpdb->get_results(
		'SELECT c.business_name, COUNT(i.id) AS inv_count, SUM(i.grand_total) AS total, SUM(i.paid_amount) AS paid, SUM(i.due_amount) AS due
		 FROM ' . wfi_clients_table() . ' c LEFT JOIN ' . wfi_invoices_table() . " i ON i.client_id = c.id AND i.status != 'cancelled'
		 GROUP BY c.id ORDER BY total DESC LIMIT 50"
	);
	echo '<div class="wfi-table-wrap"><table class="wfi-table"><thead><tr><th>Client</th><th>Invoices</th><th>Total</th><th>Paid</th><th>Due</th></tr></thead><tbody>';
	if ( $client_rev ) {
		foreach ( $client_rev as $r ) {
			echo '<tr><td>' . esc_html( $r->business_name ) . '</td><td>' . intval( $r->inv_count ) . '</td><td>' . esc_html( wfi_money( $r->total ) ) . '</td><td>' . esc_html( wfi_money( $r->paid ) ) . '</td><td>' . esc_html( wfi_money( $r->due ) ) . '</td></tr>';
		}
	} else {
		echo '<tr><td colspan="5">কোনো ডাটা নেই।</td></tr>';
	}
	echo '</tbody></table></div></div>';
}

/* ---------------------------------------------------------------------
 * Settings
 * ------------------------------------------------------------------- */
function wfi_render_settings() {
	$s = wfi_get_settings();
	echo '<h1 class="wfi-page-title">Settings</h1>';

	if ( ! current_user_can( 'administrator' ) ) {
		echo '<p class="wfi-hint">শুধুমাত্র Administrator Settings পরিবর্তন করতে পারবেন।</p>';
		return;
	}

	if ( isset( $_GET['saved'] ) ) {
		echo '<div class="wfi-notice">Settings সেভ হয়েছে।</div>';
	}

	echo '<form method="post" enctype="multipart/form-data" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" class="wfi-form">';
	echo '<input type="hidden" name="action" value="wfi_save_settings">';
	wp_nonce_field( 'wfi_save_settings', 'wfi_settings_nonce' );

	echo '<div class="wfi-grid2">';
	$fields = array(
		'company_name'     => 'Company Name',
		'company_mobile'   => 'Mobile',
		'company_whatsapp' => 'WhatsApp',
		'company_email'    => 'Email',
		'company_website'  => 'Website',
		'invoice_prefix'   => 'Invoice Prefix',
		'client_prefix'    => 'Client Prefix',
		'currency_symbol'  => 'Currency Symbol',
		'signature_name'   => 'Authorized Signature Name',
	);
	foreach ( $fields as $key => $label ) {
		echo '<div><label>' . esc_html( $label ) . '</label><input type="text" name="' . esc_attr( $key ) . '" value="' . esc_attr( $s[ $key ] ) . '"></div>';
	}
	echo '<div><label>Company Logo</label><input type="file" name="company_logo" accept="image/*"></div>';
	echo '</div>';

	echo '<div><label>Company Address</label><textarea name="company_address">' . esc_textarea( $s['company_address'] ) . '</textarea></div>';
	echo '<div><label>Default Terms & Conditions</label><textarea name="default_terms">' . esc_textarea( $s['default_terms'] ) . '</textarea></div>';
	echo '<div><label>Default Payment Information</label><textarea name="payment_info">' . esc_textarea( $s['payment_info'] ) . '</textarea></div>';

	echo '<button type="submit" class="btn btn-primary">Settings Save করুন</button>';
	echo '</form>';
}
