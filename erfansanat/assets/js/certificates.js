/**
 * Certificates lightbox (P07-T022).
 *
 * Progressive enhancement for the licenses & honors gallery. Each certificate
 * image carries [data-cert-open] with data-cert-src / data-cert-alt; clicking
 * it opens an accessible overlay ([data-cert-lightbox]) with prev/next/close
 * and Escape support. No dependency on main.js; works with keyboard + touch.
 */
(function () {
  "use strict";

  function init() {
    var root = document.querySelector("[data-cert-gallery]");
    if (!root) return;
    var triggers = Array.prototype.slice.call(root.querySelectorAll("[data-cert-open]"));
    var box = root.querySelector("[data-cert-lightbox]");
    if (!box || triggers.length === 0) return;
    var img = box.querySelector("[data-cert-img]");
    var caption = box.querySelector("[data-cert-caption]");
    var counter = box.querySelector("[data-cert-counter]");
    var closeBtn = box.querySelector("[data-cert-close]");
    var prevBtn = box.querySelector("[data-cert-prev]");
    var nextBtn = box.querySelector("[data-cert-next]");
    var active = 0;
    var isOpen = false;

    function fa(n) {
      return String(n).replace(/[0-9]/g, function (d) {
        return String.fromCharCode(0x06f0 + Number(d));
      });
    }

    function render() {
      var t = triggers[active];
      if (!t) return;
      var src = t.getAttribute("data-cert-src") || "";
      var alt = t.getAttribute("data-cert-alt") || "";
      if (img) {
        img.setAttribute("src", src);
        img.setAttribute("alt", alt);
      }
      if (caption) caption.textContent = alt;
      if (counter) counter.textContent = fa(active + 1) + " / " + fa(triggers.length);
    }

    function open(idx) {
      active = (idx + triggers.length) % triggers.length;
      render();
      isOpen = true;
      box.classList.remove("hidden");
      box.classList.add("flex");
      document.body.style.overflow = "hidden";
      if (closeBtn) closeBtn.focus();
    }

    function close() {
      isOpen = false;
      box.classList.add("hidden");
      box.classList.remove("flex");
      document.body.style.overflow = "";
    }

    function step(delta) {
      active = (active + delta + triggers.length) % triggers.length;
      render();
    }

    triggers.forEach(function (t, idx) {
      t.addEventListener("click", function (e) {
        e.preventDefault();
        open(idx);
      });
    });
    if (closeBtn) closeBtn.addEventListener("click", function (e) { e.stopPropagation(); close(); });
    if (prevBtn) prevBtn.addEventListener("click", function (e) { e.stopPropagation(); step(-1); });
    if (nextBtn) nextBtn.addEventListener("click", function (e) { e.stopPropagation(); step(1); });
    box.addEventListener("click", function (e) { if (e.target === box) close(); });
    if (img) img.addEventListener("click", function (e) { e.stopPropagation(); });
    document.addEventListener("keydown", function (e) {
      if (!isOpen) return;
      if (e.key === "Escape") close();
      else if (e.key === "ArrowLeft") step(1);
      else if (e.key === "ArrowRight") step(-1);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
