/* SectionManager drag&drop reorder + AJAX persist */
(function () {
  'use strict';
  var list = document.querySelector('.erfansanat-section-list');
  if (!list || !window.ErfanSanatSections) { return; }

  var dragEl = null;
  list.querySelectorAll('li').forEach(function (li) {
    li.addEventListener('dragstart', function () { dragEl = li; li.classList.add('dragging'); });
    li.addEventListener('dragend', function () { li.classList.remove('dragging'); persist(); });
    li.addEventListener('dragover', function (e) {
      e.preventDefault();
      var after = e.target.closest('li');
      if (after && after !== dragEl) { list.insertBefore(dragEl, after); }
    });
  });

  function persist() {
    var items = [];
    list.querySelectorAll('li').forEach(function (li, idx) {
      var orderInput = li.querySelector('.section-order');
      if (orderInput) { orderInput.value = idx + 1; }
      var cb = li.querySelector('input[type=checkbox]');
      items.push({ id: li.dataset.section, enabled: cb ? cb.checked : true, order: idx + 1 });
    });
    var data = new FormData();
    data.append('action', ErfanSanatSections.action);
    data.append('nonce', ErfanSanatSections.nonce);
    data.append('post_id', list.dataset.post);
    items.forEach(function (it, i) {
      data.append('items[' + i + '][id]', it.id);
      data.append('items[' + i + '][enabled]', it.enabled ? 1 : 0);
      data.append('items[' + i + '][order]', it.order);
    });
    fetch(ErfanSanatSections.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' });
  }
})();
