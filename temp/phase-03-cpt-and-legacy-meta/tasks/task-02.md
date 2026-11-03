# Task 03.02 — موتور متاباکس محصولات (سازگار با دیتای JetEngine)

**فاز:** phase-03-cpt-and-legacy-meta · **وضعیت:** `[PENDING]`

## شرح تسک
- ساخت `inc/Admin/ProductMetaboxes.php` — تب‌بندی‌شده روی `product`: شناسه‌ها (gs1_id…product_id با Quick Edit)، عناوین (zireonvan, english_titl, before-sale-c, true_sale)، فایل/گالری (product-catalog-file, galerry_produ + fallback _product_image_gallery/gallery-filed/image-2..6)، روابط (project-ids, usage_countr با فیلد انتخاب چندگانه)
- ۴ Repeater: product-feature, product-feature2_copy (vaule_of_variable!), usage_product, faq — خواندن/نوشتن با فرمت آرایه سریالایز/JSON سازگار `{'item-0':{…}}`
- حفظ فرمت موجود: اگر دیتا آرایه سریالایز بود همان‌طور ذخیره شود؛ اگر JSON بود JSON بماند (دو-مد هوشمند)
- Quick Edit برای شناسه‌ها در لیست محصولات

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] خواندن نمونه دیتای JetEngine (fixture آرایه و JSON) بدون خطا
- [ ] نوشتن مجدد با همان فرمت بدون تخریب (roundtrip test روی fixture)
- [ ] تمام کلیدها با املای دقیق بلوپرینت (تست رشته‌ای)

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
