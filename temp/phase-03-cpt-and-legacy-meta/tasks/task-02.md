# Task 3.2: متاباکس کامل محصول و Round-trip متاهای JetEngine

[PASSED]

## هدف و برنامه فنی
پشتیبانی همه کلیدهای محصول، گالری fallback، روابط و repeaterها؛ تشخیص فرمت خام و حفظ کلیدهای دارای غلط املای تاریخی.

## فایل‌های هدف
- `inc/Admin/ProductMetaboxes.php`
- `inc/Data/LegacyMeta.php`

## وابستگی‌ها
- 3.1

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [x] همه کلیدهای محصول در DATA_BLUEPRINT پوشش داده شوند.
- [x] JSON، آرایه سریالایز‌شده و مقدار خالی round-trip شوند.
- [x] گالری از کلیدهای قدیمی fallback بخواند.
- [x] محصول استعلامی و قابل خرید درست تفکیک شوند.
- [x] Quick Edit با nonce/capability و sanitization امن باشد.

## آزمون
- `temp/phase-03-cpt-and-legacy-meta/tests/test-task-02.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.

## نتیجه اجرا — 2026-11-04

- PHPUnit: **۱۲ تست / ۹۵ assertion** (`TaskP03T02Test`). HTTP: **۱۳/۱۳** (`check-http.py --task 3.2`).
- `inc/Data/LegacyMeta.php`: تشخیص فرمت (`json|serialized|comma|array|scalar|empty`)، خواندن رابطه‌ها به‌صورت شناسهٔ عددی مرتب و بی‌تکرار، repeater با دو شکل پذیرش (`item-0` جت‌انجین و لیست ساده)، گالری محصول با زنجیرهٔ fallback (`galerry_produ` → `_product_image_gallery` → `gallery-filed` → `image-2..6`)، گالری پروژه روی پنج شکاف تاریخی، شمارندهٔ بازدید با fallback `number-viewc`، و تشخیص `true_sale`.
- `inc/Admin/ProductMetaboxes.php`: متاباکس کامل (شناسه‌ها، عنوان‌ها، وضعیت فروش، کاتالوگ، گالری، روابط، چهار repeater)، تب ووکامرس برای `before-sale-c`، ستون «نوع فروش»، Quick Edit با nonce و `edit_post` جداگانه.
- **باگ بحرانی که آزمون گرفت:** `update_metadata()` ورودی را unslash می‌کند؛ نوشتن JSON بدون `wp_slash()` باعث می‌شد `\u0648` به متن `u0648` تبدیل شود و **کل متن فارسی در دیتابیس خراب** شود. همهٔ مسیرهای نوشتن به `wp_slash()` مجهز شدند و آزمون اختصاصی `testJsonEscapesSurviveTheWritePath` دارد.
- اصلاح‌های دیگر: `absint('-3')` به ۳ تبدیل می‌شد و شناسهٔ منفی را معتبر می‌کرد (اکنون فقط عدد مثبت پذیرفته می‌شود)؛ مقدار خالی کلید را حذف می‌کند تا «غایب» و «خالی» قابل تفکیک بمانند؛ گالری حذف‌شده تصویر معیوب تولید نمی‌کند.
- محدودیت: `product-feature`/`usage_product` فقط در محیط محلی با فرمت جت‌انجین آزموده شده‌اند؛ هیچ نمونهٔ واقعی از دیتابیس زنده در دست نبود، بنابراین «حفظ فرمت» بر پایهٔ قرارداد بلوپرینت است نه مشاهدهٔ داده واقعی.
