/**
 * Fallback PDP quantity guard (P06-T007). main.js owns the reference stepper
 * (min/step + live total + add-to-cart). This additive guard only enforces the
 * optional maximum and rejects an invalid quantity before main.js writes the
 * cart. It is loaded ONLY when WooCommerce is absent; on a real store Woo /
 * WPC Product Quantity render and validate the canonical quantity input.
 */
(function () {
	"use strict";

	var root = document.querySelector('[data-pdp-qty-root="true"]');
	if (!root) {
		return;
	}

	var min = parseInt(root.getAttribute("data-min-qty") || "1", 10);
	var step = parseInt(root.getAttribute("data-step-qty") || "1", 10);
	var max = parseInt(root.getAttribute("data-max-qty") || "0", 10);
	var current = min;

	var inc = root.querySelector('[data-pdp-qty-inc="true"]');
	var dec = root.querySelector('[data-pdp-qty-dec="true"]');
	var add = document.querySelector('[data-pdp-add-to-cart="true"]');

	function isValid(qty) {
		if (qty < min) return false;
		if (max > 0 && qty > max) return false;
		return (qty - min) % step === 0;
	}

	if (inc) {
		inc.addEventListener("click", function (event) {
			var next = current + step;
			if (!isValid(next)) {
				event.preventDefault();
				event.stopImmediatePropagation();
				return;
			}
			current = next;
		}, true);
	}

	if (dec) {
		dec.addEventListener("click", function () {
			var next = current - step;
			if (next >= min) {
				current = next;
			}
		}, true);
	}

	if (add) {
		add.addEventListener("click", function (event) {
			if (!isValid(current)) {
				event.preventDefault();
				event.stopImmediatePropagation();
			}
		}, true);
	}
})();
