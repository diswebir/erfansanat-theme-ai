# Task 7.3 — taxonomy-project_type.php و taxonomy-city.php

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[DONE — 25/25 · رگرسیون ۱۲۱۳/۰]`
**تست:** `temp/phase-07-projects-blog-and-pages/tests/test-task-03.php`

## 🎯 هدف
آرشیو ترم‌های پروژه مطابق میرور city/*.html و project_type/*.html

## 🔗 وابستگی‌ها
- Task 7.2

## 📎 مراجع (منابع برش/استخراج)
- city/esfahan.html
- project_type/light-tunnel.html

## 📤 فایل‌های خروجی
- `taxonomy-project_type.php`
- `taxonomy-city.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هیرو ترم: عنوان ترم + توضیح ترم + تصویر (اختیاری از متای ترم یا placeholder میرور)
2. گرید پروژه‌های ترم با card-project + نوار فیلتر مشترک (با پیش‌انتخاب ترم جاری)
3. صفحه‌بندی میرور
4. شمارش پروژه‌های ترم

## ⛔ محدودیت‌ها و خط‌قرمزها
- URLهای /project_type/{term} و /city/{term} حفظ

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [x] رندر ترم fixture
- [x] گرید و صفحه‌بندی
- [x] پیش‌انتخاب فیلتر جاری

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-03.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit

## ✅ نتیجه (2026-11-08)
- خروجی‌ها: `taxonomy-project_type.php` + `taxonomy-city.php` (شل‌های دوقلوی توکن‌مشترک با heredocهای جدا برای هیرو/تاپ‌بار/مقاله هر ترم) + `template-parts/archive/project-drawer.php` (دراور موبایل پویا — برداشته از ESP72E ثابت ۷.۲). ژنراتور `temp/gen_p73.py` با assert بازسازی کامل دراور هر دو میرور (lt/es) + پیوستگی ریجن‌ها.
- متاهای ترم (fallback میرور): es_archive_crumb / es_archive_badge / es_archive_title_a / es_archive_title_b / es_archive_caption / es_archive_top_title / es_archive_chip / es_archive_image_id؛ توضیح ترم = term_description با fallback نام.
- تفاوت‌های ساختاری ترم نسبت به آرشیو اصلی: ناو تک‌صفحه‌ای همیشه رندر می‌شود با کلاس `hidden` (چندصفحه‌ای: `flex`) + قبلی/بعدی disabled؛ بلوک «بازنشانی» در سایدبار (ESP72CLR با clear_url)؛ ردیف «همه انواع پروژه‌ها» غیرفعال با نقطه slate-300 و شمارش کل (wp_count_posts)؛ در حالت شهر: ردیف نوعِ هیچ فعال نیست (deactivate_all) ولی چیپ شهر جاری در سایدبار و دراور فعال است (واریانت emerald-800).
- سگمنت‌های استاتیک (درواور/مقاله SEO/CTA) با لینک‌های نسبی خام `../` می‌مانند (قرارداد ۷.۲)؛ لینک‌سازی محیطی فقط در هیرو/سایدبار/تاپ‌بار/empty/دراور.
- ProjectArchive::apply روی is_tax(['project_type','city']) هم فعال شد (per-page + sort).
- تغییر pagination.php مشترک: آرگومان‌های always_show و nav_cls + توکن یکپارچه {ES_NEXT_TAG} (enabled: class+data-href / disabled: disabled+class — هر دو چیدمان میرور پوشش داده شد؛ ۶.۷ و ۷.۲ دست‌نخورده). پاس: 25/25 (دو قیاس بایت‌دقیق کل main + ناو/فیلتر/دراور/خالی/چندصفحه‌ای/apply). رگرسیون: **۱۲۱۳ پاس / ۰ رد** (۶۵ فایل + ۱۷ placeholder).
