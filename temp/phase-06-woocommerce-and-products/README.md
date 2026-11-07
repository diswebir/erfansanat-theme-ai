# Phase 6 — قالب‌های ووکامرس، محصول تکی و آرشیو فروشگاه

**هدف:** Override قالب‌های WooCommerce با UI مرجع، اتصال متاهای legacy محصول، حفظ hookها و endpointهای پویا، و سازگاری قالب کلاسیک با افزونه‌های فروشگاهی.

**وضعیت:** پیاده‌سازی و گیت منبعی فاز تکمیل شده است. همهٔ ۱۸ فایل تست PHP-WASM سبز هستند؛ اعتبارسنجی روی WordPress/WooCommerce زنده و مرورگر اجرا نشده و باید پیش از انتشار روی سایت staging انجام شود.

**۱۸ تسک اتمیک:** `6.1` تا `6.18`. جزئیات و وضعیت هر تسک در `tasks/` ثبت شده است.

## محدودهٔ تکمیل‌شده

- قالب محصول تکی پویا، گالری سازگار با قرارداد `data-gallery-*`، مشخصات `pa_*`، repeaterهای legacy، FAQ، شناسه‌های رسمی، فایل کاتالوگ، پروژه‌های مرتبط و دیدگاه‌های استاندارد.
- آرشیو فروشگاه و taxonomyها با hero و grid مشترک، داده‌های محصول روی wrapper کارت برای سازگاری با `main.js`، صفحه‌بندی WooCommerce سمت سرور و FAQ دسته از term meta.
- قالب‌های classic cart، checkout و account با hookهای اصلی پرداخت، جمع‌های واقعی، endpointها و فرم‌های native WooCommerce.
- fixture seeder مستقل برای محصول simple، variable و inquiry با SKUهای deterministic و cleanup محدود به همان fixtureها.

## اعتبارسنجی انجام‌شده

```bash
bash temp/phase-06-woocommerce-and-products/tests/run-phase-06-tests.sh
node tools/harness/assert-phase-complete.mjs temp/phase-06-woocommerce-and-products/tests
npm run phase5:js
```

نتایج اجراشده: **۶۰ assertion، ۱۸ فایل تست، ۰ شکست، ۰ pending**؛ بررسی نحو با `token_get_all(TOKEN_PARSE)` در PHP-WASM روی **۱۶۰ فایل، ۰ خطا**؛ regression mock صفحهٔ اصلی **۷/۷**. فایل‌های محافظت‌شده `theme/assets/js/main.js` و `theme/assets/css/styles.css` تغییر نکرده‌اند.

برای افزودن وابستگی‌های harness در محیط تازه ابتدا `npm ci --no-audit --no-fund` را اجرا کنید. `npm test` بدون آرگومان در این مخزن یک تست‌رانر عمومی است و خودش تستی را انتخاب نمی‌کند.

## بررسی staging پیش از انتشار

1. این فایل را در WordPress آزمایشی دارای WooCommerce فعال کنید و `tools/harness/seed-products.php` را پس از bootstrap سایت include کرده، fixtureها را بسازید.
2. نمایش simple/variable/inquiry، تغییر variation، افزودن به سبد، کوپن، shipping/tax و checkout را با درگاه mock دستی بررسی کنید.
3. آرشیو shop/category/tag/brand و صفحه‌بندی را در مرورگر و اندازه‌های موبایل/دسکتاپ بررسی کنید.
4. پس از QA، `es_harness_cleanup_phase6_products()` را اجرا کنید.

> گیت PHP-WASM این فاز source-level است؛ seed و چرخهٔ واقعی سبد/درگاه در این محیط اجرا نشده‌اند. اجرای staging و تأیید WooCommerce Status (نسخهٔ baseline قالب‌ها: 11.2.2) عمداً به‌عنوان کار QA انتشار باقی می‌ماند.

## اجرای تست‌های فاز

```bash
bash temp/phase-06-woocommerce-and-products/tests/run-phase-06-tests.sh
```
