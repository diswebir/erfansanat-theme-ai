# Task 03.03 — متاباکس پروژه‌ها، FAQ دسته‌بندی، Hreflang و بازدید

**فاز:** phase-03-cpt-and-legacy-meta · **وضعیت:** `[PENDING]`

## شرح تسک
- متاباکس پروژه: گالری `second-image`…`six-image` (املای دقیق foure-image) + گالری مدرن چندتصویری همگام با همین کلیدها + `finish-pro-time` (text) + `mortabet_mahsole_2` (انتخاب چند محصول)
- متاباکس ترم `product_cat`: `thumbnail` (media) + Repeater `faq` (name_question/answer_question)
- متاباکس Hreflang روی post/page/product/projects/services و ترم‌های اصلی: hrefmeta_fa/en/ar + same_key_hrefl + ستون ادمین مرتب‌سازی
- شمارنده بازدید `number2-viewc` (+ fallback `number-viewc`) روی post/projects/product/services با هوک `template_redirect` + ستون ادمین

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] roundtrip خواندن/نوشتن روی تمام کلیدهای این تسک (post و term fixture)
- [ ] افزایش بازدید فقط برای بازدیدکننده واقعی (بدون بات/پرلود) و بدون افزایش در پیشخوان

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
