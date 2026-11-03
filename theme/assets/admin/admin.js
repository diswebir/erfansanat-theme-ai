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
})();
