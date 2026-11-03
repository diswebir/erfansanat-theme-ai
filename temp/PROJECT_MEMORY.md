# PROJECT MEMORY — حافظه زنده پروژه

## وضعیت فعلی
`PHASE 6 PASSED — READY FOR PHASE 7`

## محیط هارنس (Environment)
| ابزار | وضعیت |
|---|---|
| node v22.22.3 / npm 10.9.8 | ✅ OK |
| git 2.39.5 / curl 7.88.1 | ✅ OK |
| php CLI | ❌ MISSING — apt مسدود است (deb.debian.org خارج از allowlist) → برنامه: `@php-wasm/cli@3.1.62` (npm) برای `php -l` و تست‌های PHP |
| wp-cli / phpunit / xmllint | ❌ MISSING — جایگزین: تست‌های PHP مستقل + php-wasm |

## منابع استاتیک
- فایل آپلود: `/tmp/arena-coding-uploads/erfansanat-static-mirror.zip.png` (15.6MB — در واقع ZIP با magic bytes PK)
- استخراج‌شده در: `/home/user/static-mirror/static-mirror/` (**خارج از git repo** — ۱۲۸ HTML + assets)
- ۱ فایل CSS (190KB Tailwind)، ۱ فایل JS (112KB)، ۳ فونت woff2، ۲۱ تصویر/ویدیو

## تصمیمات فنی ثبت‌شده
1. میرور استاتیک خارج از مخزن git نگهداری می‌شود تا repo تمیز بماند.
2. تست‌های PHP با `php-wasm` (npm) اجرا می‌شوند؛ اسکریپت‌های فازها با bash + php-wasm نوشته می‌شوند.
3. کلاس‌های Tailwind کامپایل‌شده در `styles.css` دست‌نخورده حفظ می‌شوند (هیچ بازطراحی CSS).

## پاسخ کاربر به سوالات Stage 0: «خودت هر طور میدونی بهتره پیش ببر»
تصمیمات ثبت‌شده:
1. `product/*.html` و `products/*.html` هر دو → `single-product.php` (اسلاگ live `/product/...`)؛ فایل‌های `products/category-*` → `taxonomy-product_cat.php`؛ مسیرهای تکراری `products/*` legacy/redirect.
2. `shop.html` → `archive-product.php` (/shop/)؛ `products.html` (کاتالوگ) نیز → `archive-product.php` (همان آرشیو با view کاتالوگ).
3. `services/*.html` (۵ فایل) → `page-templates/service-*.php` زیرمجموعه صفحه smartelement (بدون CPT جداگانه).
4. لندینگ `arya` → `page-templates/arya.php` که محتوا با SectionManager قابل ویرایش است.
5. Assets در ریشه قالب: `assets/` → `get_template_directory_uri() . '/assets/'`.

## مراحل
- Stage 0: ✅ COMPLETED (commit 92747ca)
- Stage 1: ✅ COMPLETED — MASTER_PLAN + QA_REFLECTION_LOG + ۸ پوشه فاز (tasks/ + tests/) — ۵۹ فایل
- Stage 2+: ✅ MILESTONE — کاربر «هر طور میدونی بهتره پیش ببر»؛ autoplay کامل فعال شد
- Phase 1 Task 1.1: ✅ PASSED — اسکلت قالب + OOP core + تست 18/18
- Phase 1 Task 1.2: ✅ PASSED — inc/DeBloat.php (Gutenberg/emoji/head cleanup) + تست 13/13
- Phase 1 Task 1.3: ✅ PASSED — assets/ (27 فایل, 13MB) کپی شد؛ inc/AssetManager.php با filemtime versioning + تست 16/16؛ run-phase-01-tests.sh: 3/3 ALL PASS

- Phase 2 Task 2.1: ✅ PASSED — inc/Admin/ThemeOptions.php (menu, nonce, capability, AJAX save, es_option() با کش) + admin.css/js + تست 13/13 + lint clean
- Phase 2 Task 2.2: ✅ PASSED — defaults() منطبق بر استاتیک (email/logo/color) + ۴ تب کامل + es_option با fallback + تست 18/18
- Phase 2 Task 2.3: ✅ PASSED — inc/Admin/SectionManager.php (metabox, drag&drop AJAX reorder, _erfansanat_sections meta, es_sections() helper) + admin-sections.js + تست 14/14
- Phase 3 Task 3.1: ✅ PASSED — Registrar: CPT projects + tax project_type/city + product_cat slug/with_front + admin columns/filters؛ تست 23/23
- Phase 3 Task 3.2: ✅ PASSED — ProductMetaboxes با تمام کلیدهای legacy + repeater فرمت item-N + fallback گالری؛ تست 32/32
- Phase 3 Task 3.3: ✅ PASSED — LegacyMetaBoxes (project images incl. foure-image, finish-pro-time, mortabet_mahsole_2, product_cat term faq/thumbnail, hreflang meta + sortable column, number2-viewc counter با fallback) + تست 22/22
- Phase 4 Task 4.1: ✅ PASSED — header.php (125KB) و footer.php با 100% حفظ DOM/data-* + wp_head/body_class/wp_body_open/wp_footer + hreflang tags + تست 17/17
- Phase 4 Task 4.2: ✅ PASSED — MegaMenuWalker + MobileMenuWalker (4 location) + wp_nav_menu در header/footer؛ تست‌ها green
- Phase 5 Task 5.1: ✅ PASSED — 15 سکشن صفحه اصلی به template-parts/home/section-*.php + front-page.php با es_sections؛ تست 9/9
- Phase 5 Task 5.2: ✅ PASSED — inc/Queries.php + cards (product/project/post) + 3 سکشن list به loop داینامیک؛ تست 19/19
- Phase 6: ✅ PASSED — single-product.php (تمام metaهای legacy + true_sale + pa_* + repeaterها + faq + related) + archive-product + taxonomy-product_cat/tag + woocommerce cart/checkout/my-account؛ 3 suite ALL PASS

## Smoke test (runtime execution) — 2026-11-03 (post-reset rebuild)
- sandbox reset wiped uncommitted work → rebuilt env (php-wasm, static mirror) and re-created phase-8 files.
- **CRITICAL BUG FOUND & FIXED by smoke test:** es_option()/es_sections()/es_hreflang_tags() were defined inside Admin/Seo namespaces → undefined on front-end. Moved to global inc/helpers.php loaded unconditionally.
- test-smoke-render.php (wp-stubs.php mini-WP): boots theme, renders front-page/single-product/single-projects/archives/404 + AJAX JSON → ALL PASS.
- E2E: ALL-PASS. All lint clean.

## Polish — 2026-11-03
- ریست شدن محیط بعد از push → از remote بازیابی شد (33d6d35).
- template-parts/content.php + content-none.php اضافه شد (که index.php به آن‌ها ارجاع می‌دهد).
- woocommerce cart/checkout با هوک‌های واقعی WC (wc_get_template) اصلاح شد.
- Smoke test توسعه یافت: search/page/cart/checkout/taxonomy-city/archive-projects/single/index → ALL PASS. E2E: ALL-PASS.
