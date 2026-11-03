# Task 3.7 — موتور Repeater سازگار JetEngine (خواندن/نوشتن دو-مد serial/JSON)

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-07.php`

## 🎯 هدف
کلاس مرکزی خواندن و نوشتن فیلدهای تکرارشونده با حفظ دقیق فرمت فعلی دیتابیس ({item-0:{…}})

## 🔗 وابستگی‌ها
- Task 1.2

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۴ (فرمت آرایه/JSON سازگار ساختار فعلی)

## 📤 فایل‌های خروجی
- `inc/Data/RepeaterEngine.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. متد read($post_id, $key): get_post_meta → پارس: if is_array → استفاده؛ elseif string خروجی json_decode موفق → آرایه؛ elseif is_serialized → unserialize؛ else → ردیف‌های خالی
2. normalize: تبدیل associative (item-0, item-1…) به لیست حفظ ترتیب کلیدها با natsort عددی
3. متد write($post_id, $key, $rows, $format): اگر فرمت اصلی JSON بود → json_encode با unslashed slashes و کلیدهای item-N؛ وگرنه serialize آرایه با کلیدهای item-N (رفتار JetEngine)
4. متد detect_format($raw) و تست سه fixture: JSON string، serialized array، آرایه PHP خام
5. پشتیبانی ترم‌متا (متد read_term/write_term) برای faq دسته‌بندی

## ⛔ محدودیت‌ها و خط‌قرمزها
- هرگز فرمت داده موجود تغییر نکند (JSON بماند JSON، serial بماند serial)
- کلیدهای ردیف item-0/item-1/… حفظ شود

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] fixture JSON {"item-0":{"a":1}} به‌صورت JSON ذخیره مجدد شود (نه serialize)
- [ ] fixture serialized به‌صورت serialize ذخیره شود
- [ ] ترتیب ردیف‌ها پس از normalize حفظ شود
- [ ] متدهای term کار کنند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-07.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
