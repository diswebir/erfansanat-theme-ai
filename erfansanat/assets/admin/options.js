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
			var payload = { action: config.action, nonce: config.nonce, options: collect() };

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
