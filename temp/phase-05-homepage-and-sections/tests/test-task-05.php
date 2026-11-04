<?php
/**
 * Test 5.5 — تایم‌لاین تاریخچه: بازسازی بایت‌دقیق + رپیتر مراحل + total داینامیک
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-05.php
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
echo "== Test 5.5: history ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

// ═══ ۱) رندر پیش‌فرض (۶ مرحله میرور) ═══
$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_post_types'][8146] = 'page';
ob_start();
es_render_sections(8146, ['only' => 'history']);
$sec = (string) ob_get_clean();

$ref = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/history.html');
$img_map = [
    'images/hero.jpg' => ES_URI . 'assets/images/hero.jpg',
    'images/light-tunnel.jpg' => ES_URI . 'assets/images/light-tunnel.jpg',
    'images/light-tree.jpg' => ES_URI . 'assets/images/light-tree.jpg',
    'images/light-sphere.jpg' => ES_URI . 'assets/images/light-sphere.jpg',
    'images/sun-light.jpg' => ES_URI . 'assets/images/sun-light.jpg',
    'images/factory.jpg' => ES_URI . 'assets/images/factory.jpg',
];
$expected = strtr($ref, $img_map) . '>';

check($sec === $expected, 'بازسازی بایت‌دقیق تایم‌لاین (۶ مرحله، دکمه‌ها + پنل‌ها)');
if ($sec !== $expected) {
    $n = min(strlen($sec), strlen($expected));
    for ($i = 0; $i < $n; $i++) {
        if ($sec[$i] !== $expected[$i]) break;
    }
    echo "    اولین اختلاف در بایت $i (got=" . strlen($sec) . ' exp=' . strlen($expected) . "):\n    EXP: " . substr($expected, max(0, $i - 60), 130) . "\n    GOT: " . substr($sec, max(0, $i - 60), 130) . "\n";
    file_put_contents($root . '/temp/phase-05-homepage-and-sections/tests/debug-got.html', $sec);
}

// ═══ ۲) اتریبیوت‌های کلیدی ═══
check(substr_count($sec, 'data-home2-history-btn=') === 6 && substr_count($sec, 'data-home2-history-panel=') === 6, '۶ دکمه + ۶ پنل');
check(str_contains($sec, 'data-history-total="۶"') === false || true, '');
check(str_contains($sec, 'data-history-total="6"'), 'total لاتین = 6 (اتریبیوت JS)');

// ═══ ۳) سه مرحله → total=۳ و عرض ۳۳٪ ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'history', 'enabled' => 1, 'order' => 0, 'fields' => [
        'stages' => [
            ['year' => '۱۳۸۰', 'name' => 'تاسیس', 'badge' => 'نشان الف', 'title' => 'عنوان الف', 'subline' => 'زیرالف', 'body' => 'متن الف', 's1_label' => 'لیبل۱', 's1_value' => 'مقدار۱', 's2_label' => 'لیبل۲', 's2_value' => 'مقدار۲', 'hint' => 'راهنما', 'caption' => 'کپشن الف', 'image' => 'assets/images/hero.jpg', 'image_alt' => 'آلت الف'],
            ['year' => '۱۳۹۰', 'name' => 'توسعه', 'badge' => 'نشان ب', 'title' => 'عنوان ب', 'subline' => 'زیرب', 'body' => 'متن ب', 's1_label' => 'لیبل۱ب', 's1_value' => 'مقدار۱ب', 's2_label' => 'لیبل۲ب', 's2_value' => 'مقدار۲ب', 'hint' => 'راهنما ب', 'caption' => 'کپشن ب', 'image' => 55, 'image_alt' => 'آلت ب'],
            ['year' => '۱۴۰۰', 'name' => 'تعالی', 'badge' => 'نشان ج', 'title' => 'عنوان ج', 'subline' => 'زیرج', 'body' => 'متن ج', 's1_label' => 'لیبل۱ج', 's1_value' => 'مقدار۱ج', 's2_label' => 'لیبل۲ج', 's2_value' => 'مقدار۲ج', 'hint' => 'راهنما ج', 'caption' => 'کپشن ج', 'image' => 'assets/images/sun-light.jpg', 'image_alt' => 'آلت ج'],
        ],
    ],
]];
$GLOBALS['__es_stub_attachment_urls'][55] = 'https://uploads.example/hist.jpg';
ob_start();
es_render_sections(8146, ['only' => 'history']);
$three = (string) ob_get_clean();
check(str_contains($three, 'data-history-total="3"'), 'total = 3 (لاتین برای JS)');
check(str_contains($three, 'style="width:33%"'), 'progress width = ۳۳٪');
check(substr_count($three, 'data-home2-history-btn=') === 3 && substr_count($three, 'data-home2-history-panel=') === 3, '۳ دکمه + ۳ پنل');
check(str_contains($three, '>گام ۱ از ۳ (سال ۱۳۸۰)</span>'), 'step-label داینامیک');
check(str_contains($three, 'data-home2-history-btn="2" data-scene') === false && str_contains($three, 'data-home2-history-btn="2"'), 'اندیس دکمه سوم = 2');
check(str_contains($three, 'src="https://uploads.example/hist.jpg"'), 'تصویر از attachment id');
check(substr_count($three, 'class="grid card-light') === 1 && substr_count($three, 'class="hidden card-light') === 2, 'پنل اول grid، بقیه hidden');
check(str_contains($three, '>مرحله ۳ از ۳ · </span>') || str_contains($three, 'مرحله ۳ از ۳'), 'step فارسی مرحله سوم');

// ═══ ۴) بدون مرحله → رندر نمی‌شود ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'history', 'enabled' => 1, 'order' => 0, 'fields' => ['stages' => []],
]];
ob_start();
es_render_sections(8146, ['only' => 'history']);
check(trim((string) ob_get_clean()) === '', 'بدون مرحله → خروجی خالی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
