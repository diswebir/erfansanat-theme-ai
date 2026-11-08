<?php
/** OPTIONS PANEL WIRING TEST — new 8-tab panel renders, front-end reads options. */
$PHP = '/home/user/tools/node_modules/.bin/php-wasm-cli';
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

// ---- render a front-end page FIRST (subprocess; header is require_once) ----
$render = '/home/user/erfansanat-theme-ai/temp/phase-08-ajax-filters-and-audit/tests/options-render-front.php';
file_put_contents($render, '<?php
$WP = "/home/user/wp-test";
@unlink($WP."/wp-content/database/.ht.sqlite");
define("WP_INSTALLING", true);
require $WP."/wp-load.php";
require_once ABSPATH."wp-admin/includes/upgrade.php";
wp_install("t","admin","a@b.c",true,"","admin123");
add_filter("locale", fn() => "fa_IR");
switch_theme("erfansanat-theme-ai");
require_once get_theme_file_path("functions.php");
do_action("after_setup_theme"); do_action("init");
// set custom options BEFORE render
update_option("erfansanat_theme_options", [
  "general" => ["site_name" => "تست سایت", "meta_description" => "توضیح تست"],
  "header" => ["announcement" => "IMPLEMENTED announcement تست", "cta_text" => "تماس با ما تست", "sticky_header" => "0"],
  "footer" => ["phone" => "031-99999999", "email" => "test@erfansanat.com", "footer_text" => "کپی‌رایت تست"],
  "brand" => ["brand_color" => "#ff0000", "custom_css" => ".x{color:red}"],
  "advanced" => ["maintenance_mode" => "0"],
], false);
query_posts(["post_type" => "page", "name" => "__none__"]);
$home = wp_insert_post(["post_type"=>"page","post_title"=>"خانه","post_status"=>"publish"]);
update_option("page_on_front", $home); update_option("show_on_front", "page");
query_posts(["post_type" => "post", "posts_per_page" => 3]);
include get_theme_file_path("home.php");
');
$out = tempnam(sys_get_temp_dir(), 'ow') . '.html';
shell_exec(escapeshellarg($PHP) . ' ' . escapeshellarg($render) . ' > ' . escapeshellarg($out) . ' 2>/dev/null');
$h = (string) file_get_contents($out);
@unlink($out);
check('front page renders', strlen($h) > 20000);
check('brand color CSS var from option', strpos($h, '--ese-brand:#ff0000') !== false);
check('custom_css output in head', strpos($h, 'erfansanat-custom-css') !== false && strpos($h, '.x{color:red}') !== false);
check('announcement bar rendered from option', strpos($h, 'announcement تست') !== false);
check('CTA text from option', strpos($h, 'تماس با ما تست') !== false);
check('header not fixed when sticky off', strpos($h, 'relative inset-x-0 top-0 z-50') !== false);
check('footer phone from option', strpos($h, '031-99999999') !== false);
check('footer email from option', strpos($h, 'test@erfansanat.com') !== false);
check('footer copyright from option', strpos($h, 'کپی‌رایت تست') !== false);
check('meta description from option', strpos($h, 'توضیح تست') !== false);

// ---- admin panel render (main process, admin context) ----
@unlink('/home/user/wp-test/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
define('WP_ADMIN', true);
require '/home/user/wp-test/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
add_filter('locale', fn() => 'fa_IR');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('after_setup_theme');
do_action('init');
wp_set_current_user(1);

use ErfanSanat\Theme\Admin\ThemeOptions;
ob_start();
ThemeOptions::render();
$panel = ob_get_clean();
check('panel renders', strlen($panel) > 3000);
foreach (['general','header','footer','shop','forms','seo','brand','advanced'] as $tab) {
    check("panel has tab $tab", strpos($panel, 'data-tab="' . $tab . '"') !== false);
}
check('panel has media uploader buttons', strpos($panel, 'erfansanat-media-btn') !== false);
check('panel has toggles', strpos($panel, 'erfansanat-toggle') !== false);
check('panel has select for per-page', strpos($panel, 'shop[products_per_page]') !== false);
check('panel has custom css textarea', strpos($panel, 'brand[custom_css]') !== false);
check('defaults have >= 8 groups', count(ThemeOptions::defaults()) >= 8);

// save roundtrip with new fields
$_POST = [
    'erfansanat_options_submit' => '1',
    'erfansanat_nonce' => wp_create_nonce(ThemeOptions::NONCE),
    'options' => [
        'general' => ['site_name' => 'نام جدید', 'meta_description' => 'desc'],
        'shop' => ['products_per_page' => '12'],
        'advanced' => ['lazy_images' => '1'],
    ],
];
$_REQUEST = $_POST;
ThemeOptions::save();
$stored = get_option('erfansanat_theme_options', []);
check('save persists new site_name', ($stored['general']['site_name'] ?? '') === 'نام جدید');
check('save persists shop per-page', ($stored['shop']['products_per_page'] ?? '') === '12');
$_POST = []; $_REQUEST = [];

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
