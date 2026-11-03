# Task 3.5 — متاباکس محصول — تب شناسه‌های صنعتی + Quick Edit

**فاز:** 03 — معماری داده‌ها: CPTها، تکسونومی‌ها و متاباکس‌های سازگار با دیتابیس زنده
**وضعیت:** `[PASSED]`
**تست:** `temp/phase-03-cpt-and-legacy-meta/tests/test-task-05.php`

## 🎯 هدف
مدیریت ۶ شناسه قانونی با املای دقیق دیتابیس + ویرایش سریع از لیست محصولات

## 🔗 وابستگی‌ها
- Task 1.2

## 📎 مراجع (منابع برش/استخراج)
- temp/DATA_BLUEPRINT.md §۴ (شناسه‌ها)

## 📤 فایل‌های خروجی
- `inc/Admin/ProductMetaboxes.php (هسته + تب identifiers)`
- `inc/Admin/QuickEdit.php`

## 🛠 مراحل پیاده‌سازی (گام به گام)
1. ثبت metabox «اطلاعات محصولات» روی product با تب‌بندی JS داخلی (data-tab)
2. تب شناسه‌ها: ۶ فیلد text با لیبل فارسی و placeholder: gs1_id، national_code، hs_id، isic_id، cpc_id، product_id
3. ذخیره با save_post_product: nonce + current_user_can('edit_post') + update_post_meta برای هر کلید (کلیدها دقیقاً همین املای دیتابیس)
4. QuickEdit: ستون «شناسه‌ها» در لیست محصولات + فیلدهای inline در ردیف quick-edit (با JS داخلی) و ذخیره از wp_ajax یا save_post با data id
5. خواندن اولیه با get_post_meta مستقیم (این کلیدها همیشه text هستند)

## ⛔ محدودیت‌ها و خط‌قرمزها
- املای کلیدها عیناً: gs1_id, national_code, hs_id, isic_id, cpc_id, product_id

## ✅ Pass Conditions (چک‌لیست پاس شدن)
- [ ] پاس شدن php -l
- [ ] roundtrip ذخیره/خواندن هر ۶ کلید (تست با stub متا)
- [ ] save با nonce و capability گارد شود
- [ ] QuickEdit ستون و فرم inline داشته باشد

## 🔄 چرخه اجرای الزامی (۶ گام)
- [ ] READ MEMORY — خواندن temp/PROJECT_MEMORY.md و این فایل
- [ ] THINK & PLAN — تحلیل مارک‌آپ مرجع و نوشتن منطق اتصال وردپرس
- [ ] WRITE TEST — تکمیل temp/phase-03-cpt-and-legacy-meta/tests/test-task-05.php
- [ ] IMPLEMENT — پیاده‌سازی بدون حذف هیچ کلاس/hook مرجع
- [ ] RUN TEST — اجرای تست تا ۱۰۰٪ پاس + ثبت در QA_REFLECTION_LOG
- [ ] UPDATE MEMORY — آپدیت PROJECT_MEMORY/MASTER_PLAN + Git Commit
