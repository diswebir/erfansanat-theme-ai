# MASTER PLAN — نقشه راه اتمیک (۸۲ تسک)

> وضعیت‌ها: `[PENDING]` · `[IN_PROGRESS]` · `[TESTING]` · `[PASSED]` · `[BLOCKED]`
> چرخه ۶ گامی هر تسک: READ MEMORY → THINK & PLAN → WRITE TEST → IMPLEMENT → RUN TEST → UPDATE MEMORY + COMMIT

## STAGE 0 ✅ — تجهیز هارنس (PHP 8.4 wasm، Node 22)، اسکن میرور (۱۲۸ HTML)، حافظه اولیه، پرسش‌نامه Q1-Q4
## STAGE 1 ✅ — درخت فازها (نسخه اولیه)
## STAGE 1-R ✅ — **بازطراحی اتمیک درخت به درخواست کارفرما** (۲۲ تسک → ۸۲ تسک اتمیک با جزئیات کامل)

---
## Phase 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets `[PENDING]`
**هدف فاز:** ساخت اسکلت قالب وردپرس با معماری OOP، حذف کامل بloat وردپرس و انتقال یک‌به‌یک دارایی‌های میرور استاتیک
**پوشه:** `temp/phase-01-core-and-assets/` · **تست جامع:** `tests/run-phase-01-tests.sh`

### Task 1.1 — اسکلت پایه قالب و بوت‌استرپ (style.css / functions.php / index.php) `[PENDING]`
- 🎯 فایل‌های ریشه قالب به‌گونه‌ای ساخته شوند که وردپرس قالب را بدون خطا بشناسد و bootstrap مرکزی وجود داشته باشد
- 📤 خروجی: `style.css (هدر استاندارد قالب)`؛ `functions.php (bootstrap)`؛ `index.php (fallback template)` · **شروع‌کننده فاز**
- ✅ 5 شرط پاس · 🛠 5 گام اجرا

### Task 1.2 — Autoloader PSR-4 با Namespace ErfanSanat\Theme و هلپرهای پایه `[PENDING]`
- 🎯 هر کلاسی که در inc/ ساخته می‌شود بدون require دستی لود شود و توابع هلپر مرکزی (es_option، es_template) آماده باشند
- 📤 خروجی: `inc/Autoloader.php`؛ `inc/Helpers.php` · **وابستگی:** Task 1.1
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 1.3 — Theme Supports، ثبت ۴ موقعیت منو و هوک‌های فعال‌سازی `[PENDING]`
- 🎯 قابلیت‌های هسته قالب ثبت شود و ۴ منوی بلوپرینت (main-mega-menu, mobile-menu, tolidat-sherkat, landing) با اسلاگ دقیق ثبت شوند
- 📤 خروجی: `inc/ThemeSetup.php` · **وابستگی:** Task 1.1
- ✅ 5 شرط پاس · 🛠 5 گام اجرا

### Task 1.4 — DeBloat — حذف کامل CSS/JS گوتنبرگ از فرانت‌اند `[PENDING]`
- 🎯 هیچ فایل یا استایل درون‌خطی بلوک‌های وردپرس در فرانت لود نشود؛ بدون تخریب ادیتور ادمین
- 📤 خروجی: `inc/Frontend/DeBloat.php` · **وابستگی:** Task 1.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 1.5 — DeBloat — ایموجی، wp_head cleanup و حذف متاهای زائد `[PENDING]`
- 🎯 پاکسازی wp_head از ایموجی، RSD، WLW، generator، shortlink، REST link، oEmbed و feedهای اضافه برای حداکثر سرعت
- 📤 خروجی: `بسط inc/Frontend/DeBloat.php` · **وابستگی:** Task 1.4
- ✅ 3 شرط پاس · 🛠 6 گام اجرا

### Task 1.6 — انتقال بی‌نقص دارایی‌های میرور به assets/ قالب `[PENDING]`
- 🎯 کپی یک‌به‌یک styles.css، main.js، ۳ فونت Vazirmatn و تصاویر/ویدیو با حفظ ساختار پوشه‌ها تا هیچ مسیر نسبی url(... نشکند
- 📤 خروجی: `assets/css/styles.css`؛ `assets/js/main.js`؛ `assets/fonts/vazirmatn-{arabic,latin,latin-ext}.woff2`؛ `assets/images/*`؛ `assets/media/hero-video.mp4` · **وابستگی:** Task 1.1
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 1.7 — AssetManager — Enqueue سراسری/شرطی با نسخه‌گذاری filemtime و Preload فونت `[PENDING]`
- 🎯 استایل و اسکریپت قالب با نسخه خودکار (کش‌باستینگ) و فونت‌ها با preload صحیح لود شوند؛ زیرساخت شرطی‌سازی per-template آماده فازهای بعد باشد
- 📤 خروجی: `inc/Frontend/AssetManager.php` · **وابستگی:** Task 1.6, Task 1.2
- ✅ 5 شرط پاس · 🛠 6 گام اجرا

### Task 1.8 — زیرساخت تست — WP Stubs و Bootstrap تست‌های واحد (بدون نصب وردپرس) `[PENDING]`
- 🎯 چون تست‌ها روی PHP wasm بدون وردپرس اجرا می‌شوند، یک لایه stub سبک توابع وردپرس لازم است تا کلاس‌های قالب unit-test پذیر شوند
- 📤 خروجی: `temp/harness/wp-stubs.php`؛ `temp/harness/README.md` · **وابستگی:** Task 1.2
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

---
## Phase 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder `[PASSED]`
**هدف فاز:** جایگزین کامل پنل‌های Elementor/JetEngine: داشبورد تب‌دار RTL با ذخیره AJAX، موتور فیلد و Sanitizer اختصاصی و موتور مدیریت سکشن صفحات
**پوشه:** `temp/phase-02-custom-admin-dashboard/` · **تست جامع:** `tests/run-phase-02-tests.sh`

### Task 2.1 — هسته ThemeOptions — منوی پیشخوان، صفحه تب‌دار RTL و فریم‌ورک AJAX امن `[PASSED]`
- 🎯 اسکلت پنل ادمین با امنیت کامل (Nonce + Capability) و ذخیره ایجکس در یک آپشن واحد
- 📤 خروجی: `inc/Admin/ThemeOptions.php`؛ `inc/Admin/AdminAssets.php`؛ `assets/admin/admin.css`؛ `assets/admin/admin.js` · **وابستگی:** Task 1.2
- ✅ 5 شرط پاس · 🛠 6 گام اجرا

### Task 2.2 — موتور فیلدها (FieldRenderer) و Sanitizer اختصاصی `[PASSED]`
- 🎯 کامپوننت‌های رابط ادمین (۸ نوع فیلد + repeater) و sanitize امن هر نوع، قابل استفاده مجدد در تب‌ها و SectionManager
- 📤 خروجی: `inc/Admin/FieldRenderer.php`؛ `inc/Admin/Sanitizer.php`؛ `بسط assets/admin/admin.js (media picker، color، repeater، switch)` · **وابستگی:** Task 2.1
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 2.3 — تب «هدر و لوگو» + نوار اعلان — فیلدها و Fallback دقیق میرور `[PASSED]`
- 🎯 مدیریت لوگو، هدر چسبان، دکمه‌های هدر و نوار اعلان با پیش‌فرض‌هایی که عین میرور است
- 📤 خروجی: `بسط ThemeOptions.php (schema تب header)` · **وابستگی:** Task 2.2
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 2.4 — تب «فوتر، اطلاعات تماس و شبکه‌های اجتماعی» `[PASSED]`
- 🎯 مدیریت کامل محتوای فوتر سه‌ستونه و اطلاعات تماس با پیش‌فرض‌های دقیق JSON-LD میرور
- 📤 خروجی: `بسط ThemeOptions.php (schema تب footer)` · **وابستگی:** Task 2.2
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 2.5 — تب «برند/استایل» + «اسکریپت‌های سفارشی» + «تنظیمات آرشیوها» `[PASSED]`
- 🎯 رنگ‌های برند، تزریق اسکریپت هدر/فوتر کارفرما و تنظیمات لیست‌بندی آرشیوها
- 📤 خروجی: `بسط ThemeOptions.php (سه تب)` · **وابستگی:** Task 2.2
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 2.6 — هلپر نهایی es_option + تزریق تنظیمات به فرانت‌اند `[PASSED]`
- 🎯 اتصال کامل آپشن‌ها به فرانت: توابع رندر آماده برای فاز ۴ به بعد
- 📤 خروجی: `بسط inc/Helpers.php`؛ `inc/Frontend/OptionsOutput.php` · **وابستگی:** Task 2.3, Task 2.4, Task 2.5
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 2.7 — SectionManager — متاباکس، UI لیست سکشن‌ها (toggle + Drag & Drop) `[PASSED]`
- 🎯 اسکلت موتور مدیریت سکشن روی برگه‌ها: ردیف سکشن با کلید روشن/خاموش و جابه‌جایی درگ‌اند‌دراپ
- 📤 خروجی: `inc/Admin/SectionManager.php`؛ `بسط assets/admin/admin.js (sortable داخلی)` · **وابستگی:** Task 1.2
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 2.8 — SectionManager — رندر فیلدهای داینامیک هر سکشن (media/url/repeater) `[PASSED]`
- 🎯 پنل هر سکشن بر اساس schema خودش فیلدها را با FieldRenderer بسازد و ذخیره ساختارمند انجام شود
- 📤 خروجی: `بسط SectionManager.php` · **وابستگی:** Task 2.7, Task 2.2
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 2.9 — SectionManager — API رندر فرانت (es_render_sections) و پیکربندی اولیه صفحات `[PASSED]`
- 🎯 حلقه رندر سکشن‌های فعال به ترتیب ادمین + ثبت schema سکشن‌های home/about/contact/landing (بخش‌های مشترک)؛ سکشن‌های اختصاصی در فازهای ۵ و ۷ اضافه می‌شوند
- 📤 خروجی: `بسط SectionManager.php (رندر)`؛ `inc/Sections/Registry.php (ثبت سکشن‌های صفحات)` · **وابستگی:** Task 2.8
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

---
## Phase 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده `[PASSED]`
**هدف فاز:** ثبت Native داده‌های JetEngine با املای دقیق کلیدها و حفظ ۱۰۰٪ URLها؛ هیچ بایت دیتا از دست نرود
**پوشه:** `temp/phase-03-cpt-and-legacy-meta/` · **تست جامع:** `tests/run-phase-03-tests.sh`

### Task 3.1 — ثبت CPT projects و تکسونومی‌های project_type و city `[PASSED]`
- 🎯 جایگزینی مستقیم رجیستر JetEngine با کد قالب — آرگومان‌ها بایت‌به‌بایت مطابق بلوپرینت
- 📤 خروجی: `inc/Data/PostTypes.php` · **وابستگی:** Task 1.3
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 3.2 — فیلترهای ادمین لیست پروژه‌ها (دراپ‌داون city/project_type + ستون same_key_hrefl) `[PASSED]`
- 🎯 مدیریت راحت پروژه‌ها: فیلتر دراپ‌داونی تکسونومی‌ها و ستون مرتب‌سازی کلید hreflang
- 📤 خروجی: `inc/Data/AdminListFilters.php` · **وابستگی:** Task 3.1
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 3.3 — ثبت CPT services (تصمیم Q2 کارفرما) `[PASSED]`
- 🎯 پست‌تایپ خدمات به‌صورت Native — مطابق تصمیم ثبت‌شده D-005
- 📤 خروجی: `بسط inc/Data/PostTypes.php` · **وابستگی:** Task 1.3
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 3.4 — Rewrite تکسونومی‌های ووکامرس (product_cat با with_front=true) و Flush نهایی `[PASSED]`
- 🎯 حفظ ساختار سئوی فعلی URLها: /product_cat/{term} با with_front=true و flush خودکار هنگام فعال‌سازی قالب
- 📤 خروجی: `inc/Data/WooRewrite.php`؛ `بسط هوک after_switch_theme` · **وابستگی:** Task 3.1, Task 3.3
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 3.5 — متاباکس محصول — تب شناسه‌های صنعتی + Quick Edit `[PASSED]`
- 🎯 مدیریت ۶ شناسه قانونی با املای دقیق دیتابیس + ویرایش سریع از لیست محصولات
- 📤 خروجی: `inc/Admin/ProductMetaboxes.php (هسته + تب identifiers)`؛ `inc/Admin/QuickEdit.php` · **وابستگی:** Task 1.2
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 3.6 — متاباکس محصول — تب عناوین و وضعیت فروش (zireonvan/english_titl/true_sale/before-sale-c) `[PASSED]`
- 🎯 مدیریت زیرعنوان، عنوان انگلیسی (املا خاص)، اطلاعیه قبل از خرید و وضعیت خرید آنلاین/استعلامی
- 📤 خروجی: `بسط ProductMetaboxes.php (تب titles)` · **وابستگی:** Task 3.5
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 3.7 — موتور Repeater سازگار JetEngine (خواندن/نوشتن دو-مد serial/JSON) `[PASSED]`
- 🎯 کلاس مرکزی خواندن و نوشتن فیلدهای تکرارشونده با حفظ دقیق فرمت فعلی دیتابیس ({item-0:{…}})
- 📤 خروجی: `inc/Data/RepeaterEngine.php` · **وابستگی:** Task 1.2
- ✅ 5 شرط پاس · 🛠 5 گام اجرا

### Task 3.8 — متاباکس محصول — Repeaterهای ویژگی‌ها/کاربردها/FAQ با موتور 3.7 `[PASSED]`
- 🎯 چهار فیلد تکرارشونده محصول با UI افزودن/حذف/جابه‌جایی و کلیدهای دقیق دیتابیس
- 📤 خروجی: `بسط ProductMetaboxes.php (تب repeaters)` · **وابستگی:** Task 3.7, Task 2.2
- ✅ 3 شرط پاس · 🛠 6 گام اجرا

### Task 3.9 — متاباکس محصول — گالری (galerry_produ + fallbackها) و فایل کاتالوگ `[PASSED]`
- 🎯 مدیریت گالری سازگار با سه منبع دیتا + دانلود کاتالوگ، با خواندن fallback کامل
- 📤 خروجی: `بسط ProductMetaboxes.php (تب gallery)`؛ `inc/Data/ProductGallery.php` · **وابستگی:** Task 3.5
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 3.10 — متاباکس محصول — روابط (project-ids و usage_countr) `[PASSED]`
- 🎯 انتخاب چندگانه پروژه‌های مرتبط و کاربردها (ترم‌های project_type) با ذخیره آرایه/JSON مطابق ساختار فعلی
- 📤 خروجی: `بسط ProductMetaboxes.php (تب relations)`؛ `inc/Admin/RelatedPicker.php` · **وابستگی:** Task 3.7, Task 3.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 3.11 — متاباکس پروژه — گالری شش‌تایی قدیمی + گالری مدرن همگام `[PASSED]`
- 🎯 مدیریت تصاویر پروژه با کلیدهای تاریخی (second/three/foure/five/six-image) و یک گالری مدرن که با همین کلیدها sync می‌شود
- 📤 خروجی: `inc/Admin/ProjectMetaboxes.php (تب gallery)` · **وابستگی:** Task 1.2
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 3.12 — متاباکس پروژه — تاریخ تکمیل و محصولات استفاده‌شده `[PASSED]`
- 🎯 مدیریت finish-pro-time (متنی با فرمت شمسی) و انتخاب محصولات پروژه (mortabet_mahsole_2)
- 📤 خروجی: `بسط ProjectMetaboxes.php (تب info)` · **وابستگی:** Task 3.10, Task 3.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 3.13 — متای ترم product_cat — تصویر دسته (thumbnail) و FAQ دسته `[PASSED]`
- 🎯 فرم‌های افزودن/ویرایش دسته محصول با فیلد تصویر و سوالات متداول تکرارشونده (کلیدهای thumbnail و faq)
- 📤 خروجی: `inc/Admin/CategoryTermMeta.php` · **وابستگی:** Task 3.7
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 3.14 — Hreflang سراسری + شمارنده بازدید (number2-viewc) `[PASSED]`
- 🎯 متاباکس ترجمه‌ها روی همه انواع محتوا + ستون مرتب‌سازی + شمارنده بازدید با fallback و ضد-بات
- 📤 خروجی: `inc/Admin/HreflangMeta.php`؛ `inc/Frontend/ViewCounter.php` · **وابستگی:** Task 1.2
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

---
## Phase 04 — هدر، مگامنو، منوی موبایل و فوتر `[PASSED]`
**هدف فاز:** برش دقیق بلوک‌های مشترک ۱۲۸ صفحه به header.php/footer.php با Nav Walkerهای اختصاصی و اتصال به داشبورد — حفظ ۱۰۰٪ DOM و data-hookها
**پوشه:** `temp/phase-04-header-footer-navigation/` · **تست جامع:** `tests/run-phase-04-tests.sh`

### Task 4.1 — استخراج فایل‌های مرجع هدر/فوتر و سند نقشه برش `[PASSED]`
- 🎯 قبل از برش، مرجع ثابت DOM و نقشه بلوک‌ها مستند شود تا تست‌های مقایسه‌ای فاز معیار داشته باشند
- 📤 خروجی: `temp/phase-04-header-footer-navigation/refs/header-ref.html`؛ `temp/phase-04-header-footer-navigation/refs/footer-ref.html`؛ `temp/phase-04-header-footer-navigation/refs/HEADER_MAP.md` · **شروع‌کننده فاز**
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 4.2 — header.php — shell سند، hreflang و فراخوانی‌های استاندارد وردپرس `[PASSED]`
- 🎯 هست استاندارد قالب: زبان/charset/body_class/wp_head/wp_body_open + تگ‌های hreflang داینامیک از متا
- 📤 خروجی: `header.php` · **وابستگی:** Task 4.1, Task 3.14, Task 1.7
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 4.3 — header.php — بلوک هدر بالایی (لوگو، اکشن‌ها، شمارنده سبد) `[PASSED]`
- 🎯 برش دقیق مارک‌آپ هدر بالایی با جایگزینی داینامیک لوگو/لینک‌ها/شمارنده سبد — بدون تغییر DOM
- 📤 خروجی: `بسط header.php`؛ `template-parts/header/header-top.php` · **وابستگی:** Task 4.2, Task 2.3, Task 2.6
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 4.4 — MegaMenuWalker برای main-mega-menu `[PASSED]`
- 🎯 Walker اختصاصی تولید مارک‌آپ مگامنو تب‌دار (پنل محصولات/پروژه‌ها) دقیقاً مطابق میرور با آیتم‌های منوی وردپرس
- 📤 خروجی: `inc/Nav/MegaMenuWalker.php` · **وابستگی:** Task 4.3, Task 1.3
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 4.5 — MobileWalker و سایدبار موبایل (off-canvas) `[PASSED]`
- 🎯 منوی موبایل چندسطحی با آکاردئون (mobile-acc-*) دقیقاً مطابق سایدبار میرور
- 📤 خروجی: `inc/Nav/MobileWalker.php`؛ `template-parts/header/mobile-sidebar.php` · **وابستگی:** Task 4.4
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 4.6 — footer.php — سه ستون، اطلاعات تماس داینامیک و scroll-top `[PASSED]`
- 🎯 برش فوتر با جایگزینی ستون‌ها (منو/دسته‌بندی) و اطلاعات تماس از داشبورد
- 📤 خروجی: `footer.php`؛ `template-parts/footer/footer-main.php` · **وابستگی:** Task 4.1, Task 2.6, Task 2.4
- ✅ 3 شرط پاس · 🛠 6 گام اجرا

### Task 4.7 — Walkerهای tolidat-sherkat و landing `[PASSED]`
- 🎯 دو موقعیت منوی ساده: تولیدات شرکت (فوتر/هدر) و منوی اسکرول لندینگ‌ها
- 📤 خروجی: `inc/Nav/SimpleWalker.php`؛ `inc/Nav/LandingWalker.php` · **وابستگی:** Task 4.4
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 4.8 — تست جامع DOM هدر/فوتر در برابر مرجع استاتیک `[PASSED]`
- 🎯 اسکریپت مقایسه ساختاری: تعداد و ترتیب data-hookها و کلاس‌های کلیدی خروجی رندر شده با ref میرور برابر باشد
- 📤 خروجی: `بسط temp/phase-04…/tests/test-task-08.php (مقایسه DOM با DOMDocument)` · **وابستگی:** Task 4.3, Task 4.5, Task 4.6
- ✅ 3 شرط پاس · 🛠 3 گام اجرا

---
## Phase 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار `[PENDING]`
**هدف فاز:** ۱۴ سکشن index.html → template-parts داینامیک متصل به WP_Query و داشبورد؛ صفر محتوای هاردکد
**پوشه:** `temp/phase-05-homepage-and-sections/` · **تست جامع:** `tests/run-phase-05-tests.sh`

### Task 5.1 — front-page.php + پیکربندی سکشن‌های صفحه اصلی در SectionManager `[PENDING]`
- 🎯 قالب صفحه اصلی با رندر پویای SectionManager و ثبت schema کامل ۱۴ سکشن (ترتیب پیش‌فرض عین میرور)
- 📤 خروجی: `front-page.php`؛ `inc/Sections/HomeSections.php` · **وابستگی:** Task 2.9
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.2 — سکشن هیرو طرح ۱ (hero-home1) — نوتچ، scene switcher و ویدیو `[PENDING]`
- 🎯 برش سکشن #home شامل سوییچر صحنه‌ها و ویدیو درون‌خطی با حفظ همه hookهای JS
- 📤 خروجی: `template-parts/home/section-hero.php` · **وابستگی:** Task 5.1
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 5.3 — سکشن گالری پروژه‌ها (projects) `[PENDING]`
- 🎯 نمایش آخرین/برگزیده پروژه‌ها از WP_Query با کارت استاندارد
- 📤 خروجی: `template-parts/home/section-projects.php` · **وابستگی:** Task 5.1, Task 5.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.4 — سکشن درباره ما + آمار شمارنده `[PENDING]`
- 🎯 برش سکشن‌های #about و معرفی + بلوک آمار با شمارنده انیمیشنی میرور
- 📤 خروجی: `template-parts/home/section-about.php` · **وابستگی:** Task 5.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.5 — سکشن تایم‌لاین تاریخچه (history) `[PENDING]`
- 🎯 تایم‌لاین اسکرولی ۶ مرحله‌ای با progress bar — داده از Repeater
- 📤 خروجی: `template-parts/home/section-history.php` · **وابستگی:** Task 5.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.6 — سکشن دسته‌بندی محصولات (categories) `[PENDING]`
- 🎯 گرید دسته‌های product_cat با تصویر ترم (متای فاز 3.13) و fallback
- 📤 خروجی: `template-parts/home/section-categories.php` · **وابستگی:** Task 5.1, Task 3.13
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 5.7 — سکشن کاتالوگ دانلود + سرویس‌های تب‌دار `[PENDING]`
- 🎯 دو سکشن متنی/تعاملی: دانلود کاتالوگ جامع و تب خدمات با preview میرور
- 📤 خروجی: `template-parts/home/section-catalog.php`؛ `template-parts/home/section-services.php` · **وابستگی:** Task 5.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.8 — سکشن خدمات پس از فروش + مشتریان `[PENDING]`
- 🎯 دو سکشن اعتمادساز: aftersale و لوگوی مشتریان/سازمان‌ها
- 📤 خروجی: `template-parts/home/section-aftersale.php`؛ `template-parts/home/section-customers.php` · **وابستگی:** Task 5.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.9 — سکشن ویترین مقالات (blog-preview) `[PENDING]`
- 🎯 آخرین مقالات با WP_Query و کارت مقاله + لینک وبلاگ
- 📤 خروجی: `template-parts/home/section-blog.php` · **وابستگی:** Task 5.1, Task 5.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.10 — سکشن تماس با ما + نقشه کارخانه `[PENDING]`
- 🎯 اطلاعات تماس داینامیک + نقشه (iframe/تصویر) از داشبورد
- 📤 خروجی: `template-parts/home/section-contact.php`؛ `template-parts/home/section-map.php` · **وابستگی:** Task 5.1, Task 2.6
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.11 — کارت‌های استاندارد: card-product / card-project / card-post `[PENDING]`
- 🎯 سه قالب کارت مشترک مطابق کارت‌های میرور — قابل استفاده در همه آرشیوها و سکشن‌ها
- 📤 خروجی: `template-parts/cards/card-product.php`؛ `template-parts/cards/card-project.php`؛ `template-parts/cards/card-post.php` · **وابستگی:** Task 1.2
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 5.12 — تست جامع رندر صفحه اصلی (مقایسه با index.html و سکشن‌های toggle) `[PENDING]`
- 🎯 رندر کامل front-page در حالت پیش‌فرض = ساختار میرور؛ تغییر ترتیب/حذف از ادمین اثر واقعی داشته باشد
- 📤 خروجی: `بسط temp/phase-05…/tests/test-task-12.php` · **وابستگی:** Task 5.2 … 5.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

---
## Phase 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت `[PENDING]`
**هدف فاز:** تبدیل صفحات ووکامرس با پوسته دقیق میرور؛ منطق true_sale و سازگاری کامل افزونه‌های فعال
**پوشه:** `temp/phase-06-woocommerce-and-products/` · **تست جامع:** `tests/run-phase-06-tests.sh`

### Task 6.1 — Wrapper ووکامرس و پاکسازی پیش‌فرض‌های آن `[PENDING]`
- 🎯 صفحات ووکامرس داخل shell قالب رندر شوند و خروجی‌های پیش‌فرض تکراری (breadcrumb/سایدبار/related) حذف شوند
- 📤 خروجی: `woocommerce.php`؛ `inc/Woo/Setup.php` · **وابستگی:** Task 4.2, Task 4.6
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.2 — single-product.php — هیرو محصول و گالری لایت‌باکس میرور `[PENDING]`
- 🎯 هیرو (breadcrumb/H1/زیرعنوان/badges) و گالری دقیق میرور متصل به منابع گالری چندگانه
- 📤 خروجی: `single-product.php`؛ `template-parts/single-product/hero.php`؛ `template-parts/single-product/gallery.php` · **وابستگی:** Task 6.1, Task 3.9
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.3 — single-product — تب‌های مشخصات و جدول ۱۸ ویژگی فنی (pa_*) `[PENDING]`
- 🎯 تب‌بندی prod-tab میرور با جدول کامل اتریبیوت‌ها + شناسه‌های قانونی
- 📤 خروجی: `template-parts/single-product/specs-tabs.php` · **وابستگی:** Task 6.2, Task 3.5
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.4 — single-product — Repeaterها، FAQ، کاتالوگ و اطلاعیه قبل از خرید `[PENDING]`
- 🎯 رندر product-feature، feature2 دو بخشی، usage_product/usage_countr، faq آکاردئونی، دکمه کاتالوگ و نوار اطلاعیه
- 📤 خروجی: `template-parts/single-product/features.php`؛ `template-parts/single-product/usages.php`؛ `template-parts/single-product/faq.php`؛ `template-parts/single-product/notice.php` · **وابستگی:** Task 6.2, Task 3.8
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 6.5 — single-product — ناحیه خرید: true_sale، تعداد (min/step)، سوئچ‌ها و toast `[PENDING]`
- 🎯 منطق دوگانه خرید آنلاین/استعلامی + سازگاری WPC Quantity و Variation Swatches با مارک‌آپ pdp-* میرور
- 📤 خروجی: `template-parts/single-product/purchase.php`؛ `inc/Woo/PurchaseFlow.php` · **وابستگی:** Task 6.2, Task 3.6
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.6 — single-product — پروژه‌های مرتبط، محصولات مرتبط و دیدگاه‌ها `[PENDING]`
- 🎯 اتصال روابط متا + دیدگاه‌های ووکامرس با مارک‌آپ review میرور
- 📤 خروجی: `template-parts/single-product/related-projects.php`؛ `template-parts/single-product/related-products.php`؛ `template-parts/single-product/reviews.php` · **وابستگی:** Task 6.2, Task 3.10
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.7 — archive-product (shop) — هیرو، نوار فیلتر، گرید و صفحه‌بندی `[PENDING]`
- 🎯 آرشیو محصولات دقیقاً مطابق shop.html با فیلترهای GET کارا تا رسیدن فاز ۸
- 📤 خروجی: `archive-product.php`؛ `template-parts/archive/product-filters.php`؛ `template-parts/archive/pagination.php` · **وابستگی:** Task 6.1, Task 5.11
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 6.8 — taxonomy-product_cat (+FAQ دسته) و taxonomy-product_tag `[PENDING]`
- 🎯 آرشیو دسته با هیرو/توضیح/FAQ و آرشیو برچسب مطابق میرور
- 📤 خروجی: `taxonomy-product_cat.php`؛ `taxonomy-product_tag.php` · **وابستگی:** Task 6.7, Task 3.13
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.9 — سبد خرید ووکامرس با پوسته cart.html `[PENDING]`
- 🎯 جدول سبد، تغییر تعداد و خلاصه — مارک‌آپ میرور با هوک‌های استاندارد ووکامرس
- 📤 خروجی: `woocommerce/cart/cart.php (override)`؛ `بسط inc/Woo/Setup.php` · **وابستگی:** Task 6.1
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 6.10 — checkout، فیلدهای شخص/شرکت و صفحه دریافت فاکتور (thank-you) `[PENDING]`
- 🎯 پوسته checkout میرور + toggle نوع خریدار + فیلدهای رسید (کد پستی/شناسه ملی) + thank-you مطابق checkout-receipt
- 📤 خروجی: `woocommerce/checkout/*.php overrides (form-checkout.php، thankyou.php)`؛ `inc/Woo/CheckoutFields.php` · **وابستگی:** Task 6.9
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.11 — حساب کاربری (my-account) و صفحه auth (ورود پیامکی voorodak) `[PENDING]`
- 🎯 پوسته یکدست صفحات حساب کاربری ووکامرس و هماهنگی با افزونه ورود پیامکی
- 📤 خروجی: `woocommerce/myaccount/*.php overrides (سبک)`؛ `assets/css/wc-forms-override.css (یا در styles میرور enqueue شرطی)` · **وابستگی:** Task 6.10
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

---
## Phase 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها `[PENDING]`
**هدف فاز:** قالب‌های سینگل/آرشیو پروژه‌ها، وبلاگ کامل (TOC/بازدید/اشتراک)، برگه‌های اختصاصی قابل ویرایش و لندینگ‌ها
**پوشه:** `temp/phase-07-projects-blog-and-pages/` · **تست جامع:** `tests/run-phase-07-tests.sh`

### Task 7.1 — single-projects.php — سینگل کامل پروژه `[PENDING]`
- 🎯 هیرو، گالری شش‌تایی، مشخصات (تاریخ/شهر/نوع)، بدنه، محصولات پروژه و پروژه‌های مرتبط
- 📤 خروجی: `single-projects.php`؛ `template-parts/single-project/*.php` · **وابستگی:** Task 3.11, Task 3.12, Task 5.11
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 7.2 — archive-projects.php — آرشیو پروژه‌ها با فیلترها `[PENDING]`
- 🎯 آرشیو مطابق projects.html: هیرو، فیلتر نوع/شهر/مرتب‌سازی، گرید و صفحه‌بندی
- 📤 خروجی: `archive-projects.php`؛ `template-parts/archive/project-filters.php` · **وابستگی:** Task 5.11
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 7.3 — taxonomy-project_type.php و taxonomy-city.php `[PENDING]`
- 🎯 آرشیو ترم‌های پروژه مطابق میرور city/*.html و project_type/*.html
- 📤 خروجی: `taxonomy-project_type.php`؛ `taxonomy-city.php` · **وابستگی:** Task 7.2
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 7.4 — home.php / archive.php / category.php — وبلاگ و فیلتر دسته `[PENDING]`
- 🎯 آرشیو مقالات مطابق blog.html و category/magazine.html با فیلتر دسته کارا
- 📤 خروجی: `home.php`؛ `archive.php`؛ `category.php` · **وابستگی:** Task 5.11
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 7.5 — single.php — مقاله کامل با TOC، اشتراک، بازدید و مرتبط `[PENDING]`
- 🎯 سینگل مقاله مطابق blog/*.html با فهرست خودکار از سرفصل‌ها و اتصال شمارنده بازدید
- 📤 خروجی: `single.php`؛ `inc/Frontend/TOC.php` · **وابستگی:** Task 7.4, Task 3.14
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 7.6 — search.php و 404.php `[PENDING]`
- 🎯 نتایج جستجوی ترکیبی با کارت‌های استاندارد + صفحه ۴۰۴ مطابق سبک میرور
- 📤 خروجی: `search.php`؛ `404.php`؛ `searchform.php` · **وابستگی:** Task 5.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 7.7 — قالب برگه‌های about و contact (با SectionManager) `[PENDING]`
- 🎯 دو قالب برگه با سکشن‌های کاملاً قابل ویرایش از ادمین — پیش‌فرض عین میرور
- 📤 خروجی: `page-templates/page-about.php`؛ `page-templates/page-contact.php`؛ `inc/Sections/AboutSections.php`؛ `inc/Sections/ContactSections.php` · **وابستگی:** Task 2.9, Task 8.5 (اسکلت فرم)
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 7.8 — قالب برگه‌های licenses، faq، refund_returns و recruitment `[PENDING]`
- 🎯 چهار قالب برگه اختصاصی با Repeaterهای قابل ویرایش و فرم استخدام
- 📤 خروجی: `page-templates/page-licenses.php`؛ `page-templates/page-faq.php`؛ `page-templates/page-refund.php`؛ `page-templates/page-recruitment.php`؛ `inc/Sections/*.php مربوطه` · **وابستگی:** Task 2.9
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 7.9 — خدمات: single-services.php + فهرست خدمات + card-service `[PENDING]`
- 🎯 قالب‌های CPT services مطابق سینگل‌ها و services.html میرور
- 📤 خروجی: `single-services.php`؛ `page-templates/page-services.php (فهرست)`؛ `template-parts/cards/card-service.php` · **وابستگی:** Task 3.3, Task 5.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 7.10 — لندینگ‌ها: arya و smartelement + منوی landing `[PENDING]`
- 🎯 دو لندینگ اسکرولی از کامپوننت‌های میرور (تصمیم Q3) با منوی landing ثابت
- 📤 خروجی: `page-templates/page-landing.php`؛ `inc/Sections/LandingSections.php` · **وابستگی:** Task 4.7, Task 2.9
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

---
## Phase 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی `[PENDING]`
**هدف فاز:** موتور جستجو/فیلتر ایجکس سریع و امن، سیستم فرم‌ها با مدیریت ادمین، ممیزی سئو/پرفورمنس/امنیت و آمادگی استقرار
**پوشه:** `temp/phase-08-ajax-filters-and-audit/` · **تست جامع:** `tests/run-phase-08-tests.sh`

### Task 8.1 — زیرساخت AJAX امن (Router + Nonce + Rate Limit + Response استاندارد) `[PENDING]`
- 🎯 پایه مشترک همه سرویس‌های ایجکس با امنیت کامل و قالب پاسخ ثابت
- 📤 خروجی: `inc/Ajax/Router.php` · **وابستگی:** Task 1.7
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 8.2 — جستجوی زنده (Live Search) جایگزین JetSearch `[PENDING]`
- 🎯 جستجوی اینستنت محصولات/پروژه‌ها/مقالات در مودال جستجوی هدر با رندر partial سمت سرور
- 📤 خروجی: `inc/Ajax/LiveSearch.php`؛ `assets/js/live-search.js (نازک روی hookهای میرور)` · **وابستگی:** Task 8.1
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 8.3 — فیلتر هوشمند محصولات (جایگزین JetSmartFilters) `[PENDING]`
- 🎯 فیلتر ایجکس گرید آرشیو محصولات: متن/دسته/قیمت/وضعیت خرید/IP/ولتاژ/مرتب‌سازی/تعداد — بدون رفرش
- 📤 خروجی: `inc/Ajax/ProductFilters.php`؛ `بسط live-search.js یا assets/js/product-filters.js` · **وابستگی:** Task 8.1, Task 6.7
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 8.4 — فیلتر هوشمند پروژه‌ها (type/city + شمارنده) `[PENDING]`
- 🎯 فیلتر ایجکس آرشیو پروژه‌ها مطابق بلوپرینت با شمارنده تعداد هر ترم
- 📤 خروجی: `inc/Ajax/ProjectFilters.php` · **وابستگی:** Task 8.1, Task 7.2
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 8.5 — سیستم فرم‌ها — Backend (ثبت امن + مدیریت ادمین + ایمیل) `[PENDING]`
- 🎯 موتور ثبت پیام‌های فرم‌ها: CPT داخلی، Honeypot/Nonce/Rate limit، لیست ادمین و اطلاع ایمیل
- 📤 خروجی: `inc/Forms/Manager.php`؛ `inc/Forms/AdminList.php` · **وابستگی:** Task 8.1
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 8.6 — فرم‌های فرانت: تماس، استعلام قیمت، استخدام، بازخورد `[PENDING]`
- 🎯 اتصال چهار فرم میرور به Backend با پیام‌های موفقیت/خطای میرور
- 📤 خروجی: `assets/js/forms.js`؛ `template-parts/forms/*.php (۴ فرم)` · **وابستگی:** Task 8.5, Task 6.5, Task 7.7, Task 7.8
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 8.7 — ممیزی سئو: JSON-LD داینامیک، Hreflang و سازگاری SEOPress `[PENDING]`
- 🎯 اسکیماهای ساختاریافته داینامیک + hreflang بدون تکرار + پشتیبانی متاهای قدیمی rank_math برای SEOPress
- 📤 خروجی: `inc/SEO/Schema.php`؛ `inc/SEO/Hreflang.php` · **وابستگی:** Task 3.14, Task 6.2
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 8.8 — ممیزی پرفورمنس و DeBloat نهایی `[PENDING]`
- 🎯 تأیید بارگذاری حداقلی: هیچ CSS/JS بloat، فونت preload، lazy تصاویر، سازگاری کش LiteSpeed
- 📤 خروجی: `تقریر ممیزی temp/phase-08…/reports/performance-audit.md`؛ `بسط‌های ریز AssetManager/DeBloat در صورت نیاز` · **وابستگی:** Task همه فازهای فرانت
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 8.9 — ممیزی امنیت و i18n (Escaping/Sanitize/Text Domain) `[PENDING]`
- 🎯 سوییپ نهایی امنیت خروجی/ورودی و یکدستی text domain و ترجمه‌پذیری
- 📤 خروجی: `تقریر temp/phase-08…/reports/security-i18n-audit.md`؛ `اصلاحات نقطه‌ای` · **وابستگی:** Task همه فازها
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 8.10 — سوئیت E2E کل پروژه + چک‌لیست استقرار روی سایت زنده `[PENDING]`
- 🎯 اجرای run-all-phases-e2e با پاس ۱۰۰٪ + مستند استقرار مرحله‌به‌مرحله (غیرفعال‌سازی Elementor/Jet، منوها، flush، بکاپ)
- 📤 خروجی: `تقریر temp/phase-08…/reports/final-deployment.md`؛ `بسط temp/e2e/run-all-phases-e2e.sh` · **وابستگی:** Task 8.7, Task 8.8, Task 8.9, Task همه فازها
- ✅ 4 شرط پاس · 🛠 4 گام اجرا
## Phase 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets `[PENDING]`
**هدف فاز:** ساخت اسکلت قالب وردپرس با معماری OOP، حذف کامل بloat وردپرس و انتقال یک‌به‌یک دارایی‌های میرور استاتیک
**پوشه:** `temp/phase-01-core-and-assets/` · **تست جامع:** `tests/run-phase-01-tests.sh`

### Task 1.1 — اسکلت پایه قالب و بوت‌استرپ (style.css / functions.php / index.php) `[PENDING]`
- 🎯 فایل‌های ریشه قالب به‌گونه‌ای ساخته شوند که وردپرس قالب را بدون خطا بشناسد و bootstrap مرکزی وجود داشته باشد
- 📤 خروجی: `style.css (هدر استاندارد قالب)`؛ `functions.php (bootstrap)`؛ `index.php (fallback template)` · **شروع‌کننده فاز**
- ✅ 5 شرط پاس · 🛠 5 گام اجرا

### Task 1.2 — Autoloader PSR-4 با Namespace ErfanSanat\Theme و هلپرهای پایه `[PENDING]`
- 🎯 هر کلاسی که در inc/ ساخته می‌شود بدون require دستی لود شود و توابع هلپر مرکزی (es_option، es_template) آماده باشند
- 📤 خروجی: `inc/Autoloader.php`؛ `inc/Helpers.php` · **وابستگی:** Task 1.1
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 1.3 — Theme Supports، ثبت ۴ موقعیت منو و هوک‌های فعال‌سازی `[PENDING]`
- 🎯 قابلیت‌های هسته قالب ثبت شود و ۴ منوی بلوپرینت (main-mega-menu, mobile-menu, tolidat-sherkat, landing) با اسلاگ دقیق ثبت شوند
- 📤 خروجی: `inc/ThemeSetup.php` · **وابستگی:** Task 1.1
- ✅ 5 شرط پاس · 🛠 5 گام اجرا

### Task 1.4 — DeBloat — حذف کامل CSS/JS گوتنبرگ از فرانت‌اند `[PENDING]`
- 🎯 هیچ فایل یا استایل درون‌خطی بلوک‌های وردپرس در فرانت لود نشود؛ بدون تخریب ادیتور ادمین
- 📤 خروجی: `inc/Frontend/DeBloat.php` · **وابستگی:** Task 1.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 1.5 — DeBloat — ایموجی، wp_head cleanup و حذف متاهای زائد `[PENDING]`
- 🎯 پاکسازی wp_head از ایموجی، RSD، WLW، generator، shortlink، REST link، oEmbed و feedهای اضافه برای حداکثر سرعت
- 📤 خروجی: `بسط inc/Frontend/DeBloat.php` · **وابستگی:** Task 1.4
- ✅ 3 شرط پاس · 🛠 6 گام اجرا

### Task 1.6 — انتقال بی‌نقص دارایی‌های میرور به assets/ قالب `[PENDING]`
- 🎯 کپی یک‌به‌یک styles.css، main.js، ۳ فونت Vazirmatn و تصاویر/ویدیو با حفظ ساختار پوشه‌ها تا هیچ مسیر نسبی url(... نشکند
- 📤 خروجی: `assets/css/styles.css`؛ `assets/js/main.js`؛ `assets/fonts/vazirmatn-{arabic,latin,latin-ext}.woff2`؛ `assets/images/*`؛ `assets/media/hero-video.mp4` · **وابستگی:** Task 1.1
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 1.7 — AssetManager — Enqueue سراسری/شرطی با نسخه‌گذاری filemtime و Preload فونت `[PENDING]`
- 🎯 استایل و اسکریپت قالب با نسخه خودکار (کش‌باستینگ) و فونت‌ها با preload صحیح لود شوند؛ زیرساخت شرطی‌سازی per-template آماده فازهای بعد باشد
- 📤 خروجی: `inc/Frontend/AssetManager.php` · **وابستگی:** Task 1.6, Task 1.2
- ✅ 5 شرط پاس · 🛠 6 گام اجرا

### Task 1.8 — زیرساخت تست — WP Stubs و Bootstrap تست‌های واحد (بدون نصب وردپرس) `[PENDING]`
- 🎯 چون تست‌ها روی PHP wasm بدون وردپرس اجرا می‌شوند، یک لایه stub سبک توابع وردپرس لازم است تا کلاس‌های قالب unit-test پذیر شوند
- 📤 خروجی: `temp/harness/wp-stubs.php`؛ `temp/harness/README.md` · **وابستگی:** Task 1.2
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

---
## Phase 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder `[PENDING]`
**هدف فاز:** جایگزین کامل پنل‌های Elementor/JetEngine: داشبورد تب‌دار RTL با ذخیره AJAX، موتور فیلد و Sanitizer اختصاصی و موتور مدیریت سکشن صفحات
**پوشه:** `temp/phase-02-custom-admin-dashboard/` · **تست جامع:** `tests/run-phase-02-tests.sh`

### Task 2.1 — هسته ThemeOptions — منوی پیشخوان، صفحه تب‌دار RTL و فریم‌ورک AJAX امن `[PENDING]`
- 🎯 اسکلت پنل ادمین با امنیت کامل (Nonce + Capability) و ذخیره ایجکس در یک آپشن واحد
- 📤 خروجی: `inc/Admin/ThemeOptions.php`؛ `inc/Admin/AdminAssets.php`؛ `assets/admin/admin.css`؛ `assets/admin/admin.js` · **وابستگی:** Task 1.2
- ✅ 5 شرط پاس · 🛠 6 گام اجرا

### Task 2.2 — موتور فیلدها (FieldRenderer) و Sanitizer اختصاصی `[PENDING]`
- 🎯 کامپوننت‌های رابط ادمین (۸ نوع فیلد + repeater) و sanitize امن هر نوع، قابل استفاده مجدد در تب‌ها و SectionManager
- 📤 خروجی: `inc/Admin/FieldRenderer.php`؛ `inc/Admin/Sanitizer.php`؛ `بسط assets/admin/admin.js (media picker، color، repeater، switch)` · **وابستگی:** Task 2.1
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 2.3 — تب «هدر و لوگو» + نوار اعلان — فیلدها و Fallback دقیق میرور `[PENDING]`
- 🎯 مدیریت لوگو، هدر چسبان، دکمه‌های هدر و نوار اعلان با پیش‌فرض‌هایی که عین میرور است
- 📤 خروجی: `بسط ThemeOptions.php (schema تب header)` · **وابستگی:** Task 2.2
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 2.4 — تب «فوتر، اطلاعات تماس و شبکه‌های اجتماعی» `[PENDING]`
- 🎯 مدیریت کامل محتوای فوتر سه‌ستونه و اطلاعات تماس با پیش‌فرض‌های دقیق JSON-LD میرور
- 📤 خروجی: `بسط ThemeOptions.php (schema تب footer)` · **وابستگی:** Task 2.2
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 2.5 — تب «برند/استایل» + «اسکریپت‌های سفارشی» + «تنظیمات آرشیوها» `[PENDING]`
- 🎯 رنگ‌های برند، تزریق اسکریپت هدر/فوتر کارفرما و تنظیمات لیست‌بندی آرشیوها
- 📤 خروجی: `بسط ThemeOptions.php (سه تب)` · **وابستگی:** Task 2.2
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 2.6 — هلپر نهایی es_option + تزریق تنظیمات به فرانت‌اند `[PENDING]`
- 🎯 اتصال کامل آپشن‌ها به فرانت: توابع رندر آماده برای فاز ۴ به بعد
- 📤 خروجی: `بسط inc/Helpers.php`؛ `inc/Frontend/OptionsOutput.php` · **وابستگی:** Task 2.3, Task 2.4, Task 2.5
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 2.7 — SectionManager — متاباکس، UI لیست سکشن‌ها (toggle + Drag & Drop) `[PENDING]`
- 🎯 اسکلت موتور مدیریت سکشن روی برگه‌ها: ردیف سکشن با کلید روشن/خاموش و جابه‌جایی درگ‌اند‌دراپ
- 📤 خروجی: `inc/Admin/SectionManager.php`؛ `بسط assets/admin/admin.js (sortable داخلی)` · **وابستگی:** Task 1.2
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 2.8 — SectionManager — رندر فیلدهای داینامیک هر سکشن (media/url/repeater) `[PENDING]`
- 🎯 پنل هر سکشن بر اساس schema خودش فیلدها را با FieldRenderer بسازد و ذخیره ساختارمند انجام شود
- 📤 خروجی: `بسط SectionManager.php` · **وابستگی:** Task 2.7, Task 2.2
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 2.9 — SectionManager — API رندر فرانت (es_render_sections) و پیکربندی اولیه صفحات `[PENDING]`
- 🎯 حلقه رندر سکشن‌های فعال به ترتیب ادمین + ثبت schema سکشن‌های home/about/contact/landing (بخش‌های مشترک)؛ سکشن‌های اختصاصی در فازهای ۵ و ۷ اضافه می‌شوند
- 📤 خروجی: `بسط SectionManager.php (رندر)`؛ `inc/Sections/Registry.php (ثبت سکشن‌های صفحات)` · **وابستگی:** Task 2.8
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

---
## Phase 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده `[PASSED]`
**هدف فاز:** ثبت Native داده‌های JetEngine با املای دقیق کلیدها و حفظ ۱۰۰٪ URLها؛ هیچ بایت دیتا از دست نرود
**پوشه:** `temp/phase-03-cpt-and-legacy-meta/` · **تست جامع:** `tests/run-phase-03-tests.sh`

### Task 3.1 — ثبت CPT projects و تکسونومی‌های project_type و city `[PASSED]`
- 🎯 جایگزینی مستقیم رجیستر JetEngine با کد قالب — آرگومان‌ها بایت‌به‌بایت مطابق بلوپرینت
- 📤 خروجی: `inc/Data/PostTypes.php` · **وابستگی:** Task 1.3
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 3.2 — فیلترهای ادمین لیست پروژه‌ها (دراپ‌داون city/project_type + ستون same_key_hrefl) `[PASSED]`
- 🎯 مدیریت راحت پروژه‌ها: فیلتر دراپ‌داونی تکسونومی‌ها و ستون مرتب‌سازی کلید hreflang
- 📤 خروجی: `inc/Data/AdminListFilters.php` · **وابستگی:** Task 3.1
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 3.3 — ثبت CPT services (تصمیم Q2 کارفرما) `[PASSED]`
- 🎯 پست‌تایپ خدمات به‌صورت Native — مطابق تصمیم ثبت‌شده D-005
- 📤 خروجی: `بسط inc/Data/PostTypes.php` · **وابستگی:** Task 1.3
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 3.4 — Rewrite تکسونومی‌های ووکامرس (product_cat با with_front=true) و Flush نهایی `[PASSED]`
- 🎯 حفظ ساختار سئوی فعلی URLها: /product_cat/{term} با with_front=true و flush خودکار هنگام فعال‌سازی قالب
- 📤 خروجی: `inc/Data/WooRewrite.php`؛ `بسط هوک after_switch_theme` · **وابستگی:** Task 3.1, Task 3.3
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 3.5 — متاباکس محصول — تب شناسه‌های صنعتی + Quick Edit `[PASSED]`
- 🎯 مدیریت ۶ شناسه قانونی با املای دقیق دیتابیس + ویرایش سریع از لیست محصولات
- 📤 خروجی: `inc/Admin/ProductMetaboxes.php (هسته + تب identifiers)`؛ `inc/Admin/QuickEdit.php` · **وابستگی:** Task 1.2
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 3.6 — متاباکس محصول — تب عناوین و وضعیت فروش (zireonvan/english_titl/true_sale/before-sale-c) `[PASSED]`
- 🎯 مدیریت زیرعنوان، عنوان انگلیسی (املا خاص)، اطلاعیه قبل از خرید و وضعیت خرید آنلاین/استعلامی
- 📤 خروجی: `بسط ProductMetaboxes.php (تب titles)` · **وابستگی:** Task 3.5
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 3.7 — موتور Repeater سازگار JetEngine (خواندن/نوشتن دو-مد serial/JSON) `[PASSED]`
- 🎯 کلاس مرکزی خواندن و نوشتن فیلدهای تکرارشونده با حفظ دقیق فرمت فعلی دیتابیس ({item-0:{…}})
- 📤 خروجی: `inc/Data/RepeaterEngine.php` · **وابستگی:** Task 1.2
- ✅ 5 شرط پاس · 🛠 5 گام اجرا

### Task 3.8 — متاباکس محصول — Repeaterهای ویژگی‌ها/کاربردها/FAQ با موتور 3.7 `[PASSED]`
- 🎯 چهار فیلد تکرارشونده محصول با UI افزودن/حذف/جابه‌جایی و کلیدهای دقیق دیتابیس
- 📤 خروجی: `بسط ProductMetaboxes.php (تب repeaters)` · **وابستگی:** Task 3.7, Task 2.2
- ✅ 3 شرط پاس · 🛠 6 گام اجرا

### Task 3.9 — متاباکس محصول — گالری (galerry_produ + fallbackها) و فایل کاتالوگ `[PASSED]`
- 🎯 مدیریت گالری سازگار با سه منبع دیتا + دانلود کاتالوگ، با خواندن fallback کامل
- 📤 خروجی: `بسط ProductMetaboxes.php (تب gallery)`؛ `inc/Data/ProductGallery.php` · **وابستگی:** Task 3.5
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 3.10 — متاباکس محصول — روابط (project-ids و usage_countr) `[PASSED]`
- 🎯 انتخاب چندگانه پروژه‌های مرتبط و کاربردها (ترم‌های project_type) با ذخیره آرایه/JSON مطابق ساختار فعلی
- 📤 خروجی: `بسط ProductMetaboxes.php (تب relations)`؛ `inc/Admin/RelatedPicker.php` · **وابستگی:** Task 3.7, Task 3.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 3.11 — متاباکس پروژه — گالری شش‌تایی قدیمی + گالری مدرن همگام `[PASSED]`
- 🎯 مدیریت تصاویر پروژه با کلیدهای تاریخی (second/three/foure/five/six-image) و یک گالری مدرن که با همین کلیدها sync می‌شود
- 📤 خروجی: `inc/Admin/ProjectMetaboxes.php (تب gallery)` · **وابستگی:** Task 1.2
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

### Task 3.12 — متاباکس پروژه — تاریخ تکمیل و محصولات استفاده‌شده `[PASSED]`
- 🎯 مدیریت finish-pro-time (متنی با فرمت شمسی) و انتخاب محصولات پروژه (mortabet_mahsole_2)
- 📤 خروجی: `بسط ProjectMetaboxes.php (تب info)` · **وابستگی:** Task 3.10, Task 3.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 3.13 — متای ترم product_cat — تصویر دسته (thumbnail) و FAQ دسته `[PASSED]`
- 🎯 فرم‌های افزودن/ویرایش دسته محصول با فیلد تصویر و سوالات متداول تکرارشونده (کلیدهای thumbnail و faq)
- 📤 خروجی: `inc/Admin/CategoryTermMeta.php` · **وابستگی:** Task 3.7
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 3.14 — Hreflang سراسری + شمارنده بازدید (number2-viewc) `[PASSED]`
- 🎯 متاباکس ترجمه‌ها روی همه انواع محتوا + ستون مرتب‌سازی + شمارنده بازدید با fallback و ضد-بات
- 📤 خروجی: `inc/Admin/HreflangMeta.php`؛ `inc/Frontend/ViewCounter.php` · **وابستگی:** Task 1.2
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

---
## Phase 04 — هدر، مگامنو، منوی موبایل و فوتر `[PASSED]`
**هدف فاز:** برش دقیق بلوک‌های مشترک ۱۲۸ صفحه به header.php/footer.php با Nav Walkerهای اختصاصی و اتصال به داشبورد — حفظ ۱۰۰٪ DOM و data-hookها
**پوشه:** `temp/phase-04-header-footer-navigation/` · **تست جامع:** `tests/run-phase-04-tests.sh`

### Task 4.1 — استخراج فایل‌های مرجع هدر/فوتر و سند نقشه برش `[PASSED]`
- 🎯 قبل از برش، مرجع ثابت DOM و نقشه بلوک‌ها مستند شود تا تست‌های مقایسه‌ای فاز معیار داشته باشند
- 📤 خروجی: `temp/phase-04-header-footer-navigation/refs/header-ref.html`؛ `temp/phase-04-header-footer-navigation/refs/footer-ref.html`؛ `temp/phase-04-header-footer-navigation/refs/HEADER_MAP.md` · **شروع‌کننده فاز**
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 4.2 — header.php — shell سند، hreflang و فراخوانی‌های استاندارد وردپرس `[PASSED]`
- 🎯 هست استاندارد قالب: زبان/charset/body_class/wp_head/wp_body_open + تگ‌های hreflang داینامیک از متا
- 📤 خروجی: `header.php` · **وابستگی:** Task 4.1, Task 3.14, Task 1.7
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 4.3 — header.php — بلوک هدر بالایی (لوگو، اکشن‌ها، شمارنده سبد) `[PASSED]`
- 🎯 برش دقیق مارک‌آپ هدر بالایی با جایگزینی داینامیک لوگو/لینک‌ها/شمارنده سبد — بدون تغییر DOM
- 📤 خروجی: `بسط header.php`؛ `template-parts/header/header-top.php` · **وابستگی:** Task 4.2, Task 2.3, Task 2.6
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 4.4 — MegaMenuWalker برای main-mega-menu `[PASSED]`
- 🎯 Walker اختصاصی تولید مارک‌آپ مگامنو تب‌دار (پنل محصولات/پروژه‌ها) دقیقاً مطابق میرور با آیتم‌های منوی وردپرس
- 📤 خروجی: `inc/Nav/MegaMenuWalker.php` · **وابستگی:** Task 4.3, Task 1.3
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 4.5 — MobileWalker و سایدبار موبایل (off-canvas) `[PASSED]`
- 🎯 منوی موبایل چندسطحی با آکاردئون (mobile-acc-*) دقیقاً مطابق سایدبار میرور
- 📤 خروجی: `inc/Nav/MobileWalker.php`؛ `template-parts/header/mobile-sidebar.php` · **وابستگی:** Task 4.4
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 4.6 — footer.php — سه ستون، اطلاعات تماس داینامیک و scroll-top `[PASSED]`
- 🎯 برش فوتر با جایگزینی ستون‌ها (منو/دسته‌بندی) و اطلاعات تماس از داشبورد
- 📤 خروجی: `footer.php`؛ `template-parts/footer/footer-main.php` · **وابستگی:** Task 4.1, Task 2.6, Task 2.4
- ✅ 3 شرط پاس · 🛠 6 گام اجرا

### Task 4.7 — Walkerهای tolidat-sherkat و landing `[PASSED]`
- 🎯 دو موقعیت منوی ساده: تولیدات شرکت (فوتر/هدر) و منوی اسکرول لندینگ‌ها
- 📤 خروجی: `inc/Nav/SimpleWalker.php`؛ `inc/Nav/LandingWalker.php` · **وابستگی:** Task 4.4
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 4.8 — تست جامع DOM هدر/فوتر در برابر مرجع استاتیک `[PASSED]`
- 🎯 اسکریپت مقایسه ساختاری: تعداد و ترتیب data-hookها و کلاس‌های کلیدی خروجی رندر شده با ref میرور برابر باشد
- 📤 خروجی: `بسط temp/phase-04…/tests/test-task-08.php (مقایسه DOM با DOMDocument)` · **وابستگی:** Task 4.3, Task 4.5, Task 4.6
- ✅ 3 شرط پاس · 🛠 3 گام اجرا

---
## Phase 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار `[PENDING]`
**هدف فاز:** ۱۴ سکشن index.html → template-parts داینامیک متصل به WP_Query و داشبورد؛ صفر محتوای هاردکد
**پوشه:** `temp/phase-05-homepage-and-sections/` · **تست جامع:** `tests/run-phase-05-tests.sh`

### Task 5.1 — front-page.php + پیکربندی سکشن‌های صفحه اصلی در SectionManager `[PENDING]`
- 🎯 قالب صفحه اصلی با رندر پویای SectionManager و ثبت schema کامل ۱۴ سکشن (ترتیب پیش‌فرض عین میرور)
- 📤 خروجی: `front-page.php`؛ `inc/Sections/HomeSections.php` · **وابستگی:** Task 2.9
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.2 — سکشن هیرو طرح ۱ (hero-home1) — نوتچ، scene switcher و ویدیو `[PENDING]`
- 🎯 برش سکشن #home شامل سوییچر صحنه‌ها و ویدیو درون‌خطی با حفظ همه hookهای JS
- 📤 خروجی: `template-parts/home/section-hero.php` · **وابستگی:** Task 5.1
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 5.3 — سکشن گالری پروژه‌ها (projects) `[PENDING]`
- 🎯 نمایش آخرین/برگزیده پروژه‌ها از WP_Query با کارت استاندارد
- 📤 خروجی: `template-parts/home/section-projects.php` · **وابستگی:** Task 5.1, Task 5.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.4 — سکشن درباره ما + آمار شمارنده `[PENDING]`
- 🎯 برش سکشن‌های #about و معرفی + بلوک آمار با شمارنده انیمیشنی میرور
- 📤 خروجی: `template-parts/home/section-about.php` · **وابستگی:** Task 5.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.5 — سکشن تایم‌لاین تاریخچه (history) `[PENDING]`
- 🎯 تایم‌لاین اسکرولی ۶ مرحله‌ای با progress bar — داده از Repeater
- 📤 خروجی: `template-parts/home/section-history.php` · **وابستگی:** Task 5.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.6 — سکشن دسته‌بندی محصولات (categories) `[PENDING]`
- 🎯 گرید دسته‌های product_cat با تصویر ترم (متای فاز 3.13) و fallback
- 📤 خروجی: `template-parts/home/section-categories.php` · **وابستگی:** Task 5.1, Task 3.13
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 5.7 — سکشن کاتالوگ دانلود + سرویس‌های تب‌دار `[PENDING]`
- 🎯 دو سکشن متنی/تعاملی: دانلود کاتالوگ جامع و تب خدمات با preview میرور
- 📤 خروجی: `template-parts/home/section-catalog.php`؛ `template-parts/home/section-services.php` · **وابستگی:** Task 5.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.8 — سکشن خدمات پس از فروش + مشتریان `[PENDING]`
- 🎯 دو سکشن اعتمادساز: aftersale و لوگوی مشتریان/سازمان‌ها
- 📤 خروجی: `template-parts/home/section-aftersale.php`؛ `template-parts/home/section-customers.php` · **وابستگی:** Task 5.1
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.9 — سکشن ویترین مقالات (blog-preview) `[PENDING]`
- 🎯 آخرین مقالات با WP_Query و کارت مقاله + لینک وبلاگ
- 📤 خروجی: `template-parts/home/section-blog.php` · **وابستگی:** Task 5.1, Task 5.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.10 — سکشن تماس با ما + نقشه کارخانه `[PENDING]`
- 🎯 اطلاعات تماس داینامیک + نقشه (iframe/تصویر) از داشبورد
- 📤 خروجی: `template-parts/home/section-contact.php`؛ `template-parts/home/section-map.php` · **وابستگی:** Task 5.1, Task 2.6
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 5.11 — کارت‌های استاندارد: card-product / card-project / card-post `[PENDING]`
- 🎯 سه قالب کارت مشترک مطابق کارت‌های میرور — قابل استفاده در همه آرشیوها و سکشن‌ها
- 📤 خروجی: `template-parts/cards/card-product.php`؛ `template-parts/cards/card-project.php`؛ `template-parts/cards/card-post.php` · **وابستگی:** Task 1.2
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 5.12 — تست جامع رندر صفحه اصلی (مقایسه با index.html و سکشن‌های toggle) `[PENDING]`
- 🎯 رندر کامل front-page در حالت پیش‌فرض = ساختار میرور؛ تغییر ترتیب/حذف از ادمین اثر واقعی داشته باشد
- 📤 خروجی: `بسط temp/phase-05…/tests/test-task-12.php` · **وابستگی:** Task 5.2 … 5.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

---
## Phase 06 — ووکامرس: محصول تکی، آرشیوها، سبد و پرداخت `[PENDING]`
**هدف فاز:** تبدیل صفحات ووکامرس با پوسته دقیق میرور؛ منطق true_sale و سازگاری کامل افزونه‌های فعال
**پوشه:** `temp/phase-06-woocommerce-and-products/` · **تست جامع:** `tests/run-phase-06-tests.sh`

### Task 6.1 — Wrapper ووکامرس و پاکسازی پیش‌فرض‌های آن `[PENDING]`
- 🎯 صفحات ووکامرس داخل shell قالب رندر شوند و خروجی‌های پیش‌فرض تکراری (breadcrumb/سایدبار/related) حذف شوند
- 📤 خروجی: `woocommerce.php`؛ `inc/Woo/Setup.php` · **وابستگی:** Task 4.2, Task 4.6
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.2 — single-product.php — هیرو محصول و گالری لایت‌باکس میرور `[PENDING]`
- 🎯 هیرو (breadcrumb/H1/زیرعنوان/badges) و گالری دقیق میرور متصل به منابع گالری چندگانه
- 📤 خروجی: `single-product.php`؛ `template-parts/single-product/hero.php`؛ `template-parts/single-product/gallery.php` · **وابستگی:** Task 6.1, Task 3.9
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.3 — single-product — تب‌های مشخصات و جدول ۱۸ ویژگی فنی (pa_*) `[PENDING]`
- 🎯 تب‌بندی prod-tab میرور با جدول کامل اتریبیوت‌ها + شناسه‌های قانونی
- 📤 خروجی: `template-parts/single-product/specs-tabs.php` · **وابستگی:** Task 6.2, Task 3.5
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.4 — single-product — Repeaterها، FAQ، کاتالوگ و اطلاعیه قبل از خرید `[PENDING]`
- 🎯 رندر product-feature، feature2 دو بخشی، usage_product/usage_countr، faq آکاردئونی، دکمه کاتالوگ و نوار اطلاعیه
- 📤 خروجی: `template-parts/single-product/features.php`؛ `template-parts/single-product/usages.php`؛ `template-parts/single-product/faq.php`؛ `template-parts/single-product/notice.php` · **وابستگی:** Task 6.2, Task 3.8
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 6.5 — single-product — ناحیه خرید: true_sale، تعداد (min/step)، سوئچ‌ها و toast `[PENDING]`
- 🎯 منطق دوگانه خرید آنلاین/استعلامی + سازگاری WPC Quantity و Variation Swatches با مارک‌آپ pdp-* میرور
- 📤 خروجی: `template-parts/single-product/purchase.php`؛ `inc/Woo/PurchaseFlow.php` · **وابستگی:** Task 6.2, Task 3.6
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.6 — single-product — پروژه‌های مرتبط، محصولات مرتبط و دیدگاه‌ها `[PENDING]`
- 🎯 اتصال روابط متا + دیدگاه‌های ووکامرس با مارک‌آپ review میرور
- 📤 خروجی: `template-parts/single-product/related-projects.php`؛ `template-parts/single-product/related-products.php`؛ `template-parts/single-product/reviews.php` · **وابستگی:** Task 6.2, Task 3.10
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.7 — archive-product (shop) — هیرو، نوار فیلتر، گرید و صفحه‌بندی `[PENDING]`
- 🎯 آرشیو محصولات دقیقاً مطابق shop.html با فیلترهای GET کارا تا رسیدن فاز ۸
- 📤 خروجی: `archive-product.php`؛ `template-parts/archive/product-filters.php`؛ `template-parts/archive/pagination.php` · **وابستگی:** Task 6.1, Task 5.11
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 6.8 — taxonomy-product_cat (+FAQ دسته) و taxonomy-product_tag `[PENDING]`
- 🎯 آرشیو دسته با هیرو/توضیح/FAQ و آرشیو برچسب مطابق میرور
- 📤 خروجی: `taxonomy-product_cat.php`؛ `taxonomy-product_tag.php` · **وابستگی:** Task 6.7, Task 3.13
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.9 — سبد خرید ووکامرس با پوسته cart.html `[PENDING]`
- 🎯 جدول سبد، تغییر تعداد و خلاصه — مارک‌آپ میرور با هوک‌های استاندارد ووکامرس
- 📤 خروجی: `woocommerce/cart/cart.php (override)`؛ `بسط inc/Woo/Setup.php` · **وابستگی:** Task 6.1
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 6.10 — checkout، فیلدهای شخص/شرکت و صفحه دریافت فاکتور (thank-you) `[PENDING]`
- 🎯 پوسته checkout میرور + toggle نوع خریدار + فیلدهای رسید (کد پستی/شناسه ملی) + thank-you مطابق checkout-receipt
- 📤 خروجی: `woocommerce/checkout/*.php overrides (form-checkout.php، thankyou.php)`؛ `inc/Woo/CheckoutFields.php` · **وابستگی:** Task 6.9
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 6.11 — حساب کاربری (my-account) و صفحه auth (ورود پیامکی voorodak) `[PENDING]`
- 🎯 پوسته یکدست صفحات حساب کاربری ووکامرس و هماهنگی با افزونه ورود پیامکی
- 📤 خروجی: `woocommerce/myaccount/*.php overrides (سبک)`؛ `assets/css/wc-forms-override.css (یا در styles میرور enqueue شرطی)` · **وابستگی:** Task 6.10
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

---
## Phase 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها `[PENDING]`
**هدف فاز:** قالب‌های سینگل/آرشیو پروژه‌ها، وبلاگ کامل (TOC/بازدید/اشتراک)، برگه‌های اختصاصی قابل ویرایش و لندینگ‌ها
**پوشه:** `temp/phase-07-projects-blog-and-pages/` · **تست جامع:** `tests/run-phase-07-tests.sh`

### Task 7.1 — single-projects.php — سینگل کامل پروژه `[PENDING]`
- 🎯 هیرو، گالری شش‌تایی، مشخصات (تاریخ/شهر/نوع)، بدنه، محصولات پروژه و پروژه‌های مرتبط
- 📤 خروجی: `single-projects.php`؛ `template-parts/single-project/*.php` · **وابستگی:** Task 3.11, Task 3.12, Task 5.11
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 7.2 — archive-projects.php — آرشیو پروژه‌ها با فیلترها `[PENDING]`
- 🎯 آرشیو مطابق projects.html: هیرو، فیلتر نوع/شهر/مرتب‌سازی، گرید و صفحه‌بندی
- 📤 خروجی: `archive-projects.php`؛ `template-parts/archive/project-filters.php` · **وابستگی:** Task 5.11
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 7.3 — taxonomy-project_type.php و taxonomy-city.php `[PENDING]`
- 🎯 آرشیو ترم‌های پروژه مطابق میرور city/*.html و project_type/*.html
- 📤 خروجی: `taxonomy-project_type.php`؛ `taxonomy-city.php` · **وابستگی:** Task 7.2
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 7.4 — home.php / archive.php / category.php — وبلاگ و فیلتر دسته `[PENDING]`
- 🎯 آرشیو مقالات مطابق blog.html و category/magazine.html با فیلتر دسته کارا
- 📤 خروجی: `home.php`؛ `archive.php`؛ `category.php` · **وابستگی:** Task 5.11
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 7.5 — single.php — مقاله کامل با TOC، اشتراک، بازدید و مرتبط `[PENDING]`
- 🎯 سینگل مقاله مطابق blog/*.html با فهرست خودکار از سرفصل‌ها و اتصال شمارنده بازدید
- 📤 خروجی: `single.php`؛ `inc/Frontend/TOC.php` · **وابستگی:** Task 7.4, Task 3.14
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 7.6 — search.php و 404.php `[PENDING]`
- 🎯 نتایج جستجوی ترکیبی با کارت‌های استاندارد + صفحه ۴۰۴ مطابق سبک میرور
- 📤 خروجی: `search.php`؛ `404.php`؛ `searchform.php` · **وابستگی:** Task 5.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 7.7 — قالب برگه‌های about و contact (با SectionManager) `[PENDING]`
- 🎯 دو قالب برگه با سکشن‌های کاملاً قابل ویرایش از ادمین — پیش‌فرض عین میرور
- 📤 خروجی: `page-templates/page-about.php`؛ `page-templates/page-contact.php`؛ `inc/Sections/AboutSections.php`؛ `inc/Sections/ContactSections.php` · **وابستگی:** Task 2.9, Task 8.5 (اسکلت فرم)
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 7.8 — قالب برگه‌های licenses، faq، refund_returns و recruitment `[PENDING]`
- 🎯 چهار قالب برگه اختصاصی با Repeaterهای قابل ویرایش و فرم استخدام
- 📤 خروجی: `page-templates/page-licenses.php`؛ `page-templates/page-faq.php`؛ `page-templates/page-refund.php`؛ `page-templates/page-recruitment.php`؛ `inc/Sections/*.php مربوطه` · **وابستگی:** Task 2.9
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 7.9 — خدمات: single-services.php + فهرست خدمات + card-service `[PENDING]`
- 🎯 قالب‌های CPT services مطابق سینگل‌ها و services.html میرور
- 📤 خروجی: `single-services.php`؛ `page-templates/page-services.php (فهرست)`؛ `template-parts/cards/card-service.php` · **وابستگی:** Task 3.3, Task 5.11
- ✅ 3 شرط پاس · 🛠 4 گام اجرا

### Task 7.10 — لندینگ‌ها: arya و smartelement + منوی landing `[PENDING]`
- 🎯 دو لندینگ اسکرولی از کامپوننت‌های میرور (تصمیم Q3) با منوی landing ثابت
- 📤 خروجی: `page-templates/page-landing.php`؛ `inc/Sections/LandingSections.php` · **وابستگی:** Task 4.7, Task 2.9
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

---
## Phase 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی `[PENDING]`
**هدف فاز:** موتور جستجو/فیلتر ایجکس سریع و امن، سیستم فرم‌ها با مدیریت ادمین، ممیزی سئو/پرفورمنس/امنیت و آمادگی استقرار
**پوشه:** `temp/phase-08-ajax-filters-and-audit/` · **تست جامع:** `tests/run-phase-08-tests.sh`

### Task 8.1 — زیرساخت AJAX امن (Router + Nonce + Rate Limit + Response استاندارد) `[PENDING]`
- 🎯 پایه مشترک همه سرویس‌های ایجکس با امنیت کامل و قالب پاسخ ثابت
- 📤 خروجی: `inc/Ajax/Router.php` · **وابستگی:** Task 1.7
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 8.2 — جستجوی زنده (Live Search) جایگزین JetSearch `[PENDING]`
- 🎯 جستجوی اینستنت محصولات/پروژه‌ها/مقالات در مودال جستجوی هدر با رندر partial سمت سرور
- 📤 خروجی: `inc/Ajax/LiveSearch.php`؛ `assets/js/live-search.js (نازک روی hookهای میرور)` · **وابستگی:** Task 8.1
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 8.3 — فیلتر هوشمند محصولات (جایگزین JetSmartFilters) `[PENDING]`
- 🎯 فیلتر ایجکس گرید آرشیو محصولات: متن/دسته/قیمت/وضعیت خرید/IP/ولتاژ/مرتب‌سازی/تعداد — بدون رفرش
- 📤 خروجی: `inc/Ajax/ProductFilters.php`؛ `بسط live-search.js یا assets/js/product-filters.js` · **وابستگی:** Task 8.1, Task 6.7
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 8.4 — فیلتر هوشمند پروژه‌ها (type/city + شمارنده) `[PENDING]`
- 🎯 فیلتر ایجکس آرشیو پروژه‌ها مطابق بلوپرینت با شمارنده تعداد هر ترم
- 📤 خروجی: `inc/Ajax/ProjectFilters.php` · **وابستگی:** Task 8.1, Task 7.2
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 8.5 — سیستم فرم‌ها — Backend (ثبت امن + مدیریت ادمین + ایمیل) `[PENDING]`
- 🎯 موتور ثبت پیام‌های فرم‌ها: CPT داخلی، Honeypot/Nonce/Rate limit، لیست ادمین و اطلاع ایمیل
- 📤 خروجی: `inc/Forms/Manager.php`؛ `inc/Forms/AdminList.php` · **وابستگی:** Task 8.1
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 8.6 — فرم‌های فرانت: تماس، استعلام قیمت، استخدام، بازخورد `[PENDING]`
- 🎯 اتصال چهار فرم میرور به Backend با پیام‌های موفقیت/خطای میرور
- 📤 خروجی: `assets/js/forms.js`؛ `template-parts/forms/*.php (۴ فرم)` · **وابستگی:** Task 8.5, Task 6.5, Task 7.7, Task 7.8
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 8.7 — ممیزی سئو: JSON-LD داینامیک، Hreflang و سازگاری SEOPress `[PENDING]`
- 🎯 اسکیماهای ساختاریافته داینامیک + hreflang بدون تکرار + پشتیبانی متاهای قدیمی rank_math برای SEOPress
- 📤 خروجی: `inc/SEO/Schema.php`؛ `inc/SEO/Hreflang.php` · **وابستگی:** Task 3.14, Task 6.2
- ✅ 4 شرط پاس · 🛠 6 گام اجرا

### Task 8.8 — ممیزی پرفورمنس و DeBloat نهایی `[PENDING]`
- 🎯 تأیید بارگذاری حداقلی: هیچ CSS/JS بloat، فونت preload، lazy تصاویر، سازگاری کش LiteSpeed
- 📤 خروجی: `تقریر ممیزی temp/phase-08…/reports/performance-audit.md`؛ `بسط‌های ریز AssetManager/DeBloat در صورت نیاز` · **وابستگی:** Task همه فازهای فرانت
- ✅ 4 شرط پاس · 🛠 5 گام اجرا

### Task 8.9 — ممیزی امنیت و i18n (Escaping/Sanitize/Text Domain) `[PENDING]`
- 🎯 سوییپ نهایی امنیت خروجی/ورودی و یکدستی text domain و ترجمه‌پذیری
- 📤 خروجی: `تقریر temp/phase-08…/reports/security-i18n-audit.md`؛ `اصلاحات نقطه‌ای` · **وابستگی:** Task همه فازها
- ✅ 3 شرط پاس · 🛠 5 گام اجرا

### Task 8.10 — سوئیت E2E کل پروژه + چک‌لیست استقرار روی سایت زنده `[PENDING]`
- 🎯 اجرای run-all-phases-e2e با پاس ۱۰۰٪ + مستند استقرار مرحله‌به‌مرحله (غیرفعال‌سازی Elementor/Jet، منوها، flush، بکاپ)
- 📤 خروجی: `تقریر temp/phase-08…/reports/final-deployment.md`؛ `بسط temp/e2e/run-all-phases-e2e.sh` · **وابستگی:** Task 8.7, Task 8.8, Task 8.9, Task همه فازها
- ✅ 4 شرط پاس · 🛠 4 گام اجرا

