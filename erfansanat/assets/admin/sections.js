/**
 * Erfan Sanat section manager metabox: drag & drop ordering, enable/disable,
 * add/remove and AJAX save (no page reload). Loaded only on the page editor.
 */
(function () {
	"use strict";

	var container = document.querySelector("[data-es-sections]");
	if (!container) {
		return;
	}

	var list = container.querySelector("[data-es-sections-list]");
	var saveBtn = container.querySelector("[data-es-sections-save]");
	var addBtn = container.querySelector("[data-es-section-add]");
	var addSelect = container.querySelector("[data-es-section-add-select]");
	var status = container.querySelector("[data-es-sections-status]");
	var config = window.ES_SECTIONS || {};
	var dragRow = null;

	function rows() {
		return Array.prototype.slice.call(list.querySelectorAll("[data-section-id]"));
	}

	function setStatus(message, kind) {
		if (!status) {
			return;
		}
		status.textContent = message;
		status.classList.remove("is-success", "is-error");
		if (kind) {
			status.classList.add(kind === "success" ? "is-success" : "is-error");
		}
	}

	function collect() {
		return rows().map(function (row) {
			var enabled = row.querySelector("[data-es-section-enabled]");
			return {
				id: row.getAttribute("data-section-id"),
				type: row.getAttribute("data-section-type"),
				enabled: enabled ? enabled.checked : true
			};
		});
	}

	function save() {
		if (!config.ajaxUrl) {
			return;
		}
		saveBtn.disabled = true;
		setStatus("در حال ذخیره…", "");

		var body = new URLSearchParams();
		body.append("action", config.action || "es_save_sections");
		body.append(config.nonceName || "es_sections_nonce", config.nonce || "");
		body.append("post_id", container.getAttribute("data-post-id"));
		body.append("context", container.getAttribute("data-context"));
		body.append("sections", JSON.stringify(collect()));

		fetch(config.ajaxUrl, {
			method: "POST",
			credentials: "same-origin",
			headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
			body: body.toString()
		})
			.then(function (response) {
				return response.json().then(function (data) {
					return { status: response.status, data: data };
				});
			})
			.then(function (result) {
				if (result.status === 200 && result.data && result.data.success) {
					setStatus("ترتیب سکشن‌ها ذخیره شد.", "success");
				} else {
					setStatus((result.data && result.data.data && result.data.data.message) || "خطا در ذخیره‌سازی.", "error");
				}
			})
			.catch(function () {
				setStatus("خطای شبکه در ذخیره‌سازی.", "error");
			})
			.finally(function () {
				saveBtn.disabled = false;
			});
	}

	// Drag & drop reordering.
	list.addEventListener("dragstart", function (event) {
		dragRow = event.target.closest("[data-section-id]");
		if (dragRow) {
			event.dataTransfer.effectAllowed = "move";
			dragRow.classList.add("is-dragging");
		}
	});
	list.addEventListener("dragend", function () {
		if (dragRow) {
			dragRow.classList.remove("is-dragging");
			dragRow = null;
		}
	});
	list.addEventListener("dragover", function (event) {
		event.preventDefault();
		var target = event.target.closest("[data-section-id]");
		if (!target || !dragRow || target === dragRow) {
			return;
		}
		var rect = target.getBoundingClientRect();
		var after = (event.clientY - rect.top) > rect.height / 2;
		list.insertBefore(dragRow, after ? target.nextSibling : target);
	});

	// Button controls (up / down / remove / enable).
	list.addEventListener("click", function (event) {
		var row = event.target.closest("[data-section-id]");
		if (!row) {
			return;
		}
		if (event.target.closest("[data-es-section-up]")) {
			event.preventDefault();
			var prev = row.previousElementSibling;
			if (prev) {
				list.insertBefore(row, prev);
			}
		} else if (event.target.closest("[data-es-section-down]")) {
			event.preventDefault();
			var next = row.nextElementSibling;
			if (next) {
				list.insertBefore(next, row);
			}
		} else if (event.target.closest("[data-es-section-remove]")) {
			event.preventDefault();
			row.remove();
		}
	});

	// Add a section of the selected type (unique instance id).
	if (addBtn && addSelect) {
		addBtn.addEventListener("click", function (event) {
			event.preventDefault();
			var type = addSelect.value;
			if (!type) {
				return;
			}
			var existing = rows().filter(function (r) {
				return r.getAttribute("data-section-type") === type;
			}).length;
			var id = existing === 0 ? type : type + "-" + (existing + 1);
			var label = addSelect.options[addSelect.selectedIndex].textContent;

			var row = document.createElement("div");
			row.className = "es-section-row";
			row.draggable = true;
			row.setAttribute("data-section-id", id);
			row.setAttribute("data-section-type", type);
			row.innerHTML =
				'<span class="es-section-row__handle" aria-hidden="true">⠿</span>' +
				'<label class="es-section-row__toggle"><input type="checkbox" data-es-section-enabled checked /></label>' +
				'<span class="es-section-row__title"></span>' +
				'<span class="es-section-row__controls">' +
				'<button type="button" class="button" data-es-section-up>↑</button>' +
				'<button type="button" class="button" data-es-section-down>↓</button>' +
				'<button type="button" class="button" data-es-section-remove>×</button>' +
				'</span>';
			row.querySelector(".es-section-row__title").textContent = label;
			list.appendChild(row);
		});
	}

	if (saveBtn) {
		saveBtn.addEventListener("click", save);
	}
})();
