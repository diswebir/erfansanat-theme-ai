<?php
/**
 * WP Function Stubs — زیرساخت تست واحد قالب عرفان صنعت بدون وردپرس
 *
 * محیط اجرا: PHP 8.4 wasm (/home/user/tools/php -f)
 * رفتار همه توابع با هسته واقعی وردپرس منطبق طراحی شده؛ ثبت‌ها (اکشن/فیلتر/انکیو)
 * در $GLOBALS['__wp_hooks'] ثبت و در تست‌ها بازرسی می‌شوند.
 *
 * مستند کامل: temp/harness/README.md
 */
declare(strict_types=1);

if (defined('ES_WP_STUBS_LOADED')) {
    return; // گارد include چندباره
}
define('ES_WP_STUBS_LOADED', '1');

if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__) . '/');
}

$GLOBALS['__wp_options']         = [];
$GLOBALS['__wp_is_admin']        = false;
$GLOBALS['__wp_user_logged_in']  = false;
$GLOBALS['__wp_current_user_can'] = false;
$GLOBALS['__es_theme_dir']       = dirname(__DIR__, 2); // ریشه قالب (ریپو)
$GLOBALS['__es_locate_override'] = [];
$GLOBALS['__wp_hooks']           = es_stubs_fresh_hooks();

/** ساختار تازه هوک‌ها — برای ریست ایزوله تست‌ها */
function es_stubs_fresh_hooks(): array {
    return [
        'actions'          => [],
        'filters'          => [],
        'removed_actions'  => [],
        'removed_filters'  => [],
        'supports'         => [],
        'menus'            => [],
        'sizes'            => [],
        'enqueued_styles'  => [],
        'enqueued_scripts' => [],
        'dequeued_styles'  => [],
        'dequeued_scripts' => [],
        'localized'        => [],
        'flushed'          => 0,
        'json_responses'   => [],
    ];
}

/** ریست کامل وضعیت تست (هوک‌ها + آپشن‌ها + فلگ‌های زمینه) */
function es_stubs_reset(): void {
    $GLOBALS['__wp_options']         = [];
    $GLOBALS['__wp_is_admin']        = false;
    $GLOBALS['__wp_user_logged_in']  = false;
    $GLOBALS['__wp_current_user_can'] = false;
    $GLOBALS['__es_locate_override'] = [];
    $GLOBALS['__wp_hooks']           = es_stubs_fresh_hooks();
}

/** تنظیم مستقیم مقدار آپشن برای تست */
function es_stubs_set_option(string $name, mixed $value): void {
    $GLOBALS['__wp_options'][$name] = $value;
}

function add_action(string $hook_name, callable|array|string $callback, int $priority = 10, int $accepted_args = 1): bool
{
    $GLOBALS['__wp_hooks']['actions'][$hook_name][] = ['cb' => $callback, 'priority' => $priority];
    return true;
}

function remove_action(string $hook_name, callable|array|string $callback, int $priority = 10): bool
{
    $GLOBALS['__wp_hooks']['removed_actions'][] = ['hook' => $hook_name, 'cb' => $callback, 'priority' => $priority];
    return true;
}

function add_filter(string $hook_name, callable|array|string $callback, int $priority = 10, int $accepted_args = 1): bool
{
    $GLOBALS['__wp_hooks']['filters'][$hook_name][] = ['cb' => $callback, 'priority' => $priority];
    return true;
}

function remove_filter(string $hook_name, callable|array|string $callback, int $priority = 10): bool
{
    $GLOBALS['__wp_hooks']['removed_filters'][] = ['hook' => $hook_name, 'cb' => $callback, 'priority' => $priority];
    return true;
}

function apply_filters(string $hook_name, mixed $value, mixed ...$args): mixed
{
    $list = $GLOBALS['__wp_hooks']['filters'][$hook_name] ?? [];
    usort($list, static fn(array $a, array $b): int => $a['priority'] <=> $b['priority']);
    foreach ($list as $entry) {
        $value = call_user_func($entry['cb'], $value, ...$args);
    }
    return $value;
}

function do_action(string $hook_name, mixed ...$args): void
{
    $list = $GLOBALS['__wp_hooks']['actions'][$hook_name] ?? [];
    usort($list, static fn(array $a, array $b): int => $a['priority'] <=> $b['priority']);
    foreach ($list as $entry) {
        call_user_func($entry['cb'], ...$args);
    }
}

function add_theme_support(string $feature, mixed $args = null): void
{
    $GLOBALS['__wp_hooks']['supports'][$feature] = $args ?? true;
}

function get_theme_support(string $feature): mixed
{
    return $GLOBALS['__wp_hooks']['supports'][$feature] ?? false;
}

function register_nav_menus(array $locations = []): void
{
    $GLOBALS['__wp_hooks']['menus'] = array_merge($GLOBALS['__wp_hooks']['menus'], $locations);
}

function add_image_size(string $name, int $width, int $height, bool $crop = false): void
{
    $GLOBALS['__wp_hooks']['sizes'][$name] = ['w' => $width, 'h' => $height, 'crop' => $crop];
}

function get_option(string $option, mixed $default = false): mixed
{
    return $GLOBALS['__wp_options'][$option] ?? $default;
}

function update_option(string $option, mixed $value, ?bool $autoload = null): bool
{
    $GLOBALS['__wp_options'][$option] = $value;
    return true;
}

function wp_enqueue_style(string $handle, string $src = '', array $deps = [], string|bool|null $ver = false, string|bool|null $media = 'all'): void
{
    $GLOBALS['__wp_hooks']['enqueued_styles'][$handle] = ['src' => $src, 'deps' => $deps, 'ver' => $ver, 'media' => $media];
}

function wp_dequeue_style(string $handle): void
{
    $GLOBALS['__wp_hooks']['dequeued_styles'][] = $handle;
}

function wp_enqueue_script(string $handle, string $src = '', array $deps = [], string|bool|null $ver = false, array|bool $args = []): void
{
    $GLOBALS['__wp_hooks']['enqueued_scripts'][$handle] = ['src' => $src, 'deps' => $deps, 'ver' => $ver, 'args' => $args];
}

function wp_dequeue_script(string $handle): void
{
    $GLOBALS['__wp_hooks']['dequeued_scripts'][] = $handle;
}

function wp_localize_script(string $handle, string $object_name, array $l10n): bool
{
    $GLOBALS['__wp_hooks']['localized'][$handle] = ['name' => $object_name, 'data' => $l10n];
    return true;
}

function esc_html(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_attr(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_textarea(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_url(string $url): string
{
    $url = trim($url);
    if ($url === '' || preg_match('/^\s*(javascript|data|vbscript):/i', $url)) {
        return '';
    }
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}

function esc_url_raw(string $url): string
{
    return esc_url($url);
}

function wp_kses_post(string $content): string
{
    return $content; // در تست، پاکسازی به عهده خود کد است
}

function sanitize_text_field(string $str): string
{
    return trim(strip_tags($str));
}

function sanitize_hex_color(string $color): string
{
    return preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $color) ? $color : '';
}

function absint(mixed $maybeint): int
{
    return abs((int) $maybeint);
}

function is_admin(): bool
{
    return $GLOBALS['__wp_is_admin'];
}

function is_user_logged_in(): bool
{
    return $GLOBALS['__wp_user_logged_in'];
}

function current_user_can(string $capability, mixed ...$args): bool
{
    return $GLOBALS['__wp_current_user_can'];
}

function wp_send_json_success(mixed $data = null, int $status_code = 200): never
{
    $GLOBALS['__wp_hooks']['json_responses'][] = ['success' => true, 'data' => $data, 'code' => $status_code];
    exit(0);
}

function wp_send_json_error(mixed $data = null, int $status_code = 400): never
{
    $GLOBALS['__wp_hooks']['json_responses'][] = ['success' => false, 'data' => $data, 'code' => $status_code];
    exit(1);
}

function trailingslashit(string $value): string
{
    return rtrim($value, '/\\') . '/';
}

function untrailingslashit(string $value): string
{
    return rtrim($value, '/\\');
}

function get_template_directory(): string
{
    return $GLOBALS['__es_theme_dir'];
}

function get_template_directory_uri(): string
{
    return 'https://test.example/wp-content/themes/erfansanat';
}

function get_stylesheet_directory(): string
{
    return get_template_directory();
}

function locate_template(array|string $template_names, bool $load = false, bool $require_once = true, array $args = []): string
{
    foreach ((array) $template_names as $tpl) {
        if (isset($GLOBALS['__es_locate_override'][$tpl])) {
            return $GLOBALS['__es_locate_override'][$tpl];
        }
        $file = rtrim(get_template_directory(), '/') . '/' . ltrim($tpl, '/');
        if (is_file($file)) {
            return $file;
        }
    }
    return '';
}

function admin_url(string $path = ''): string
{
    return 'https://test.example/wp-admin/' . ltrim($path, '/');
}

function home_url(string $path = ''): string
{
    if ($path === '' || $path === '/') {
        return 'https://test.example/';
    }
    return 'https://test.example/' . ltrim($path, '/');
}

function wp_create_nonce(string|int $action = -1): string
{
    return 'test-nonce-' . $action;
}

function flush_rewrite_rules(bool $hard = true): void
{
    $GLOBALS['__wp_hooks']['flushed']++;
}

function __($text, string $domain = 'default'): string
{
    return $text;
}

function esc_html__($text, string $domain = 'default'): string
{
    return esc_html((string) $text);
}

function esc_attr__($text, string $domain = 'default'): string
{
    return esc_attr((string) $text);
}

function _e($text, string $domain = 'default'): void
{
    echo (string) $text;
}

function esc_html_e($text, string $domain = 'default'): void
{
    echo esc_html((string) $text);
}

function esc_attr_e($text, string $domain = 'default'): void
{
    echo esc_attr((string) $text);
}

function __return_empty_string(): string
{
    return '';
}

function __return_false(): bool
{
    return false;
}

function __return_true(): bool
{
    return true;
}

function __return_null(): null
{
    return null;
}

function get_header($name = null, array $args = []): void
{
}

function get_footer($name = null, array $args = []): void
{
}

function wp_head(): void
{
}

function wp_footer(): void
{
}

function language_attributes(): void
{
}

function body_class(string $css_class = ''): void
{
}

function wp_body_open(): void
{
}

function bloginfo(string $show = ''): void
{
}
