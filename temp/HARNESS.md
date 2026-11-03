# HARNESS — محیط اجرا و تست (این سندباکس)

## ۱. ابزارهای موجود
| ابزار | نسخه | وضعیت |
|---|---|---|
| Node.js | v22.22.3 | ✅ |
| npm | 10.9.8 | ✅ (فقط `registry.npmjs.org`) |
| Python 3 | 3.11.2 | ✅ |
| git / curl / unzip / zip / gcc / make | — | ✅ |
| **PHP** | 8.3 (wasm, `@php-wasm/node`) | ✅ از طریق `tools/harness/php-cli.mjs` |
| MySQL / MariaDB | — | ❌ (سرویس‌های apt مسدودند) → **SQLite drop-in** جایگزین شد |
| WP-CLI | — | ❌ (دانلود release از `objects.githubusercontent.com` مسدود است) |
| WordPress | 6.7.1 | ✅ در `/home/user/.cache/harness/wpn` (+ `wp-content/db.php` = SQLite) |
| WooCommerce | — | ⚠️ باید از طریق `downloads.wordpress.org` یا GitHub Release دریافت شود (در حال بررسی) |

## ۲. مسیرهای مهم (خارج از مخزن، پایدار در این سندباکس)
```
/home/user/.cache/harness/wpn/          ← WordPress 6.7.1 + SQLite + wp-config.php (WP_DEBUG=true)
/home/user/.cache/harness/wpn/wp-content/database/.ht.sqlite
/home/user/.cache/npm/node_modules/@php-wasm/   ← رانتایم PHP
```

## ۳. اجرای PHP
```bash
cd /home/user/.cache/npm
node php-cli.mjs /path/to/script.php arg1 arg2   # اجرای اسکریپت
node php-cli.mjs install-wp.php                  # نصب اولیه (یک‌بار انجام شد)
```
> هر اجرا ~۲۵–۳۰ ثانیه (بوت wasm). تست‌های یک تسک را در **یک** فایل PHP تجمیع کنید.

## ۴. وضعیت نصب وردپرس
- `wp_install()` انجام شد: کاربر `admin`، رمز `admin123`، `home/siteurl = http://localhost:8080`
- نسخه: 6.7.1 — سایت‌آدرس در `wp-config.php` قابل تغییر است
- `WP_DEBUG=true`, `WP_DEBUG_LOG=true` → لاگ در `wp-content/debug.log`

## ۵. محدودیت‌های شناخته‌شده
1. بدون وب‌سرور PHP واقعی → رندر HTML کامل فقط با بوت‌استرپ دستی `template-loader` یا `wp_remote` شبیه‌سازی می‌شود (اسکریپت `tools/harness/render.php` در فاز ۱ ساخته می‌شود).
2. بدون MySQL → تست کوئری‌های MySQL-محور محدود؛ اما لایه SQLite-شده وردپرس برای ۹۵٪ تست‌ها کافی است.
3. هر پروسه PHP-wasm ~۲۵ ثانیه راه‌اندازی دارد → تست‌ها به‌صورت دسته‌ای.
