<?php
/**
 * Test 5.6 — سکشن دسته‌بندی تولیدات: بازسازی بایت‌دقیق + رپیتر کارت‌ها + هلپر ترم
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-06.php
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
echo "== Test 5.6: categories ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

// ═══ ۱) هلپر ترم: thumbnail دقیق ═══
$GLOBALS['__wp_term_meta'][77]['thumbnail'] = 91;
$GLOBALS['__es_stub_attachment_urls'][91] = 'https://uploads.example/cat.jpg';
check(es_get_category_thumb(77) === 'https://uploads.example/cat.jpg', 'es_get_category_thumb از آیدی پیوست');
$GLOBALS['__wp_term_meta'][78]['thumbnail'] = 'assets/images/cat.png';
check(es_get_category_thumb(78) === ES_URI . 'assets/images/cat.png', 'es_get_category_thumb از مسیر assets');
check(es_get_category_thumb(79) === '', 'ترم بدون تصویر → خالی');
$GLOBALS['__wp_term_meta'][77]['faq'] = '{"item-0":{"name_question":"س؟","answer_question":"ج"}}';
$faq = es_get_category_faq(77);
check(($faq[0]['name_question'] ?? '') === 'س؟', 'es_get_category_faq ردیف JSON');

// ═══ ۲) رندر پیش‌فرض (میرور) ═══
$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_post_types'][8146] = 'page';
ob_start();
es_render_sections(8146, ['only' => 'categories']);
$sec = (string) ob_get_clean();

$ref = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/categories.html');
$expected = strtr($ref, [
    'href="product/artam-light-tree.html"' => 'href="https://test.example/product/artam-light-tree/"',
    'href="product/sun-light-48.html"' => 'href="https://test.example/product/sun-light-48/"',
    'href="product/foroogh-chandelier.html"' => 'href="https://test.example/product/foroogh-chandelier/"',
    'href="product/needle-string-lights.html"' => 'href="https://test.example/product/needle-string-lights/"',
    'href="product/point-light-pixel.html"' => 'href="https://test.example/product/point-light-pixel/"',
    'href="product/wall-washer.html"' => 'href="https://test.example/product/wall-washer/"',
    'href="contact.html"' => 'href="https://test.example/contact/"',
    'src="images/light-tree.jpg"' => 'src="' . ES_URI . 'assets/images/light-tree.jpg"',
    'src="images/sun-light.jpg"' => 'src="' . ES_URI . 'assets/images/sun-light.jpg"',
    'src="images/chandelier.jpg"' => 'src="' . ES_URI . 'assets/images/chandelier.jpg"',
    'src="images/string-lights.jpg"' => 'src="' . ES_URI . 'assets/images/string-lights.jpg"',
    'src="images/light-sphere.jpg"' => 'src="' . ES_URI . 'assets/images/light-sphere.jpg"',
    'src="images/light-tunnel.jpg"' => 'src="' . ES_URI . 'assets/images/light-tunnel.jpg"',
]) . '>';

check($sec === $expected, 'بازسازی بایت‌دقیق سکشن دسته‌بندی‌ها (۶ کارت + CTA)');
if ($sec !== $expected) {
    $n = min(strlen($sec), strlen($expected));
    for ($i = 0; $i < $n; $i++) {
        if ($sec[$i] !== $expected[$i]) break;
    }
    echo "    اولین اختلاف در بایت $i (got=" . strlen($sec) . ' exp=' . strlen($expected) . "):\n    EXP: " . substr($expected, max(0, $i - 60), 130) . "\n    GOT: " . substr($sec, max(0, $i - 60), 130) . "\n";
}

// ═══ ۳) رپیتر سفارشی ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'categories', 'enabled' => 1, 'order' => 0, 'fields' => [
        'kicker' => 'کیکر آزمایشی', 'title_highlight' => 'برند آزمایشی', 'excerpt' => 'متن آزمایشی',
        'cards' => [
            ['title' => 'کارت یک', 'sub' => 'زیریک', 'desc' => 'توضیح یک', 'chip' => 'IP66', 'flag' => '', 'price' => '', 'image' => 'assets/images/hero.jpg', 'alt' => 'آلت یک', 'url' => '/product/one/'],
            ['title' => 'کارت دو', 'sub' => 'زیردو', 'desc' => 'توضیح دو', 'chip' => 'IP67', 'flag' => 'پرفروش', 'price' => 'تماس', 'image' => 55, 'alt' => 'آلت دو', 'url' => 'https://ext.example/x/'],
        ],
    ],
]];
$GLOBALS['__es_stub_attachment_urls'][55] = 'https://uploads.example/two.jpg';
ob_start();
es_render_sections(8146, ['only' => 'categories']);
$custom = (string) ob_get_clean();
check(substr_count($custom, 'class="card-glow group relative flex h-full') === 2, 'دو کارت سفارشی');
check(substr_count($custom, 'sm:col-span-2') === 1, 'کارت اول col-span-2');
check(str_contains($custom, '>پرفروش</span>') && !str_contains($custom, '>خرید آنلاین</span>'), 'پرچم سفارشی');
check(str_contains($custom, '<span class="truncate">توضیح دو</span><span class="shrink-0 font-extrabold text-emerald-300 tabular-nums">تماس</span></div>'), 'قیمت بعد از توضیح');
check(str_contains($custom, 'src="https://uploads.example/two.jpg"'), 'تصویر attachment');
check(str_contains($custom, 'href="https://ext.example/x/"'), 'لینک مطلق');
check(substr_count($custom, 'lucide-wifi') === 1, 'چیپ ویژگی (wifi) سرجا');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
