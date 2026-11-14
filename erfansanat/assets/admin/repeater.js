/**
 * Erfan Sanat repeater field: add / remove / reorder with stable reindexing.
 * Loaded only on the dashboard screen.
 */
(function () {
	"use strict";

	function escapeRegex(value) {
		return value.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
	}

	function itemCount(repeater) {
		return repeater.querySelectorAll("[data-es-repeater-items] > [data-es-repeater-item]").length;
	}

	function updateButtons(repeater) {
		var min = parseInt(repeater.getAttribute("data-min") || "0", 10);
		var max = parseInt(repeater.getAttribute("data-max") || "20", 10);
		var count = itemCount(repeater);
		var addBtn = repeater.querySelector("[data-es-repeater-add]");
		if (addBtn) {
			addBtn.disabled = count >= max;
		}
		repeater.querySelectorAll("[data-es-repeater-remove]").forEach(function (btn) {
			btn.disabled = count <= min;
		});
	}

	function reindex(repeater) {
		var key = repeater.getAttribute("data-es-repeater");
		var nameRe = new RegExp("(\\[" + escapeRegex(key) + "\\])\\[[^\\]]*\\]");
		var keyRe = new RegExp("^(" + escapeRegex(key) + ")\\[[^\\]]*\\]");
		var idRe = new RegExp("^(es-field-" + escapeRegex(key) + ")-[^-]+-");
		var items = repeater.querySelectorAll("[data-es-repeater-items] > [data-es-repeater-item]");

		items.forEach(function (item, i) {
			item.setAttribute("data-index", String(i));
			item.querySelectorAll("[name], [data-es-key], [id]").forEach(function (el) {
				if (el.name) {
					el.name = el.name.replace(nameRe, "$1[" + i + "]");
				}
				var dk = el.getAttribute("data-es-key");
				if (dk) {
					el.setAttribute("data-es-key", dk.replace(keyRe, "$1[" + i + "]"));
				}
				if (el.id) {
					el.id = el.id.replace(idRe, "$1-" + i + "-");
				}
			});
		});
		updateButtons(repeater);
	}

	function initRepeater(repeater) {
		var itemsContainer = repeater.querySelector("[data-es-repeater-items]");
		var template = repeater.querySelector("[data-es-repeater-template]");
		var addBtn = repeater.querySelector("[data-es-repeater-add]");
		var max = parseInt(repeater.getAttribute("data-max") || "20", 10);
		var min = parseInt(repeater.getAttribute("data-min") || "0", 10);

		if (addBtn) {
			addBtn.addEventListener("click", function (event) {
				event.preventDefault();
				if (itemCount(repeater) >= max || !template) {
					return;
				}
				var html = template.innerHTML.replace(/__INDEX__/g, String(itemCount(repeater)));
				var holder = document.createElement("div");
				holder.innerHTML = html.trim();
				var item = holder.firstChild;
				itemsContainer.appendChild(item);
				reindex(repeater);
			});
		}

		repeater.addEventListener("click", function (event) {
			var target = event.target;
			var item = target.closest("[data-es-repeater-item]");
			if (!item) {
				return;
			}

			if (target.closest("[data-es-repeater-remove]")) {
				event.preventDefault();
				if (itemCount(repeater) <= min) {
					return;
				}
				item.remove();
				reindex(repeater);
			} else if (target.closest("[data-es-repeater-up]")) {
				event.preventDefault();
				var prev = item.previousElementSibling;
				if (prev) {
					itemsContainer.insertBefore(item, prev);
					reindex(repeater);
				}
			} else if (target.closest("[data-es-repeater-down]")) {
				event.preventDefault();
				var next = item.nextElementSibling;
				if (next) {
					itemsContainer.insertBefore(next, item);
					reindex(repeater);
				}
			}
		});

		reindex(repeater);
	}

	document.querySelectorAll("[data-es-repeater]").forEach(initRepeater);
})();
