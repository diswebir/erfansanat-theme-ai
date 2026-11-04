# Task 2.5 — تب «برند/استایل» + «اسکریپت‌های سفارشی» + «تنظیمات آرشیوها»

**فاز:** 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-02-custom-admin-dashboard/tests/test-task-05.php`

## 🎯 هدف
رنگ‌های برند، تزریق اسکریپت هدر/فوتر کارفرما و تنظیمات لیست‌بندی آرشیوها

## 🔗 وابستگی‌ها
- Task 2.2

## 📎 مراجع (منابع برش/استخراج)
- متغیرهای رنگ styles.css میرور (سبز emerald غالب)

## 📤 فایل‌های خروجی
- `بسط ThemeOptions.php (سه تب)`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. برند: brand_primary (color، پیش‌فرض از CSS میرور)، brand_dark، فیلد‌های اختیاری accent — با help: تغییر رنگ فقط از طریق CSS Variables تزریق‌شونده در head اعمال شود (بدون دست‌زدن به styles.css)
2. اسکریپت سفارشی: custom_head_js و custom_footer_js (textarea) — چاپ در wp_head/wp_footer فقط اگر current_user اجازه unfiltered_html یا در ذخیره با wp_check_invalid_utf8؛ هشدار امنیتی در label
3. آرشیوها: products_per_page (number، پیش‌فرض ۲۰ مطابق گرید میرور)، projects_per_page (پیش‌فرض میرور)، posts_per_page، products_columns (پیش‌فرض ۳/۴ ریسپانسیو مطابق میرور)
4. تزریق CSS variable در head: :root{--es-brand:...} فقط وقتی مقدار با پیش‌فرض فرق دارد

## ⛔ محدودیت‌ها و خط‌قرمزها
- اسکریپت سفارشی هرگز سورس قالب را خراب نکند (خروجی خام فقط برای کاربر مجاز)

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] per_page پیش‌فرض محصولات = ۲۰
- [ ] اسکریپت سفارشی در head/footer دقیقاً چاپ شود (تست رشته)
- [ ] CSS variable برند فقط در صورت مقدار متفاوت تزریق شود

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-02-custom-admin-dashboard/tests/test-task-05.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
