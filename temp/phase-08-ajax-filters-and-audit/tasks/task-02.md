# Task 8.2 — سیستم فرم‌های اختصاصی + ذخیره‌سازی و ایمیل

**فاز:** 8 — جایگزینی Native جستجو/فیلترها، فرم‌ها و ممیزی نهایی  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 8.1

## هدف

فرم تماس، استعلام قیمت (پاپ‌آپ)، استخدام، بازخورد مشتری با ذخیره در جدول اختصاصی و ایمیل اطلاع‌رسانی.

## فایل‌های هدف

- `inc/Forms/FormHandler.php`
- `inc/Forms/FormRepository.php`
- `inc/Admin/FormsList.php`

## مراحل اجرا (چرخه ۶ گامی)

| گام | اقدام |
|---|---|
| `[READ MEMORY]` | خواندن `temp/PROJECT_MEMORY.md` و `temp/STATIC_TEMPLATE_MAP.md` و همین فایل |
| `[THINK & PLAN]` | تحلیل مارک‌آپ استاتیک مربوطه و طرح اتصال به وردپرس |
| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` |
| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM |
| `[RUN TEST]` | اجرا و رفع خطا تا ۱۰۰٪ شروط |
| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md`, `QA_REFLECTION_LOG.md` + کامیت |

## جزئیات پیاده‌سازی

1. جدول `{$wpdb->prefix}es_forms` با migration نسخه‌دار
2. Nonce + Honeypot + محدودیت نرخ
3. ایمیل با `wp_mail` و قالب فارسی
4. لیست و جزئیات در ادمین + نشانگر خوانده‌نشده

## شروط پاس شدن (Pass Conditions)

- [ ] ثبت امن از فرانت
- [ ] لیست پیام‌ها در منوی اختصاصی
- [ ] ارسال ایمیل (mock)
- [ ] رد درخواست‌های بدون Nonce

## تست‌های خودکار

- تست ثبت فرم
- تست لیست ادمین
- تست ایمیل mock
- تست امنیت

> اسکریپت: `temp/phase-08-ajax-filters-and-audit/tests/test-task-2.php`
