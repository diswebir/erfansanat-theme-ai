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
}
}

if (!function_exists('get_footer')) {
function get_footer($name = null, array $args = []): void
{
}
}

if (!function_exists('wp_head')) {
function wp_head(): void
{
}
}

if (!function_exists('wp_footer')) {
function wp_footer(): void
{
}
}

if (!function_exists('language_attributes')) {
function language_attributes(): void
{
}
}

if (!function_exists('body_class')) {
function body_class(string $css_class = ''): void
{
}
}

if (!function_exists('wp_body_open')) {
function wp_body_open(): void
{
}
}

if (!function_exists('bloginfo')) {
function bloginfo(string $show = ''): void
{
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
        $GLOBALS['__wp_hooks']['meta_boxes'][$id] = [
            'title' => $title, 'callback' => $callback, 'screen' => $screen,
            'context' => $context, 'priority' => $priority,
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

if (!function_exists('get_terms')) {
    function get_terms(array|string $args = []): array|string
    {
        $fixture = $GLOBALS['__es_stub_terms'] ?? [];
        if (is_string($args)) {
            $args = ['taxonomy' => $args];
        }
        $tax = $args['taxonomy'] ?? '';
        if ($tax === '' || isset($fixture[$tax])) {
            return is_array($tax) || $tax === '' ? array_merge(...array_values($fixture ?: [])) : $fixture[$tax];
        }
        return [];
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
