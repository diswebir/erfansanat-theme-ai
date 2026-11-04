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

## DOM fidelity + service template — 2026-11-03
- test-dom-fidelity: رندر front-page با ۱۵ سکشن (parity با استاتیک) + ۵۰ هوک data-* حفظ‌شده → ALL PASS.
- رفع باگ: SectionManager registry فقط ۶ سکشن داشت → به ۱۵ سکشن گسترش یافت.
- page-templates/service-detail.php برای ۵ صفحه dtail خدمات ساخته شد.
- E2E: ALL-PASS | Smoke: ALL PASS

## WP API surface test — 2026-11-03
- WordPress core از GitHub clone شد (4690 تابع core). تست: ۷۱۸ call تابع در قالب → همه یا تابع واقعی WP یا builtin یا helper خود قالب → ALL PASS.
- E2E با smoke + api-surface: ALL-PASS

## REAL WordPress integration — 2026-11-03
- WP 7.2-beta3 (clone از GitHub) + SQLite dropin روی php-wasm بوت شد؛ thme فعال، 19/19 ALL PASS: front-page (279KB, 15 سکشن)، single-product (metaهای legacy)، single-projects.
- رفع باگ‌های واقعی: get_template_part با .php در front-page.php؛ guard fonksiyon_exists برای woocommerce_template_single_add_to_cart.

## FULL DEBUG pass — 2026-11-03
- test-debug-full: رندر ۱۲ template روی WP واقعی با WP_DEBUG → theme-file diagnostics: **0** (293 noise همگی از core/harness).
- باگ‌های رفع‌شده: comments.php اضافه شد, guard null $term در taxonomyها, number_format(null), woocommerce_* بدون guard در archive-product.
- E2E incl. debug suite: ALL-PASS

## SECURITY+ASSETS audit — 2026-11-03
- test-security-assets: superglobals همه nonce/sanitize (Registrar فقط !empty read-only), صفر eval/exec، صفر CDN خارجی (فونت/آیکن لوکال)، JS تمیز، همه echo ها escaped، scriptها همگی defer/async، jQuery و wp-emoji در front-end نیستند. نتیجه: ALL PASS.

## ADMIN+A11Y+PERF audit — 2026-11-03
- test-admin-a11y-perf: admin save با nonce/cap → meta legacy `_erfansanat_sections` roundtrip OK، nonce坏 reject می‌شود؛ rtl/lang با locale fa_IR OK؛ skip-link و aria-label search اضافه شد؛ ۶۵ img همگی alt دارند؛ code assets ۴۰۲KB (css ۱۹۲ + js ۱۲۴ + fonts ۱۰۸) vs استاتیک ۲۹۶KB؛ front-page ۲۷۱KB. ALL PASS.

## STATIC LINKS rewrite — 2026-11-03
- ۸۷ لینک ثابت `.html` در کل قالب با es_static_url() به URLهای واقعی WP (archive/page/term/permalink) تبدیل شد؛ helper با fallback امن harden شد (get_post_type_archive_link false بدون ووکامرس). JSıklı hero هم دیگر .html append نمی‌کند. تست: ۱۱۲ href رندر شده، صفر .html باقی‌مانده. E2E ALL-PASS.

## DEEP FIDELITY + full page port — 2026-11-03
- تست fidelity عمیق (set-based signatures) روی ۶ صفحه: shop/about/contact/product/project = **۱۰۰٪**، index = ۹۵.۷٪ (بخش‌های dynamic).
- port کامل ۱۲ صفحه stub (page-*, archive, home, cart, checkout) + ۱۵ سکشن home + header overlay + single templates از میرر؛ injekt dynamic loops (products/projects/blog) + legacy meta hooks (faq, galerry_produ, english_titl, legal ids, pa_* attributes, finish-pro-time, project gallery/terms).
- E2E: ALL-PASS با صفر FAIL.

## AJAX FUNCTIONAL — 2026-11-03
- test-ajax-functional: همه endpointها روی WP واقعی (subprocess per call): search/filter products/projects با nonce معتبر success + payload sane، nonce坏 reject، ۴ فرم (contact/inquiry/recruitment/feedback) ثبت و در CPT es_form_entry ذخیره می‌شوند. ALL PASS. E2E 0 FAIL.

## SEO OUTPUT — 2026-11-03
- test-seo-output: روی WP واقعی title/robots/OG/hreflang(fa,en,ar,x-default)/canonical/og:url/viewport/charset + overrideهای hrefmeta_en/ar روی single + theme supports — همه PASS. <title> صریح به header اضافه شد (WP 7.2). E2E 0 FAIL.

## MIGRATOR — 2026-11-03
- inc/Migrator.php: import یک Mercéd `_elementor_data` → `_erfansanat_sections` (idempotent) + backfill تمام meta keyهای legacy روی product/projects/post + صفحه ابزار ادمین (Tools → import) با nonce. تست روی WP واقعی: ALL PASS. E2E 0 FAIL.

## I18N + RSS/SITEMAP — 2026-11-03
- test-i18n-rss: locale=fa_IR، بدون هیچ dir=ltr هاردکد (بجز mailto/tel)، textdomain erfansanat، RSS feed با item/link/title، WP_Sitemaps index با submap.posts — 11/11 ALL PASS. E2E 0 FAIL.

## REMAINING PAGES + COMMENTS/REST — 2026-11-03
- test-fidelity-remaining: fidelity صفحات باقی‌مانده میرر — faq/services/recruitment/refund/licenses/cart/checkout/blog همه ۱۰۰٪، blog-single ۹۳.۷٪، search ۹۹.۵٪، 404 structural PASS. test-comments-rest: comment insert/approve/list + template render + REST (posts/types/projects/product routes, es_form_entry مخفی) — ۱۴/۱۴ ALL PASS. E2E 0 FAIL.

## FULL 128-PAGE MIRROR COVERAGE — 2026-11-04
- test-fidelity-all: همه ۱۲۸ صفحه میرر fidelity-tested. Gap پیداشده و fix شد: taxonomy-city/product_cat/project_typecium now full clones of archive templates (99.4-99.6% sigs) + empty-state blocks + legacy term-meta hooks (thumbnail/faq/count). E2E 0 FAIL.
