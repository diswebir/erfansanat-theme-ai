# Task 8.4 — فیلتر هوشمند پروژه‌ها (type/city + شمارنده)

**فاز:** 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-08-ajax-filters-and-audit/tests/test-task-04.php`

## 🎯 هدف
فیلتر ایجکس آرشیو پروژه‌ها مطابق بلوپرینت با شمارنده تعداد هر ترم

## 🔗 وابستگی‌ها
- Task 8.1
- Task 7.2

## 📎 مراجع (منابع برش/استخراج)
- نوار فیلتر projects.html (project-type/province/sort/count)

## 📤 فایل‌های خروجی
- `inc/Ajax/ProjectFilters.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. هندلر es_filter_projects: type، city، sort، s، paged — args امن
2. شمارنده تعداد پروژه هر ترم project_type/city (WP_Term_Query counts یا get_objects_in_term count) با کش
3. partial گرید card-project + شمارنده project-count
4. JS بایند به فیلترهای موجود + URL state
5. حالت خالی مطابق میرور

## ⛔ محدودیت‌ها و خط‌قرمزها
- شمارنده‌ها دقیق و کش‌شده

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] فیلترها args صحیح
- [ ] شمارنده‌ها با fixture صحیح
- [ ] partial و URL state

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-08-ajax-filters-and-audit/tests/test-task-04.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
