<?php
/**
 * Test 5.3 — سکشن پروژه‌های شاخص: کوئری + کارت استاندارد + بازسازی بایت‌دقیق
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-03.php
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
echo "== Test 5.3: projects ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

// ═══ فیکسچر: ۳ پروژه ═══
$GLOBALS['__es_stub_permalinks'] = [
    201 => 'https://test.example/projects/half-arch-tunnel-alborz/',
    202 => 'https://test.example/projects/esfahan-shohada-square/',
    203 => 'https://test.example/projects/artam-tree-midan/',
];
$GLOBALS['__es_stub_posts'] = [
    (object) ['ID' => 201, 'post_type' => 'projects', 'post_title' => 'تونل نوری شهری', 'post_excerpt' => 'مسیرهای عبوری با جلوه‌ای چشم‌نواز؛ از ورودی بوستان‌ها تا جشنواره‌های شهری با سازه‌های فولادی و پلی‌کربنات ضدضربه'],
    (object) ['ID' => 202, 'post_type' => 'projects', 'post_title' => 'گوی و المان حجمی نورانی', 'post_excerpt' => 'کره‌ها و حجم‌های نوری درخشان در نقطه کانونی میادین و فضاهای باز شهری'],
    (object) ['ID' => 203, 'post_type' => 'projects', 'post_title' => 'درخت و المان نوری هوشمند', 'post_excerpt' => 'طراحی و ساخت المان‌های نوری منحصربه‌فرد با هویت بصری ماندگار برای شهرداری‌ها'],
];
$GLOBALS['__wp_post_meta'] = [
    201 => ['_thumbnail_id' => '31'],
    202 => ['_thumbnail_id' => '32'],
    203 => ['_thumbnail_id' => '33'],
];
$GLOBALS['__es_stub_attachment_urls'] = [
    31 => ES_URI . 'assets/images/light-tunnel.jpg',
    32 => ES_URI . 'assets/images/light-sphere.jpg',
    33 => ES_URI . 'assets/images/light-element.jpg',
];
$GLOBALS['__es_stub_post_term_map'] = [
    201 => ['project_type' => [(object) ['name' => 'کمانی'], (object) ['name' => 'نیم‌آرک'], (object) ['name' => '۹۰ درجه'], (object) ['name' => 'مربعی']]],
    202 => ['project_type' => [(object) ['name' => 'المان حجمی سه‌بعدی'], (object) ['name' => 'فول‌کالر']]],
    203 => ['project_type' => [(object) ['name' => 'سازه‌های نوری اختصاصی'], (object) ['name' => 'استاندارد IP68']]],
];

// ═══ ۱) کوئری args ═══
$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__es_stub_post_types'][8146] = 'page';
$GLOBALS['__wp_post_meta'][8146] = [
    '_es_sections' => [['id' => 'projects', 'enabled' => 1, 'order' => 0, 'fields' => ['count' => 3, 'handpicked' => '202, 201']]],
];
$GLOBALS['__es_stub_get_posts_args'] = [];
ob_start();
es_render_sections(8146);
$all = (string) ob_get_clean();
$args_rec = $GLOBALS['__es_stub_get_posts_args'][0] ?? [];
check(($args_rec['post_type'] ?? '') === 'projects', 'کوئری: post_type = projects');
check((int) ($args_rec['posts_per_page'] ?? 0) === 3, 'کوئری: posts_per_page از فیلد');
check(($args_rec['no_found_rows'] ?? false) === true, 'کوئری: no_found_rows سبک');
check(($args_rec['post__in'] ?? []) === [202, 201], 'کوئری: handpicked → post__in');
check(($args_rec['orderby'] ?? '') === 'post__in', 'کوئری: orderby = post__in');

// ═══ ۲) بازسازی بایت‌دقیق (بدون handpicked) ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [];
$GLOBALS['__es_stub_get_posts_args'] = [];
ob_start();
es_render_sections(8146, ['only' => 'projects']);
// جدا کردن خروجی سکشن projects از بقیه
$out = (string) ob_get_clean();
$start = strpos($out, '<section id="projects"');
$end   = strpos($out, '<section id="projects-gallery"');
$sec   = $end !== false ? substr($out, $start, $end - $start) : substr($out, $start);

$ref = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/projects.html');
$expected = strtr($ref, [
    'href="projects/half-arch-tunnel-alborz.html"' => 'href="https://test.example/projects/half-arch-tunnel-alborz/"',
    'href="projects/esfahan-shohada-square.html"' => 'href="https://test.example/projects/esfahan-shohada-square/"',
    'href="projects/artam-tree-midan.html"' => 'href="https://test.example/projects/artam-tree-midan/"',
    'src="images/light-tunnel.jpg"' => 'src="' . ES_URI . 'assets/images/light-tunnel.jpg"',
    'src="images/light-sphere.jpg"' => 'src="' . ES_URI . 'assets/images/light-sphere.jpg"',
    'src="images/light-element.jpg"' => 'src="' . ES_URI . 'assets/images/light-element.jpg"',
]) . '>';

check($sec === $expected, 'بازسازی بایت‌دقیق سکشن projects');
if ($sec !== $expected) {
    $n = min(strlen($sec), strlen($expected));
    for ($i = 0; $i < $n; $i++) {
        if ($sec[$i] !== $expected[$i]) break;
    }
    echo "    اولین اختلاف در بایت $i:\n    EXP: " . substr($expected, max(0, $i - 50), 110) . "\n    GOT: " . substr($sec, max(0, $i - 50), 110) . "\n";
}

// ═══ ۳) سکشن خالی → رندر نمی‌شود ═══
$GLOBALS['__es_stub_posts'] = [];
ob_start();
es_render_sections(8146, ['only' => 'projects']);
check(trim((string) ob_get_clean()) === '', 'سکشن خالی → رندر نشد');

// ═══ ۴) ۴ پروژه → کارت چهارم در پشته با شماره ۰۴ ═══
$GLOBALS['__wp_post_meta'][8146] = [
    '_es_sections' => [['id' => 'projects', 'enabled' => 1, 'order' => 0, 'fields' => ['count' => 4, 'handpicked' => '']]],
];
$GLOBALS['__es_stub_posts'] = [
    (object) ['ID' => 201, 'post_type' => 'projects', 'post_title' => 'تونل نوری شهری', 'post_excerpt' => 'مسیرهای عبوری با جلوه‌ای چشم‌نواز؛ از ورودی بوستان‌ها تا جشنواره‌های شهری با سازه‌های فولادی و پلی‌کربنات ضدضربه'],
    (object) ['ID' => 202, 'post_type' => 'projects', 'post_title' => 'گوی و المان حجمی نورانی', 'post_excerpt' => 'کره‌ها و حجم‌های نوری درخشان در نقطه کانونی میادین و فضاهای باز شهری'],
    (object) ['ID' => 203, 'post_type' => 'projects', 'post_title' => 'درخت و المان نوری هوشمند', 'post_excerpt' => 'طراحی و ساخت المان‌های نوری منحصربه‌فرد با هویت بصری ماندگار برای شهرداری‌ها'],
    (object) ['ID' => 204, 'post_type' => 'projects', 'post_title' => 'پروژه چهارم', 'post_excerpt' => 'توضیح چهارم'],
];
$GLOBALS['__es_stub_permalinks'][204] = 'https://test.example/projects/fourth/';
$GLOBALS['__wp_post_meta'][204] = ['_thumbnail_id' => '34'];
$GLOBALS['__es_stub_attachment_urls'][34] = ES_URI . 'assets/images/factory.jpg';
$GLOBALS['__es_stub_post_term_map'][204] = ['project_type' => [(object) ['name' => 'نمونه']]];
ob_start();
es_render_sections(8146, ['only' => 'projects']);
$out4 = (string) ob_get_clean();
check(str_contains($out4, '>۰۴</span>'), 'پروژه چهارم → شماره ۰۴');
check(substr_count($out4, 'flex flex-1 flex-col" custom="0.2"') === 1, 'کارت چهارم: custom=0.2');
check(str_contains($out4, 'href="https://test.example/projects/fourth/"'), 'کارت چهارم از card-project با permalink');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
