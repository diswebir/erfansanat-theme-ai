# Task 4.6 — footer.php — سه ستون، اطلاعات تماس داینامیک و scroll-top

**فاز:** 04 — هدر، مگامنو، منوی موبایل و فوتر
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-04-header-footer-navigation/tests/test-task-06.php`

## 🎯 هدف
برش فوتر با جایگزینی ستون‌ها (منو/دسته‌بندی) و اطلاعات تماس از داشبورد

## 🔗 وابستگی‌ها
- Task 4.1
- Task 2.6
- Task 2.4

## 📎 مراجع (منابع برش/استخراج)
- temp/phase-04…/refs/footer-ref.html

## 📤 فایل‌های خروجی
- `footer.php`
- `template-parts/footer/footer-main.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. برش مارک‌آپ فوتر (کپی عین) به footer-main.php
2. ستون دسترسی سریع: wp_nav_menu(location فوتر یا fallback repeater آپشن footer.quick_links از 2.4)
3. ستون دسته‌بندی محصولات: wp_list_categories از product_cat با مارک‌آپ سفارشی (walker سبک) — یا منوی tolidat-sherkat اگر تعریف شده
4. ستون اطلاعات تماس: es_contact_args() (تلفن/ایمیل/آدرس/ساعات) + آیکون‌های SVG میرور
5. نوار پایانی: کپی‌رایت از آپشن + social از آپشن + دکمه data-scroll-top
6. فراخوانی wp_footer() در انتها

## ⛔ محدودیت‌ها و خط‌قرمزها
- DOM فوتر عین ref؛ فقط محتوا داینامیک

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] مقایسه DOM فوتر با ref مطابق
- [ ] سه منبع محتوایی (منو/fallback/آپشن) هر کدام تست
- [ ] wp_footer فراخوانی شود

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-04-header-footer-navigation/tests/test-task-06.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
