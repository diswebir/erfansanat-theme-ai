<?php
/**
 * Test 1.7 — AssetManager: نسخه‌گذاری filemtime، Enqueue، Preload فونت، defer و داده ES
 * اجرا: /home/user/tools/php -f temp/phase-01-core-and-assets/tests/test-task-07.php
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
echo "== Test 1.7: AssetManager ==\n";

require $root . '/temp/harness/wp-stubs.php';
es_stubs_reset();
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;

require $root . '/functions.php';

check(class_exists(\ErfanSanat\Theme\Frontend\AssetManager::class), 'کلاس AssetManager از اوتولودر resolve شد');

// ثبت هوک‌ها
$hooks = $GLOBALS['__wp_hooks'];
$find = static function (string $where, string $hook, array $cb) use ($hooks): bool {
    foreach ($hooks[$where][$hook] ?? [] as $entry) {
        if (is_array($entry['cb']) && $entry['cb'] === $cb) {
            return true;
        }
    }
    return false;
};
check($find('actions', 'wp_enqueue_scripts', [\ErfanSanat\Theme\Frontend\AssetManager::class, 'enqueue']), 'هوک wp_enqueue_scripts → enqueue');
check($find('actions', 'wp_head', [\ErfanSanat\Theme\Frontend\AssetManager::class, 'preload_fonts']), 'هوک wp_head → preload_fonts (اولویت ۱)');
check($find('filters', 'script_loader_tag', [\ErfanSanat\Theme\Frontend\AssetManager::class, 'add_defer_to_main_js']), 'فیلتر script_loader_tag → defer');

// نسخه‌گذاری
$css_mtime = (string) filemtime($root . '/assets/css/styles.css');
check(\ErfanSanat\Theme\Frontend\AssetManager::version('assets/css/styles.css') === $css_mtime, 'version = filemtime فایل واقعی');
check(\ErfanSanat\Theme\Frontend\AssetManager::version('assets/css/nope.css') === ERFANSANAT_VERSION, 'فایل ناموجود → نسخه قالب (fallback)');

// Enqueue واقعی
\ErfanSanat\Theme\Frontend\AssetManager::enqueue();

$styles  = $GLOBALS['__wp_hooks']['enqueued_styles'];
$scripts = $GLOBALS['__wp_hooks']['enqueued_scripts'];
check(isset($styles['erfansanat-main']) && str_ends_with($styles['erfansanat-main']['src'], 'assets/css/styles.css'), 'استایل سراسری با src صحیح');
check(($styles['erfansanat-main']['ver'] ?? '') === $css_mtime, 'نسخه استایل = filemtime');
check(isset($scripts['erfansanat-main']) && str_ends_with($scripts['erfansanat-main']['src'], 'assets/js/main.js'), 'اسکریپت سراسری با src صحیح');

// داده ES
$localized = $GLOBALS['__wp_hooks']['localized']['erfansanat-main'] ?? [];
check(($localized['name'] ?? '') === 'ES', 'wp_localize_script با آبجکت ES');
$es = $localized['data'] ?? [];
check(isset($es['ajaxUrl']) && str_contains($es['ajaxUrl'], 'admin-ajax.php'), 'ES.ajaxUrl تنظیم شده');
check(isset($es['nonce']) && $es['nonce'] !== '', 'ES.nonce تنظیم شده');
check(($es['homeUrl'] ?? '') === 'https://test.example/', 'ES.homeUrl صحیح');
check(str_contains($es['shopUrl'] ?? '', '/shop'), 'ES.shopUrl به آرشیو فروشگاه اشاره می‌کند');
check(($es['themeVersion'] ?? '') === '1.0.0', 'ES.themeVersion صحیح');

// فیلتر defer
$tag = '<script src="https://test.example/wp-content/themes/erfansanat/assets/js/main.js?ver=1"></script>';
$deferred = \ErfanSanat\Theme\Frontend\AssetManager::add_defer_to_main_js($tag, 'erfansanat-main', $tag);
check(str_contains($deferred, '<script defer '), 'defer به main.js اضافه شد');
$already = \ErfanSanat\Theme\Frontend\AssetManager::add_defer_to_main_js('<script defer src="x.js"></script>', 'erfansanat-main', 'x.js');
check(substr_count($already, 'defer') === 1, 'defer تکراری اضافه نمی‌شود');
$other = \ErfanSanat\Theme\Frontend\AssetManager::add_defer_to_main_js($tag, 'wc-add-to-cart', $tag);
check(!str_contains($other, '<script defer '), 'هندل‌های دیگر دست‌نخورده می‌مانند');

// Preload فونت‌ها
ob_start();
\ErfanSanat\Theme\Frontend\AssetManager::preload_fonts();
$head = (string) ob_get_clean();
check(substr_count($head, 'rel="preload"') === 3, 'سه تگ preload چاپ شد');
check(str_contains($head, 'as="font"') && str_contains($head, 'crossorigin'), 'preload با as=font و crossorigin');
foreach (['vazirmatn-arabic', 'vazirmatn-latin', 'vazirmatn-latin-ext'] as $subset) {
    check(str_contains($head, $subset . '.woff2'), "preload: {$subset}.woff2");
}

// نقشه دارایی شرطی + فیلتر توسعه
check(\ErfanSanat\Theme\Frontend\AssetManager::assets_map() === [], 'نقشه پیش‌فرض دارایی شرطی خالی است');
$GLOBALS['__wp_hooks']['filters']['es_asset_map'] = [[
    'cb'       => static fn(array $m): array => $m + ['product' => ['js' => 'assets/js/single-product.js']],
    'priority' => 10,
]];
$map = \ErfanSanat\Theme\Frontend\AssetManager::assets_map();
check(isset($map['product']['js']), 'فیلتر es_asset_map نقشه را توسعه می‌دهد');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
