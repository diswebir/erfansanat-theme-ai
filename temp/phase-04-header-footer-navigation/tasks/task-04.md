# Task 4.4 — MegaMenuWalker برای main-mega-menu

**فاز:** 04 — هدر، مگامنو، منوی موبایل و فوتر
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-04-header-footer-navigation/tests/test-task-04.php`

## 🎯 هدف
Walker اختصاصی تولید مارک‌آپ مگامنو تب‌دار (پنل محصولات/پروژه‌ها) دقیقاً مطابق میرور با آیتم‌های منوی وردپرس

## 🔗 وابستگی‌ها
- Task 4.3
- Task 1.3

## 📎 مراجع (منابع برش/استخراج)
- header-ref.html ناو دسکتاپ: desktop-mega-trigger/panel، mega-prod-tab/panel، mega-proj-tab/panel

## 📤 فایل‌های خروجی
- `inc/Nav/MegaMenuWalker.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. تحلیل مارک‌آپ مگامنو: سطح اول = triggerها؛ کنوانسیون: آیتم با کلاس CSS «es-mega-tab» → تب؛ «es-mega-panel» → پنل؛ داخل پنل، سطح بعدی گروه‌ها
2. پیاده‌سازی Walker سفارشی start_lvl/end_lvl/start_el/end_el با حفظ همه کلاس‌ها و data-hookها و ساختار تودرتو
3. مدیریت current-menu-item/current-menu-ancestor با کلاس‌های میرور فعال
4. fallback بدون منو: wp_nav_menu با callback خروجی ثابت مطابق میرور (لینک‌های آرشیو اصلی) تا هدر هرگز خالی نشود
5. تست با fixture آیتم‌های منو (آرایه ساختگی مشابه خروجی wp_get_nav_menu_items)

## ⛔ محدودیت‌ها و خط‌قرمزها
- خروجی walker باید با JS موجود میرور کار کند (hookها حفظ)
- کلاس‌های CSS آیتم‌ها دقیقاً مطابق ref

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] خروجی walker با fixture شامل trigger/panel/tab و زیرآیتم‌ها = مطابق ساختار ref (تست رشته/DOM)
- [ ] کلاس‌های active درست اعمال شوند
- [ ] fallback بدون منو رندر کامل میرورگونه بدهد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-04-header-footer-navigation/tests/test-task-04.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit

## 🔄 چرخه اجرا
- [x] READ MEMORY — refها/نقشه برش از HEADER_MAP
- [x] THINK & PLAN — فرگمنت‌های byte-exact + نقاط داینامیک
- [x] WRITE TEST — `test-task-04.php`
- [x] IMPLEMENT — بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — پاس کامل (مجموع فاز ۴: 212/212)
- [x] UPDATE MEMORY + COMMIT
