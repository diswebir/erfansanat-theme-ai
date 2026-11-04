<?php
/**
 * Test 5.2 — سکشن هیرو طرح ۱: بازسازی بایت‌دقیق + رپیتر صحنه‌ها + ویدیو داینامیک
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-02.php
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
echo "== Test 5.2: hero-home1 ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Frontend\Icons;

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

// ═══ فیکسچر ترم‌ها: ۷ دسته میرور ═══
$GLOBALS['__es_stub_terms']['product_cat'] = [
    (object) ['term_id' => 11, 'slug' => 'point-light', 'name' => 'پوینت لایت پیکسل', 'description' => '۴ و ۶ سانتی پلی‌کربنات', 'count' => 12, 'parent' => 0, 'taxonomy' => 'product_cat'],
    (object) ['term_id' => 12, 'slug' => 'needle-thread', 'name' => 'ریسه سوزنی IP68', 'description' => 'تزریق مستقیم ضدآب', 'count' => 8, 'parent' => 0, 'taxonomy' => 'product_cat'],
    (object) ['term_id' => 13, 'slug' => 'light-tree', 'name' => 'درخت نوری هوشمند', 'description' => 'مدل‌های دلسا و آرتام', 'count' => 10, 'parent' => 0, 'taxonomy' => 'product_cat'],
    (object) ['term_id' => 14, 'slug' => 'acorn-thread', 'name' => 'ریسه بلوطی و فندقی', 'description' => 'هفت‌رنگ و فول‌کالر', 'count' => 14, 'parent' => 0, 'taxonomy' => 'product_cat'],
    (object) ['term_id' => 15, 'slug' => 'wall-washer', 'name' => 'والواشر و لینیرلایت', 'description' => 'نورپردازی نمای ساختمان و پل', 'count' => 18, 'parent' => 0, 'taxonomy' => 'product_cat'],
    (object) ['term_id' => 16, 'slug' => 'sun-light-elements', 'name' => 'المان خورشیدنما', 'description' => '۳۲ و ۴۸ پره فول‌کالر', 'count' => 6, 'parent' => 0, 'taxonomy' => 'product_cat'],
    (object) ['term_id' => 17, 'slug' => 'light-chandelier', 'name' => 'لوستر و المان شهری', 'description' => 'آذین‌بندی بلوار و میادین', 'count' => 15, 'parent' => 0, 'taxonomy' => 'product_cat'],
];

// ═══ ۱) آیکون‌ها ═══
$cpu = Icons::svg('cpu', 'lucide lucide-cpu h-6 w-6 stroke-[1.75]');
check(str_contains($cpu, 'lucide lucide-cpu h-6 w-6 stroke-[1.75]') && str_contains($cpu, '<rect x="4" y="4" width="16" height="16" rx="2">'), 'آیکون cpu ساخته شد');
check(Icons::svg('flame', 'x') !== Icons::svg('sun', 'x'), 'آیکون‌های متفاوت inner متفاوت');

// ═══ ۲) رندر پیش‌فرض (میرور) ═══
$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_post_types'][8146] = 'page';
ob_start();
es_render_sections(8146, ['only' => 'hero-home1']);
// فقط خروجی سکشن هیرو را جدا می‌کنیم: همه سکشن‌ها رندر می‌شوند؛ هیرو اولین بخش بین MARK نیست چون قالب واقعی جایگزین شده
$all = (string) ob_get_clean();
// سکشن هیرو از ابتدای خروجی تا شروع سکشن projects
$hero_html = strstr($all, '<section id="projects"', true) ?: $all;

$ref = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/hero-home1.html');

// نرمال‌سازی ref به فضای URL استاب
$URI = ES_URI;
$map = [
    'images/hero-video.mp4' => $URI . 'assets/media/hero-video.mp4',
    'images/hero.jpg' => $URI . 'assets/images/hero.jpg',
    'images/light-tunnel.jpg' => $URI . 'assets/images/light-tunnel.jpg',
    'images/sun-light.jpg' => $URI . 'assets/images/sun-light.jpg',
    'images/light-tree.jpg' => $URI . 'assets/images/light-tree.jpg',
    'images/light-sphere.jpg' => $URI . 'assets/images/light-sphere.jpg',
    'href="about.html"' => 'href="https://test.example/about/"',
    'projects/half-arch-tunnel-alborz.html' => 'https://test.example/projects/half-arch-tunnel-alborz/',
    'projects/esfahan-shohada-square.html' => 'https://test.example/projects/esfahan-shohada-square/',
    'projects/artam-tree-midan.html' => 'https://test.example/projects/artam-tree-midan/',
    'projects/ahvaz-semi-pyramid-light.html' => 'https://test.example/projects/ahvaz-semi-pyramid-light/',
    'href="projects.html"' => 'href="https://test.example/projects/"',
    'project_type/light-tunnel.html' => 'https://test.example/project_type/light-tunnel/',
];
foreach (['point-light', 'needle-thread', 'light-tree', 'acorn-thread', 'wall-washer', 'urban-lighting-elements', 'sun-light-elements', 'hazelnut-thread', 'light-chandelier'] as $slug) {
    $map["product_cat/{$slug}.html"] = "https://test.example/product_cat/{$slug}/";
}
$expected = strtr($ref, $map) . '>';

check($hero_html === $expected, 'بازسازی بایت‌دقیق هیرو (حالت پیش‌فرض میرور)');
if ($hero_html !== $expected) {
    // اولین اختلاف برای دیباگ
    $n = min(strlen($hero_html), strlen($expected));
    for ($i = 0; $i < $n; $i++) {
        if ($hero_html[$i] !== $expected[$i]) {
            break;
        }
    }
    echo "    اولین اختلاف در بایت $i:\n    EXP: " . substr($expected, max(0, $i - 60), 120) . "\n    GOT: " . substr($hero_html, max(0, $i - 60), 120) . "\n";
    file_put_contents($root . '/temp/phase-05-homepage-and-sections/tests/debug-got.html', $hero_html);
    file_put_contents($root . '/temp/phase-05-homepage-and-sections/tests/debug-exp.html', $expected);
}

// ═══ ۳) رپیتر صحنه‌ها: ۳ صحنه دلخواه ═══
$GLOBALS['__es_stub_attachment_urls'][55] = 'https://uploads.example/scene.jpg';
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'hero-home1', 'enabled' => 1, 'order' => 0, 'fields' => [
        'kicker' => 'کیکر آزمایشی', 'title' => 'عنوان آزمایشی', 'brand_line' => 'برند آزمایشی',
        'video' => 'assets/media/custom-video.mp4', 'video_scene' => 1,
        'scenes' => [
            ['name' => 'صحنه الف', 'image' => 'assets/images/hero.jpg', 'title' => 'تیزر آلفا', 'cta_text' => 'بیشتر', 'cta_url' => '/about/'],
            ['name' => 'صحنه ب', 'image' => 55, 'title' => 'تونل بتا', 'cta_text' => 'پروژه', 'cta_url' => '/projects/x/'],
            ['name' => 'صحنه ج', 'image' => 'assets/images/sun-light.jpg', 'title' => 'آذین گاما', 'cta_text' => 'دیدن', 'cta_url' => 'https://ext.example/p/'],
        ],
        'quick_links' => [['label' => 'لینک ۱', 'url' => '/projects/']],
    ],
]];
ob_start();
es_render_sections(8146);
$custom = (string) ob_get_clean();
check(substr_count($custom, 'data-home1-scene-img=') === 3, 'رپیتر: ۳ صحنه → ۳ تصویر');
check(substr_count($custom, 'data-home1-scene-btn=') === 3, 'رپیتر: ۳ دکمه صحنه');
check(str_contains($custom, 'data-home1-scene-btn="1" data-scene-title="تونل بتا" data-scene-href="https://test.example/projects/x/" data-scene-cta="پروژه" data-scene-is-video="true"'), 'video_scene=1 → صحنه دوم ویدیویی + active');
check(str_contains($custom, 'src="' . $URI . 'assets/media/custom-video.mp4"'), 'ویدیو داینامیک از فیلد');
check(str_contains($custom, 'poster="https://uploads.example/scene.jpg"'), 'poster ویدیو = تصویر صحنه فعال');
check(str_contains($custom, 'src="https://uploads.example/scene.jpg"'), 'آیدی attachment در es_media_url → URL پیوست');
check(str_contains($custom, 'href="https://ext.example/p/"'), 'لینک مطلق عیناً');
check(str_contains($custom, '>کیکر آزمایشی<') && str_contains($custom, '>عنوان آزمایشی<') && str_contains($custom, '>برند آزمایشی<'), 'نوتچ از فیلدها');
check(substr_count($custom, '<a href="https://test.example/projects/" class="rounded-xl border border-white/30') === 1, 'چیپ‌ها: ۱ لینک سفارشی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
