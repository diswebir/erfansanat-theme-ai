(function () {
	'use strict';

	if (!window.wp || typeof window.wp.media !== 'function') {
		return;
	}

	function notifyChange(input) {
		input.dispatchEvent(new Event('input', { bubbles: true }));
		input.dispatchEvent(new Event('change', { bubbles: true }));
	}

	function selectedAttachments(frame) {
		var selection = frame.state().get('selection');
		if (selection && typeof selection.toJSON === 'function') {
			return selection.toJSON();
		}
		if (selection && typeof selection.toArray === 'function') {
			return selection.toArray().map(function (model) {
				return model.attributes || {};
			});
		}
		return [];
	}

	function imageUrl(attachment) {
		return (attachment.sizes && attachment.sizes.thumbnail && attachment.sizes.thumbnail.url) || attachment.url || '';
	}

	function safeURL(value) {
		try {
			var url = new URL(value, window.location.href);
			return url.protocol === 'https:' || url.protocol === 'http:' ? url.href : '';
		} catch (error) {
			return '';
		}
	}

	function makePreview(attachment) {
		var wrapper = document.createElement('span');
		wrapper.className = 'es-media__item';
		var isImage = attachment.type === 'image' || (attachment.mime && attachment.mime.indexOf('image/') === 0);
		var previewURL = safeURL(imageUrl(attachment));
		if (isImage && previewURL) {
			var image = document.createElement('img');
			image.className = 'es-media__preview-image';
			image.src = previewURL;
			image.alt = attachment.alt || attachment.title || '';
			wrapper.appendChild(image);
		} else {
			var fileURL = safeURL(attachment.url || '');
			var fileName = attachment.filename || attachment.title || 'فایل انتخاب‌شده';
			if (fileURL) {
				var link = document.createElement('a');
				link.href = fileURL;
				link.textContent = fileName;
				link.rel = 'noopener';
				wrapper.appendChild(link);
			} else {
				var label = document.createElement('span');
				label.textContent = fileName;
				wrapper.appendChild(label);
			}
		}
		return wrapper;
	}

	function openPicker(control) {
		var input = control.querySelector('.es-media__value');
		var preview = control.querySelector('.es-media__preview');
		var multiple = control.getAttribute('data-es-media-mode') === 'multiple';
		if (!input || !preview) {
			return;
		}

		var frame = window.wp.media({
			title: 'انتخاب رسانه',
			button: { text: 'استفاده از رسانه' },
			library: control.getAttribute('data-es-media-kind') === 'image' ? { type: 'image' } : (control.getAttribute('data-es-media-kind') === 'pdf' ? { type: 'application/pdf' } : {}),
			multiple: multiple
		});

		frame.on('select', function () {
			var attachments = selectedAttachments(frame).filter(function (item) {
				return item && Number.isInteger(Number(item.id)) && Number(item.id) > 0;
			});
			if (!multiple) {
				attachments = attachments.slice(0, 1);
			}
			if (control.getAttribute('data-es-media-value') === 'url') {
				input.value = attachments[0] && attachments[0].url ? String(attachments[0].url) : '';
			} else {
				input.value = attachments.map(function (item) { return String(Number(item.id)); }).filter(Boolean).join(',');
			}
			preview.replaceChildren();
			attachments.forEach(function (item) {
				preview.appendChild(makePreview(item));
			});
			notifyChange(input);
		});
		frame.open();
	}

	document.addEventListener('click', function (event) {
		var select = event.target.closest('[data-es-media-select]');
		if (select) {
			event.preventDefault();
			var control = select.closest('[data-es-media]');
			if (control) {
				openPicker(control);
			}
			return;
		}

		var remove = event.target.closest('[data-es-media-remove]');
		if (remove) {
			event.preventDefault();
			var removeControl = remove.closest('[data-es-media]');
			var input = removeControl && removeControl.querySelector('.es-media__value');
			var preview = removeControl && removeControl.querySelector('.es-media__preview');
			if (input && preview) {
				input.value = '';
				preview.replaceChildren();
				notifyChange(input);
			}
		}
	});
})();
