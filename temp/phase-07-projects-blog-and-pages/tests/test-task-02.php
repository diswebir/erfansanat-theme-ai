<?php
/**
 * Test 7.2 — archive-projects: آرشیو پروژه‌ها — هیرو/فیلترها/گرید/صفحه‌بندی (بایت‌دقیق مقابل میرور)
 * اجرا: /home/user/tools/php -f temp/phase-07-projects-blog-and-pages/tests/test-task-02.php
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
echo "== Test 7.2: archive-projects ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';
const MIRROR = '/uploads/static-mirror/projects.html';

/* ---------- استخراج فیکسچر از خود میرور (بدون تایپ دستی) ---------- */
$mirror = (string) file_get_contents($root . MIRROR);
$m0 = strpos($mirror, '<main>') + strlen('<main>');
$m1 = strpos($mirror, '</main>', $m0);

// ترم‌ها از ردیف‌های سایدبار
preg_match_all('/<a href="project_type\/([^"]+)\.html"[^>]*><span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-500\/60"><\/span><span>([^<]+)<\/span><\/span><span class="rounded-md px-2 py-0\.5 text-\[11px\] font-extrabold bg-slate-100 text-slate-500">([^<]+)<\/span><\/a>/', $mirror, $tm, PREG_SET_ORDER);
preg_match_all('/<a href="city\/([^"]+)\.html"[^>]*><span class="truncate">([^<]+)<\/span><span class="rounded px-1\.5 py-0\.5 text-\[10px\] font-extrabold bg-white text-slate-500">([^<]+)<\/span><\/a>/', $mirror, $cm, PREG_SET_ORDER);
$fa2en = fn (string $v): string => strtr($v, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9']);
$types = [];
foreach ($tm as $mm) {
    $types[] = (object) ['term_id' => 0, 'taxonomy' => 'project_type', 'slug' => (string) $mm[1], 'name' => (string) $mm[2], 'count' => (int) $fa2en((string) $mm[3])];
}
$cities = [];
foreach ($cm as $mm) {
    $cities[] = (object) ['term_id' => 0, 'taxonomy' => 'city', 'slug' => (string) $mm[1], 'name' => (string) $mm[2], 'count' => (int) $fa2en((string) $mm[3])];
}
$name2slug = [];
foreach (array_merge($types, $cities) as $t) {
    $name2slug[$t->taxonomy . '|' . $t->name] = (string) $t->slug;
}
$GLOBALS['__es_stub_terms'] = ['project_type' => $types, 'city' => $cities];

// شش کارت صفحه ۱
preg_match_all('/<div data-project-item="true".*?(?=<div data-project-item="true"|<\/div><nav aria-label)/s', substr($mirror, $m0, $m1 - $m0), $im);
$items_all = $im[0];
check(count($items_all) === 12, 'میرور: ۱۲ کارت در گرید آرشیو');
$items6 = array_slice($items_all, 0, 6);

$U = 'https://test.example/wp-content/uploads/';
$img_ids = [];
$posts = [];
$titles = [];
$permalinks = [];
$meta = [];
$tmap = [];
$next_aid = 61;
foreach ($items6 as $k => $item) {
    $pid = 501 + $k;
    preg_match('/data-project-type="([^"]*)"/', $item, $chip);
    preg_match('/data-project-types="([^"]*)"/', $item, $typesall);
    preg_match('/data-project-location="([^"]*)"/', $item, $loc);
    preg_match('/data-project-views="([^"]*)"/', $item, $views);
    preg_match('/data-project-rating="([^"]*)"/', $item, $rating);
    preg_match('/data-project-search-text="([^"]*)"/', $item, $search);
    preg_match('/href="projects\/([^"]+)\.html"/', $item, $href);
    preg_match('/<img src="(images\/[^"]+)" alt="[^"]*"/', $item, $img);
    preg_match('/<h2 class="text-lg font-extrabold leading-7 text-white \[text-shadow:0_2px_12px_rgba\(0,0,0,\.8\)\]">([^<]+)<\/h2>/', $item, $title);
    preg_match('/lucide-map-pin h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4\.993[^"]*"><\/path><circle cx="12" cy="10" r="3"><\/circle><\/svg>([^<]+)<\/span>/', $item, $citytx);
    preg_match('/lucide-calendar-check2 lucide-calendar-check-2 h-3\.5 w-3\.5 text-emerald-600" aria-hidden="true">(?:<path d="[^"]*"><\/path>)+<\/svg>([^<]+)<\/span>/', $item, $datetx);
    preg_match('/text-amber-600"><svg[^>]*>.*?<\/svg>([^<]+)<\/span>/s', $item, $ratingtx);
    preg_match('/lucide-eye h-3\.5 w-3\.5 text-slate-400" aria-hidden="true"><path d="M2\.062[^"]*"><\/path><circle cx="12" cy="12" r="3"><\/circle><\/svg>([^<]+)<\/span>/', $item, $viewstx);
    $title = (string) $title[1];
    $city  = (string) $citytx[1];
    $chipx = (string) $chip[1];
    $prefix = $title . ' ' . $city . ' ' . $chipx . ' ';
    $client = str_starts_with((string) $search[1], $prefix) ? substr((string) $search[1], strlen($prefix)) : '';
    $titles[$pid] = $title;
    $permalinks[$pid] = 'https://test.example/projects/' . $href[1] . '/';
    $ipath = (string) $img[1];
    if (!isset($img_ids[$ipath])) {
        $img_ids[$ipath] = (string) $next_aid++;
    }
    $meta[$pid] = [
        '_thumbnail_id' => $img_ids[$ipath],
        'number2-viewc' => (string) $views[1],
        'es_rating'     => (string) $rating[1],
        'finish-pro-time' => (string) $datetx[1],
        'es_client'     => $client,
    ];
    $tterms = [];
    foreach (explode('|', (string) $typesall[1]) as $tn) {
        $tslug = $name2slug['project_type|' . $tn] ?? '';
        $tterms[] = (object) ['term_id' => 0, 'taxonomy' => 'project_type', 'slug' => $tslug, 'name' => $tn, 'count' => 0];
    }
    $cslug = $name2slug['city|' . $city] ?? '';
    $tmap[$pid] = [
        'project_type' => $tterms,
        'city' => [(object) ['term_id' => 0, 'taxonomy' => 'city', 'slug' => $cslug, 'name' => $city, 'count' => 0]],
    ];
    $posts[$pid] = (object) ['ID' => $pid, 'post_type' => 'projects', 'post_title' => $title, 'post_excerpt' => '', 'post_content' => ''];
}
$GLOBALS['__es_stub_titles'] = $titles;
$GLOBALS['__es_stub_posts'] = array_values($posts);
$GLOBALS['__wp_post_meta'] = $meta;
$GLOBALS['__es_stub_post_term_map'] = $tmap;
$GLOBALS['__es_stub_permalinks'] = $permalinks;
$GLOBALS['__es_stub_attachment_urls'] = [];
foreach ($img_ids as $path => $aid) {
    $GLOBALS['__es_stub_attachment_urls'][(int) $aid] = $U . substr($path, strlen('images/'));
}
$GLOBALS['__es_stub_archive_links'] = ['projects' => 'https://test.example/projects/'];
$GLOBALS['__es_stub_wc_page_ids'] = ['shop' => 9];
$GLOBALS['__es_stub_permalinks'][9] = 'https://test.example/shop/';
$GLOBALS['wp_query'] = (object) ['posts' => array_values($posts), 'found_posts' => 12];
$GLOBALS['__es_stub_query_vars'] = ['paged' => 1];
es_option_reset_cache();

$common_img_rep = [];
foreach ($img_ids as $path => $aid) {
    $common_img_rep['src="' . $path . '"'] = 'src="' . $U . substr($path, strlen('images/')) . '"';
}

/* ---------- ۱) هیرو — بایت‌دقیق ---------- */
$hero0 = strpos($mirror, '<section class="relative overflow-hidden bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-36">', $m0);
$sec0 = strpos($mirror, '<section id="archive"', $m0);
ob_start();
es_get_template('template-parts/archive/hero-projects.php', []);
$hero_direct = ob_get_clean(); // نباید وجود داشته باشد — شل خودش nowdoc دارد
check($hero_direct === '', 'هیرو: پارشیال جدا وجود ندارد (داخل شل)');
$HERO_TEST = null; // هیرو از رندر شل قیاس می‌شود

/* ---------- ۲) سایدبار فیلترها — بایت‌دقیق ---------- */
$aside0 = strpos($mirror, '<aside aria-label="فیلتر و آرشیو پروژه‌ها"', $m0);
$aside1 = strpos($mirror, '</aside>', $aside0) + strlen('</aside>');
$aside_ref = substr($mirror, $aside0, $aside1 - $aside0);
$aside_ref = str_replace('href="projects.html"', 'href="https://test.example/projects/"', $aside_ref);
foreach ($types as $t) {
    $aside_ref = str_replace('href="project_type/' . $t->slug . '.html"', 'href="https://test.example/project_type/' . $t->slug . '/"', $aside_ref);
}
foreach ($cities as $t) {
    $aside_ref = str_replace('href="city/' . $t->slug . '.html"', 'href="https://test.example/city/' . $t->slug . '/"', $aside_ref);
}
ob_start();
es_get_template('template-parts/archive/project-filters.php', ['count' => 12, 'active_type' => '', 'active_city' => '']);
$aside_html = (string) ob_get_clean();
check($aside_html === $aside_ref, 'بایت‌دقیق: سایدبار فیلتر (نوع ۸ ردیف + شهر ۱۰ ردیف + همه + جستجو + مشاوره)');
ob_start();
es_get_template('template-parts/archive/project-filters.php', ['count' => 12, 'active_type' => 'light-tunnel', 'active_city' => '']);
$aside_active = (string) ob_get_clean();
$at = $types[1];
check(substr_count($aside_active, 'bg-emerald-700 text-white shadow-sm shadow-emerald-700/20') === 1
    && str_contains($aside_active, '<span>' . $at->name . '</span></span><span class="rounded-md px-2 py-0.5 text-[11px] font-extrabold bg-white/20 text-white">' . es_fa_num((string) $at->count) . '</span>')
    && !str_contains($aside_active, 'href="https://test.example/projects/" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all bg-emerald-700'),
    'فیلتر: حالت فعال نوع → همه غیرفعال + ردیف «' . $at->name . '» فعال');

/* ---------- ۳) نوار بالای گرید — بایت‌دقیق ---------- */
$topbar0 = $aside1;
$grid0 = strpos($mirror, '<div data-project-grid="true"', $topbar0);
$topbar_ref = substr($mirror, $topbar0, $grid0 - $topbar0);
$shell_prev = null;

/* ---------- ۴) رندر کامل شل — ترکیب main بایت‌دقیق ---------- */
ob_start();
require $root . '/archive-projects.php';
$shell_html = (string) ob_get_clean();
$s0 = strpos($shell_html, '<main>');
$s1 = strpos($shell_html, '</main>', $s0);
$inner = substr($shell_html, $s0 + strlen('<main>'), $s1 - $s0 - strlen('<main>'));

// بازسازی main از میرور با جایگزینی‌های محیطی
$main_ref = substr($mirror, $m0, $m1 - $m0);
// میرور ۱۲ کارت دمو دارد؛ per-page=۶ → فقط ۶ کارت اول بازسازی می‌شود
$ipos = [];
$icur = 0;
foreach ($items_all as $it) {
    $icur = strpos($main_ref, $it, $icur);
    $ipos[] = [$icur, $icur + strlen($it)];
    $icur += strlen($it);
}
$main_ref = substr($main_ref, 0, $ipos[6][0]) . substr($main_ref, $ipos[11][1]);
$main_ref = str_replace('href="index.html"', 'href="https://test.example/"', $main_ref);
foreach ($common_img_rep as $from => $to) {
    $main_ref = str_replace($from, $to, $main_ref);
}
// جایگزینی لینک‌های ترم فقط داخل سایدبار (پویا)؛ دراور/مقاله استاتیک می‌مانند (قرارداد archive-product)
$a0 = strpos($main_ref, '<aside aria-label="فیلتر و آرشیو پروژه‌ها"');
$a1 = strpos($main_ref, '</aside>', $a0) + 8;
$aside_ref = substr($main_ref, $a0, $a1 - $a0);
$aside_ref = str_replace('href="projects.html"', 'href="https://test.example/projects/"', $aside_ref);
foreach ($types as $t) {
    $aside_ref = str_replace('href="project_type/' . $t->slug . '.html"', 'href="https://test.example/project_type/' . $t->slug . '/"', $aside_ref);
}
foreach ($cities as $t) {
    $aside_ref = str_replace('href="city/' . $t->slug . '.html"', 'href="https://test.example/city/' . $t->slug . '/"', $aside_ref);
}
$main_ref = substr($main_ref, 0, $a0) . $aside_ref . substr($main_ref, $a1);
// لینک باکس empty-state پویاست ({ES_PROJECTS_URL} در شل)
$e0 = strpos($main_ref, '<div data-project-empty="true"');
$e1 = strpos($main_ref, 'نمایش همه پروژه‌ها</span></a></div>', $e0) + strlen('نمایش همه پروژه‌ها</span></a></div>');
$empty_ref = substr($main_ref, $e0, $e1 - $e0);
$empty_ref = str_replace('href="projects.html"', 'href="https://test.example/projects/"', $empty_ref);
$main_ref = substr($main_ref, 0, $e0) . $empty_ref . substr($main_ref, $e1);
foreach ($permalinks as $pid => $pl) {
    $slug = str_replace(['https://test.example/projects/', '/'], '', $pl);
    $main_ref = str_replace('href="projects/' . $slug . '.html"', 'href="' . $pl . '"', $main_ref);
}
// تصاویر هیرو: میرور images/... → دارایی‌های تم (es_media_url)
$hstart = strpos($main_ref, '<section class="relative overflow-hidden bg-[#f6f8f7]');
$hend = strpos($main_ref, '</section>', $hstart) + 10;
$hero_part = substr($main_ref, $hstart, $hend - $hstart);
foreach (['light-tunnel', 'light-sphere', 'light-element'] as $im) {
    $hero_part = str_replace('src="' . $U . $im . '.jpg"', 'src="' . ES_URI . 'assets/images/' . $im . '.jpg"', $hero_part);
}
$main_ref = substr($main_ref, 0, $hstart) . $hero_part . substr($main_ref, $hend);
// صفحه ۲ ناو: دکمه → لینک استاندارد + data-href بعدی
$paged2 = 'https://test.example/projects/?paged=2';
$main_ref = preg_replace('/<button type="button" data-page-num="2"[^>]*>([^<]*)<\/button>/',
    '<a href="' . $paged2 . '" data-page-num="2" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer border border-slate-200 bg-white text-slate-700 hover:border-emerald-300 hover:text-emerald-800">$1</a>', $main_ref);
$main_ref = str_replace('disabled:opacity-40 cursor-pointer"><span>بعدی</span>',
    'disabled:opacity-40 cursor-pointer" data-href="' . $paged2 . '"><span>بعدی</span>', $main_ref);

check($inner === $main_ref, 'بایت‌دقیق: کل main شل = میرور (هیرو+فیلترها+نوار+۶ کارت+ناو+خالی+CTA)');
if ($inner !== $main_ref) {
    $n = min(strlen($inner), strlen($main_ref));
    for ($i = 0; $i < $n; $i++) { if ($inner[$i] !== $main_ref[$i]) break; }
    echo 'MAINLEN out=' . strlen($inner) . ' ref=' . strlen($main_ref) . ' diff@' . $i . "\n";
    file_put_contents('/host/home/user/erfansanat-theme-ai/temp/out.win', substr($inner, max(0, $i - 160), 400));
    file_put_contents('/host/home/user/erfansanat-theme-ai/temp/ref.win', substr($main_ref, max(0, $i - 160), 400));
}
check(str_starts_with($shell_html, '<!doctype html>') && strlen($shell_html) > $s1 + 100, 'شل: header و footer رندر شدند');
check(str_contains($inner, 'data-projects-archive="true"') && str_contains($inner, 'data-items-per-page="6"'), 'شل: هوک‌های آرشیو (data-projects-archive + per-page=6)');
check(substr_count($inner, 'data-project-item="true"') === 6, 'گرید: ۶ کارت صفحه ۱ (per-page میرور)');

/* ---------- ۵) آرشیو خالی ---------- */
$GLOBALS['__es_archive_total'] = 0;
$GLOBALS['wp_query'] = (object) ['posts' => [], 'found_posts' => 0];
ob_start();
require $root . '/archive-projects.php';
$empty_html = (string) ob_get_clean();
check(str_contains($empty_html, '<div data-project-empty="true" class="block rounded-2xl'), 'خالی: باکس empty بدون hidden (block)');
check(!str_contains($empty_html, '<nav aria-label="صفحه‌بندی پروژه‌ها"') && !str_contains($empty_html, 'data-project-item="true"'), 'خالی: بدون ناو و بدون کارت');
unset($GLOBALS['__es_archive_total']);
$GLOBALS['wp_query'] = (object) ['posts' => array_values($posts), 'found_posts' => 12];

/* ---------- ۶) مرتب‌سازی: selected سورتمان ---------- */
$_GET['sort'] = 'popular';
ob_start();
require $root . '/archive-projects.php';
$pop_html = (string) ob_get_clean();
check(str_contains($pop_html, '<option value="popular" selected="">') && !str_contains($pop_html, '<option value="default" selected="">'), 'مرتب‌سازی: sort=popular → selected در سکلت');
$_GET = [];

/* ---------- ۷) ProjectArchive — args و apply ---------- */
$GLOBALS['__wp_options']['erfansanat_theme_options'] = ['archives' => ['projects_per_page' => '9']];
es_option_reset_cache();
$_GET = ['type' => 'light-tunnel', 'location' => 'ahvaz', 'sort' => 'popular'];
$PA = \ErfanSanat\Theme\Frontend\ProjectArchive::class;
$args = $PA::query_args();
check(($args['tax_query'] ?? []) === [
    ['taxonomy' => 'project_type', 'field' => 'slug', 'terms' => 'light-tunnel'],
    ['taxonomy' => 'city', 'field' => 'slug', 'terms' => 'ahvaz'],
], 'ProjectArchive: tax_query نوع+شهر (slug)');
check(($args['meta_key'] ?? '') === 'number2-viewc' && ($args['orderby'] ?? '') === 'meta_value_num' && ($args['order'] ?? '') === 'DESC', 'ProjectArchive: sort=popular → متای بازدید');
$_GET = ['sort' => 'rating'];
$args2 = $PA::query_args();
check(($args2['meta_key'] ?? '') === 'es_rating' && !isset($args2['tax_query']), 'ProjectArchive: sort=rating → امتیاز، بدون فیلتر ترم');
$_GET = ['type' => 'x"><script>', 'location' => 'all', 'sort' => 'bogus'];
$args3 = $PA::query_args();
$terms3 = (string) ($args3['tax_query'][0]['terms'] ?? '');
check(!str_contains($terms3, '<') && !str_contains($terms3, 'script'), 'ProjectArchive: ورودی خطرناک sanitize شد');
check(!isset($args3['meta_key']), 'ProjectArchive: sort نامعتبر نادیده گرفته شد');
$_GET = [];

class ES_PQ72 {
    public array $vars = ['posts_per_page' => 10];
    public function is_main_query(): bool { return true; }
    public function set(string $k, mixed $v): void { $this->vars[$k] = $v; }
    public function get(string $k): mixed { return $this->vars[$k] ?? null; }
}
$GLOBALS['__wp_is_post_type_archive'] = true;
$GLOBALS['__wp_post_type_archive'] = 'projects';
$q = new ES_PQ72();
$PA::apply($q);
check(($q->vars['posts_per_page'] ?? 0) === 9, 'apply: per-page از آپشن (۹)');
$_GET = ['sort' => 'popular', 'type' => 'ball-of-light'];
$q2 = new ES_PQ72();
$PA::apply($q2);
check(($q2->vars['meta_key'] ?? '') === 'number2-viewc' && ($q2->vars['tax_query'][0]['terms'] ?? '') === 'ball-of-light', 'apply: sort+type اعمال شد');
$GLOBALS['__wp_is_post_type_archive'] = false;
$q3 = new ES_PQ72();
$PA::apply($q3);
check(!isset($q3->vars['meta_key']) && ($q3->vars['posts_per_page'] ?? 10) === 10, 'apply: غیر از آرشیو projects → دست‌نخورده');
$GLOBALS['__wp_post_type_archive'] = 'projects';

/* ---------- ۸) clamp per-page ---------- */
$GLOBALS['__wp_options']['erfansanat_theme_options'] = ['archives' => ['projects_per_page' => '500']];
$GLOBALS['__wp_is_post_type_archive'] = true;
es_option_reset_cache();
$q4 = new ES_PQ72();
$PA::apply($q4);
check(($q4->vars['posts_per_page'] ?? 0) === 100, 'apply: کلامپ per-page (۵۰۰ → ۱۰۰)');
es_option_reset_cache();
unset($GLOBALS['__wp_options']['erfansanat_theme_options']);

echo "\nنتیجه: $passes پاس، " . count($failures) . " رد\n";
if ($failures !== []) {
    echo "FAILURES:\n";
    foreach ($failures as $f) {
        echo "  - $f\n";
    }
    exit(1);
}
exit(0);
