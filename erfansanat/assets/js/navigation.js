/**
 * Erfan Sanat navigation accessibility enhancements.
 *
 * The bundled main.js drives the drawer/modal open/close and scroll-lock.
 * This file adds what main.js lacks: Escape-to-close for the mobile drawer,
 * focus move-on-open + focus trap + focus restore, and an active-on-scroll
 * spy for landing section menus ([data-landing-link]).
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
		var mobileMenu = document.querySelector('[data-mobile-menu="true"]');
		var sidebar = document.querySelector('[data-mobile-sidebar="true"]');
		var toggle = document.querySelector('[data-mobile-toggle="true"]');

		function isOpen() {
			return mobileMenu && !mobileMenu.classList.contains("hidden");
		}

		function closeMenu(restoreFocus) {
			if (!mobileMenu) {
				return;
			}
			mobileMenu.classList.add("hidden");
			mobileMenu.classList.remove("block");
			mobileMenu.setAttribute("aria-hidden", "true");
			document.body.style.overflow = "";
			if (toggle) {
				toggle.setAttribute("aria-expanded", "false");
				if (restoreFocus) {
					toggle.focus();
				}
			}
		}

		// Escape closes the drawer (SC-02).
		document.addEventListener("keydown", function (e) {
			if (e.key === "Escape" && isOpen()) {
				closeMenu(true);
			}
		});

		// Move focus into the drawer when it opens (SC-01).
		if (toggle && sidebar) {
			toggle.addEventListener("click", function () {
				window.setTimeout(function () {
					if (isOpen()) {
						var closeBtn = sidebar.querySelector('[data-mobile-close="true"]');
						if (closeBtn) {
							closeBtn.focus();
						}
					}
				}, 0);
			});
		}

		// Focus trap inside the drawer while open (AC-03).
		if (sidebar) {
			sidebar.addEventListener("keydown", function (e) {
				if (e.key !== "Tab" || !isOpen()) {
					return;
				}
				var focusables = sidebar.querySelectorAll(
					'a[href], button:not([disabled]), input, select, textarea, [tabindex]:not([tabindex="-1"])'
				);
				if (!focusables.length) {
					return;
				}
				var first = focusables[0];
				var last = focusables[focusables.length - 1];
				if (e.shiftKey && document.activeElement === first) {
					e.preventDefault();
					last.focus();
				} else if (!e.shiftKey && document.activeElement === last) {
					e.preventDefault();
					first.focus();
				}
			});
		}

		// Landing section scroll-spy: highlight the active anchor (P04-T019).
		var landingLinks = Array.prototype.slice.call(
			document.querySelectorAll("[data-landing-link]")
		);
		if (landingLinks.length && "IntersectionObserver" in window) {
			var byId = {};
			landingLinks.forEach(function (link) {
				var href = link.getAttribute("href") || "";
				var id = href.charAt(0) === "#" ? href.slice(1) : "";
				if (id) {
					byId[id] = link;
				}
			});
			var setActive = function (id) {
				landingLinks.forEach(function (link) {
					var on = link === byId[id];
					link.classList.toggle("is-active", on);
					if (on) {
						link.setAttribute("aria-current", "true");
					} else {
						link.removeAttribute("aria-current");
					}
				});
			};
			var observer = new IntersectionObserver(
				function (entries) {
					entries.forEach(function (entry) {
						if (entry.isIntersecting && byId[entry.target.id]) {
							setActive(entry.target.id);
						}
					});
				},
				{ rootMargin: "-45% 0px -50% 0px", threshold: 0 }
			);
			Object.keys(byId).forEach(function (id) {
				var section = document.getElementById(id);
				if (section) {
					observer.observe(section);
				}
			});
		}
	});
})();
