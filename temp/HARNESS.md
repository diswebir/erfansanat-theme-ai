# HARNESS — ابزار اجرای قالب بدون وردپرس نصب‌شده

همه ابزارها در `tools/harness/` هستند و روی **php-wasm** (`@php-wasm/node` 3.1.62) اجرا می‌شوند.
یک نسخه واقعی WordPress 6.7.1 + SQLite در `/home/user/.cache/harness/wpn/` نصب است (خارج از ریپو، در snapshot نمی‌ماند).

## راه‌اندازی مجدد (اگر snapshot تازه شد)

```bash
npm install                       # @php-wasm/node + universal
bash tools/harness/setup-wp.sh    # ساخت wp-config، drop-in SQLite، نصب وردپرس
node tools/harness/run-php.mjs tools/harness/setup-site.php  # صفحات پایه + فعال‌سازی قالب
node tools/harness/run-php.mjs tools/harness/setup-site.php
ln -sfn "$PWD/theme" /home/user/.cache/harness/wpn/wp-content/themes/erfansanat
```

## دستورهای روزمره

| کار | دستور |
|---|---|
| رندر یک URL | `node tools/harness/fetch.mjs "/"` |
| رندر + ذخیره HTML | `node tools/harness/render-check.mjs "/" /tmp/home.html` |
| سنجش بودجه عملکرد | `node tools/harness/perf.mjs "/" "/contact/"` |
| بررسی بودجه | `node tools/harness/run-php.mjs tools/harness/gate.php` |
| اجرای یک تست | `node tools/harness/run-tests.mjs temp/phase-01-core-and-assets/tests/test-task-01.php` |
| اجرای کل فاز (تک‌پروسه) | `node tools/harness/run-phase.mjs temp/phase-01-core-and-assets/tests` |
| گیت کامل فاز | `node tools/harness/phase-gate.mjs 01 "/"` |
| اجرای اسکریپت PHP دلبخواه | `node tools/harness/run-php.mjs /tmp/script.php` |
| تولید WebP/Png دسته‌ای | ویرایش `tools/harness/img-jobs.json` سپس `node tools/harness/run-php.mjs tools/harness/img-batch.php` |

## قواعد سخت این محیط

1. **بوت هر پروسه PHP-wasm ~۹۰ تا ۱۲۰ ثانیه** طول می‌کشد → `timeout` دستورها را ≥ ۲۶۰ ثانیه بگیرید و تا حد امکان چند تست را در **یک** پروسه اجرا کنید.
2. `argv` و `getenv()` به اسکریپت PHP نمی‌رسند → پارامترها را در یک فایل JSON بگذارید (`fetch.json`, `phase-run.json`, `img-jobs.json`).
3. `shell_exec`/`proc_open` در wasm کار نمی‌کند → هر چیزی که به پروسه دیگر نیاز دارد را در **Node** انجام دهید (مثل `perf.mjs`).
4. مسیرها باید **مطلق** باشند؛ مسیر نسبی در wasm به cwd دیگری حل می‌شود.
5. `WP_DEBUG=true` است؛ هر Warning/Deprecated در `warnings[]` گزارش می‌شود و بودجه gate صفر است.
6. سنجه زمان: `theme_ms` (از `template_redirect` تا پایان) سنجه قالب است؛ `time_ms` شامل بوت وردپرس است و در بودجه ۵۰۰ms حساب نمی‌شود.

## mu-plugin هارنس

`wp-content/mu-plugins/es-timing.php` فقط در harness نصب است (بخشی از ریپو نیست) و زمان `template_redirect` و `wp_footer` را برای محاسبه `theme_ms` ثبت می‌کند.
