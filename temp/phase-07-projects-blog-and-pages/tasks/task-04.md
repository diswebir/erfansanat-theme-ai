# Task 7.4 — home.php / archive.php / category.php — وبلاگ و فیلتر دسته

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[DONE — 26/26 · رگرسیون ۱۲۳۹/۰]`
**تست:** `temp/phase-07-projects-blog-and-pages/tests/test-task-04.php`

## 🎯 هدف
آرشیو مقالات مطابق blog.html و category/magazine.html با فیلتر دسته کارا

## 🔗 وابستگی‌ها
- Task 5.11

## 📎 مراجع (منابع برش/استخراج)
- blog.html (blog-cat-btn، blog-search، blog-grid، blog-count)
- category/magazine.html

## 📤 فایل‌های خروجی
- `home.php`
- `archive.php`
- `category.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هیرو وبلاگ: عنوان از برگه blog (7426) در home.php؛ عنوان ترم در category.php
2. نوار فیلتر: دکمه‌های دسته (blog-cat-btn با active) + جستجو (blog-search) — فیلتر با GET (cat/s) و حفظ hookها برای فاز ۸
3. گرید card-post + pagination میرور + شمارنده blog-count
4. استicky post مدیریت مطابق میرور (بدون تکرار)
5. حالت blog-empty

## ⛔ محدودیت‌ها و خط‌قرمزها
- page_for_posts رعایت شود (home.php فقط برای صفحه نوشته‌ها)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [x] فیلتر cat با GET کار کند
- [x] pagination و شمارنده
- [x] حالت خالی

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-04.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit

## 🧾 گزارش اجرا (2026-11-08)
- خروجی‌های اضافه: `template-parts/archive/blog-shell.php` (شل مشترک mode=home|category) + `inc/Frontend/BlogArchive.php` (فیلتر GET + هوک es_blog_archive_query_args) + `temp/gen_p74.py` (assert بازسازی دو میرور).
- بایت‌دقیق: home ≡ blog.html (۴۳٬۴۳۶ کاراکتر) و category ≡ magazine.html (۳۲٬۱۹۵ کاراکتر) بعد از لینک‌سازی محیطی.
- sticky چندگانه (۷۰۱–۷۰۳) = ۳ «★ ویژه»؛ هیرو «★ منتخب سردبیر» = نخستین sticky؛ بدون حذف از گرید (مطابق میرور).
- فیلتر GET: BlogArchive::apply فقط روی is_home/is_category (posts_per_page=100، ignore_sticky_posts)؛ سمت کلاینت data-items-per-page="4" میرور حفظ شد.
- نکته: per-page میرور (۴) از مقدار ذخیره‌شده `archives.posts_per_page` می‌آید؛ پیش‌فرض ادمین (۹) فاز ۲ دست نخورد.
