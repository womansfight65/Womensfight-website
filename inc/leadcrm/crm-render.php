<?php
/**
 * Lead CRM — HTML rendering. Every function here returns/echoes a
 * fragment that both the initial page load AND the AJAX refresh calls
 * use, so the dashboard never drifts from the drawer's idea of the
 * same data.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wfa_crm_render_stat_group( $title, $items, $warn_label = '' ) {
	echo '<div class="crm-stat-group">';
	echo '<h4 class="crm-stat-group-title">' . esc_html( $title ) . '</h4>';
	echo '<div class="crm-cards">';
	foreach ( $items as $label => $value ) {
		$warn = ( $label === $warn_label && $value > 0 ) ? ' crm-card-warn' : '';
		echo '<div class="crm-card' . esc_attr( $warn ) . '"><span>' . esc_html( $label ) . '</span><b>' . intval( $value ) . '</b></div>';
	}
	echo '</div></div>';
}

function wfa_crm_render_stats_cards() {
	$s = wfa_crm_get_stats();

	echo '<div class="crm-stat-groups">';

	wfa_crm_render_stat_group(
		'Lead Summary',
		array(
			'Total Leads'        => $s['total'],
			'WF Academy Leads'   => $s['academy_total'],
			'WF Agency Leads'    => $s['agency_total'],
			'New Leads'          => $s['new'],
			"Today's Leads"      => $s['today'],
			'Follow-ups Today'   => $s['followups_today'],
			'Overdue Follow-ups' => $s['followups_overdue'],
			'Interested Leads'   => $s['interested'],
			'Admission Pending'  => $s['admission_pending'],
			'Admitted'           => $s['admitted'],
		),
		'Overdue Follow-ups'
	);

	echo '</div>';
}

function wfa_crm_render_filters() {
	$counselors = wfa_crm_counselor_choices();
	echo '<div class="crm-filters">';

	echo '<select id="crm-f-type"><option value="">সব Type</option>';
	foreach ( wfa_crm_lead_types() as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
	}
	echo '</select>';

	echo '<input type="text" id="crm-f-search" placeholder="নাম, ফোন বা Lead ID খুঁজুন">';

	/* Hidden but still functional — kept empty so the AJAX filter
	   payload stays compatible; the date/source filters were dropped
	   from the visible toolbar to keep the bar simple for daily use. */
	echo '<input type="hidden" id="crm-f-from" value="">';
	echo '<input type="hidden" id="crm-f-to" value="">';
	echo '<input type="hidden" id="crm-f-source" value="">';

	echo '<select id="crm-f-course"><option value="">সব Course</option>';
	foreach ( wfa_crm_courses() as $c ) {
		echo '<option>' . esc_html( $c ) . '</option>';
	}
	echo '</select>';

	echo '<select id="crm-f-counselor"><option value="">সব Counselor</option>';
	foreach ( $counselors as $id => $name ) {
		echo '<option value="' . esc_attr( $id ) . '">' . esc_html( $name ) . '</option>';
	}
	echo '</select>';

	echo '<select id="crm-f-status"><option value="">সব Status</option>';
	foreach ( wfa_crm_statuses() as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
	}
	echo '</select>';

	echo '<button type="button" class="btn btn-ghost" id="crm-f-clear">Clear</button>';
	echo '<button type="button" class="btn btn-primary" id="crm-new-lead">+ নতুন Lead</button>';
	echo '</div>';
}

function wfa_crm_status_badge( $status ) {
	$labels = wfa_crm_statuses();
	$label  = isset( $labels[ $status ] ) ? $labels[ $status ] : $status;
	return '<span class="crm-badge crm-badge-' . esc_attr( $status ) . '">' . esc_html( $label ) . '</span>';
}

function wfa_crm_type_badge( $type ) {
	$labels = wfa_crm_lead_types();
	$label  = isset( $labels[ $type ] ) ? $labels[ $type ] : $type;
	return '<span class="crm-badge crm-badge-type-' . esc_attr( $type ) . '">' . esc_html( $label ) . '</span>';
}

function wfa_crm_render_leads_table( $leads ) {
	echo '<div class="crm-table-wrap"><table class="crm-table">';
	echo '<thead><tr><th>#</th><th>Lead ID</th><th>Type</th><th>নাম</th><th>ফোন</th><th>Source</th><th>Course/Service</th><th>Counselor</th><th>Next Follow-up</th><th>Status</th><th>Action</th></tr></thead><tbody>';

	if ( $leads ) {
		global $wpdb;
		$ft  = wfa_crm_followups_table();
		$row = 1;
		foreach ( $leads as $lead ) {
			$next_fu = $wpdb->get_var(
				$wpdb->prepare( "SELECT next_date FROM {$ft} WHERE lead_id = %d AND status = 'pending' ORDER BY next_date ASC LIMIT 1", $lead->id )
			);
			echo '<tr data-lead-id="' . esc_attr( $lead->id ) . '">';
			echo '<td>' . intval( $row++ ) . '</td>';
			echo '<td>' . esc_html( $lead->lead_code ) . '</td>';
			echo '<td>' . wfa_crm_type_badge( $lead->lead_type ) . '</td>';
			echo '<td>' . esc_html( $lead->name ) . '</td>';
			echo '<td>' . esc_html( $lead->phone ) . '</td>';
			echo '<td>' . esc_html( $lead->source ) . '</td>';
			echo '<td>' . esc_html( $lead->course_interested ) . '</td>';
			echo '<td>' . esc_html( wfa_crm_counselor_name( $lead->counselor_id ) ) . '</td>';
			echo '<td>' . ( $next_fu ? esc_html( $next_fu ) : '—' ) . '</td>';
			echo '<td>' . wfa_crm_status_badge( $lead->status ) . '</td>';
			echo '<td><button type="button" class="btn btn-ghost crm-open-lead" data-lead-id="' . esc_attr( $lead->id ) . '">View</button></td>';
			echo '</tr>';
		}
	} else {
		echo '<tr><td colspan="11">কোনো Lead পাওয়া যায়নি।</td></tr>';
	}

	echo '</tbody></table></div>';
}

/**
 * Commission summary — Life Support IT Institute's commission, right
 * on this same page (no separate Commission page), per the "Commission
 * and CRM on one page" request.
 */
function wfa_crm_render_commission_summary() {
	$c = wfa_crm_get_commission_summary();
	echo '<h2 class="crm-section-title">Commission — Life Support IT Institute</h2>';
	echo '<div class="crm-cards crm-cards-commission">';
	echo '<div class="crm-card"><span>মোট Fee সংগ্রহ (সব Admission)</span><b>' . esc_html( wfa_accounts_money( $c['total_fee'] ) ) . '</b></div>';
	echo '<div class="crm-card crm-card-warn"><span>Commission বাকি (' . intval( $c['pending_count'] ) . 'টা Admission)</span><b>' . esc_html( wfa_accounts_money( $c['pending_amount'] ) ) . '</b></div>';
	echo '<div class="crm-card crm-card-good"><span>Commission পরিশোধিত</span><b>' . esc_html( wfa_accounts_money( $c['paid_amount'] ) ) . '</b></div>';
	echo '</div>';
}

function wfa_crm_render_dashboard() {
	wfa_crm_render_stats_cards();
	wfa_crm_render_commission_summary();
	echo '<h2 class="crm-section-title">সব Lead</h2>';
	wfa_crm_render_filters();
	echo '<div id="crm-table-region">';
	wfa_crm_render_leads_table( wfa_crm_query_leads() );
	echo '</div>';
}

/**
 * The lead detail drawer — Information, Conversation Timeline,
 * Follow-up, Status change, and Convert to Admission (or the
 * admission's own details, once converted), all in one scrollable
 * panel. Opened/refreshed entirely over AJAX, never a page navigation.
 */
function wfa_crm_render_lead_drawer( $lead_id ) {
	$lead = wfa_crm_get_lead( $lead_id );
	if ( ! $lead ) {
		echo '<p>Lead পাওয়া যায়নি।</p>';
		return;
	}

	$conversations = wfa_crm_get_conversations( $lead_id );
	$followups     = wfa_crm_get_followups( $lead_id );
	$admission     = wfa_crm_get_admission_by_lead( $lead_id );
	$counselors    = wfa_crm_counselor_choices();
	$channels      = wfa_crm_channels();
	$statuses      = wfa_crm_statuses();

	echo '<div class="crm-drawer-inner" data-lead-id="' . esc_attr( $lead->id ) . '">';

	echo '<div class="crm-drawer-head">';
	echo '<h2>' . esc_html( $lead->name ? $lead->name : $lead->lead_code ) . ' <span class="crm-drawer-code">' . esc_html( $lead->lead_code ) . '</span></h2>';
	echo wfa_crm_type_badge( $lead->lead_type ) . ' ' . wfa_crm_status_badge( $lead->status );
	echo '</div>';

	/* ---- Section 1: Lead Information ---- */
	echo '<div class="crm-drawer-section"><h3>Lead Information</h3>';
	echo '<form class="crm-inline-form" id="crm-lead-info-form">';
	echo '<div class="crm-grid2">';
	echo '<div><label>Lead Type</label><select name="lead_type">';
	foreach ( wfa_crm_lead_types() as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '"' . selected( $lead->lead_type, $key, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select></div>';
	echo '<div><label>নাম</label><input type="text" name="name" value="' . esc_attr( $lead->name ) . '"></div>';
	echo '<div><label>Phone</label><input type="text" name="phone" value="' . esc_attr( $lead->phone ) . '"></div>';
	echo '<div><label>WhatsApp</label><input type="text" name="whatsapp" value="' . esc_attr( $lead->whatsapp ) . '"></div>';
	echo '<div><label>Email</label><input type="email" name="email" value="' . esc_attr( $lead->email ) . '"></div>';
	echo '<div><label>Course Interested</label><select name="course_interested">';
	foreach ( wfa_crm_courses() as $c ) {
		echo '<option' . selected( $lead->course_interested, $c, false ) . '>' . esc_html( $c ) . '</option>';
	}
	echo '</select></div>';
	echo '<div><label>Source</label><select name="source">';
	foreach ( wfa_crm_sources() as $s ) {
		echo '<option' . selected( $lead->source, $s, false ) . '>' . esc_html( $s ) . '</option>';
	}
	echo '</select></div>';
	echo '<div><label>Platform</label><input type="text" name="platform" value="' . esc_attr( $lead->platform ) . '"></div>';
	echo '<div><label>Campaign</label><input type="text" name="campaign" value="' . esc_attr( $lead->campaign ) . '"></div>';
	echo '<div><label>Ad Set</label><input type="text" name="ad_set" value="' . esc_attr( $lead->ad_set ) . '"></div>';
	echo '<div><label>Ad</label><input type="text" name="ad" value="' . esc_attr( $lead->ad ) . '"></div>';
	echo '<div><label>Lead Date</label><input type="date" name="lead_date" value="' . esc_attr( $lead->lead_date ) . '"></div>';
	echo '<div><label>Counselor</label><select name="counselor_id"><option value="0">—</option>';
	foreach ( $counselors as $id => $name ) {
		echo '<option value="' . esc_attr( $id ) . '"' . selected( $lead->counselor_id, $id, false ) . '>' . esc_html( $name ) . '</option>';
	}
	echo '</select></div>';
	echo '</div>';
	echo '<button type="submit" class="btn btn-primary">তথ্য Save করুন</button>';
	echo '</form></div>';

	/* ---- Status change ---- */
	echo '<div class="crm-drawer-section"><h3>Lead Status</h3>';
	echo '<form class="crm-inline-form crm-row-form" id="crm-status-form">';
	echo '<select name="status">';
	foreach ( $statuses as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '"' . selected( $lead->status, $key, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select>';
	echo '<button type="submit" class="btn btn-ghost">Status বদলান</button>';
	echo '</form></div>';

	/* ---- Section 2: Conversation Timeline ---- */
	echo '<div class="crm-drawer-section"><h3>Conversation Timeline</h3>';
	echo '<form class="crm-inline-form" id="crm-conversation-form">';
	echo '<div class="crm-grid2">';
	echo '<div><label>Channel</label><select name="channel">';
	foreach ( $channels as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
	}
	echo '</select></div>';
	echo '<div><label>Outcome</label><input type="text" name="outcome" placeholder="যেমন: Interested, No Answer"></div>';
	echo '</div>';
	echo '<div><label>Note</label><textarea name="note" rows="2" required></textarea></div>';
	echo '<button type="submit" class="btn btn-ghost">Conversation যোগ করুন</button>';
	echo '</form>';

	echo '<div class="crm-timeline">';
	if ( $conversations ) {
		foreach ( $conversations as $c ) {
			echo '<div class="crm-timeline-item">';
			echo '<div class="crm-timeline-meta"><b>' . esc_html( isset( $channels[ $c->channel ] ) ? $channels[ $c->channel ] : $c->channel ) . '</b>';
			echo '<span>' . esc_html( $c->conv_date ) . ' ' . esc_html( substr( $c->conv_time, 0, 5 ) ) . ' — ' . esc_html( wfa_crm_counselor_name( $c->counselor_id ) ) . '</span></div>';
			if ( $c->note ) {
				echo '<p>' . nl2br( esc_html( $c->note ) ) . '</p>';
			}
			if ( $c->outcome ) {
				echo '<span class="crm-outcome">' . esc_html( $c->outcome ) . '</span>';
			}
			echo '</div>';
		}
	} else {
		echo '<p class="crm-hint">এখনো কোনো Conversation যোগ হয়নি।</p>';
	}
	echo '</div></div>';

	/* ---- Follow-up ---- */
	echo '<div class="crm-drawer-section"><h3>Follow-up</h3>';
	echo '<form class="crm-inline-form" id="crm-followup-form">';
	echo '<div class="crm-grid2">';
	echo '<div><label>Next Follow-up Date</label><input type="date" name="next_date" required></div>';
	echo '<div><label>Time</label><input type="time" name="next_time"></div>';
	echo '<div><label>Priority</label><select name="priority">';
	foreach ( wfa_crm_priorities() as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
	}
	echo '</select></div>';
	echo '<div><label>Reason</label><input type="text" name="reason" placeholder="যেমন: Call back about fee"></div>';
	echo '</div>';
	echo '<button type="submit" class="btn btn-ghost">Follow-up যোগ করুন</button>';
	echo '</form>';

	echo '<div class="crm-followup-list">';
	if ( $followups ) {
		$fu_statuses = wfa_crm_followup_statuses();
		foreach ( $followups as $f ) {
			echo '<div class="crm-followup-item crm-fu-' . esc_attr( $f->status ) . '">';
			echo '<div><b>' . esc_html( $f->next_date ) . ( $f->next_time ? ' ' . esc_html( substr( $f->next_time, 0, 5 ) ) : '' ) . '</b> — ' . esc_html( $f->reason );
			echo '<span class="crm-fu-priority">' . esc_html( ucfirst( $f->priority ) ) . '</span>';
			echo '<span class="crm-fu-status">' . esc_html( isset( $fu_statuses[ $f->status ] ) ? $fu_statuses[ $f->status ] : $f->status ) . '</span></div>';
			if ( 'pending' === $f->status ) {
				echo '<div class="crm-fu-actions">';
				echo '<button type="button" class="btn btn-ghost crm-fu-complete" data-fu-id="' . esc_attr( $f->id ) . '">Mark Completed</button>';
				echo '</div>';
			}
			echo '</div>';
		}
	} else {
		echo '<p class="crm-hint">কোনো Follow-up সেট করা নেই।</p>';
	}
	echo '</div></div>';

	/* ---- Convert to Admission ---- */
	echo '<div class="crm-drawer-section"><h3>Admission</h3>';
	if ( $admission ) {
		$types = wfa_crm_commission_types();
		echo '<table class="crm-admission-table">';
		echo '<tr><th>Fee Collected</th><td>' . esc_html( wfa_accounts_money( $admission->fee_collected ) ) . '</td></tr>';
		echo '<tr><th>Commission</th><td>' . esc_html( isset( $types[ $admission->commission_type ] ) ? $types[ $admission->commission_type ] : $admission->commission_type ) . ' — ' . esc_html( $admission->commission_value ) . ( 'percentage' === $admission->commission_type ? '%' : '' ) . ' = ' . esc_html( wfa_accounts_money( $admission->commission_amount ) ) . '</td></tr>';
		echo '<tr><th>Commission Status</th><td>' . esc_html( ucfirst( $admission->commission_status ) ) . '</td></tr>';
		echo '<tr><th>Admitted On</th><td>' . esc_html( $admission->admitted_at ) . '</td></tr>';
		echo '</table>';
		if ( 'pending' === $admission->commission_status ) {
			echo '<form class="crm-inline-form crm-row-form" id="crm-commission-pay-form">';
			echo '<select name="payment_method"><option value="">Payment Method</option>';
			foreach ( wfa_accounts_payment_methods() as $m ) {
				echo '<option>' . esc_html( $m ) . '</option>';
			}
			echo '</select>';
			echo '<button type="submit" class="btn btn-primary">Commission পরিশোধ হয়েছে চিহ্নিত করুন</button>';
			echo '</form>';
		} else {
			echo '<p class="crm-hint">Commission পরিশোধ হয়ে গেছে — Accounts-এ Expense হিসেবে রেকর্ড করা আছে।</p>';
		}
	} else {
		echo '<form class="crm-inline-form" id="crm-admission-form">';
		echo '<div class="crm-grid2">';
		echo '<div><label>মোট Fee Collected (৳)</label><input type="number" step="0.01" min="0" name="fee_collected" required></div>';
		echo '<div><label>Payment Method</label><select name="fee_payment_method"><option value="">সিলেক্ট করুন</option>';
		foreach ( wfa_accounts_payment_methods() as $m ) {
			echo '<option>' . esc_html( $m ) . '</option>';
		}
		echo '</select></div>';
		echo '<div><label>Commission Type</label><select name="commission_type">';
		foreach ( wfa_crm_commission_types() as $key => $label ) {
			echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
		}
		echo '</select></div>';
		echo '<div><label>Commission Value (% অথবা ৳)</label><input type="number" step="0.01" min="0" name="commission_value" required></div>';
		echo '</div>';
		echo '<p class="crm-hint">Life Support IT Institute-কে প্রদেয় Commission — চুক্তি অনুযায়ী Percentage বা Fixed টাকা লিখুন।</p>';
		echo '<button type="submit" class="btn btn-primary">Convert to Admission</button>';
		echo '</form>';
	}
	echo '</div>';

	echo '</div>'; // .crm-drawer-inner
}

/**
 * The "+ নতুন Lead" quick-add form, shown inside the same side
 * drawer/modal shell (no separate page) when staff manually add a
 * walk-in or phone-call lead.
 */
function wfa_crm_render_new_lead_form() {
	$counselors = wfa_crm_counselor_choices();
	echo '<div class="crm-drawer-inner">';
	echo '<div class="crm-drawer-head"><h2>নতুন Lead যোগ করুন</h2></div>';
	echo '<div class="crm-drawer-section">';
	echo '<form class="crm-inline-form" id="crm-new-lead-form">';
	echo '<div class="crm-grid2">';
	echo '<div><label>Lead Type</label><select name="lead_type" required><option value="">সিলেক্ট করুন</option>';
	foreach ( wfa_crm_lead_types() as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
	}
	echo '</select></div>';
	echo '<div><label>নাম</label><input type="text" name="name" required></div>';
	echo '<div><label>Phone</label><input type="text" name="phone" required></div>';
	echo '<div><label>WhatsApp</label><input type="text" name="whatsapp"></div>';
	echo '<div><label>Email</label><input type="email" name="email"></div>';
	echo '<div><label>Course Interested</label><select name="course_interested"><option value="">সিলেক্ট করুন</option>';
	foreach ( wfa_crm_courses() as $c ) {
		echo '<option>' . esc_html( $c ) . '</option>';
	}
	echo '</select></div>';
	echo '<div><label>Source</label><select name="source"><option value="">সিলেক্ট করুন</option>';
	foreach ( wfa_crm_sources() as $s ) {
		echo '<option>' . esc_html( $s ) . '</option>';
	}
	echo '</select></div>';
	echo '<div><label>Campaign</label><input type="text" name="campaign"></div>';
	echo '<div><label>Counselor</label><select name="counselor_id"><option value="0">—</option>';
	foreach ( $counselors as $id => $name ) {
		echo '<option value="' . esc_attr( $id ) . '">' . esc_html( $name ) . '</option>';
	}
	echo '</select></div>';
	echo '</div>';
	echo '<button type="submit" class="btn btn-primary">Lead তৈরি করুন</button>';
	echo '</form>';
	echo '</div></div>';
}
