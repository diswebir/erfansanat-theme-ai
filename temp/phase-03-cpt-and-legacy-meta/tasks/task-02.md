# Task 3.2 — فیلترهای ادمین لیست پروژه‌ها (دراپ‌داون city/project_type + ستون same_key_hrefl)

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-02.php`

## 🎯 هدف
مدیریت راحت پروژه‌ها: فیلتر دراپ‌داونی تکسونومی‌ها و ستون مرتب‌سازی کلید hreflang

## 🔗 وابستگی‌ها
- Task 3.1

## 📎 مراجع (منابع برش/استخراج)
- agent.md §Phase3 Task3.1 (بند فیلترها)

## 📤 فایل‌های خروجی
- `inc/Data/AdminListFilters.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هوک restrict_manage_posts برای projects: دو دراپ‌داون (city، project_type) با get_terms
2. هوک parse_query: اعمال فیلتر tax_query وقتی GET تنظیم شده
3. ستون‌های ادمین: شهر، نوع پروژه، same_key_hrefl (از متا) — قابل مرتب‌سازی با pre_get_posts (orderby=meta_value meta_key=same_key_hrefl)
4. خروجی ستون‌ها escaping شده

## ⛔ محدودیت‌ها و خط‌قرمزها
- فیلترها فقط روی admin list — روی فرانت اثری نداشته باشند (گارد is_admin)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] دو دراپ‌داون تکسونومی رندر شود
- [ ] parse_query فیلترها را به tax_query تبدیل کند (تست args)
- [ ] ستون same_key_hrefl sortable باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-02.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
