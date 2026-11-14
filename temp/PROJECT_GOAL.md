# PROJECT_GOAL — اهداف کلان و خط قرمزهای پروژه

> مرجع ثابت پروژه. منبع: `agent.md` (منشور کارفرما) — هر تغییر در این فایل فقط با تایید کاربر.

## ماموریت
تبدیل **مو‌به‌موی** قالب استاتیک آماده (`static-mirror/` — میرور کامل سایت erfansanat.com) به یک **قالب وردپرس کاملاً اختصاصی، فوق‌سریع، بدون صفحه‌ساز (Zero-Bloat / Non-Block)** برای سایت زنده و فعال «عرفان صنعت اصفهان» (`https://erfansanat.com`).

## خط قرمزها (Red Lines)
1. **ممنوعیت طراحی مجدد فرانت‌اند:** هیچ HTML/CSS جدیدی از صفر طراحی نمی‌شود. فقط برش فایل‌های HTML موجود و اتصال به وردپرس با حفظ **۱۰۰٪ کلاس‌های CSS، ساختار DOM، آیدی‌ها و اتریبیوت‌های `data-*`**.
2. **جایگزینی کامل Elementor + JetEngine + JetSearch + JetSmartFilters + JetTabs:** داشبورد اختصاصی ادمین (Theme Options + Native Metaboxes + Section Manager) دقیقاً به کلیدهای دیتابیس فعلی (با همان املای تاریخی مثل `foure-image`, `galerry_produ`, `english_titl`, `vaule_of_variable`, `usage_countr`) متصل می‌شود؛ **بدون از دست رفتن حتی یک بایت داده**.
3. **حفظ ۱۰۰٪ سئو و Permalinkها:** ساختار URL دقیقاً مطابق بلوپرینت سایت زنده (`product_cat` نه `product-category`، `projects`، `project_type`، `city`، `/product/` و...).
4. **ممنوعیت کد ناقص (ZERO PLACEHOLDERS):** بدون `// TODO` یا حذف مارک‌آپ برای کوتاه‌شدن کد.
5. **حرکت فاز‌به‌فاز و تسک‌به‌تسک (Atomic):** هرگز کل فاز/پروژه یکجا کدنویسی نمی‌شود.
6. **دروازه‌های تایید کاربر (Human-in-the-loop):** توقف در پایان Stage 0 و Stage 2 و پس از هر تسک/فاز طبق دستور کاربر.
7. **چرخه ۶ گامی هر تسک:** READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST & SELF-CORRECT → UPDATE MEMORY + Git Commit.

## معیارهای پرفورمنس و کیفیت
- صفر شدن CSS/JS گوتنبرگ (`wp-block-library`, `global-styles`)، ایموجی‌ها و تگ‌های اضافی `wp_head` در خروجی.
- Enqueue شرطی Assetها per-template با نسخه‌گذاری `filemtime`.
- پاسخ ایجکس فیلتر/جستجو < ۲۰۰ میلی‌ثانیه؛ امنیت کامل SQLi/XSS (Nonce + Sanitize + Escape).
- `WP_DEBUG = true` با صفر Warning/Notice.
- Escaping تمام خروجی‌ها؛ سازگاری با SEOPress (+ متاهای قدیمی `rank_math_*`)، LiteSpeed Cache، WP-Parsidate، voorodak، fl-panel، hreflangpch.
- سازگاری با WooCommerce (IRT، RTL)، Variation Swatches، WPC Product Quantity.

## محیط هدف
WordPress `7.1+` · PHP `8.4+` · WooCommerce `10.9+` · RTL (`fa_IR`) · واحد پول `IRT`.

## سیستم حافظه
۶ فایل مرجع در `temp/` + پوشه‌های `temp/phase-XX-name/{tasks,tests}/` — قبل از هر تسک خواندن و بعد از هر تسک به‌روزرسانی الزامی است.
