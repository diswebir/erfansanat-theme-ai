# Task 5.2 — سکشن هیرو طرح ۱ (hero-home1) — نوتچ، scene switcher و ویدیو

**فاز:** 05 — تبدیل صفحه اصلی و کامپوننت‌های ماژولار
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-05-homepage-and-sections/tests/test-task-02.php`

## 🎯 هدف
برش سکشن #home شامل سوییچر صحنه‌ها و ویدیو درون‌خطی با حفظ همه hookهای JS

## 🔗 وابستگی‌ها
- Task 5.1

## 📎 مراجع (منابع برش/استخراج)
- index.html سکشن #home (home1-scene-btn/img/title/cta/link، home1-inline-video/play/mute/close، home1-bottom-fillet/tab، home1-atmosphere)

## 📤 فایل‌های خروجی
- `template-parts/home/section-hero.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. کپی عین مارک‌آپ سکشن به فایل؛ جایگزینی: عنوان/زیرعنوان/متون از fields سکشن (schema 5.1)
2. Repeater صحنه‌ها: هر scene → home1-scene-btn + home1-scene-img + home1-scene-title + home1-scene-cta + home1-scene-link (پیش‌فرض: صحنه‌های میرور با تصاویر assets)
3. ویدیو: فیلد media ویدیو (پیش‌فرض hero-video.mp4) + اتریبیوت‌های حفظ‌شده برای کنترل‌های mute/close/play
4. لینک‌های CTA هر صحنه → فیلد url (پیش‌فرض میرور)
5. تصاویر با es_media_url() از آیدی media

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ data-hookی حذف نشود
- تصاویر پیش‌فرض از assets/images میرور

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] مقایسه ساختار سکشن رندرشده با ref (کلاس‌ها/hookها)
- [ ] Repeater صحنه‌ها به تعداد دلخواه کار کند (تست ۳ صحنه)
- [ ] ویدیو فیلد داینامیک داشته باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-05-homepage-and-sections/tests/test-task-02.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
