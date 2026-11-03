# Task 08.01 — موتور جستجوی زنده و فیلترهای هوشمند AJAX

**فاز:** phase-08-ajax-filters-and-audit · **وضعیت:** `[PENDING]`

## شرح تسک
- `inc/Ajax/LiveSearch.php` — جستجوی زنده (product/projects/post) با Nonce + rate limit + escaping کامل؛ اتصال به data-hookهای search-modal میرور
- `inc/Ajax/SmartFilters.php` — فیلتر محصولات: متن، product_cat، محدوده قیمت (_price)، true_sale، pa_* انتخابی، sort، per-page؛ فیلتر پروژه‌ها: project_type، city + شمارنده تعداد
- رندر سمت سرور همان کارت‌های template-parts (partial response) — بدون رفرش صفحه
- هدف پرفورمنس: پاسخ < ۲۰۰ms روی داده تستی (cache transients)

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] تست امنیت: SQLi/XSS/Nonce/Capability (آبجکت‌های WP_Query با escaping)
- [ ] تست پاسخ JSON/partial و به‌روزرسانی گرید با hookهای موجود
- [ ] بنچمارک ساده روی دیتاست fixture

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
