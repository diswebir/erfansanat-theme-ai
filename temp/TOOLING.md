# ابزارهای توسعه و محدوده آزمون

## راه‌اندازی

از ریشه مخزن:

```bash
bash tools/setup.sh
bash tools/test-stage0.sh
```

دانلودها فقط از رجیستری npm و GitHub API/codeload انجام می‌شوند. فایل‌های اجرایی و dependencyها داخل `~/.cache/erfansanat-tools` نصب می‌شوند و وارد بسته قالب نمی‌شوند. برای محل دیگر، `ERFANSANAT_TOOL_CACHE` را پیش از اجرا تنظیم کنید.

## نسخه‌های آماده‌شده

| ابزار | نسخه | روش/محدوده |
|---|---|---|
| PHP | 8.4.27 | PHP.wasm از `@php-wasm/cli` نسخه 3.1.62؛ مناسب lint و تست واحد، نه شاهد عملکرد سرور تولید |
| WP-CLI | 2.12.0 | PHAR با Git blob SHA `4432e71d61f7425ed028b23e58b44ad5ecb0f5bf`؛ اجرای `--info` موفق |
| PHPUnit | 12.4.2 | سورس و ۲۴ وابستگی مطابق lock رسمی؛ autoloader مخصوص ابزار، خارج از قالب |
| PHP_CodeSniffer | 3.13.5 | tag `3.13.5`؛ بررسی ثابت نسخه در `src/Config.php` |
| WPCS | 3.4.1 | commit `ec2ff942335f33683a5957a85d138753876a05cf` |
| PHPCSUtils | 1.2.3 | commit `5f35d9408c54d7b529501f3c688b6eae562aea1f` |
| PHPCSExtra | 1.5.1 | commit `39467533fdb742446d68c1d10ac33d625ee0311c` |
| Node.js | 24.18.0 | runtime ابزار طبق engine موردنیاز PHP.wasm |
| npm | 11.16.0 | نسخه قفل‌شده برای بازتولید ابزار |
| xmllint | libxml2 از xmllint-wasm 5.3.0 | adapter فایل‌محور برای XML/SVG؛ همه گزینه‌های CLI بومی را پیاده نمی‌کند |
| curl | ابزار سیستمی | موجود و برای دریافت مجاز ابزار استفاده شد |

## wrapperها

- `tools/php`: PHP 8.4.
- `tools/wp`: WP-CLI؛ برای اطلاعات عمومی `tools/wp --info --allow-root`.
- `tools/phpunit`: PHPUnit 12.4.2.
- `tools/phpcs`: استانداردهای PSR و WordPress ثبت شده‌اند.
- `tools/xmllint --noout FILE`: بررسی well-formedness فایل XML/SVG.
- `tools/scan_static.py`: اسکن بدون تغییر منبع و تولید inventory/map.

## مرزهای قابل ادعا

- تست‌های Stage 0 سلامت آرشیو، دست‌نخورده بودن مرجع، نگاشت همه HTMLها، منابع محلی، JS، JSON-LD و ابزار PHP را می‌سنجند.
- هیچ تست وردپرس/ووکامرس، فعال‌سازی قالب، پرداخت بانکی، سرعت روی سایت زنده یا یکپارچگی داده واقعی در این مرحله انجام نشده است.
- محیط WebAssembly جای استیجینگ با PHP/وب‌سرور/دیتابیس هم‌مشخصات تولید را نمی‌گیرد. هدف پاسخ زیر ۲۰۰ms باید آنجا اندازه‌گیری شود.
- هیچ رمز، token یا اتصال دیتابیس تولید درخواست یا ذخیره نشده است.

## محیط وردپرس محلی

- `bash tools/setup-wordpress.sh`: نصب/بازاستفاده از وردپرس، ووکامرس، SQLite، زبان فارسی و داده ساختگی در `.local/`.
- `tools/php -S 0.0.0.0:8080 -t .local/wordpress tools/local/router.php`: پیش‌نمایش وردپرس.
- `bash tools/test-local.sh`: آزمون runtime و HTTP محیط محلی.
- `.local/` شامل هسته، افزونه‌ها، دیتابیس، رسانه و اعتبارنامه است و در Git ثبت نمی‌شود.
- `tools/local/runtime-manifest.json` نسخه‌ها و منشأ دانلودها را pin می‌کند.
