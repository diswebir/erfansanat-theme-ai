# Task 02.01 — پنل «داشبورد اختصاصی عرفان صنعت»

**فاز:** phase-02-custom-admin-dashboard · **وضعیت:** `[PENDING]`

## شرح تسک
- ساخت `inc/Admin/ThemeOptions.php` — منوی پیشخوان اختصاصی با آیکون و صفحه تب‌بندی‌شده RTL (CSS/JS داخلی، بدون CDN)
- ذخیره ایجکس با `admin-ajax` + Nonce + `current_user_can('manage_options')`
- ذخیره کل تنظیمات در یک آپشن `erfansanat_theme_options` (آرایه)
- هلپر کش‌شده `es_option($key, $default)` در `inc/Helpers.php`
- فیلدهای استاندارد: text, textarea, media, color, switch, select, repeater (کامپوننت‌های JS ساخت خود قالب، بدون وابستگی خارجی)

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] ذخیره بدون reload با پاس Nonce و Capability (تست امنیتی)
- [ ] خروجی `es_option` کش‌شده و صحیح
- [ ] رندر تب‌ها در ادمین RTL بدون خطای JS

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
