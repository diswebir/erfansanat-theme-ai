/**
 * داشبورد اختصاصی عرفان صنعت — JS ادمین (Vanilla، بدون jQuery/CDN)
 * کامپوننت‌ها: تب‌ها، ذخیره ایجکس (FormData با نام‌های nesting)، پیکر رسانه،
 * رنگ، سوئیچ و Repeater (افزودن/حذف/جابه‌جایی با درگ ساده).
 */
(function () {
	'use strict';

	function qs(sel, root) { return (root || document).querySelector(sel); }
	function qsa(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

	function toast(msg, type) {
		var t = document.createElement('div');
		t.className = 'es-admin-toast es-admin-toast--' + (type || 'info');
		t.textContent = msg;
		document.body.appendChild(t);
		requestAnimationFrame(function () { t.classList.add('is-visible'); });
		setTimeout(function () {
			t.classList.remove('is-visible');
			setTimeout(function () { t.remove(); }, 300);
		}, 3200);
	}

	function initTabs(root) {
		qsa('[data-es-tab-btn]', root).forEach(function (btn) {
			btn.addEventListener('click', function () {
				var id = btn.getAttribute('data-es-tab-btn');
				qsa('[data-es-tab-btn]', root).forEach(function (b) {
					var active = b === btn;
					b.classList.toggle('is-active', active);
					b.setAttribute('aria-selected', active ? 'true' : 'false');
				});
				qsa('[data-es-tab-panel]', root).forEach(function (p) {
					var match = p.getAttribute('data-es-tab-panel') === id;
					p.classList.toggle('is-active', match);
					p.hidden = !match;
				});
			});
		});
	}

	function initSave(root) {
		var btn = qs('[data-es-save]', root);
		var form = qs('[data-es-options-form]', root);
		if (!btn || !form) { return; }

		btn.addEventListener('click', function () {
			btn.disabled = true;
			btn.classList.add('is-busy');

			var data = new FormData(form);
			data.append('action', (window.ESAdmin && window.ESAdmin.action) || 'es_theme_options_save');
			data.append('nonce', (window.ESAdmin && window.ESAdmin.nonce) || '');

			fetch((window.ESAdmin && window.ESAdmin.ajaxUrl) || window.location.href, {
				method: 'POST',
				credentials: 'same-origin',
				body: data
			}).then(function (res) { return res.json(); }).then(function (json) {
				btn.disabled = false;
				btn.classList.remove('is-busy');
				if (json && json.success) {
					toast(json.data && json.data.message ? json.data.message : (window.ESAdmin && window.ESAdmin.savedText) || 'ذخیره شد', 'success');
					document.dispatchEvent(new CustomEvent('es-options-saved', { detail: json }));
				} else {
					toast((json && json.data && json.data.message) || (window.ESAdmin && window.ESAdmin.errorText) || 'خطا', 'error');
				}
			}).catch(function () {
				btn.disabled = false;
				btn.classList.remove('is-busy');
				toast((window.ESAdmin && window.ESAdmin.networkText) || 'خطای شبکه', 'error');
			});
		});
	}

	function initMedia(root) {
		qsa('[data-es-media]', root).forEach(function (box) {
			var input = qs('[data-es-media-value]', box);
			var preview = qs('[data-es-media-preview]', box);
			var selectBtn = qs('[data-es-media-select]', box);
			var clearBtn = qs('[data-es-media-clear]', box);
			if (!input || !selectBtn) { return; }

			function setPreview(url) {
				if (!preview) { return; }
				if (url) {
					preview.src = url;
					preview.hidden = false;
				} else {
					preview.removeAttribute('src');
					preview.hidden = true;
				}
			}

			selectBtn.addEventListener('click', function () {
				if (!window.wp || !window.wp.media) {
					// Fallback: درخواست آدرس (بدون وابستگی)
					var url = window.prompt('آدرس فایل (URL):');
					if (url) { input.value = url; setPreview(url); }
					return;
				}
				var frame = window.wp.media({
					title: 'انتخاب رسانه',
					multiple: false,
					library: { type: 'image' }
				});
				frame.on('select', function () {
					var att = frame.state().get('selection').first().toJSON();
					input.value = att.id;
					setPreview((att.sizes && att.sizes.thumbnail && att.sizes.thumbnail.url) || att.url);
				});
				frame.open();
			});

			if (clearBtn) {
				clearBtn.addEventListener('click', function () {
					input.value = '';
					setPreview('');
				});
			}
		});
	}

	function initColors(root) {
		qsa('[data-es-color]', root).forEach(function (picker) {
			picker.addEventListener('input', function () {
				var code = picker.parentElement && qs('[data-es-color-code]', picker.parentElement);
				if (code) { code.textContent = picker.value; }
			});
		});
	}

	function renumberRows(container) {
		qsa('[data-es-repeater-row]', container).forEach(function (row, idx) {
			row.setAttribute('data-index', String(idx));
			var num = qs('.es-repeater__row-num', row);
			if (num) { num.textContent = String(idx + 1); }
			qsa('input, select, textarea', row).forEach(function (el) {
				var name = el.getAttribute('name');
				if (!name) { return; }
				el.setAttribute('name', name.replace(/\[\d+\]/, '[' + idx + ']'));
			});
		});
	}

	function initRepeaters(root) {
		qsa('[data-es-repeater]', root).forEach(function (rep) {
			var rowsBox = qs('[data-es-repeater-rows]', rep);

			var addBtn = qs('[data-es-repeater-add]', rep);
			if (addBtn && rowsBox) {
				addBtn.addEventListener('click', function () {
					var rows = qsa('[data-es-repeater-row]', rowsBox);
					var src = rows.length ? rows[rows.length - 1] : null;
					var newRow;
					if (src) {
						newRow = src.cloneNode(true);
						qsa('input', newRow).forEach(function (el) {
							if (el.type === 'checkbox') { el.checked = false; }
							else { el.value = ''; }
						});
						var prevHidden = qs('input[type="hidden"]', newRow);
						if (prevHidden && prevHidden.value === '0') { prevHidden.value = '0'; }
						qsa('img', newRow).forEach(function (img) { img.hidden = true; img.removeAttribute('src'); });
					} else {
						newRow = document.createElement('div');
						newRow.className = 'es-repeater__row';
						newRow.setAttribute('data-es-repeater-row', '');
						newRow.innerHTML = '<div class="es-repeater__row-head"><span class="es-repeater__drag" data-es-repeater-move>⠿</span><span class="es-repeater__row-num"></span><button type="button" class="es-repeater__remove" data-es-repeater-remove>✕</button></div><div class="es-repeater__row-fields"></div>';
					}
					rowsBox.appendChild(newRow);
					renumberRows(rowsBox);
					newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
				});
			}

			rowsBox.addEventListener('click', function (e) {
				var target = e.target;
				if (!(target instanceof Element)) { return; }
				var removeBtn = target.closest('[data-es-repeater-remove]');
				if (removeBtn && rowsBox.contains(removeBtn)) {
					var row = removeBtn.closest('[data-es-repeater-row]');
					if (row) { row.remove(); renumberRows(rowsBox); }
					return;
				}
				var moveHandle = target.closest('[data-es-repeater-move]');
				if (moveHandle) {
					var current = moveHandle.closest('[data-es-repeater-row]');
					if (!current || !window.__esDragging) { return; }
				}
			});

			// جابه‌جایی با HTML5 Drag & Drop
			rowsBox.addEventListener('dragstart', function (e) {
				var row = e.target instanceof Element ? e.target.closest('[data-es-repeater-row]') : null;
				if (row) {
					window.__esDragging = row;
					row.style.opacity = '0.5';
				}
			});
			rowsBox.addEventListener('dragend', function () {
				if (window.__esDragging) {
					window.__esDragging.style.opacity = '';
					window.__esDragging = null;
				}
				renumberRows(rowsBox);
			});
			rowsBox.addEventListener('dragover', function (e) {
				e.preventDefault();
				var dragging = window.__esDragging;
				if (!dragging) { return; }
				var over = e.target instanceof Element ? e.target.closest('[data-es-repeater-row]') : null;
				if (over && over !== dragging && rowsBox.contains(over)) {
					var rect = over.getBoundingClientRect();
					var before = (e.clientY - rect.top) < rect.height / 2;
					rowsBox.insertBefore(dragging, before ? over : over.nextSibling);
				}
			});
			qsa('[data-es-repeater-move]', rowsBox).forEach(function (handle) {
				handle.setAttribute('draggable', 'true');
			});
			rowsBox.addEventListener('mousedown', function (e) {
				var target = e.target instanceof Element ? e.target.closest('[data-es-repeater-move]') : null;
				if (target) {
					var row = target.closest('[data-es-repeater-row]');
					if (row) { row.setAttribute('draggable', 'true'); }
				}
			});
		});
	}

	function boot() {
		var root = qs('[data-es-options]');
		if (!root) { return; }
		initTabs(root);
		initSave(root);
		initMedia(root);
		initColors(root);
		initRepeaters(root);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
