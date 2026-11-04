# Task 4.2 — `header.php`: نوار اعلان + لوگو + نوار ناوبری

| فیلد | مقدار |
|---|---|
| فاز | 4 — هدر، مگامنو، منوی موبایل و فوتر |
| اندازه | `L` — بزرگ (~۲ روز) |
| وابستگی | `Task 4.1`، `Task 2.7` |
| وضعیت | `[PASSED]` |
| تست | `tests/test-task-02.php` |

## ۱. هدف

استخراج دقیق مارک‌آپ هدر استاتیک: نوار اعلان بالایی، لوگو (ESE + نام فارسی/انگلیسی)، نوار ناوبری دسکتاپ، دکمه‌های جستجو/سبد/منو، با داده‌های داشبورد و حفظ تمام کلاس‌ها و هوک‌ها.

## ۲. فایل‌های هدف

- `header.php`
- `template-parts/header/topbar.php`
- `template-parts/header/branding.php`
- `template-parts/header/primary-nav.php`

## ۳. مراحل پیاده‌سازی (گام‌به‌گام)

1. کپی مارک‌آپ از `index.html` (بازه `<header>` تا `</header>`) با حفظ کلاس‌ها.
2. تبدیل `href="index.html"` → `home_url('/')`، `licenses-and-honors.html` → `get_permalink` برگه، `product_cat/...` → `get_term_link`.
3. داده‌های نوار اعلان از `es_option('header.announcement')`.
4. کلاس حالت فعال با `aria-current="page"` در منو (Walker در ۴.۴).
5. حذف `custom="..."` بازمانده و `data-initial-qty` تزریقی.
6. وجود `wp_head`/`wp_body_open`/`body_class` در قالب‌های والد (index).

## ۴. شروط پاس شدن (Pass Conditions)

> هر شرط باید در تست خودکار به `PASS` تبدیل شود؛ یک شرط `PENDING` یعنی تسک تمام نشده است.

- [ ] **P1** — مقایسه ساختاری DOM با استاتیک ≥ ۹۹٪ (تگ/کلاس/اتریبیوت)
- [ ] **P2** — هیچ لینک `.html` در خروجی هدر نباشد
- [ ] **P3** — بدون `custom=` در خروجی
- [ ] **P4** — نوار اعلان با toggle داشبورد حذف شود
- [ ] **P5** — `aria-label="منوی اصلی"` حفظ شود

## ۵. تست‌های خودکار

- `diff ساختاری`
- `بدون لینک html`
- `بدون custom`
- `toggle نوار`
- `aria-label`

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
