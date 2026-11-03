# Task 4.2 — Nav Walker مگامنو + مدیریت از Appearance → Menus

**فاز:** 4 — هدر، مگامنو، منوی موبایل و فوتر  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 4.1

## هدف

Walker اختصاصی که مارک‌آپ مگامنو (تب محصولات، تب نوع/شهر پروژه، ستون‌های لینک) را از فهرست‌های وردپرس تولید کند. تصمیم Gate 0: **ساختار منو، آیتم‌ها و قابلیت‌های مگامنو (نوع پنل، ستون، آیکون، برچسب، تب) همه در Appearance → Menus** مدیریت می‌شوند؛ **فقط تنظیمات نمایشی سطح‌بالا** (روشن/خاموش مگامنو، حالت hover/click، ارتفاع هدر، چسبندگی) در داشبورد اختصاصی قالب است.

## فایل‌های هدف

- `inc/Nav/MegaMenuWalker.php`
- `inc/Nav/Fields.php`
- `template-parts/header/mega-products.php`
- `template-parts/header/mega-projects.php`
- `inc/Nav/MenuLocations.php`

## مراحل اجرا (چرخه ۶ گامی)

| گام | اقدام |
|---|---|
| `[READ MEMORY]` | خواندن `temp/PROJECT_MEMORY.md` و `temp/STATIC_TEMPLATE_MAP.md` و همین فایل |
| `[THINK & PLAN]` | تحلیل مارک‌آپ استاتیک مربوطه و طرح اتصال به وردپرس |
| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` |
| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM |
| `[RUN TEST]` | اجرا و رفع خطا تا ۱۰۰٪ شروط |
| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md`, `QA_REFLECTION_LOG.md` + کامیت |

## جزئیات پیاده‌سازی

1. ثبت `main-mega-menu`, `mobile-menu`, `tolidat-sherkat`, `landing`
2. `wp_nav_menu_item_custom_fields` برای فیلدهای آیتم (نوع مگامنو، ستون، آیکون، برچسب، تصویر، تب)
3. Walker تولیدکننده‌ی دقیق `<div data-mega-prod-panel>` و …
4. حالت فعال (`aria-current`) مطابق تفاوت‌های مشاهده‌شده در میرور
5. تنظیمات نمایشی (روشن/خاموش، hover/click، چسبندگی) از داشبورد؛ ساختار و آیتم‌ها و فیلدهای مگامنو فقط از Appearance → Menus

## شروط پاس شدن (Pass Conditions)

- [ ] خروجی Walker با مارک‌آپ استاتیک منطبق باشد (هش ساختاری)
- [ ] فیلدهای مگامنو دقیقاً در صفحه Appearance → Menus (هوک `wp_nav_menu_item_custom_fields`) رندر و ذخیره شوند و هیچ صفحه مدیریت منوی جداگانه‌ای در داشبورد ساخته نشود
- [ ] منوی موبایل off-canvas با JS موجود باز/بسته شود
- [ ] در نبود منو، fallback دقیقاً مارک‌آپ استاتیک باشد

## تست‌های خودکار

- تست Walker با منوی نمونه
- تست ذخیره فیلد آیتم منو
- تست fallback
- تست aria-current

> اسکریپت: `temp/phase-04-header-footer-navigation/tests/test-task-2.php`
