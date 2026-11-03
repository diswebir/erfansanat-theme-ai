# Task 3.2 — متاباکس محصولات + لایه نرمال‌سازی متا

**فاز:** 3 — معماری داده‌ها، CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده  
**وضعیت:** `[PENDING]`  
**وابستگی:** Task 3.1

## هدف

متاباکس تب‌بندی‌شده برای همه کلیدهای متای محصول و Repeaterها با پشتیبانی از آرایه سریالایز و JSON؛ نوشتن با حفظ فرمت قبلی.

## فایل‌های هدف

- `inc/Admin/Metaboxes/ProductMeta.php`
- `inc/Meta/MetaRepository.php`
- `inc/Meta/RepeaterCodec.php`

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

1. تب‌ها: شناسه‌های قانونی / عناوین و فروش / فایل و گالری / روابط / ویژگی‌ها / کاربردها / FAQ
2. `RepeaterCodec`: decode/encode هم برای serialize و هم JSON
3. Auto-sync `galerry_produ` ↔ `_product_image_gallery`
4. Quick Edit برای `gs1_id..product_id`, `zireonvan`, `english_titl`, `true_sale`
5. Media picker برای `product-catalog-file` (ذخیره URL) و گالری (CSV IDs)
6. Relations: انتخابگر پروژه‌ها و ترم‌های `project_type`

## شروط پاس شدن (Pass Conditions)

- [ ] خواندن مقادیر `{"item-0":{...}}` سریالایز همان نتیجه JSON را بدهد
- [ ] ذخیره مجدد، ساختار قبلی را خراب نکند (round-trip byte-stable در سطح داده)
- [ ] تمام ۱۴ کلید و ۴ Repeater خوانده/نوشته شوند
- [ ] `true_sale` فقط `true`/`false` بپذیرد
- [ ] Nonce و capability روی هر متاباکس اعمال شود

## تست‌های خودکار

- تست round-trip همه کلیدها
- تست هر دو فرمت Repeater
- تست sync گالری
- تست sanitize و capability

> اسکریپت: `temp/phase-03-cpt-and-legacy-meta/tests/test-task-2.php`
