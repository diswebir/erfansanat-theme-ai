(function () {
  'use strict';

  document.addEventListener('click', function (event) {
    var origin = event.target;
    var row = origin && origin.closest ? origin.closest('[data-service-row][data-service-url]') : null;
    if (!row) return;

    var url = row.getAttribute('data-service-url');
    if (!url) return;
    event.preventDefault();

    // The optional seam lets the PHP-WASM/jsdom harness verify navigation
    // without asking jsdom to perform a real document navigation.
    if (typeof window.__esNavigate === 'function') {
      window.__esNavigate(url);
      return;
    }
    window.location.assign(url);
  });
})();
