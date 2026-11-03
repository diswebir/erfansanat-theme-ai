# Task 8.10 — سوئیت E2E کل پروژه + چک‌لیست استقرار روی سایت زنده

**فاز:** 08 — جایگزینی Native JetSearch/JetSmartFilters، فرم‌ها و ممیزی نهایی
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-08-ajax-filters-and-audit/tests/test-task-10.php`

## 🎯 هدف
اجرای run-all-phases-e2e با پاس ۱۰۰٪ + مستند استقرار مرحله‌به‌مرحله (غیرفعال‌سازی Elementor/Jet، منوها، flush، بکاپ)

## 🔗 وابستگی‌ها
- Task 8.7
- Task 8.8
- Task 8.9
- Task همه فازها

## 📎 مراجع (منابع برش/استخراج)
- temp/e2e/run-all-phases-e2e.sh
- agent.md §Phase8 Task8.3

## 📤 فایل‌های خروجی
- `تقریر temp/phase-08…/reports/final-deployment.md`
- `بسط temp/e2e/run-all-phases-e2e.sh`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. اجرای تمام run-phase-01…08 — همه باید ۱۰۰٪ پاس شوند؛ هر FAIL → رفتن به تسک مربوطه و اصلاح → اجرای مجدد
2. چک‌لیست استقرار: بکاپ کامل دیتابیس/فایل‌ها؛ نصب قالب؛ ثبت منوها در ۴ location؛ تخصیص قالب‌های برگه؛ flush permalinks؛ تست لینک‌های نمونه (۱۲۸ مسیر میرور → مقایسه 200/301)؛ غیرفعال‌سازی ترتیبی المنتور/جت‌ها؛ تست چرخه خرید واقعی و فرم‌ها؛ LiteSpeed purge
3. ماتریس ریسک و rollback (بازگشت به قالب قبل)
4. گزارش نهایی تحویل کارفرما با خلاصه تسک‌ها/تست‌ها/مقادیر

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ مرحله استقرار بدون بکاپ

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] خروجی e2e: صفر FAIL
- [ ] چک‌لیست استقرار کامل و قابل اجرا
- [ ] ماتریس rollback مستند
- [ ] گزارش نهایی تولید شود

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-08-ajax-filters-and-audit/tests/test-task-10.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
