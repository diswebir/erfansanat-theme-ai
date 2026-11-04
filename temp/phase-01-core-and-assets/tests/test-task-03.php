<?php
/**
 * Test 1.3 — ThemeSetup: Theme Supports، ۴ منو، سایز تصاویر و هوک فعال‌سازی
 * اجرا: /home/user/tools/php -f temp/phase-01-core-and-assets/tests/test-task-03.php
 */
declare(strict_types=1);

error_reporting(E_ALL);
$failures = [];
$passes   = 0;
function check(bool $cond, string $label): void {
    global $failures, $passes;
    if ($cond) { $passes++; echo "  PASS  $label\n"; }
    else       { $failures[] = $label; echo "  FAIL  $label\n"; }
}

$root = dirname(ES_SCRIPT_PATH, 4);
echo "== Test 1.3: ThemeSetup و Theme Supports ==\n";

require $root . '/temp/harness/wp-stubs.php';
es_stubs_reset();
$GLOBALS['__es_theme_dir'] = $root;

require $root . '/functions.php';

check(class_exists(\ErfanSanat\Theme\ThemeSetup::class), 'کلاس ThemeSetup از اوتولودر resolve شد');

// ثبت هوک‌ها هنگام init (بوت قبلاً در functions.php انجام شده)
$hooks = $GLOBALS['__wp_hooks']['actions'];
$setup_hook_registered = false;
$activate_hook_registered = false;
foreach ($hooks['after_setup_theme'] ?? [] as $entry) {
    if (is_array($entry['cb']) && $entry['cb'][0] === \ErfanSanat\Theme\ThemeSetup::class && $entry['cb'][1] === 'setup') {
        $setup_hook_registered = true;
    }
}
foreach ($hooks['after_switch_theme'] ?? [] as $entry) {
    if (is_array($entry['cb']) && $entry['cb'][0] === \ErfanSanat\Theme\ThemeSetup::class && $entry['cb'][1] === 'activate') {
        $activate_hook_registered = true;
    }
}
check($setup_hook_registered, 'after_setup_theme → ThemeSetup::setup ثبت شده');
check($activate_hook_registered, 'after_switch_theme → ThemeSetup::activate ثبت شده');

// اجرای واقعی setup
\ErfanSanat\Theme\ThemeSetup::setup();

$supports = $GLOBALS['__wp_hooks']['supports'];
check($supports['title-tag'] ?? false, 'add_theme_support: title-tag');
check($supports['post-thumbnails'] ?? false, 'add_theme_support: post-thumbnails');
check(is_array($supports['html5'] ?? null) && in_array('search-form', $supports['html5'], true) && in_array('comment-form', $supports['html5'], true), 'add_theme_support: html5 (search-form/comment-form)');
check(is_array($supports['custom-logo'] ?? null), 'add_theme_support: custom-logo');
check($supports['woocommerce'] ?? false, 'add_theme_support: woocommerce');
check($supports['automatic-feed-links'] ?? false, 'add_theme_support: automatic-feed-links');
// گالری پیش‌فرض ووکامرس عمداً ثبت نشده باشد
check(!isset($supports['wc-product-gallery-zoom']) && !isset($supports['wc-product-gallery-lightbox']) && !isset($supports['wc-product-gallery-slider']), 'گالری پیش‌فرض ووکامرس ثبت نشده (جایگزین: گالری میرور)');

// ۴ منوی بلوپرینت با اسلاگ دقیق
$menus = $GLOBALS['__wp_hooks']['menus'];
check(isset($menus['main-mega-menu']), 'منو: main-mega-menu');
check(isset($menus['mobile-menu']), 'منو: mobile-menu');
check(isset($menus['tolidat-sherkat']), 'منو: tolidat-sherkat');
check(isset($menus['landing']), 'منو: landing');
check(count($menus) === 4, 'دقیقاً ۴ موقعیت منو ثبت شده');

// سایز تصاویر کارت
$sizes = $GLOBALS['__wp_hooks']['sizes'];
check(($sizes['es-card-600'] ?? null) === ['w' => 600, 'h' => 400, 'crop' => true], 'add_image_size: es-card-600 (600×400 crop)');
check(($sizes['es-card-400'] ?? null) === ['w' => 400, 'h' => 300, 'crop' => true], 'add_image_size: es-card-400 (400×300 crop)');

// فعال‌سازی → flush
$flushed_before = $GLOBALS['__wp_hooks']['flushed'];
\ErfanSanat\Theme\ThemeSetup::activate();
check($GLOBALS['__wp_hooks']['flushed'] === $flushed_before + 1, 'activate() یکبار flush_rewrite_rules اجرا می‌کند');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
