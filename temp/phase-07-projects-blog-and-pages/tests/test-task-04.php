<?php
/**
 * Test 7.4 — home.php / archive.php / category.php — آرشیو نوشته‌ها (وبلاگ) بایت‌دقیق مقابل میرور
 * اجرا: /home/user/tools/php -f temp/phase-07-projects-blog-and-pages/tests/test-task-04.php
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
echo "== Test 7.4: home / archive / category (وبلاگ) ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';
$U = 'https://test.example/wp-content/uploads/';

/* ---------- استخراج فیکسچر از میرورها ---------- */
$MIR_B = (string) file_get_contents($root . '/uploads/static-mirror/blog.html');
$MIR_C = (string) file_get_contents($root . '/uploads/static-mirror/category/magazine.html');

$fa2en = fn (string $v): string => strtr($v, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9']);

function blog_main_inner(string $html): string {
    $m0 = strpos($html, '<main class="min-h-screen bg-[#f8faf9]">') + strlen('<main class="min-h-screen bg-[#f8faf9]">');
    return substr($html, $m0, (int) strpos($html, '</main>', $m0) - $m0);
}
$inner_b = blog_main_inner($MIR_B);
$inner_c = blog_main_inner($MIR_C);
check(strlen($inner_b) > 30000 && strlen($inner_c) > 30000, 'برش main اینر میرورها');

preg_match_all('/<div data-blog-item="true".*?(?=<div data-blog-item="true"|<\/div><nav aria-label)/s', $inner_b, $im);
$items = $im[0];
check(count($items) === 7, 'میرور بلاگ: ۷ کارت');

$INNER = blog_main_inner($MIR_B);

$img_ids = [];
$next_aid = 80;
$posts = [];
$titles = [];
$permalinks = [];
$meta = [];
$tmap = [];
foreach ($items as $k => $item) {
    $pid = 701 + $k;
    preg_match('/data-blog-category="([^"]*)"/', $item, $chip);
    preg_match('/data-blog-search-text="([^"]*)"/', $item, $search);
    preg_match('/href="(?:\.\.\/)?blog\/([^"]+)\.html"/', $item, $href);
    preg_match('/<img src="(?:\.\.\/)?(images\/[^"]+)"/', $item, $img);
    preg_match('/<h3 class="text-\[16\.5px\] font-extrabold leading-8 text-slate-900[^"]*">([^<]+)<\/h3>/', $item, $title);
    preg_match('/lucide-calendar-days h-3\.5 w-3\.5 text-emerald-600" aria-hidden="true">(?:<path d="[^"]*"><\/path>)+<rect[^>]*\/><path d="M3 10h18"><\/path>(?:<path d="M\d+ 14h\.01"><\/path>)+<\/svg>([^<]+)<\/span>/', $item, $datetx);
    preg_match('/<\/svg>([۰-۹]+) دقیقه مطالعه<\/span><\/div><h3/', $item, $mintx);
    preg_match('/<p class="line-clamp-3 text-\[13px\] leading-7 text-slate-600">([^<]+)<\/p>/', $item, $exc);
    $title = (string) $title[1];
    $excerpt = (string) $exc[1];
    $chipx = (string) $chip[1];
    $prefix = $title . ' ' . $excerpt . ' ';
    $kw = str_starts_with((string) $search[1], $prefix) ? substr((string) $search[1], strlen($prefix)) : '';
    $titles[$pid] = $title;
    $permalinks[$pid] = 'https://test.example/blog/' . $href[1] . '/';
    $ipath = (string) $img[1];
    if (!isset($img_ids[$ipath])) {
        $img_ids[$ipath] = (string) $next_aid++;
    }
    $meta[$pid] = [
        '_thumbnail_id' => $img_ids[$ipath],
        'es_read_time'  => (string) $fa2en((string) $mintx[1]),
        'es_keywords'   => $kw,
    ];
    $tmap[$pid] = ['category' => [(object) ['term_id' => 0, 'taxonomy' => 'category', 'slug' => $chipx === 'مجله علمی' ? 'magazine' : 'news-and-events', 'name' => $chipx, 'count' => 0]]];
    $posts[$pid] = (object) ['ID' => $pid, 'post_type' => 'post', 'post_title' => $title, 'post_excerpt' => $excerpt, 'post_content' => ''];
}
check(count($posts) === 7, 'فیکسچر: ۷ نوشته ساخته شد');
check(isset($titles[701]) && str_contains((string) $titles[701], 'تونل نوری'), 'نوشته ۷۰۱ = تونل نوری');

$GLOBALS['__es_stub_terms'] = ['category' => [
    (object) ['term_id' => 51, 'taxonomy' => 'category', 'slug' => 'magazine', 'name' => 'مجله علمی', 'count' => 5],
    (object) ['term_id' => 52, 'taxonomy' => 'category', 'slug' => 'news-and-events', 'name' => 'اخبار و رویداد ها', 'count' => 2],
]];
$GLOBALS['__es_stub_permalinks'] = $permalinks;
$GLOBALS['__es_stub_attachment_urls'] = [];
foreach ($img_ids as $path => $aid) {
    $GLOBALS['__es_stub_attachment_urls'][(int) $aid] = $U . substr((string) $path, strlen('images/'));
}
$GLOBALS['__es_stub_post_counts'] = ['post' => 7];
$GLOBALS['__wp_options']['page_for_posts'] = 7426;
$GLOBALS['__wp_options']['sticky_posts'] = [701, 702, 703];
// تنظیم ذخیره‌شدهٔ سایت: ۴ مقاله در صفحه (مطابق data-items-per-page="4" میرور؛ پیش‌فرض ادمین ۹ است)
$GLOBALS['__wp_options']['erfansanat_theme_options'] = ['archives' => ['posts_per_page' => '4']];
$GLOBALS['__wp_post_meta'] = $meta;
$GLOBALS['__es_stub_post_term_map'] = $tmap;
$GLOBALS['__es_stub_post_dates'] = [];
foreach ($items as $k => $item) {
    preg_match('/<\/svg>([^<]+)<\/span><span class="flex items-center gap-1\.5"><svg xmlns="http:\/\/www\.w3\.org\/2000\/svg"[^>]*class="lucide lucide-clock3/', (string) $item, $dtx);
    $GLOBALS['__es_stub_post_dates'][701 + $k] = (string) $dtx[1];
}
es_option_reset_cache();

$all_posts = array_values($posts);
$HOME = 'https://test.example/';
$BLOG_URL = 'https://test.example/blog/';
$MAG_URL = 'https://test.example/category/magazine/';
$NEWS_URL = 'https://test.example/category/news-and-events/';

/** لینک‌سازی محیطی مرجع بلاگ (href/src میرور → URLهای تست) */
$env_blog = static function (string $ref) use ($permalinks, $img_ids, $U, $HOME, $BLOG_URL, $MAG_URL, $NEWS_URL): string {
    $ref = str_replace('href="index.html"', 'href="' . $HOME . '"', $ref);
    $ref = str_replace('href="blog.html"', 'href="' . $BLOG_URL . '"', $ref);
    $ref = str_replace('href="category/magazine.html"', 'href="' . $MAG_URL . '"', $ref);
    $ref = str_replace('href="category/news-and-events.html"', 'href="' . $NEWS_URL . '"', $ref);
    foreach ($permalinks as $pid => $pl) {
        $slug = str_replace(['https://test.example/blog/', '/'], '', $pl);
        $ref = str_replace('href="blog/' . $slug . '.html"', 'href="' . $pl . '"', $ref);
    }
    foreach ($img_ids as $path => $aid) {
        $ref = str_replace('src="' . $path . '"', 'src="' . $U . substr((string) $path, strlen('images/')) . '"', $ref);
    }
    return $ref;
};
/** لینک‌سازی محیطی مرجع کت (../) */
$env_cat = static function (string $ref) use ($permalinks, $img_ids, $U, $HOME, $BLOG_URL, $MAG_URL, $NEWS_URL): string {
    $ref = str_replace('href="../index.html"', 'href="' . $HOME . '"', $ref);
    $ref = str_replace('href="../blog.html"', 'href="' . $BLOG_URL . '"', $ref);
    $ref = str_replace('href="../category/magazine.html"', 'href="' . $MAG_URL . '"', $ref);
    $ref = str_replace('href="../category/news-and-events.html"', 'href="' . $NEWS_URL . '"', $ref);
    foreach ($permalinks as $pid => $pl) {
        $slug = str_replace(['https://test.example/blog/', '/'], '', $pl);
        $ref = str_replace('href="../blog/' . $slug . '.html"', 'href="' . $pl . '"', $ref);
    }
    foreach ($img_ids as $path => $aid) {
        $ref = str_replace('src="../' . $path . '"', 'src="' . $U . substr((string) $path, strlen('images/')) . '"', $ref);
    }
    return $ref;
};

/* ---------- ۱) رندر home.php — بایت‌دقیق مقابل blog.html ---------- */
$GLOBALS['wp_query'] = (object) ['posts' => $all_posts, 'found_posts' => 7];
$GLOBALS['__es_stub_query_vars'] = ['paged' => 1];
$GLOBALS['__es_stub_is_home'] = true;
$_GET = [];

ob_start();
require $root . '/home.php';
$html_h = (string) ob_get_clean();
$h0 = strpos($html_h, '<main class="min-h-screen bg-[#f8faf9]">');
$h1 = strpos($html_h, '</main>', $h0);
$inner_h = substr($html_h, $h0 + strlen('<main class="min-h-screen bg-[#f8faf9]">'), $h1 - $h0 - strlen('<main class="min-h-screen bg-[#f8faf9]">'));
$ref_h = $env_blog($inner_b);
check($inner_h === $ref_h, 'بازسازی بایت‌دقیق home.php مقابل blog.html (' . strlen($inner_h) . ' کاراکتر)');

/* ---------- ۲) رندر category.php (مجله علمی) — بایت‌دقیق مقابل magazine.html ---------- */
$GLOBALS['__es_stub_queried_object'] = (object) ['term_id' => 51, 'name' => 'مجله علمی', 'slug' => 'magazine', 'taxonomy' => 'category', 'description' => 'مقالات تخصصی، راهنماهای خرید، بررسی استانداردهای IP67/IP68 و دانش فنی تجهیزات نورپردازی شهری و صنعتی.', 'count' => 7];
$GLOBALS['wp_query'] = (object) ['posts' => array_slice($all_posts, 0, 5), 'found_posts' => 5];

ob_start();
require $root . '/category.php';
$html_c = (string) ob_get_clean();
$c0 = strpos($html_c, '<main class="min-h-screen bg-[#f8faf9]">');
$c1 = strpos($html_c, '</main>', $c0);
$inner_c2 = substr($html_c, $c0 + strlen('<main class="min-h-screen bg-[#f8faf9]">'), $c1 - $c0 - strlen('<main class="min-h-screen bg-[#f8faf9]">'));
$ref_c = $env_cat($inner_c);
check($inner_c2 === $ref_c, 'بازسازی بایت‌دقیق category.php مقابل magazine.html (' . strlen($inner_c2) . ' کاراکتر)');

/* ---------- ۳) خروجی‌های ساختاری ---------- */
check(str_contains($inner_h, '<span data-blog-count="true">۷</span> مقاله'), 'شمارنده بلاگ = ۷');
check(str_contains($inner_h, 'مجله فنی و آموزشی عرفان صنعت · ۷ مقاله'), 'بج هیرو بلاگ');
check(substr_count($inner_h, '★ ویژه') === 3, 'سه نشان ویژه (sticky سه‌گانه) در گرید');
check(substr_count($inner_h, '★ منتخب سردبیر') === 1, 'کارت منتخب سردبیر در هیرو');
check(str_contains($inner_h, 'data-blog-empty="true" class="hidden'), 'حالت خالی مخفی در بلاگ');
check(str_contains($inner_h, 'data-pagination-root="blog"'), 'ناو بلاگ');
check(substr_count($inner_h, 'data-blog-item="true"') === 7, 'هر ۷ آیتم در DOM');
check(substr_count($inner_h, 'data-blog-item="true" data-blog-category="اخبار و رویداد ها"') === 2, 'دو کارت اخبار');

/* ---------- ۴) sticky: کارت هیرو = نخستین sticky ---------- */
$GLOBALS['__wp_options']['sticky_posts'] = [705];
es_option_reset_cache();
ob_start();
require $root . '/home.php';
$h5 = (string) ob_get_clean();
check(str_contains($h5, '<h3 class="line-clamp-2 text-[19px] font-black leading-8 text-white [text-shadow:0_2px_14px_rgba(0,0,0,0.6)]">' . $titles[705] . '</h3>'), 'کارت هیرو = sticky جدید (۷۰۵)');
$GLOBALS['__wp_options']['sticky_posts'] = [701, 702, 703];
es_option_reset_cache();

/* ---------- ۵) حالت خالی ---------- */
$GLOBALS['wp_query'] = (object) ['posts' => [], 'found_posts' => 0];
ob_start();
require $root . '/home.php';
$he = (string) ob_get_clean();
check(str_contains($he, 'data-blog-empty="true" class="block rounded-2xl border-dashed') || str_contains($he, '<div data-blog-empty="true" class="block'), 'حالت خالی نمایان');
check(str_contains($he, '<span data-blog-count="true">۰</span> مقاله'), 'شمارنده صفر');
check(str_contains($he, 'data-blog-item="true"') === false, 'بدون کارت در حالت خالی');

/* ---------- ۶) فیلتر GET (cat/s) + هوک فاز ۸ ---------- */
$_GET = ['cat' => '5', 's' => 'تونل نوری'];
$qargs = \ErfanSanat\Theme\Frontend\BlogArchive::query_args();
check(($qargs['cat'] ?? 0) === 5 && ($qargs['s'] ?? '') === 'تونل نوری', 'query_args از GET');

$fired = 0;
add_filter('es_blog_archive_query_args', function (array $args) use (&$fired): array {
    $fired++;
    $args['orderby'] = 'title';
    return $args;
});
$qargs2 = \ErfanSanat\Theme\Frontend\BlogArchive::query_args();
check($fired === 1 && ($qargs2['orderby'] ?? '') === 'title', 'هوک es_blog_archive_query_args');

$q = new class {
    public $query_vars = [];
    public function is_main_query(): bool { return true; }
    public function is_home(): bool { return true; }
    public function is_category(): bool { return false; }
    public function set(string $k, mixed $v): void { $this->query_vars[$k] = $v; }
};
\ErfanSanat\Theme\Frontend\BlogArchive::apply($q);
check(($q->query_vars['cat'] ?? 0) === 5 && ($q->query_vars['posts_per_page'] ?? 0) === 100 && ($q->query_vars['ignore_sticky_posts'] ?? false) === true, 'اعمال فیلترها روی کوئری (apply)');

$q2 = new class {
    public $query_vars = [];
    public function is_main_query(): bool { return true; }
    public function is_home(): bool { return false; }
    public function is_category(): bool { return false; }
    public function set(string $k, mixed $v): void { $this->query_vars[$k] = $v; }
};
\ErfanSanat\Theme\Frontend\BlogArchive::apply($q2);
check($q2->query_vars === [], 'بدون دست‌کاری کوئری غیرآرشیو');

/* جستجو در اینپوت هیرو */
$GLOBALS['wp_query'] = (object) ['posts' => $all_posts, 'found_posts' => 7];
ob_start();
require $root . '/home.php';
$hs = (string) ob_get_clean();
check(str_contains($hs, 'value="تونل نوری"'), 'مقدار جستجو از GET در اینپوت');

/* ---------- ۷) سازگاری card-post مستقل (رفتار ۵.۱۱) ---------- */
$GLOBALS['__es_stub_post_dates'][701] = '۱۲ مرداد ۱۴۰۴';
$GLOBALS['__wp_post_meta'][701]['es_read_time'] = '9';
ob_start();
es_get_template('template-parts/cards/card-post.php', ['post' => $posts[701], 'featured' => true, 'custom' => '0.05']);
$bc1 = (string) ob_get_clean();
check(str_contains($bc1, 'data-blog-item="true" data-blog-category="مجله علمی"') && str_contains($bc1, 'class="block"></div><div custom="0.05">'), 'کارت مستقل: WRAP بسته + بدنه (رفتار ۵.۱۱)');
check(str_contains($bc1, '>★ ویژه</span>'), 'کارت مستقل: نشان ویژه');
check(str_contains($bc1, '>ادامه مطلب'), 'کارت مستقل: CTA');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
