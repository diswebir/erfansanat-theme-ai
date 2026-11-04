/* Small adapter for the whitelisted navigation mode; main.js stays unchanged. */
(function () {
  "use strict";
  function bindNavigationMode() {
    var headers = document.querySelectorAll('[data-navbar-header="true"]');
    for (var h = 0; h < headers.length; h++) {
      var header = headers[h];
      var mode = header.getAttribute("data-nav-open") || "hover";
      var triggers = header.querySelectorAll("[data-desktop-mega-trigger]");
      for (var i = 0; i < triggers.length; i++) {
        (function (trigger) {
          if (mode === "click") {
            trigger.addEventListener("mouseenter", function (event) {
              event.stopImmediatePropagation();
            }, true);
          } else {
            trigger.addEventListener("click", function (event) {
              event.preventDefault();
              event.stopImmediatePropagation();
            }, true);
          }
        })(triggers[i]);
      }
    }
  }
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", bindNavigationMode);
  } else {
    bindNavigationMode();
  }
})();
