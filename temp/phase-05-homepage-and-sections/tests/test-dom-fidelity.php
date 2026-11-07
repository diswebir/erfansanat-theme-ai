<?php
/**
 * DOM FIDELITY TEST — render front-page.php under WP stubs and compare its
 * structure against the static index.html: section count, key classes,
 * and data-* hooks must match (100% DOM preservation rule).
 *
 * Run: php-wasm-cli temp/phase-05-homepage-and-sections/tests/test-dom-fidelity.php
 */

$THEME = dirname(__DIR__, 3);
require $THEME . '/temp/phase-08-ajax-filters-and-audit/tests/wp-stubs.php';

$fail = 0;
function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

// render front-page
require $THEME . '/functions.php';
__seed_sample_data();
global $post, $wp_query;
$wp_query = new WP_Query(['post_type' => ['product', 'projects', 'post'], 'posts_per_page' => 10]);
$post = new WP_Post(1, 'product', 'والاشر RGB'); $GLOBALS['post'] = $post;
ob_start();
include $THEME . '/front-page.php';
$rendered = (string) ob_get_clean();

// static reference
$static = file_get_contents('/home/user/static-mirror/static-mirror/index.html');
$staticMain = preg_match('/<main.*?<\/main>/s', $static, $m) ? $m[0] : $static;

// 1. section count parity
$rc = substr_count($rendered, '<section');
$sc = substr_count($staticMain, '<section');
check("section count parity (rendered=$rc static=$sc)", $rc === $sc);

// 2. key structural markers from static survive
foreach (['data-navbar-header', '<footer', 'data-mobile-menu', 'data-search-modal-open', 'data-cart-modal-open', 'id="main"', 'class="site-main"'] as $marker) {
    check("marker present in rendered: $marker", strpos($rendered, $marker) !== false);
}

// 3. data-* hooks of the static shell all present in rendered output
preg_match_all('/data-[a-z0-9-]+/i', $staticMain, $mm);
$shellHooks = array_unique($mm[0]);
$missing = [];
foreach ($shellHooks as $hook) {
    if (strpos($rendered, $hook) === false) { $missing[] = $hook; }
}
check('all static main data-* hooks preserved (' . count($shellHooks) . ' hooks)', $missing === []);
foreach (array_slice($missing, 0, 10) as $h) { echo "  missing: $h\n"; }

// 4. hero section classes preserved
check('hero section id="home" present', strpos($rendered, 'id="home"') !== false);

// 5. no PHP notices/leaks in output
check('no "Fatal error"/"Warning" text leaked into output', strpos($rendered, 'Fatal error') === false && strpos($rendered, 'Warning:') === false);

// 6. asset URLs point to theme URI
check('assets use theme URI', substr_count($rendered, 'wp-content/themes/erfansanat-theme-ai/assets/') > 5);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
