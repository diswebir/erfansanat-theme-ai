# Task 04.01 — برش header.php و footer.php

**فاز:** phase-04-header-footer-navigation · **وضعیت:** `[PENDING]`

## شرح تسک
- برش دقیق بلوک `<header data-navbar-header=…>` میرور به `header.php` و بلوک `<footer>` به `footer.php`
- تعویض مقادیر هاردکد با تنظیمات داشبورد (لوگو، تلفن، آدرس، سوشال، متن فوتر، لینک‌های ستون‌ها) + Fallback میرور
- تگ‌های Hreflang در `<head>` از متاهای hrefmeta_* (هماهنگ با hreflangpch)
- فراخوانی استاندارد wp_head(), language_attributes(), body_class(), wp_body_open(), wp_footer()
- لوگو: custom-logo یا تنظیم داشبورد با fallback ese-logo.svg میرور

## فایل‌های هدف
- (در گام THINK & PLAN دقیق می‌شود)

## Pass Conditions (چک‌لیست پاس شدن)
- [ ] تطابق ۱۰۰٪ DOM هدر/فوتر خروجی با میرور (مقایسه خط‌به‌خط ساختار، صرف‌نظر از محتوای داینامیک)
- [ ] بدون خطای PHP؛ حفظ همه data-hookها

## چرخه اجرا
- [ ] READ MEMORY
- [ ] THINK & PLAN
- [ ] WRITE TEST
- [ ] IMPLEMENT
- [ ] RUN TEST & CHECK CONDITIONS
- [ ] UPDATE MEMORY + COMMIT
