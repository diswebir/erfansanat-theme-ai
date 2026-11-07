(function () {
	'use strict';

	function initProductTabs() {
		var containers = document.querySelectorAll('[data-prod-tabs-container="true"]');
		Array.prototype.forEach.call(containers, function (container) {
			var buttons = Array.prototype.slice.call(container.querySelectorAll('[data-prod-tab-btn]'));
			var panels = Array.prototype.slice.call(container.querySelectorAll('[data-prod-tab-panel]'));
			if (!buttons.length || !panels.length) { return; }

			function activate(button, moveFocus) {
				var target = button.getAttribute('data-prod-tab-btn');
				buttons.forEach(function (item, index) {
					var selected = item === button;
					item.setAttribute('role', 'tab');
					item.setAttribute('aria-selected', selected ? 'true' : 'false');
					item.tabIndex = selected ? 0 : -1;
					if (!item.id) { item.id = 'es-product-tab-' + index; }
				});
				panels.forEach(function (panel) {
					var selected = panel.getAttribute('data-prod-tab-panel') === target;
					panel.setAttribute('role', 'tabpanel');
					panel.hidden = !selected;
					panel.setAttribute('aria-hidden', selected ? 'false' : 'true');
					var activeButton = buttons.find(function (item) { return item.getAttribute('data-prod-tab-btn') === target; });
					if (activeButton) {
						if (!panel.id) { panel.id = 'es-product-panel-' + target; }
						panel.setAttribute('aria-labelledby', activeButton.id);
						activeButton.setAttribute('aria-controls', panel.id);
					}
				});
				if (moveFocus) { button.focus(); }
			}

			var initial = buttons.find(function (button) { return button.getAttribute('aria-selected') === 'true'; }) || buttons[0];
			activate(initial, false);
			buttons.forEach(function (button, index) {
				button.addEventListener('click', function () { activate(button, false); });
				button.addEventListener('keydown', function (event) {
					var next = index;
					if (event.key === 'ArrowRight' || event.key === 'ArrowDown') { next = (index + 1) % buttons.length; }
					else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') { next = (index + buttons.length - 1) % buttons.length; }
					else if (event.key === 'Home') { next = 0; }
					else if (event.key === 'End') { next = buttons.length - 1; }
					else { return; }
					event.preventDefault();
					activate(buttons[next], true);
				});
			});
		});
	}

	function initReviewRating() {
		var groups = document.querySelectorAll('[data-review-stars-group="true"]');
		Array.prototype.forEach.call(groups, function (group) {
			var form = group.closest('form');
			var input = form && form.querySelector('[data-review-rating-input="true"]');
			var label = group.parentNode && group.parentNode.querySelector('[data-review-rating-label="true"]');
			var buttons = Array.prototype.slice.call(group.querySelectorAll('[data-review-star]'));
			function selectRating(value) {
				if (input) { input.value = String(value); }
				if (label) { label.textContent = value + ' از ۵'; }
				buttons.forEach(function (button) {
					var active = Number(button.getAttribute('data-review-star')) <= value;
					button.setAttribute('aria-pressed', Number(button.getAttribute('data-review-star')) === value ? 'true' : 'false');
					var icon = button.querySelector('[data-review-star-svg="true"]');
					if (icon) {
						icon.classList.toggle('text-amber-400', active);
						icon.classList.toggle('text-slate-300', !active);
					}
				});
			}
			buttons.forEach(function (button) {
				button.addEventListener('click', function () { selectRating(Number(button.getAttribute('data-review-star')) || 0); });
			});
			if (input) { input.addEventListener('change', function () { selectRating(Number(input.value) || 0); }); }
		});
	}

	function initFaqAccessibility() {
		var toggles = document.querySelectorAll('[data-faq-toggle]');
		Array.prototype.forEach.call(toggles, function (button) {
			button.addEventListener('click', function () {
				var list = button.closest('[data-faq-list="true"]');
				var index = button.getAttribute('data-faq-toggle');
				var body = list && list.querySelector('[data-faq-body="' + index + '"]');
				if (body) { button.setAttribute('aria-expanded', body.classList.contains('hidden') ? 'false' : 'true'); }
			});
		});
	}

	function fallbackCopy(value) {
		var input = document.createElement('textarea');
		input.value = value;
		input.setAttribute('readonly', 'readonly');
		input.style.position = 'fixed';
		input.style.opacity = '0';
		document.body.appendChild(input);
		input.select();
		var copied = false;
		try { copied = document.execCommand('copy'); } catch (error) { copied = false; }
		document.body.removeChild(input);
		return copied;
	}

	function initCopyButtons() {
		document.addEventListener('click', function (event) {
			var button = event.target.closest && event.target.closest('[data-copy-button]');
			if (!button) { return; }
			event.preventDefault();
			var value = button.getAttribute('data-copy-value') || '';
			var status = button.parentNode && button.parentNode.querySelector('[data-copy-status]');
			var done = function (copied) {
				if (status) { status.textContent = copied ? 'کپی شد' : 'کپی انجام نشد'; }
				button.setAttribute('aria-label', copied ? 'شناسه کپی شد' : 'کپی شناسه انجام نشد');
			};
			if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
				navigator.clipboard.writeText(value).then(function () { done(true); }).catch(function () { done(fallbackCopy(value)); });
			} else {
				done(fallbackCopy(value));
			}
		});
	}

	initProductTabs();
	initReviewRating();
	initFaqAccessibility();
	initCopyButtons();
})();
