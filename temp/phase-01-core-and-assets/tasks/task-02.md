# Task 1.2 — Autoloader PSR-4 با Namespace ErfanSanat\Theme و هلپرهای پایه

**فاز:** 01 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets
**وضعیت:** `[PENDING]`
**تست:** `temp/phase-01-core-and-assets/tests/test-task-02.php`

## 🎯 هدف
هر کلاسی که در inc/ ساخته می‌شود بدون require دستی لود شود و توابع هلپر مرکزی (es_option، es_template) آماده باشند

## 🔗 وابستگی‌ها
- Task 1.1

## 📎 مراجع (منابع برش/استخراج)
- هیچ — فایل جدید (معماری)

## 📤 فایل‌های خروجی
- `inc/Autoloader.php`
- `inc/Helpers.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. ساخت Autoloader.php با spl_autoload_register: نگاشت پیشوند ErfanSanat\Theme\ → مسیر inc/ و تبدیل \ به / و مطابقت حروف کوچک-خط‌تیره فایل
2. ساخت Helpers.php: تابع es_option(string $key, mixed $default = null) با کش استاتیک روی get_option('erfansanat_theme_options', []) و فیلتر es_default_options برای توسعه آینده
3. تابع es_get_template(string $tpl, array $args = []): لود امن template-parts با extract کردن args و مسیر relative برای override از چایلدتم
4. تابع es_template_path($tpl) برای resolve مسیر قالب (locate_template)

## ⛔ محدودیت‌ها و خط‌قرمزها
- Autoloader نباید بیش از یک بار ثبت شود (guard static)
- es_option نباید دیتابیس را بنویسد — فقط خواندن

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] php -l هر دو فایل پاس شود
- [ ] اتولودر یک کلاس fixture داخل inc/ را بدون require دستی resolve کند (تست با فایل فرضی)
- [ ] es_option در حالت نبودِ آپشن، مقدار default را برگرداند
- [ ] es_get_template مسیر فایل موجود را resolve کند و extract امن args انجام شود

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-01-core-and-assets/tests/test-task-02.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
