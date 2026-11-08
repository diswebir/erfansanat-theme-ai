/* erfansanat admin dashboard — tabs + AJAX save (no jQuery dependency) */
(function () {
  'use strict';
  document.querySelectorAll('.erfansanat-tab').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.erfansanat-tab').forEach(function (b) { b.classList.remove('active'); });
      document.querySelectorAll('.erfansanat-tab-panel').forEach(function (p) { p.classList.remove('active'); });
      btn.classList.add('active');
      var panel = document.querySelector('.erfansanat-tab-panel[data-panel="' + btn.dataset.tab + '"]');
      if (panel) { panel.classList.add('active'); }
    });
  });

  var form = document.getElementById('erfansanat-options-form');
  if (form && window.ErfanSanatAdmin) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var data = new FormData(form);
      data.append('action', ErfanSanatAdmin.action);
      data.append('nonce', ErfanSanatAdmin.nonce);
      fetch(ErfanSanatAdmin.ajaxUrl, { method: 'POST', body: data, credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (res) {
          if (res && res.success) {
            var n = document.createElement('div');
            n.className = 'notice notice-success is-dismissible';
            n.innerHTML = '<p>' + (res.data.message || 'OK') + '</p>';
            form.parentNode.insertBefore(n, form);
          }
        });
    });
  }
})();
