# Task 2.7 — SectionManager — متاباکس، UI لیست سکشن‌ها (toggle + Drag & Drop)

**فاز:** 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-02-custom-admin-dashboard/tests/test-task-07.php`

## 🎯 هدف
اسکلت موتور مدیریت سکشن روی برگه‌ها: ردیف سکشن با کلید روشن/خاموش و جابه‌جایی درگ‌اند‌دراپ

## 🔗 وابستگی‌ها
- Task 1.2

## 📎 مراجع (منابع برش/استخراج)
- agent.md §Phase2 Task2.3

## 📤 فایل‌های خروجی
- `inc/Admin/SectionManager.php`
- `بسط assets/admin/admin.js (sortable داخلی)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. ثبت metabox «سکشن‌های صفحه» روی page (شرطی به قالب‌های دارای سکشن — فیلتر es_sections_post_types)
2. API ثبت سکشن: SectionManager::register_section($page_key, $id, $label, $schema) — schema فیلدها با قالب FieldRenderer
3. UI: هر سکشن ردیفی با drag-handle، switch فعال، عنوان و پنل جمع‌شونده فیلدها
4. ذخیره در متای post با کلید _es_sections به‌صورت آرایه [{id, enabled, order, fields:{…}}] + nonce + capability edit_post
5. Sortable با JS داخلی (HTML5 drag events — بدون کتابخانه) و ذخیره order هنگام save

## ⛔ محدودیت‌ها و خط‌قرمزها
- کلید متا با پیشوند _ (پنهان از فیلدهای سفارشی)
- بدون کتابخانه sortable خارجی

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] ذخیره/خواندن roundtrip آرایه سکشن‌ها با nonce (تست)
- [ ] capability edit_post چک شود
- [ ] order ذخیره‌شده با ترتیب drag مطابق باشد (تست منطق)

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-02-custom-admin-dashboard/tests/test-task-07.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
