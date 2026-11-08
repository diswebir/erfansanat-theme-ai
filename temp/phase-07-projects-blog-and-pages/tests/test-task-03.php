<?php
/**
 * Test 7.3 — taxonomy-project_type / taxonomy-city — آرشیو ترم‌های پروژه (بایت‌دقیق مقابل میرور)
 * اجرا: /home/user/tools/php -f temp/phase-07-projects-blog-and-pages/tests/test-task-03.php
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
echo "== Test 7.3: taxonomy-project_type / taxonomy-city ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';
$U = 'https://test.example/wp-content/uploads/';

/* ---------- استخراج فیکسچر از میرورها ---------- */
$MIR_T = (string) file_get_contents($root . '/uploads/static-mirror/project_type/light-tunnel.html');
$MIR_C = (string) file_get_contents($root . '/uploads/static-mirror/city/esfahan.html');

$fa2en = fn (string $v): string => strtr($v, ['۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9']);

function build_terms(string $mirror): array {
    $fa2en = $GLOBALS['fa2en'];
    preg_match_all('/<a href="\.\.?\/project_type\/([^"]+)\.html"[^>]*><span class="flex items-center gap-2"><span class="h-2 w-2[^"]*"><\/span><span>([^<]+)<\/span><\/span><span class="rounded-md px-2 py-0\.5 text-\[11px\] font-extrabold bg-(?:slate-100|white\/20) text-(?:slate-500|white)">([^<]+)<\/span><\/a>/', $mirror, $tm, PREG_SET_ORDER);
    preg_match_all('/<a href="\.\.?\/city\/([^"]+)\.html"[^>]*><span class="truncate">([^<]+)<\/span><span class="rounded px-1\.5 py-0\.5 text-\[10px\] font-extrabold bg-white text-slate-500">([^<]+)<\/span><\/a>/', $mirror, $cm, PREG_SET_ORDER);
    $types = [];
    $seen = [];
    foreach ($tm as $mm) {
        if (isset($seen['t' . $mm[1]])) { continue; }
        $seen['t' . $mm[1]] = true;
        $types[] = (object) ['term_id' => 0, 'taxonomy' => 'project_type', 'slug' => (string) $mm[1], 'name' => (string) $mm[2], 'count' => (int) $fa2en((string) $mm[3])];
    }
    $cities = [];
    foreach ($cm as $mm) {
        if (isset($seen['c' . $mm[1]])) { continue; }
        $seen['c' . $mm[1]] = true;
        $cities[] = (object) ['term_id' => 0, 'taxonomy' => 'city', 'slug' => (string) $mm[1], 'name' => (string) $mm[2], 'count' => (int) $fa2en((string) $mm[3])];
    }
    return [$types, $cities];
}
$GLOBALS['fa2en'] = $fa2en;
[$types, $cities] = build_terms($MIR_T);
$name2slug = [];
foreach (array_merge($types, $cities) as $t) {
    $name2slug[$t->taxonomy . '|' . $t->name] = (string) $t->slug;
}

function build_cards(string $mirror, string $nav_anchor): array {
    $m0 = strpos($mirror, '<main>') + 6;
    $m1 = strpos($mirror, '</main>', $m0);
    preg_match_all('/<div data-project-item="true".*?(?=<div data-project-item="true"|<\/div><nav aria-label)/s', substr($mirror, $m0, $m1 - $m0), $im);
    return $im[0];
}
$items_t = build_cards($MIR_T, '');
$items_c = build_cards($MIR_C, '');
check(count($items_t) === 4, 'میرور نوع: ۴ کارت (تونل نوری)');
check(count($items_c) === 2, 'میرور شهر: ۲ کارت (اصفهان)');

$img_ids = [];
$next_aid = 71;

function make_fixture(array $items, string $U, array $name2slug, array &$img_ids, int &$next_aid): array {
    $posts = [];
    $titles = [];
    $permalinks = [];
    $meta = [];
    $tmap = [];
    foreach ($items as $k => $item) {
        $pid = 601 + $k;
        preg_match('/data-project-type="([^"]*)"/', $item, $chip);
        preg_match('/data-project-types="([^"]*)"/', $item, $typesall);
        preg_match('/data-project-location="([^"]*)"/', $item, $loc);
        preg_match('/data-project-views="([^"]*)"/', $item, $views);
        preg_match('/data-project-rating="([^"]*)"/', $item, $rating);
        preg_match('/data-project-search-text="([^"]*)"/', $item, $search);
        preg_match('/href="\.\.?\/projects\/([^"]+)\.html"/', $item, $href);
        preg_match('/<img src="(\.\.?\/images\/[^"]+)" alt="[^"]*"/', $item, $img);
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
        $ipath = preg_replace('~^(\.\./)+~', '', (string) $img[1]);
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
    return [$posts, $titles, $permalinks, $meta, $tmap];
}

[$posts_t, $titles_t, $permalinks_t, $meta_t, $tmap_t] = make_fixture($items_t, $U, $name2slug, $img_ids, $next_aid);
[$posts_c, $titles_c, $permalinks_c, $meta_c, $tmap_c] = make_fixture($items_c, $U, $name2slug, $img_ids, $next_aid);

$GLOBALS['__es_stub_terms'] = ['project_type' => $types, 'city' => $cities];
$GLOBALS['__es_stub_archive_links'] = ['projects' => 'https://test.example/projects/'];
$GLOBALS['__es_stub_wc_page_ids'] = ['shop' => 9];
$GLOBALS['__es_stub_permalinks'][9] = 'https://test.example/shop/';
$GLOBALS['__es_stub_attachment_urls'] = [];
foreach ($img_ids as $path => $aid) {
    $GLOBALS['__es_stub_attachment_urls'][(int) $aid] = $U . substr((string) $path, strlen('images/'));
}
$GLOBALS['__es_stub_post_counts'] = ['projects' => 12];
es_option_reset_cache();

$common_rep = fn (string $mirror): string => str_replace(
    ['href="../index.html"', 'href="../projects.html"', 'href="../contact.html"'],
    ['href="https://test.example/"', 'href="https://test.example/projects/"', 'href="../contact.html"'],
    $mirror
);

/* ---------- ۱) رندر ترم نوع (تونل نوری) — بایت‌دقیق ---------- */
$GLOBALS['__es_stub_queried_object'] = (object) ['term_id' => 71, 'name' => 'تونل نوری', 'slug' => 'light-tunnel', 'taxonomy' => 'project_type', 'description' => 'مجموعه پروژه‌های اجراشده تونل نوری (نیم‌آرک، ۹۰ درجه، کمانی و قلبی) با ریسه‌های فول‌کالر و پوینت لایت پیکسل IP68 در سراسر کشور.', 'count' => 4];
$GLOBALS['__wp_term_meta'] = [71 => [
    'es_archive_crumb' => 'تونل نوری',
    'es_archive_badge' => 'آرشیو تخصصی بر اساس نوع پروژه',
    'es_archive_title_a' => 'پروژه‌های اجرایی',
    'es_archive_title_b' => 'تونل نوری هوشمند و پیکسلی',
    'es_archive_caption' => 'پروژه‌های اجرایی تونل نوری نیم‌آرک، ۹۰ درجه و کمانی با پیکسل‌های فول‌کالر',
    'es_archive_top_title' => 'آرشیو نوع پروژه: تونل نوری',
    'es_archive_chip' => 'تونل نوری',
    'es_archive_image_id' => (int) $img_ids['images/light-tunnel.jpg'],
]];
$GLOBALS['__es_stub_titles'] = $titles_t;
$GLOBALS['__es_stub_posts'] = array_values($posts_t);
$GLOBALS['__wp_post_meta'] = $meta_t;
$GLOBALS['__es_stub_post_term_map'] = $tmap_t;
$GLOBALS['__es_stub_permalinks'] += $permalinks_t;
$GLOBALS['wp_query'] = (object) ['posts' => array_values($posts_t), 'found_posts' => 4];
$GLOBALS['__es_stub_query_vars'] = ['paged' => 1];

ob_start();
require $root . '/taxonomy-project_type.php';
$html_t = (string) ob_get_clean();
$s0 = strpos($html_t, '<main>');
$s1 = strpos($html_t, '</main>', $s0);
$inner_t = substr($html_t, $s0 + 6, $s1 - $s0 - 6);

$ref_t = substr($MIR_T, strpos($MIR_T, '<main>') + 6);
$ref_t = substr($ref_t, 0, strpos($ref_t, '</main>'));
// لینک‌سازی محیطی فقط تا قبل از مقاله — مقاله/CTA سگمنت استاتیک با لینک خام می‌مانند (قرارداد ۷.۲)
$art0 = strpos($ref_t, '<article');
$ref_art = substr($ref_t, $art0);
$ref_t = substr($ref_t, 0, $art0);
$ref_t = $common_rep($ref_t);
$ref_t = str_replace('src="../images/light-tunnel.jpg"', 'src="' . $U . 'light-tunnel.jpg"', $ref_t);
foreach (array_merge($types, $cities) as $t) {
    $ref_t = str_replace('href="../' . $t->taxonomy . '/' . $t->slug . '.html"', 'href="https://test.example/' . $t->taxonomy . '/' . $t->slug . '/"', $ref_t);
}
foreach ($permalinks_t as $pid => $pl) {
    $slug = str_replace(['https://test.example/projects/', '/'], '', $pl);
    $ref_t = str_replace('href="../projects/' . $slug . '.html"', 'href="' . $pl . '"', $ref_t);
}
foreach ($img_ids as $path => $aid) {
    $ref_t = str_replace('src="../' . $path . '"', 'src="' . $U . substr((string) $path, strlen('images/')) . '"', $ref_t);
}
$ref_t .= $ref_art;
check($inner_t === $ref_t, 'بایت‌دقیق: کل main شل نوع = میرور light-tunnel.html');
if ($inner_t !== $ref_t) {
    $n = min(strlen($inner_t), strlen($ref_t));
    for ($i = 0; $i < $n; $i++) { if ($inner_t[$i] !== $ref_t[$i]) { break; } }
}
check(str_starts_with($html_t, '<!doctype html>'), 'شل نوع: header/footer رندر شدند');
check(str_contains($inner_t, 'data-initial-type="تونل نوری"') && str_contains($inner_t, 'data-initial-location="همه"'), 'شل نوع: هوک‌های initial (type=تونل نوری)');
check(substr_count($inner_t, 'data-project-item="true"') === 4, 'شل نوع: ۴ کارت');
check(str_contains($inner_t, 'class="hidden mt-10 flex-wrap items-center justify-between') && str_contains($inner_t, '<span data-pagination-total="true" class="font-black text-slate-900">۱</span>'), 'ناو ترم: تک‌صفحه‌ای hidden + «۱ از ۱»');
check(str_contains($inner_t, 'data-page-prev="true" disabled=""') && str_contains($inner_t, 'data-page-next="true" disabled=""'), 'ناو ترم: قبلی/بعدی disabled');
check(substr_count($inner_t, 'بازنشانی</span></a>') === 1 && str_contains($inner_t, 'href="https://test.example/projects/" class="inline-flex items-center gap-1 rounded-lg bg-slate-100'), 'فیلتر: بلوک بازنشانی با لینک آرشیو');
check(str_contains($inner_t, 'href="https://test.example/project_type/light-tunnel/" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all bg-emerald-700'), 'فیلتر: ردیف فعال تونل نوری در سایدبار');
check(str_contains($inner_t, 'href="https://test.example/project_type/light-tunnel/" class="flex items-center justify-between rounded-xl border px-3.5 py-2.5 text-[12.5px] font-bold transition-all border-emerald-700 bg-emerald-700 text-white"><span>تونل نوری</span><span class="text-[11px] opacity-80">(۴)</span>'), 'دراور: ردیف فعال تونل نوری با شمارش (۴)');
check(!str_contains($inner_t, 'lucide-check h-4 w-4 opacity-100') || !str_contains($inner_t, '<span>همه پروژه‌ها</span><svg'), 'دراور: ردیف «همه» غیرفعال (opacity-0)');
check(str_contains($inner_t, '<span class="rounded-full bg-emerald-600 px-2.5 py-0.5 text-[11px] font-extrabold text-white">۱ فیلتر</span>'), 'دکمه شناساگر: «۱ فیلتر»');

/* ---------- ۲) رندر ترم شهر (اصفهان) — بایت‌دقیق ---------- */
$GLOBALS['__es_stub_queried_object'] = (object) ['term_id' => 72, 'name' => 'اصفهان', 'slug' => 'esfahan', 'taxonomy' => 'city', 'description' => 'مشاهده پروژه‌های شاخص طراحی، تولید و اجرای نورپردازی میادین، معابر و المان‌های هوشمند شهری در اصفهان توسط شرکت دانش‌بنیان عرفان صنعت اصفهان.', 'count' => 2];
$GLOBALS['__wp_term_meta'] = [72 => [
    'es_archive_crumb' => 'پروژه‌های اصفهان',
    'es_archive_badge' => 'آرشیو پروژه‌ها بر اساس شهر اجرای پروژه',
    'es_archive_title_a' => 'پروژه‌های نورپردازی شهری در',
    'es_archive_title_b' => 'کلان‌شهر اصفهان',
    'es_archive_caption' => 'پروژه نورپردازی میدان شهدای جهان اسلام — کلان‌شهر اصفهان',
    'es_archive_top_title' => 'پروژه‌های اجراشده در اصفهان',
    'es_archive_chip' => 'شهر: اصفهان',
    'es_archive_image_id' => (int) $img_ids['images/light-sphere.jpg'],
]];
$GLOBALS['__es_stub_titles'] = $titles_c;
$GLOBALS['__es_stub_posts'] = array_values($posts_c);
$GLOBALS['__wp_post_meta'] = $meta_c;
$GLOBALS['__es_stub_post_term_map'] = $tmap_c;
$GLOBALS['__es_stub_permalinks'] = [9 => 'https://test.example/shop/'] + $permalinks_c;
$GLOBALS['wp_query'] = (object) ['posts' => array_values($posts_c), 'found_posts' => 2];

ob_start();
require $root . '/taxonomy-city.php';
$html_c = (string) ob_get_clean();
$c0 = strpos($html_c, '<main>');
$c1 = strpos($html_c, '</main>', $c0);
$inner_c = substr($html_c, $c0 + 6, $c1 - $c0 - 6);

$ref_c = substr($MIR_C, strpos($MIR_C, '<main>') + 6);
$ref_c = substr($ref_c, 0, strpos($ref_c, '</main>'));
$artc0 = strpos($ref_c, '<article');
$ref_cart = substr($ref_c, $artc0);
$ref_c = substr($ref_c, 0, $artc0);
$ref_c = $common_rep($ref_c);
$ref_c = str_replace('src="../images/light-sphere.jpg"', 'src="' . $U . 'light-sphere.jpg"', $ref_c);
foreach (array_merge($types, $cities) as $t) {
    $ref_c = str_replace('href="../' . $t->taxonomy . '/' . $t->slug . '.html"', 'href="https://test.example/' . $t->taxonomy . '/' . $t->slug . '/"', $ref_c);
}
foreach ($permalinks_c as $pid => $pl) {
    $slug = str_replace(['https://test.example/projects/', '/'], '', $pl);
    $ref_c = str_replace('href="../projects/' . $slug . '.html"', 'href="' . $pl . '"', $ref_c);
}
foreach ($img_ids as $path => $aid) {
    $ref_c = str_replace('src="../' . $path . '"', 'src="' . $U . substr((string) $path, strlen('images/')) . '"', $ref_c);
}
$ref_c .= $ref_cart;
if ($inner_c !== $ref_c) {
}
check($inner_c === $ref_c, 'بایت‌دقیق: کل main شل شهر = میرور esfahan.html');
if ($inner_c !== $ref_c) {
    $n = min(strlen($inner_c), strlen($ref_c));
    for ($i = 0; $i < $n; $i++) { if ($inner_c[$i] !== $ref_c[$i]) { break; } }
}
check(str_contains($inner_c, 'data-initial-type="همه"') && str_contains($inner_c, 'data-initial-location="اصفهان"'), 'شل شهر: هوک‌های initial (location=اصفهان)');
check(str_contains($inner_c, 'href="https://test.example/city/esfahan/" class="flex items-center justify-between rounded-xl border px-3 py-2.5 text-[12px] font-bold transition-all border-emerald-800 bg-emerald-800 text-white"><span class="truncate">اصفهان</span><span class="text-[11px] opacity-75">(۲)</span>'), 'دراور شهر: ردیف فعال اصفهان (emerald-800)');
check(!str_contains($inner_c, 'rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all bg-emerald-700'), 'شل شهر: هیچ ردیف نوعی در سایدبار فعال نیست (deactivate_all)');

/* ---------- ۳) پیش‌فرض‌ها بدون متا ---------- */
$GLOBALS['__es_stub_queried_object'] = (object) ['term_id' => 73, 'name' => 'درخت نوری', 'slug' => 'light-tree', 'taxonomy' => 'project_type', 'description' => 'توضیح ترم درخت نوری.', 'count' => 3];
$GLOBALS['__wp_term_meta'] = [];
$GLOBALS['wp_query'] = (object) ['posts' => [array_values($posts_t)[0]], 'found_posts' => 3];
ob_start();
require $root . '/taxonomy-project_type.php';
$def_html = (string) ob_get_clean();
check(str_contains($def_html, 'پروژه‌های اجرایی') && str_contains($def_html, '>درخت نوری</span>') && str_contains($def_html, 'آرشیو تخصصی بر اساس نوع پروژه'), 'پیش‌فرض‌ها: عنوان/نشان از نام ترم، توضیح از description');
check(str_contains($def_html, 'src="' . ES_URI . 'assets/images/light-tunnel.jpg"'), 'پیش‌فرض: تصویر هیرو از دارایی تم');

/* ---------- ۴) حالت خالی + چندصفحه‌ای ---------- */
$GLOBALS['__wp_term_meta'] = [71 => ['es_archive_image_id' => (int) $img_ids['images/light-tunnel.jpg']]];
$GLOBALS['__es_stub_queried_object'] = (object) ['term_id' => 71, 'name' => 'تونل نوری', 'slug' => 'light-tunnel', 'taxonomy' => 'project_type', 'description' => 'د', 'count' => 0];
$GLOBALS['wp_query'] = (object) ['posts' => [], 'found_posts' => 0];
ob_start();
require $root . '/taxonomy-project_type.php';
$empty_html = (string) ob_get_clean();
check(str_contains($empty_html, '<div data-project-empty="true" class="block rounded-2xl') && !str_contains($empty_html, 'data-project-item="true"') && !str_contains($empty_html, '<nav aria-label="صفحه‌بندی پروژه‌ها"'), 'خالی: باکس empty block، بدون کارت و ناو');

$GLOBALS['__es_stub_queried_object'] = (object) ['term_id' => 71, 'name' => 'تونل نوری', 'slug' => 'light-tunnel', 'taxonomy' => 'project_type', 'description' => 'د', 'count' => 4];
$GLOBALS['__wp_options']['erfansanat_theme_options'] = ['archives' => ['projects_per_page' => '2']];
es_option_reset_cache();
$GLOBALS['wp_query'] = (object) ['posts' => array_values($posts_t), 'found_posts' => 4];
ob_start();
require $root . '/taxonomy-project_type.php';
$multi_html = (string) ob_get_clean();
check(str_contains($multi_html, 'class="flex mt-10 flex-wrap') && str_contains($multi_html, 'href="https://test.example/project_type/light-tunnel/?paged=2"'), 'چندصفحه‌ای: ناو flex + لینک صفحه ۲ با پایه ترم');
es_option_reset_cache();
unset($GLOBALS['__wp_options']['erfansanat_theme_options']);

/* ---------- ۵) ProjectArchive روی آرشیو ترم ---------- */
$PA = \ErfanSanat\Theme\Frontend\ProjectArchive::class;
$GLOBALS['__wp_is_post_type_archive'] = false;
$GLOBALS['__es_stub_is_tax'] = true;
$GLOBALS['__wp_options']['erfansanat_theme_options'] = ['archives' => ['projects_per_page' => '9']];
es_option_reset_cache();
class ES_PQ73 {
    public array $vars = ['posts_per_page' => 10];
    public function is_main_query(): bool { return true; }
    public function set(string $k, mixed $v): void { $this->vars[$k] = $v; }
    public function get(string $k): mixed { return $this->vars[$k] ?? null; }
}
$q = new ES_PQ73();
$PA::apply($q);
check(($q->vars['posts_per_page'] ?? 0) === 9, 'apply ترم: per-page از آپشن روی is_tax');
$_GET = ['sort' => 'rating'];
$q2 = new ES_PQ73();
$PA::apply($q2);
check(($q2->vars['meta_key'] ?? '') === 'es_rating', 'apply ترم: sort=rating → متا امتیاز');
$_GET = ['type' => 'light-tunnel', 'sort' => 'bogus'];
$args3 = $PA::query_args();
check(($args3['tax_query'][0]['terms'] ?? '') === 'light-tunnel' && !isset($args3['meta_key']), 'query_args: type و sort نامعتبر');
$_GET = [];
$GLOBALS['__es_stub_is_tax'] = false;
$q3 = new ES_PQ73();
$PA::apply($q3);
check(($q3->vars['posts_per_page'] ?? 10) === 10, 'apply: خارج از آرشیو پروژه/ترم → دست‌نخورده');
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
