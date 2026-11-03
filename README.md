# قالب اختصاصی «عرفان صنعت اصفهان» (erfansanat-theme-ai)

قالب وردپرس **بدون صفحه‌ساز (Zero-Bloat)** که مو‌به‌موی از میرور استاتیک ۱۲۸ صفحه‌ای سایت `erfansanat.com` ساخته شده است. تمام کلاس‌های CSS، ساختار DOM و هوک‌های `data-*` حفظ شده‌اند.

## نصب
1. پوشه را در `wp-content/themes/erfansanat-theme-ai` کپی کنید.
2. در پیشخوان → ظاهر → پوسته‌ها فعال کنید.
3.اونه‌های `Elementor`، `JetEngine`، `JetSearch`، `JetSmartFilters`، `JetTabs` را **غیرفعال** کنید — تمام کارهای آن‌ها (CPT پروژه‌ها، متاباکس‌ها، فیلتر و جستجوی ایجکس، فرم‌ها) توسط قالب Native انجام می‌شود.
4. منوها را در ظاهر → منوها به موقعیت‌های `main-mega-menu`، `mobile-menu`، `tolidat-sherkat`، `landing` اختصاص دهید.
5. تنظیمات هدر/فوتر/برند را از منوی **عرفان صنعت** در پیشخوان مدیریت کنید.

## ساختار
- `inc/` — هسته OOP با namespace `ErfanSanat\Theme` (Autoloader PSR-4)
- `inc/Admin/` — داشبورد اختصاصی (ThemeOptions، SectionManager، ProductMetaboxes، LegacyMetaBoxes)
- `inc/Data/Registrar.php` — CPT و تکسونومی‌ها با rewriteهای دقیق (حفظ سئو)
- `inc/Ajax/` — فیلتر/جستجوی زنده و سیستم فرم‌ها
- `template-parts/home/` — ۱۵ سکشن صفحه اصلی | `template-parts/cards/` — کارت‌های استاندارد
- `temp/` — مستندات پروژه، نقشه راه ۸ فاز و سوئیت‌های تست

## تست
```bash
# E2E کامل (نیاز به @php-wasm/cli)
bash temp/phase-08-ajax-filters-and-audit/tests/run-all-phases-e2e.sh
# اسموک‌تست اجرای واقعی قالب با stubهای WP
php-wasm-cli temp/phase-08-ajax-filters-and-audit/tests/test-smoke-render.php
```

## کلیدهای متای legacy (سازگار با دیتابیس فعلی)
`galerry_produ`, `foure-image`, `english_titl`, `vaule_of_variable`, `usage_countr`, `number2-viewc`, `hrefmeta_*` و... با همان املای دیتابیس خوانده/نوشته می‌شوند.
