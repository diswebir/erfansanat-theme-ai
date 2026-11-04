/* Keep the server-rendered WooCommerce count authoritative without editing main.js. */
(function () {
  "use strict";
  function syncBadge() {
    var badges = document.querySelectorAll('[data-header-cart-badge="true"][data-wc-cart-count], [data-cart-modal-count="true"][data-wc-cart-count]');
    for (var i = 0; i < badges.length; i++) {
      var count = badges[i].getAttribute("data-wc-cart-count");
      var suffix = badges[i].getAttribute("data-wc-cart-suffix") || "";
      var text = count === null ? "" : count + suffix;
      if (count !== null && badges[i].textContent !== text) badges[i].textContent = text;
    }
  }
  if (document.readyState === "loading") document.addEventListener("DOMContentLoaded", syncBadge);
  else syncBadge();
  if (window.MutationObserver && document.body) {
    var observer = new MutationObserver(syncBadge);
    observer.observe(document.body, { childList: true, subtree: true, characterData: true });
  }
  if (window.jQuery) {
    window.jQuery(document.body).on("added_to_cart removed_from_cart wc_fragments_refreshed", function () {
      window.setTimeout(syncBadge, 0);
    });
  }
})();
