# Task 3.3: متای پروژه، ترم، ترجمه و بازدید

[PASSED]

## هدف و برنامه فنی
متاباکس‌های بومی برای گالری پروژه، تاریخ و روابط؛ FAQ/تصویر دسته؛ hreflang و شمارنده بازدید با fallback.

## فایل‌های هدف
- `inc/Admin/ProjectMetaboxes.php`
- `inc/Admin/TermMetaboxes.php`
- `inc/Admin/ContentMetaboxes.php`

## وابستگی‌ها
- 3.2

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [x] خواندن/نوشتن تمام کلیدهای پروژه و ترم پاس شود.
- [x] foure-image و mortabet_mahsole_2 دقیقاً حفظ شوند.
- [x] hrefmeta_fa/en/ar روی محتوا و ترم کار کند.
- [x] number2-viewc با fallback عددی از number-viewc خوانده شود.
- [x] داده سایر افزونه‌ها هنگام ذخیره حذف نشود.

## آزمون
- `temp/phase-03-cpt-and-legacy-meta/tests/test-task-03.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.

## نتیجه اجرا — 2026-11-04

- PHPUnit: **۱۲ تست / ۱۲۹ assertion** (`TaskP03T03Test`). HTTP: **۹/۹** (کل فاز ۳: **۳۷/۳۷**).
- `inc/Admin/ProjectMetaboxes.php`: پنج شکاف گالری تاریخی (`second/three/foure/five/six-image`)، تاریخ تکمیل شمسی با نرمال‌سازی ارقام فارسی و رد تاریخ نامعتبر، و `mortabet_mahsole_2` به‌صورت آرایهٔ JSON شناسه.
- `inc/Admin/TermMetaboxes.php`: `thumbnail` و `faq` روی `product_cat`/`project_type`/`city`، ستون تصویر در فهرست دسته‌ها، nonce و مجوز ترم اختصاصی.
- `inc/Admin/ContentMetaboxes.php`: `hrefmeta_fa/en/ar` + `same_key_hrefl` روی نوشته/برگه/محصول/پروژه، شمارندهٔ بازدید با fallback `number-viewc`، و بازدیدکنندهٔ وارد‌شده با مجوز ویرایش شمرده نمی‌شود.
- **باگ واقعی:** کلید `number2-viewc` به‌عنوان `integer` ثبت شده بود و `update_post_meta` مقدار خراب را به `"0"` نرمال می‌کرد؛ در نتیجه fallback روی `number-viewc` هرگز فعال نمی‌شد. نوع ثبت‌شده به `string` تغییر کرد تا دادهٔ خراب قابل تشخیص بماند و آزمون `number2-viewc` خراب → `17` از کلید قدیمی را قفل می‌کند.
- باگ‌های دیگر: `increment_views` مقدار fallback را در کلید جدید می‌نوشت (اکنون از مقدار جدید/قدیمی درست ادامه می‌دهد و کلید قدیمی را بازنویسی نمی‌کند)؛ repeater ترم بدون زیرفیلد `answer_question` هشدار PHP می‌داد.
- امنیت: بدون nonce/مجوز هیچ نوشتنی انجام نمی‌شود (آزمون contributor)، `javascript:` رد می‌شود، و متاهای افزونه‌های دیگر (`_rank_math_*`، `_seopress_*`، `_yoast_*`، `_jet_engine_*`، `_product_attributes`) دست‌نخورده می‌مانند.
- hreflang: قالب عمداً هیچ تگ `<link rel="alternate" hreflang>` چاپ نمی‌کند تا با SEOPress رقابت نکند؛ آزمون این را قفل می‌کند.
- محدودیت: نمایش واقعی متاباکس‌ها و Media Library در مرورگر آزمون نشده؛ همهٔ آزمون‌ها روی SQLite محلی و دادهٔ ساختگی هستند.
