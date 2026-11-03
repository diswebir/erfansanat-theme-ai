# PROJECT GOAL — قالب اختصاصی وردپرس «عرفان صنعت اصفهان» (erfansanat-theme-ai)

> این فایل مرجع ثابت اهداف کلان، قوانین خط‌قرمز و معیارهای پذیرش پروژه است. در طول پروژه تغییر نمی‌کند مگر با دستور صریح کارفرما.

## ۱. ماموریت کلان
تبدیل مو‌به‌موی **قالب استاتیک آماده** (میرور ۱۲۸ صفحه‌ای HTML/CSS/JS واقع در `uploads/static-mirror/`) به یک **قالب وردپرسی کاملاً اختصاصی، فوق‌سریع و بدون صفحه‌ساز (Zero-Bloat / Non-Block Theme)** برای وب‌سایت زنده `https://erfansanat.com`.

## ۲. قوانین خط‌قرمز (RED LINES)
1. **STRICT STATIC-TO-DYNAMIC:** ممنوعیت طراحی مجدد فرانت‌اند. فقط برش HTML موجود به `template-parts` و اتصال به حلقه‌های داینامیک. حفظ **۱۰۰٪ کلاس‌های CSS، ساختار DOM، آیدی‌ها و اتریبیوت‌های `data-*`**.
2. **جایگزینی کامل Elementor / JetEngine:** تمام قابلیت‌های Elementor Pro، JetEngine، JetSearch، JetSmartFilters، JetTabs باید با **داشبورد مدیریتی اختصاصی در ادمین** (Theme Options + Native Metaboxes + Section Manager) جایگزین شود و مستقیماً به کلیدهای دیتابیس فعلی متصل شود. **صفر بایت دیتا نباید از دست برود.**
3. **NO ONE-SHOT:** پیشرفت فقط فاز به فاز و تسک به تسک (Atomic) با چرخه CoT + TDD (۶ گام الزامی هر تسک).
4. **HUMAN-IN-THE-LOOP:** توقف در پایان Stage 0/Stage 2 و پس از هر تسک/فاز برای تایید کارفرما.
5. **ZERO PLACEHOLDERS:** ممنوعیت `// TODO`، `<!-- rest of html -->` و حذف بخش‌هایی از مارک‌آپ.
6. **سازگاری با افزونه‌های باقی‌مانده:** WooCommerce، Variation Swatches، WPC Product Quantity، SEOPress (± متاهای قدیمی rank_math)، LiteSpeed Cache، WP-Parsidate، voorodak، fl-panel، hreflangpch.

## ۳. محیط هدف
- WordPress `7.1+` / PHP `8.4+` / WooCommerce `10.9+`
- واحد پول: **IRT (تومان)** — جهت: **RTL** — فونت: **Vazirmatn** (۳ ساب‌ست woff2 موجود در `assets/fonts/`)
- افزونه‌های حذف‌شونده: Elementor, Elementor Pro, JetEngine, JetSearch, JetSmartFilters, JetTabs
- افزونه‌های باقی‌مانده: WooCommerce, Variation Swatches, WPC Product Quantity, SEOPress ± PRO, LiteSpeed Cache, WP-Parsidate, voorodak, fl-panel, hreflangpch

## ۴. معیارهای پرفورمنس و کیفیت
- صفر بودن CSS/JS گوتنبرگ (`wp-block-library`, `global-styles`)، ایموجی و تگ‌های زائد `wp_head`
- Enqueue شرطی CSS/JS به ازای هر قالب + نسخه‌گذاری `filemtime`
- حفظ ۱۰۰٪ ساختار URLهای سئوشده فعلی (اسلاگ‌های `product`, `product_cat`, `projects`, `city`, `project_type`)
- پاس شدن ۱۰۰٪ تست‌های خودکار هر فاز (`temp/phase-XX/tests/run-phase-XX-tests.sh`)
- صفر خطا و صفر Warning با `WP_DEBUG = true`
- پاسخ ایجکس فیلترها/جستجو هدف < ۲۰۰ms

## ۵. معماری کد
- ساختار OOP در `inc/` با Namespace `ErfanSanat\Theme`
- متن دامنه (Text Domain): `erfansanat` — پیشوند آپشن: `erfansanat_theme_options` / هلپر `es_option($key, $default)`
- قالب Non-Block (کلاسیک)، بدون وابستگی به افزونه راه‌انداز (No Required Plugins)

## ۶. منابع پروژه
- میرور استاتیک کامل: `uploads/static-mirror/` (۱۲۸ فایل HTML — فقط مرجع؛ در Git کامیت نمی‌شود)
- مشخصات دیتابیس زنده: `temp/DATA_BLUEPRINT.md`
- نقشه نگاشت استاتیک→وردپرس: `temp/STATIC_TEMPLATE_MAP.md`
