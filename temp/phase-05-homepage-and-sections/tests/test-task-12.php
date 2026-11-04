<?php
/**
 * Test 5.12 — تست جامع رندر صفحه اصلی: ساختار میرور + toggle + reorder + آرگومان کوئری‌ها
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-12.php
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
echo "== Test 5.12: comprehensive front-page render ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_post_types'][8146] = 'page';
$GLOBALS['__es_stub_wc_page_ids'] = ['shop' => 9];
$GLOBALS['__es_stub_permalinks'] = [9 => 'https://test.example/shop/', 7426 => 'https://test.example/blog/'];
$GLOBALS['__wp_options']['page_for_posts'] = 7426;

/** امضای شروع هر سکشن (برش از ref) — [id/aria-label منحصربه‌فرد برای تشخیص] */
$ES_SIGS = [
    'hero-home1'       => 'aria-label="بخش آغازین - طرح خانه ۱',
    'projects'         => '<section id="projects"',
    'projects-gallery' => 'aria-label="گالری نمونه‌کارها',
    'about'            => '<section id="about"',
    'about-intro'      => 'aria-label="درباره تاسیس شرکت',
    'history'          => 'data-history-scroll-track="true"',
    'categories'       => '<section id="products"',
    'catalog'          => 'aria-label="دانلود کاتالوگ جامع',
    'services'         => 'data-services-section="true"',
    'quality'          => 'مزیت‌های رقابتی کارخانه',
    'aftersale'        => 'aria-label="خدمات پس از فروش عرفان صنعت در سراسر ایران"',
    'customers'        => 'aria-label="مشتریان و سازمان‌های همکار',
    'blog-preview'     => '<section id="blog-preview"',
    'contact'          => '<section id="contact"',
    'map'              => 'aria-label="موقعیت کارخانه روی نقشه',
];

// ═══ فیکسچر داینامیک: پروژه‌ها + مقالات + ترم‌ها ═══
$es_proj1 = (object) ['ID' => 201, 'post_type' => 'projects', 'post_title' => 'پروژه نمونه یک', 'post_excerpt' => 'خلاصه پروژه یک', 'post_content' => '', 'post_date' => '2025-01-01 00:00:00'];
$es_proj2 = (object) ['ID' => 202, 'post_type' => 'projects', 'post_title' => 'پروژه نمونه دو', 'post_excerpt' => 'خلاصه پروژه دو', 'post_content' => '', 'post_date' => '2025-01-02 00:00:00'];
$GLOBALS['__es_stub_posts'] = [$es_proj1, $es_proj2];
$GLOBALS['__es_stub_permalinks'] = ($GLOBALS['__es_stub_permalinks'] ?? []) + [201 => 'https://test.example/projects/one/', 202 => 'https://test.example/projects/two/'];
$GLOBALS['__wp_post_meta'] = ($GLOBALS['__wp_post_meta'] ?? []) + [
    201 => ['finish-pro-time' => '۱۵ بهمن ۱۴۰۳', 'es_project_year' => '۱۴۰۳', 'number2-viewc' => '100', 'es_rating' => '4.5', '_thumbnail_id' => 31],
    202 => ['finish-pro-time' => '۲ اسفند ۱۴۰۳', 'es_project_year' => '۱۴۰۳', 'number2-viewc' => '200', 'es_rating' => '4.6', '_thumbnail_id' => 32],
];
$GLOBALS['__es_stub_attachment_urls'] = ($GLOBALS['__es_stub_attachment_urls'] ?? []) + [31 => ES_URI . 'assets/images/light-sphere.jpg', 32 => ES_URI . 'assets/images/light-tree.jpg'];
$GLOBALS['__es_stub_post_term_map'] = ($GLOBALS['__es_stub_post_term_map'] ?? []) + [
    201 => ['project_type' => [(object) ['name' => 'المان‌های نورانی']], 'city' => [(object) ['name' => 'اصفهان']]],
    202 => ['project_type' => [(object) ['name' => 'نورپردازی میدان']], 'city' => [(object) ['name' => 'شیراز']]],
];
$es_post1 = (object) ['ID' => 401, 'post_type' => 'post', 'post_title' => 'مقاله نمونه', 'post_excerpt' => 'خلاصه مقاله نمونه', 'post_content' => str_repeat('واژه ', 300), 'post_date' => '2025-08-03 10:00:00'];
$GLOBALS['__es_stub_wp_query_posts'] = [$es_post1];
$GLOBALS['__es_stub_wp_query_args'] = [];
$GLOBALS['__wp_post_meta'] = ($GLOBALS['__wp_post_meta'] ?? []) + [401 => ['_thumbnail_id' => 41, 'es_read_time' => '9']];
$GLOBALS['__es_stub_attachment_urls'] += [41 => ES_URI . 'assets/images/light-tunnel.jpg'];
$GLOBALS['__es_stub_post_term_map'] = ($GLOBALS['__es_stub_post_term_map'] ?? []) + [401 => ['category' => [(object) ['name' => 'مجله علمی']]]];
$GLOBALS['__es_stub_post_dates'] = [401 => '۱۲ مرداد ۱۴۰۴'];

// ═══ ۱) رندر پیش‌فرض: همه ۱۵ سکشن به ترتیب Registry ═══
ob_start();
es_render_sections(8146);
$all = (string) ob_get_clean();
$pos = [];
$ok_all = true;
foreach ($ES_SIGS as $id => $sig) {
    $p = mb_strpos($all, $sig);
    if ($p === false) {
        $ok_all = false;
        echo "    missing section: $id\n";
    } else {
        $pos[$id] = $p;
    }
}
check($ok_all, 'رندر پیش‌فرض: هر ۱۵ سکشن حاضرند');

// ترتیب مطابق HomeSections::ORDER
$ORDER = ['hero-home1', 'projects', 'projects-gallery', 'about', 'about-intro', 'history', 'categories', 'catalog', 'services', 'quality', 'aftersale', 'customers', 'blog-preview', 'contact', 'map'];
$seq_ok = true;
for ($i = 1; $i < count($ORDER); $i++) {
    if (!isset($pos[$ORDER[$i - 1]], $pos[$ORDER[$i]]) || $pos[$ORDER[$i - 1]] >= $pos[$ORDER[$i]]) {
        $seq_ok = false;
        echo "    order broken at: {$ORDER[$i-1]} → {$ORDER[$i]}\n";
    }
}
check($seq_ok, 'ترتیب سکشن‌ها = HomeSections::ORDER');

// data-hookهای کلیدی هر سکشن در خروجی
$es_hooks = ['data-history-total' => '6', 'data-services-section' => 'true', 'data-product-item' => null, 'data-blog-item' => null];
check(str_contains($all, 'data-history-total="6"') && str_contains($all, 'data-services-section="true"'), 'data-hookهای history/services');
check(str_contains($all, 'lucide-download') && str_contains($all, 'کاتالوگ جامع محصولات'), 'کاتالوگ حاضر');

// ═══ ۲) toggle: خاموش‌کردن سکشن‌ها ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [
    ['id' => 'history', 'enabled' => 0, 'order' => 0, 'fields' => []],
    ['id' => 'catalog', 'enabled' => 0, 'order' => 1, 'fields' => []],
    ['id' => 'hero-home1', 'enabled' => 1, 'order' => 2, 'fields' => []],
    ['id' => 'projects', 'enabled' => 1, 'order' => 3, 'fields' => []],
    ['id' => 'projects-gallery', 'enabled' => 0, 'order' => 4, 'fields' => []],
    ['id' => 'about', 'enabled' => 0, 'order' => 5, 'fields' => []],
    ['id' => 'about-intro', 'enabled' => 0, 'order' => 6, 'fields' => []],
    ['id' => 'categories', 'enabled' => 0, 'order' => 7, 'fields' => []],
    ['id' => 'services', 'enabled' => 0, 'order' => 8, 'fields' => []],
    ['id' => 'quality', 'enabled' => 0, 'order' => 9, 'fields' => []],
    ['id' => 'aftersale', 'enabled' => 0, 'order' => 10, 'fields' => []],
    ['id' => 'customers', 'enabled' => 0, 'order' => 11, 'fields' => []],
    ['id' => 'blog-preview', 'enabled' => 0, 'order' => 12, 'fields' => []],
    ['id' => 'contact', 'enabled' => 0, 'order' => 13, 'fields' => []],
    ['id' => 'map', 'enabled' => 0, 'order' => 14, 'fields' => []],
];
ob_start();
es_render_sections(8146);
$two = (string) ob_get_clean();
check(str_contains($two, $ES_SIGS['hero-home1']) && str_contains($two, $ES_SIGS['projects']), 'فقط سکشن‌های فعال رندر شدند');
foreach (['history', 'catalog', 'projects-gallery', 'services', 'customers'] as $id) {
    if (str_contains($two, $ES_SIGS[$id])) {
        check(false, "سکشن خاموش $id نباید رندر شود");
    }
}
check(true, 'سکشن‌های خاموش غایبند');

// ═══ ۳) reorder: جابجایی ترتیب ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [
    ['id' => 'map', 'enabled' => 1, 'order' => 0, 'fields' => []],
    ['id' => 'contact', 'enabled' => 1, 'order' => 1, 'fields' => []],
    ['id' => 'hero-home1', 'enabled' => 1, 'order' => 2, 'fields' => []],
];
ob_start();
es_render_sections(8146);
$rev = (string) ob_get_clean();
$pm = mb_strpos($rev, $ES_SIGS['map']);
$pc = mb_strpos($rev, $ES_SIGS['contact']);
$ph = mb_strpos($rev, $ES_SIGS['hero-home1']);
check($pm !== false && $pc !== false && $ph !== false && $pm < $pc && $pc < $ph, 'ترتیب سفارشی رعایت شد (map → contact → hero)');

// ═══ ۴) آرگومان کوئری‌ها در رندر کامل ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [];
$GLOBALS['__es_stub_get_posts_args'] = [];
$GLOBALS['__es_stub_wp_query_args'] = [];
ob_start();
es_render_sections(8146);
ob_end_clean();
$qargs = $GLOBALS['__es_stub_wp_query_args'][0] ?? [];
check(($qargs['post_type'] ?? '') === 'post' && ($qargs['posts_per_page'] ?? 0) === 4, 'آرگومان WP_Query مقالات در رندر کامل');
$gargs = $GLOBALS['__es_stub_get_posts_args'][0] ?? [];
$gargs2 = $GLOBALS['__es_stub_get_posts_args'][1] ?? [];
check(
    ($gargs['post_type'] ?? '') === 'projects' && ($gargs['posts_per_page'] ?? 0) === 3 && ($gargs['post_status'] ?? '') === 'publish'
    && ($gargs2['post_type'] ?? '') === 'projects' && ($gargs2['numberposts'] ?? 0) === 30,
    'آرگومان get_posts پروژه‌ها در رندر کامل'
);

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
