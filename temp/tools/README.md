# QA Harness Tools — ابزارهای تست پروژه

## اجرا
```bash
bash temp/tools/setup-harness.sh        # نصب/بازسازی کامل (PHP+Woo-less WP+WP-CLI)
export PATH="$PWD/temp/tools:$PATH"     # فعال‌سازی php و wp در شل جاری
php -v                                  # PHP 8.4.27 (WASM)
php -l path/to/file.php                 # lint
wp core version                         # WP-CLI روی نصب تست temp/wp
wp eval 'echo home_url();'              # اجرای کد در contexto WP
```

## معماری
- **PHP:** `@php-wasm/node@3.1.62` (بیلد WASM پروژه WordPress Playground) — چون apt/wordpress.org در سندباکس مسدود است. همه اکستنشن‌های لازم (sqlite, mbstring, dom, curl, gd, zip, mysqli) موجود است. محدودیت: `proc_open` ندارد.
- **WordPress تست:** نسخه 7.1.4 در `temp/wp/` با دیتابیس SQLite (drop-in `wp-sqlite-db`). فقط برای QA — روی سایت واقعی هرگز استفاده نشود.
- **WP-CLI:** نسخه 2.12.0 از ریپوی `wp-cli/builds` گیت‌هاب (blob API).
- `wp-config.php` قالب در همین پوشه نگهداری و توسط setup به `temp/wp/` کپی می‌شود.

## فایل‌های gitignore شده (بازتولیدپذیر)
`node_modules/`, `bin/`, `../wp/` — با `setup-harness.sh` بازسازی می‌شوند.

## برنامه اتمیک
- داده تسک‌ها: `temp/tools/planning/phase_01.py` .. `phase_08.py`
- بازتولید برنامه/تست‌ها: `python3 temp/tools/generate_phase_tree.py`
- خروجی: ۱۷۵ تسک اتمیک + ۱۷۵ اسکلت تست + `MASTER_PLAN.md` + `ATOMIC_TASK_INDEX.md` + `LEGACY_TASK_MAP.md`
- این جنریتور فقط برنامه‌ریزی است و هیچ کد قالبی تولید نمی‌کند.

## افزودن ابزارهای بعدی (فازهای آینده)
- WooCommerce برای تست فاز ۶: از ریپوی گیت‌هاب (نیاز به بررسی بیلد).
- تست‌ها: اسکریپت‌های plain PHP در `temp/phase-XX/tests/` (بدون نیاز به PHPUnit؛ قابل اجرا با `php`).
