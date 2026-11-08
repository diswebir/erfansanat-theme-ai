# PROJECT GOAL — تبدیل قالب استاتیک «عرفان صنعت اصفهان» به قالب وردپرس اختصاصی

## 🎯 هدف کلان
تبدیل مو‌به‌موی میرور استاتیک HTML/CSS/JS (۱۲۸ صفحه) به یک **قالب وردپرس کاملاً اختصاصی، فوق‌سریع و بدون صفحه‌ساز (Zero-Bloat / Non-Block Theme)** برای سایت زنده `https://erfansanat.com`.

## 🚫 قوانین خط قرمز (Red Lines)
1. **ممنوعیت طراحی مجدد فرانت‌اند:** هیچ HTML/CSS از صفر نوشته نمی‌شود؛ فایل‌های استاتیک اسکن، برش و به templateهای وردپرس تبدیل می‌شوند.
2. **حفظ ۱۰۰٪ وابستگی‌ها:** کلاس‌های CSS، ساختار DOM، آیدی‌ها و اتریبیوت‌های `data-*` باید دقیقاً حفظ شوند.
3. **جایگزینی کامل Elementor/JetEngine/JetSearch/JetSmartFilters/JetTabs:** با داشبورد ادمین اختصاصی (Theme Options + Native Metaboxes) که به کلیدهای دیتابیس live متصل است.
4. **ZERO PLACEHOLDERS:** استفاده از `// TODO` یا حذف مارک‌آپ برای کوتاه‌سازی کد = خطای بحرانی.
5. **NO ONE-SHOT EXECUTION:** کار به‌صورت فازبه‌فاز و تسک‌به‌تسک (Atomic) با چرخه CoT + TDD.
6. **Human-in-the-Loop Gates:** توقف و دریافت تایید کاربر پس از Stage 0 و Stage 2 و پس از هر تسک/فاز.

## ⚡ معیارهای پرفورمنس
- حذف کامل استایل/اسکریپت‌های گوتنبرگ (wp-block-library, global-styles, emoji)
- Enqueue شرطی per-page با نسخه‌گذاری filemtime
- فیلتر/جستجوی ایجکس < 200ms
- صفر Warning در `WP_DEBUG = true`

## 🧰 نیازمندی‌های کارفرما (از agent.md)
- محیط هدف: WP 7.1+، PHP 8.4+، WooCommerce 10.9+ (IRT، RTL)
- افزونه‌های باقی‌مانده: WooCommerce، Variation Swatches، WPC Product Quantity، SEOPress (+PRO)، LiteSpeed Cache، WP-Parsidate، voorodak، fl-panel، hreflangpch
- حفظ ۱۰۰٪ URLهای live (سئو): اسلاگ‌های `projects`, `/product/...`, `product_cat/...`, `product-tag`, `project_type`, `city`
- اتصال به کلیدهای متای live با املای دقیق (مثلاً `foure-image`, `galerry_produ`, `english_titl`, `vaule_of_variable`, `usage_countr`, `number2-viewc`)
