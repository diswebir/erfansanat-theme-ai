# QA REFLECTION LOG — لاگ تفکر، تست و خوداصلاحی

## Stage 0 — 2026-11-03
- [READ MEMORY] n/a (bootstrap)
- [SCAN] فایل آپلود `erfansanat-static-mirror.zip.png` در واقع ZIP است (PK magic) → استخراج در `/home/user/static-mirror/static-mirror/` (۱۲۸ HTML).
- [ENV] php/wpcli/xmllint از apt قابل نصب نیست (deb.debian.org مسدود) → `@php-wasm/cli@3.1.62` نصب شد؛ `php -v` = PHP 8.5.12 (WASM) OK.
- [DECISION] جواب سوالات Stage 0 توسط کاربر به «هر طور میدونی بهتره» واگذار شد؛ تصمیمات در PROJECT_MEMORY.md ثبت شد.
- [MEMORY] ۴ فایل حافظه ساخته و commit شد (92747ca).

## Stage 1 — 2026-11-03
- [PLAN] MASTER_PLAN + ۸ پوشه فاز با tasks/ و tests/ ساخته شد.

## Phase 1 / Task 1.1 — 2026-11-03
- [THINK] Theme root = repo root. Autoloader PSR-4-ish → inc/. Theme::instance() boot با after_setup_theme.
- [TEST] test-task-1-1.php: ۱۸ چک — ۲ FAIL اولیه (style.css header بدون پیشوند ` * `) → self-correction: پیشوند استاندارد اضافه شد.
- [RUN] php -l روی تمام فایل‌های PHP: clean. تست: ALL PASS (18/18).

## Phase 1 / Tasks 1.2, 1.3 — 2026-11-03
- Task 1.2: DeBloat — dequeue wp-block-library/global-styles, emoji off, head cleanup. تست 13/13 PASS. lint clean.
- Task 1.3: assets کپی شد (css/js/fonts/images → 13MB, 27 فایل؛ ساختار نسبی حفظ شد تا url(../fonts) کار کند). AssetManager با filemtime. تست 16/16 PASS.
- run-phase-01-tests.sh: 3/3 suites ALL PASS → **PHASE 1 [PASSED]**

## Phase 2 / Task 2.1 — 2026-11-03
- ThemeOptions dashboard ساخته شد؛ self-correction: یک پرانتز اضافی در save() → lint fail → اصلاح شد؛ تست 13/13 PASS.

## Phase 2 / Task 2.2 — 2026-11-03
- defaults() با مقادیر استاتیک (info@erfansanat.com, ese-logo.svg, emerald #059669) + ۴ تب + wp_enqueue_media؛ تست 18/18 PASS.

## Phase 2 / Task 2.3 — 2026-11-03
- SectionManager: متاباکس سکشن‌ها با nonce/capability، AJAX reorder، meta key `_erfansanat_sections`، helper `es_sections()` (مرتب‌سازی + فیلتر enabled). تست 14/14 PASS. run-phase-02: 3/3 ALL PASS → **PHASE 2 [PASSED]**

## Phase 3 / Task 3.1 — 2026-11-03
- Registrar ساخته شد؛ self-correction: with_front=true برای product_cat نیاز به فیلتر woocommerce_taxonomy_args_product_cat داشت + هوک ستون‌ها manage_edit-projects_columns؛ تست 23/23 PASS.

## Phase 3 / Task 3.2 — 2026-11-03
- ProductMetaboxes: کلیدهای legacy با املای دقیق + quick edit + repeater + fallback گالری. تست 32/32 PASS.

## Phase 3 CLOSED — 2026-11-03
- run-phase-03: 3/3 ALL PASS → **PHASE 3 [PASSED]** (CPT/tax/rewrite + product meta + legacy meta/hreflang/views)

## Phase 4 / Task 4.1 — 2026-11-03
- header.php/footer.php از استاتیک slice شد ( incl. body-level overlays: mobile menu, search modal, cart modal)؛ self-correction: تست اولیه data-* را با کل صفحه مقایسه می‌کرد → محدود به shell شد. تست 17/17 PASS.

## Phase 4 CLOSED — 2026-11-03
- Walkerها ساخته شد و nav استاتیک با wp_nav_menu جایگزین شد؛ self-correction: تست 4.1 wirtschaftliche apósě hooked data-* را در walker را در walker run-phase-04: 2/2 ALL PASS → **PHASE 4 [PASSED]**

## Phase 5 CLOSED — 2026-11-03
- run-phase-05: 2/2 ALL PASS → **PHASE 5 [PASSED]** (homepage sections + dynamic queries + cards)

## Phase 6 CLOSED — 2026-11-03
- run-phase-06: 3/3 ALL PASS → **PHASE 6 [PASSED]** (single-product + archives + cart/checkout/account)

## Smoke test — 2026-11-03
- Self-correction caught a REAL front-end fatal: helpers were namespaced → moved to global inc/helpers.php.
- Stub fixes: get_template_part .php handling, get_post returns WP_Post, Walker_Nav_Menu stub, array_key_exists for supports.
- RESULT: smoke ALL PASS, E2E ALL-PASS.

## Polish — 2026-11-03
- Added content.php/content-none.php; fixed WC cart/checkout wrappers to real hooks (wc_get_template); extended smoke coverage (9 templates). Smoke ALL PASS, E2E ALL-PASS.

## DOM fidelity test — 2026-11-03
- test-dom-fidelity.php: rendered vs static comparison → 15/15 sections, 50/50 data-* hooks, all markers. Found & fixed: SectionManager registry had only 6 of 15 home sections. ALL PASS.

## WP API surface — 2026-11-03
- Cloned WordPress/WordPress (sparse). Built 4690-function list from wp-includes + wp-admin/includes. Scanned 718 function calls in theme: all resolve to WP core / builtins / own helpers / wc_* prefixes. ALL PASS.

## REAL WP integration — 2026-11-03
- WordPress 7.2 + SQLite on php-wasm: install OK, theme activates (0 errors), CPT/tax registered, legacy meta roundtrip OK, front-page/single-product/single-projects render through real WP. 19/19 ALL PASS. Real bugs fixed: get_template_part .php suffix in front-page.php; woocommerce call guarded by function_exists.

## FULL DEBUG — 2026-11-03
- Rendered 12 templates on real WP 7.2/SQLite with error handler: 0 theme-file diagnostics. Fixed: missing comments.php (WP deprecation), null $term in taxonomy templates, number_format(null), unguarded woocommerce_* in archive-product. E2E ALL-PASS.

## SECURITY+ASSETS audit — 2026-11-03
- 8 security/bloat checks on real WP 7.2: nonce-guarded superglobals, no eval, no external CDNs, all scripts defer/async, no jQuery/emoji on front-end, escaped output. ALL PASS, wired into E2E.

## ADMIN+A11Y+PERF — 2026-11-03
- Real-WP admin dashboard test (SectionManager save + nonce rejection + legacy meta roundtrip), a11y (skip-link added, aria-label on search input, all 65 imgs have alt, rtl/lang via fa_IR), performance budget (402KB code assets vs 296KB static mirror, 271KB rendered page). ALL PASS, in E2E.
