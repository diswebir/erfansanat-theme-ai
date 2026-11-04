# Task 5.7 — سکشن کاتالوگ دانلود + سرویس‌های تب‌دار

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-07.php`

## 🎯 هدف
دو سکشن متنی/تعاملی: دانلود کاتالوگ جامع و تب خدمات با preview میرور

## 🔗 وابستگی‌ها
- Task 5.1

## 📎 مراجع (منابع برش/استخراج)
- index.html aria-label دانلود کاتالوگ + سکشن #services (service-row/preview/num/icon/counter)

## 📤 فایل‌های خروجی
- `template-parts/home/section-catalog.php`
- `template-parts/home/section-services.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. کاتالوگ: فیلد فایل PDF (media) + متن دعوت + دکمه دانلود (پیش‌فرض میرور)
2. سرویس‌ها: Repeater ردیف‌ها (service-num، service-title، service-desc، service-img، لینک) — پیش‌فرض ۵ خدمت میرور که به سینگل CPT services لینک شوند
3. پنل preview (service-preview-*) با حفظ hookها؛ شمارنده service-counter
4. تب‌های تعاملی با JS میرور کار کنند (ساختار عین)

## ⛔ محدودیت‌ها و خط‌قرمزها
- لینک پیش‌فرض ردیف‌ها به /services/{slug} سرویس‌های هم‌نام

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] Repeater ۵ خدمت پیش‌فرض
- [ ] تب‌ها با JS میرور کار کنند
- [ ] کاتالوگ داینامیک

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-07.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
