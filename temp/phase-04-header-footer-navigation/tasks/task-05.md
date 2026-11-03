# Task 4.5 — MobileWalker و سایدبار موبایل (off-canvas)

**فاز:** 04 — هدر، مگامنو، منوی موبایل و فوتر
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-04-header-footer-navigation/tests/test-task-05.php`

## 🎯 هدف
منوی موبایل چندسطحی با آکاردئون (mobile-acc-*) دقیقاً مطابق سایدبار میرور

## 🔗 وابستگی‌ها
- Task 4.4

## 📎 مراجع (منابع برش/استخراج)
- header-ref.html بلوک mobile-sidebar/backdrop/toggle/close، mobile-prod/proj-panel، mobile-acc-btn/icon/panel

## 📤 فایل‌های خروجی
- `inc/Nav/MobileWalker.php`
- `template-parts/header/mobile-sidebar.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. برش سایدبار موبایل و backdrop به template-parts/header/mobile-sidebar.php (کپی عین مارک‌آپ)
2. Walker موبایل: آیتم والد → دکمه آکاردئون mobile-acc-btn + پنل mobile-acc-panel؛ فرزندان در پنل
3. دو تب موبایل (محصولات/پروژه‌ها) با mobile-prod-tab/panel و mobile-proj-tab/panel — تخصیص آیتم‌ها از منوی mobile-menu با کلاس‌های es-meg‌a-… (همان کنوانسیون 4.4)
4. اتصال toggle/close/backdrop به دکمه‌های هدر (hookهای حفظ‌شده)
5. fallback بدون منو: لینک‌های اصلی مطابق میرور

## ⛔ محدودیت‌ها و خط‌قرمزها
- آیکون‌های chevron/بستن SVG عیناً
- بدون اسکریپت جدید — JS میرور کافی است

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] مقایسه DOM سایدبار با ref
- [ ] آکاردئون با JS میرور کار کند (hookها موجود)
- [ ] fallback رندر شود

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-04-header-footer-navigation/tests/test-task-05.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
