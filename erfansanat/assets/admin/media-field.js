/**
 * Erfan Sanat media / link / icon field wiring (wp.media).
 * Loaded only on the dashboard screen.
 */
(function () {
	"use strict";

	if (typeof wp === "undefined" || !wp.media) {
		return;
	}

	function initMedia(container) {
		var input = container.querySelector("[data-es-key]");
		var preview = container.querySelector("[data-es-media-preview]");
		var selectBtn = container.querySelector("[data-es-media-select]");
		var removeBtn = container.querySelector("[data-es-media-remove]");
		var frame = null;

		function setValue(id, url) {
			if (input) {
				input.value = id || 0;
				input.dispatchEvent(new Event("change", { bubbles: true }));
			}
			if (preview) {
				preview.innerHTML = url ? '<img src="' + url + '" alt="" class="es-media__img" />' : "";
			}
			if (removeBtn) {
				if (id) {
					removeBtn.removeAttribute("hidden");
				} else {
					removeBtn.setAttribute("hidden", "");
				}
			}
		}

		if (selectBtn) {
			selectBtn.addEventListener("click", function (event) {
				event.preventDefault();
				if (!frame) {
					frame = wp.media({
						title: "انتخاب رسانه",
						button: { text: "استفاده از این رسانه" },
						multiple: false
					});
					frame.on("select", function () {
						var attachment = frame.state().get("selection").first().toJSON();
						var url = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : attachment.url;
						setValue(attachment.id, url);
					});
				}
				frame.open();
			});
		}

		if (removeBtn) {
			removeBtn.addEventListener("click", function (event) {
				event.preventDefault();
				setValue(0, "");
			});
		}
	}

	document.querySelectorAll("[data-es-media]").forEach(initMedia);

	// Link fields: keep rel/target safe on the client as a convenience.
	document.querySelectorAll("[data-es-link]").forEach(function (container) {
		var target = container.querySelector("[data-es-link-target]");
		if (target) {
			target.addEventListener("change", function () {
				// Server enforces rel=noopener noreferrer for _blank; nothing to leak here.
			});
		}
	});
})();
