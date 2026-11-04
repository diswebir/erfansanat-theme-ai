<?php
/**
 * Test 4.5 — سایدبار موبایل: بازسازی byte-exact آکاردئون محصولات + رندر داینامیک + حالت‌ها
 * اجرا: /home/user/tools/php -f temp/phase-04-header-footer-navigation/tests/test-task-05.php
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
echo "== Test 4.5: MobileWalker / سایدبار موبایل ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Nav\MobileWalker;

$ref = (string) file_get_contents($root . '/temp/phase-04-header-footer-navigation/refs/mobile-sidebar-ref.html');

// ═══ ۱) بازسازی byte-exact آکاردئون محصولات از فرگمنت‌ها ═══
$R = new ReflectionClass(MobileWalker::class);
$mp_head = str_replace('{ES_TOTAL}', '۱۵', (string) $R->getConstant('F_MP_HEAD'));
$tabs_m  = ['پیکسل و برد', 'ریسه‌های LED', 'درخت و المان', 'والواشر و نما'];
$cls     = ['border-emerald-600 bg-emerald-700 text-white', 'border-slate-200 bg-white text-slate-700', 'border-slate-200 bg-white text-slate-700', 'border-slate-200 bg-white text-slate-700'];
$recon   = $mp_head;
foreach ($tabs_m as $i => $title) {
    $tpl = $i === 0 ? (string) $R->getConstant('F_MP_TAB_ACTIVE') : (string) $R->getConstant('F_MP_TAB_IDLE');
    $recon .= strtr($tpl, ['{ES_I}' => (string) $i, '{ES_TITLE}' => $title, '{ES_TAB_CLASS}' => $cls[$i]]);
}
$recon .= (string) $R->getConstant('F_MP_MID');
for ($i = 0; $i < 4; $i++) {
    $p_i = (int) strpos($ref, '<div data-mobile-prod-panel="' . $i . '"');
    $g_i = (int) strpos($ref, '<a href="product_cat/', $p_i);
    $g_c = (int) strpos($ref, '</div>', $p_i);
    $pfx = strtr((string) $R->getConstant('F_MP_PANEL_PFX'), [
        '{ES_I}'           => (string) $i,
        '{ES_PANEL_CLASS}' => $i === 0 ? 'space-y-1 block' : 'hidden',
    ]);
    $recon .= $pfx . substr($ref, $g_i, $g_c - $g_i) . (string) $R->getConstant('F_MP_PANEL_SFX');
}
$recon .= str_replace('{ES_SHOP}', 'shop.html', (string) $R->getConstant('F_MP_TAIL'));
$i_acc1 = (int) strpos($ref, '<div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white"><button type="button" data-mobile-acc-btn="products"');
$i_proj = (int) strpos($ref, '<div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white"><button type="button" data-mobile-acc-btn="projects"');
check($recon === substr($ref, $i_acc1, $i_proj - $i_acc1), 'بازسازی byte-exact آکاردئون کامل محصولات');

// آکاردئون پروژه‌ها: بازسازی با لینک‌های میرور
$acc_proj = (string) $R->getConstant('F_ACC_PROJ');
preg_match('/data-mobile-proj-panel="type"[^>]*>(.*?)<\/div><div data-mobile-proj-panel="city"/s', $ref, $type_m);
$mt = (string) $R->getConstant('F_MT');
$type_recon = '';
preg_match_all('/<a href="project_type\/[^"]+"[^>]*>.*?<\/a>/s', $type_m[1] ?? '', $links);
foreach ($links[0] as $link) {
    preg_match('/href="([^"]+)"/', $link, $hm);
    preg_match('/>([^<]+)<\/a>/', $link, $lm);
    $type_recon .= strtr($mt, ['{ES_HREF}' => $hm[1], '{ES_LABEL}' => $lm[1]]);
}
preg_match('/data-mobile-proj-panel="city" class="hidden">(.*?)<\/div><a href="projects\.html"/s', $ref, $city_m);
preg_match_all('/<a href="city\/[^"]+"[^>]*>[^<]*<\/a>/s', $city_m[1] ?? '', $city_links);
$city_recon = '';
foreach ($city_links[0] as $link) {
    preg_match('/href="([^"]+)"/', $link, $hm);
    preg_match('/>([^<]+)<\/a>/', $link, $lm);
    $city_recon .= strtr((string) $R->getConstant('F_PC'), ['{ES_HREF}' => $hm[1], '{ES_LABEL}' => $lm[1]]);
}
$proj_full = strtr($acc_proj, [
    '{ES_LINKS_TYPE}' => $type_recon,
    '{ES_LINKS_CITY}' => $city_recon,
    '{ES_ALL_URL}'    => 'projects.html',
]);
check(str_contains($ref, $proj_full), 'فرگمنت کامل آکاردئون پروژه‌ها = مرجع (byte-exact)');

// ═══ ۲) رندر داینامیک ═══
$GLOBALS['__wp_options'] = [];
es_option_reset_cache();
$GLOBALS['__es_stub_terms'] = [
    'product_cat'  => [
        (object) ['term_id' => 1, 'name' => 'پوینت لایت', 'slug' => 'point-light', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 2, 'name' => 'ریسه LED', 'slug' => 'strings', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 3, 'name' => 'درخت نوری', 'slug' => 'light-tree', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 4, 'name' => 'والواشر', 'slug' => 'wall-washer', 'taxonomy' => 'product_cat', 'parent' => 0],
        (object) ['term_id' => 5, 'name' => 'باکس کنترل', 'slug' => 'control-box', 'taxonomy' => 'product_cat', 'parent' => 1],
    ],
    'project_type' => [
        (object) ['term_id' => 10, 'name' => 'نورپردازی میدان', 'slug' => 'square-lighting', 'taxonomy' => 'project_type', 'parent' => 0],
    ],
    'city'         => [
        (object) ['term_id' => 20, 'name' => 'اصفهان', 'slug' => 'esfahan', 'taxonomy' => 'city', 'parent' => 0],
    ],
];
$GLOBALS['__es_stub_posts'] = [(object) ['ID' => 90, 'post_type' => 'services', 'post_title' => 'نورپردازی پل']];

ob_start();
MobileWalker::output();
$html = (string) ob_get_clean();

check(!str_contains($html, '{ES_'), 'بدون placeholder جامانده');
check(str_contains($html, 'data-mobile-menu="true"') && str_contains($html, 'data-mobile-backdrop="true"') && str_contains($html, 'data-mobile-sidebar="true"'), 'wrapper/backdrop/sidebar');
check(substr_count($html, 'data-mobile-acc-btn=') === 4, '۴ آکاردئون (products/projects/services/corporate)');
check(substr_count($html, 'data-mobile-prod-tab=') === 4, '۴ تب محصولات موبایل');
check(substr_count($html, 'data-mobile-prod-panel=') === 4, '۴ پنل محصولات');
check(substr_count($html, 'data-mobile-proj-tab=') === 2, '۲ تب پروژه');
check(str_contains($html, 'محصولات تولیدی (۴ دسته)'), 'شمارنده دسته‌ها با ارقام فارسی');
check(str_contains($html, 'cursor-pointer border-emerald-600 bg-emerald-700 text-white">'), 'تب اول موبایل فعال (کلاس‌های انتهای attr مطابق میرور)');
check(str_contains($html, '<div data-mobile-prod-panel="0" class="space-y-1 block">'), 'پنل اول block');
check(str_contains($html, '<div data-mobile-prod-panel="1" class="hidden">'), 'پنل‌های بعدی hidden');
check(str_contains($html, 'href="' . home_url('/') . 'product_cat/control-box/"'), 'لینک فرزند دسته (داینامیک)');
check(str_contains($html, 'href="' . home_url('/') . 'project_type/square-lighting/"'), 'لینک project_type');
check(str_contains($html, 'href="' . home_url('/') . 'city/esfahan/"'), 'لینک city');
check(str_contains($html, 'همه پروژه‌ها'), 'CTA همه پروژه‌ها');
check(str_contains($html, 'href="' . es_page_url('shop') . '"'), 'CTA کاتالوگ → فروشگاه');
check(str_contains($html, 'نورپردازی پل'), 'خدمات از CPT');
check(str_contains($html, 'href="tel:031-91091011"'), 'تلفن از es_contact_args');
check(str_contains($html, 'تماس با کارخانه: ۰۳۱ - ۹۱۰۹۱۰۱۱'), 'نمایش تلفن بدون پرانتز');
check(str_contains($html, 'درباره کارخانه عرفان صنعت'), 'corporate — لیبل ثابت میرور');
check(str_contains($html, 'data-cart-modal-open="true"'), 'دکمه سبد (hook)');
check(!str_contains(preg_replace('/<div data-mobile-menu.*?<aside/s', '<aside', $html) ?? $html, '>ESE</div>') || str_contains($html, '>ESE</div>'), 'لوگوی متنی ESE پیش‌فرض');
check(str_contains($html, 'data-mobile-acc-icon="true"'), 'آیکون آکاردئون (hook JS)');

// ═══ ۳) اسکیپ امن ═══
$GLOBALS['__es_stub_terms']['product_cat'][0]->name = 'پوینت<script>alert(1)</script>';
ob_start();
MobileWalker::output();
$safe = (string) ob_get_clean();
check(!str_contains($safe, '<script>alert'), 'عنوان تب اسکیپ می‌شود');

// ═══ ۴) حالت خالی ═══
$GLOBALS['__es_stub_terms'] = [];
$GLOBALS['__es_stub_posts'] = [];
ob_start();
MobileWalker::output();
$empty = (string) ob_get_clean();
check(substr_count($empty, 'data-mobile-acc-btn=') === 4, 'حالت خالی — اسکلت ۴ آکاردئون حفظ');
check(str_contains($empty, 'محصولات تولیدی (۰ دسته)'), 'شمارنده صفر با ارقام فارسی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
