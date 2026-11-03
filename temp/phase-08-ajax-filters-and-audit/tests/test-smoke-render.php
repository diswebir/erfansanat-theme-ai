<?php
/**
 * SMOKE TEST — actually EXECUTE the theme under php-wasm with WP stubs:
 * boot functions.php, then render key templates with sample data and
 * assert no fatal errors + expected markers in output.
 *
 * Run: php-wasm-cli temp/phase-08-ajax-filters-and-audit/tests/test-smoke-render.php
 */

$THEME = dirname(__DIR__, 3);
require __DIR__ . '/wp-stubs.php';

$fail = 0;
function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

// 1. Boot the theme (functions.php → Autoloader → Theme::instance → hooks)
ob_start();
require $THEME . '/functions.php';
ob_end_clean();
check('functions.php boots without fatal', class_exists('ErfanSanat\Theme\Theme'));
check('Autoloader loads classes', class_exists('ErfanSanat\Theme\DeBloat') && class_exists('ErfanSanat\Theme\Admin\ThemeOptions'));

// fire after_setup_theme + init
ob_start();
do_action('after_setup_theme');
do_action('init');
ob_end_clean();
check('theme supports registered', array_key_exists('woocommerce', $GLOBALS['__support'] ?? []) && array_key_exists('title-tag', $GLOBALS['__support'] ?? []));
check('CPT projects registered', isset($GLOBALS['__cpts']['projects']));
check('taxonomies registered', isset($GLOBALS['__tax']['project_type']) && isset($GLOBALS['__tax']['city']));
check('4 menu locations registered', count($GLOBALS['__menus'] ?? []) === 4);

// seed data
__seed_sample_data();

// helper to render a template safely
function render(string $file): string {
    global $THEME, $post, $wp_query;
    $posts = [];
    foreach ($GLOBALS['__posts'] as $id => $p) { $posts[] = new WP_Post($id, $p['type'], $p['title']); }
    $wp_query = new WP_Query(['post_type' => ['product', 'projects', 'post'], 'posts_per_page' => 10]);
    $post = $posts[0] ?? null; $GLOBALS['post'] = $post;
    ob_start();
    include $THEME . '/' . $file;
    return (string) ob_get_clean();
}

// 2. Render templates
$out = render('front-page.php');
check('front-page renders without fatal', strlen($out) > 5000);
check('front-page includes header shell', strpos($out, 'data-navbar-header') !== false);
check('front-page includes footer', strpos($out, '<footer') !== false);
check('front-page includes hero section', strpos($out, 'data-home') !== false);

$out = render('single-product.php');
check('single-product renders without fatal', strlen($out) > 1000);
check('single-product shows title', strpos($out, 'والاشر') !== false || strpos($out, 'عنوان تست') !== false);
check('single-product shows zireonvan', strpos($out, 'زیرعنوان') !== false);
check('single-product buy button (true_sale=true)', strpos($out, 'افزودن به سبد') !== false);
check('single-product faq rendered', strpos($out, 'سوال؟') !== false);

$out = render('single-projects.php');
check('single-projects renders', strpos($out, 'تونل') !== false || strlen($out) > 1000);

$out = render('archive-product.php');
check('archive-product renders grid', strpos($out, 'card-glow') !== false);

$out = render('taxonomy-product_cat.php');
check('taxonomy-product_cat renders term title', strpos($out, 'ریسه') !== false);

$out = render('home.php');
check('home.php blog index renders', strlen($out) > 500);

$out = render('404.php');
check('404 renders', strpos($out, '404') !== false);

// 3. es_option / es_sections helpers work
check('es_option returns default', es_option('footer.email') === 'info@erfansanat.com');
check('es_sections returns array', is_array(es_sections(8146)));

// 4. AJAX filter handler executes and returns JSON
$_REQUEST['nonce'] = 'test-nonce';
$_GET['s'] = 'والاشر';
ob_start();
try { \ErfanSanat\Theme\Ajax\Filters::search_products(); } catch (Throwable $e) {}
$json = (string) ob_get_clean();
check('ajax search returns JSON success', strpos($json, '"success":true') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
