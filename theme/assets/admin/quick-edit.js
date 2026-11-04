(function ($) {
	'use strict';
	if (!window.inlineEditPost) return;
	var originalEdit = window.inlineEditPost.edit;
	window.inlineEditPost.edit = function (id) {
		originalEdit.apply(this, arguments);
		var postId = typeof id === 'object' ? parseInt(this.getId(id), 10) : parseInt(id, 10);
		if (!postId) return;
		var source = document.querySelector('#post-' + postId + ' [data-es-product-quick-edit]');
		var editRow = document.querySelector('#edit-' + postId);
		if (!source || !editRow) return;
		var values;
		try { values = JSON.parse(source.getAttribute('data-es-product-quick-edit') || '{}'); } catch (error) { return; }
		Object.keys(values).forEach(function (key) {
			var field = editRow.querySelector('[name="es_product_quick_edit[' + key + ']"]');
			if (field) field.value = String(values[key]);
		});
	};
})(jQuery);
