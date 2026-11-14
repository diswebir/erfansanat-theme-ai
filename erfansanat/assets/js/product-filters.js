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
		var attrBoxes = archive.querySelectorAll("[data-product-attr-filter]");
		var sortSelects = archive.querySelectorAll('[data-product-sort="true"]');
		var paginationRoots = archive.querySelectorAll('[data-pagination-root="true"]');

		// P08-T008: attrs = { pa_tax: [termIds] }; everything else lives in state.
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
			state.attrs = {};
			attrBoxes.forEach(function (box) {
				if (!box.checked) return;
				var tax = box.getAttribute("data-product-attr-filter");
				(state.attrs[tax] = state.attrs[tax] || []).push(box.value);
			});
			// P08-T009: reference sort select (default/popular/rating/price-asc/price-desc).
			var sort = archive.querySelector('[data-product-sort="true"]');
			if (sort) {
				var sv = sort.value;
				state.orderby = sv === "price-asc" ? "price_asc" : sv === "price-desc" ? "price_desc" : sv === "popular" ? "popular" : "latest";
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
			attrBoxes.forEach(function (box) {
				var tax = box.getAttribute("data-product-attr-filter");
				var values = state.attrs[tax] || [];
				box.checked = values.indexOf(box.value) !== -1;
			});
			var sortBack = { latest: "default", oldest: "default", name: "default", popular: "popular", price_asc: "price-asc", price_desc: "price-desc" };
			sortSelects.forEach(function (select) { select.value = sortBack[state.orderby] || "default"; });
		}

		function writeUrl() {
			var params = new URLSearchParams(window.location.search);
			if (state.cats.length) params.set("es_cats", state.cats.join(",")); else params.delete("es_cats");
			var seenTaxes = {};
			attrBoxes.forEach(function (box) { seenTaxes[box.getAttribute("data-product-attr-filter")] = true; });
			Object.keys(seenTaxes).forEach(function (tax) {
				var values = state.attrs[tax] || [];
				if (values.length) params.set("es_attr_" + tax, values.join(",")); else params.delete("es_attr_" + tax);
			});
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
			state.attrs = {};
			params.forEach(function (value, key) {
				if (key.indexOf("es_attr_") === 0) {
					var values = value.split(",").filter(Boolean);
					if (values.length) state.attrs[key.slice(8)] = values;
				}
			});
			state.term = params.get("es_q") || "";
			state.price_max = parseInt(params.get("es_price") || "0", 10) || 0;
			state.sale_mode = params.get("es_mode") || "all";
			state.orderby = params.get("es_sort") || "latest";
			state.page = parseInt(params.get("es_page") || "1", 10) || 1;
			return !!(state.cats.length || state.term || state.price_max || state.sale_mode !== "all" || state.orderby !== "latest" || state.page > 1 || Object.keys(state.attrs).length);
		}

		// P08-T009: server-driven pagination inside the reference root.
		function renderPagination(data) {
			var pages = data.pages || 1;
			paginationRoots.forEach(function (root) {
				if (pages <= 1) {
					root.classList.add("hidden");
					root.classList.remove("flex");
					return;
				}
				root.classList.remove("hidden");
				root.classList.add("flex");
				var summary = root.querySelector('[data-pagination-summary="true"]');
				if (summary) {
					summary.textContent = "صفحه " + faDigits(data.page) + " از " + faDigits(pages) + " (" + faDigits(data.total) + " مورد)";
				}
				var buttons = root.querySelector('[data-pagination-buttons="true"]');
				if (!buttons) return;
				var out = "";
				var navClass = "inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-extrabold text-slate-600 transition hover:bg-emerald-50 disabled:opacity-40";
				out += '<button type="button" data-page-prev="true" ' + (data.page <= 1 ? "disabled" : "") + ' class="' + navClass + '">' +
					'<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>' +
					'<span>قبلی</span></button>';
				for (var num = 1; num <= pages; num++) {
					var active = num === data.page;
					out += '<button type="button" data-page-btn="' + num + '" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer ' +
						(active ? "bg-emerald-700 text-white shadow-md shadow-emerald-700/25" : "border border-slate-200 bg-white text-slate-700 hover:border-emerald-400 hover:text-emerald-700") + '"' +
						(active ? ' aria-current="page"' : "") + ">" + faDigits(num) + "</button>";
				}
				out += '<button type="button" data-page-next="true" ' + (data.page >= pages ? "disabled" : "") + ' class="' + navClass + '">' +
					'<span>بعدی</span>' +
					'<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button>';
				buttons.innerHTML = out;
				buttons.querySelectorAll("[data-page-btn]").forEach(function (btn) {
					btn.addEventListener("click", function () {
						state.page = parseInt(btn.getAttribute("data-page-btn"), 10) || 1;
						apply();
					});
				});
				var prev = buttons.querySelector('[data-page-prev="true"]');
				if (prev) prev.addEventListener("click", function () {
					if (state.page > 1) { state.page--; apply(); }
				});
				var next = buttons.querySelector('[data-page-next="true"]');
				if (next) next.addEventListener("click", function () {
					if (state.page < pages) { state.page++; apply(); }
				});
			});
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
			Object.keys(state.attrs).forEach(function (tax) { body.set(tax, state.attrs[tax].join(",")); });
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
				renderPagination(data);
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
		attrBoxes.forEach(function (box) { box.addEventListener("change", schedule); });
		sortSelects.forEach(function (select) { select.addEventListener("change", schedule); });
		clearBtns.forEach(function (btn) {
			btn.addEventListener("click", function () {
				state = { cats: [], attrs: {}, term: "", price_max: 0, sale_mode: "all", orderby: "latest", page: 1 };
				syncControls();
				renderPagination({ pages: 1 });
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
