<?php
/**
 * Test 5.7 — کاتالوگ + خدمات تب‌دار: بازسازی بایت‌دقیق + رپیتر
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-07.php
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
echo "== Test 5.7: catalog + services ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Frontend\Icons;

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

// ═══ ۱) آیکون‌های جدید ═══
check(str_contains(Icons::svg('download', 'lucide lucide-download h-4 w-4'), '<path d="M12 15V3">'), 'آیکون download');
check(str_contains(Icons::svg('landmark', 'x') !== Icons::svg('milestone', 'x') ? 'y' : 'n', 'y'), 'landmark ≠ milestone');
check(str_contains(Icons::svg('waypoints', 'x'), '<path'), 'آیکون waypoints');

// ═══ ۲) کاتالوگ: بازسازی بایت‌دقیق ═══
$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_post_types'][8146] = 'page';
$GLOBALS['__es_stub_wc_page_ids'] = ['shop' => 9];
$GLOBALS['__es_stub_permalinks'] = [9 => 'https://test.example/shop/'];
ob_start();
es_render_sections(8146, ['only' => 'catalog']);
$cat = (string) ob_get_clean();
$ref = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/catalog.html');
$expected = strtr($ref, [
    'href="shop.html"' => 'href="https://test.example/shop/"',
    'src="images/light-tunnel.jpg"' => 'src="' . ES_URI . 'assets/images/light-tunnel.jpg"',
    'src="images/light-tree.jpg"' => 'src="' . ES_URI . 'assets/images/light-tree.jpg"',
]) . '>';
check($cat === $expected, 'کاتالوگ: بازسازی بایت‌دقیق');
if ($cat !== $expected) {
    $n = min(strlen($cat), strlen($expected));
    for ($i = 0; $i < $n; $i++) { if ($cat[$i] !== $expected[$i]) break; }
    echo "    diff@$i (got=" . strlen($cat) . " exp=" . strlen($expected) . "):\n    EXP: " . substr($expected, max(0, $i-60), 130) . "\n    GOT: " . substr($cat, max(0, $i-60), 130) . "\n";
}

// ═══ ۳) کاتالوگ: فایل PDF سفارشی ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'catalog', 'enabled' => 1, 'order' => 0, 'fields' => [
        'title' => 'کاتالوگ آزمایشی', 'desc' => 'توضیح آزمایشی', 'file' => 'assets/media/catalog.pdf',
        'button_text' => 'دانلود', 'note' => 'یادداشت آزمایشی',
        'cover' => 'assets/images/factory.jpg', 'version' => 'نسخه ۱۴۰۵',
    ],
]];
ob_start();
es_render_sections(8146, ['only' => 'catalog']);
$cat2 = (string) ob_get_clean();
check(str_contains($cat2, 'href="' . ES_URI . 'assets/media/catalog.pdf"'), 'دکمه → فایل PDF از media');
check(str_contains($cat2, '>کاتالوگ آزمایشی</h3>') && str_contains($cat2, '>دانلود</span>'), 'فیلدهای کاتالوگ');

// ═══ ۴) خدمات: بازسازی بایت‌دقیق ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [];
ob_start();
es_render_sections(8146, ['only' => 'services']);
$svc = (string) ob_get_clean();
$ref2 = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/services.html');
$img_map = [
    'images/light-sphere.jpg' => ES_URI . 'assets/images/light-sphere.jpg',
    'images/hero.jpg' => ES_URI . 'assets/images/hero.jpg',
    'images/string-lights.jpg' => ES_URI . 'assets/images/string-lights.jpg',
    'images/light-element.jpg' => ES_URI . 'assets/images/light-element.jpg',
    'images/chandelier.jpg' => ES_URI . 'assets/images/chandelier.jpg',
];
$expected2 = strtr($ref2, $img_map) . '>';
check($svc === $expected2, 'خدمات: بازسازی بایت‌دقیق (۵ ردیف + پیش‌نمایش)');
if ($svc !== $expected2) {
    $n = min(strlen($svc), strlen($expected2));
    for ($i = 0; $i < $n; $i++) { if ($svc[$i] !== $expected2[$i]) break; }
    echo "    diff@$i (got=" . strlen($svc) . " exp=" . strlen($expected2) . "):\n    EXP: " . substr($expected2, max(0, $i-60), 130) . "\n    GOT: " . substr($svc, max(0, $i-60), 130) . "\n";
}

// ═══ ۵) خدمات: شمارنده و total داینامیک ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'services', 'enabled' => 1, 'order' => 0, 'fields' => [
        'rows' => [
            ['title' => 'خدمت الف', 'desc' => 'توضیح الف', 'icon' => 'landmark', 'image' => 'assets/images/hero.jpg'],
            ['title' => 'خدمت ب', 'desc' => 'توضیح ب', 'icon' => 'milestone', 'image' => 55],
            ['title' => 'خدمت ج', 'desc' => 'توضیح ج', 'icon' => 'waypoints', 'image' => 'assets/images/sun-light.jpg'],
        ],
    ],
]];
$GLOBALS['__es_stub_attachment_urls'][55] = 'https://uploads.example/svc.jpg';
ob_start();
es_render_sections(8146, ['only' => 'services']);
$svc2 = (string) ob_get_clean();
check(str_contains($svc2, 'data-service-counter="۱ / ۳"') && str_contains($svc2, 'data-service-counter="۳ / ۳"'), 'شمارنده فارسی با total=۳');
check(str_contains($svc2, 'data-service-num="2"') && str_contains($svc2, '>۰۳</span>'), 'شماره ۰۳ برای ردیف سوم');
check(str_contains($svc2, 'src="https://uploads.example/svc.jpg"'), 'تصویر attachment در ردیف');
check(substr_count($svc2, 'data-service-row=') === 3 && substr_count($svc2, 'data-service-preview-icon=') === 3, '۳ ردیف + ۳ آیکون پیش‌نمایش');
check(str_contains($svc2, '>خدمت الف</span>') && str_contains($svc2, 'alt="خدمت الف"'), 'عنوان ردیف + alt');

// ═══ ۶) بدون خدمت → خروجی خالی ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [['id' => 'services', 'enabled' => 1, 'order' => 0, 'fields' => ['rows' => []]]];
ob_start();
es_render_sections(8146, ['only' => 'services']);
check(trim((string) ob_get_clean()) === '', 'بدون خدمت → خروجی خالی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
