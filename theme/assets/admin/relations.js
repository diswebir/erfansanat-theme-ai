(function () {
	'use strict';
	document.addEventListener('input', function (event) {
		var input = event.target.closest('[data-es-relations-search="projects"]');
		if (!input || !window.ajaxurl) { return; }
		var box = input.closest('.es-product-meta__relations');
		var results = box && box.querySelector('[data-es-relations-results]');
		if (!results) { return; }
		var nonce = box.querySelector('[name="es_relations_nonce"]');
		var url = new URL(window.ajaxurl, window.location.href);
		url.searchParams.set('action', 'es_search_posts');
		url.searchParams.set('q', input.value);
		url.searchParams.set('nonce', nonce ? nonce.value : '');
		window.fetch(url.toString(), { credentials: 'same-origin' }).then(function (response) { return response.json(); }).then(function (payload) {
			results.replaceChildren();
			if (!payload || !payload.success || !Array.isArray(payload.data)) { return; }
			payload.data.forEach(function (item) {
				var button = document.createElement('button');
				button.type = 'button'; button.textContent = item.title; button.setAttribute('data-es-relation-add', String(item.id));
				results.appendChild(button);
			});
		}).catch(function () { results.replaceChildren(); });
	});
	document.addEventListener('click', function (event) {
		var add = event.target.closest('[data-es-relation-add]');
		if (add) {
			var box = add.closest('.es-product-meta__relations');
			var selected = box && box.querySelector('[data-es-relations-selected]');
			var id = add.getAttribute('data-es-relation-add');
			if (selected && !selected.querySelector('input[value="' + CSS.escape(id) + '"]')) {
				var tag = document.createElement('span'); tag.className = 'es-relation-tag'; tag.setAttribute('data-es-relation-tag', '');
				var hidden = document.createElement('input'); hidden.type = 'hidden'; hidden.name = 'es_product_meta[project-ids][]'; hidden.value = id;
				var label = document.createTextNode(add.textContent + ' '); var remove = document.createElement('button'); remove.type = 'button'; remove.textContent = '×'; remove.setAttribute('data-es-relation-remove', '');
				tag.append(hidden, label, remove); selected.appendChild(tag);
			}
		}
		if (event.target.closest('[data-es-relation-remove]')) { event.preventDefault(); event.target.closest('[data-es-relation-tag]').remove(); }
	});
})();
