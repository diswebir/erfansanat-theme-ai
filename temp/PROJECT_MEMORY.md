# PROJECT_MEMORY — حافظه زنده پروژه (Active State)

> آخرین به‌روزرسانی: 2026-11-03 · قبل از هر تسک بخوان، بعد از هر تسک آپدیت کن.

## وضعیت فعلی: **PHASE 2 PASSED** — همه ۱۸ تسک فاز ۲ پاس شدند.
```
PHASE 2 PASSED — P02-T001..T018 PASSED (260 checks) — WAITING FOR USER APPROVAL FOR PHASE 3
```
- **فاز ۲ کامل شد (داشبورد اختصاصی + Section Builder):** Schema/Store آپشن‌ها (`erfansanat_theme_options`)، `es_option()` با کش، منوی ادمین + AJAX امن (nonce/cap)، UI تب‌بندی RTL/ARIA، FieldRenderer (پایه/media/link/icon/repeater)، Sanitizer per-type، تب‌های Header/MegaMenu/Footer/Brand/Archive، و Section Builder کامل: `Sections\Schema` (۱۳ سکشن خانه + about/contact/arya)، `SectionManager` (متای نسخه‌دار `_es_sections_v1`، ترتیب/فعال/شناسه یکتا، ویرایش فیلد schema-only)، `Admin\SectionMetabox` (drag&drop + AJAX امن)، `Sections\Renderer` (رندر ترتیبی enabled-only + کش + fallback امن).
- نتیجه runner فاز ۲: `PHASE RESULT: ALL PASS` (۱۸ تست، **۲۶۰ چک پاس، ۰ fail/pending**).
- شمارش چک هر تسک: T001=16، T002=10، T003=9، T004=24، T005=30، T006=19، T007=18، T008=13، T009=24، T010=11، T011=7، T012=10، T013=10، T014=9، T015=14، T016=12، T017=14، T018=10.
- **فاز ۱ کامل شد:** bootstrap/constants، Autoloader، Theme supports، fallback index، DeBloat، انتقال CSS/فونت/JS/تصاویر/ویدیو، AssetManager نسخه‌گذاری/enqueue شرطی، بسته‌بندی zip.
- نتیجه runner فاز ۱: `PHASE RESULT: PASS` (۱۲ تست، ۰ fail/pending). HTTP asset check: 27/27 OK. zip فقط `erfansanat/`.
- Stage 0 ✅ (اسکن + هارنس + ۴ فایل حافظه) — کاربر به همه ۷ سوال پاسخ داد.
- Stage 1 ✅ نسخه اول: ۸ فاز / ۲۲ تسک.
- **Stage 1 بازطراحی اتمیک ✅ طبق درخواست کاربر:** ۸ فاز / **۱۷۵ تسک اتمیک** با وابستگی، فایل هدف، منبع، کلید داده، شروط پذیرش AC و سناریوی تست SC.
- شمارش فازها: P1=12، P2=18، P3=24، P4=20، P5=21، P6=28، P7=28، P8=24.
- نگاشت تسک‌های قبلی: `temp/LEGACY_TASK_MAP.md` · فهرست: `temp/ATOMIC_TASK_INDEX.md` · برنامه: `temp/MASTER_PLAN.md`.
- کاربر شروع Phase 1 را تایید کرد. **P01-T001 پاس شد**: `erfansanat/style.css` + حداقل `erfansanat/index.php` ساخته شد؛ تست 12/12 پاس. CSS استاتیک هنوز کپی نشده.
- اصلاح حین اجرا: WP پوسته کلاسیک بدون `index.php` خطای `theme_no_index` می‌دهد؛ بنابراین P01-T001 شامل حداقل index.php شد. fallback کامل در P01-T005 می‌ماند.

## پاسخ‌های کاربر به سوالات Stage 0 (ثبت قطعی — 2026-11-03)
1. **صفحات غایب** (`arya`, `my-account`, `auth`, `search`, `404`): ✅ ساخته شوند با همان design system.
2. **خدمات:** ✅ `services.html` = برگه `smartelement` (11984). ۵ تک‌خدمت → **CPT بومی `services`** (پیش‌فرضِ گزینه اول؛ اسلاگ `services`، آرشیو مستقل ندارد — آرشیو همان برگه smartelement).
3. **Aliasها:** ساختار اصلی حفظ می‌شود. ⚠️ نکته حیاتی کاربر: در سایت لایو اسلاگ ترم‌ها **فارسی** است (مثال: `https://erfansanat.com/city/اصفهان/`). پس قالب‌ها باید **slug-agnostic** باشند؛ اسلاگ‌های انگلیسی میرور فقط مرجع DOM هستند. مسیرهای قدیمی → **ریدایرکت 301** به ساختار اصلی (نگاشت اسلاگ هنگام استقرار از دیتابیس لایو).
4. **برگه `project` (8366):** ✅ همان آرشیو پروژه‌ها → **ریدایرکت 301 به `/projects/`**.
5. **هیرو صفحه اصلی:** ✅ در تنظیمات قالب قابل تنظیم؛ **ویدیوی اصلی جداگانه** (Media) و **بقیه اسلایدها از بین پروژه‌ها انتخاب‌شدنی** (Repeater آیدی پروژه).
6. **وبلاگ:** ✅ TOC + بخش دیدگاه **با همان سبک طراحی** اضافه شود (مجوز طراحی جدید فقط برای این دو کامپوننت).
7. **تست ووکامرس:** ✅ اوکی — تلاش از گیت‌هاب؛ در صورت عدم امکان، shim/stub + نهایی‌سازی روی استیجینگ.

## تصمیمات فنی (تاکنون)
- **D8 — محل سورس قالب:** پوشه `erfansanat/` در ریشه ریپو (نه خود ریشه). دلیل: ریشه شامل `static-mirror/` و `temp/` است که نباید در zip قالب بروند. یک mu-plugin در محیط تست، ریشه ریپو را به‌عنوان theme directory ثبت می‌کند (`register_theme_directory`) تا قالب بدون کپی کشف شود. بیلد/زیپ نهایی فقط `erfansanat/` را بسته‌بندی می‌کند.
- محیط تست WP 7.1.4 + PHP 8.4.27 (WASM) + SQLite در `temp/wp/` (gitignore). بازسازی: `bash temp/tools/setup-harness.sh`.
- `static-mirror/` در Git (مرجع طراحی + تست DOM). سنگین‌ها (`temp/wp`, `node_modules`, `bin`) gitignore.
- **ووکامرس:** دانلود zip بیلدشده از release گیت‌هاب ممکن نشد (asset به هاست مسدود `objects.githubusercontent.com` ریدایرکت می‌شود؛ TLS EOF). → فاز ۶ با **shim سازگاری** (ثبت `product`/`product_cat`/`product_tag` + stub توابع WC موردنیاز قالب) تست می‌شود؛ نهایی‌سازی روی استیجینگ واقعی.
- تست‌ها: plain-PHP با `temp/tests/bootstrap.php` (توابع `es_check/es_file/es_contains/es_load_wp/es_activate_theme/es_summary`). اجرا: `php temp/phase-XX/tests/test-task-YY.php`.

## گام بعدی (پس از تایید کاربر)
**PHASE 3** — طبق `temp/MASTER_PLAN.md` (۲۴ تسک اتمیک). فاز ۲ کامل پاس شد (۲۶۰ چک)؛ در انتظار تایید کاربر برای شروع فاز ۳.
- نکته معماری فاز ۲ برای فازهای بعد: template-partهای سکشن‌ها هنوز slice نشده‌اند (فاز ۵/۷)؛ `Sections\Renderer` تا آن زمان fallback ساختاری امن می‌دهد. مسیر قرارداد: `template-parts/sections/{context}/section-{type}.php`. هر سکشن `{id,type,enabled,order,fields}`. کش رندر با اکشن `erfansanat_sections_updated` invalid می‌شود.
- `SectionManager::save_section_fields` فقط کلیدهای schema را نگه می‌دارد؛ مقدار خالی فیلد را حذف نمی‌کند؛ media/link/icon با `Sanitizer::sanitize_field` پاک‌سازی می‌شوند.

## نکات یادآوری برای تسک‌های آینده
- املای تاریخی متاها مقدس است: `foure-image`, `galerry_produ`, `english_titl`, `vaule_of_variable`, `usage_countr`, `mortabet_mahsole_2`, `number2-viewc`.
- `product_cat` اسلاگ واقعی (نه `product-category`)؛ `with_front=true` فقط همین تکسونومی.
- اسلاگ ترم‌های لایو فارسی است → هیچ کوئری/لینکی نباید به اسلاگ انگلیسی میرور هاردکد شود.
- هدر/فوتر تماماً `data-*` + کلاس Tailwind (بدون id) → Walkerها همان DOM را تولید کنند.
- کارت‌های آرشیو `data-product-*`/`data-project-*` را داینامیک پر کنند.
- هیرو: ویدیوی اصلی جدا + اسلایدهای انتخابی از پروژه‌ها (D5).
- وبلاگ: TOC + دیدگاه با همان سبک (D6). برگه `project` → 301 به `/projects/` (D4).
- هر تسک = ۶ گام + کامیت روی برنچ `arena/7378effd-erfansanat-theme-ai`.
