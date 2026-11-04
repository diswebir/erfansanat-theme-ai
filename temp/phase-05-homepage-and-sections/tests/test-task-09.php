<?php
/**
 * Test 5.9 — ویترین مقالات: بازسازی بایت‌دقیق + WP_Query داینامیک + حالت خالی
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-09.php
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
echo "== Test 5.9: blog-preview ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__wp_options']['page_for_posts'] = 7426;
$GLOBALS['__es_stub_permalinks'][7426] = 'https://test.example/blog/';
$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_post_types'][8146] = 'page';

// چهار مقاله فیکسچر (بایت‌دقیق میرور)
$es_posts = [
    (object) ['ID' => 401, 'post_type' => 'post', 'post_title' => 'آشنایی جامع با انواع تونل نوری و راهنمای انتخاب بهترین تونل نوری شهری', 'post_excerpt' => 'تونل نوری یکی از جذاب‌ترین سازه‌های نورپردازی شهری است. در این راهنما انواع تونل نوری شامل کمانی، نیم‌آرک، ۹۰ درجه، مربعی و قلبی را با کاربردها و مزایای هر کدام بررسی می‌کنیم.', 'post_content' => str_repeat('واژه ', 800), 'post_date' => '2025-08-03 10:00:00'],
    (object) ['ID' => 402, 'post_type' => 'post', 'post_title' => 'المان‌های نورپردازی شهری | راهنمای جامع خرید و استانداردهای فنی', 'post_excerpt' => 'المان‌های نورپردازی شهری بخشی از هویت بصری شهر هستند. در این مقاله ویژگی‌های کلیدی المان‌های نوری، استانداردهای IP67/IP68 و انواع محصولات درخت نوری، لوستر، خورشیدنما و ریسه را بررسی می‌کنیم.', 'post_content' => str_repeat('واژه ', 800), 'post_date' => '2025-07-27 10:00:00'],
    (object) ['ID' => 403, 'post_type' => 'post', 'post_title' => 'ریسه بلوطی هفت رنگ | مشخصات فنی، قیمت و کاربردها در نورپردازی شهری', 'post_excerpt' => 'ریسه بلوطی هفت رنگ با مصرف تنها ۱ وات در ساعت برای هر شعله، طول عمر ۵۰ هزار ساعت و مقاومت IP67؛ از پرطرفدارترین محصولات نورپردازی شهری. مشخصات کامل و کاربردها را بخوانید.', 'post_content' => str_repeat('واژه ', 800), 'post_date' => '2025-07-19 10:00:00'],
    (object) ['ID' => 404, 'post_type' => 'post', 'post_title' => 'بررسی و مقایسه خورشیدنما ۳۲ و ۴۸ پره | کدام گزینه برای پروژه شما مناسب‌تر است؟', 'post_excerpt' => 'خورشیدنما یکی از ماندگارترین المان‌های نورپردازی شهری است. در این مقاله مدل‌های ۳۲ و ۴۸ پره را از نظر قطر، تراکم پیکسلی، کنترل و کاربرد با هم مقایسه می‌کنیم.', 'post_content' => str_repeat('واژه ', 800), 'post_date' => '2025-07-12 10:00:00'],
];
$GLOBALS['__es_stub_wp_query_posts'] = $es_posts;
$GLOBALS['__es_stub_wp_query_args'] = [];
$GLOBALS['__es_stub_permalinks'] += [
    401 => 'https://test.example/blog/types-of-light-tunnel.html',
    402 => 'https://test.example/blog/urban-lighting-elements.html',
    403 => 'https://test.example/blog/baloti-seven-color-string.html',
    404 => 'https://test.example/blog/sun-display-32-vs-48.html',
];
$GLOBALS['__wp_post_meta'] += [
    401 => ['_thumbnail_id' => 41, 'es_read_time' => '9'],
    402 => ['_thumbnail_id' => 42, 'es_read_time' => '11'],
    403 => ['_thumbnail_id' => 43, 'es_read_time' => '8'],
    404 => ['_thumbnail_id' => 44, 'es_read_time' => '7'],
];
$GLOBALS['__es_stub_attachment_urls'] = [
    41 => ES_URI . 'assets/images/light-tunnel.jpg',
    42 => ES_URI . 'assets/images/light-element.jpg',
    43 => ES_URI . 'assets/images/string-lights.jpg',
    44 => ES_URI . 'assets/images/sun-light.jpg',
];
$GLOBALS['__es_stub_post_term_map'] = [
    401 => ['category' => [(object) ['name' => 'مجله علمی']]],
    402 => ['category' => [(object) ['name' => 'مجله علمی']]],
    403 => ['category' => [(object) ['name' => 'مجله علمی']]],
    404 => ['category' => [(object) ['name' => 'مجله علمی']]],
];
$GLOBALS['__es_stub_post_dates'] = [
    401 => '12 مرداد 1404', 402 => '5 مرداد 1404', 403 => '28 تیر 1404', 404 => '21 تیر 1404',
];

// ═══ ۱) بازسازی بایت‌دقیق ═══
ob_start();
es_render_sections(8146, ['only' => 'blog-preview']);
$blog = (string) ob_get_clean();
$ref = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/blog-preview.html');
$expected = strtr($ref, [
    'href="blog.html"' => 'href="https://test.example/blog/"',
    'href="blog/types-of-light-tunnel.html"' => 'href="https://test.example/blog/types-of-light-tunnel.html"',
    'href="blog/urban-lighting-elements.html"' => 'href="https://test.example/blog/urban-lighting-elements.html"',
    'href="blog/baloti-seven-color-string.html"' => 'href="https://test.example/blog/baloti-seven-color-string.html"',
    'href="blog/sun-display-32-vs-48.html"' => 'href="https://test.example/blog/sun-display-32-vs-48.html"',
    'src="images/light-tunnel.jpg"' => 'src="' . ES_URI . 'assets/images/light-tunnel.jpg"',
    'src="images/light-element.jpg"' => 'src="' . ES_URI . 'assets/images/light-element.jpg"',
    'src="images/string-lights.jpg"' => 'src="' . ES_URI . 'assets/images/string-lights.jpg"',
    'src="images/sun-light.jpg"' => 'src="' . ES_URI . 'assets/images/sun-light.jpg"',
]) . '>';
check($blog === $expected, 'blog-preview: بازسازی بایت‌دقیق (۴ مقاله)');
if ($blog !== $expected) {
    $n = min(strlen($blog), strlen($expected));
    for ($i = 0; $i < $n; $i++) { if ($blog[$i] !== $expected[$i]) break; }
    echo "    diff@$i (got=" . strlen($blog) . " exp=" . strlen($expected) . "):\n    EXP: " . substr($expected, max(0, $i-70), 150) . "\n    GOT: " . substr($blog, max(0, $i-70), 150) . "\n";
}

// ═══ ۲) کوئری ثبت‌شده ═══
$qargs = $GLOBALS['__es_stub_wp_query_args'][0] ?? [];
check(($qargs['post_type'] ?? '') === 'post' && ($qargs['posts_per_page'] ?? 0) === 4 && ($qargs['ignore_sticky_posts'] ?? false) === true, 'آرگومان‌های WP_Query (post، ۴، ignore_sticky)');

// ═══ ۳) فیلدهای سفارشی: تعداد ۲ + متن دکمه ═══
$GLOBALS['__es_stub_wp_query_posts'] = array_slice($es_posts, 0, 2);
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'blog-preview', 'enabled' => 1, 'order' => 0, 'fields' => [
        'count' => 2, 'archive_text' => 'همه مقالات', 'kicker' => 'بالانویس آزمایشی',
    ],
]];
ob_start();
es_render_sections(8146, ['only' => 'blog-preview']);
$blog2 = (string) ob_get_clean();
check(substr_count($blog2, 'card-light') === 2, 'تعداد مقاله = ۲');
check(str_contains($blog2, '>همه مقالات</span>') && str_contains($blog2, '<span>بالانویس آزمایشی</span>'), 'فیلدهای سفارشی');

// ═══ ۴) چیپ بدون دسته → fallback + زمان مطالعه برآوردی ═══
$GLOBALS['__es_stub_post_term_map'][401] = [];
unset($GLOBALS['__wp_post_meta'][401]['es_read_time']);
$GLOBALS['__es_stub_post_dates'][401] = '۱ فروردین 1404';
ob_start();
es_render_sections(8146, ['only' => 'blog-preview']);
$blog3 = (string) ob_get_clean();
check(str_contains($blog3, '<span class="text-emerald-700">مجله علمی</span>'), 'چیپ fallback «مجله علمی»');
check(str_contains($blog3, '>۴ دقیقه مطالعه</span>'), 'زمان مطالعه برآوردی (۸۰۰/۲۲۵→۴)');
check(str_contains($blog3, '>۱ فروردین ۱۴۰۴</span>'), 'تاریخ فارسی‌شده با es_fa_num');

// ═══ ۵) بدون مقاله → مخفی ═══
$GLOBALS['__es_stub_wp_query_posts'] = [];
ob_start();
es_render_sections(8146, ['only' => 'blog-preview']);
check(trim((string) ob_get_clean()) === '', 'بدون مقاله → خروجی خالی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
