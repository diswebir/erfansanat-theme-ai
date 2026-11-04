(function () {
	'use strict';

	var form = document.querySelector('[data-es-sections-form]');
	if (!form) {
		return;
	}

	var activeList = form.querySelector('[data-es-section-list]');
	var availableList = form.querySelector('[data-es-section-available]');
	var status = form.querySelector('[data-es-sections-status]');
	var dragged = null;
	if (!activeList || !availableList) {
		return;
	}

	function cardFor(target) {
		return target && typeof target.closest === 'function' ? target.closest('[data-es-section-item]') : null;
	}

	function cardsIn(list) {
		return Array.prototype.slice.call(list.querySelectorAll('[data-es-section-item]'));
	}

	function updateCard(card) {
		var available = card.parentNode === availableList;
		var add = card.querySelector('[data-es-section-add]');
		var remove = card.querySelector('[data-es-section-remove]');
		var enabled = card.querySelector('[data-es-section-enabled]');
		card.classList.toggle('is-available', available);
		if (add) { add.hidden = !available; }
		if (remove) { remove.hidden = available; }
		if (enabled) { enabled.checked = !available && enabled.checked; }
	}

	function announce(message) {
		if (status) { status.textContent = message; }
	}

	function moveBefore(card, reference) {
		if (!card || !reference || card === reference || !reference.parentNode) { return; }
		reference.parentNode.insertBefore(card, reference);
		updateCard(card);
	}

	function moveBy(card, delta) {
		if (!card || !card.parentNode) { return; }
		var siblings = cardsIn(card.parentNode);
		var index = siblings.indexOf(card);
		var nextIndex = index + delta;
		if (nextIndex < 0 || nextIndex >= siblings.length) { return; }
		if (delta < 0) {
			moveBefore(card, siblings[nextIndex]);
		} else {
			var after = siblings[nextIndex].nextElementSibling;
			if (after) { moveBefore(card, after); } else { card.parentNode.appendChild(card); }
		}
		var handle = card.querySelector('[data-es-section-handle]');
		if (handle && typeof handle.focus === 'function') { handle.focus(); }
	}

	function fieldValue(field) {
		var type = field.getAttribute('data-es-section-field-type') || '';
		var controls = Array.prototype.slice.call(field.querySelectorAll('input, textarea, select'));
		if (type === 'toggle') {
			var toggle = controls.find(function (control) { return control.type === 'checkbox'; });
			return !!(toggle && toggle.checked);
		}
		if (type === 'checkbox-group') {
			return controls.filter(function (control) { return control.type === 'checkbox' && control.checked; }).map(function (control) { return control.value; });
		}
		if (type === 'repeater') {
			var rows = Array.prototype.slice.call(field.querySelectorAll('[data-es-repeater-item]'));
			var result = {};
			rows.forEach(function (row, index) {
				var rowKey = row.getAttribute('data-es-repeater-item') || ('item-' + index);
				var values = {};
				Array.prototype.slice.call(row.querySelectorAll('input, textarea, select')).forEach(function (control) {
					var match = (control.name || '').match(/\[([A-Za-z][A-Za-z0-9_-]*)\](?:\[\])?$/);
					if (!match) { return; }
					if (control.type === 'checkbox') {
						if (control.checked) { values[match[1]] = control.value; }
					} else if (control.type !== 'hidden' || !values[match[1]]) {
						values[match[1]] = control.value;
					}
				});
				result[rowKey] = values;
			});
			return result;
		}
		var control = controls.find(function (item) { return item.type !== 'hidden' || item.classList.contains('es-media__value'); }) || controls[0];
		return control ? control.value : '';
	}

	function buildState() {
		var active = cardsIn(activeList);
		var available = cardsIn(availableList);
		var all = active.concat(available);
		var order = all.map(function (card) { return card.getAttribute('data-es-section-key'); });
		var availableKeys = available.map(function (card) { return card.getAttribute('data-es-section-key'); });
		var enabled = {};
		var data = {};
		all.forEach(function (card) {
			var key = card.getAttribute('data-es-section-key');
			var control = card.querySelector('[data-es-section-enabled]');
			enabled[key] = !!(control && control.checked && card.parentNode === activeList);
			data[key] = {};
			Array.prototype.slice.call(card.querySelectorAll('[data-es-section-field]')).forEach(function (field) {
				data[key][field.getAttribute('data-es-section-field')] = fieldValue(field);
			});
		});
		return { order: order, enabled: enabled, data: data, available: availableKeys };
	}

	document.addEventListener('click', function (event) {
		var card = cardFor(event.target);
		if (!card) { return; }
		var button = event.target.closest('button');
		if (!button) { return; }
		if (button.matches('[data-es-section-add]')) {
			event.preventDefault();
			activeList.appendChild(card);
			var checkbox = card.querySelector('[data-es-section-enabled]');
			if (checkbox) { checkbox.checked = true; }
			updateCard(card);
			announce('سکشن به چیدمان افزوده شد.');
		} else if (button.matches('[data-es-section-remove]')) {
			event.preventDefault();
			availableList.appendChild(card);
			var enabled = card.querySelector('[data-es-section-enabled]');
			if (enabled) { enabled.checked = false; }
			updateCard(card);
			announce('سکشن از چیدمان حذف و غیرفعال شد.');
		} else if (button.matches('[data-es-section-up]')) {
			event.preventDefault();
			moveBy(card, -1);
		} else if (button.matches('[data-es-section-down]')) {
			event.preventDefault();
			moveBy(card, 1);
		} else if (button.matches('[data-es-section-toggle-editor]')) {
			event.preventDefault();
			var editor = card.querySelector('[data-es-section-editor]');
			var expanded = button.getAttribute('aria-expanded') === 'true';
			button.setAttribute('aria-expanded', expanded ? 'false' : 'true');
			if (editor) { editor.hidden = expanded; }
		}
	});

	document.addEventListener('keydown', function (event) {
		var handle = event.target.closest && event.target.closest('[data-es-section-handle]');
		if (!handle || (event.key !== 'ArrowUp' && event.key !== 'ArrowDown')) { return; }
		event.preventDefault();
		moveBy(cardFor(handle), event.key === 'ArrowUp' ? -1 : 1);
		announce('ترتیب سکشن تغییر کرد.');
	});

	form.addEventListener('change', function (event) {
		if (event.target.matches('[data-es-section-enabled]')) {
			var card = cardFor(event.target);
			if (card && card.parentNode === availableList && event.target.checked) {
				activeList.appendChild(card);
				updateCard(card);
			}
		}
	});

	[activeList, availableList].forEach(function (list) {
		list.addEventListener('dragstart', function (event) {
			var card = cardFor(event.target);
			if (!card) { return; }
			dragged = card;
			if (event.dataTransfer) {
				event.dataTransfer.effectAllowed = 'move';
				event.dataTransfer.setData('text/plain', card.getAttribute('data-es-section-key') || '');
			}
			var handle = card.querySelector('[data-es-section-handle]');
			if (handle) { handle.setAttribute('aria-grabbed', 'true'); }
		});
		list.addEventListener('dragover', function (event) {
			if (dragged) { event.preventDefault(); }
		});
		list.addEventListener('drop', function (event) {
			if (!dragged) { return; }
			event.preventDefault();
			var target = cardFor(event.target);
			if (target && target.parentNode === list) { moveBefore(dragged, target); }
			else { list.appendChild(dragged); }
			if (list === availableList) {
				var checkbox = dragged.querySelector('[data-es-section-enabled]');
				if (checkbox) { checkbox.checked = false; }
			} else {
				var activeCheckbox = dragged.querySelector('[data-es-section-enabled]');
				if (activeCheckbox) { activeCheckbox.checked = true; }
			}
			updateCard(dragged);
			var handle = dragged.querySelector('[data-es-section-handle]');
			if (handle) { handle.setAttribute('aria-grabbed', 'false'); }
			dragged = null;
		});
	});

	form.addEventListener('submit', function (event) {
		event.preventDefault();
		var endpoint = form.getAttribute('data-ajax-url') || window.ajaxurl;
		if (!endpoint || typeof window.fetch !== 'function') {
			announce('نشانی ذخیره‌سازی در دسترس نیست.');
			return;
		}
		var payload = new FormData(form);
		payload.append('action', 'es_save_sections');
		payload.append('state', JSON.stringify(buildState()));
		announce('در حال ذخیره تغییرات…');
		var submit = form.querySelector('[type="submit"]');
		if (submit) { submit.disabled = true; }
		window.fetch(endpoint, { method: 'POST', credentials: 'same-origin', body: payload })
			.then(function (response) {
				return response.json().then(function (body) {
					if (!response.ok || !body || body.success !== true) {
						throw new Error(body && body.data && body.data.message ? body.data.message : 'ذخیره سکشن‌ها انجام نشد.');
					}
					return body;
				});
			})
			.then(function () { announce('تغییرات سکشن‌ها با موفقیت ذخیره شد.'); })
			.catch(function (error) { announce(error.message || 'خطا در ذخیره سکشن‌ها.'); })
			.finally(function () { if (submit) { submit.disabled = false; } });
	});

	cardsIn(activeList).concat(cardsIn(availableList)).forEach(updateCard);
})();
