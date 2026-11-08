# Task 4.2: Walkerهای مگامنو، موبایل، تولیدات و لندینگ

[PASSED]

## هدف و برنامه فنی
چهار محل منوی وردپرس با ساختار تب‌ها و زیرمنوهای مرجع؛ داده واقعی ترم‌ها و حالت صفحه‌کلید/موبایل.

## فایل‌های هدف
- `inc/Navigation/`
- `template-parts/navigation/`

## وابستگی‌ها
- 4.1

## مرجع و داده
- `temp/STATIC_TEMPLATE_MAP.md` و سکشن متناظر در `static-mirror/`.
- `temp/DATA_BLUEPRINT.md`؛ داده ساختگی فقط در محیط محلی و بدون جایگزینی داده زنده.

## شروط پاس شدن
- [x] چهار menu location ثبت شوند.
- [x] مارک‌آپ walker با مرجع مقایسه شود.
- [x] مگامنو، آکاردئون موبایل و focus/escape کار کنند.
- [x] منوی خالی باعث Fatal یا لینک ساختگی نشود.

## آزمون
- `temp/phase-04-header-footer-navigation/tests/test-task-02.php`
- علاوه بر contract خودکار، حالت‌های خالی/خطا، دسترسی غیرمجاز، موبایل و داده قدیمی متناسب با تسک اضافه شود.
- نتیجه واقعی و محدودیت‌ها در `temp/QA_REFLECTION_LOG.md` ثبت شود.

## خروج از تسک
به‌روزرسانی PROJECT_MEMORY و MASTER_PLAN، گزارش تست و commit مستقل؛ سپس دریافت تأیید طبق دروازه کاربر.

## نتیجه اجرا

- `temp/phase-04-header-footer-navigation/tests/test-task-02.php`: **11 تست / 100 assertion، بدون warning**.
- `check-http.py --task 4.2`: **۲۴ بررسی HTTP**.
- PHPCS تمیز.

### چهار محل منو
`main-mega-menu`، `mobile-menu`، `tolidat-sherkat`، `landing` (هر چهار از قبل در `Theme.php` ثبت بودند؛ حالا آزمون دارد).

### داده بلوک‌ها از ترم‌های واقعی
| بلوک | منبع | شمارش در فیکسچر |
|---|---|---|
| products | `product_cat` والدها + فرزندان | ۴ تب × تا ۸ آیتم |
| projects | `project_type` و `city` | ۲ تب `type`/`city` |
| services | CPT `services` | تا ۸ |
| corporate | برگه‌های `about`/`licenses-and-honors`/`faq`/`recruitment`/`contact` | ۵ (هر ۵ موجود) |

### سه نقص واقعی که آزمون‌ها گرفتند
1. تریگرهای مگامنو `aria-controls` نداشتند → ID پنل و `aria-controls` اضافه شد (بررسی «هیچ `aria-controls` معلقی نباشد» تصویب می‌کند).
2. `MegaMenu::render_block()` برای نام بلوک ناشناخته یک پوسته خالی و فوکوس‌پذیر چاپ می‌کرد → حالا فقط چهار بلوک مرجع را می‌پذیرد.
3. ریلی تب‌های موبایل/دسکتاپ (`data-mega-proj-tab`، `data-mobile-prod-tab`، `data-mobile-proj-tab` و پنل‌های متناظر) ساخته نشده بود؛ بدون آن‌ها JS منتقل‌شده بی‌اثر بود → هر هشت هوک با شمارش مرجع (۴/۴/۲/۲) بازسازی شد.

### امنیت و حالت خالی
- عنوان/URL آیتم‌های منو escape می‌شوند و `javascript:` حذف می‌شود (آزمون اختصاصی).
- منوی خالی/یتیم/حلقه‌دار باعث Fatal نمی‌شود و هم‌نیاها را از دست نمی‌دهد.
- corporate هرگز به برگه ناموجود لینک نمی‌دهد.
