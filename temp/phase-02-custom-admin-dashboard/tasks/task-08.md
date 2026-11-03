# Task 2.8 — SectionManager — رندر فیلدهای داینامیک هر سکشن (media/url/repeater)

**فاز:** 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-02-custom-admin-dashboard/tests/test-task-08.php`

## 🎯 هدف
پنل هر سکشن بر اساس schema خودش فیلدها را با FieldRenderer بسازد و ذخیره ساختارمند انجام شود

## 🔗 وابستگی‌ها
- Task 2.7
- Task 2.2

## 📎 مراجع (منابع برش/استخراج)
- سکشن‌های index.html / about.html / contact.html (schemaهای فاز ۵ و ۷)

## 📤 فایل‌های خروجی
- `بسط SectionManager.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. متد render_fields($section_schema, $saved_fields) → فراخوانی FieldRenderer برای هر فیلد با نام nesting opt_sections[{index}][fields][{key}]
2. پشتیبانی schema فیلد: type، label، default، help، options (برای select)، repeater با زیرفیلدها
3. ذخیره: normalize ترتیب ردیف‌ها بعد از drag + merge فیلدهای جدید schema با داده ذخیره‌شده (سازگاری رو به جلو)
4. حذف امن سکشن‌های unregister شده از خروجی ذخیره (keep data نه — تمیز نگه‌داشتن با کپی بکاپ در متای دوم اختیاری)

## ⛔ محدودیت‌ها و خط‌قرمزها
- merge schema نباید داده ذخیره‌شده معتبر را حذف کند

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر فیلدهای media و repeater داخل سکشن (snapshot تست)
- [ ] ذخیره roundtrip فیلدهای repeater داخل سکشن
- [ ] merge schema با داده قدیمی بدون از دست دادن فیلدها

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-02-custom-admin-dashboard/tests/test-task-08.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
