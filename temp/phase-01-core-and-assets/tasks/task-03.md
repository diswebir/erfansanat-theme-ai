# Task 01.03 — انتقال Assets و AssetManager با Enqueue شرطی

**فاز:** phase-01-core-and-assets · **وضعیت:** `[PENDING]`

## شرح تسک
- کپی `styles.css`, `main.js`, ۳ فونت و `images/` از میرور به `assets/` قالب با همان ساختار (حفظ مسیر نسبی `../fonts/`)
- ساخت `inc/Frontend/AssetManager.php` با متد شرطی per-template بر اساس `STATIC_TEMPLATE_MAP.md`
- نسخه‌گذاری همه دارایی‌ها با `filemtime`
- Preload فونت‌ها + `dns-prefetch` صفر (همه چیز local)
- متغیر JS (wp_localize_script یا data-attr) برای URLهای AJAX در فاز ۸ (اسکلت آماده)

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] تمام فایل‌های CSS/JS/فونت با HTTP 200 لود می‌شوند (تست مسیر با sandbox)
- [ ] هیچ url( نسبی داخل CSS نشکند (ساختار پوشه حفظ شده)
- [ ] ورژن query = filemtime هر فایل

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
