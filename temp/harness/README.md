# WP Stubs Harness — زیرساخت تست واحد بدون وردپرس

## هدف
تست‌های قالب روی **PHP 8.4 wasm** (`/home/user/tools/php -f`) اجرا می‌شوند و وردپرس در دسترس نیست.
این فایل یک لایه سبک از توابع وردپرس را شبیه‌سازی می‌کند تا کلاس‌های قالب به‌صورت واحد قابل تست باشند.

## نحوه استفاده در تست
```php
$root = dirname(__DIR__, 3);            // temp/phase-XX/tests → ریشه ریپو
require $root . '/temp/harness/wp-stubs.php';
es_stubs_reset();                        // وضعیت ایزوله هر تست
$GLOBALS['__es_theme_dir'] = $root;      // ریشه قالب برای get_template_directory()
require $root . '/functions.php';        // بوت واقعی قالب (اختیاری)
```

## توابع موجود
> ⚠️ اسکریپت تست هنگام اجرا به `/tmp/script.php` کپی می‌شود؛ مسیر واقعی فایل تست را از ثابت‌های تزریق‌شده بگیرید: `dirname(ES_SCRIPT_PATH, 4)` (چهار سطح بالاتر: نام فایل + tests + phase + temp) — نه `__DIR__`.
**هوک‌ها:** `add_action` `remove_action` `add_filter` `remove_filter` `apply_filters` (کال‌بک‌ها واقعاً اجرا می‌شوند) `do_action`
**آپشن:** `get_option` `update_option` + `es_stubs_set_option()`
**قالب:** `add_theme_support` `get_theme_support` `register_nav_menus` `add_image_size` `locate_template` (با override از `$GLOBALS['__es_locate_override']`) `get_template_directory(_uri)` `get_stylesheet_directory` `get_header/footer` `wp_head/footer` `body_class` `wp_body_open` `language_attributes` `bloginfo`
**دارایی‌ها:** `wp_enqueue_style/script` `wp_dequeue_style/script` `wp_localize_script` — همه در `$GLOBALS['__wp_hooks']` ثبت و قابل بازرسی‌اند
**امنیت/زمینه:** `is_admin` `is_user_logged_in` `current_user_can` (با فلگ `$GLOBALS['__wp_*']`) `wp_create_nonce` `esc_html/attr/url/url_raw/textarea` `wp_kses_post` `sanitize_text_field` `sanitize_hex_color` `absint` `wp_send_json_success/error`
**متفرقه:** `trailingslashit` `untrailingslashit` `admin_url` `home_url` `flush_rewrite_rules` (شمارنده) `__` `esc_html__` `esc_attr__` `_e` `esc_html_e` `esc_attr_e` `__return_empty_string/false/true/null`

## بازرسی ثبت‌ها در تست
```php
$GLOBALS['__wp_hooks']['removed_actions']   // [['hook','cb','priority'], …]
$GLOBALS['__wp_hooks']['dequeued_styles']   // ['wp-block-library', …]
$GLOBALS['__wp_hooks']['enqueued_scripts']  // [handle => ['src','deps','ver','args']]
$GLOBALS['__wp_hooks']['menus']             // [location => label]
$GLOBALS['__wp_hooks']['flushed']           // تعداد flush_rewrite_rules
```

## قواعد
1. هرگز داخل کد قالب (`inc/`، قالب‌ها) require نشود — فقط تست‌ها.
2. رفتار هر تابع باید با وردپرس واقعی منطبق بماند؛ هنگام افزودن stub جدید، همین README به‌روز شود.
3. هر تست در ابتدا `es_stubs_reset()` بزند تا از نشت وضعیت بین تسک‌ها جلوگیری شود.
