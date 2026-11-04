# Task 7.2 — archive-projects.php — آرشیو پروژه‌ها با فیلترها

**فاز:** 07 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها
**وضعیت:** `[PENDING]`
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
- [ ] فیلترها args صحیح (fixture)
- [ ] گرید و pagination
- [ ] حالت خالی

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-07-projects-blog-and-pages/tests/test-task-02.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
