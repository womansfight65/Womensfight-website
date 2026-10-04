(function () {
	'use strict';
	var body = document.getElementById('wfi-items-body');
	if (!body) { return; }

	var template = document.getElementById('wfi-row-template');

	function addRow(data) {
		var frag = template.content.cloneNode(true);
		var row = frag.querySelector('.wfi-item-row');
		if (data) {
			row.querySelector('.wfi-i-name').value = data.name || '';
			row.querySelector('.wfi-i-desc').value = data.desc || '';
			row.querySelector('.wfi-i-qty').value = data.qty || 1;
			row.querySelector('.wfi-i-price').value = data.price || 0;
		}
		body.appendChild(frag);
		recalcRow(body.lastElementChild);
		recalcTotals();
	}

	function recalcRow(row) {
		var qty = parseFloat(row.querySelector('.wfi-i-qty').value) || 0;
		var price = parseFloat(row.querySelector('.wfi-i-price').value) || 0;
		var amount = qty * price;
		row.querySelector('.wfi-i-amount').textContent = amount.toFixed(2);
		return amount;
	}

	function recalcTotals() {
		var subtotal = 0;
		body.querySelectorAll('.wfi-item-row').forEach(function (row) {
			subtotal += recalcRow(row);
		});
		var discount = parseFloat(document.getElementById('wfi-discount').value) || 0;
		var tax = parseFloat(document.getElementById('wfi-tax').value) || 0;
		var other = parseFloat(document.getElementById('wfi-other').value) || 0;
		var grand = subtotal - discount + tax + other;
		var paid = parseFloat(document.getElementById('wfi-paid').value) || 0;
		var due = grand - paid;

		document.getElementById('wfi-t-subtotal').textContent = subtotal.toFixed(2);
		document.getElementById('wfi-t-grand').textContent = grand.toFixed(2);
		document.getElementById('wfi-t-due').textContent = due.toFixed(2);
	}

	document.getElementById('wfi-add-row').addEventListener('click', function () {
		addRow();
	});

	body.addEventListener('input', function (e) {
		if (e.target.classList.contains('wfi-i-qty') || e.target.classList.contains('wfi-i-price')) {
			recalcTotals();
		}
	});

	body.addEventListener('click', function (e) {
		var row = e.target.closest('.wfi-item-row');
		if (!row) { return; }
		if (e.target.classList.contains('wfi-row-del')) {
			if (body.querySelectorAll('.wfi-item-row').length > 1) {
				row.remove();
				recalcTotals();
			}
		}
		if (e.target.classList.contains('wfi-row-dup')) {
			addRow({
				name: row.querySelector('.wfi-i-name').value,
				desc: row.querySelector('.wfi-i-desc').value,
				qty: row.querySelector('.wfi-i-qty').value,
				price: row.querySelector('.wfi-i-price').value
			});
		}
	});

	['wfi-discount', 'wfi-tax', 'wfi-other', 'wfi-paid'].forEach(function (id) {
		document.getElementById(id).addEventListener('input', recalcTotals);
	});

	if (window.wfiExistingItems && window.wfiExistingItems.length) {
		window.wfiExistingItems.forEach(function (it) { addRow(it); });
	} else {
		addRow();
	}
})();
