# Task 7.2 — archive-projects.php — آرشیو پروژه‌ها با فیلترها

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[DONE — 20/20 · رگرسیون ۱۱۸۸/۰]`
**تست:** `temp/phase-07-projects-blog-and-pages/tests/test-task-02.php`

## 🎯 هدف
آرشیو مطابق projects.html: هیرو، فیلتر نوع/شهر/مرتب‌سازی، گرید و صفحه‌بندی

## 🔗 وابستگی‌ها
- Task 5.11

## 📎 مراجع (منابع برش/استخراج)
- projects.html

## 📤 فایل‌های خروجی
- `archive-projects.php`
- `template-parts/archive/project-filters.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هیرو آرشیو (عنوان/متن از آپشن آرشیو فاز 2.5)
2. نوار فیلتر میرور: project-search، project-type (دراپ‌داون ترم‌ها)، project-province (city)، project-sort (جدیدترین/پربازدید/امتیاز) + project-count
3. pre_get_posts: اعمال GET امن (s/tax_query/orderby meta views) — AJAX در فاز ۸
4. گرید card-project + pagination میرور
5. حالت project-empty

## ⛔ محدودیت‌ها و خط‌قرمزها
- data-hookها عین میرور
- فیلترهای GET امن

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [x] فیلترها args صحیح (fixture)
- [x] گرید و pagination
- [x] حالت خالی

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-02.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit

## ✅ نتیجه (2026-11-08)
- خروجی‌ها: `archive-projects.php` + `template-parts/archive/project-filters.php` + **بازنویسی مشترک** `template-parts/archive/pagination.php` (پیش‌فرض‌ها = محصولات برای سازگاری ۶.۷؛ پروژه‌ها با root/count_attr/unit/label) + `inc/Frontend/ProjectArchive.php` (boot در Theme.php). ژنراتور `temp/gen_p72.py` با assert بازسازی بایت‌دقیق ۹ ریجن + برابری نرمال‌شده ناو projects≡shop.
- فرمت‌های میرور: ۱۲ کارت دم در گرید ولی `data-items-per-page="6"` و ناو دوصفحه‌ای → شل ۶ کارت صفحه ۱ می‌رندر می‌کند؛ custom کارت‌ها یک‌درمیان 0.04/0.1.
- ساختار آرشیو نستد است (برخلاف صفحه اصلی): `<div data-project-item ... class="block">` محتوا را در بر می‌گیرد → فلگ `nested` در card-project (WRAP بدون بستن + بستن بعد از BODY)؛ شل بعد از include `</div>` چاپ می‌کند.
- ترتیب میرور بعد از گرید: nav → empty-state → دراور موبایل → مقاله SEO (همه در ESP72E؛ مقاله/دراور استاتیک با لینک‌های .html خام طبق قرارداد archive-product).
- ProjectArchive: pre_get_posts @20 — type/location → tax_query (slug)، sort popular→number2-viewc / rating→es_rating (meta_value_num DESC)، per-page = es_option با کلامپ ۱..۱۰۰، گارد is_main_query+is_post_type_archive، متد استاتیک query_args() برای تست.
- **اصلاح فاز ۲:** دیفالت اسکیمای `archives.projects_per_page` از ۱۲ به ۶ (تست ۲.۵ هم به‌روز شد) — میرور per-page=۶ و ناو دوصفحه‌ای برای ۱۲ آیتم، گواه بی‌چون‌وچرا است.
- card-project: `es_client` (کارفرما) به data-project-search-text اضافه شد — میرور آرشیو نام کارفرما را در جستجو دارد (پسوند امن؛ ۵.۳ بدون متا دست‌نخورده).
- تست ۷.۲: 20/20 — سایدبار بایت‌دقیق (۹ ردیف نوع + ۱۰ شهر + همه)، حالت فعال نوع، کل main شل ≡ میرور (با ۶ حذف کارت دمو + ۶ جایگزینی محیطی)، empty toggle، selected مرتب‌سازی، query_args/apply/کلامپ/sanitize.
- رگرسیون: **۱۱۸۸ پاس / ۰ رد** (۶۴ فایل پیاده‌شده؛ ۱۸ placeholder بدون «نتیجه»).
