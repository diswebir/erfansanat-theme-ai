(function () {
	'use strict';

	var params = new URLSearchParams(window.location.search);
	var current = params.get('page');

	document.querySelectorAll('.es-admin [data-es-admin-nav]').forEach(function (link) {
		try {
			var target = new URL(link.href, window.location.href);
			if (current && target.searchParams.get('page') === current) {
				link.setAttribute('aria-current', 'page');
			}
		} catch (error) {
			// A malformed link must not prevent the rest of the admin screen working.
		}
	});

	var tablist = document.querySelector('.es-admin__settings-tabs');
	if (tablist) {
		var tabs = Array.prototype.slice.call(tablist.querySelectorAll('[data-es-settings-tab]'));
		var panels = Array.prototype.slice.call(document.querySelectorAll('[data-es-settings-panel]'));

		function activateTab(tab, moveFocus) {
			var selected = tab.getAttribute('data-es-settings-tab');
			tabs.forEach(function (item) {
				var active = item === tab;
				item.setAttribute('aria-selected', active ? 'true' : 'false');
				item.tabIndex = active ? 0 : -1;
			});
			panels.forEach(function (panel) {
				panel.hidden = panel.getAttribute('data-es-settings-panel') !== selected;
			});
			if (moveFocus) {
				tab.focus();
			}
		}

		tabs.forEach(function (tab, index) {
			tab.addEventListener('click', function () { activateTab(tab, false); });
			tab.addEventListener('keydown', function (event) {
				var next = index;
				if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
					next = (index + 1) % tabs.length;
				} else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
					next = (index + tabs.length - 1) % tabs.length;
				} else if (event.key === 'Home') {
					next = 0;
				} else if (event.key === 'End') {
					next = tabs.length - 1;
				} else {
					return;
				}
				event.preventDefault();
				activateTab(tabs[next], true);
			});
		});
	}

	var form = document.querySelector('[data-es-options-form]');
	if (!form) {
		return;
	}
	form.addEventListener('submit', function (event) {
		event.preventDefault();
		var status = form.querySelector('[data-es-save-status]');
		var submit = form.querySelector('[type="submit"]');
		var payload = new FormData(form);
		payload.append('action', 'es_save_options');
		if (status) {
			status.textContent = 'در حال ذخیره…';
		}
		if (submit) {
			submit.disabled = true;
		}
		var endpoint = (window.esAdmin && window.esAdmin.ajaxUrl) || window.ajaxurl;
		if (!endpoint) {
			if (status) { status.textContent = 'نشانی ذخیره‌سازی در دسترس نیست.'; }
			if (submit) { submit.disabled = false; }
			return;
		}
		window.fetch(endpoint, { method: 'POST', credentials: 'same-origin', body: payload })
			.then(function (response) {
				return response.json().then(function (body) {
					if (!response.ok || !body || body.success !== true) {
						throw new Error(body && body.data && body.data.message ? body.data.message : 'ذخیره تنظیمات انجام نشد.');
					}
					return body;
				});
			})
			.then(function () {
				if (status) { status.textContent = 'تغییرات با موفقیت ذخیره شد.'; }
			})
			.catch(function (error) {
				if (status) { status.textContent = error.message || 'خطا در ذخیره تنظیمات.'; }
			})
			.finally(function () {
				if (submit) { submit.disabled = false; }
			});
	});
})();
