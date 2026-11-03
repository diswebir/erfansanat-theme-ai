# Task 1.6 — DeBloat: حذف کامل CSS/JS بلاک‌ها، ایموجی، oEmbed و متاتگ‌های اضافی

| فیلد | مقدار |
|---|---|
| فاز | 1 — زیرساخت هسته قالب، Autoloader و انتقال بی‌نقص Assets استاتیک |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 1.5` |
| وضعیت | `[PENDING]` |
| تست | `tests/test-task-06.php` |

## ۱. هدف

ماژول `Performance/DeBloat` که فرانت را از بار بلاک‌های گوتنبرگ، ایموجی، `wp-embed`, `dashicons` (برای مهمان), `global-styles`, `classic-theme-styles`, و متاتگ‌های `generator/rsd/wlwmanifest/shortlink/rest` پاک می‌کند — **فقط فرانت و بدون دست‌زدن به ادمین** (`is_admin()` guard در همه‌جا).

## ۲. فایل‌های هدف

- `inc/Performance/DeBloat.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. `wp_enqueue_scripts` (اولویت ۱۰۰): dequeue `wp-block-library`, `wp-block-library-theme`, `wc-blocks-style` (در صورت وجود), `global-styles`, `classic-theme-styles`.
2. حذف `wp_enqueue_global_styles` و `wp_enqueue_global_styles_custom_css` و `wp_enqueue_block_support_styles` از هوک‌ها.
3. حذف ایموجی: `print_emoji_detection_script`, `wp_enqueue_emoji_styles`, فیلترهای `the_content_feed`/`wp_mail`/`comment_text_rss`.
4. حذف `wp_oembed_add_discovery_links`, `wp_oembed_add_host_js` (در صورت نبود نیاز embedding).
5. حذف `wp_generator`, `rsd_link`, `wlwmanifest_link`, `wp_shortlink_wp_head`, `rest_output_link_wp_head`.
6. `dashicons` فقط برای کاربران وارد‌شده (ادمین‌بار).
7. همه‌چیز با `if ( is_admin() ) return;` محافظت شود.

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — در خروجی `/` هیچ مسیر `wp-includes/css/dist/block-library` نباشد
- [ ] **P2** — هیچ `wp-emoji` / `wp-embed` در سورس نباشد
- [ ] **P3** — `<meta name="generator">` حذف شده باشد ولی `wp_head()` بقیه متاها را رندر کند
- [ ] **P4** — در پیشخوان و ویرایشگر هیچ تغییری رخ ندهد (تست `is_admin`)
- [ ] **P5** — تعداد `stylesheet` لینک‌ها ≤ ۳

## ۵. تست‌های خودکار

- `اسکن سورس خروجی`
- `ایمنی ادمین`
- `حذف متاتگ‌ها`
- `شمارش استایل‌ها`

## ۶. چرخه اجرایی (۶ گام اجباری)

| گام | اقدام |
|---|---|
| `[READ MEMORY]` | `temp/PROJECT_MEMORY.md` + `temp/STATIC_TEMPLATE_MAP.md` + این فایل |
| `[THINK & PLAN]` | تحلیل مارک‌آپ/داده مربوطه و طرح اتصال |
| `[WRITE TEST]` | تکمیل `tests/test-task-XX.php` (تبدیل `pending()` به `check()`) |
| `[IMPLEMENT]` | پیاده‌سازی بدون حذف کلاس/DOM/هوک |
| `[RUN TEST]` | اجرا تا ۱۰۰٪ PASS + ثبت در `QA_REFLECTION_LOG.md` |
| `[UPDATE MEMORY]` | آپدیت `PROJECT_MEMORY.md`, `MASTER_PLAN.md` + کامیت |

## ۷. ضدالگوها (Anti-patterns) — انجام نده

- طراحی مجدد مارک‌آپ یا افزودن کلاس Tailwind جدید (قانون R1)
- تغییر فایل `assets/js/main.js` یا `assets/css/styles.css` (md5 باید ثابت بماند)
- استفاده از `TODO`/`...`/حذف بخشی از HTML برای کوتاه‌سازی (قانون R6)
- خواندن/نوشتن متا با املای «تصحیح‌شده» (مثل `english_title` به‌جای `english_titl`)
