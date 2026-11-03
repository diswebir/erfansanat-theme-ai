<?php
/**
 * wp-stubs.php — minimal WordPress function stubs so the theme's PHP can be
 * EXECUTED (not just linted) under php-wasm. Not a full WP — just enough to
 * smoke-render templates with sample data and catch runtime fatals.
 */

if (!defined('ABSPATH')) { define('ABSPATH', '/tmp/wp/'); }

$GLOBALS['__hooks'] = [];
$GLOBALS['__options'] = ['page_on_front' => 8146, 'admin_email' => 'test@example.com'];
$GLOBALS['__postmeta'] = [];
$GLOBALS['__posts'] = [];
$GLOBALS['__terms'] = [];

function add_action($h, $cb, $p = 10, $a = 1) { $GLOBALS['__hooks'][$h][] = $cb; return true; }
function add_filter($h, $cb, $p = 10, $a = 1) { return add_action($h, $cb, $p, $a); }
function do_action($h, ...$args) { foreach ($GLOBALS['__hooks'][$h] ?? [] as $cb) { call_user_func_array($cb, $args); } }
function add_theme_support($f, $a = null) { $GLOBALS['__support'][$f] = $a; return true; }
function register_post_type($t, $a = []) { $GLOBALS['__cpts'][$t] = $a; return true; }
function register_taxonomy($t, $o, $a = []) { $GLOBALS['__tax'][$t] = $a; return true; }
function register_nav_menus($m) { $GLOBALS['__menus'] = $m; return true; }
function load_theme_textdomain($d, $p = null) { return true; }
function is_rtl() { return true; }
function is_admin() { return false; }
function get_template_directory() { return dirname(__DIR__, 3); }
function get_template_directory_uri() { return 'https://example.test/wp-content/themes/erfansanat-theme-ai/'; }
function trailingslashit($s) { return rtrim($s, '/') . '/'; }
function get_option($k, $d = false) { return $GLOBALS['__options'][$k] ?? $d; }
function update_option($k, $v, $a = null) { $GLOBALS['__options'][$k] = $v; return true; }
function get_post_meta($id, $k, $single = false) { return $GLOBALS['__postmeta'][$id][$k] ?? ($single ? '' : []); }
function update_post_meta($id, $k, $v) { $GLOBALS['__postmeta'][$id][$k] = $v; return true; }
function get_term_meta($id, $k, $single = false) { return $GLOBALS['__termmeta'][$id][$k] ?? ($single ? '' : []); }
function update_term_meta($id, $k, $v) { $GLOBALS['__termmeta'][$id][$k] = $v; return true; }
function get_post($id) { $p = $GLOBALS['__posts'][$id] ?? null; return $p ? new WP_Post($id, $p['type'], $p['title']) : null; }
function get_queried_object_id() { return $GLOBALS['__qid'] ?? 0; }
function get_queried_object() { return $GLOBALS['__qobj'] ?? null; }
function home_url($p = '/') { return 'https://example.test' . $p; }
function admin_url($p = '') { return 'https://example.test/wp-admin/' . $p; }
function esc_url($u) { return htmlspecialchars((string) $u, ENT_QUOTES); }
function esc_attr($s) { return htmlspecialchars((string) $s, ENT_QUOTES); }
function esc_html($s) { return htmlspecialchars((string) $s, ENT_QUOTES); }
function esc_html__($s, $d = null) { return $s; }
function esc_html_e($s, $d = null) { echo esc_html($s); }
function __($s, $d = null) { return $s; }
function _e($s, $d = null) { echo $s; }
function _n($s, $p, $n, $d = null) { return $n == 1 ? $s : $p; }
function esc_attr__($s, $d = null) { return $s; }
function sanitize_text_field($s) { return trim(strip_tags((string) $s)); }
function sanitize_textarea_field($s) { return trim(strip_tags((string) $s)); }
function sanitize_email($s) { return filter_var($s, FILTER_SANITIZE_EMAIL); }
function sanitize_key($s) { return preg_replace('/[^a-z0-9_\-]/', '', strtolower((string) $s)); }
function sanitize_title($s) { return preg_replace('/[^a-z0-9\-]/', '', strtolower((string) $s)); }
function wp_unslash($s) { return $s; }
function absint($v) { return abs((int) $v); }
function esc_url_raw($u) { return $u; }
function wp_kses_post($s) { return $s; }
function wp_json_encode($v) { return json_encode($v); }
function maybe_unserialize($v) { $r = @unserialize((string) $v); return $r === false && $v !== 'b:0;' ? $v : $r; }
function is_wp_error($t) { return $t instanceof WP_Error; }
function wp_die($m = '') { throw new Exception('wp_die: ' . $m); }
function current_user_can($c) { return true; }
function wp_verify_nonce($n, $a) { return $n === 'test-nonce' ? 1 : false; }
function check_admin_referer($a, $f = null) { return true; }
function wp_create_nonce($a) { return 'test-nonce'; }
function wp_send_json_success($d, $c = 200) { echo json_encode(['success' => true, 'data' => $d]); throw new Exception('__JSON_SENT__'); }
function wp_send_json_error($d, $c = 400) { echo json_encode(['success' => false, 'data' => $d]); throw new Exception('__JSON_SENT__'); }
function wp_mail($to, $s, $b) { return true; }
function wp_reset_postdata() { global $post; }
function wp_insert_post($a) { static $id = 100; return ++$id; }
function wp_enqueue_style(...$a) {} function wp_enqueue_script(...$a) {} function wp_enqueue_media() {}
function wp_dequeue_style(...$a) {} function wp_deregister_script(...$a) {}
function wp_script_add_data(...$a) {} function wp_localize_script(...$a) {}
function remove_action(...$a) {} function remove_filter(...$a) {}
function wp_nav_menu($a = []) { echo '<!-- nav:' . ($a['theme_location'] ?? '') . ' -->'; }
function wp_head() { do_action('wp_head'); }
function wp_footer() { do_action('wp_footer'); }
function body_class($c = '') { echo 'class="' . esc_attr($c) . '"'; }
function language_attributes() { echo 'lang="fa-IR"'; }
function wp_body_open() { do_action('wp_body_open'); }
function bloginfo($s) { echo $s === 'charset' ? 'UTF-8' : 'test'; }
function get_header() { include get_template_directory() . '/header.php'; }
function get_footer() { include get_template_directory() . '/footer.php'; }
function get_template_part($slug, $name = null) { $f = get_template_directory() . '/' . $slug . ($name ? '-' . $name : ''); if (substr($f, -4) !== '.php') { $f .= '.php'; } if (is_file($f)) { include $f; } }
function locate_template($f) { return is_file(get_template_directory() . '/' . $f) ? $f : ''; }
function have_posts() { global $wp_query; return $wp_query ? $wp_query->have_posts() : false; }
function the_post() { global $wp_query; if ($wp_query) { $wp_query->the_post(); } }
function the_posts_pagination() { echo '<nav class="pagination">1 2 3</nav>'; }
function post_type_archive_title() { echo 'آرشیو'; }
function single_term_title($p = '', $e = true) { $t = get_queried_object(); $n = $t ? $t->name : ''; if ($e) { echo $n; } return $n; }
function single_cat_title() { echo 'دسته'; }
function the_archive_title() { echo 'آرشیو'; }
function get_search_query() { return 'والاشر'; }
function the_title() { echo esc_html(get_the_title()); }
function get_post_type($id = 0) { global $post; return ($id ? ($GLOBALS["__posts"][$id]["type"] ?? "post") : ($post->post_type ?? "post")); }
function get_the_ID() { global $post; return $post->ID ?? 0; }
function get_the_title($id = 0) { global $post; $p = $id ?: ($post->ID ?? 0); return $GLOBALS['__posts'][$p]['title'] ?? 'عنوان تست'; }
function the_content() { global $post; echo $GLOBALS['__posts'][$post->ID]['content'] ?? ''; }
function get_the_excerpt($id = 0) { return 'خلاصه تست'; }
function wp_trim_words($t, $n) { return implode(' ', array_slice(explode(' ', $t), 0, $n)); }
function get_the_date($f = '', $id = 0) { return '1404/08/12'; }
function get_permalink($id = 0) { global $post; $p = $id ?: ($post->ID ?? 0); return 'https://example.test/?p=' . $p; }
function has_post_thumbnail($id = 0) { return true; }
function get_the_post_thumbnail_url($id = 0, $s = '') { return 'https://example.test/img.jpg'; }
function wp_get_attachment_image_url($id, $s = '') { return 'https://example.test/img-' . $id . '.jpg'; }
function get_the_category($id = 0) { return [ (object) ['term_id' => 1, 'name' => 'Tin kategorisi'] ]; }
function wp_get_post_terms($id, $tax, $a = []) { return [1]; }
function get_the_terms($id, $tax) { return $GLOBALS['__terms'][$tax] ?? false; }
function get_term_link($t) { return 'https://example.test/term/' . ($t->slug ?? ''); }
function get_terms($a) { return $GLOBALS['__terms'][$a['taxonomy'] ?? ''] ?? []; }
function wp_list_pluck($arr, $f) { return array_map(fn($x) => $x->$f, $arr); }
function get_page_template_slug($id) { return ''; }
function comments_template() { echo '<!-- comments -->'; }
function checked($a, $b, $e = true) { $r = $a == $b ? 'checked="checked"' : ''; if ($e) { echo $r; } return $r; }
function selected($a, $b, $e = true) { $r = $a == $b ? 'selected="selected"' : ''; if ($e) { echo $r; } return $r; }
function is_singular($t = null) { return true; }
function is_preview() { return false; }
function number_format_i18n($n) { return number_format($n); }
function paginate_links($a = []) { return '<nav>1</nav>'; }
function woocommerce_breadcrumb() {} function woocommerce_product_loop() { return have_posts(); }
function woocommerce_pagination() { the_posts_pagination(); }
function woocommerce_template_single_add_to_cart() { echo '<button>افزودن به سبد</button>'; }
function wc_get_product($id) { return new class { public function get_price_html() { return '۱٬۰۰۰٬۰۰۰ تومان'; } }; }
function do_shortcode($s) { return $s; }
function get_bloginfo($s) { return 'UTF-8'; }

class WP_Error {}
class Walker_Nav_Menu {}
class WP_Post {
    public $ID; public $post_type; public $post_title;
    public function __construct($id, $type, $title) { $this->ID = $id; $this->post_type = $type; $this->post_title = $title; }
}

class WP_Query {
    public $posts = []; public $found_posts = 0; public $max_num_pages = 1; private $i = -1;
    public function __construct($args = []) {
        $type = $args['post_type'] ?? 'post';
        $types = (array) $type;
        $n = $args['posts_per_page'] ?? 5;
        if ($n === -1) { $n = 100; }
        $cnt = 0;
        foreach ($GLOBALS['__posts'] as $id => $p) {
            if (in_array($p['type'], $types, true) && $cnt < $n) {
                if (!empty($args['post__not_in']) && in_array($id, (array) $args['post__not_in'], true)) { continue; }
                $this->posts[] = new WP_Post($id, $p['type'], $p['title']);
                $cnt++;
            }
        }
        $this->found_posts = count($this->posts);
    }
    public function have_posts() { return $this->i + 1 < count($this->posts); }
    public function the_post() { global $post; $this->i++; $p = $this->posts[$this->i]; $post = $p; $GLOBALS['post'] = $p; return $p; }
}

// sample data
function __seed_sample_data(): void {
    $GLOBALS['__posts'][1] = ['type' => 'product', 'title' => 'والاشر RGB', 'content' => 'محتوای تست محصول'];
    $GLOBALS['__posts'][2] = ['type' => 'projects', 'title' => 'تونل نوری قم', 'content' => 'محتوای پروژه'];
    $GLOBALS['__posts'][3] = ['type' => 'post', 'title' => 'مقاله تست', 'content' => 'محتوای مقاله'];
    $GLOBALS['__postmeta'][1] = ['true_sale' => 'true', 'zireonvan' => 'زیرعنوان', 'galerry_produ' => '11,12', 'faq' => ['item-0' => ['name_question' => 'سوال؟', 'answer_question' => 'جواب']]];
    $GLOBALS['__postmeta'][2] = ['foure-image' => 21, 'finish-pro-time' => '1404/01/01', 'mortabet_mahsole_2' => '[1]'];
    $GLOBALS['__terms']['city'] = [ (object) ['term_id' => 5, 'name' => 'قم', 'slug' => 'qom', 'count' => 3] ];
    $GLOBALS['__terms']['project_type'] = [ (object) ['term_id' => 6, 'name' => 'تونل نوری', 'slug' => 'light-tunnel', 'count' => 2] ];
    $GLOBALS['__terms']['pa_wattage'] = [ (object) ['name' => '50W', 'slug' => '50w'] ];
    $GLOBALS['__qobj'] = (object) ['term_id' => 9, 'name' => 'ریسه LED', 'slug' => 'led-lamp', 'description' => 'توضیحات دسته', 'count' => 8];
}

function the_ID() { echo get_the_ID(); }
function post_class($c = '') { echo 'class="' . esc_attr($c) . '"'; }
function the_permalink() { echo esc_url(get_permalink()); }
function the_excerpt() { echo esc_html(get_the_excerpt()); }
function wc_print_notices() { echo '<!-- wc notices -->'; }
function wc_get_template($f) { echo '<!-- wc template: ' . esc_html($f) . ' -->'; }
