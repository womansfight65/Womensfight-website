(function () {
	'use strict';
	if (typeof wfaCrm === 'undefined') { return; }

	function post(action, data) {
		var body = new FormData();
		body.append('action', action);
		body.append('nonce', wfaCrm.nonce);
		for (var k in data) {
			if (Object.prototype.hasOwnProperty.call(data, k) && data[k] !== null && data[k] !== undefined) {
				body.append(k, data[k]);
			}
		}
		return fetch(wfaCrm.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body })
			.then(function (r) { return r.json(); });
	}

	function formToObject(form) {
		var out = {};
		new FormData(form).forEach(function (value, key) { out[key] = value; });
		return out;
	}

	var overlay, drawer, tableRegion, cardsRegion;

	function openDrawerWithHtml(html) {
		drawer.innerHTML = '<button type="button" class="crm-drawer-close" id="crm-drawer-close">&times;</button>' + html;
		overlay.classList.add('open');
		document.body.style.overflow = 'hidden';
	}

	function closeDrawer() {
		overlay.classList.remove('open');
		document.body.style.overflow = '';
	}

	function loadLead(leadId) {
		post('wfa_crm_get_drawer', { lead_id: leadId }).then(function (res) {
			if (res.success) { openDrawerWithHtml(res.data.html); }
		});
	}

	function loadNewLeadForm() {
		post('wfa_crm_get_new_lead_form', {}).then(function (res) {
			if (res.success) { openDrawerWithHtml(res.data.html); }
		});
	}

	function currentFilters() {
		return {
			search: document.getElementById('crm-f-search').value,
			date_from: document.getElementById('crm-f-from').value,
			date_to: document.getElementById('crm-f-to').value,
			source: document.getElementById('crm-f-source').value,
			course: document.getElementById('crm-f-course').value,
			counselor: document.getElementById('crm-f-counselor').value,
			status: document.getElementById('crm-f-status').value
		};
	}

	function refreshDashboard() {
		post('wfa_crm_get_dashboard', currentFilters()).then(function (res) {
			if (res.success) {
				cardsRegion.outerHTML = res.data.stats;
				cardsRegion = document.querySelector('.crm-cards');
				tableRegion.innerHTML = res.data.table;
			}
		});
	}

	var filterTimer;
	function debouncedRefresh() {
		clearTimeout(filterTimer);
		filterTimer = setTimeout(refreshDashboard, 350);
	}

	document.addEventListener('DOMContentLoaded', function () {
		overlay = document.getElementById('crm-drawer-overlay');
		drawer = document.getElementById('crm-drawer');
		tableRegion = document.getElementById('crm-table-region');
		cardsRegion = document.querySelector('.crm-cards');

		overlay.addEventListener('click', function (e) {
			if (e.target === overlay) { closeDrawer(); }
		});

		document.addEventListener('click', function (e) {
			if (e.target.id === 'crm-drawer-close') { closeDrawer(); }
			if (e.target.classList.contains('crm-open-lead')) { loadLead(e.target.getAttribute('data-lead-id')); }
			if (e.target.id === 'crm-new-lead') { loadNewLeadForm(); }
			if (e.target.id === 'crm-f-clear') {
				document.getElementById('crm-f-search').value = '';
				document.getElementById('crm-f-from').value = '';
				document.getElementById('crm-f-to').value = '';
				document.getElementById('crm-f-source').value = '';
				document.getElementById('crm-f-course').value = '';
				document.getElementById('crm-f-counselor').value = '';
				document.getElementById('crm-f-status').value = '';
				refreshDashboard();
			}
			if (e.target.classList.contains('crm-fu-complete')) {
				post('wfa_crm_complete_followup', { followup_id: e.target.getAttribute('data-fu-id') }).then(function (res) {
					if (res.success) { openDrawerWithHtml(res.data.html); refreshDashboard(); }
				});
			}
		});

		['crm-f-search', 'crm-f-from', 'crm-f-to', 'crm-f-source', 'crm-f-course', 'crm-f-counselor', 'crm-f-status'].forEach(function (id) {
			var el = document.getElementById(id);
			if (el) { el.addEventListener('input', debouncedRefresh); el.addEventListener('change', debouncedRefresh); }
		});

		/* Delegated form submissions — the drawer's inner HTML is
		   replaced on every refresh, so listeners are attached to
		   `document` and matched by form id, not bound once to elements
		   that get thrown away. */
		document.addEventListener('submit', function (e) {
			var form = e.target;

			if (form.id === 'crm-new-lead-form') {
				e.preventDefault();
				post('wfa_crm_create_lead', formToObject(form)).then(function (res) {
					if (res.success) { closeDrawer(); refreshDashboard(); }
					else { alert(res.data && res.data.message ? res.data.message : 'সমস্যা হয়েছে।'); }
				});
			}

			if (form.id === 'crm-lead-info-form') {
				e.preventDefault();
				var leadId = form.closest('.crm-drawer-inner').getAttribute('data-lead-id');
				post('wfa_crm_save_lead_info', Object.assign({ lead_id: leadId }, formToObject(form))).then(function (res) {
					if (res.success) { openDrawerWithHtml(res.data.html); refreshDashboard(); }
				});
			}

			if (form.id === 'crm-status-form') {
				e.preventDefault();
				var lid1 = form.closest('.crm-drawer-inner').getAttribute('data-lead-id');
				post('wfa_crm_change_status', Object.assign({ lead_id: lid1 }, formToObject(form))).then(function (res) {
					if (res.success) { openDrawerWithHtml(res.data.html); refreshDashboard(); }
				});
			}

			if (form.id === 'crm-conversation-form') {
				e.preventDefault();
				var lid2 = form.closest('.crm-drawer-inner').getAttribute('data-lead-id');
				post('wfa_crm_add_conversation', Object.assign({ lead_id: lid2 }, formToObject(form))).then(function (res) {
					if (res.success) { openDrawerWithHtml(res.data.html); refreshDashboard(); }
				});
			}

			if (form.id === 'crm-followup-form') {
				e.preventDefault();
				var lid3 = form.closest('.crm-drawer-inner').getAttribute('data-lead-id');
				post('wfa_crm_add_followup', Object.assign({ lead_id: lid3 }, formToObject(form))).then(function (res) {
					if (res.success) { openDrawerWithHtml(res.data.html); refreshDashboard(); }
				});
			}

			if (form.id === 'crm-admission-form') {
				e.preventDefault();
				var lid4 = form.closest('.crm-drawer-inner').getAttribute('data-lead-id');
				post('wfa_crm_convert_admission', Object.assign({ lead_id: lid4 }, formToObject(form))).then(function (res) {
					if (res.success) { openDrawerWithHtml(res.data.html); refreshDashboard(); }
					else { alert(res.data && res.data.message ? res.data.message : 'সমস্যা হয়েছে।'); }
				});
			}

			if (form.id === 'crm-commission-pay-form') {
				e.preventDefault();
				var lid5 = form.closest('.crm-drawer-inner').getAttribute('data-lead-id');
				if (!confirm('Commission পরিশোধ হয়েছে নিশ্চিত করছেন? এটা Accounts-এ Expense হিসেবে যোগ হবে।')) { return; }
				post('wfa_crm_pay_commission', Object.assign({ lead_id: lid5 }, formToObject(form))).then(function (res) {
					if (res.success) { openDrawerWithHtml(res.data.html); }
				});
			}
		});
	});
})();
