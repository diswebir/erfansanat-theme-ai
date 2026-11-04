<?php
/**
 * Test 5.4 — سکشن درباره + آمار شمارنده: بازسازی بایت‌دقیق + رپیترها
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-04.php
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
echo "== Test 5.4: about ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Frontend\Icons;

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

// ═══ ۱) آیکون‌های جدید ═══
check(str_contains(Icons::svg('circle-check', 'lucide lucide-circle-check h-4.5 w-4.5 shrink-0 text-emerald-600'), '<path d="m9 12 2 2 4-4">'), 'آیکون circle-check');
check(str_contains(Icons::svg('award', 'lucide lucide-award h-4 w-4'), '<circle cx="12" cy="8" r="6">'), 'آیکون award');

// ═══ ۲) رندر پیش‌فرض (میرور) ═══
$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_post_types'][8146] = 'page';
ob_start();
es_render_sections(8146, ['only' => 'about']);
$sec = (string) ob_get_clean();

$ref = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/about.html');
$expected = strtr($ref, [
    'href="about.html"' => 'href="https://test.example/about/"',
    'src="images/factory.jpg"' => 'src="' . ES_URI . 'assets/images/factory.jpg"',
]) . '>';

check($sec === $expected, 'بازسازی بایت‌دقیق سکشن about (۴ آمار + ۳ چک‌لیست)');
if ($sec !== $expected) {
    $n = min(strlen($sec), strlen($expected));
    for ($i = 0; $i < $n; $i++) {
        if ($sec[$i] !== $expected[$i]) break;
    }
    echo "    اولین اختلاف در بایت $i (len got=" . strlen($sec) . " exp=" . strlen($expected) . "):\n    EXP: " . substr($expected, max(0, $i - 60), 130) . "\n    GOT: " . substr($sec, max(0, $i - 60), 130) . "\n";
}

// ═══ ۳) رپیترها: آمار سفارشی + چک‌لیست سفارشی ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'about', 'enabled' => 1, 'order' => 0, 'fields' => [
        'kicker' => 'کیکر آزمایشی',
        'title_highlight' => '۳۰ سال',
        'excerpt' => 'متن آزمایشی معرفی',
        'points' => [['text' => 'مورد یک'], ['text' => 'مورد دو']],
        'cta_text' => 'دکمه آزمایشی', 'cta_url' => '/factory/',
        'stats' => [
            ['target' => '42', 'suffix' => '٪', 'label' => 'آزمون', 'sub' => 'زیرآزمون'],
            ['target' => '7', 'suffix' => '+', 'label' => 'هفت', 'sub' => 'sub7'],
        ],
    ],
]];
ob_start();
es_render_sections(8146, ['only' => 'about']);
$custom = (string) ob_get_clean();
check(str_contains($custom, 'data-counter-target="42" data-counter-suffix="٪"'), 'آمار سفارشی: target/suffix از رپیتر');
check(str_contains($custom, '>۴۲٪<'), 'آمار سفارشی: عدد فارسی ۴۲٪');
check(substr_count($custom, 'data-counter-target=') === 2, 'دو کارت آمار (بدون پیش‌فرض‌ها)');
check(str_contains($custom, 'custom="0.12"') && str_contains($custom, 'custom="0.16999999999999998"'), 'دلی آیتم دوم = مقدار میرور');
check(substr_count($custom, 'lucide-circle-check') === 2, 'دو مورد چک‌لیست سفارشی');
check(str_contains($custom, '>متن آزمایشی معرفی</p>') && str_contains($custom, '>۳۰ سال</span>'), 'متن و هایلایت از فیلدها');
check(str_contains($custom, 'href="https://test.example/factory/"') && str_contains($custom, '>دکمه آزمایشی</span>'), 'CTA سفارشی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
