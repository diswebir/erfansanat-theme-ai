/**
 * Erfan Sanat smart project filters → live AJAX (P08-T010).
 *
 * Keeps the reference projects DOM (data-project-search / type & city
 * checkboxes / term counters / count / empty state / grid) and swaps cards
 * WITHOUT refresh via es_project_filters. Filter state is shareable: mirrored
 * into the URL (es_ptypes, es_pcities, es_pq, es_psort, es_ppage) with
 * replaceState and restored on load. Facet counters update from the response.
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
		if (window.__esProjectFiltersInit) return; // idempotent.
		var archive = document.querySelector('[data-projects-archive="true"]');
		var grid = document.querySelector('[data-project-grid="true"]');
		if (!archive || !grid) return;
		var config = window.ES_AJAX;
		if (!config || !config.url || !config.nonces || !config.nonces.project_filters) {
			return; // main.js client-side filtering remains the fallback.
		}
		window.__esProjectFiltersInit = true;

		var DEBOUNCE = 250;
		var timer = null;
		var controller = null;
		var seq = 0;
		var emptyEl = archive.querySelector('[data-project-empty="true"]');
		var countEls = archive.querySelectorAll('[data-project-count="true"]');
		var searchInputs = archive.querySelectorAll('[data-project-search="true"]');
		var sortSelects = archive.querySelectorAll('[data-project-sort="true"]');
		var typeBoxes = archive.querySelectorAll("[data-project-type-filter]");
		var cityBoxes = archive.querySelectorAll("[data-project-city-filter]");
		var counterEls = archive.querySelectorAll("[data-project-term-count]");
		var clearBtns = archive.querySelectorAll('[data-project-filters-clear="true"]');
		var paginationRoots = archive.querySelectorAll('[data-pagination-root="true"]');

		var state = { types: [], cities: [], term: "", orderby: "newest", page: 1 };

		function faDigits(v) {
			return String(v).replace(/[0-9]/g, function (d) { return "۰۱۲۳۴۵۶۷۸۹"[d]; });
		}

		function readControls() {
			state.types = [];
			typeBoxes.forEach(function (box) { if (box.checked) state.types.push(box.getAttribute("data-project-type-filter")); });
			state.cities = [];
			cityBoxes.forEach(function (box) { if (box.checked) state.cities.push(box.getAttribute("data-project-city-filter")); });
			var search = archive.querySelector('[data-project-search="true"]');
			state.term = search ? (search.value || "").trim() : "";
			// Reference sort select (newest/popular/rating); rating falls back to newest.
			var sort = archive.querySelector('[data-project-sort="true"]');
			if (sort) state.orderby = sort.value === "popular" ? "popular" : "newest";
		}

		function syncControls() {
			typeBoxes.forEach(function (box) { box.checked = state.types.indexOf(box.getAttribute("data-project-type-filter")) !== -1; });
			cityBoxes.forEach(function (box) { box.checked = state.cities.indexOf(box.getAttribute("data-project-city-filter")) !== -1; });
			searchInputs.forEach(function (input) { input.value = state.term; });
			sortSelects.forEach(function (select) { select.value = state.orderby === "popular" ? "popular" : "newest"; });
		}

		function writeUrl() {
			var params = new URLSearchParams(window.location.search);
			if (state.types.length) params.set("es_ptypes", state.types.join(",")); else params.delete("es_ptypes");
			if (state.cities.length) params.set("es_pcities", state.cities.join(",")); else params.delete("es_pcities");
			if (state.term) params.set("es_pq", state.term); else params.delete("es_pq");
			if (state.orderby !== "newest") params.set("es_psort", state.orderby); else params.delete("es_psort");
			if (state.page > 1) params.set("es_ppage", String(state.page)); else params.delete("es_ppage");
			var qs = params.toString();
			window.history.replaceState(null, "", window.location.pathname + (qs ? "?" + qs : "") + window.location.hash);
		}

		function readUrl() {
			var params = new URLSearchParams(window.location.search);
			state.types = (params.get("es_ptypes") || "").split(",").filter(Boolean);
			state.cities = (params.get("es_pcities") || "").split(",").filter(Boolean);
			state.term = params.get("es_pq") || "";
			state.orderby = params.get("es_psort") || "newest";
			state.page = parseInt(params.get("es_ppage") || "1", 10) || 1;
			return !!(state.types.length || state.cities.length || state.term || state.orderby !== "newest" || state.page > 1);
		}

		function setBusy(on) {
			grid.setAttribute("aria-busy", on ? "true" : "false");
			grid.classList.toggle("opacity-60", on);
		}

		// Server-driven pagination inside the reference root (P08-T009 pattern).
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
					'<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg><span>قبلی</span></button>';
				for (var num = 1; num <= pages; num++) {
					var active = num === data.page;
					out += '<button type="button" data-page-btn="' + num + '" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer ' +
						(active ? "bg-emerald-700 text-white shadow-md shadow-emerald-700/25" : "border border-slate-200 bg-white text-slate-700 hover:border-emerald-400 hover:text-emerald-700") + '"' +
						(active ? ' aria-current="page"' : "") + ">" + faDigits(num) + "</button>";
				}
				out += '<button type="button" data-page-next="true" ' + (data.page >= pages ? "disabled" : "") + ' class="' + navClass + '">' +
					'<span>بعدی</span><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button>';
				buttons.innerHTML = out;
				buttons.querySelectorAll("[data-page-btn]").forEach(function (btn) {
					btn.addEventListener("click", function () {
						state.page = parseInt(btn.getAttribute("data-page-btn"), 10) || 1;
						apply();
					});
				});
				var prev = buttons.querySelector('[data-page-prev="true"]');
				if (prev) prev.addEventListener("click", function () { if (state.page > 1) { state.page--; apply(); } });
				var next = buttons.querySelector('[data-page-next="true"]');
				if (next) next.addEventListener("click", function () { if (state.page < pages) { state.page++; apply(); } });
			});
		}

		function apply() {
			var mySeq = ++seq;
			if (controller) controller.abort();
			controller = typeof AbortController !== "undefined" ? new AbortController() : null;
			writeUrl();
			setBusy(true);
			var perPage = 6;
			var pagRoot = archive.querySelector('[data-pagination-root="true"]');
			if (pagRoot && pagRoot.getAttribute("data-per-page")) perPage = parseInt(pagRoot.getAttribute("data-per-page"), 10) || 6;
			var body = new URLSearchParams();
			body.set("action", "es_project_filters");
			body.set("nonce", config.nonces.project_filters);
			body.set("term", state.term);
			body.set("types", state.types.join(","));
			body.set("cities", state.cities.join(","));
			body.set("orderby", state.orderby);
			body.set("page", String(state.page));
			body.set("per_page", String(perPage));
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
				var counts = data.counts || {};
				counterEls.forEach(function (el) {
					var key = el.getAttribute("data-project-term-count") || "";
					var tax = key.slice(0, key.indexOf(":"));
					var id = key.slice(key.indexOf(":") + 1);
					if (counts[tax] && counts[tax][id] !== undefined) el.textContent = faDigits(counts[tax][id]);
				});
				renderPagination(data);
				document.dispatchEvent(new CustomEvent("es:projects-filtered", { detail: data }));
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

		typeBoxes.forEach(function (box) { box.addEventListener("change", schedule); });
		cityBoxes.forEach(function (box) { box.addEventListener("change", schedule); });
		searchInputs.forEach(function (input) { input.addEventListener("input", schedule); });
		sortSelects.forEach(function (select) { select.addEventListener("change", schedule); });
		clearBtns.forEach(function (btn) {
			btn.addEventListener("click", function () {
				state = { types: [], cities: [], term: "", orderby: "newest", page: 1 };
				syncControls();
				renderPagination({ pages: 1 });
				if (timer) clearTimeout(timer);
				apply();
			});
		});

		// Restore shared state from the URL on first load.
		if (readUrl()) {
			syncControls();
			apply();
		}
	});
})();
