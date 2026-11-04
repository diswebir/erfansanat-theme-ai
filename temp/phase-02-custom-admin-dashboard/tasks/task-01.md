# Task 2.1 — هسته ThemeOptions — منوی پیشخوان، صفحه تب‌دار RTL و فریم‌ورک AJAX امن

**فاز:** 02 — فریم‌ورک داشبورد اختصاصی ادمین و Section Builder
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-02-custom-admin-dashboard/tests/test-task-01.php`

## 🎯 هدف
اسکلت پنل ادمین با امنیت کامل (Nonce + Capability) و ذخیره ایجکس در یک آپشن واحد

## 🔗 وابستگی‌ها
- Task 1.2

## 📎 مراجع (منابع برش/استخراج)
- agent.md §Phase2 Task2.1

## 📤 فایل‌های خروجی
- `inc/Admin/ThemeOptions.php`
- `inc/Admin/AdminAssets.php`
- `assets/admin/admin.css`
- `assets/admin/admin.js`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. add_menu_page «عرفان صنعت» با dashicons-admin-customizer و capability manage_options + register submenu واحد «تنظیمات قالب»
2. رندر صفحه: کانتینر تب‌دار (data-es-tab) با ناوبری سمت راست و پنل‌ها — بدون هیچ CDN
3. AdminAssets: ثبت admin.css/admin.js فقط روی صفحه قالب (هوک admin_enqueue_scripts با شرط screen)
4. AJAX: اکشن wp_ajax_es_theme_options_save → check_ajax_referer('es_admin', 'nonce') + current_user_can('manage_options') + sanitize کل آرایه + update_option('erfansanat_theme_options', $data, false)
5. پاسخ JSON استاندارد {success, message} و نمایش toast در JS
6. خروجی nonce به JS با wp_localize_script روی admin.js

## ⛔ محدودیت‌ها و خط‌قرمزها
- هیچ ذخیره‌ای بدون nonce/capability مسیر نداشته باشد
- آپشن واحد و autoload=false

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] اکشن ذخیره قبل از هر پردازشی nonce و capability را چک کند (ترتیب کد)
- [ ] update_option دقیقاً روی کلید erfansanat_theme_options با autoload=false
- [ ] منوی ادمین با capability manage_options ثبت شود
- [ ] admin.css/admin.js فقط روی صفحه تنظیمات قالب enqueue شوند

## 🔄 چرخه اجرای الزامی (۶ گام)
- [x] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [x] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [x] WRITE TEST — تکمیل temp/phase-02-custom-admin-dashboard/tests/test-task-01.php
- [x] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [x] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [x] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
