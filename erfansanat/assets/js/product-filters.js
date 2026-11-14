/**
 * Erfan Sanat smart product filters → live AJAX (P08-T006).
 *
 * Keeps the reference shop DOM (data-product-search / cat checkboxes / count /
 * empty state / grid) and swaps cards WITHOUT refresh via es_product_filters.
 * Filter state is shareable: mirrored into the URL query (es_cats, es_q) with
 * replaceState and restored on load. Price/purchase controls join in T007,
 * pa_* attributes in T008, sorting/pagination in T009 — the state object and
 * apply() pipeline are built to carry them.
 */
(function () {
	"use strict";

	function ready(fn) {
		if (document.readyState !== "loading") {
			fn();
		} else {
			document.addEventListener("DOMContentLoaded", fn);
		}
	}

	ready(function () {
		if (window.__esProductFiltersInit) return; // idempotent.
		var archive = document.querySelector('[data-products-archive="true"]');
		var grid = document.querySelector('[data-product-grid="true"]');
		if (!archive || !grid) return;
		var config = window.ES_AJAX;
		if (!config || !config.url || !config.nonces || !config.nonces.product_filters) {
			return; // main.js client-side filtering remains the fallback.
		}
		window.__esProductFiltersInit = true;

		var DEBOUNCE = 250;
		var timer = null;
		var controller = null;
		var seq = 0;
		var emptyEl = archive.querySelector('[data-product-empty="true"]');
		var countEls = archive.querySelectorAll('[data-product-count="true"]');
		var searchInputs = archive.querySelectorAll('[data-product-search="true"]');
		var catBoxes = archive.querySelectorAll("[data-product-cat-filter]");
		var clearBtns = archive.querySelectorAll('[data-product-filters-clear="true"]');

		var state = { cats: [], term: "", price_max: 0, sale_mode: "all", orderby: "latest", page: 1 };

		function faDigits(v) {
			return String(v).replace(/[0-9]/g, function (d) { return "۰۱۲۳۴۵۶۷۸۹"[d]; });
		}

		var priceRanges = archive.querySelectorAll('[data-product-price-range="true"]');
		var modeRadios = archive.querySelectorAll("[data-product-purchase-mode]");

		function readControls() {
			state.cats = [];
			catBoxes.forEach(function (box) {
				if (box.checked) state.cats.push(box.getAttribute("data-product-cat-filter"));
			});
			var search = archive.querySelector('[data-product-search="true"]');
			state.term = search ? (search.value || "").trim() : "";
			// P08-T007: price ceiling — at slider max means "all prices".
			var range = archive.querySelector('[data-product-price-range="true"]');
			if (range) {
				var value = parseInt(range.value || "0", 10) || 0;
				var max = parseInt(range.getAttribute("max") || "0", 10) || 0;
				state.price_max = (max > 0 && value >= max) ? 0 : value;
			}
			// P08-T007: purchase mode radios (reference "call" = inquiry).
			var checked = archive.querySelector("[data-product-purchase-mode]:checked");
			if (checked) {
				var mode = checked.getAttribute("data-product-purchase-mode");
				state.sale_mode = mode === "online" ? "online" : (mode === "call" || mode === "inquiry") ? "inquiry" : "all";
			}
		}

		function syncControls() {
			catBoxes.forEach(function (box) {
				box.checked = state.cats.indexOf(box.getAttribute("data-product-cat-filter")) !== -1;
			});
			searchInputs.forEach(function (input) { input.value = state.term; });
			priceRanges.forEach(function (range) {
				var max = parseInt(range.getAttribute("max") || "0", 10) || 0;
				range.value = state.price_max > 0 ? String(state.price_max) : String(max);
				range.dispatchEvent(new Event("es:sync")); // label listeners may hook.
			});
			modeRadios.forEach(function (radio) {
				var mode = radio.getAttribute("data-product-purchase-mode");
				radio.checked = state.sale_mode === "all" ? mode === "all"
					: state.sale_mode === "online" ? mode === "online"
					: (mode === "call" || mode === "inquiry");
			});
		}

		function writeUrl() {
			var params = new URLSearchParams(window.location.search);
			if (state.cats.length) params.set("es_cats", state.cats.join(",")); else params.delete("es_cats");
			if (state.term) params.set("es_q", state.term); else params.delete("es_q");
			if (state.price_max > 0) params.set("es_price", String(state.price_max)); else params.delete("es_price");
			if (state.sale_mode !== "all") params.set("es_mode", state.sale_mode); else params.delete("es_mode");
			if (state.orderby !== "latest") params.set("es_sort", state.orderby); else params.delete("es_sort");
			if (state.page > 1) params.set("es_page", String(state.page)); else params.delete("es_page");
			var qs = params.toString();
			window.history.replaceState(null, "", window.location.pathname + (qs ? "?" + qs : "") + window.location.hash);
		}

		function readUrl() {
			var params = new URLSearchParams(window.location.search);
			state.cats = (params.get("es_cats") || "").split(",").filter(Boolean);
			state.term = params.get("es_q") || "";
			state.price_max = parseInt(params.get("es_price") || "0", 10) || 0;
			state.sale_mode = params.get("es_mode") || "all";
			state.orderby = params.get("es_sort") || "latest";
			state.page = parseInt(params.get("es_page") || "1", 10) || 1;
			return !!(state.cats.length || state.term || state.price_max || state.sale_mode !== "all" || state.orderby !== "latest" || state.page > 1);
		}

		function setBusy(on) {
			grid.setAttribute("aria-busy", on ? "true" : "false");
			grid.classList.toggle("opacity-60", on);
		}

		function apply() {
			var mySeq = ++seq;
			if (controller) controller.abort();
			controller = typeof AbortController !== "undefined" ? new AbortController() : null;
			writeUrl();
			setBusy(true);
			var body = new URLSearchParams();
			body.set("action", "es_product_filters");
			body.set("nonce", config.nonces.product_filters);
			body.set("term", state.term);
			body.set("cats", state.cats.join(","));
			body.set("price_max", String(state.price_max || 0));
			body.set("sale_mode", state.sale_mode);
			body.set("orderby", state.orderby);
			body.set("page", String(state.page));
			body.set("per_page", archive.getAttribute("data-items-per-page") || "12");
			(window.__esFiltersExtraBody || function () {})(body, state); // T007/T008 hooks.
			fetch(config.url, {
				method: "POST",
				credentials: "same-origin",
				headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
				body: body.toString(),
				signal: controller ? controller.signal : undefined
			}).then(function (res) { return res.json(); }).then(function (payload) {
				if (mySeq !== seq) return; // stale response dropped.
				setBusy(false);
				if (!payload || !payload.success) return;
				var data = payload.data || {};
				grid.innerHTML = data.html || ""; // server-rendered, escaped cards.
				countEls.forEach(function (el) { el.textContent = faDigits(data.total || 0); });
				if (emptyEl) {
					var empty = !data.total;
					emptyEl.classList.toggle("hidden", !empty);
					emptyEl.classList.toggle("block", empty);
				}
				document.dispatchEvent(new CustomEvent("es:products-filtered", { detail: data }));
			}).catch(function (err) {
				if (err && err.name === "AbortError") return;
				if (mySeq === seq) setBusy(false);
			});
		}

		function schedule() {
			if (timer) clearTimeout(timer);
			timer = setTimeout(function () {
				readControls();
				state.page = 1;
				apply();
			}, DEBOUNCE);
		}

		catBoxes.forEach(function (box) { box.addEventListener("change", schedule); });
		searchInputs.forEach(function (input) { input.addEventListener("input", schedule); });
		priceRanges.forEach(function (range) { range.addEventListener("input", schedule); });
		modeRadios.forEach(function (radio) { radio.addEventListener("change", schedule); });
		clearBtns.forEach(function (btn) {
			btn.addEventListener("click", function () {
				state = { cats: [], term: "", price_max: 0, sale_mode: "all", orderby: "latest", page: 1 };
				syncControls();
				if (timer) clearTimeout(timer);
				apply();
			});
		});

		// Expose for sibling enhancers (T007 price/mode, T008 attributes, T009 sort).
		window.ES_PRODUCT_FILTERS = {
			state: state,
			apply: function () { state.page = 1; apply(); },
			readControls: readControls
		};

		// Restore shared state from the URL on first load.
		if (readUrl()) {
			syncControls();
			apply();
		}
	});
})();
