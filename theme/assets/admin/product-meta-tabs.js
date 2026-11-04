(function () {
	'use strict';
	function activate(tab) {
		var root = tab.closest('.es-product-meta__tabs');
		if (!root) return;
		root.querySelectorAll('[data-es-product-tab]').forEach(function (item) {
			var active = item === tab;
			item.setAttribute('aria-selected', active ? 'true' : 'false');
			item.tabIndex = active ? 0 : -1;
		});
		root.querySelectorAll('[data-es-product-panel]').forEach(function (panel) {
			panel.hidden = panel.getAttribute('data-es-product-panel') !== tab.getAttribute('data-es-product-tab');
		});
	}
	document.addEventListener('click', function (event) {
		var tab = event.target.closest('[data-es-product-tab]');
		if (tab) { event.preventDefault(); activate(tab); }
	});
	document.addEventListener('keydown', function (event) {
		var tab = event.target.closest('[data-es-product-tab]');
		if (!tab || !['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
		event.preventDefault();
		var tabs = Array.prototype.slice.call(tab.closest('.es-product-meta__tabs').querySelectorAll('[data-es-product-tab]'));
		var index = tabs.indexOf(tab);
		if (event.key === 'Home') index = 0;
		else if (event.key === 'End') index = tabs.length - 1;
		else index = (index + (event.key === 'ArrowRight' ? 1 : tabs.length - 1)) % tabs.length;
		tabs[index].focus(); activate(tabs[index]);
	});
})();
