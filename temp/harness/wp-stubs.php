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

if (!function_exists('es_stubs_fresh_hooks')) {
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
}

$GLOBALS['__wp_options']         = [];
$GLOBALS['__wp_is_admin']        = false;
$GLOBALS['__wp_user_logged_in']  = false;
$GLOBALS['__wp_current_user_can'] = false;
$GLOBALS['__es_theme_dir']       = dirname(__DIR__, 2); // ریشه قالب (ریپو)
$GLOBALS['__es_locate_override'] = [];
$GLOBALS['__es_stub_get_posts_args'] = [];
$GLOBALS['__wp_hooks']           = [
    'actions' => [], 'filters' => [], 'removed_actions' => [], 'removed_filters' => [],
    'supports' => [], 'menus' => [], 'sizes' => [],
    'enqueued_styles' => [], 'enqueued_scripts' => [],
    'dequeued_styles' => [], 'dequeued_scripts' => [], 'localized' => [],
    'flushed' => 0, 'json_responses' => [],
];

/** ساختار تازه هوک‌ها — برای ریست ایزوله تست‌ها */

/** ریست کامل وضعیت تست (هوک‌ها + آپشن‌ها + فلگ‌های زمینه) */
if (!function_exists('es_stubs_reset')) {
function es_stubs_reset(): void {
    $GLOBALS['__wp_options']         = [];
    $GLOBALS['__wp_is_admin']        = false;
    $GLOBALS['__wp_user_logged_in']  = false;
    $GLOBALS['__wp_current_user_can'] = false;
    $GLOBALS['__es_locate_override'] = [];
$GLOBALS['__es_stub_get_posts_args'] = [];
    $GLOBALS['__wp_hooks']           = [
    'actions' => [], 'filters' => [], 'removed_actions' => [], 'removed_filters' => [],
    'supports' => [], 'menus' => [], 'sizes' => [],
    'enqueued_styles' => [], 'enqueued_scripts' => [],
    'dequeued_styles' => [], 'dequeued_scripts' => [], 'localized' => [],
    'flushed' => 0, 'json_responses' => [],
];
    // کش استاتیک es_option قالب هم باید معتبردور شود (معادل شروع ریکوئست تازه)
    if (function_exists('es_option_reset_cache')) {
        es_option_reset_cache();
    }
}
}

/** تنظیم مستقیم مقدار آپشن برای تست */
if (!function_exists('es_stubs_set_option')) {
function es_stubs_set_option(string $name, mixed $value): void {
    $GLOBALS['__wp_options'][$name] = $value;
}
}

if (!function_exists('add_action')) {
function add_action(string $hook_name, callable|array|string $callback, int $priority = 10, int $accepted_args = 1): bool
{
    $GLOBALS['__wp_hooks']['actions'][$hook_name][] = ['cb' => $callback, 'priority' => $priority];
    return true;
}
}

if (!function_exists('remove_action')) {
function remove_action(string $hook_name, callable|array|string $callback, int $priority = 10): bool
{
    $GLOBALS['__wp_hooks']['removed_actions'][] = ['hook' => $hook_name, 'cb' => $callback, 'priority' => $priority];
    return true;
}
}

if (!function_exists('add_filter')) {
function add_filter(string $hook_name, callable|array|string $callback, int $priority = 10, int $accepted_args = 1): bool
{
    $GLOBALS['__wp_hooks']['filters'][$hook_name][] = ['cb' => $callback, 'priority' => $priority];
    return true;
}
}

if (!function_exists('remove_filter')) {
function remove_filter(string $hook_name, callable|array|string $callback, int $priority = 10): bool
{
    if (isset($GLOBALS['__wp_hooks']['filters'][$hook_name]) && is_array($GLOBALS['__wp_hooks']['filters'][$hook_name])) {
        foreach ($GLOBALS['__wp_hooks']['filters'][$hook_name] as $i => $entry) {
            $cb   = $entry['cb'] ?? null;
            $same = is_object($cb) && is_object($callback) ? spl_object_id($cb) === spl_object_id($callback) : $cb === $callback;
            if ($same && (int) ($entry['priority'] ?? 10) === $priority) {
                unset($GLOBALS['__wp_hooks']['filters'][$hook_name][$i]);
            }
        }
    }
    $GLOBALS['__wp_hooks']['removed_filters'][] = ['hook' => $hook_name, 'cb' => $callback, 'priority' => $priority];
    return true;
}
}

if (!function_exists('apply_filters')) {
function apply_filters(string $hook_name, mixed $value, mixed ...$args): mixed
{
    $list = $GLOBALS['__wp_hooks']['filters'][$hook_name] ?? [];
    usort($list, static fn(array $a, array $b): int => $a['priority'] <=> $b['priority']);
    foreach ($list as $entry) {
        $value = call_user_func($entry['cb'], $value, ...$args);
    }
    return $value;
}
}

if (!function_exists('do_action')) {
function do_action(string $hook_name, mixed ...$args): void
{
    $list = $GLOBALS['__wp_hooks']['actions'][$hook_name] ?? [];
    usort($list, static fn(array $a, array $b): int => $a['priority'] <=> $b['priority']);
    foreach ($list as $entry) {
        call_user_func($entry['cb'], ...$args);
    }
}
}

if (!function_exists('add_theme_support')) {
function add_theme_support(string $feature, mixed $args = null): void
{
    $GLOBALS['__wp_hooks']['supports'][$feature] = $args ?? true;
}
}

if (!function_exists('get_theme_support')) {
function get_theme_support(string $feature): mixed
{
    return $GLOBALS['__wp_hooks']['supports'][$feature] ?? false;
}
}

if (!function_exists('register_nav_menus')) {
function register_nav_menus(array $locations = []): void
{
    $GLOBALS['__wp_hooks']['menus'] = array_merge($GLOBALS['__wp_hooks']['menus'], $locations);
}
}

if (!function_exists('add_image_size')) {
function add_image_size(string $name, int $width, int $height, bool $crop = false): void
{
    $GLOBALS['__wp_hooks']['sizes'][$name] = ['w' => $width, 'h' => $height, 'crop' => $crop];
}
}

if (!function_exists('get_option')) {
function get_option(string $option, mixed $default = false): mixed
{
    return $GLOBALS['__wp_options'][$option] ?? $default;
}
}

if (!function_exists('update_option')) {
function update_option(string $option, mixed $value, ?bool $autoload = null): bool
{
    $GLOBALS['__wp_options'][$option] = $value;
    $GLOBALS['__wp_option_autoload'][$option] = $autoload;
    return true;
}
}

if (!function_exists('wp_enqueue_style')) {
function wp_enqueue_style(string $handle, string $src = '', array $deps = [], string|bool|null $ver = false, string|bool|null $media = 'all'): void
{
    $GLOBALS['__wp_hooks']['enqueued_styles'][$handle] = ['src' => $src, 'deps' => $deps, 'ver' => $ver, 'media' => $media];
}
}

if (!function_exists('wp_dequeue_style')) {
function wp_dequeue_style(string $handle): void
{
    $GLOBALS['__wp_hooks']['dequeued_styles'][] = $handle;
}
}

if (!function_exists('wp_enqueue_script')) {
function wp_enqueue_script(string $handle, string $src = '', array $deps = [], string|bool|null $ver = false, array|bool $args = []): void
{
    $GLOBALS['__wp_hooks']['enqueued_scripts'][$handle] = ['src' => $src, 'deps' => $deps, 'ver' => $ver, 'args' => $args];
}
}

if (!function_exists('wp_dequeue_script')) {
function wp_dequeue_script(string $handle): void
{
    $GLOBALS['__wp_hooks']['dequeued_scripts'][] = $handle;
}
}

if (!function_exists('wp_localize_script')) {
function wp_localize_script(string $handle, string $object_name, array $l10n): bool
{
    $GLOBALS['__wp_hooks']['localized'][$handle] = ['name' => $object_name, 'data' => $l10n];
    return true;
}
}

if (!function_exists('esc_html')) {
function esc_html(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
}

if (!function_exists('esc_attr')) {
function esc_attr(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
}

if (!function_exists('esc_textarea')) {
function esc_textarea(string $text): string
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}
}

if (!function_exists('esc_url')) {
function esc_url(string $url): string
{
    $url = trim($url);
    if ($url === '' || preg_match('/^\s*(javascript|data|vbscript):/i', $url)) {
        return '';
    }
    return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
}
}

if (!function_exists('esc_url_raw')) {
function esc_url_raw(string $url): string
{
    return esc_url($url);
}
}

if (!function_exists('wp_kses_post')) {
function wp_kses_post(string $content): string
{
    // تقریب رفتار kses: فقط تگ‌های مجاز محتوایی می‌مانند (script/style حذف)
    $allowed = '<p><br><strong><em><b><i><u><a><ul><ol><li><h2><h3><h4><blockquote><code><pre><span><img><table><tr><td><th><thead><tbody>';
    return strip_tags($content, $allowed);
}
}

if (!function_exists('wp_strip_all_tags')) {
function wp_strip_all_tags(string $string, bool $remove_breaks = false): string
{
    $string = preg_replace('@<(script|style)[^>]*?>.*?</\\1>@si', '', $string) ?? '';
    $string = strip_tags($string);
    if ($remove_breaks) {
        $string = preg_replace('/[\\r\\n\\t ]+/', ' ', $string) ?? '';
    }
    return trim($string);
}
}

if (!function_exists('sanitize_text_field')) {
function sanitize_text_field(string $str): string
{
    return trim(strip_tags($str));
}
}

if (!function_exists('sanitize_hex_color')) {
function sanitize_hex_color(string $color): string
{
    return preg_match('/^#([A-Fa-f0-9]{3}){1,2}$/', $color) ? $color : '';
}
}

if (!function_exists('absint')) {
function absint(mixed $maybeint): int
{
    return abs((int) $maybeint);
}
}

if (!function_exists('is_admin')) {
function is_admin(): bool
{
    return $GLOBALS['__wp_is_admin'];
}
}

if (!function_exists('is_user_logged_in')) {
function is_user_logged_in(): bool
{
    return $GLOBALS['__wp_user_logged_in'];
}
}

if (!function_exists('current_user_can')) {
function current_user_can(string $capability, mixed ...$args): bool
{
    return $GLOBALS['__wp_current_user_can'];
}
}

if (!function_exists('wp_send_json_success')) {
function wp_send_json_success(mixed $data = null, int $status_code = 200): never
{
    $response = ['success' => true, 'data' => $data, 'code' => $status_code];
    $GLOBALS['__wp_hooks']['json_responses'][] = $response;
    throw new \ES_JsonExitException($response);
}
}

if (!function_exists('wp_send_json_error')) {
function wp_send_json_error(mixed $data = null, int $status_code = 400): never
{
    $response = ['success' => false, 'data' => $data, 'code' => $status_code];
    $GLOBALS['__wp_hooks']['json_responses'][] = $response;
    throw new \ES_JsonExitException($response);
}
}

if (!function_exists('trailingslashit')) {
function trailingslashit(string $value): string
{
    return rtrim($value, '/\\') . '/';
}
}

if (!function_exists('untrailingslashit')) {
function untrailingslashit(string $value): string
{
    return rtrim($value, '/\\');
}
}

if (!function_exists('get_template_directory')) {
function get_template_directory(): string
{
    return $GLOBALS['__es_theme_dir'];
}
}

if (!function_exists('get_template_directory_uri')) {
function get_template_directory_uri(): string
{
    return 'https://test.example/wp-content/themes/erfansanat';
}
}

if (!function_exists('get_stylesheet_directory')) {
function get_stylesheet_directory(): string
{
    return get_template_directory();
}
}

if (!function_exists('locate_template')) {
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
}

if (!function_exists('admin_url')) {
function admin_url(string $path = ''): string
{
    return 'https://test.example/wp-admin/' . ltrim($path, '/');
}
}

if (!function_exists('home_url')) {
function home_url(string $path = ''): string
{
    if ($path === '' || $path === '/') {
        return 'https://test.example/';
    }
    return 'https://test.example/' . ltrim($path, '/');
}
}

if (!function_exists('wp_create_nonce')) {
function wp_create_nonce(string|int $action = -1): string
{
    return 'test-nonce-' . $action;
}
}

if (!function_exists('flush_rewrite_rules')) {
function flush_rewrite_rules(bool $hard = true): void
{
    $GLOBALS['__wp_hooks']['flushed']++;
}
}

if (!function_exists('__')) {
function __($text, string $domain = 'default'): string
{
    return $text;
}
}

if (!function_exists('esc_html__')) {
function esc_html__($text, string $domain = 'default'): string
{
    return esc_html((string) $text);
}
}

if (!function_exists('esc_attr__')) {
function esc_attr__($text, string $domain = 'default'): string
{
    return esc_attr((string) $text);
}
}

if (!function_exists('_e')) {
function _e($text, string $domain = 'default'): void
{
    echo (string) $text;
}
}

if (!function_exists('esc_html_e')) {
function esc_html_e($text, string $domain = 'default'): void
{
    echo esc_html((string) $text);
}
}

if (!function_exists('esc_attr_e')) {
function esc_attr_e($text, string $domain = 'default'): void
{
    echo esc_attr((string) $text);
}
}

if (!function_exists('__return_empty_string')) {
function __return_empty_string(): string
{
    return '';
}
}

if (!function_exists('__return_false')) {
function __return_false(): bool
{
    return false;
}
}

if (!function_exists('__return_true')) {
function __return_true(): bool
{
    return true;
}
}

if (!function_exists('__return_null')) {
function __return_null(): null
{
    return null;
}
}

if (!function_exists('get_header')) {
function get_header($name = null, array $args = []): void
{
    $file = ($GLOBALS['__es_theme_dir'] ?? '') . '/header.php';
    if (is_file($file)) {
        include $file;
    }
}
}

if (!function_exists('get_footer')) {
function get_footer($name = null, array $args = []): void
{
    $file = ($GLOBALS['__es_theme_dir'] ?? '') . '/footer.php';
    if (is_file($file)) {
        include $file;
    }
}
}

if (!function_exists('wp_head')) {
function wp_head(): void
{
    do_action('wp_head');
    echo 'wp-head-marker\n';
}
}

if (!function_exists('wp_footer')) {
function wp_footer(): void
{
    do_action('wp_footer');
    echo 'wp-footer-marker\n';
}
}

if (!function_exists('language_attributes')) {
function language_attributes(): void
{
    $lang = $GLOBALS['__es_stub_locale'] ?? 'fa_IR';
    $html_lang = str_replace('_', '-', $lang);
    echo 'dir="rtl" lang="' . esc_attr($html_lang) . '"';
}
}

if (!function_exists('body_class')) {
function body_class(string $css_class = ''): void
{
    $classes = array_filter(array_map('trim', explode(' ', $css_class)));
    $classes[] = ($GLOBALS['__es_stub_current_post_type'] ?? '') !== '' ? $GLOBALS['__es_stub_current_post_type'] : 'es-page';
    echo 'class="' . esc_attr(implode(' ', $classes)) . '"';
}
}

if (!function_exists('wp_body_open')) {
function wp_body_open(): void
{
    do_action('wp_body_open');
    echo 'wp-body-open-marker\n';
}
}

if (!function_exists('bloginfo')) {
function bloginfo(string $show = ''): void
{
    if ($show === 'charset') {
        echo 'UTF-8';
    }
}
}

if (!function_exists('language_attributes_reset')) {
function language_attributes_reset(): void
{
    // نگهبان ساده برای سازگاری تست‌ها — بدون عملیات
}
}

if (!function_exists('get_bloginfo')) {
function get_bloginfo(string $show = ''): string
{
    return match ($show) {
        'name' => $GLOBALS['__es_stub_blogname'] ?? 'عرفان صنعت اصفهان',
        'charset' => 'UTF-8',
        'language' => $GLOBALS['__es_stub_locale'] ?? 'fa-IR',
        default => '',
    };
}
}

/** اکشن‌های ادمین — ثبت صفحات منو */
if (!function_exists('add_menu_page')) {
    function add_menu_page(string $page_title, string $menu_title, string $capability, string $menu_slug, callable|array|string $callback = '', string $icon_url = '', ?int $position = null): string {
        $GLOBALS['__wp_hooks']['admin_pages'][$menu_slug] = [
            'type' => 'menu', 'page_title' => $page_title, 'menu_title' => $menu_title,
            'capability' => $capability, 'callback' => $callback, 'icon' => $icon_url, 'position' => $position,
        ];
        return $menu_slug;
    }
}

if (!function_exists('add_submenu_page')) {
    function add_submenu_page(string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable|array|string $callback = ''): string {
        if (isset($GLOBALS['__wp_hooks']['admin_pages'][$menu_slug])) {
            $GLOBALS['__wp_hooks']['admin_pages'][$menu_slug]['submenus'][$parent_slug] = [
                'page_title' => $page_title, 'menu_title' => $menu_title,
                'capability' => $capability, 'callback' => $callback,
            ];
        } else {
            $GLOBALS['__wp_hooks']['admin_pages'][$menu_slug] = [
                'type' => 'submenu', 'parent' => $parent_slug, 'page_title' => $page_title,
                'menu_title' => $menu_title, 'capability' => $capability, 'callback' => $callback,
            ];
        }
        return $menu_slug;
    }
}

/** wp_die در تست: استثنا می‌دهد (رفتار die) */
if (!class_exists('ES_WpDieException')) {
    class ES_WpDieException extends \RuntimeException {
        public $message_raw;
        public function __construct($message = '', $code = 0) {
            $this->message_raw = $message;
            parent::__construct(is_string($message) ? $message : 'wp-die', $code);
        }
    }
}

if (!class_exists('ES_JsonExitException')) {
    /** پاسخ JSON وردپرس در تست به‌صورت استثنا منتشر می‌شود (معادل exit هسته) */
    class ES_JsonExitException extends \RuntimeException
    {
        public array $response;
        public function __construct(array $response)
        {
            $this->response = $response;
            parent::__construct('json-exit', (int) ($response['code'] ?? 0));
        }
    }
}

if (!function_exists('wp_die')) {
    function wp_die(mixed $message = '', mixed $title = '', mixed $args = []): never {
        throw new ES_WpDieException(is_string($message) ? $message : 'wp-die', is_array($args) ? (int) ($args['response'] ?? 0) : 0);
    }
}

/** چک نانس ajax — مطابق رفتار واقعی: نامعتبر → die */
if (!function_exists('check_ajax_referer')) {
    function check_ajax_referer(string|int $action = -1, string $query_arg = 'nonce', bool $die = true): string|int|false {
        $expected = 'test-nonce-' . $action;
        $provided = isset($_POST[$query_arg]) && is_string($_POST[$query_arg]) ? $_POST[$query_arg] : ($_REQUEST[$query_arg] ?? '');
        if (is_string($provided) && hash_equals($expected, $provided)) {
            return '1';
        }
        if ($die) {
            throw new ES_WpDieException('-1', 403);
        }
        return false;
    }
}

if (!function_exists('wp_unslash')) {
    function wp_unslash(mixed $value): mixed {
        if (is_string($value)) {
            return stripslashes($value);
        }
        if (is_array($value)) {
            return array_map('wp_unslash', $value);
        }
        return $value;
    }
}

if (!function_exists('sanitize_key')) {
    function sanitize_key(string $key): string {
        return preg_replace('/[^a-z0-9_\-]/', '', strtolower($key));
    }
}

if (!function_exists('sanitize_email')) {
    function sanitize_email(string $email): string {
        return filter_var(trim($email), FILTER_VALIDATE_EMAIL) ? $email : '';
    }
}

if (!function_exists('is_email')) {
    function is_email(mixed $email): string|bool {
        return is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
    }
}

if (!function_exists('wp_check_invalid_utf8')) {
    function wp_check_invalid_utf8(string $text, bool $strip = false): string {
        return mb_check_encoding($text, 'UTF-8') ? $text : ($strip ? '' : '');
    }
}


if (!function_exists('wp_get_attachment_url')) {
    function wp_get_attachment_url(int $attachment_id): string|false
    {
        return $GLOBALS['__es_stub_attachment_urls'][$attachment_id] ?? false;
    }
}
if (!function_exists('wp_enqueue_media')) {
    function wp_enqueue_media(array $args = []): void {
        $GLOBALS['__wp_hooks']['media_enqueued'] = ($GLOBALS['__wp_hooks']['media_enqueued'] ?? 0) + 1;
    }
}

/** متای پست برای تست متاباکس‌ها */
if (!function_exists('get_post_meta')) {
    function get_post_meta(int $post_id, string $key = '', bool $single = false): mixed {
        $meta = $GLOBALS['__wp_post_meta'][$post_id][$key] ?? ($key === '' ? [] : null);
        if ($key === '') {
            return $GLOBALS['__wp_post_meta'][$post_id] ?? [];
        }
        return $single ? $meta : (($GLOBALS['__wp_post_meta'][$post_id][$key] ?? null) === null ? [] : [$meta]);
    }
}

if (!function_exists('update_post_meta')) {
    function update_post_meta(int $post_id, string $meta_key, mixed $meta_value, mixed $prev_value = ''): bool {
        $GLOBALS['__wp_post_meta'][$post_id][$meta_key] = $meta_value;
        return true;
    }
}

if (!function_exists('add_meta_box')) {
    function add_meta_box(string $id, string $title, callable|array|string $callback, string|array|false|null $screen = null, string $context = 'advanced', string $priority = 'default', ?array $callback_args = null): void {
        if (isset($GLOBALS['__wp_hooks']['meta_boxes'][$id])) {
            $prev = $GLOBALS['__wp_hooks']['meta_boxes'][$id];
            $screens = (array) ($prev['screens'] ?? [$prev['screen']]);
            foreach ((array) $screen as $one) {
                if (!in_array($one, $screens, true)) {
                    $screens[] = $one;
                }
            }
            $GLOBALS['__wp_hooks']['meta_boxes'][$id]['screens'] = $screens;
            return;
        }
        $GLOBALS['__wp_hooks']['meta_boxes'][$id] = [
            'title' => $title, 'callback' => $callback, 'screen' => $screen,
            'screens' => (array) $screen, 'context' => $context, 'priority' => $priority,
        ];
    }
}

if (!function_exists('wp_nonce_field')) {
    function wp_nonce_field(string|int $action = -1, string $name = '_wpnonce', bool $referer = true, bool $echo = true): string {
        $html = '<input type="hidden" id="' . esc_attr($name) . '" name="' . esc_attr($name) . '" value="' . esc_attr(wp_create_nonce($action)) . '">';
        if ($echo) {
            echo $html;
        }
        return $html;
    }
}

if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce(string $nonce, string|int $action = -1): int|false {
        return hash_equals('test-nonce-' . $action, $nonce) ? 1 : false;
    }
}

if (!function_exists('get_post_type')) {
    function get_post_type(int|string|object|null $post = null): string|false {
        $id = is_object($post) ? ($post->ID ?? 0) : (int) ($post ?? 0);
        return $GLOBALS['__es_stub_post_types'][$id] ?? false;
    }
}


if (!function_exists('wp_is_post_autosave')) {
    function wp_is_post_autosave(int|string|object $post): bool
    {
        $id = is_object($post) ? ($post->ID ?? 0) : (int) $post;
        return ($GLOBALS['__es_stub_autosave'] ?? 0) === $id && $id !== 0;
    }
}

if (!function_exists('wp_is_post_revision')) {
    function wp_is_post_revision(int|string|object $post): bool
    {
        $id = is_object($post) ? ($post->ID ?? 0) : (int) $post;
        return ($GLOBALS['__es_stub_revision'] ?? 0) === $id && $id !== 0;
    }
}

/** CPT و تکسونومی */
if (!function_exists('register_post_type')) {
    function register_post_type(string $post_type, array $args = []): object|false
    {
        $GLOBALS['__wp_hooks']['post_types'][$post_type] = $args;
        return new \stdClass();
    }
}

if (!function_exists('register_taxonomy')) {
    function register_taxonomy(string $taxonomy, array|string $object_type = [], array $args = []): object|false
    {
        $args['object_type'] = (array) $object_type;
        $GLOBALS['__wp_hooks']['taxonomies'][$taxonomy] = $args;
        return new \stdClass();
    }
}

/** ترم‌متا */
if (!function_exists('get_term_meta')) {
    function get_term_meta(int $term_id, string $key = '', bool $single = false): mixed
    {
        if ($key === '') {
            return $GLOBALS['__wp_term_meta'][$term_id] ?? [];
        }
        return $GLOBALS['__wp_term_meta'][$term_id][$key] ?? '';
    }
}

if (!function_exists('update_term_meta')) {
    function update_term_meta(int $term_id, string $meta_key, mixed $meta_value, mixed $prev = ''): bool
    {
        $GLOBALS['__wp_term_meta'][$term_id][$meta_key] = $meta_value;
        return true;
    }
}

if (!function_exists('is_post_type_archive')) {
    function is_post_type_archive(string|array $post_types = ''): bool
    {
        if (!($GLOBALS['__wp_is_post_type_archive'] ?? false)) {
            return false;
        }
        if ($post_types === '') {
            return true;
        }
        return in_array((string) ($GLOBALS['__wp_post_type_archive'] ?? ''), (array) $post_types, true);
    }
}
if (!function_exists('get_terms')) {
    function get_terms(array|string $args = []): array|string
    {
        $fixture = $GLOBALS['__es_stub_terms'] ?? [];
        if (is_string($args)) {
            $args = ['taxonomy' => $args];
        }
        $taxes = $args['taxonomy'] ?? '';
        $taxes = is_array($taxes) ? $taxes : ($taxes === '' ? array_keys($fixture) : [$taxes]);
        $out = [];
        foreach ($taxes as $tax) {
            foreach ($fixture[$tax] ?? [] as $term) {
                if (isset($args['parent']) && (int) ($term->parent ?? 0) !== (int) $args['parent']) {
                    continue;
                }
                if (isset($args['slug']) && (string) $term->slug !== (string) $args['slug']) {
                    continue;
                }
                if (!empty($args['name__like']) && !str_contains((string) $term->name, (string) $args['name__like'])) {
                    continue;
                }
                $out[] = $term;
            }
        }
        $number = (int) ($args['number'] ?? 0);
        return $number > 0 ? array_slice($out, 0, $number) : $out;
    }
}

if (!function_exists('wp_count_posts')) {
    function wp_count_posts(string $type = 'post', string $perm = ''): object
    {
        $counts = $GLOBALS['__es_stub_post_counts'] ?? [];
        return (object) ['publish' => (int) ($counts[$type] ?? 0), 'draft' => 0, 'trash' => 0];
    }
}

if (!function_exists('get_posts')) {
    function get_posts(array $args = []): array
    {
        $GLOBALS['__es_stub_get_posts_args'][] = $args;
        $posts = $GLOBALS['__es_stub_posts'] ?? [];
        $type  = $args['post_type'] ?? 'post';
        $num   = (int) ($args['numberposts'] ?? ($args['posts_per_page'] ?? 5));
        $include = array_map('intval', (array) ($args['include'] ?? ($args['post__in'] ?? [])));
        $not_in  = array_map('intval', (array) ($args['post__not_in'] ?? []));
        $taxes = [];
        foreach ((array) ($args['tax_query'] ?? []) as $tq) {
            if (is_array($tq) && isset($tq['taxonomy'])) {
                $taxes[(string) $tq['taxonomy']] = [
                    'field' => (string) ($tq['field'] ?? 'term_id'),
                    'terms' => (array) ($tq['terms'] ?? []),
                ];
            }
        }
        $out = [];
        foreach ($posts as $post) {
            if ((string) ($post->post_type ?? '') !== (string) $type) {
                continue;
            }
            $pid = (int) ($post->ID ?? 0);
            if ($include !== [] && !in_array($pid, $include, true)) {
                continue;
            }
            if ($not_in !== [] && in_array($pid, $not_in, true)) {
                continue;
            }
            $ok = true;
            foreach ($taxes as $tax => $spec) {
                // map ممکن است آبجکت ترم باشد — term_id/slug/name بسته به field
                $have_ids = [];
                $have_slugs = [];
                $have_names = [];
                foreach (($GLOBALS['__es_stub_post_term_map'][$pid][$tax] ?? []) as $t) {
                    if (is_object($t)) {
                        $have_ids[] = (int) ($t->term_id ?? 0);
                        $have_slugs[] = (string) ($t->slug ?? '');
                        $have_names[] = (string) ($t->name ?? '');
                    } else {
                        $have_ids[] = (int) $t;
                    }
                }
                $pool = $spec['field'] === 'slug' ? $have_slugs : ($spec['field'] === 'name' ? $have_names : $have_ids);
                $want = $spec['field'] === 'term_id' ? array_map('intval', $spec['terms']) : array_map('strval', $spec['terms']);
                if (array_intersect($want, $pool) === []) {
                    $ok = false;
                    break;
                }
            }
            if (!$ok) {
                continue;
            }
            $out[] = $post;
        }
        if ($include !== []) {
            usort($out, static fn($a, $b) => array_search((int) $a->ID, $include, true) <=> array_search((int) $b->ID, $include, true));
        }
        return array_slice($out, 0, max($num, 0));
    }
}

if (!function_exists('get_permalink')) {
    function get_permalink(mixed $post = null): string|false
    {
        $id = is_object($post) ? (int) ($post->ID ?? 0) : (int) $post;
        if (isset($GLOBALS['__es_stub_permalinks'][$id])) {
            return (string) $GLOBALS['__es_stub_permalinks'][$id];
        }
        return $id > 0 ? home_url('/?p=' . $id) : false;
    }
}

if (!function_exists('setcookie_es_stub')) {
}

/** استاب setcookie — ثبت در گلوبال برای assert */
if (!function_exists('setcookie')) {
    function setcookie(string $name, string $value = '', array|int $expires_or_options = 0, string $path = '', string $domain = '', bool $secure = false, bool $httponly = false): bool
    {
        $GLOBALS['__es_stub_cookies'][$name] = $value;
        return true;
    }
}

/** استاب WooCommerce (فااز ۴: badge سبد؛ فاز ۶ کامل می‌شود) */
if (!function_exists('WC')) {
    function WC(): ?object
    {
        return $GLOBALS['__es_stub_wc'] ?? null;
    }
}

if (!function_exists('wc_get_page_id')) {
    function wc_get_page_id(string $page): int
    {
        return (int) ($GLOBALS['__es_stub_wc_page_ids'][$page] ?? -1);
    }
}

/** ─── استاب‌های فاز ۴: ناوبری، منوها، theme mod ─── */
if (!function_exists('get_theme_mod')) {
    function get_theme_mod(string $name, mixed $default = ''): mixed
    {
        return $GLOBALS['__wp_theme_mods'][$name] ?? $default;
    }
}

if (!function_exists('has_custom_logo')) {
    function has_custom_logo(int $blog_id = 0): bool
    {
        return (int) (get_theme_mod('custom_logo', 0)) > 0;
    }
}

if (!function_exists('get_custom_logo')) {
    function get_custom_logo(int $blog_id = 0): string
    {
        $id = (int) get_theme_mod('custom_logo', 0);
        if ($id <= 0) {
            return '';
        }
        $url = $GLOBALS['__es_stub_attachment_urls'][$id] ?? '';
        return '<img src="' . esc_url((string) $url) . '" class="custom-logo" alt="">';
    }
}

if (!function_exists('has_nav_menu')) {
    function has_nav_menu(string $location): bool
    {
        return !empty($GLOBALS['__es_stub_nav_menus'][$location]);
    }
}

if (!function_exists('wp_get_nav_menu_items')) {
    function wp_get_nav_menu_items(mixed $menu): array|false
    {
        $items = $GLOBALS['__es_stub_nav_items'] ?? null;
        if (is_array($items)) {
            return $items;
        }
        return false;
    }
}

if (!function_exists('get_term_link')) {
    function get_term_link(mixed $term): string|\WP_Error
    {
        if (is_object($term)) {
            $tax = (string) ($term->taxonomy ?? 'category');
            $slug = (string) ($term->slug ?? '');
            return home_url('/' . $tax . '/' . $slug . '/');
        }
        return new \WP_Error('invalid_term', 'Term does not exist');
    }
}

if (!function_exists('is_front_page')) {
    function is_front_page(): bool
    {
        return (bool) ($GLOBALS['__es_stub_is_front_page'] ?? false);
    }
}

if (!function_exists('is_search')) {
    function is_search(): bool
    {
        return (bool) ($GLOBALS['__es_stub_is_search'] ?? false);
    }
}

if (!function_exists('get_search_form')) {
    function get_search_form(bool $echo = true): string
    {
        $file = ($GLOBALS['__es_theme_dir'] ?? '') . '/searchform.php';
        ob_start();
        if (is_file($file)) {
            include $file;
        }
        $html = (string) ob_get_clean();
        if ($echo) {
            echo $html;
            return '';
        }
        return $html;
    }
}

if (!function_exists('is_home')) {
    function is_home(): bool
    {
        return (bool) ($GLOBALS['__es_stub_is_home'] ?? false);
    }
}

if (!function_exists('wp_list_categories')) {
    function wp_list_categories(array $args = []): string
    {
        $terms = $GLOBALS['__es_stub_terms']['product_cat'] ?? [];
        $out = '<ul>';
        foreach ($terms as $term) {
            $out .= '<li><a href="' . esc_url(get_term_link($term)) . '">' . esc_html((string) $term->name) . '</a></li>';
        }
        $out .= '</ul>';
        return $out;
    }
}

if (!function_exists('wp_nav_menu')) {
    function wp_nav_menu(array $args = []): string
    {
        $GLOBALS['__wp_nav_menu_calls'][] = $args;
        $walker = $args['walker'] ?? null;
        if ($walker !== null && method_exists($walker, 'walk')) {
            $items = wp_get_nav_menu_items($args['menu'] ?? 0);
            if (is_array($items) && $items !== []) {
                return (string) $walker->walk($items, (int) ($args['depth'] ?? 0));
            }
        }
        return '';
    }
}

/** کلاس پایه Walker_Nav_Menu — پیمایش بازگشتی استاندارد (walkerها walk را override می‌کنند و رشته برمی‌گردانند) */
if (!class_exists('Walker_Nav_Menu')) {
    class Walker_Nav_Menu
    {
        public array $db_fields = ['parent' => 'menu_item_parent', 'id' => 'db_id'];

        public function walk(array $elements, int $max_depth, mixed ...$args): string
        {
            $out = '';
            $children = [];
            foreach ($elements as $el) {
                $children[(int) ($el->menu_item_parent ?? 0)][] = $el;
            }
            $top = $children[0] ?? $elements;
            $limit = ($max_depth === 0 || $max_depth === -1) ? PHP_INT_MAX : $max_depth;
            foreach ($top as $el) {
                $out .= $this->es_traverse($el, 0, $children, $limit, $args);
            }
            return $out;
        }

        private function es_traverse(object $el, int $depth, array $children, int $max_depth, array $args): string
        {
            $out  = '';
            $id   = (int) ($el->{$this->db_fields['id']} ?? 0);
            $kids = $children[$id] ?? [];
            $out .= $this->start_el($el, $depth, $args);
            if ($kids !== [] && $depth + 1 < $max_depth) {
                $out .= $this->start_lvl($depth, $args);
                foreach ($kids as $kid) {
                    $out .= $this->es_traverse($kid, $depth + 1, $children, $max_depth, $args);
                }
                $out .= $this->end_lvl($depth, $args);
            }
            $out .= $this->end_el($el, $depth, $args);
            return $out;
        }

        public function start_lvl(int $depth = 0, array $args = []): string { return ''; }
        public function end_lvl(int $depth = 0, array $args = []): string { return ''; }
        public function start_el(object $el, int $depth = 0, array $args = []): string { return ''; }
        public function end_el(object $el, int $depth = 0, array $args = []): string { return ''; }
    }
}

/** قالب انتخابی برگه — برای نگاشت گروه سکشن‌ها (فاز ۷.۷) */
if (!function_exists('get_page_template_slug')) {
    function get_page_template_slug(int|string|object|null $post = null): string
    {
        $id = is_object($post) ? (int) ($post->ID ?? 0) : (int) $post;
        return (string) ($GLOBALS['__es_stub_template_slug'][$id] ?? '');
    }
}

/** شرطی‌های کوئری برای ViewCounter و فیلترهای ادمین */
if (!function_exists('is_singular')) {
    function is_singular(array|string $post_types = []): bool
    {
        $current = $GLOBALS['__es_stub_current_post_type'] ?? '';
        if ($post_types === []) {
            return $current !== '';
        }
        return in_array($current, (array) $post_types, true);
    }
}

/** کوئری‌بندی: get_queried_object_id / get_queried_object / is_tax برای HreflangMeta */
if (!function_exists('get_queried_object_id')) {
    function get_queried_object_id(): int
    {
        return (int) ($GLOBALS['__es_stub_queried_object_id'] ?? 0);
    }
}

if (!function_exists('get_queried_object')) {
    function get_queried_object(): mixed
    {
        return $GLOBALS['__es_stub_queried_object'] ?? null;
    }
}

if (!function_exists('is_tax')) {
    function is_tax(string|array $taxonomies = ''): bool
    {
        return (bool) ($GLOBALS['__es_stub_is_tax'] ?? false);
    }
}

if (!function_exists('is_category')) {
    function is_category(mixed $category = ''): bool
    {
        return false;
    }
}

if (!function_exists('is_tag')) {
    function is_tag(mixed $tag = ''): bool
    {
        return false;
    }
}

if (!function_exists('headers_sent')) {
    function headers_sent(): bool
    {
        return (bool) ($GLOBALS['__es_stub_headers_sent'] ?? true);
    }
}

if (!function_exists('setcookie_stub_note')) {
}

if (!function_exists('is_preview')) {
    function is_preview(): bool
    {
        return (bool) ($GLOBALS['__es_stub_is_preview'] ?? false);
    }
}

if (!function_exists('is_feed')) {
    function is_feed(string $feeds = ''): bool
    {
        return (bool) ($GLOBALS['__es_stub_is_feed'] ?? false);
    }
}

if (!function_exists('is_object_in_taxonomy')) {
    function is_object_in_taxonomy(string $object_type, string $taxonomy): bool
    {
        return in_array($object_type, ['post', 'page'], true) || true; // در تست، همیشه سازگار فرض
    }
}

if (!function_exists('is_serialized')) {
    function is_serialized(mixed $data, bool $strict = true): bool
    {
        if (!is_string($data)) {
            return false;
        }
        $data = trim($data);
        return $data !== '' && preg_match('/^(s|i|d|b|a|O|C):/', $data) === 1;
    }
}


if (!function_exists('selected')) {
    /** چاپ اتریبیوت selected مطابق هسته */
    function selected(mixed $helper, mixed $current = true, bool $display = true): string
    {
        $result = (string) $helper === (string) $current ? " selected='selected'" : '';
        if ($display) {
            echo $result;
        }
        return $result;
    }
}

if (!function_exists('checked')) {
    function checked(mixed $helper, mixed $current = true, bool $display = true): string
    {
        $result = (string) $helper === (string) $current ? " checked='checked'" : '';
        if ($display) {
            echo $result;
        }
        return $result;
    }
}

if (!function_exists('get_comments_number')) {
    function get_comments_number(int|object|null $post = null): int
    {
        $id = is_object($post) ? ($post->ID ?? 0) : (int) ($post ?? ($GLOBALS['__es_stub_current_id'] ?? 0));
        return (int) ($GLOBALS['__es_stub_comment_counts'][$id] ?? 0);
    }
}
if (!function_exists('get_post_field')) {
    function get_post_field(string $field, int|object $post, string $context = 'display'): mixed
    {
        if (is_object($post)) {
            return $post->{$field} ?? '';
        }
        foreach (($GLOBALS['__es_stub_posts'] ?? []) as $p) {
            if ((int) ($p->ID ?? 0) === (int) $post) {
                return $p->{$field} ?? '';
            }
        }
        return '';
    }
}
if (!function_exists('get_term')) {
    function get_term(int|object $term, string $taxonomy = ''): object
    {
        if (is_object($term)) {
            return $term;
        }
        $t = $GLOBALS['__es_stub_terms'][(int) $term] ?? null;
        return $t instanceof \WP_Error ? $t : ($t ?? new \WP_Error('invalid_term', 'Term not found'));
    }
}
if (!function_exists('is_wp_error')) {
    function is_wp_error(mixed $thing): bool
    {
        return $thing instanceof \WP_Error;
    }
}
if (!function_exists('get_the_ID')) {
    function get_the_ID(): int
    {
        return (int) ($GLOBALS['__es_stub_current_id'] ?? 0);
    }
}
if (!function_exists('get_the_terms')) {
    function get_the_terms(int|object $post, string $taxonomy): array|false
    {
        $id = is_object($post) ? ($post->ID ?? 0) : (int) $post;
        return $GLOBALS['__es_stub_post_term_map'][$id][$taxonomy] ?? false;
    }
}
if (!function_exists('pre_get_posts_hook_recorder')) {
    function pre_get_posts_hook_recorder(): void
    {
    }
}

if (!function_exists('update_meta_cache')) {
    function update_meta_cache(): void
    {
    }
}

if (!function_exists('wp_next_scheduled')) {
    function wp_next_scheduled(string $hook): bool|int
    {
        return false;
    }
}
if (!function_exists('wp_reset_vars')) {
    function wp_reset_vars(array $vars = []): void {
    }
}

/** استاب WP_Query (فاز ۵: ویترین مقالات) — آرگومان‌ها ثبت و پست‌ها از گلوبال */
if (!class_exists('WP_Query')) {
    class WP_Query {
        public array $posts;
        public int $found_posts;
        public function __construct(array $args = []) {
            $GLOBALS['__es_stub_wp_query_args'][] = $args;
            $this->posts = is_array($GLOBALS['__es_stub_wp_query_posts'] ?? null) ? $GLOBALS['__es_stub_wp_query_posts'] : [];
            $this->found_posts = count($this->posts);
        }
        public function have_posts(): bool {
            return $this->posts !== [];
        }
        /** @return array<int, object> */
        public function get_posts(): array {
            return $this->posts;
        }
    }
}

/** استاب get_the_title */
if (!function_exists('get_the_title')) {
    function get_the_title(int|object|null $post = null): string {
        if (is_object($post)) {
            return (string) ($post->post_title ?? '');
        }
        if (is_int($post) && isset($GLOBALS['__es_stub_titles'][$post])) {
            return (string) $GLOBALS['__es_stub_titles'][$post];
        }
        $cur = $GLOBALS['__es_stub_current_post'] ?? null;
        return is_object($cur) ? (string) ($cur->post_title ?? '') : '';
    }
}

/** استاب get_the_excerpt */
if (!function_exists('get_the_excerpt')) {
    function get_the_excerpt(int|object|null $post = null): string {
        if (is_object($post)) {
            return (string) ($post->post_excerpt ?? '');
        }
        $cur = $GLOBALS['__es_stub_current_post'] ?? null;
        return is_object($cur) ? (string) ($cur->post_excerpt ?? '') : '';
    }
}

/** استاب get_the_date — تاریخ نمایشی از گلوبال (رفتار i18n فارسی در تولید شبیه‌سازی می‌شود) */
if (!function_exists('get_the_date')) {
    function get_the_date(string $format = '', int|object|null $post = null): string|int|false {
        $pid = is_object($post) ? (int) ($post->ID ?? 0) : (int) $post;
        if (isset($GLOBALS['__es_stub_post_dates'][$pid])) {
            return (string) $GLOBALS['__es_stub_post_dates'][$pid];
        }
        $raw = is_object($post) ? (string) ($post->post_date ?? 'now') : 'now';
        return gmdate($format === '' ? 'j F Y' : $format, strtotime($raw) ?: time());
    }
}

/** استاب get_the_category روی نقشه ترم گلوبال */
if (!function_exists('get_the_category')) {
    function get_the_category(int|object|null $post = null): array {
        $pid = is_object($post) ? (int) ($post->ID ?? 0) : (int) $post;
        $terms = $GLOBALS['__es_stub_post_term_map'][$pid]['category'] ?? [];
        return is_array($terms) ? $terms : [];
    }
}

/** استاب wp_trim_words */
if (!function_exists('wp_trim_words')) {
    function wp_trim_words(string $text, int $num_words = 55, string $more = '&hellip;'): string {
        $words = preg_split('/\s+/u', trim($text)) ?: [];
        if (count($words) <= $num_words) {
            return trim($text);
        }
        return implode(' ', array_slice($words, 0, $num_words)) . $more;
    }
}

/** استاب wp_kses — حذف تگ‌ها/اتریبیوت‌های خارج از لیست مجاز (فاز ۵: iframe نقشه) */
if (!function_exists('wp_kses')) {
    function wp_kses(string $string, array $allowed_html, array $allowed_protocols = []): string {
        $allowed_tags = array_keys($allowed_html);
        $allowed = '<' . implode('><', array_map('strtolower', array_map('strval', $allowed_tags))) . '>';
        // حذف محتوای اسکریپت/استایل و سپس فیلتر تگ‌ها
        $string = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $string) ?? '';
        // فیلتر اتریبیوت‌ها در تگ‌های مجاز
        $clean = strip_tags($string, $allowed);
        return preg_replace_callback('/<(\w+)([^>]*)>/', function ($m) use ($allowed_html) {
            $tag = strtolower($m[1]);
            if (!isset($allowed_html[$tag]) || $allowed_html[$tag] !== true && !is_array($allowed_html[$tag])) {
                return '<' . $m[1] . '>';
            }
            $ok = $allowed_html[$tag] === true ? [] : array_map('strtolower', array_keys($allowed_html[$tag]));
            $attrs = '';
            if ($ok !== []) {
                if (preg_match_all('/([a-zA-Z-]+)=("([^"]*)"|\'([^\']*)\')/', $m[2], $am, PREG_SET_ORDER)) {
                    foreach ($am as $a) {
                        $name = strtolower($a[1]);
                        if (in_array($name, $ok, true)) {
                            $attrs .= ' ' . $name . '="' . ($a[3] ?? $a[4] ?? '') . '"';
                        }
                    }
                }
            }
            return '<' . $m[1] . $attrs . '>';
        }, $clean) ?? $clean;
    }
}

/* ─── استاب‌های دیدگاه (تسک ۶.۶) ─── */
if (!function_exists('get_comments')) {
    function get_comments(array $args = []): array
    {
        $pid   = (int) ($args['post_id'] ?? 0);
        $status = (string) ($args['status'] ?? '');
        $out = [];
        foreach (($GLOBALS['__es_stub_comments'] ?? []) as $c) {
            if ($pid > 0 && (int) ($c->comment_post_ID ?? 0) !== $pid) {
                continue;
            }
            if ($status !== '' && (string) ($c->comment_approved ?? '') !== $status) {
                continue;
            }
            $out[] = $c;
        }
        return $out;
    }
}
if (!function_exists('get_comment_meta')) {
    function get_comment_meta(int $comment_id, string $key, bool $single = false): mixed
    {
        $meta = $GLOBALS['__wp_comment_meta'][$comment_id] ?? [];
        $val = $meta[$key] ?? ($single ? '' : []);
        return $single && is_array($val) && isset($val[0]) ? $val[0] : $val;
    }
}
if (!function_exists('add_comment_meta')) {
    function add_comment_meta(int $comment_id, string $key, mixed $value, bool $unique = false): bool
    {
        $GLOBALS['__wp_comment_meta'][$comment_id] = ($GLOBALS['__wp_comment_meta'][$comment_id] ?? []);
        if ($unique && isset($GLOBALS['__wp_comment_meta'][$comment_id][$key])) {
            return false;
        }
        $GLOBALS['__wp_comment_meta'][$comment_id][$key] = $value;
        return true;
    }
}
if (!function_exists('get_comment_date')) {
    function get_comment_date(string $format = 'Y/m/d', int|object|null $comment = null): string
    {
        $cid = is_object($comment) ? (int) ($comment->comment_ID ?? 0) : (int) ($comment ?? 0);
        $raw = (string) ($GLOBALS['__es_stub_comment_dates'][$cid] ?? '');
        if ($raw === '') {
            return '';
        }
        return ctype_digit($raw) ? date($format, (int) $raw) : $raw;
    }
}
if (!function_exists('wp_parse_url')) {
    function wp_parse_url(string $url, int $component = -1): mixed
    {
        return parse_url($url, $component);
    }
}
if (!function_exists('current_user_id') && false) {
}

/** استاب get_query_var — فاز ۷: صفحه‌بندی آرشیوها (paged) */
if (!function_exists('get_query_var')) {
    function get_query_var(string $var, mixed $default = ''): mixed
    {
        return $GLOBALS['__es_stub_query_vars'][$var] ?? $default;
    }
}

/** استاب get_post_type_archive_link — فاز ۷: لینک پایه آرشیو */
if (!function_exists('get_post_type_archive_link')) {
    function get_post_type_archive_link(string $post_type): string
    {
        return (string) ($GLOBALS['__es_stub_archive_links'][$post_type] ?? '');
    }
}
