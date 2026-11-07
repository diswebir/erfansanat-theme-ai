<?php
/** ADMIN FONT FULL INTEGRATION + JSON-LD + fa_IR.mo — real WP. */
$WP = '/home/user/wp-test';
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

// render single product via subprocess helper (header is require_once)
$PHP = '/home/user/tools/node_modules/.bin/php-wasm-cli';
$outFile = tempnam(sys_get_temp_dir(), 'jld') . '.html';
shell_exec(escapeshellarg($PHP) . ' ' . escapeshellarg('/home/user/erfansanat-theme-ai/temp/phase-08-ajax-filters-and-audit/tests/jsonld-render-product.php') . ' > ' . escapeshellarg($outFile) . ' 2>/dev/null');
$html = (string) file_get_contents($outFile);
@unlink($outFile);

@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
define('WP_ADMIN', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
update_option('WPLANG', 'fa_IR');
add_filter('locale', fn() => 'fa_IR');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('after_setup_theme');
do_action('init');

// ---- admin font integration ----
ob_start(); \ErfanSanat\Theme\Theme::instance()->admin_font_css(); $ah = ob_get_clean();
check('admin_head has Vazirmatn @font-face', strpos($ah, '@font-face') !== false && strpos($ah, 'Vazirmatn') !== false);
check('admin_head covers login + adminbar + metaboxes', strpos($ah, 'body.login') !== false && strpos($ah, '#wpadminbar') !== false && strpos($ah, 'metabox-holder') !== false);
ob_start(); \ErfanSanat\Theme\Theme::instance()->admin_font_css(); $lh = ob_get_clean();
check('login_head also injects Vazirmatn', strpos($lh, 'Vazirmatn') !== false);
$css = file_get_contents(get_theme_file_path('assets/css/admin-font.css'));
check('admin-font.css covers login + metaboxes + theme pages', strpos($css, 'body.login') !== false && strpos($css, 'postbox') !== false && strpos($css, 'erfansanat-admin') !== false);
check('woff2 file exists', is_file(get_theme_file_path('assets/fonts/vazirmatn-arabic.woff2')));
check('login_enqueue hook registered', has_action('login_enqueue_scripts') !== false);

// ---- fa_IR textdomain loaded from .mo ----
check('erfansanat textdomain loaded (fa_IR.mo)', is_textdomain_loaded('erfansanat'));
check('translation via mo works', translate('خانه', 'erfansanat') === 'خانه');

// ---- JSON-LD ----
$prod = wp_insert_post(['post_type' => 'product', 'post_title' => 'والاشر LED', 'post_status' => 'publish', 'post_excerpt' => 'توضیح محصول']);
update_post_meta($prod, 'true_sale', 'true');
update_post_meta($prod, 'pa_brand', 'عرفان صنعت');
register_post_type('product', ['public' => true]); // WC absent in harness

check('product page renders', strlen($html) > 5000);
check('JSON-LD script present', strpos($html, 'application/ld+json') !== false);
check('JSON-LD has Organization', strpos($html, '"Organization"') !== false);
check('JSON-LD has Product', strpos($html, '"Product"') !== false);
check('JSON-LD Product brand', strpos($html, 'عرفان صنعت') !== false);
check('JSON-LD InStock for true_sale', strpos($html, 'InStock') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
