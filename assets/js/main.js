/**
 * Erfan Sanat Esfahan (ESE) — Pure Vanilla JS Interactive Engine
 * Zero framework dependencies. Works 100% offline over file:// and http://.
 */
(function () {
  "use strict";

  var PERSIAN_DIGITS = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹"];

  function toFa(val) {
    return String(val).replace(/\d/g, function (d) {
      return PERSIAN_DIGITS[Number(d)];
    });
  }
  var toPersianDigits = toFa;

  function formatToman(num) {
    var formatted = String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return toFa(formatted) + " تومان";
  }

  /* ───────────────────────────────────────────────
     1. Navbar Scroll Effect & Mobile Menu Drawer
     ─────────────────────────────────────────────── */
  function initNavbar() {
    var header = document.querySelector('[data-navbar-header="true"]');
    var toggleBtn = document.querySelector('[data-mobile-toggle="true"]');
    var mobileMenu = document.querySelector('[data-mobile-menu="true"]');
    var iconOpen = document.querySelector('[data-icon-open="true"]');
    var iconClose = document.querySelector('[data-icon-close="true"]');

    if (header) {
      var updateScroll = function () {
        if (window.scrollY > 28) {
          header.classList.add(
            "border-emerald-100",
            "shadow-[0_8px_30px_-20px_rgba(0,168,62,.35)]"
          );
          header.classList.remove(
            "border-transparent",
            "shadow-[0_1px_0_0_rgba(5,150,105,0.04)]"
          );
        } else {
          header.classList.remove(
            "border-emerald-100",
            "shadow-[0_8px_30px_-20px_rgba(0,168,62,.35)]"
          );
          header.classList.add(
            "border-transparent",
            "shadow-[0_1px_0_0_rgba(5,150,105,0.04)]"
          );
        }
      };
      updateScroll();
      window.addEventListener("scroll", updateScroll, { passive: true });
    }

    if (toggleBtn && mobileMenu) {
      var isOpen = false;
      var closeBtns = mobileMenu.querySelectorAll('[data-mobile-close="true"], [data-mobile-backdrop="true"]');
      var setMenuOpen = function (next) {
        isOpen = next;
        toggleBtn.setAttribute("aria-expanded", String(isOpen));
        mobileMenu.setAttribute("aria-hidden", String(!isOpen));
        if (isOpen) {
          mobileMenu.classList.remove("hidden");
          mobileMenu.classList.add("block");
          document.body.style.overflow = "hidden";
          if (iconOpen) iconOpen.classList.add("hidden");
          if (iconClose) iconClose.classList.remove("hidden");
        } else {
          mobileMenu.classList.add("hidden");
          mobileMenu.classList.remove("block");
          document.body.style.overflow = "";
          if (iconOpen) iconOpen.classList.remove("hidden");
          if (iconClose) iconClose.classList.add("hidden");
        }
      };

      toggleBtn.addEventListener("click", function () {
        setMenuOpen(!isOpen);
      });

      for (var c = 0; c < closeBtns.length; c++) {
        closeBtns[c].addEventListener("click", function () {
          setMenuOpen(false);
        });
      }

      var menuLinks = mobileMenu.querySelectorAll("a");
      for (var i = 0; i < menuLinks.length; i++) {
        menuLinks[i].addEventListener("click", function () {
          setMenuOpen(false);
        });
      }
    }
  }

  /* ───────────────────────────────────────────────
     2. Footer Scroll-to-Top Button
     ─────────────────────────────────────────────── */
  function initScrollTop() {
    var btns = document.querySelectorAll('[data-scroll-top="true"]');
    for (var i = 0; i < btns.length; i++) {
      btns[i].addEventListener("click", function () {
        window.scrollTo({ top: 0, behavior: "smooth" });
      });
    }
  }

  /* ───────────────────────────────────────────────
     3. Statistical Counters (About Section)
     ─────────────────────────────────────────────── */
  function initCounters() {
    var counters = document.querySelectorAll("[data-counter-target]");
    if (!counters.length) return;

    var animateCounter = function (el) {
      if (el.getAttribute("data-counted") === "true") return;
      el.setAttribute("data-counted", "true");
      var target = parseInt(el.getAttribute("data-counter-target") || "0", 10);
      var suffix = el.getAttribute("data-counter-suffix") || "";
      if (!target || isNaN(target)) return;

      var duration = 1400;
      var startTime = null;

      function step(timestamp) {
        if (!startTime) startTime = timestamp;
        var progress = Math.min((timestamp - startTime) / duration, 1);
        var eased = 1 - Math.pow(1 - progress, 3);
        var current = Math.round(eased * target);
        el.textContent = toFa(current) + suffix;
        if (progress < 1) {
          window.requestAnimationFrame(step);
        } else {
          el.textContent = toFa(target) + suffix;
        }
      }

      window.requestAnimationFrame(step);
    };

    if ("IntersectionObserver" in window) {
      var observer = new IntersectionObserver(
        function (entries) {
          for (var i = 0; i < entries.length; i++) {
            if (entries[i].isIntersecting) {
              animateCounter(entries[i].target);
              observer.unobserve(entries[i].target);
            }
          }
        },
        { threshold: 0.25 }
      );
      for (var i = 0; i < counters.length; i++) {
        observer.observe(counters[i]);
      }
    } else {
      for (var j = 0; j < counters.length; j++) {
        animateCounter(counters[j]);
      }
    }
  }

  /* ───────────────────────────────────────────────
     4. Home Page Services Accordion & Sticky Preview
     ─────────────────────────────────────────────── */
  function initHomeServices() {
    var section = document.querySelector('[data-services-section="true"]');
    if (!section) return;

    var rows = section.querySelectorAll("[data-service-row]");
    var previewImg = section.querySelector('[data-service-preview-img="true"]');
    var previewTitle = section.querySelector('[data-service-preview-title="true"]');
    var previewDesc = section.querySelector('[data-service-preview-desc="true"]');
    var previewCounter = section.querySelector('[data-service-preview-counter="true"]');
    var previewIcons = section.querySelectorAll("[data-service-preview-icon]");

    function setActiveService(index) {
      for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        var isActive = i === index;
        var indicator = row.querySelector("[data-service-indicator]");
        var num = row.querySelector("[data-service-num]");
        var heading = row.querySelector("[data-service-heading]");
        var rowIcon = row.querySelector("[data-service-row-icon]");
        var desc = row.querySelector("[data-service-desc]");
        var mobileImg = row.querySelector("[data-service-mobile-img]");

        if (isActive) {
          row.classList.add("rounded-2xl", "bg-emerald-50");
          row.classList.remove("hover:bg-slate-50");
          if (indicator) {
            indicator.classList.add("h-3/4");
            indicator.classList.remove("h-0");
          }
          if (num) {
            num.classList.add("text-emerald-700");
            num.classList.remove("text-slate-400");
          }
          if (heading) {
            heading.classList.add("text-emerald-950");
            heading.classList.remove("text-slate-800");
          }
          if (rowIcon) {
            rowIcon.classList.add("scale-110", "text-emerald-700");
            rowIcon.classList.remove("text-slate-400", "group-hover:text-emerald-600");
          }
          if (desc) {
            desc.classList.remove("hidden");
            desc.classList.add("block");
          }
          if (mobileImg) {
            mobileImg.classList.remove("hidden");
            mobileImg.classList.add("block");
          }
          if (previewImg) {
            previewImg.setAttribute("src", row.getAttribute("data-service-img") || "");
            previewImg.setAttribute("alt", row.getAttribute("data-service-title") || "");
          }
          if (previewTitle) {
            previewTitle.textContent = row.getAttribute("data-service-title") || "";
          }
          if (previewDesc) {
            previewDesc.textContent = row.getAttribute("data-service-desc-text") || "";
          }
          if (previewCounter) {
            previewCounter.textContent = row.getAttribute("data-service-counter") || "";
          }
        } else {
          row.classList.remove("rounded-2xl", "bg-emerald-50");
          row.classList.add("hover:bg-slate-50");
          if (indicator) {
            indicator.classList.remove("h-3/4");
            indicator.classList.add("h-0");
          }
          if (num) {
            num.classList.remove("text-emerald-700");
            num.classList.add("text-slate-400");
          }
          if (heading) {
            heading.classList.remove("text-emerald-950");
            heading.classList.add("text-slate-800");
          }
          if (rowIcon) {
            rowIcon.classList.remove("scale-110", "text-emerald-700");
            rowIcon.classList.add("text-slate-400", "group-hover:text-emerald-600");
          }
          if (desc) {
            desc.classList.add("hidden");
            desc.classList.remove("block");
          }
          if (mobileImg) {
            mobileImg.classList.add("hidden");
            mobileImg.classList.remove("block");
          }
        }
      }

      for (var k = 0; k < previewIcons.length; k++) {
        var iconSpan = previewIcons[k];
        if (k === index) {
          iconSpan.classList.remove("hidden");
          iconSpan.classList.add("block");
        } else {
          iconSpan.classList.add("hidden");
          iconSpan.classList.remove("block");
        }
      }
    }

    for (var i = 0; i < rows.length; i++) {
      (function (idx) {
        rows[idx].addEventListener("mouseenter", function () {
          setActiveService(idx);
        });
        rows[idx].addEventListener("click", function () {
          setActiveService(idx);
        });
      })(i);
    }
  }

  /* ───────────────────────────────────────────────
     5. Detail Page Image Gallery & Lightbox
     ─────────────────────────────────────────────── */
  function initGalleries() {
    var galleries = document.querySelectorAll('[data-gallery-root="true"]');
    for (var g = 0; g < galleries.length; g++) {
      (function (root) {
        var thumbs = root.querySelectorAll("[data-gallery-thumb]");
        var mainImg = root.querySelector('[data-gallery-main-img="true"]');
        var counter = root.querySelector('[data-gallery-counter="true"]');
        var prevBtn = root.querySelector('[data-gallery-prev="true"]');
        var nextBtn = root.querySelector('[data-gallery-next="true"]');
        var expandBtn = root.querySelector('[data-gallery-expand="true"]');
        var lightbox = root.querySelector('[data-gallery-lightbox="true"]');
        var lbClose = root.querySelector('[data-gallery-lightbox-close="true"]');
        var lbImg = root.querySelector('[data-gallery-lightbox-img="true"]');
        var lbPrev = root.querySelector('[data-gallery-lightbox-prev="true"]');
        var lbNext = root.querySelector('[data-gallery-lightbox-next="true"]');
        var lbDots = root.querySelectorAll("[data-gallery-lightbox-dot]");

        var srcs = [];
        for (var i = 0; i < thumbs.length; i++) {
          srcs.push(thumbs[i].getAttribute("data-gallery-src") || "");
        }
        if (srcs.length === 0 && mainImg) {
          srcs.push(mainImg.getAttribute("src") || "");
        }

        var active = 0;
        var isOpen = false;

        function setSlide(idx) {
          if (!srcs.length) return;
          active = (idx + srcs.length) % srcs.length;
          var src = srcs[active];
          if (mainImg) mainImg.setAttribute("src", src);
          if (lbImg) lbImg.setAttribute("src", src);
          if (counter) {
            counter.textContent = toFa(active + 1) + " / " + toFa(srcs.length);
          }
          for (var t = 0; t < thumbs.length; t++) {
            var thumb = thumbs[t];
            var ring = thumb.querySelector("[data-gallery-thumb-ring]");
            if (t === active) {
              thumb.classList.add(
                "border-emerald-500",
                "shadow-[0_4px_16px_-4px_rgba(5,150,105,.45)]"
              );
              thumb.classList.remove("border-transparent", "opacity-65", "hover:opacity-100");
              if (ring) {
                ring.classList.remove("hidden");
                ring.classList.add("block");
              }
            } else {
              thumb.classList.remove(
                "border-emerald-500",
                "shadow-[0_4px_16px_-4px_rgba(5,150,105,.45)]"
              );
              thumb.classList.add("border-transparent", "opacity-65", "hover:opacity-100");
              if (ring) {
                ring.classList.add("hidden");
                ring.classList.remove("block");
              }
            }
          }
          for (var d = 0; d < lbDots.length; d++) {
            if (d === active) {
              lbDots[d].classList.add("w-7", "bg-emerald-400");
              lbDots[d].classList.remove("w-2", "bg-white/40", "hover:bg-white/70");
            } else {
              lbDots[d].classList.remove("w-7", "bg-emerald-400");
              lbDots[d].classList.add("w-2", "bg-white/40", "hover:bg-white/70");
            }
          }
        }

        function setLightboxOpen(openState) {
          if (!lightbox) return;
          isOpen = openState;
          if (isOpen) {
            lightbox.classList.remove("hidden");
            lightbox.classList.add("flex");
            document.body.style.overflow = "hidden";
          } else {
            lightbox.classList.add("hidden");
            lightbox.classList.remove("flex");
            document.body.style.overflow = "";
          }
        }

        for (var j = 0; j < thumbs.length; j++) {
          (function (idx) {
            thumbs[idx].addEventListener("click", function () {
              setSlide(idx);
            });
          })(j);
        }

        for (var k = 0; k < lbDots.length; k++) {
          (function (idx) {
            lbDots[idx].addEventListener("click", function (e) {
              e.stopPropagation();
              setSlide(idx);
            });
          })(k);
        }

        if (prevBtn) {
          prevBtn.addEventListener("click", function () {
            setSlide(active - 1);
          });
        }
        if (nextBtn) {
          nextBtn.addEventListener("click", function () {
            setSlide(active + 1);
          });
        }
        if (mainImg) {
          mainImg.addEventListener("click", function () {
            setLightboxOpen(true);
          });
        }
        if (expandBtn) {
          expandBtn.addEventListener("click", function () {
            setLightboxOpen(true);
          });
        }
        if (lbClose) {
          lbClose.addEventListener("click", function (e) {
            e.stopPropagation();
            setLightboxOpen(false);
          });
        }
        if (lightbox) {
          lightbox.addEventListener("click", function () {
            setLightboxOpen(false);
          });
        }
        if (lbImg) {
          lbImg.addEventListener("click", function (e) {
            e.stopPropagation();
          });
        }
        if (lbPrev) {
          lbPrev.addEventListener("click", function (e) {
            e.stopPropagation();
            setSlide(active - 1);
          });
        }
        if (lbNext) {
          lbNext.addEventListener("click", function (e) {
            e.stopPropagation();
            setSlide(active + 1);
          });
        }

        window.addEventListener("keydown", function (e) {
          if (!isOpen) return;
          if (e.key === "Escape") setLightboxOpen(false);
          if (e.key === "ArrowLeft") setSlide(active + 1);
          if (e.key === "ArrowRight") setSlide(active - 1);
        });
      })(galleries[g]);
    }
  }

  /* ───────────────────────────────────────────────
     6. Shared Archive Drawer & Pagination Helper
     ─────────────────────────────────────────────── */
  function initArchiveDrawer(archiveRoot) {
    var drawer = archiveRoot.querySelector('[data-archive-drawer="true"]');
    if (!drawer) return;

    var openBtns = archiveRoot.querySelectorAll('[data-archive-drawer-open="true"]');
    var closeBtns = archiveRoot.querySelectorAll('[data-archive-drawer-close="true"]');
    var backdrop = archiveRoot.querySelector('[data-archive-drawer-backdrop="true"]');

    function setOpen(openState) {
      if (openState) {
        drawer.classList.remove("hidden");
        drawer.classList.add("flex");
        document.body.style.overflow = "hidden";
      } else {
        drawer.classList.add("hidden");
        drawer.classList.remove("flex");
        document.body.style.overflow = "";
      }
    }

    for (var i = 0; i < openBtns.length; i++) {
      openBtns[i].addEventListener("click", function () {
        setOpen(true);
      });
    }
    for (var j = 0; j < closeBtns.length; j++) {
      closeBtns[j].addEventListener("click", function () {
        setOpen(false);
      });
    }
    if (backdrop) {
      backdrop.addEventListener("click", function () {
        setOpen(false);
      });
    }
    window.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && !drawer.classList.contains("hidden")) {
        setOpen(false);
      }
    });
  }

  function updatePaginationUI(paginationRoot, matchingItems, currentPage, perPage, onPageSelect) {
    var totalMatches = matchingItems.length;
    var totalPages = Math.max(1, Math.ceil(totalMatches / perPage));
    var safePage = Math.min(Math.max(1, currentPage), totalPages);
    var startIdx = (safePage - 1) * perPage;
    var endIdx = startIdx + perPage;

    for (var i = 0; i < matchingItems.length; i++) {
      if (i >= startIdx && i < endIdx) {
        matchingItems[i].classList.remove("hidden");
      } else {
        matchingItems[i].classList.add("hidden");
      }
    }

    if (!paginationRoot) return safePage;

    if (totalPages <= 1) {
      paginationRoot.classList.add("hidden");
      return safePage;
    }

    paginationRoot.classList.remove("hidden");
    var pageBtns = paginationRoot.querySelectorAll("[data-page-btn]");
    var prevBtn = paginationRoot.querySelector('[data-page-prev="true"]');
    var nextBtn = paginationRoot.querySelector('[data-page-next="true"]');
    var summaryEl = paginationRoot.querySelector('[data-pagination-summary="true"]');

    for (var b = 0; b < pageBtns.length; b++) {
      var btn = pageBtns[b];
      var pNum = parseInt(btn.getAttribute("data-page-btn") || "1", 10);
      if (pNum > totalPages) {
        btn.classList.add("hidden");
      } else {
        btn.classList.remove("hidden");
        if (pNum === safePage) {
          btn.classList.add(
            "bg-emerald-700",
            "text-white",
            "shadow-md",
            "shadow-emerald-700/25"
          );
          btn.classList.remove(
            "border",
            "border-slate-200",
            "bg-white",
            "text-slate-700",
            "hover:border-emerald-400",
            "hover:text-emerald-700"
          );
          btn.setAttribute("aria-current", "page");
        } else {
          btn.classList.remove(
            "bg-emerald-700",
            "text-white",
            "shadow-md",
            "shadow-emerald-700/25"
          );
          btn.classList.add(
            "border",
            "border-slate-200",
            "bg-white",
            "text-slate-700",
            "hover:border-emerald-400",
            "hover:text-emerald-700"
          );
          btn.removeAttribute("aria-current");
        }
      }
    }

    if (prevBtn) {
      prevBtn.disabled = safePage <= 1;
    }
    if (nextBtn) {
      nextBtn.disabled = safePage >= totalPages;
    }
    if (summaryEl) {
      summaryEl.textContent =
        "صفحه " +
        toFa(safePage) +
        " از " +
        toFa(totalPages) +
        " (" +
        toFa(totalMatches) +
        " مورد)";
    }

    if (paginationRoot.getAttribute("data-pagination-bound") !== "true") {
      paginationRoot.setAttribute("data-pagination-bound", "true");
      for (var k = 0; k < pageBtns.length; k++) {
        pageBtns[k].addEventListener("click", function () {
          var targetP = parseInt(this.getAttribute("data-page-btn") || "1", 10);
          onPageSelect(targetP);
        });
      }
      if (prevBtn) {
        prevBtn.addEventListener("click", function () {
          onPageSelect("prev");
        });
      }
      if (nextBtn) {
        nextBtn.addEventListener("click", function () {
          onPageSelect("next");
        });
      }
    }

    return safePage;
  }

  /* ───────────────────────────────────────────────
     7. Projects Archive Filter, Sort & Pagination
     ─────────────────────────────────────────────── */
  function initProjectsArchive() {
    var archive = document.querySelector('[data-projects-archive="true"]');
    if (!archive) return;

    initArchiveDrawer(archive);

    var searchInputs = archive.querySelectorAll('[data-project-search="true"]');
    var sortSelect = archive.querySelector('[data-project-sort="true"]');
    var gridEl = archive.querySelector('[data-project-grid="true"]');
    var countEls = archive.querySelectorAll('[data-project-count="true"]');
    var emptyEl = archive.querySelector('[data-project-empty="true"]');
    var paginationRoot = archive.querySelector('[data-pagination-root="true"]');
    var perPage = paginationRoot
      ? parseInt(paginationRoot.getAttribute("data-per-page") || "6", 10)
      : Math.max(1, gridEl ? gridEl.querySelectorAll('[data-project-item="true"]').length : 6);

    var activeType = archive.getAttribute("data-initial-type") || "همه";
    var activeLoc = archive.getAttribute("data-initial-location") || "همه";
    var searchQuery = "";
    var sortBy = "newest";
    var currentPage = 1;

    try {
      var params = new URLSearchParams(window.location.search);
      var qType = params.get("type");
      var qLoc = params.get("location");
      var qPage = params.get("page");
      if (qType) activeType = qType;
      if (qLoc) activeLoc = qLoc;
      if (qPage) currentPage = Math.max(1, parseInt(qPage, 10) || 1);
    } catch (_e) {}

    function renderProjectsFilter() {
      if (!gridEl) return;
      var allItems = Array.prototype.slice.call(
        gridEl.querySelectorAll('[data-project-item="true"]')
      );

      // Sort items in DOM if needed
      allItems.sort(function (a, b) {
        if (sortBy === "popular") {
          var vA = parseInt(a.getAttribute("data-project-views") || "0", 10);
          var vB = parseInt(b.getAttribute("data-project-views") || "0", 10);
          return vB - vA;
        }
        if (sortBy === "rating") {
          var rA = parseFloat(a.getAttribute("data-project-rating") || "0");
          var rB = parseFloat(b.getAttribute("data-project-rating") || "0");
          return rB - rA;
        }
        var dA = a.getAttribute("data-project-date") || "";
        var dB = b.getAttribute("data-project-date") || "";
        return dB.localeCompare(dA);
      });

      for (var s = 0; s < allItems.length; s++) {
        gridEl.appendChild(allItems[s]);
      }

      var q = searchQuery.trim();
      var matchingItems = [];

      for (var i = 0; i < allItems.length; i++) {
        var item = allItems[i];
        var pType = item.getAttribute("data-project-type") || "";
        var pLoc = item.getAttribute("data-project-location") || "";
        var pText = item.getAttribute("data-project-search-text") || "";
        var matchType = activeType === "همه" || pType === activeType;
        var matchLoc = activeLoc === "همه" || pLoc === activeLoc;
        var matchQuery = !q || pText.indexOf(q) !== -1;

        if (matchType && matchLoc && matchQuery) {
          matchingItems.push(item);
        } else {
          item.classList.add("hidden");
        }
      }

      for (var c = 0; c < countEls.length; c++) {
        countEls[c].textContent = toFa(matchingItems.length);
      }

      if (emptyEl && gridEl) {
        if (matchingItems.length === 0) {
          emptyEl.classList.remove("hidden");
          emptyEl.classList.add("block");
          gridEl.classList.add("hidden");
        } else {
          emptyEl.classList.add("hidden");
          emptyEl.classList.remove("block");
          gridEl.classList.remove("hidden");
        }
      }

      currentPage = updatePaginationUI(
        paginationRoot,
        matchingItems,
        currentPage,
        perPage,
        function (action) {
          if (action === "prev") currentPage = Math.max(1, currentPage - 1);
          else if (action === "next") currentPage = currentPage + 1;
          else currentPage = Number(action) || 1;
          renderProjectsFilter();
          archive.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      );
    }

    for (var s = 0; s < searchInputs.length; s++) {
      searchInputs[s].addEventListener("input", function () {
        var val = this.value || "";
        searchQuery = val;
        currentPage = 1;
        for (var k = 0; k < searchInputs.length; k++) {
          if (searchInputs[k] !== this) searchInputs[k].value = val;
        }
        renderProjectsFilter();
      });
    }

    if (sortSelect) {
      sortSelect.addEventListener("change", function () {
        sortBy = this.value || "newest";
        currentPage = 1;
        renderProjectsFilter();
      });
    }

    renderProjectsFilter();
  }

  /* ───────────────────────────────────────────────
     8. Products Archive JetSmartFilters, Sort & Pagination
     ─────────────────────────────────────────────── */
  function initProductsArchive() {
    var archive = document.querySelector("[data-products-archive]");
    if (!archive) return;

    initArchiveDrawer(archive);

    var searchInputs = archive.querySelectorAll('[data-product-search="true"]');
    var purchaseRadios = archive.querySelectorAll("[data-product-purchase-mode]");
    var priceSliders = archive.querySelectorAll('[data-product-price-range="true"]');
    var priceLabels = archive.querySelectorAll('[data-product-price-label="true"]');
    var ipCheckboxes = archive.querySelectorAll("[data-product-ip-filter]");
    var voltageCheckboxes = archive.querySelectorAll("[data-product-voltage-filter]");
    var sortSelect = archive.querySelector('[data-product-sort="true"]');
    var purchBadge = archive.querySelector('[data-product-active-purch-badge="true"]');
    var countEls = archive.querySelectorAll('[data-product-count="true"]');
    var emptyEl = archive.querySelector('[data-product-empty="true"]');
    var gridEl = archive.querySelector('[data-product-grid="true"]');
    var paginationRoot = archive.querySelector('[data-pagination-root="true"]');
    var perPage = paginationRoot
      ? parseInt(paginationRoot.getAttribute("data-per-page") || "9", 10)
      : 9;

    var purchaseMode = "all";
    var maxPrice = 3000000;
    var selectedIps = [];
    var selectedVoltages = [];
    var searchQuery = "";
    var sortBy = sortSelect ? (sortSelect.value || "menu_order") : "menu_order";
    var currentPage = 1;

    try {
      var params = new URLSearchParams(window.location.search);
      if (params.get("purchasable") === "true") purchaseMode = "online";
      var qPage = params.get("page");
      if (qPage) currentPage = Math.max(1, parseInt(qPage, 10) || 1);
    } catch (_e) {}

    function renderProductsFilter() {
      if (!gridEl) return;
      var allItems = Array.prototype.slice.call(
        gridEl.querySelectorAll("[data-product-item]")
      );
      for (var orderIdx = 0; orderIdx < allItems.length; orderIdx++) {
        if (!allItems[orderIdx].hasAttribute("data-product-default-order")) {
          allItems[orderIdx].setAttribute("data-product-default-order", String(orderIdx));
        }
      }

      // Sort items
      allItems.sort(function (a, b) {
        if (sortBy === "rating") {
          var rA = parseFloat(a.getAttribute("data-product-rating") || "0");
          var rB = parseFloat(b.getAttribute("data-product-rating") || "0");
          return rB - rA;
        }
        if (sortBy === "price-asc" || sortBy === "price") {
          var pA = parseInt(a.getAttribute("data-product-price") || "999999999", 10) || 999999999;
          var pB = parseInt(b.getAttribute("data-product-price") || "999999999", 10) || 999999999;
          return pA - pB;
        }
        if (sortBy === "price-desc") {
          var pdA = parseInt(a.getAttribute("data-product-price") || "0", 10) || 0;
          var pdB = parseInt(b.getAttribute("data-product-price") || "0", 10) || 0;
          return pdB - pdA;
        }
        if (sortBy === "popularity" || sortBy === "popular") {
          var vA = parseInt(a.getAttribute("data-product-views") || "0", 10);
          var vB = parseInt(b.getAttribute("data-product-views") || "0", 10);
          return vB - vA;
        }
        var orderA = parseInt(a.getAttribute("data-product-default-order") || "0", 10);
        var orderB = parseInt(b.getAttribute("data-product-default-order") || "0", 10);
        return orderA - orderB;
      });

      for (var s = 0; s < allItems.length; s++) {
        gridEl.appendChild(allItems[s]);
      }

      var q = searchQuery.trim();
      var matchingItems = [];

      for (var i = 0; i < allItems.length; i++) {
        var item = allItems[i];
        var purch = item.getAttribute("data-product-purchasable") === "true";
        var itemPrice = parseInt(item.getAttribute("data-product-price") || "0", 10) || 0;
        var itemIp = item.getAttribute("data-product-ip") || "";
        var itemVoltage = item.getAttribute("data-product-voltage") || "";
        var pText = item.getAttribute("data-product-search-text") || "";

        var matchPurch =
          purchaseMode === "all" ||
          (purchaseMode === "online" && purch) ||
          ((purchaseMode === "call" || purchaseMode === "inquiry") && !purch);

        var matchPrice = maxPrice >= 3000000 || (purch && itemPrice <= maxPrice);

        var matchIp =
          selectedIps.length === 0 ||
          selectedIps.some(function (ip) {
            return itemIp.indexOf(ip) !== -1;
          });

        var matchVoltage =
          selectedVoltages.length === 0 ||
          selectedVoltages.some(function (v) {
            return itemVoltage.indexOf(v) !== -1;
          });

        var matchQuery = !q || pText.indexOf(q) !== -1;

        if (matchPurch && matchPrice && matchIp && matchVoltage && matchQuery) {
          matchingItems.push(item);
        } else {
          item.classList.add("hidden");
        }
      }

      if (purchBadge) {
        if (purchaseMode === "online") {
          purchBadge.classList.remove("hidden");
          purchBadge.classList.add("inline-flex");
        } else {
          purchBadge.classList.add("hidden");
          purchBadge.classList.remove("inline-flex");
        }
      }

      for (var c = 0; c < countEls.length; c++) {
        countEls[c].textContent = toFa(matchingItems.length);
      }

      if (emptyEl && gridEl) {
        if (matchingItems.length === 0) {
          emptyEl.classList.remove("hidden");
          emptyEl.classList.add("block");
          gridEl.classList.add("hidden");
        } else {
          emptyEl.classList.add("hidden");
          emptyEl.classList.remove("block");
          gridEl.classList.remove("hidden");
        }
      }

      currentPage = updatePaginationUI(
        paginationRoot,
        matchingItems,
        currentPage,
        perPage,
        function (action) {
          if (action === "prev") currentPage = Math.max(1, currentPage - 1);
          else if (action === "next") currentPage = currentPage + 1;
          else currentPage = Number(action) || 1;
          renderProductsFilter();
          archive.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      );
    }

    for (var r = 0; r < purchaseRadios.length; r++) {
      purchaseRadios[r].addEventListener("change", function () {
        if (!this.checked) return;
        purchaseMode = this.getAttribute("data-product-purchase-mode") || "all";
        currentPage = 1;
        for (var k = 0; k < purchaseRadios.length; k++) {
          purchaseRadios[k].checked =
            purchaseRadios[k].getAttribute("data-product-purchase-mode") === purchaseMode;
        }
        renderProductsFilter();
      });
    }

    for (var sl = 0; sl < priceSliders.length; sl++) {
      priceSliders[sl].addEventListener("input", function () {
        var val = parseInt(this.value || "3000000", 10);
        maxPrice = val;
        currentPage = 1;
        for (var k = 0; k < priceSliders.length; k++) {
          if (priceSliders[k] !== this) priceSliders[k].value = String(val);
        }
        for (var l = 0; l < priceLabels.length; l++) {
          priceLabels[l].textContent = "تا " + formatToman(val);
        }
        renderProductsFilter();
      });
    }

    for (var ipIdx = 0; ipIdx < ipCheckboxes.length; ipIdx++) {
      ipCheckboxes[ipIdx].addEventListener("change", function () {
        var ipVal = this.getAttribute("data-product-ip-filter") || "";
        var checked = this.checked;
        for (var k = 0; k < ipCheckboxes.length; k++) {
          if (ipCheckboxes[k].getAttribute("data-product-ip-filter") === ipVal) {
            ipCheckboxes[k].checked = checked;
          }
        }
        selectedIps = [];
        for (var m = 0; m < ipCheckboxes.length; m++) {
          var v = ipCheckboxes[m].getAttribute("data-product-ip-filter") || "";
          if (ipCheckboxes[m].checked && selectedIps.indexOf(v) === -1) {
            selectedIps.push(v);
          }
        }
        currentPage = 1;
        renderProductsFilter();
      });
    }

    for (var vIdx = 0; vIdx < voltageCheckboxes.length; vIdx++) {
      voltageCheckboxes[vIdx].addEventListener("change", function () {
        var vVal = this.getAttribute("data-product-voltage-filter") || "";
        var checked = this.checked;
        for (var k = 0; k < voltageCheckboxes.length; k++) {
          if (voltageCheckboxes[k].getAttribute("data-product-voltage-filter") === vVal) {
            voltageCheckboxes[k].checked = checked;
          }
        }
        selectedVoltages = [];
        for (var m = 0; m < voltageCheckboxes.length; m++) {
          var v = voltageCheckboxes[m].getAttribute("data-product-voltage-filter") || "";
          if (voltageCheckboxes[m].checked && selectedVoltages.indexOf(v) === -1) {
            selectedVoltages.push(v);
          }
        }
        currentPage = 1;
        renderProductsFilter();
      });
    }

    for (var s = 0; s < searchInputs.length; s++) {
      searchInputs[s].addEventListener("input", function () {
        var val = this.value || "";
        searchQuery = val;
        currentPage = 1;
        for (var k = 0; k < searchInputs.length; k++) {
          if (searchInputs[k] !== this) searchInputs[k].value = val;
        }
        renderProductsFilter();
      });
    }

    if (sortSelect) {
      sortSelect.addEventListener("change", function () {
        sortBy = this.value || "popular";
        currentPage = 1;
        renderProductsFilter();
      });
    }

    renderProductsFilter();
  }

  /* ───────────────────────────────────────────────
     9. Product Detail Tabs, All FAQ Accordions & Options
     ─────────────────────────────────────────────── */
  function initProductDetailAndFaqs() {
    var tabsContainer = document.querySelector('[data-prod-tabs-container="true"]');
    if (tabsContainer) {
      var tabBtns = tabsContainer.querySelectorAll("[data-prod-tab-btn]");
      var tabPanels = tabsContainer.querySelectorAll("[data-prod-tab-panel]");

      var activateProdTab = function (targetId) {
        for (var b = 0; b < tabBtns.length; b++) {
          var btn = tabBtns[b];
          var icon = btn.querySelector("[data-prod-tab-icon]");
          var isAct = btn.getAttribute("data-prod-tab-btn") === targetId;
          if (isAct) {
            btn.classList.add("bg-emerald-800", "text-white", "shadow-sm");
            btn.classList.remove(
              "text-slate-600",
              "hover:bg-emerald-100/60",
              "hover:text-emerald-900"
            );
            if (icon) {
              icon.classList.add("text-white");
              icon.classList.remove("text-emerald-700");
            }
          } else {
            btn.classList.remove("bg-emerald-800", "text-white", "shadow-sm");
            btn.classList.add(
              "text-slate-600",
              "hover:bg-emerald-100/60",
              "hover:text-emerald-900"
            );
            if (icon) {
              icon.classList.remove("text-white");
              icon.classList.add("text-emerald-700");
            }
          }
        }
        for (var p = 0; p < tabPanels.length; p++) {
          var panel = tabPanels[p];
          if (panel.getAttribute("data-prod-tab-panel") === targetId) {
            panel.classList.remove("hidden");
            panel.classList.add("block");
          } else {
            panel.classList.add("hidden");
            panel.classList.remove("block");
          }
        }
      };

      for (var i = 0; i < tabBtns.length; i++) {
        tabBtns[i].addEventListener("click", function () {
          var targetId = this.getAttribute("data-prod-tab-btn");
          activateProdTab(targetId);
        });
      }

      var scrollToSpecsBtns = document.querySelectorAll('[data-scroll-to-specs="true"]');
      for (var s = 0; s < scrollToSpecsBtns.length; s++) {
        scrollToSpecsBtns[s].addEventListener("click", function () {
          activateProdTab("specs");
        });
      }

      var scrollToTabBtns = document.querySelectorAll("[data-scroll-to-tab]");
      for (var stIdx = 0; stIdx < scrollToTabBtns.length; stIdx++) {
        scrollToTabBtns[stIdx].addEventListener("click", function () {
          var tId = this.getAttribute("data-scroll-to-tab");
          if (tId) activateProdTab(tId);
        });
      }
    }

    // All FAQ Accordions across the site (ProductDetail, ProductsPage Category FAQ, FaqPage)
    var faqLists = document.querySelectorAll('[data-faq-list="true"]');
    for (var fl = 0; fl < faqLists.length; fl++) {
      (function (faqList) {
        var toggles = faqList.querySelectorAll("[data-faq-toggle]");
        for (var f = 0; f < toggles.length; f++) {
          toggles[f].addEventListener("click", function () {
            var idx = this.getAttribute("data-faq-toggle");
            var body = faqList.querySelector('[data-faq-body="' + idx + '"]');
            var icon = faqList.querySelector('[data-faq-icon="' + idx + '"]');
            if (!body) return;
            var isCurrentlyOpen = !body.classList.contains("hidden");
            if (isCurrentlyOpen) {
              body.classList.add("hidden");
              body.classList.remove("block");
              if (icon) {
                icon.classList.remove("bg-emerald-800", "text-white", "rotate-45");
                icon.classList.add("bg-emerald-100", "text-emerald-800");
              }
            } else {
              body.classList.remove("hidden");
              body.classList.add("block");
              if (icon) {
                icon.classList.add("bg-emerald-800", "text-white", "rotate-45");
                icon.classList.remove("bg-emerald-100", "text-emerald-800");
              }
            }
          });
        }
      })(faqLists[fl]);
    }

    // Product Options Selector
    var optGroups = document.querySelectorAll("[data-opt-group]");
    for (var g = 0; g < optGroups.length; g++) {
      (function (groupEl) {
        var btns = groupEl.querySelectorAll("[data-opt-btn]");
        var selectedLabel = groupEl.querySelector('[data-opt-selected-label="true"]');
        for (var k = 0; k < btns.length; k++) {
          btns[k].addEventListener("click", function () {
            var clicked = this;
            var valLabel = clicked.getAttribute("data-opt-value-label") || "";
            if (selectedLabel) {
              selectedLabel.textContent = valLabel;
            }
            for (var m = 0; m < btns.length; m++) {
              var b = btns[m];
              var isColor = b.getAttribute("data-opt-is-color") === "true";
              if (b === clicked) {
                if (isColor) {
                  b.classList.add("ring-2", "ring-emerald-700", "ring-offset-2", "scale-110");
                  b.classList.remove("ring-1", "ring-slate-200", "hover:scale-105");
                } else {
                  b.classList.add("border-emerald-600", "bg-emerald-700", "text-white", "shadow-xs");
                  b.classList.remove(
                    "border-slate-200",
                    "bg-white",
                    "bg-slate-50",
                    "text-slate-700",
                    "hover:border-emerald-400",
                    "hover:text-emerald-800",
                    "hover:bg-white"
                  );
                }
              } else {
                if (isColor) {
                  b.classList.remove("ring-2", "ring-emerald-700", "ring-offset-2", "scale-110");
                  b.classList.add("ring-1", "ring-slate-200", "hover:scale-105");
                } else {
                  b.classList.remove(
                    "border-emerald-600",
                    "bg-emerald-700",
                    "text-white",
                    "shadow-xs"
                  );
                  b.classList.add(
                    "border-slate-200",
                    "bg-white",
                    "text-slate-700",
                    "hover:border-emerald-400",
                    "hover:text-emerald-800"
                  );
                }
              }
            }
          });
        }
      })(optGroups[g]);
    }

    // 5. Product Review Form & Interactive Star Rating
    var reviewForm = document.querySelector('[data-review-form="true"]');
    if (reviewForm) {
      var starBtns = reviewForm.querySelectorAll("[data-review-star]");
      var ratingInput = reviewForm.querySelector('[data-review-rating-input="true"]');
      var ratingLabel = reviewForm.querySelector('[data-review-rating-label="true"]');
      var nameInput = reviewForm.querySelector('[data-review-name="true"]');
      var orgInput = reviewForm.querySelector('[data-review-org="true"]');
      var commentInput = reviewForm.querySelector('[data-review-comment="true"]');
      var successBox = reviewForm.querySelector('[data-review-success="true"]');
      var reviewsList = document.querySelector('[data-reviews-list="true"]');
      var currentStarRating = 5;
      var ratingLabelsMap = {
        1: "۱ از ۵ (ضعیف)",
        2: "۲ از ۵ (متوسط)",
        3: "۳ از ۵ (خوب)",
        4: "۴ از ۵ (خیلی خوب)",
        5: "۵ از ۵ (عالی)"
      };

      function renderStars(activeScore) {
        for (var s = 0; s < starBtns.length; s++) {
          var starVal = parseInt(starBtns[s].getAttribute("data-review-star") || "5", 10);
          var svgEl = starBtns[s].querySelector('[data-review-star-svg="true"]');
          if (svgEl) {
            if (starVal <= activeScore) {
              svgEl.classList.add("fill-amber-400", "text-amber-400");
              svgEl.classList.remove("text-slate-300");
            } else {
              svgEl.classList.remove("fill-amber-400", "text-amber-400");
              svgEl.classList.add("text-slate-300");
            }
          }
        }
        if (ratingLabel) {
          ratingLabel.textContent = ratingLabelsMap[activeScore] || "۵ از ۵ (عالی)";
        }
      }

      for (var sb = 0; sb < starBtns.length; sb++) {
        (function (btn) {
          var val = parseInt(btn.getAttribute("data-review-star") || "5", 10);
          btn.addEventListener("mouseenter", function () {
            renderStars(val);
          });
          btn.addEventListener("mouseleave", function () {
            renderStars(currentStarRating);
          });
          btn.addEventListener("click", function () {
            currentStarRating = val;
            if (ratingInput) ratingInput.value = String(val);
            renderStars(currentStarRating);
          });
        })(starBtns[sb]);
      }

      reviewForm.addEventListener("submit", function (e) {
        e.preventDefault();
        var userName = nameInput && nameInput.value ? nameInput.value.trim() : "";
        var userOrg = orgInput && orgInput.value ? orgInput.value.trim() : "";
        var userComment = commentInput && commentInput.value ? commentInput.value.trim() : "";
        if (!userName || !userComment) return;

        if (reviewsList) {
          var card = document.createElement("div");
          card.className = "rounded-2xl border border-emerald-200 bg-emerald-50/20 p-5 shadow-xs transition-all";
          var starsHtml = "";
          for (var st = 1; st <= 5; st++) {
            var starClass = st <= currentStarRating ? "fill-amber-400 text-amber-400" : "text-slate-200";
            starsHtml +=
              '<svg class="h-3.5 w-3.5 ' +
              starClass +
              '" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
          }
          var initialChar = userName.charAt(0) || "ک";
          var roleText = userOrg || "خریدار و بهره‌بردار";
          card.innerHTML =
            '<div class="mb-3 flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 pb-3">' +
            '<div class="flex items-center gap-3">' +
            '<div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-sm font-black text-emerald-700 border border-emerald-200/60"></div>' +
            '<div><div class="text-sm font-black text-slate-900" data-rv-name></div><div class="text-[11px] font-medium text-slate-500" data-rv-role></div></div>' +
            "</div>" +
            '<div class="flex flex-col items-end gap-1">' +
            '<div class="flex items-center gap-0.5">' +
            starsHtml +
            "</div>" +
            '<span class="text-[11px] font-medium text-emerald-700">هم‌اکنون</span>' +
            "</div>" +
            "</div>" +
            '<p class="text-sm leading-relaxed text-slate-600" data-rv-comment></p>';
          var avatarBox = card.querySelector(".h-10.w-10");
          if (avatarBox) avatarBox.textContent = initialChar;
          var nameNode = card.querySelector("[data-rv-name]");
          if (nameNode) nameNode.textContent = userName;
          var roleNode = card.querySelector("[data-rv-role]");
          if (roleNode) roleNode.textContent = roleText;
          var commentNode = card.querySelector("[data-rv-comment]");
          if (commentNode) commentNode.textContent = userComment;

          reviewsList.insertBefore(card, reviewsList.firstChild);
        }

        if (nameInput) nameInput.value = "";
        if (orgInput) orgInput.value = "";
        if (commentInput) commentInput.value = "";
        currentStarRating = 5;
        if (ratingInput) ratingInput.value = "5";
        renderStars(5);

        if (successBox) {
          successBox.classList.remove("hidden");
          setTimeout(function () {
            successBox.classList.add("hidden");
          }, 4500);
        }
      });
    }
  }

  /* ───────────────────────────────────────────────
     10. Blog Archive Search, Category Filter & Pagination
     ─────────────────────────────────────────────── */
  function initBlogArchive() {
    var archive = document.querySelector('[data-blog-archive="true"]');
    if (!archive) return;

    var searchInput = document.querySelector('[data-blog-search="true"]');
    var catBtns = document.querySelectorAll("[data-blog-cat-btn]");
    var resetBtn = archive.querySelector('[data-blog-reset="true"]');
    var headingEl = archive.querySelector('[data-blog-heading="true"]');
    var countEl = archive.querySelector('[data-blog-count="true"]');
    var emptyEl = archive.querySelector('[data-blog-empty="true"]');
    var gridEl = archive.querySelector('[data-blog-grid="true"]');
    var items = archive.querySelectorAll('[data-blog-item="true"]');
    var paginationRoot = archive.querySelector('[data-pagination-root="true"]');
    var perPage = paginationRoot
      ? parseInt(paginationRoot.getAttribute("data-per-page") || "6", 10)
      : 6;

    var activeCat = "همه";
    var query = "";
    var currentPage = 1;

    function renderBlogFilter() {
      var q = query.trim();
      var matchingItems = [];

      for (var i = 0; i < items.length; i++) {
        var item = items[i];
        var itemCat = item.getAttribute("data-blog-category") || "";
        var itemText = item.getAttribute("data-blog-search-text") || "";
        var matchCat = activeCat === "همه" || itemCat === activeCat;
        var matchQ = !q || itemText.indexOf(q) !== -1;

        if (matchCat && matchQ) {
          matchingItems.push(item);
        } else {
          item.classList.add("hidden");
        }
      }

      for (var c = 0; c < catBtns.length; c++) {
        var btn = catBtns[c];
        var bCat = btn.getAttribute("data-blog-cat-btn");
        if (bCat === activeCat) {
          btn.classList.add(
            "bg-emerald-700",
            "text-white",
            "shadow-md",
            "shadow-emerald-700/25"
          );
          btn.classList.remove(
            "border",
            "border-slate-200",
            "bg-white",
            "text-slate-600",
            "hover:border-emerald-400",
            "hover:text-emerald-700"
          );
        } else {
          btn.classList.remove(
            "bg-emerald-700",
            "text-white",
            "shadow-md",
            "shadow-emerald-700/25"
          );
          btn.classList.add(
            "border",
            "border-slate-200",
            "bg-white",
            "text-slate-600",
            "hover:border-emerald-400",
            "hover:text-emerald-700"
          );
        }
      }

      if (headingEl && !window.location.pathname.includes("/category/")) {
        headingEl.textContent = activeCat === "همه" && !q ? "آخرین مقالات" : "نتایج جستجو";
      }
      if (countEl) {
        countEl.textContent = toFa(matchingItems.length);
      }
      if (resetBtn) {
        if (activeCat !== "همه" || q) {
          resetBtn.classList.remove("hidden");
          resetBtn.classList.add("inline-flex");
        } else {
          resetBtn.classList.add("hidden");
          resetBtn.classList.remove("inline-flex");
        }
      }
      if (emptyEl && gridEl) {
        if (matchingItems.length === 0) {
          emptyEl.classList.remove("hidden");
          emptyEl.classList.add("block");
          gridEl.classList.add("hidden");
        } else {
          emptyEl.classList.add("hidden");
          emptyEl.classList.remove("block");
          gridEl.classList.remove("hidden");
        }
      }

      currentPage = updatePaginationUI(
        paginationRoot,
        matchingItems,
        currentPage,
        perPage,
        function (action) {
          if (action === "prev") currentPage = Math.max(1, currentPage - 1);
          else if (action === "next") currentPage = currentPage + 1;
          else currentPage = Number(action) || 1;
          renderBlogFilter();
          archive.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      );
    }

    if (searchInput) {
      searchInput.addEventListener("input", function () {
        query = this.value || "";
        currentPage = 1;
        renderBlogFilter();
      });
    }
    for (var i = 0; i < catBtns.length; i++) {
      catBtns[i].addEventListener("click", function () {
        activeCat = this.getAttribute("data-blog-cat-btn") || "همه";
        currentPage = 1;
        renderBlogFilter();
      });
    }
    if (resetBtn) {
      resetBtn.addEventListener("click", function () {
        activeCat = "همه";
        query = "";
        currentPage = 1;
        if (searchInput) searchInput.value = "";
        renderBlogFilter();
      });
    }

    renderBlogFilter();
  }

  /* ───────────────────────────────────────────────
     11. Recruitment Department Tabs
     ─────────────────────────────────────────────── */
  function initRecruitmentTabs() {
    var root = document.querySelector('[data-recruit-tabs="true"]');
    if (!root) return;

    var btns = root.querySelectorAll("[data-recruit-tab-btn]");
    var panels = root.querySelectorAll("[data-recruit-panel]");

    for (var i = 0; i < btns.length; i++) {
      btns[i].addEventListener("click", function () {
        var target = this.getAttribute("data-recruit-tab-btn") || "all";

        for (var b = 0; b < btns.length; b++) {
          var btn = btns[b];
          var isAct = btn.getAttribute("data-recruit-tab-btn") === target;
          if (isAct) {
            btn.classList.add("bg-emerald-700", "text-white", "shadow-sm");
            btn.classList.remove(
              "border",
              "border-slate-200",
              "bg-slate-50",
              "text-slate-700",
              "hover:border-emerald-300",
              "hover:text-emerald-800"
            );
          } else {
            btn.classList.remove("bg-emerald-700", "text-white", "shadow-sm");
            btn.classList.add(
              "border",
              "border-slate-200",
              "bg-slate-50",
              "text-slate-700",
              "hover:border-emerald-300",
              "hover:text-emerald-800"
            );
          }
        }

        for (var p = 0; p < panels.length; p++) {
          var panel = panels[p];
          var pKey = panel.getAttribute("data-recruit-panel");
          if (target === "all" || pKey === target) {
            panel.classList.remove("hidden");
          } else {
            panel.classList.add("hidden");
          }
        }
      });
    }
  }

  /* ───────────────────────────────────────────────
     12. Share Link Button (BlogPost)
     ─────────────────────────────────────────────── */
  function initShareButtons() {
    var shareBtns = document.querySelectorAll('[data-share-btn="true"]');
    for (var i = 0; i < shareBtns.length; i++) {
      shareBtns[i].addEventListener("click", function () {
        var label = this.querySelector('[data-share-label="true"]');
        var url = window.location.href;
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(url).catch(function () {});
        }
        if (label) {
          var orig = label.textContent;
          label.textContent = "لینک مقاله کپی شد ✓";
          setTimeout(function () {
            label.textContent = orig;
          }, 2200);
        }
      });
    }
  }

  /* ───────────────────────────────────────────────
     13. Contact & Recruitment Form Submission & Reset
     ─────────────────────────────────────────────── */
  function initContactForm() {
    var form = document.querySelector('[data-contact-form="true"]');
    var successBox = document.querySelector('[data-contact-success="true"]');
    var resetBtn = document.querySelector('[data-contact-reset="true"]');

    if (form && successBox) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        form.classList.add("hidden");
        form.classList.remove("flex");
        successBox.classList.remove("hidden");
        successBox.classList.add("flex");
      });
    }
    if (resetBtn && form && successBox) {
      resetBtn.addEventListener("click", function () {
        form.reset();
        successBox.classList.add("hidden");
        successBox.classList.remove("flex");
        form.classList.remove("hidden");
        form.classList.add("flex");
      });
    }
  }

  /* ───────────────────────────────────────────────
     14. Mega-Menu Tabs, Mobile Accordions & Live AJAX Search
     ─────────────────────────────────────────────── */
  function initMegaMenuAndSearch() {
    // 14a. Desktop Mega-Menu Triggers (Hover + Click)
    var headerEl = document.querySelector('[data-navbar-header="true"]');
    var desktopTriggers = document.querySelectorAll("[data-desktop-mega-trigger]");
    var desktopPanels = document.querySelectorAll("[data-desktop-mega-panel]");

    function setDesktopMegaMenu(activeKey) {
      for (var t = 0; t < desktopTriggers.length; t++) {
        var trig = desktopTriggers[t];
        var key = trig.getAttribute("data-desktop-mega-trigger");
        var isAct = key === activeKey;
        trig.setAttribute("aria-expanded", String(isAct));
      }
      for (var p = 0; p < desktopPanels.length; p++) {
        var panel = desktopPanels[p];
        var pKey = panel.getAttribute("data-desktop-mega-panel");
        if (pKey === activeKey) {
          panel.classList.remove("invisible", "translate-y-1", "opacity-0");
          panel.classList.add("visible", "translate-y-0", "opacity-100");
        } else {
          panel.classList.add("invisible", "translate-y-1", "opacity-0");
          panel.classList.remove("visible", "translate-y-0", "opacity-100");
        }
      }
    }

    for (var d = 0; d < desktopTriggers.length; d++) {
      desktopTriggers[d].addEventListener("mouseenter", function () {
        setDesktopMegaMenu(this.getAttribute("data-desktop-mega-trigger"));
      });
      desktopTriggers[d].addEventListener("click", function (e) {
        e.preventDefault();
        var key = this.getAttribute("data-desktop-mega-trigger");
        var isAlreadyOpen = this.getAttribute("aria-expanded") === "true";
        setDesktopMegaMenu(isAlreadyOpen ? null : key);
      });
    }
    if (headerEl) {
      headerEl.addEventListener("mouseleave", function () {
        setDesktopMegaMenu(null);
      });
    }

    // 14b. Products Mega-Menu Tab Switching (Desktop)
    var prodTabs = document.querySelectorAll("[data-mega-prod-tab]");
    var prodPanels = document.querySelectorAll("[data-mega-prod-panel]");
    function activateProdTab(slug) {
      for (var i = 0; i < prodTabs.length; i++) {
        var btn = prodTabs[i];
        var isActive = btn.getAttribute("data-mega-prod-tab") === slug;
        if (isActive) {
          btn.classList.add("bg-white", "text-emerald-950", "shadow-sm", "ring-1", "ring-emerald-300/90");
          btn.classList.remove("text-slate-700", "hover:bg-white/70");
        } else {
          btn.classList.remove("bg-white", "text-emerald-950", "shadow-sm", "ring-1", "ring-emerald-300/90");
          btn.classList.add("text-slate-700", "hover:bg-white/70");
        }
      }
      for (var j = 0; j < prodPanels.length; j++) {
        var panel = prodPanels[j];
        if (panel.getAttribute("data-mega-prod-panel") === slug) {
          panel.classList.remove("hidden");
          panel.classList.add("block");
        } else {
          panel.classList.add("hidden");
          panel.classList.remove("block");
        }
      }
    }
    for (var i = 0; i < prodTabs.length; i++) {
      prodTabs[i].addEventListener("mouseenter", function () {
        activateProdTab(this.getAttribute("data-mega-prod-tab"));
      });
      prodTabs[i].addEventListener("click", function () {
        activateProdTab(this.getAttribute("data-mega-prod-tab"));
      });
      prodTabs[i].addEventListener("focus", function () {
        activateProdTab(this.getAttribute("data-mega-prod-tab"));
      });
    }

    // 14c. Projects Mega-Menu Tab Switching (Desktop: type vs city)
    var projTabs = document.querySelectorAll("[data-mega-proj-tab]");
    var projPanels = document.querySelectorAll("[data-mega-proj-panel]");
    function activateProjTab(mode) {
      for (var k = 0; k < projTabs.length; k++) {
        var pBtn = projTabs[k];
        var active = pBtn.getAttribute("data-mega-proj-tab") === mode;
        if (active) {
          pBtn.classList.add("bg-white", "text-emerald-950", "shadow-sm", "ring-1", "ring-emerald-300/90");
          pBtn.classList.remove("text-slate-700", "hover:bg-white/70");
        } else {
          pBtn.classList.remove("bg-white", "text-emerald-950", "shadow-sm", "ring-1", "ring-emerald-300/90");
          pBtn.classList.add("text-slate-700", "hover:bg-white/70");
        }
      }
      for (var m = 0; m < projPanels.length; m++) {
        var pPanel = projPanels[m];
        if (pPanel.getAttribute("data-mega-proj-panel") === mode) {
          pPanel.classList.remove("hidden");
          pPanel.classList.add("block");
        } else {
          pPanel.classList.add("hidden");
          pPanel.classList.remove("block");
        }
      }
    }
    for (var n = 0; n < projTabs.length; n++) {
      projTabs[n].addEventListener("mouseenter", function () {
        activateProjTab(this.getAttribute("data-mega-proj-tab"));
      });
      projTabs[n].addEventListener("click", function () {
        activateProjTab(this.getAttribute("data-mega-proj-tab"));
      });
    }

    // 14d. Mobile Off-Canvas Accordion Sections & Mobile Mega-Menu Inner Tabs
    var mobileAccBtns = document.querySelectorAll("[data-mobile-acc-btn]");
    for (var a = 0; a < mobileAccBtns.length; a++) {
      mobileAccBtns[a].addEventListener("click", function () {
        var key = this.getAttribute("data-mobile-acc-btn");
        var panel = document.querySelector('[data-mobile-acc-panel="' + key + '"]');
        var icon = this.querySelector("[data-mobile-acc-icon]");
        if (!panel) return;
        var isHidden = panel.classList.contains("hidden");
        if (isHidden) {
          panel.classList.remove("hidden");
          panel.classList.add("flex");
          this.setAttribute("aria-expanded", "true");
          if (icon) icon.classList.add("rotate-180");
        } else {
          panel.classList.add("hidden");
          panel.classList.remove("flex");
          this.setAttribute("aria-expanded", "false");
          if (icon) icon.classList.remove("rotate-180");
        }
      });
    }

    // Mobile Products Mega-Menu Group Tabs
    var mobProdTabs = document.querySelectorAll("[data-mobile-prod-tab]");
    var mobProdPanels = document.querySelectorAll("[data-mobile-prod-panel]");
    for (var mp = 0; mp < mobProdTabs.length; mp++) {
      mobProdTabs[mp].addEventListener("click", function () {
        var targetIdx = this.getAttribute("data-mobile-prod-tab");
        for (var b = 0; b < mobProdTabs.length; b++) {
          var tBtn = mobProdTabs[b];
          if (tBtn.getAttribute("data-mobile-prod-tab") === targetIdx) {
            tBtn.classList.add("border-emerald-600", "bg-emerald-700", "text-white", "shadow-xs");
            tBtn.classList.remove("border-slate-200", "bg-slate-50", "text-slate-700");
          } else {
            tBtn.classList.remove("border-emerald-600", "bg-emerald-700", "text-white", "shadow-xs");
            tBtn.classList.add("border-slate-200", "bg-slate-50", "text-slate-700");
          }
        }
        for (var p = 0; p < mobProdPanels.length; p++) {
          var pnl = mobProdPanels[p];
          if (pnl.getAttribute("data-mobile-prod-panel") === targetIdx) {
            pnl.classList.remove("hidden");
            pnl.classList.add("block");
          } else {
            pnl.classList.add("hidden");
            pnl.classList.remove("block");
          }
        }
      });
    }

    // Mobile Projects Mega-Menu Mode Tabs (type vs city)
    var mobProjTabs = document.querySelectorAll("[data-mobile-proj-tab]");
    var mobProjPanels = document.querySelectorAll("[data-mobile-proj-panel]");
    for (var mj = 0; mj < mobProjTabs.length; mj++) {
      mobProjTabs[mj].addEventListener("click", function () {
        var mode = this.getAttribute("data-mobile-proj-tab");
        for (var b = 0; b < mobProjTabs.length; b++) {
          var jBtn = mobProjTabs[b];
          if (jBtn.getAttribute("data-mobile-proj-tab") === mode) {
            jBtn.classList.add("bg-white", "text-emerald-800", "shadow-2xs");
            jBtn.classList.remove("text-slate-600");
          } else {
            jBtn.classList.remove("bg-white", "text-emerald-800", "shadow-2xs");
            jBtn.classList.add("text-slate-600");
          }
        }
        for (var p = 0; p < mobProjPanels.length; p++) {
          var jPnl = mobProjPanels[p];
          if (jPnl.getAttribute("data-mobile-proj-panel") === mode) {
            jPnl.classList.remove("hidden");
            jPnl.classList.add("grid", "grid-cols-2", "gap-1.5");
          } else {
            jPnl.classList.add("hidden");
            jPnl.classList.remove("grid", "grid-cols-2", "gap-1.5");
          }
        }
      });
    }

    // 14e. Global AJAX Search Modal (jet-ajax-search)
    var searchModal = document.querySelector('[data-search-modal="true"]');
    var searchOpenBtns = document.querySelectorAll('[data-search-modal-open="true"]');
    var searchCloseBtns = document.querySelectorAll('[data-search-modal-close="true"]');
    var searchInput = document.querySelector('[data-global-search-input="true"]');
    var searchItems = document.querySelectorAll("[data-search-item]");

    function syncSearchButtonsState(isOpen) {
      for (var s = 0; s < searchOpenBtns.length; s++) {
        var btn = searchOpenBtns[s];
        btn.setAttribute("aria-expanded", String(isOpen));
        if (btn.classList.contains("grid")) {
          if (isOpen) {
            btn.classList.add("border-emerald-500", "bg-emerald-100", "text-emerald-900", "shadow-inner");
            btn.classList.remove("border-slate-200", "bg-slate-50/80", "text-slate-700");
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>';
            btn.setAttribute("aria-label", "بستن کادر جستجو");
          } else {
            btn.classList.remove("border-emerald-500", "bg-emerald-100", "text-emerald-900", "shadow-inner");
            btn.classList.add("border-slate-200", "bg-slate-50/80", "text-slate-700");
            btn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>';
            btn.setAttribute("aria-label", "جستجو در محصولات و پروژه‌ها");
          }
        }
      }
    }

    function openSearchModal() {
      if (!searchModal) return;
      searchModal.classList.remove("hidden");
      searchModal.classList.add("block");
      syncSearchButtonsState(true);
      if (searchInput) {
        setTimeout(function () {
          searchInput.focus();
        }, 50);
      }
    }
    function closeSearchModal() {
      if (!searchModal) return;
      searchModal.classList.add("hidden");
      searchModal.classList.remove("block");
      syncSearchButtonsState(false);
    }
    function toggleSearchModal() {
      if (!searchModal) return;
      var isOpen = !searchModal.classList.contains("hidden");
      if (isOpen) {
        closeSearchModal();
      } else {
        openSearchModal();
      }
    }

    for (var s = 0; s < searchOpenBtns.length; s++) {
      searchOpenBtns[s].addEventListener("click", function (e) {
        e.preventDefault();
        if (this.closest && this.closest('[data-mobile-sidebar="true"]')) {
          var mobileMenu = document.querySelector('[data-mobile-menu="true"]');
          if (mobileMenu) {
            mobileMenu.classList.add("hidden");
            mobileMenu.classList.remove("block");
            document.body.style.overflow = "";
          }
          openSearchModal();
        } else {
          toggleSearchModal();
        }
      });
    }
    for (var c = 0; c < searchCloseBtns.length; c++) {
      searchCloseBtns[c].addEventListener("click", closeSearchModal);
    }
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") closeSearchModal();
    });

    if (searchInput) {
      searchInput.addEventListener("input", function () {
        var q = (this.value || "").trim().toLowerCase();
        for (var idx = 0; idx < searchItems.length; idx++) {
          var item = searchItems[idx];
          var text = (item.getAttribute("data-search-item") || "").toLowerCase();
          if (!q || text.indexOf(q) !== -1) {
            item.classList.remove("hidden");
          } else {
            item.classList.add("hidden");
          }
        }
      });
    }
  }

  /* ───────────────────────────────────────────────
     15. Complete Purchase Journey (PDP, Cart & Checkout)
     ─────────────────────────────────────────────── */
  function initPurchaseJourney() {
    var nativeWooPage = document.querySelector('[data-woocommerce-page="cart"], [data-woocommerce-page="checkout"], [data-woocommerce-page="account"]');
    if (nativeWooPage) {
      var nativeCartUrl = nativeWooPage.getAttribute("data-woocommerce-cart-url") || "/cart/";
      var nativeCartButtons = document.querySelectorAll('[data-cart-modal-open="true"]');
      for (var nativeButtonIndex = 0; nativeButtonIndex < nativeCartButtons.length; nativeButtonIndex++) {
        nativeCartButtons[nativeButtonIndex].addEventListener("click", function (event) {
          event.preventDefault();
          window.location.assign(nativeCartUrl);
        });
      }
      return;
    }

    var CART_KEY = "esfahan_emrooz_cart_v1";

    function loadCart() {
      try {
        var raw = localStorage.getItem(CART_KEY);
        if (raw) {
          var parsed = JSON.parse(raw);
          if (Array.isArray(parsed) && parsed.length > 0) return parsed;
        }
      } catch (e) {}
      return null;
    }

    function saveCart(items) {
      try {
        localStorage.setItem(CART_KEY, JSON.stringify(items));
      } catch (e) {}
      updateHeaderCartBadge();
    }

    function updateHeaderCartBadge() {
      var badge = document.querySelector('[data-header-cart-badge="true"]');
      if (!badge) return;
      var modalRows = document.querySelectorAll("[data-cart-modal-row]");
      if (modalRows.length > 0) {
        var activeRows = 0;
        for (var i = 0; i < modalRows.length; i++) {
          var q = parseInt(modalRows[i].getAttribute("data-current-qty") || "0", 10);
          if (q > 0) activeRows++;
        }
        badge.textContent = toPersianDigits(activeRows);
        return;
      }
      var items = loadCart();
      var count = items ? items.length : 3;
      badge.textContent = toPersianDigits(count);
    }

    // 15.0 Interactive Cart Modal (Open/Close, Qty +/-, Remove, Quick-Add, Restore)
    var cartModal = document.querySelector('[data-cart-modal="true"]');
    var cartOpenBtns = document.querySelectorAll('[data-cart-modal-open="true"]');
    var cartCloseBtns = document.querySelectorAll(
      '[data-cart-modal-close="true"], [data-cart-modal-backdrop="true"]'
    );

    function openCartModal() {
      if (!cartModal) return;
      cartModal.classList.remove("hidden");
      cartModal.classList.add("flex");
      cartModal.setAttribute("aria-hidden", "false");
      document.body.style.overflow = "hidden";
    }

    function closeCartModal() {
      if (!cartModal) return;
      cartModal.classList.add("hidden");
      cartModal.classList.remove("flex");
      cartModal.setAttribute("aria-hidden", "true");
      document.body.style.overflow = "";
    }

    for (var co = 0; co < cartOpenBtns.length; co++) {
      cartOpenBtns[co].addEventListener("click", function (e) {
        e.preventDefault();
        openCartModal();
      });
    }
    for (var cc = 0; cc < cartCloseBtns.length; cc++) {
      cartCloseBtns[cc].addEventListener("click", closeCartModal);
    }
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape") closeCartModal();
    });

    if (cartModal) {
      var modalRows = cartModal.querySelectorAll("[data-cart-modal-row]");
      var modalCountEl = cartModal.querySelector('[data-cart-modal-count="true"]');
      var modalTotalEl = cartModal.querySelector('[data-cart-modal-total="true"]');
      var modalEmptyEl = cartModal.querySelector('[data-cart-modal-empty="true"]');
      var modalRestoreBtn = cartModal.querySelector('[data-cart-modal-restore="true"]');
      var modalAddBtns = cartModal.querySelectorAll("[data-cart-modal-add]");

      function syncCartModalUI() {
        var total = 0;
        var activeCount = 0;
        for (var i = 0; i < modalRows.length; i++) {
          var row = modalRows[i];
          var slug = row.getAttribute("data-cart-modal-row") || "";
          var unitPrice = parseInt(row.getAttribute("data-unit-price") || "0", 10);
          var qty = parseInt(row.getAttribute("data-current-qty") || "0", 10);
          var qtyEl = row.querySelector('[data-cart-modal-qty="' + slug + '"]');
          var rowTotalEl = row.querySelector('[data-cart-modal-row-total="' + slug + '"]');

          if (qty > 0) {
            activeCount++;
            total += unitPrice * qty;
            row.classList.remove("hidden");
            row.classList.add("flex", "flex-col", "sm:flex-row");
            if (qtyEl) qtyEl.textContent = toPersianDigits(qty);
            if (rowTotalEl) rowTotalEl.textContent = formatToman(unitPrice * qty);
          } else {
            row.classList.add("hidden");
            row.classList.remove("flex", "flex-col", "sm:flex-row");
          }
        }

        if (modalCountEl) modalCountEl.textContent = toPersianDigits(activeCount) + " کالا";
        if (modalTotalEl) modalTotalEl.textContent = formatToman(total);
        if (modalEmptyEl) {
          if (activeCount === 0) {
            modalEmptyEl.classList.remove("hidden");
            modalEmptyEl.classList.add("flex");
          } else {
            modalEmptyEl.classList.add("hidden");
            modalEmptyEl.classList.remove("flex");
          }
        }
        updateHeaderCartBadge();
      }

      for (var mr = 0; mr < modalRows.length; mr++) {
        (function (row) {
          var slug = row.getAttribute("data-cart-modal-row") || "";
          var step = parseInt(row.getAttribute("data-step-qty") || "1", 10);
          var decBtn = row.querySelector('[data-cart-modal-dec="' + slug + '"]');
          var incBtn = row.querySelector('[data-cart-modal-inc="' + slug + '"]');
          var remBtn = row.querySelector('[data-cart-modal-remove="' + slug + '"]');

          if (decBtn) {
            decBtn.addEventListener("click", function () {
              var cur = parseInt(row.getAttribute("data-current-qty") || "0", 10);
              row.setAttribute("data-current-qty", String(Math.max(0, cur - step)));
              syncCartModalUI();
            });
          }
          if (incBtn) {
            incBtn.addEventListener("click", function () {
              var cur = parseInt(row.getAttribute("data-current-qty") || "0", 10);
              row.setAttribute("data-current-qty", String(cur + step));
              syncCartModalUI();
            });
          }
          if (remBtn) {
            remBtn.addEventListener("click", function () {
              row.setAttribute("data-current-qty", "0");
              syncCartModalUI();
            });
          }
        })(modalRows[mr]);
      }

      for (var ma = 0; ma < modalAddBtns.length; ma++) {
        modalAddBtns[ma].addEventListener("click", function () {
          var targetSlug = this.getAttribute("data-cart-modal-add") || "";
          var targetRow = cartModal.querySelector('[data-cart-modal-row="' + targetSlug + '"]');
          if (!targetRow) return;
          var cur = parseInt(targetRow.getAttribute("data-current-qty") || "0", 10);
          var defAdd = parseInt(targetRow.getAttribute("data-default-add-qty") || "1", 10);
          targetRow.setAttribute("data-current-qty", String(cur + defAdd));
          syncCartModalUI();
        });
      }

      if (modalRestoreBtn) {
        modalRestoreBtn.addEventListener("click", function () {
          for (var i = 0; i < modalRows.length; i++) {
            var initQ = modalRows[i].getAttribute("data-initial-qty") || "0";
            modalRows[i].setAttribute("data-current-qty", initQ);
          }
          syncCartModalUI();
        });
      }

      syncCartModalUI();
    }

    updateHeaderCartBadge();

    // 15a. PDP Quantity Stepper & Live Total + Add to Cart
    var pdpRoot = document.querySelector('[data-pdp-qty-root="true"]');
    if (pdpRoot) {
      var unitPrice = parseInt(pdpRoot.getAttribute("data-unit-price") || "0", 10);
      var minQty = parseInt(pdpRoot.getAttribute("data-min-qty") || "1", 10);
      var stepQty = parseInt(pdpRoot.getAttribute("data-step-qty") || "1", 10);
      var qtyValueEl = pdpRoot.querySelector('[data-pdp-qty-value="true"]');
      var totalValueEl = pdpRoot.querySelector('[data-pdp-total-value="true"]');
      var decBtn = pdpRoot.querySelector('[data-pdp-qty-dec="true"]');
      var incBtn = pdpRoot.querySelector('[data-pdp-qty-inc="true"]');
      var currentQty = minQty;

      function renderPdpQty() {
        if (qtyValueEl) qtyValueEl.textContent = toPersianDigits(currentQty);
        if (totalValueEl) totalValueEl.textContent = formatToman(currentQty * unitPrice);
      }

      if (decBtn) {
        decBtn.addEventListener("click", function () {
          if (currentQty - stepQty >= minQty) {
            currentQty -= stepQty;
            renderPdpQty();
          }
        });
      }
      if (incBtn) {
        incBtn.addEventListener("click", function () {
          currentQty += stepQty;
          renderPdpQty();
        });
      }

      var addToCartBtn = document.querySelector('[data-pdp-add-to-cart="true"]');
      var toastBox = document.querySelector('[data-pdp-cart-toast="true"]');
      if (addToCartBtn) {
        addToCartBtn.addEventListener("click", function () {
          var slug = this.getAttribute("data-product-slug") || "";
          var title = this.getAttribute("data-product-title") || "";
          var sku = this.getAttribute("data-product-sku") || "";
          var price = parseInt(this.getAttribute("data-product-price") || "0", 10);
          var unit = this.getAttribute("data-product-unit") || "عدد";
          var image = this.getAttribute("data-product-image") || "";
          var category = this.getAttribute("data-product-category") || "";

          var cart = loadCart() || [];
          var found = false;
          for (var i = 0; i < cart.length; i++) {
            if (cart[i].slug === slug) {
              cart[i].qty = (cart[i].qty || minQty) + currentQty;
              found = true;
              break;
            }
          }
          if (!found) {
            cart.push({
              slug: slug,
              title: title,
              sku: sku,
              priceNumeric: price,
              unit: unit,
              minOrder: minQty,
              step: stepQty,
              qty: currentQty,
              image: image,
              categoryTitle: category
            });
          }
          saveCart(cart);
          if (toastBox) {
            toastBox.classList.remove("hidden");
            toastBox.classList.add("flex");
          }
        });
      }
    }

    // 15b. Cart Page Interactive Rows & Totals
    var cartPage = document.querySelector('[data-cart-page="true"]');
    if (cartPage) {
      function recalcCartDomTotals() {
        var rows = cartPage.querySelectorAll("[data-cart-item-row]");
        var subtotal = 0;
        var activeCount = 0;
        for (var i = 0; i < rows.length; i++) {
          var r = rows[i];
          if (r.classList.contains("hidden")) continue;
          activeCount++;
          var price = parseInt(r.getAttribute("data-unit-price") || "0", 10);
          var qtyAttr = parseInt(r.getAttribute("data-current-qty") || r.getAttribute("data-min-qty") || "1", 10);
          var rowTotal = price * qtyAttr;
          subtotal += rowTotal;
          var rowTotalEl = r.querySelector("[data-cart-row-total]");
          if (rowTotalEl) rowTotalEl.textContent = formatToman(rowTotal);
          var rowQtyEl = r.querySelector("[data-cart-qty-val]");
          if (rowQtyEl) rowQtyEl.textContent = toPersianDigits(qtyAttr);
        }
        var tax = Math.round(subtotal * 0.1);
        var grand = subtotal + tax;

        var countEl = cartPage.querySelector('[data-cart-count="true"]');
        var subEl = cartPage.querySelector('[data-cart-subtotal="true"]');
        var taxEl = cartPage.querySelector('[data-cart-tax="true"]');
        var grandEl = cartPage.querySelector('[data-cart-grandtotal="true"]');
        var emptyEl = cartPage.querySelector('[data-cart-empty="true"]');
        var listEl = cartPage.querySelector('[data-cart-items-list="true"]');

        if (countEl) countEl.textContent = toPersianDigits(activeCount);
        if (subEl) subEl.textContent = formatToman(subtotal);
        if (taxEl) taxEl.textContent = formatToman(tax);
        if (grandEl) grandEl.textContent = formatToman(grand);

        if (activeCount === 0 && emptyEl && listEl) {
          listEl.classList.add("hidden");
          emptyEl.classList.remove("hidden");
          emptyEl.classList.add("flex");
        }
      }

      var rows = cartPage.querySelectorAll("[data-cart-item-row]");
      for (var rIdx = 0; rIdx < rows.length; rIdx++) {
        (function (row) {
          var minQ = parseInt(row.getAttribute("data-min-qty") || "1", 10);
          var stepQ = parseInt(row.getAttribute("data-step-qty") || "1", 10);
          row.setAttribute("data-current-qty", String(minQ));

          var decB = row.querySelector("[data-cart-qty-dec]");
          var incB = row.querySelector("[data-cart-qty-inc]");
          var remB = row.querySelector("[data-cart-remove]");

          if (decB) {
            decB.addEventListener("click", function () {
              var cur = parseInt(row.getAttribute("data-current-qty") || String(minQ), 10);
              if (cur - stepQ >= minQ) {
                row.setAttribute("data-current-qty", String(cur - stepQ));
                recalcCartDomTotals();
              }
            });
          }
          if (incB) {
            incB.addEventListener("click", function () {
              var cur = parseInt(row.getAttribute("data-current-qty") || String(minQ), 10);
              row.setAttribute("data-current-qty", String(cur + stepQ));
              recalcCartDomTotals();
            });
          }
          if (remB) {
            remB.addEventListener("click", function () {
              row.classList.add("hidden");
              recalcCartDomTotals();
            });
          }
        })(rows[rIdx]);
      }

      var clearBtn = cartPage.querySelector('[data-cart-clear="true"]');
      if (clearBtn) {
        clearBtn.addEventListener("click", function () {
          for (var cIdx = 0; cIdx < rows.length; cIdx++) {
            rows[cIdx].classList.add("hidden");
          }
          localStorage.removeItem(CART_KEY);
          recalcCartDomTotals();
        });
      }
    }

    // 15c. Checkout Form & Printable Proforma / Order Review Receipt
    var checkoutPage = document.querySelector('[data-checkout-page="true"]');
    if (checkoutPage) {
      var buyerRadios = checkoutPage.querySelectorAll("[data-buyer-type-radio]");
      var legalFields = checkoutPage.querySelector('[data-legal-fields="true"]');
      for (var bIdx = 0; bIdx < buyerRadios.length; bIdx++) {
        buyerRadios[bIdx].addEventListener("change", function () {
          if (!legalFields) return;
          if (this.value === "real") {
            legalFields.classList.add("opacity-60");
          } else {
            legalFields.classList.remove("opacity-60");
          }
        });
      }

      var checkoutForm = checkoutPage.querySelector('[data-checkout-form="true"]');
      var receiptBox = checkoutPage.querySelector('[data-checkout-receipt="true"]');
      if (checkoutForm && receiptBox) {
        checkoutForm.addEventListener("submit", function (e) {
          e.preventDefault();
          var formData = new FormData(checkoutForm);
          var fullName = formData.get("fullName") || "مشتری گرامی";
          var orgName = formData.get("orgName") || "پیمانکار / مجموعه عمرانی";
          var phone = formData.get("phone") || "۰۹۱۲۰۰۰۰۰۰۰";
          var payMethod = formData.get("paymentMethod") || "mellat";

          var nameTarget = receiptBox.querySelector('[data-receipt-customer="true"]');
          var phoneTarget = receiptBox.querySelector('[data-receipt-phone="true"]');
          var methodTarget = receiptBox.querySelector('[data-receipt-method="true"]');
          var codeTarget = receiptBox.querySelector('[data-receipt-code="true"]');

          if (nameTarget) nameTarget.textContent = fullName + (orgName ? " (" + orgName + ")" : "");
          if (phoneTarget) phoneTarget.textContent = String(phone);
          if (methodTarget) {
            if (payMethod === "mellat") {
              methodTarget.textContent = "درگاه هوشمند بانک ملت (پرداخت آنلاین شاپرک)";
            } else if (payMethod === "proforma") {
              methodTarget.textContent = "صدور پیش‌فاکتور رسمی ۱۰٪ ارزش افزوده (حواله پایا/ساتنا)";
            } else {
              methodTarget.textContent = "خرید اعتباری پروژه‌ای و سامانه تدارکات دولت (ستاد)";
            }
          }
          if (codeTarget) {
            var rnd = Math.floor(10000 + Math.random() * 90000);
            codeTarget.textContent = "ESE-1404-" + toPersianDigits(rnd);
          }

          checkoutForm.classList.add("hidden");
          receiptBox.classList.remove("hidden");
          receiptBox.classList.add("block");
          window.scrollTo({ top: 0, behavior: "smooth" });
        });
      }
    }
  }

  /* ───────────────────────────────────────────────
     16. Hero Multi-Project Showcase, Background Cross-Fade & Video Modal
     ─────────────────────────────────────────────── */
  function initHeroVideo() {
    var heroSection = document.querySelector('[data-hero-section="true"]');
    var bgSlides = heroSection
      ? heroSection.querySelectorAll("[data-hero-bg-slide]")
      : [];
    var cardSlides = heroSection
      ? heroSection.querySelectorAll("[data-hero-card-slide]")
      : [];
    var slideBtns = heroSection
      ? heroSection.querySelectorAll("[data-hero-slide-btn]")
      : [];
    var prevBtn = heroSection
      ? heroSection.querySelector('[data-hero-prev="true"]')
      : null;
    var nextBtn = heroSection
      ? heroSection.querySelector('[data-hero-next="true"]')
      : null;
    var titleEl = heroSection
      ? heroSection.querySelector('[data-hero-project-title="true"]')
      : null;
    var provinceEl = heroSection
      ? heroSection.querySelector('[data-hero-project-province="true"]')
      : null;

    var modal = document.querySelector('[data-hero-video-modal="true"]');
    var modalVideo = document.querySelector('[data-hero-modal-video="true"]');
    var openBtns = document.querySelectorAll('[data-hero-modal-open="true"]');
    var closeBtns = document.querySelectorAll(
      '[data-hero-modal-close="true"], [data-hero-modal-backdrop="true"]'
    );

    var activeIdx = 0;
    var totalSlides = bgSlides.length;

    function setHeroSlide(nextIdx) {
      if (!totalSlides) return;
      activeIdx = (nextIdx + totalSlides) % totalSlides;

      for (var i = 0; i < bgSlides.length; i++) {
        var bgEl = bgSlides[i];
        if (i === activeIdx) {
          bgEl.classList.remove("opacity-0", "scale-105");
          bgEl.classList.add("opacity-100", "scale-100");
          if (titleEl) {
            titleEl.textContent = bgEl.getAttribute("data-project-title") || "";
          }
          if (provinceEl) {
            provinceEl.textContent = bgEl.getAttribute("data-project-province") || "";
          }
        } else {
          bgEl.classList.remove("opacity-100", "scale-100");
          bgEl.classList.add("opacity-0", "scale-105");
        }
      }

      for (var j = 0; j < cardSlides.length; j++) {
        if (j === activeIdx) {
          cardSlides[j].classList.remove("opacity-0", "scale-105");
          cardSlides[j].classList.add("opacity-90", "scale-100");
        } else {
          cardSlides[j].classList.remove("opacity-90", "scale-100");
          cardSlides[j].classList.add("opacity-0", "scale-105");
        }
      }

      for (var k = 0; k < slideBtns.length; k++) {
        if (k === activeIdx) {
          slideBtns[k].classList.remove("w-2", "bg-slate-300");
          slideBtns[k].classList.add("w-5", "bg-emerald-600");
        } else {
          slideBtns[k].classList.remove("w-5", "bg-emerald-600");
          slideBtns[k].classList.add("w-2", "bg-slate-300");
        }
      }
    }

    if (totalSlides > 0) {
      if (prevBtn) {
        prevBtn.addEventListener("click", function () {
          setHeroSlide(activeIdx - 1);
        });
      }
      if (nextBtn) {
        nextBtn.addEventListener("click", function () {
          setHeroSlide(activeIdx + 1);
        });
      }
      for (var s = 0; s < slideBtns.length; s++) {
        (function (idx) {
          slideBtns[idx].addEventListener("click", function () {
            setHeroSlide(idx);
          });
        })(s);
      }
      window.setInterval(function () {
        if (modal && !modal.classList.contains("hidden")) return;
        setHeroSlide(activeIdx + 1);
      }, 12000);
    }

    function openVideoModal() {
      if (!modal) return;
      modal.classList.remove("hidden");
      modal.classList.add("flex");
      modal.setAttribute("aria-hidden", "false");
      if (modalVideo) {
        modalVideo.play().catch(function () {});
      }
    }

    function closeVideoModal() {
      if (!modal) return;
      if (modalVideo) {
        modalVideo.pause();
      }
      modal.classList.add("hidden");
      modal.classList.remove("flex");
      modal.setAttribute("aria-hidden", "true");
    }

    for (var m = 0; m < openBtns.length; m++) {
      openBtns[m].addEventListener("click", openVideoModal);
    }
    for (var c = 0; c < closeBtns.length; c++) {
      closeBtns[c].addEventListener("click", closeVideoModal);
    }
  }

  function initHomeHeroVariants() {
    /* Home 1: Backdrop Scene Switcher & Inline Video Player */
    var h1Btns = document.querySelectorAll("[data-home1-scene-btn]");
    var h1Imgs = document.querySelectorAll("[data-home1-scene-img]");
    var h1Video = document.querySelector('[data-home1-inline-video="true"]');
    var h1PlayBtn = document.querySelector('[data-home1-play-trigger="true"]');
    var h1CloseBtn = document.querySelector('[data-home1-video-close="true"]');
    var h1MuteBtn = document.querySelector('[data-home1-video-mute="true"]');
    var h1Controls = document.querySelector('[data-home1-video-controls="true"]');
    var h1Atmosphere = document.querySelector('[data-home1-atmosphere="true"]');
    var h1Overlays = document.querySelectorAll('[data-home1-overlay-item="true"]');
    var h1BottomTab = document.querySelector('[data-home1-bottom-tab="true"]');
    var h1BottomFillets = document.querySelectorAll('[data-home1-bottom-fillet="true"]');
    var h1CategoriesGrid = document.querySelector('[data-home1-categories-grid="true"]');
    var h1TitleEl = document.querySelector('[data-home1-scene-title="true"]');
    var h1CtaEl = document.querySelector('[data-home1-scene-cta="true"]');
    var h1LinkEl = document.querySelector('[data-home1-scene-link="true"]');

    var h1ActiveScene = "0";
    var h1IsFullVideo = false;

    if (h1Video) {
      h1Video.playbackRate = 1.0;
    }

    function syncH1VideoUI() {
      var showVid = h1ActiveScene === "0" || h1IsFullVideo;
      if (h1Video) {
        if (showVid) {
          h1Video.classList.remove("opacity-0", "z-0", "pointer-events-none");
          h1Video.classList.add("opacity-100", "z-10");
        } else {
          h1Video.classList.add("opacity-0", "z-0", "pointer-events-none");
          h1Video.classList.remove("opacity-100", "z-10");
        }
      }
      for (var im = 0; im < h1Imgs.length; im++) {
        var isImgAct =
          String(h1Imgs[im].getAttribute("data-home1-scene-img")) === String(h1ActiveScene);
        if (isImgAct) {
          h1Imgs[im].classList.remove("opacity-0", "z-0", "scale-100");
          h1Imgs[im].classList.add("opacity-100", "z-10", "scale-105");
        } else {
          h1Imgs[im].classList.remove("opacity-100", "z-10", "scale-105");
          h1Imgs[im].classList.add("opacity-0", "z-0", "scale-100");
        }
      }
      if (h1Controls) {
        if (h1IsFullVideo) {
          h1Controls.classList.remove("opacity-0", "-translate-y-3", "pointer-events-none");
          h1Controls.classList.add("opacity-100", "translate-y-0");
        } else {
          h1Controls.classList.add("opacity-0", "-translate-y-3", "pointer-events-none");
          h1Controls.classList.remove("opacity-100", "translate-y-0");
        }
      }
      if (h1Atmosphere) {
        if (h1IsFullVideo) {
          h1Atmosphere.classList.remove("opacity-100");
          h1Atmosphere.classList.add("opacity-15");
        } else {
          h1Atmosphere.classList.remove("opacity-15");
          h1Atmosphere.classList.add("opacity-100");
        }
      }
      for (var ov = 0; ov < h1Overlays.length; ov++) {
        if (h1IsFullVideo) {
          h1Overlays[ov].classList.remove("opacity-100");
          h1Overlays[ov].classList.add("opacity-0", "pointer-events-none");
        } else {
          h1Overlays[ov].classList.remove("opacity-0", "pointer-events-none");
          h1Overlays[ov].classList.add("opacity-100");
        }
      }
      if (h1BottomTab) {
        if (h1IsFullVideo) {
          h1BottomTab.classList.remove("translate-y-0");
          h1BottomTab.classList.add("translate-y-13", "sm:translate-y-15");
        } else {
          h1BottomTab.classList.remove("translate-y-13", "sm:translate-y-15");
          h1BottomTab.classList.add("translate-y-0");
        }
      }
      for (var bf = 0; bf < h1BottomFillets.length; bf++) {
        if (h1IsFullVideo) {
          h1BottomFillets[bf].classList.remove("opacity-100");
          h1BottomFillets[bf].classList.add("opacity-0");
        } else {
          h1BottomFillets[bf].classList.remove("opacity-0");
          h1BottomFillets[bf].classList.add("opacity-100");
        }
      }
      if (h1CategoriesGrid) {
        if (h1IsFullVideo) {
          h1CategoriesGrid.classList.remove("translate-y-0", "pb-0");
          h1CategoriesGrid.classList.add("translate-y-13", "pb-13", "sm:translate-y-15", "sm:pb-15");
        } else {
          h1CategoriesGrid.classList.remove("translate-y-13", "pb-13", "sm:translate-y-15", "sm:pb-15");
          h1CategoriesGrid.classList.add("translate-y-0", "pb-0");
        }
      }
    }

    if (h1PlayBtn) {
      h1PlayBtn.addEventListener("click", function () {
        h1IsFullVideo = true;
        syncH1VideoUI();
        if (h1Video) {
          h1Video.playbackRate = 1.0;
          h1Video.muted = false;
          h1Video.currentTime = 0;
          h1Video.play().catch(function () {
            h1Video.muted = true;
            h1Video.play().catch(function () {});
          });
        }
      });
    }

    if (h1CloseBtn) {
      h1CloseBtn.addEventListener("click", function () {
        h1IsFullVideo = false;
        syncH1VideoUI();
        if (h1Video) {
          h1Video.playbackRate = 1.0;
          h1Video.muted = true;
          if (h1ActiveScene === "0") {
            h1Video.play().catch(function () {});
          } else {
            h1Video.pause();
          }
        }
      });
    }

    if (h1MuteBtn && h1Video) {
      h1MuteBtn.addEventListener("click", function () {
        h1Video.muted = !h1Video.muted;
      });
    }

    if (h1Btns.length > 0 && h1Imgs.length > 0) {
      var activateH1 = function (targetIdx, btnEl) {
        h1ActiveScene = String(targetIdx);
        h1IsFullVideo = false;
        for (var i = 0; i < h1Btns.length; i++) {
          var isAct =
            String(h1Btns[i].getAttribute("data-home1-scene-btn")) === String(targetIdx);
          var isDesktopBtn = h1Btns[i].parentElement && h1Btns[i].parentElement.getAttribute("role") === "tablist";
          if (isDesktopBtn) {
            h1Btns[i].className = isAct
              ? "inline-flex items-center gap-1.5 rounded-xl px-2.5 py-1.5 text-[11px] font-extrabold transition-all cursor-pointer bg-emerald-500 text-slate-950 shadow-xs"
              : "inline-flex items-center gap-1.5 rounded-xl px-2.5 py-1.5 text-[11px] font-extrabold transition-all cursor-pointer text-white/85 hover:bg-white/10 hover:text-white";
          } else {
            h1Btns[i].className = isAct
              ? "inline-flex items-center gap-1 rounded-xl border px-2.5 py-1.5 text-[11px] font-extrabold transition-all cursor-pointer border-emerald-600 bg-emerald-600 text-white"
              : "inline-flex items-center gap-1 rounded-xl border px-2.5 py-1.5 text-[11px] font-extrabold transition-all cursor-pointer border-slate-200 bg-slate-50 text-slate-700";
          }
        }
        if (h1ActiveScene === "0") {
          if (h1Video) {
            h1Video.muted = true;
            h1Video.currentTime = 0;
            h1Video.play().catch(function () {});
          }
        } else {
          if (h1Video) {
            h1Video.muted = true;
            h1Video.pause();
          }
        }
        syncH1VideoUI();
        if (btnEl) {
          var sTitle = btnEl.getAttribute("data-scene-title");
          var sCta = btnEl.getAttribute("data-scene-cta");
          var sHref = btnEl.getAttribute("data-scene-href");
          if (h1TitleEl && sTitle) h1TitleEl.textContent = sTitle;
          if (h1CtaEl && sCta) h1CtaEl.textContent = sCta;
          if (h1LinkEl && sHref) {
            var cleanHref = sHref.replace(/^\/+/, "");
            h1LinkEl.setAttribute("href", cleanHref.endsWith(".html") ? cleanHref : cleanHref + ".html");
          }
        }
      };

      for (var s1 = 0; s1 < h1Btns.length; s1++) {
        (function (btn) {
          btn.addEventListener("click", function () {
            activateH1(btn.getAttribute("data-home1-scene-btn"), btn);
          });
        })(h1Btns[s1]);
      }
    }

    /* Home 2: Interactive & Wheel-Step History Timeline (1380 - 1405) */
    var h2HistTrack = document.querySelector('[data-history-scroll-track="true"]');
    var h2HistStage = document.querySelector('[data-history-stage="true"]');
    var h2HistBtns = document.querySelectorAll("[data-home2-history-btn]");
    var h2HistPanels = document.querySelectorAll("[data-home2-history-panel]");
    var h2HistPrevBtns = document.querySelectorAll('[data-home2-history-prev="true"]');
    var h2HistNextBtns = document.querySelectorAll('[data-home2-history-next="true"]');
    var h2HistProgBar = document.querySelector('[data-history-progress-bar="true"]');
    var h2HistStepLabel = document.querySelector('[data-history-step-label="true"]');
    var h2CurrentIdx = 0;
    var h2LastStepTime = 0;
    var faNums = ["۰", "۱", "۲", "۳", "۴", "۵", "۶", "۷", "۸", "۹"];
    var toFaNum = function (n) {
      return String(n).replace(/\d/g, function (d) {
        return faNums[Number(d)] || d;
      });
    };

    if (h2HistBtns.length > 0 && h2HistPanels.length > 0) {
      var totalHistSteps = h2HistBtns.length;
      var activateH2History = function (targetIdx) {
        h2CurrentIdx = Math.max(0, Math.min(totalHistSteps - 1, Number(targetIdx) || 0));
        var activeYearText = "۱۳۸۰";
        for (var i = 0; i < h2HistBtns.length; i++) {
          var btnIdx = Number(h2HistBtns[i].getAttribute("data-home2-history-btn")) || 0;
          var isAct = btnIdx === h2CurrentIdx;
          var isPassed = btnIdx < h2CurrentIdx;
          h2HistBtns[i].setAttribute("aria-selected", isAct ? "true" : "false");
          h2HistBtns[i].className = isAct
            ? "group flex flex-col items-start justify-between rounded-2xl border px-3.5 py-2.5 text-right transition-all cursor-pointer border-emerald-700 bg-emerald-700 text-white shadow-sm"
            : isPassed
            ? "group flex flex-col items-start justify-between rounded-2xl border px-3.5 py-2.5 text-right transition-all cursor-pointer border-emerald-200/90 bg-emerald-50/70 text-emerald-950 hover:bg-emerald-100/70"
            : "group flex flex-col items-start justify-between rounded-2xl border px-3.5 py-2.5 text-right transition-all cursor-pointer border-slate-200/75 bg-[#f6f8f7] text-slate-700 hover:border-emerald-200 hover:bg-white";
          var spans = h2HistBtns[i].querySelectorAll("span");
          if (spans.length >= 1 && isAct) {
            activeYearText = spans[0].textContent || "";
          }
          if (spans.length >= 2) {
            spans[1].className = isAct
              ? "inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-black tabular-nums bg-white/20 text-white"
              : isPassed
              ? "inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-black tabular-nums bg-emerald-600 text-white"
              : "inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-black tabular-nums bg-slate-200/80 text-slate-600";
          }
          if (spans.length >= 3) {
            spans[2].className = isAct
              ? "mt-1 line-clamp-1 text-[10.5px] font-bold text-emerald-100"
              : isPassed
              ? "mt-1 line-clamp-1 text-[10.5px] font-bold text-emerald-800"
              : "mt-1 line-clamp-1 text-[10.5px] font-bold text-slate-500";
          }
        }
        if (h2HistStepLabel) {
          h2HistStepLabel.textContent =
            "گام " + toFaNum(h2CurrentIdx + 1) + " از " + toFaNum(totalHistSteps) + " (سال " + activeYearText + ")";
        }
        if (h2HistProgBar) {
          var pct = Math.round(((h2CurrentIdx + 1) / totalHistSteps) * 100);
          h2HistProgBar.style.width = String(pct) + "%";
        }
        for (var p = 0; p < h2HistPanels.length; p++) {
          var isPanelAct =
            String(h2HistPanels[p].getAttribute("data-home2-history-panel")) === String(h2CurrentIdx);
          if (isPanelAct) {
            h2HistPanels[p].classList.remove("hidden");
            h2HistPanels[p].classList.add("grid");
          } else {
            h2HistPanels[p].classList.add("hidden");
            h2HistPanels[p].classList.remove("grid");
          }
        }
      };

      var targetStageEl = h2HistStage || h2HistTrack;
      if (targetStageEl) {
        window.addEventListener(
          "wheel",
          function (e) {
            if (window.innerWidth < 768) return;
            var rect = targetStageEl.getBoundingClientRect();
            var vh = window.innerHeight || 800;
            var inActiveZone = rect.top <= vh * 0.34 && rect.bottom >= vh * 0.52;
            if (!inActiveZone) return;

            var deltaY = e.deltaY || 0;
            if (Math.abs(deltaY) < 6) return;

            var now = Date.now();
            if (deltaY > 0) {
              if (h2CurrentIdx < totalHistSteps - 1) {
                e.preventDefault();
                if (now - h2LastStepTime >= 380) {
                  h2LastStepTime = now;
                  activateH2History(h2CurrentIdx + 1);
                }
                return;
              }
              if (now - h2LastStepTime < 380) {
                e.preventDefault();
                return;
              }
            } else if (deltaY < 0) {
              if (h2CurrentIdx > 0) {
                e.preventDefault();
                if (now - h2LastStepTime >= 380) {
                  h2LastStepTime = now;
                  activateH2History(h2CurrentIdx - 1);
                }
                return;
              }
              if (now - h2LastStepTime < 380) {
                e.preventDefault();
                return;
              }
            }
          },
          { passive: false }
        );
      }

      for (var hb = 0; hb < h2HistBtns.length; hb++) {
        (function (btn) {
          btn.addEventListener("click", function () {
            h2LastStepTime = Date.now();
            activateH2History(btn.getAttribute("data-home2-history-btn"));
          });
        })(h2HistBtns[hb]);
      }

      for (var pv = 0; pv < h2HistPrevBtns.length; pv++) {
        h2HistPrevBtns[pv].addEventListener("click", function () {
          h2LastStepTime = Date.now();
          var nextIdx = h2CurrentIdx > 0 ? h2CurrentIdx - 1 : totalHistSteps - 1;
          activateH2History(nextIdx);
        });
      }

      for (var nx = 0; nx < h2HistNextBtns.length; nx++) {
        h2HistNextBtns[nx].addEventListener("click", function () {
          h2LastStepTime = Date.now();
          var nextIdx = h2CurrentIdx < totalHistSteps - 1 ? h2CurrentIdx + 1 : 0;
          activateH2History(nextIdx);
        });
      }
    }

    /* Home 2: Portfolio Category Filter Tabs (Consistent Segmented Style & Full 5-Card Panels) */
    var h2PortTabs = document.querySelectorAll("[data-home2-port-tab]");
    var h2PortPanels = document.querySelectorAll("[data-home2-port-panel]");
    if (h2PortTabs.length > 0 && h2PortPanels.length > 0) {
      for (var pt = 0; pt < h2PortTabs.length; pt++) {
        (function (tabBtn) {
          tabBtn.addEventListener("click", function () {
            var cat = tabBtn.getAttribute("data-home2-port-tab") || "all";
            for (var j = 0; j < h2PortTabs.length; j++) {
              var isTabAct = h2PortTabs[j].getAttribute("data-home2-port-tab") === cat;
              h2PortTabs[j].setAttribute("aria-selected", isTabAct ? "true" : "false");
              h2PortTabs[j].className = isTabAct
                ? "inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap bg-emerald-700 text-white shadow-xs"
                : "inline-flex items-center gap-2 rounded-xl px-4 py-2 text-xs font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-600 hover:bg-white/80 hover:text-slate-900";
              var badgeSpans = h2PortTabs[j].querySelectorAll("span");
              if (badgeSpans.length >= 2) {
                badgeSpans[1].className = isTabAct
                  ? "rounded-md px-1.5 py-0.5 text-[10px] font-black tabular-nums bg-white/20 text-white"
                  : "rounded-md px-1.5 py-0.5 text-[10px] font-black tabular-nums bg-slate-200/75 text-slate-600";
              }
            }
            for (var p = 0; p < h2PortPanels.length; p++) {
              var pKey = h2PortPanels[p].getAttribute("data-home2-port-panel");
              if (pKey === cat) {
                h2PortPanels[p].classList.remove("hidden");
                h2PortPanels[p].classList.add("grid");
              } else {
                h2PortPanels[p].classList.add("hidden");
                h2PortPanels[p].classList.remove("grid");
              }
            }
          });
        })(h2PortTabs[pt]);
      }
    }

    /* Home 3: "Get to Know ESE Better" Interactive Feature Rows & Video */
    var h3KnowRows = document.querySelectorAll("[data-home3-know-row]");
    var h3KnowThumb = document.querySelector('[data-home3-know-thumb="true"]');
    if (h3KnowRows.length > 0) {
      var activateH3Know = function (targetIdx) {
        var idxNum = Number(targetIdx) || 0;
        if (h3KnowThumb) {
          h3KnowThumb.style.marginTop = String(idxNum * 18) + "%";
        }
        for (var i = 0; i < h3KnowRows.length; i++) {
          var isAct = String(h3KnowRows[i].getAttribute("data-home3-know-row")) === String(idxNum);
          var iconBox = h3KnowRows[i].querySelector("[data-home3-know-icon]");
          var titleEl = h3KnowRows[i].querySelector("[data-home3-know-title]");
          var descEl = h3KnowRows[i].querySelector("[data-home3-know-desc]");
          if (iconBox) {
            iconBox.className = isAct
              ? "grid h-10 w-10 shrink-0 place-items-center rounded-xl transition-colors bg-emerald-100 text-emerald-700"
              : "grid h-10 w-10 shrink-0 place-items-center rounded-xl transition-colors bg-slate-100 text-slate-600 group-hover:bg-emerald-50 group-hover:text-emerald-700";
          }
          if (titleEl) {
            titleEl.className = isAct
              ? "text-xs font-extrabold transition-colors sm:text-sm text-emerald-800"
              : "text-xs font-extrabold transition-colors sm:text-sm text-slate-700 group-hover:text-slate-950";
          }
          if (descEl) {
            if (isAct) {
              descEl.classList.remove("hidden");
              descEl.classList.add("block");
            } else {
              descEl.classList.add("hidden");
              descEl.classList.remove("block");
            }
          }
        }
      };

      for (var kr = 0; kr < h3KnowRows.length; kr++) {
        (function (rowEl) {
          rowEl.addEventListener("click", function () {
            activateH3Know(rowEl.getAttribute("data-home3-know-row"));
          });
        })(h3KnowRows[kr]);
      }
    }

    var h3VideoToggle = document.querySelector('[data-home3-video-toggle="true"]');
    var h3AboutVideo = document.querySelector('[data-home3-about-video="true"]');
    if (h3VideoToggle && h3AboutVideo) {
      h3VideoToggle.addEventListener("click", function () {
        if (h3AboutVideo.paused) {
          h3AboutVideo.muted = false;
          h3AboutVideo.play().catch(function () {
            h3AboutVideo.muted = true;
            h3AboutVideo.play().catch(function () {});
          });
        } else {
          h3AboutVideo.pause();
        }
      });
    }
  }

  function initAll() {
    initNavbar();
    initMegaMenuAndSearch();
    initHeroVideo();
    initHomeHeroVariants();
    initScrollTop();
    initCounters();
    initHomeServices();
    initGalleries();
    initProjectsArchive();
    initProductsArchive();
    initProductDetailAndFaqs();
    initPurchaseJourney();
    initBlogArchive();
    initRecruitmentTabs();
    initShareButtons();
    initContactForm();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initAll);
  } else {
    initAll();
  }
})();
