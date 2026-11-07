/**
 * Erfan Sanat admin panel — loaded only on the theme settings screen.
 * Plain ES5-compatible JS with no build step.
 */
(function () {
	'use strict';

	var config = window.erfansanatAdmin || {};
	var root = document.querySelector('.erfansanat-admin');
	if (!root) {
		return;
	}

	var status = root.querySelector('.erfansanat-admin__status');
	var form = document.getElementById('erfansanat-options-form');

	function setStatus(message, state) {
		if (!status) {
			return;
		}
		status.hidden = false;
		status.textContent = message;
		status.className = 'erfansanat-admin__status is-' + (state || 'info');
	}

	/* ── Tabs ─────────────────────────────────────────────── */
	var tabs = root.querySelectorAll('.erfansanat-admin__tab');
	var panels = root.querySelectorAll('.erfansanat-admin__panel');

	function activateTab(name) {
		for (var i = 0; i < tabs.length; i++) {
			var isActive = tabs[i].getAttribute('data-tab') === name;
			tabs[i].classList.toggle('is-active', isActive);
			tabs[i].setAttribute('aria-selected', isActive ? 'true' : 'false');
			tabs[i].tabIndex = isActive ? 0 : -1;
		}
		for (var j = 0; j < panels.length; j++) {
			var show = panels[j].getAttribute('data-panel') === name;
			panels[j].classList.toggle('is-active', show);
			panels[j].hidden = !show;
		}
	}

	for (var t = 0; t < tabs.length; t++) {
		tabs[t].addEventListener('click', function () {
			activateTab(this.getAttribute('data-tab'));
		});
		tabs[t].addEventListener('keydown', function (event) {
			var keys = ['ArrowLeft', 'ArrowRight', 'Home', 'End'];
			if (keys.indexOf(event.key) === -1) {
				return;
			}
			event.preventDefault();
			var index = Array.prototype.indexOf.call(tabs, this);
			if (event.key === 'ArrowLeft') {
				index = (index + 1) % tabs.length;
			} else if (event.key === 'ArrowRight') {
				index = (index - 1 + tabs.length) % tabs.length;
			} else if (event.key === 'Home') {
				index = 0;
			} else {
				index = tabs.length - 1;
			}
			tabs[index].focus();
			activateTab(tabs[index].getAttribute('data-tab'));
		});
	}

	/* ── Media picker ─────────────────────────────────────── */
	function initMedia(box) {
		var input = box.querySelector('[data-media-input]');
		var preview = box.querySelector('[data-media-preview]');
		var choose = box.querySelector('[data-media-select]');
		var clear = box.querySelector('[data-media-clear]');
		var frame = null;

		if (choose) {
			choose.addEventListener('click', function () {
				if (!window.wp || !window.wp.media) {
					return;
				}
				if (!frame) {
					frame = window.wp.media({
						title: config.strings.chooseImage,
						button: { text: config.strings.useImage },
						multiple: false
					});
					frame.on('select', function () {
						var attachment = frame.state().get('selection').first().toJSON();
						input.value = attachment.id;
						preview.src = attachment.sizes && attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
						preview.hidden = false;
					});
				}
				frame.open();
			});
		}
		if (clear) {
			clear.addEventListener('click', function () {
				input.value = '0';
				preview.src = '';
				preview.hidden = true;
			});
		}
	}

	var mediaBoxes = root.querySelectorAll('.erfansanat-media');
	for (var m = 0; m < mediaBoxes.length; m++) {
		initMedia(mediaBoxes[m]);
	}

	/* ── Repeaters ────────────────────────────────────────── */
	function nextIndex(rows) {
		return rows.querySelectorAll('.erfansanat-repeater__row').length;
	}

	function bindRow(row) {
		var remove = row.querySelector('[data-repeater-remove]');
		if (!remove) {
			return;
		}
		remove.addEventListener('click', function () {
			row.parentNode.removeChild(row);
		});
	}

	var repeaters = root.querySelectorAll('[data-repeater]');
	for (var r = 0; r < repeaters.length; r++) {
		var rows = repeaters[r].querySelector('[data-repeater-rows]');
		var template = repeaters[r].querySelector('[data-repeater-template]');
		var add = repeaters[r].querySelector('[data-repeater-add]');
		var existing = rows.querySelectorAll('.erfansanat-repeater__row');
		for (var e = 0; e < existing.length; e++) {
			bindRow(existing[e]);
		}
		if (add && template) {
			add.addEventListener('click', function () {
				var holder = this.parentNode;
				var list = holder.querySelector('[data-repeater-rows]');
				var tpl = holder.querySelector('[data-repeater-template]');
				var html = tpl.innerHTML.replace(/__INDEX__/g, String(nextIndex(list)));
				var wrapper = document.createElement('div');
				wrapper.innerHTML = html.trim();
				var row = wrapper.firstChild;
				bindRow(row);
				list.appendChild(row);
				var first = row.querySelector('input');
				if (first) {
					first.focus();
				}
			});
		}
	}


	/* ── Section manager ──────────────────────────────────── */
	var sectionsBox = root.querySelector('[data-sections]');
	if (sectionsBox) {
		var sectionList = sectionsBox.querySelector('[data-sections-list]');
		var sectionInput = sectionsBox.querySelector('[data-sections-input]');
		var dragged = null;

		function sectionRows() {
			return Array.prototype.slice.call(sectionList.querySelectorAll('.erfansanat-sections__item'));
		}

		function moveRow(row, direction) {
			if (direction === 'up' && row.previousElementSibling) {
				sectionList.insertBefore(row, row.previousElementSibling);
			} else if (direction === 'down' && row.nextElementSibling) {
				sectionList.insertBefore(row.nextElementSibling, row);
			} else {
				return;
			}
			syncSections();
			var button = row.querySelector('[data-move="' + direction + '"]');
			if (button) {
				button.focus();
			}
		}

		function readFieldValue(el) {
			if (el.type === 'checkbox') {
				return el.checked ? 1 : 0;
			}
			return el.value;
		}

		function setFieldValue(obj, path, value) {
			var parts = path.split('.');
			var cursor = obj;
			for (var i = 0; i < parts.length - 1; i++) {
				var part = parts[i];
				if (/^\d+$/.test(part)) {
					part = Number(part);
				}
				if (typeof cursor[part] === 'undefined' || cursor[part] === null) {
					cursor[part] = /^\d+$/.test(parts[i + 1]) ? [] : {};
				}
				cursor = cursor[part];
			}
			var last = parts[parts.length - 1];
			cursor[/^\d+$/.test(last) ? Number(last) : last] = value;
		}

		function collectSections() {
			var layout = [];
			var rows = sectionRows();
			for (var i = 0; i < rows.length; i++) {
				var row = rows[i];
				var entry = { key: row.getAttribute('data-key'), enabled: 0, fields: {} };
				var toggle = row.querySelector('[data-section-enabled]');
				entry.enabled = toggle && toggle.checked ? 1 : 0;
				var fields = row.querySelectorAll('[data-field]');
				for (var f = 0; f < fields.length; f++) {
					var path = fields[f].getAttribute('data-field');
					// Repeater rows are re-indexed by DOM position so a removed row
					// never leaves a hole that would drop a later item.
					var repeater = fields[f].closest('[data-section-repeater]');
					if (repeater) {
						var repeaterPath = repeater.getAttribute('data-section-repeater');
						var rowEl = fields[f].closest('[data-row-index]');
						var rowIndex = Array.prototype.indexOf.call(
							repeater.querySelectorAll('[data-repeater-rows] > [data-row-index]'),
							rowEl
						);
						path = path.replace(repeaterPath + '.', repeaterPath + '.' + rowIndex + '.');
					}
					setFieldValue(entry.fields, path, readFieldValue(fields[f]));
				}
				layout.push(entry);
			}
			return layout;
		}

		function syncSections() {
			if (sectionInput) {
				sectionInput.value = JSON.stringify(collectSections());
			}
		}

		sectionList.addEventListener('click', function (event) {
			var move = event.target.closest('[data-move]');
			if (move) {
				event.preventDefault();
				moveRow(move.closest('.erfansanat-sections__item'), move.getAttribute('data-move'));
				return;
			}
			var edit = event.target.closest('[data-section-edit]');
			if (edit) {
				event.preventDefault();
				var item = edit.closest('.erfansanat-sections__item');
				var box = item.querySelector('[data-section-fields]');
				var open = box.hidden;
				box.hidden = !open;
				edit.setAttribute('aria-expanded', open ? 'true' : 'false');
			}
		});

		// Keyboard: the whole row can be moved when focus is not inside a field.
		sectionList.addEventListener('keydown', function (event) {
			if (event.altKey && (event.key === 'ArrowUp' || event.key === 'ArrowDown')) {
				var row = event.target.closest('.erfansanat-sections__item');
				if (!row) {
					return;
				}
				event.preventDefault();
				moveRow(row, event.key === 'ArrowUp' ? 'up' : 'down');
			}
		});

		sectionList.addEventListener('change', function (event) {
			if (event.target.matches('[data-section-enabled], [data-field]')) {
				syncSections();
			}
		});
		sectionList.addEventListener('input', function (event) {
			if (event.target.matches('[data-field]')) {
				syncSections();
			}
		});

		// Native drag and drop.
		sectionList.addEventListener('dragstart', function (event) {
			var row = event.target.closest('.erfansanat-sections__item');
			if (!row) {
				return;
			}
			dragged = row;
			row.classList.add('is-dragging');
			if (event.dataTransfer) {
				event.dataTransfer.effectAllowed = 'move';
				event.dataTransfer.setData('text/plain', row.getAttribute('data-key'));
			}
		});
		sectionList.addEventListener('dragend', function () {
			if (dragged) {
				dragged.classList.remove('is-dragging');
			}
			dragged = null;
			syncSections();
		});
		sectionList.addEventListener('dragover', function (event) {
			event.preventDefault();
			var over = event.target.closest('.erfansanat-sections__item');
			if (!dragged || !over || over === dragged) {
				return;
			}
			var box = over.getBoundingClientRect();
			var after = event.clientY - box.top > box.height / 2;
			sectionList.insertBefore(dragged, after ? over.nextElementSibling : over);
		});
		sectionList.addEventListener('drop', function (event) {
			event.preventDefault();
			syncSections();
		});

		// Repeaters inside sections reuse the global add/remove behaviour.
		sectionList.addEventListener('click', function (event) {
			var remove = event.target.closest('[data-repeater-remove]');
			if (remove) {
				var row = remove.closest('[data-row-index]');
				if (row) {
					row.parentNode.removeChild(row);
					syncSections();
				}
				return;
			}
			var add = event.target.closest('[data-repeater-add]');
			if (add) {
				var holder = add.closest('[data-section-repeater]');
				var list = holder.querySelector('[data-repeater-rows]');
				var tpl = holder.querySelector('[data-repeater-template]');
				var html = tpl.innerHTML.replace(/__INDEX__/g, String(list.children.length));
				var wrapper = document.createElement('div');
				wrapper.innerHTML = html.trim();
				list.appendChild(wrapper.firstChild);
				syncSections();
			}
		});

		// Media pickers inside sections.
		var sectionMedia = sectionList.querySelectorAll('.erfansanat-media');
		for (var sm = 0; sm < sectionMedia.length; sm++) {
			initMedia(sectionMedia[sm]);
			(function (box) {
				var input = box.querySelector('[data-media-input]');
				if (input) {
					input.addEventListener('change', syncSections);
				}
			})(sectionMedia[sm]);
		}

		syncSections();
	}

	/* ── Collect form values ──────────────────────────────── */
	function collect() {
		var data = {};
		var fields = form.querySelectorAll('input[name], textarea[name], select[name]');

		function assign(name, value) {
			var links = name.match(/^([a-z0-9_]+)\[(\d+)\]\[(title|url)\]$/);
			if (links) {
				data[links[1]] = data[links[1]] || [];
				data[links[1]][Number(links[2])] = data[links[1]][Number(links[2])] || {};
				data[links[1]][Number(links[2])][links[3]] = value;
				return;
			}
			var list = name.match(/^([a-z0-9_]+)\[\]$/);
			if (list) {
				data[list[1]] = data[list[1]] || [];
				data[list[1]].push(value);
				return;
			}
			data[name] = value;
		}

		for (var i = 0; i < fields.length; i++) {
			var field = fields[i];
			if (field.disabled) {
				continue;
			}
			// The layout input holds JSON rebuilt from the section list; its inner
			// field inputs are collected separately by collectSections().
			if (field.hasAttribute('data-sections-input') || field.hasAttribute('data-field')) {
				continue;
			}
			if (field.type === 'checkbox') {
				assign(field.name, field.checked ? 1 : 0);
			} else if (field.tagName === 'SELECT' && field.multiple) {
				var chosen = [];
				for (var s = 0; s < field.options.length; s++) {
					if (field.options[s].selected) {
						chosen.push(field.options[s].value);
					}
				}
				assign(field.name, chosen);
			} else if (field.type !== 'button' && field.type !== 'submit') {
				assign(field.name, field.value);
			}
		}

		// Multi-select arrays were collected as a single entry; normalise them.
		Object.keys(data).forEach(function (key) {
			if (key.slice(-2) === '[]') {
				var clean = key.slice(0, -2);
				data[clean] = data[key];
				delete data[key];
			}
		});

		return data;
	}

	if (form) {
		form.addEventListener('submit', function (event) {
			event.preventDefault();
			var button = form.querySelector('.button-primary');
			var options = collect();
			if (sectionsBox && sectionInput) {
				syncSections();
				options.home_sections = sectionInput.value;
			}
			var payload = { action: config.action, nonce: config.nonce, options: options };

			if (button) {
				button.disabled = true;
			}
			setStatus(config.strings.saving, 'info');

			window.fetch(config.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: new URLSearchParams(payload).toString()
			}).then(function (response) {
				return response.json();
			}).then(function (json) {
				if (json && json.success) {
					setStatus(json.data && json.data.message ? json.data.message : config.strings.saved, 'success');
					if (json.data && json.data.errors) {
						var names = Object.keys(json.data.errors);
						if (names.length) {
							setStatus(config.strings.saved + ' ' + names.join(', '), 'warning');
						}
					}
				} else {
					var message = json && json.data && json.data.message ? json.data.message : config.strings.error;
					setStatus(message, 'error');
				}
			}).catch(function () {
				setStatus(config.strings.error, 'error');
			}).then(function () {
				if (button) {
					button.disabled = false;
				}
			});
		});
	}
})();
