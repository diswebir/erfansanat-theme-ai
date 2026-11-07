/* Native AJAX filters & live search — replaces JetSearch/JetSmartFilters.
 * Updates the results grid via fetch without page reload. */
(function () {
  'use strict';
  if (!window.ErfanSanatAjax) { return; }
  var A = window.ErfanSanatAjax;

  function ajax(action, params) {
    var fd = new FormData();
    fd.append('action', action);
    fd.append('nonce', A.nonce);
    Object.keys(params || {}).forEach(function (k) { fd.append(k, params[k]); });
    return fetch(A.ajaxUrl + '?action=' + action, { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function (r) { return r.json(); });
  }

  // Live search input (debounced, <200ms target)
  var searchInput = document.querySelector('input[data-live-search]');
  if (searchInput) {
    var timer = null;
    searchInput.addEventListener('input', function () {
      clearTimeout(timer);
      var q = searchInput.value;
      timer = setTimeout(function () {
        if (q.length < 2) { return; }
        ajax('erfansanat_search_products', { s: q }).then(function (res) {
          var box = document.querySelector('[data-search-results]');
          if (box && res && res.success) { box.innerHTML = res.data.html; }
        });
      }, 180);
    });
  }

  // Filter form
  var form = document.querySelector('[data-filters-form]');
  if (form) {
    form.addEventListener('change', function () {
      var params = {};
      new FormData(form).forEach(function (v, k) { if (v) { params[k] = v; } });
      ajax(form.dataset.action || 'erfansanat_filter_products', params).then(function (res) {
        var grid = document.querySelector('[data-results-grid]');
        if (grid && res && res.success) { grid.innerHTML = res.data.html; }
      });
    });
  }
})();
