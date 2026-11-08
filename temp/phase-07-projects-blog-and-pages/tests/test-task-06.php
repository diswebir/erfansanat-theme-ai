<?php
/**
 * Test 7.6 — search.php / 404.php / searchform.php — جستجوی ترکیبی و صفحه ۴۰۴
 * اجرا: /home/user/tools/php -f temp/phase-07-projects-blog-and-pages/tests/test-task-06.php
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
echo "== Test 7.6: search / 404 / searchform ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';
$U = 'https://test.example/wp-content/uploads/';
$HOME = 'https://test.example/';

/* ---------- فرگمنت‌های مرجع از میرور ---------- */
$BLOG = (string) file_get_contents($root . '/uploads/static-mirror/blog.html');
$m0 = strpos($BLOG, '<main class="min-h-screen bg-[#f8faf9]">') + strlen('<main class="min-h-screen bg-[#f8faf9]">');
$B = substr($BLOG, $m0, (int) strpos($BLOG, '</main>', $m0) - $m0);

$mir_input = (string) preg_replace('/ value="[^"]*"/', '', (string) preg_replace('/ data-blog-search="true"/', '', (string) preg_replace('/ name="[^"]*"/', '', (string) preg_replace('/ type="text"/', 'type="text"', (string) $B))));
preg_match('/<input type="text" data-blog-search="true"[^>]*class="([^"]+)"/', $B, $im);
$MIR_INPUT_CLS = $im[1];

preg_match('/<div data-blog-empty="true" class="([^"]+)"><p class="([^"]+)">/', $B, $em);
[$MIR_EMPTY_CLS, $MIR_EMPTY_P_CLS] = [$em[1], $em[2]];

$i0 = strpos($B, '<div aria-hidden="true" class="pointer-events-none absolute inset-0">');
$MIR_BACKDROP = substr($B, $i0, (int) strpos($B, '</div></div>', $i0) + 12 - $i0);

/* ---------- فیکسچر: نتایج از سه نوع ---------- */
$p_product = (object) ['ID' => 401, 'post_type' => 'product', 'post_title' => 'پوینت لایت تونل نوری', 'post_excerpt' => 'پیکسل IP68', 'post_content' => ''];
$p_project = (object) ['ID' => 601, 'post_type' => 'projects', 'post_title' => 'پروژه تونل نوری اصفهان', 'post_excerpt' => '', 'post_content' => ''];
$p_post = (object) ['ID' => 701, 'post_type' => 'post', 'post_title' => 'راهنمای تونل نوری', 'post_excerpt' => 'انواع تونل نوری', 'post_content' => ''];

$GLOBALS['__es_stub_posts'] = [$p_product, $p_project, $p_post];
$GLOBALS['__es_stub_permalinks'] = [
    401 => $HOME . 'product/point-light-tunnel/',
    601 => $HOME . 'projects/tunnel-esfahan/',
    701 => $HOME . 'blog/types-of-light-tunnel/',
];
$GLOBALS['__es_stub_attachment_urls'] = [90 => $U . 'light-tunnel.jpg'];
$GLOBALS['__wp_post_meta'] = [
    401 => ['_thumbnail_id' => '90', '_sku' => 'PLT-01', 'es_ip_rating' => 'IP68', 'es_voltage' => 'DC24V', 'number2-viewc' => '12', 'es_rating' => '۴.۵'],
    601 => ['_thumbnail_id' => '90', 'es_city' => 'اصفهان', 'number2-viewc' => '8', 'es_rating' => '۵', 'finish-pro-time' => '۱۲ مرداد ۱۴۰۴'],
    701 => ['_thumbnail_id' => '90', 'es_read_time' => '6', 'es_tags' => 'تونل نوری,نورپردازی'],
];
$GLOBALS['__es_stub_post_term_map'] = [
    401 => ['product_cat' => [(object) ['term_id' => 0, 'taxonomy' => 'product_cat', 'slug' => 'point-light', 'name' => 'پوینت لایت', 'count' => 0]]],
    601 => ['project_type' => [(object) ['term_id' => 0, 'taxonomy' => 'project_type', 'slug' => 'light-tunnel', 'name' => 'تونل نوری', 'count' => 0]], 'city' => [(object) ['term_id' => 0, 'taxonomy' => 'city', 'slug' => 'esfahan', 'name' => 'اصفهان', 'count' => 0]]],
    701 => ['category' => [(object) ['term_id' => 0, 'taxonomy' => 'category', 'slug' => 'magazine', 'name' => 'مجله علمی', 'count' => 0]]],
];
$GLOBALS['__es_stub_post_dates'] = [401 => '۱ مرداد ۱۴۰۴', 601 => '۱۲ مرداد ۱۴۰۴', 701 => '۱۲ مرداد ۱۴۰۴'];
$GLOBALS['__wp_options']['page_for_posts'] = 7426;
$GLOBALS['__es_stub_wc_page_ids'] = ['shop' => 9];
$GLOBALS['__es_stub_permalinks'][9] = $HOME . 'shop/';
es_option_reset_cache();

$GLOBALS['wp_query'] = (object) ['posts' => [$p_product, $p_project, $p_post], 'found_posts' => 3];
$GLOBALS['__es_stub_query_vars'] = ['s' => 'تونل', 'paged' => 1];
$_GET = [];
$GLOBALS['__es_stub_is_search'] = true;

/* ---------- ۱) رندر search.php ---------- */
ob_start();
require $root . '/search.php';
$sh = (string) ob_get_clean();

check(str_contains($sh, '<main class="min-h-screen bg-[#f8faf9]">'), 'main میرور');
check(str_contains($sh, 'نتایج جستجو برای: تونل'), 'H1 با عبارت جستجو');
check(str_contains($sh, $MIR_BACKDROP), 'backdrop بایت‌دقیق بلاگ');
check(str_contains($sh, 'data-search-count="true">۳</span> نتیجه برای «تونل»'), 'شمارنده نتایج = ۳');
check(str_contains($sh, 'data-product-item="true"') && str_contains($sh, 'data-project-item="true"') && str_contains($sh, 'data-blog-item="true"'), 'سه کارت سه نوع رندر شد');
check(str_contains($sh, 'href="' . $HOME . 'product/point-light-tunnel/"') && str_contains($sh, 'href="' . $HOME . 'blog/types-of-light-tunnel/"'), 'لینک کارت‌ها صحیح');
check(substr_count($sh, 'class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors bg-emerald-700 text-white"') === 2, 'تب فعال «همه» در هیرو و سکشن (قرص بایت‌دقیق ۷.۴)');
check(str_contains($sh, 'href="' . $HOME . '?s=' . rawurlencode('تونل') . '&amp;type=product"'), 'تب محصولات با type=product');
check(str_contains($sh, '<div data-search-empty="true" class="hidden ' . substr($MIR_EMPTY_CLS, strlen('hidden ')) . '">'), 'حالت خالی مخفی وقتی نتیجه هست (الگوی میرور)');
check(!str_contains($sh, 'data-pagination-root="search"'), 'تک‌صفحه‌ای: بدون ناو (قرارداد ۶.۷)');

/* ---------- ۲) فیلتر type=product ---------- */
$_GET = ['type' => 'product'];
$qargs = \ErfanSanat\Theme\Frontend\SearchArchive::query_args();
check(($qargs['post_type'] ?? '') === 'product', 'query_args: type=product');

$GLOBALS['wp_query'] = (object) ['posts' => [$p_product], 'found_posts' => 1];
ob_start();
es_get_template('template-parts/search/search-shell.php');
$sh2 = (string) ob_get_clean();
check(str_contains($sh2, 'data-product-item="true"') && !str_contains($sh2, 'data-project-item="true"') && !str_contains($sh2, 'data-blog-item="true"'), 'فیلتر type: فقط کارت محصول');
check(substr_count($sh2, 'bg-emerald-700 text-white"') === 0 || str_contains($sh2, 'نتایج محصولات'), 'هدر تب محصولات');
check(str_contains($sh2, 'href="' . $HOME . '?s=' . rawurlencode('تونل') . '"'), 'تب «همه» بدون type');

/* ---------- ۳) type نامعتبر → همه ---------- */
$_GET = ['type' => 'x"><script>'];
check(\ErfanSanat\Theme\Frontend\SearchArchive::query_args() === [], 'type نامعتبر دور ریخته می‌شود (sanitize_key)');

/* ---------- ۴) حالت خالی ---------- */
$GLOBALS['wp_query'] = (object) ['posts' => [], 'found_posts' => 0];
ob_start();
es_get_template('template-parts/search/search-shell.php');
$se = (string) ob_get_clean();
check(str_contains($se, '<div data-search-empty="true" class="' . substr($MIR_EMPTY_CLS, strlen('hidden ')) . '"><p class="' . $MIR_EMPTY_P_CLS . '">نتیجه‌ای برای این جستجو یافت نشد.'), 'حالت خالی با کلاس‌های بایت‌دقیق میرور (بدون hidden)');
check(str_contains($se, '<span data-search-count="true">۰</span> نتیجه'), 'شمارنده صفر');

/* ---------- ۴ب) ناو چندصفحه‌ای ---------- */
$many = [];
$many_meta = [];
for ($i = 0; $i < 12; $i++) {
    $pid = 750 + $i;
    $many[] = (object) ['ID' => $pid, 'post_type' => 'post', 'post_title' => 'مقاله ' . $i, 'post_excerpt' => '', 'post_content' => ''];
    $many_meta[$pid] = ['_thumbnail_id' => '90', 'es_read_time' => '5'];
    $GLOBALS['__es_stub_permalinks'][$pid] = $HOME . 'blog/post-' . $i . '/';
    $GLOBALS['__es_stub_post_term_map'][$pid] = ['category' => [(object) ['term_id' => 0, 'taxonomy' => 'category', 'slug' => 'magazine', 'name' => 'مجله علمی', 'count' => 0]]];
}
$GLOBALS['__wp_post_meta'] += $many_meta;
$GLOBALS['wp_query'] = (object) ['posts' => $many, 'found_posts' => 12];
ob_start();
es_get_template('template-parts/search/search-shell.php');
$sn = (string) ob_get_clean();
check(str_contains($sn, 'data-pagination-root="search"') && str_contains($sn, 'data-search-count="true"') && str_contains($sn, '(مجموعاً <span data-search-count="true">۱۲</span> نتیجه)'), 'ناو مشترک دوصفحه‌ای با شمارنده مجموع');

/* ---------- ۵) apply روی کوئری جستجو ---------- */
$_GET = ['type' => 'projects'];
$q = new class {
    public $query_vars = [];
    public function is_main_query(): bool { return true; }
    public function is_search(): bool { return true; }
    public function set(string $k, mixed $v): void { $this->query_vars[$k] = $v; }
};
\ErfanSanat\Theme\Frontend\SearchArchive::apply($q);
check(($q->query_vars['post_type'] ?? '') === 'projects' && ($q->query_vars['posts_per_page'] ?? 0) === 9, 'apply: post_type + per_page=9');

$q2 = new class {
    public $query_vars = [];
    public function is_main_query(): bool { return true; }
    public function is_search(): bool { return false; }
    public function set(string $k, mixed $v): void { $this->query_vars[$k] = $v; }
};
\ErfanSanat\Theme\Frontend\SearchArchive::apply($q2);
check($q2->query_vars === [], 'apply: کوئری غیرجستجو دست نمی‌خورد');

$fired = 0;
add_filter('es_search_query_args', function (array $args) use (&$fired): array {
    $fired++;
    return $args;
});
\ErfanSanat\Theme\Frontend\SearchArchive::query_args();
check($fired === 1, 'هوک es_search_query_args');

/* ---------- ۶) searchform ---------- */
$GLOBALS['__es_stub_query_vars'] = ['s' => 'تونل نوری'];
ob_start();
get_search_form();
$sf = (string) ob_get_clean();
check(str_contains($sf, '<form role="search" method="get" class="es-searchform" action="' . $HOME . '">'), 'فرم: GET به خانه');
check(str_contains($sf, 'name="s" ') && str_contains($sf, 'value="تونل نوری"'), 'فرم: name=s با مقدار پیش‌پر');
check(str_contains($sf, 'class="' . $MIR_INPUT_CLS . '"'), 'فرم: کلاس‌های اینپوت بایت‌دقیق میرور');
check(str_contains($sf, 'placeholder="جستجو در سایت…"'), 'فرم: placeholder');
check(!str_contains($sf, 'data-blog-search'), 'فرم: بدون هوک JS بلاگ');

/* ---------- ۷) 404 ---------- */
$GLOBALS['__es_stub_query_vars'] = ['s' => ''];
ob_start();
require $root . '/404.php';
$f4 = (string) ob_get_clean();
check(str_contains($f4, '<main class="min-h-screen bg-[#f8faf9]">') && str_contains($f4, $MIR_BACKDROP), '404: main + backdrop میرور');
check(str_contains($f4, '۴۰۴</div>') && str_contains($f4, 'این صفحه در دسترس نیست'), '404: شماره و پیام');
check(str_contains($f4, '<form role="search" method="get"'), '404: فرم جستجو رندر شد');
check(str_contains($f4, 'href="' . $HOME . 'shop/"') && str_contains($f4, 'فروشگاه محصولات'), '404: لینک فروشگاه');
check(str_contains($f4, 'href="' . $HOME . 'projects/"') && str_contains($f4, 'href="' . $HOME . 'blog/"'), '404: لینک پروژه‌ها و مقالات');
check(str_contains($f4, 'bg-emerald-700 text-white">صفحه اصلی</a>'), '404: قرص فعال صفحه اصلی');

/* ---------- ۸) جستجوی خالی ---------- */
$GLOBALS['wp_query'] = (object) ['posts' => [$p_product, $p_project, $p_post], 'found_posts' => 3];
$GLOBALS['__es_stub_query_vars'] = ['s' => ''];
ob_start();
es_get_template('template-parts/search/search-shell.php');
$s0 = (string) ob_get_clean();
check(str_contains($s0, '<h1 class="mx-auto mt-6 max-w-4xl text-center') && str_contains($s0, '>جستجو</h1>'), 'جستجوی خالی: H1 ساده');
check(!str_contains($s0, 'rounded-xl px-3.5 py-2'), 'جستجوی خالی: بدون تب‌ها');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
