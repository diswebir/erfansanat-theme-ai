/**
 * Erfan Sanat global search modal → live AJAX (P08-T004).
 *
 * The bundled main.js keeps ownership of open/close and the local fallback
 * filter over pre-rendered [data-search-item] rows. This enhancer adds:
 *  - debounced queries (250ms) to es_product_search / es_content_search,
 *  - AbortController + a sequence guard so fast typing only shows the LAST
 *    request's results (AC-02 / SC-01),
 *  - keyboard navigation: ArrowDown/ArrowUp move, Enter opens, Escape resets
 *    (AC-03 / SC-02),
 *  - a safe empty state and graceful fallback when ES_AJAX is unavailable.
 * Rows keep the reference DOM/classes and data-search-item attribute (AC-01).
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
		if (window.__esSearchInit) return; // idempotent: never bind twice.
		window.__esSearchInit = true;
		var input = document.querySelector('[data-global-search-input="true"]');
		var productsBox = document.querySelector('[data-global-search-products="true"]');
		var projectsBox = document.querySelector('[data-global-search-projects="true"]');
		var modal = document.querySelector('[data-search-modal="true"]');
		if (!input || !productsBox || !projectsBox) return;

		var config = window.ES_AJAX;
		if (!config || !config.url || !config.nonces || !config.nonces.product_search) {
			return; // main.js local filter remains the fallback.
		}

		var DEBOUNCE = 250;
		var MIN_CHARS = 2;
		var initialProducts = productsBox.innerHTML;
		var initialProjects = projectsBox.innerHTML;
		var timer = null;
		var controller = null;
		var seq = 0;
		var activeIndex = -1;

		function rows() {
			return Array.prototype.slice.call(
				modal.querySelectorAll("[data-search-result-row]")
			).filter(function (a) { return !a.classList.contains("hidden"); });
		}

		function setActive(index) {
			var all = rows();
			activeIndex = index;
			all.forEach(function (a, i) {
				var on = i === activeIndex;
				a.classList.toggle("border-emerald-400", on);
				a.classList.toggle("bg-emerald-50", on);
				if (on) {
					a.setAttribute("aria-current", "true");
					if (a.scrollIntoView) a.scrollIntoView({ block: "nearest" });
				} else {
					a.removeAttribute("aria-current");
				}
			});
		}

		function resetActive() { setActive(-1); }

		function el(tag, className, text) {
			var node = document.createElement(tag);
			if (className) node.className = className;
			if (text !== undefined) node.textContent = text; // never innerHTML with user data.
			return node;
		}

		function resultRow(item) {
			var a = el("a", "flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/70 p-2.5 transition-colors hover:border-emerald-300 hover:bg-emerald-50/50");
			a.href = item.url;
			a.setAttribute("data-search-result-row", item.type || "result");
			a.setAttribute("data-search-item", [item.title, item.subtitle || ""].join(" "));
			var wrap = el("div", "flex min-w-0 items-center gap-3");
			var img = el("img", "h-10 w-10 shrink-0 rounded-lg object-cover");
			img.src = item.image;
			img.alt = item.title;
			img.loading = "lazy";
			var texts = el("div", "min-w-0");
			texts.appendChild(el("div", "truncate text-xs font-extrabold text-slate-900", item.title));
			var sub = item.subtitle || "";
			if (item.type === "post") sub = sub ? "مقاله · " + sub : "مقاله";
			texts.appendChild(el("div", "text-[10.5px] font-semibold text-slate-500", sub));
			wrap.appendChild(img);
			wrap.appendChild(texts);
			a.appendChild(wrap);
			return a;
		}

		function emptyNote(message) {
			return el("p", "rounded-xl border border-dashed border-slate-200 bg-slate-50/60 p-3 text-[11.5px] font-medium text-slate-500", message || "نتیجه‌ای یافت نشد.");
		}

		function renderBox(box, items, message) {
			box.textContent = "";
			if (!items.length) {
				box.appendChild(emptyNote(message));
				return;
			}
			items.forEach(function (item) { box.appendChild(resultRow(item)); });
		}

		function restoreInitial() {
			productsBox.innerHTML = initialProducts; // theme-rendered, escaped server-side.
			projectsBox.innerHTML = initialProjects;
			resetActive();
		}

		function query(action, term, signal) {
			var body = new URLSearchParams();
			body.set("action", action);
			body.set("nonce", config.nonces[action === "es_product_search" ? "product_search" : "content_search"] || "");
			body.set("term", term);
			body.set("limit", "6");
			return fetch(config.url, {
				method: "POST",
				credentials: "same-origin",
				headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
				body: body.toString(),
				signal: signal
			}).then(function (res) { return res.json(); });
		}

		function run(term) {
			var mySeq = ++seq;
			if (controller) controller.abort(); // AC-02: cancel the previous flight.
			controller = typeof AbortController !== "undefined" ? new AbortController() : null;
			var signal = controller ? controller.signal : undefined;
			productsBox.setAttribute("aria-busy", "true");
			projectsBox.setAttribute("aria-busy", "true");
			Promise.all([
				query("es_product_search", term, signal).catch(function () { return null; }),
				query("es_content_search", term, signal).catch(function () { return null; })
			]).then(function (responses) {
				if (mySeq !== seq) return; // AC-02/SC-01: stale response dropped.
				productsBox.removeAttribute("aria-busy");
				projectsBox.removeAttribute("aria-busy");
				var p = responses[0], c = responses[1];
				var pData = p && p.success ? p.data : null;
				var cData = c && c.success ? c.data : null;
				renderBox(productsBox, (pData && pData.results) || [], pData && pData.message);
				renderBox(projectsBox, (cData && cData.results) || [], cData && cData.message);
				resetActive();
			});
		}

		input.addEventListener("input", function () {
			var term = (input.value || "").trim();
			if (timer) clearTimeout(timer);
			if (term.length < MIN_CHARS) {
				seq++; // invalidate any in-flight response.
				if (controller) controller.abort();
				restoreInitial();
				return;
			}
			timer = setTimeout(function () { run(term); }, DEBOUNCE);
		});

		input.addEventListener("keydown", function (e) {
			var all = rows();
			if (e.key === "ArrowDown") {
				e.preventDefault();
				if (all.length) setActive((activeIndex + 1) % all.length);
			} else if (e.key === "ArrowUp") {
				e.preventDefault();
				if (all.length) setActive((activeIndex - 1 + all.length) % all.length);
			} else if (e.key === "Enter") {
				if (activeIndex >= 0 && all[activeIndex]) {
					e.preventDefault();
					all[activeIndex].click();
				}
			} else if (e.key === "Escape") {
				resetActive(); // main.js closes the modal itself.
			}
		});
	});
})();
