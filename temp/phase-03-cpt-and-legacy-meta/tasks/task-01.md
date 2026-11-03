# Task 03.01 — ثبت CPTها و تکسونومی‌ها با Rewrite دقیق

**فاز:** phase-03-cpt-and-legacy-meta · **وضعیت:** `[PENDING]`

## شرح تسک
- `register_post_type('projects')` با تنظیمات دقیق بلوپرینت (slug `projects`, with_front=false, show_in_rest, dashicons-screenoptions)
- `register_taxonomy('project_type')` و `('city')` متصل به projects (hierarchical, با جداسازی query var)
- ثبت CPT `services` (تصمیم Q2): public, rewrite slug `services`, has_archive=false, show_in_rest, supports title/editor/thumbnail/excerpt
- فلتر rewrite برای `product_cat` → slug `product_cat` با `with_front => true` (طبق بلوپرینت سئو)
- فلاش خودکار rewrite rules در activation hook قالب
- فیلتر دراپ‌داون `city` و `project_type` و ستون‌های مرتب‌سازی `same_key_hrefl` در لیست ادمین projects

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] URLهای نهایی: `/projects/{slug}`, `/project_type/{term}`, `/city/{term}`, `/product_cat/{term}`, `/services/{slug}` (منطبق بلوپرینت)
- [ ] بدون تداخل با برگه‌های موجود (shop, blog, services listing)
- [ ] فیلترهای ادمین کار می‌کنند (قابلیت تست با unit test روی query args)

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
