/**
 * FAQ search (P07-T023).
 *
 * Client-side filter for the FAQ accordion. The open/close behavior lives in
 * main.js (data-faq-list/toggle/body/icon); this script only filters the
 * [data-faq-item] cards by their question/answer/group text as the user types
 * into [data-faq-search], toggles a no-results notice ([data-faq-empty]) and
 * updates the visible count ([data-faq-count]). Persian/Arabic letter variants
 * and diacritics are normalized so search is forgiving.
 */
(function () {
  "use strict";

  function normalize(s) {
    return (s || "")
      .toString()
      .toLowerCase()
      .replace(/[\u064B-\u0652\u0670]/g, "") // harakat
      .replace(/\u200c/g, " ") // ZWNJ → space
      .replace(/[يى]/g, "ی")
      .replace(/ك/g, "ک")
      .replace(/[أإآ]/g, "ا")
      .replace(/ة/g, "ه")
      .replace(/\s+/g, " ")
      .trim();
  }

  function fa(n) {
    return String(n).replace(/[0-9]/g, function (d) {
      return String.fromCharCode(0x06f0 + Number(d));
    });
  }

  function init() {
    var input = document.querySelector("[data-faq-search]");
    var list = document.querySelector("[data-faq-list]");
    if (!input || !list) return;
    var items = Array.prototype.slice.call(list.querySelectorAll("[data-faq-item]"));
    var empty = document.querySelector("[data-faq-empty]");
    var count = document.querySelector("[data-faq-count]");
    var haystacks = items.map(function (item) {
      return normalize(item.getAttribute("data-faq-text") || item.textContent);
    });

    function apply() {
      var q = normalize(input.value);
      var visible = 0;
      items.forEach(function (item, i) {
        var match = q === "" || haystacks[i].indexOf(q) !== -1;
        if (match) {
          item.classList.remove("hidden");
          visible++;
        } else {
          item.classList.add("hidden");
        }
      });
      if (empty) {
        if (visible === 0) empty.classList.remove("hidden");
        else empty.classList.add("hidden");
      }
      if (count) count.textContent = fa(visible);
    }

    input.addEventListener("input", apply);
    input.addEventListener("search", apply);
    apply();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
