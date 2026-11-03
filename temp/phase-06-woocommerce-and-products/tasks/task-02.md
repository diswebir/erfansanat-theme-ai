# Task 06.02 — آرشیو محصولات و دسته‌بندی‌ها

**فاز:** phase-06-woocommerce-and-products · **وضعیت:** `[PENDING]`

## شرح تسک
- `archive-product.php` (برگه shop)، `taxonomy-product_cat.php` (هیرو + توضیح + FAQ دسته + thumbnail)، `taxonomy-product_tag.php`
- نوار فیلتر میرور (search/category/ip/voltage/purchase-mode/price-range/sort/per-page) رندر می‌شود (اتصال AJAX در فاز ۸؛ در این فاز با GET params کار کند)
- کارت محصول `card-product.php` (price، sku، purchasable badge، rating، views)
- صفحه‌بندی میرور (pagination-*) با paginate_links

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] آرشیو /shop/ و /product_cat/…/ رندر صحیح با کارت‌های میرور
- [ ] فیلترهای GET در فاز جاری پاسخ صحیح می‌دهند (تست WP_Query args)

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
