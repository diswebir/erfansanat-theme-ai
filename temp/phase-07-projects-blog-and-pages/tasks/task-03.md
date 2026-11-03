# Task 7.3 — قالب‌های برگه اختصاصی

**فاز:** 7 — پروژه‌ها، وبلاگ، برگه‌های عمومی و لندینگ‌ها  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 7.2

## هدف

درباره ما، تماس، مجوزها، FAQ، شرایط گارانتی، استخدام (+فرم) با ویرایش کامل از داشبورد؛ لندینگ‌های `arya`/`smartelement` فعلاً مسکوت (تصمیم Gate 0).

## فایل‌های هدف

- `page-templates/template-about.php`
- `template-contact.php`
- `template-licenses.php`
- `template-faq.php`
- `template-refund.php`
- `template-recruitment.php`

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

1. انتقال هر برگه به Section Schema
2. فرم تماس/استخدام با Nonce و Honeypot
3. آکاردئون FAQ (`data-faq-*`)
4. تب‌های استخدام (`initRecruitmentTabs`)

## شروط پاس شدن (Pass Conditions)

- [ ] تمام متون/تصاویر/تب‌ها از داشبورد قابل ویرایش باشند
- [ ] فرم‌ها امن و با پیام موفقیت درست
- [ ] هیچ متن هاردکدی باقی نماند

## تست‌های خودکار

- تست قالب برگه‌ها
- تست فرم
- اسکن هاردکد

> اسکریپت: `temp/phase-07-projects-blog-and-pages/tests/test-task-3.php`
