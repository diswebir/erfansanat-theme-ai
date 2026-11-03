# Task 2.9 — SectionManager — API رندر فرانت (es_render_sections) و پیکربندی اولیه صفحات

**فاز:** 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-02-custom-admin-dashboard/tests/test-task-09.php`

## 🎯 هدف
حلقه رندر سکشن‌های فعال به ترتیب ادمین + ثبت schema سکشن‌های home/about/contact/landing (بخش‌های مشترک)؛ سکشن‌های اختصاصی در فازهای ۵ و ۷ اضافه می‌شوند

## 🔗 وابستگی‌ها
- Task 2.8

## 📎 مراجع (منابع برش/استخراج)
- index.html / about.html / contact.html (ترتیب سکشن‌ها)

## 📤 فایل‌های خروجی
- `بسط SectionManager.php (رندر)`
- `inc/Sections/Registry.php (ثبت سکشن‌های صفحات)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. تابع es_render_sections(int $post_id): خواندن متا → sort by order → for each enabled: es_get_template('template-parts/sections/{id}', ['fields'=>…])
2. fallback: اگر متا خالی → همه سکشن‌های ثبت‌شده در ترتیب ثبت، فعال (دقیقاً ظاهر میرور)
3. Registry: ثبت سکشن‌های پایه صفحات با id و schema (اسکلت؛ پر کردن جزئیات در فاز ۵/۷)
4. فیلتر es_sections_render_args برای توسعه‌پذیری

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ سکشن غیرفعال رندر نشود
- ترتیب پیش‌فرض عین میرور

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] رندر با متای تست: فقط سکشن‌های enabled به ترتیب order لود شوند (تست با stub template loader)
- [ ] حالت متای خالی → ترتیب کامل پیش‌فرض
- [ ] args فیلدها به template پاس شوند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-02-custom-admin-dashboard/tests/test-task-09.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
