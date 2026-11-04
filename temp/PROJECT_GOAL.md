# PROJECT GOAL — قالب وردپرس و ووکامرس اختصاصی «عرفان صنعت اصفهان» (`erfansanat.com`)

## ۱. ماموریت کلان پروژه (Core Mission)
تبدیل مو‌به‌موی قالب استاتیک آماده (`static-mirror/` شامل HTML5، CSS3 و Vanilla JS آفلاین) به یک **قالب وردپرسی و ووکامرسی کاملاً اختصاصی، شیءگرا (OOP)، فوق‌سریع و بدون صفحه‌ساز (Zero-Bloat / Classic Non-Block Theme)** برای وب‌سایت زنده و فعال **شرکت دانش‌بنیان عرفان صنعت اصفهان (`https://erfansanat.com`)**.

---

## ۲. قوانین خط قرمز (Red-Line Architectural Laws)
1. **قانون مطلق تبدیل استاتیک به داینامیک (100% DOM & Class Fidelity):**
   - طراحی مجدد کدهای HTML یا CSS از صفر **ممنوع** است.
   - تمام فایل‌های قالب وردپرس و `template-parts` باید مستقیماً از برش دقیق فایل‌های HTML داخل `static-mirror/` تولید شوند.
   - **۱۰۰٪ کلاس‌های CSS، ساختار درختی DOM، آیدی‌ها (`id`) و تمام اتریبیوت‌های تعاملی `data-*`** (مورد استفاده در `assets/js/main.js`) باید بدون کم‌وکاست حفظ شوند.
2. **جایگزینی ۱۰۰٪ افزونه‌های صفحه‌ساز و جت (Zero Page-Builder Dependency):**
   - حذف کامل وابستگی به `Elementor`, `Elementor Pro`, `JetEngine`, `JetSearch`, `JetSmartFilters`, `JetTabs`.
   - پیاده‌سازی **داشبورد مدیریتی اختصاصی (`ThemeOptions`)**، **موتور مدیریت سکشن‌ها (`SectionManager`)**، **متاباکس‌های بومی (`ProductMetaboxes`, `ProjectMetaboxes`, `TaxonomyMetaboxes`, `HreflangMetaboxes`)**، **جستجوی زنده ایجکس (`Native Live Search`)** و **فیلتر هوشمند ایجکس (`Native Smart Filters`)** در هسته قالب (`Namespace: ErfanSanat\Theme`).
3. **حفظ ۱۰۰٪ داده‌ها و سئوی سایت زنده (Zero Data-Loss & 100% SEO Continuity):**
   - اتصال دقیق به کلیدهای متای فعلی دیتابیس (حتی کلیدهای دارای املای تاریخی خاص مانند `galerry_produ`, `english_titl`, `usage_countr`, `vaule_of_variable`, `foure-image`, `mortabet_mahsole_2`, `number2-viewc`, `same_key_hrefl`).
   - پشتیبانی دوطرفه از فرمت داده‌های سریالایزشده/JSON ذخیره‌شده توسط `JetEngine` (مانند ساختار `{"item-0": {...}}` در فیلدهای Repeater).
   - حفظ دقیق ساختار Permalinkها و Rewrite Slugها (به‌ویژه اسلاگ `product_cat` با `with_front => true` برای دسته‌بندی محصولات، `/product/` برای تک‌محصول، `/projects/` برای پروژه‌ها، `/project_type/` و `/city/`).
4. **ممنوعیت کدهای ناقص (Zero Placeholders):**
   - استفاده از هرگونه `// TODO`، `<!-- rest of html -->` یا خلاصه‌سازی مارک‌آپ استاتیک خطای بحرانی محسوب می‌شود.
5. **انضباط اجرایی و چرخه TDD (Atomic Task-by-Task Execution):**
   - اجرای گام‌به‌گام فازها و تسک‌ها با چرخه ۶ مرحله‌ای `[READ MEMORY]` -> `[THINK & PLAN]` -> `[WRITE TEST]` -> `[IMPLEMENT]` -> `[RUN TEST & CHECK CONDITIONS]` -> `[UPDATE MEMORY]`.
   - رعایت دروازه‌های تایید انسانی (Human-in-the-Loop Gates).

---

## ۳. معیارهای پرفورمنس، امنیت و کیفیت (Performance, Security & QA Standards)
- **محیط هدف:** WordPress `7.1+`، PHP `8.4+` (`declare(strict_types=1)` سازگار، صفر خطا و صفر Warning در `WP_DEBUG = true`)، WooCommerce `10.9+`، راست‌چین (`RTL`)، واحد پول تومان (`IRT`).
- **پرفورمنس (Zero-Bloat):**
  - حذف کامل CSS/JS بلوک‌های گوتنبرگ (`wp-block-library`, `global-styles`, `classic-theme-styles`)، اسکریپت ایموجی وردپرس و تگ‌های غیرضروری `wp_head`.
  - بارگذاری شرطی و نسخه‌گذاری خودکار (`filemtime`) دارایی‌ها با فونت محلی `Vazirmatn` (`woff2`) بدون هیچ درخواست خارجی.
  - کش استاتیک درون‌حافظه‌ای برای تنظیمات قالب (`es_option($key, $default)`) در آپشن واحد `erfansanat_theme_options`.
  - پاسخگویی اندپوینت‌های ایجکس جستجو و فیلتر هوشمند در کمتر از `200ms` با کوئری‌های بهینه `WP_Query`.
- **امنیت (Hardened WordPress Security):**
  - بررسی دقیق `Nonce` (`check_ajax_referer` / `wp_verify_nonce`) و سطح دسترسی (`current_user_can`) در تمام درخواست‌های ادمین و فرم‌ها.
  - پاکسازی کامل ورودی‌ها (`sanitize_*`) و فراردهی کامل خروجی‌ها (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`).
  - محافظت Honeypot و Rate-Limit در فرم‌های عمومی فرانت‌اند.
- **سازگاری با اکوسیستم افزونه‌های باقی‌مانده سایت زنده:**
  - `WooCommerce`، `Variation Swatches for WooCommerce`، `WPC Product Quantity for WooCommerce`، `SEOPress` & `SEOPress PRO` (به‌همراه Fallback متاهای قدیمی `rank_math_*`)، `LiteSpeed Cache`، `WP-Parsidate`، `voorodak` (ورود پیامکی)، `fl-panel` (پنل کاربری)، و `hreflangpch`.
