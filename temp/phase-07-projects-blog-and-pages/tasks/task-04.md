# Task 7.4 — home.php / archive.php / category.php — وبلاگ و فیلتر دسته

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[PENDING]`
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
- [ ] فیلتر cat با GET کار کند
- [ ] pagination و شمارنده
- [ ] حالت خالی

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-04.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
