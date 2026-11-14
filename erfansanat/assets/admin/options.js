/**
 * Erfan Sanat custom dashboard — RTL tabs, keyboard navigation and AJAX save.
 * Vanilla JS, no dependencies. Loaded only on the dashboard screen.
 */
(function () {
	"use strict";

	var dashboard = document.querySelector("[data-es-dashboard]");
	if (!dashboard) {
		return;
	}

	var tablist = dashboard.querySelector('[role="tablist"]');
	var tabs = Array.prototype.slice.call(dashboard.querySelectorAll('[role="tab"]'));
	var panels = Array.prototype.slice.call(dashboard.querySelectorAll('[role="tabpanel"]'));

	function panelFor(tab) {
		var id = tab.getAttribute("aria-controls");
		return document.getElementById(id);
	}

	function activate(tab, setFocus) {
		tabs.forEach(function (t) {
			var selected = t === tab;
			t.setAttribute("aria-selected", selected ? "true" : "false");
			t.tabIndex = selected ? 0 : -1;
			var panel = panelFor(t);
			if (panel) {
				if (selected) {
					panel.removeAttribute("hidden");
				} else {
					panel.setAttribute("hidden", "");
				}
			}
		});
		if (setFocus) {
			tab.focus();
		}
	}

	tabs.forEach(function (tab) {
		tab.addEventListener("click", function (event) {
			event.preventDefault();
			activate(tab, true);
		});
	});

	if (tablist) {
		tablist.addEventListener("keydown", function (event) {
			var index = tabs.indexOf(document.activeElement);
			if (index === -1) {
				return;
			}
			var next = null;
			switch (event.key) {
				case "ArrowDown":
				case "ArrowLeft":
					next = (index + 1) % tabs.length;
					break;
				case "ArrowUp":
				case "ArrowRight":
					next = (index - 1 + tabs.length) % tabs.length;
					break;
				case "Home":
					next = 0;
					break;
				case "End":
					next = tabs.length - 1;
					break;
				default:
					return;
			}
			event.preventDefault();
			activate(tabs[next], true);
		});
	}

	// ── AJAX save ────────────────────────────────────────────────────────────
	var saveButton = dashboard.querySelector("[data-es-save]");
	var status = dashboard.querySelector("[data-es-save-status]");
	var config = window.ES_OPTIONS || {};

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

	function setPath(target, path, value) {
		var parts = path.replace(/\]/g, "").split("[");
		var current = target;
		for (var i = 0; i < parts.length; i++) {
			var part = parts[i];
			if (i === parts.length - 1) {
				current[part] = value;
			} else {
				var nextIsIndex = /^\d+$/.test(parts[i + 1]);
				if (current[part] === undefined) {
					current[part] = nextIsIndex ? [] : {};
				}
				current = current[part];
			}
		}
	}

	function collectLinkGroups(values) {
		// Link fields store an object {url, label, target}; collect each group
		// as a whole so the label/target are never lost on save.
		dashboard.querySelectorAll("[data-es-link-key]").forEach(function (group) {
			if (group.closest("template")) {
				return;
			}
			var key = group.getAttribute("data-es-link-key");
			var urlInput = group.querySelector("[data-es-link-url]");
			var labelInput = group.querySelector("[data-es-link-label]");
			var targetInput = group.querySelector("[data-es-link-target]");
			values[key] = {
				url: urlInput ? urlInput.value : "",
				label: labelInput ? labelInput.value : "",
				target: targetInput && targetInput.checked ? "_blank" : "_self"
			};
		});
	}

	function collectValues() {
		var values = {};
		collectLinkGroups(values);
		dashboard.querySelectorAll("[data-es-key]").forEach(function (input) {
			// Skip inputs that live inside a <template> (repeater prototype)
			// or inside a link group (already collected as an object).
			if (input.closest("template") || input.closest("[data-es-link-key]")) {
				return;
			}
			var key = input.getAttribute("data-es-key");
			var value = input.type === "checkbox" ? (input.checked ? 1 : 0) : input.value;
			if (key.indexOf("[") !== -1) {
				setPath(values, key, value);
			} else {
				values[key] = value;
			}
		});
		return values;
	}

	if (saveButton) {
		saveButton.addEventListener("click", function () {
			if (!config.ajaxUrl) {
				return;
			}
			saveButton.disabled = true;
			setStatus("در حال ذخیره…", "");

			var body = new URLSearchParams();
			body.append("action", config.action || "es_save_options");
			body.append("es_nonce", config.nonce || "");
			body.append("values", JSON.stringify(collectValues()));

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
						setStatus("تغییرات ذخیره شد.", "success");
					} else {
						var message = (result.data && result.data.data && result.data.data.message) || "خطا در ذخیره‌سازی.";
						setStatus(message, "error");
					}
				})
				.catch(function () {
					setStatus("خطای شبکه در ذخیره‌سازی.", "error");
				})
				.finally(function () {
					saveButton.disabled = false;
					dirty = false;
					saveButton.classList.remove("is-dirty");
				});
		});
	}

	// ── Unsaved-changes guard ────────────────────────────────────────────────
	var dirty = false;
	dashboard.addEventListener("input", function () {
		dirty = true;
		if (saveButton) {
			saveButton.classList.add("is-dirty");
		}
	});
	dashboard.addEventListener("change", function () {
		dirty = true;
		if (saveButton) {
			saveButton.classList.add("is-dirty");
		}
	});
	window.addEventListener("beforeunload", function (event) {
		if (dirty) {
			event.preventDefault();
			event.returnValue = "";
		}
	});
})();
