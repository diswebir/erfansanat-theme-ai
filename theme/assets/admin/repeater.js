(function () {
	'use strict';

	var dragging = null;
	var token = 'item-__INDEX__';

	function rows(container) {
		return Array.prototype.slice.call(container.querySelectorAll('[data-es-repeater-item]'));
	}

	function replaceKey(value, oldKey, newKey) {
		return String(value)
			.split(token).join(newKey)
			.split('[' + oldKey + ']').join('[' + newKey + ']')
			.split('-' + oldKey + '-').join('-' + newKey + '-');
	}

	function setKey(row, oldKey, newKey) {
		row.setAttribute('data-es-repeater-item', newKey);
		row.querySelectorAll('[name], [id], label[for]').forEach(function (element) {
			['name', 'id', 'for'].forEach(function (attribute) {
				if (element.hasAttribute(attribute)) {
					element.setAttribute(attribute, replaceKey(element.getAttribute(attribute), oldKey, newKey));
				}
			});
		});
	}

	function reindex(container) {
		rows(container).forEach(function (row, index) {
			var oldKey = row.getAttribute('data-es-repeater-item') || token;
			var newKey = 'item-' + index;
			if (oldKey !== newKey) {
				setKey(row, oldKey, newKey);
			}
		});
	}

	function move(row, direction) {
		var container = row.closest('[data-es-repeater]');
		if (!container) return;
		var list = rows(container);
		var index = list.indexOf(row);
		var target = list[index + direction];
		if (!target) return;
		if (direction < 0) {
			container.insertBefore(row, target);
		} else {
			container.insertBefore(row, target.nextSibling);
		}
		reindex(container);
		var handle = row.querySelector('[data-es-repeater-handle]');
		if (handle) handle.focus();
	}

	document.addEventListener('click', function (event) {
		var add = event.target.closest('[data-es-repeater-add]');
		if (add) {
			event.preventDefault();
			var container = add.closest('[data-es-repeater]');
			var template = container && container.querySelector('template[data-es-repeater-template]');
			if (!container || !template || !template.content) return;
			var fragment = template.content.cloneNode(true);
			var row = fragment.querySelector('[data-es-repeater-item]');
			if (!row) return;
			var current = rows(container).length;
			setKey(row, token, 'item-' + current);
			container.insertBefore(fragment, template);
			reindex(container);
			var first = row.querySelector('input, textarea, select');
			if (first) first.focus();
			return;
		}

		var remove = event.target.closest('[data-es-repeater-remove]');
		if (remove) {
			event.preventDefault();
			var removeRow = remove.closest('[data-es-repeater-item]');
			var removeContainer = remove.closest('[data-es-repeater]');
			if (removeRow && removeContainer) {
				removeRow.remove();
				reindex(removeContainer);
				var addButton = removeContainer.querySelector('[data-es-repeater-add]');
				if (addButton) addButton.focus();
			}
			return;
		}

		var up = event.target.closest('[data-es-repeater-up]');
		if (up) {
			event.preventDefault();
			var upRow = up.closest('[data-es-repeater-item]');
			if (upRow) move(upRow, -1);
			return;
		}
		var down = event.target.closest('[data-es-repeater-down]');
		if (down) {
			event.preventDefault();
			var downRow = down.closest('[data-es-repeater-item]');
			if (downRow) move(downRow, 1);
		}
	});

	document.addEventListener('keydown', function (event) {
		var handle = event.target.closest('[data-es-repeater-handle]');
		if (!handle) return;
		var row = handle.closest('[data-es-repeater-item]');
		if (event.key === 'ArrowUp') {
			event.preventDefault();
			if (row) move(row, -1);
		} else if (event.key === 'ArrowDown') {
			event.preventDefault();
			if (row) move(row, 1);
		}
	});

	document.addEventListener('dragstart', function (event) {
		var handle = event.target.closest('[data-es-repeater-handle]');
		dragging = handle && handle.closest('[data-es-repeater-item]');
		if (dragging && event.dataTransfer) {
			event.dataTransfer.effectAllowed = 'move';
			event.dataTransfer.setData('text/plain', 'es-repeater-row');
		}
	});

	document.addEventListener('dragover', function (event) {
		if (!dragging) return;
		var target = event.target.closest('[data-es-repeater-item]');
		var container = target && target.closest('[data-es-repeater]');
		if (!target || !container || dragging.closest('[data-es-repeater]') !== container || target === dragging) return;
		event.preventDefault();
		var rect = target.getBoundingClientRect();
		var after = event.clientY > rect.top + rect.height / 2;
		container.insertBefore(dragging, after ? target.nextSibling : target);
		reindex(container);
	});

	document.addEventListener('drop', function (event) {
		if (dragging) {
			event.preventDefault();
			var container = dragging.closest('[data-es-repeater]');
			if (container) reindex(container);
			dragging = null;
		}
	});

	document.addEventListener('dragend', function () { dragging = null; });
})();
