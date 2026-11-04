<?php
/**
 * Test 6.2 — single-product: هیرو + گالری لایت‌باکس (بایت‌دقیق مقابل میرور) + fallback زنجیره‌ای
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-02.php
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
echo "== Test 6.2: single-product hero + gallery ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

/* ---------- فیکسچر: محصول والواشر مطابق میرور ---------- */
$TITLE = 'والواشر RGB و تک‌رنگ صنعتی (IP68)';
$GLOBALS['__es_stub_titles'] = [900 => $TITLE];
$GLOBALS['__wp_post_meta'] = [
    900 => [
        '_thumbnail_id' => '71',
        'galerry_produ' => '72,73,74',
        'zireonvan'     => 'بدنه آلومینیوم اکسترود سنگین با شیشه سکوریت، ۱۶ میلیون رنگ و توان ۳۶ وات',
    ],
];
$GLOBALS['__es_stub_post_term_map'] = [
    900 => ['product_cat' => [(object) ['name' => 'لینیرلایت و والواشر', 'slug' => 'wall-washer', 'taxonomy' => 'product_cat']]],
];
$GLOBALS['__es_stub_attachment_urls'] = [
    71 => 'https://test.example/wp-content/uploads/light-element.jpg',
    72 => 'https://test.example/wp-content/uploads/pexels-35318900.jpg',
    73 => 'https://test.example/wp-content/uploads/pexels-36052966.jpg',
    74 => 'https://test.example/wp-content/uploads/pexels-29570545.jpg',
];
$GLOBALS['__es_stub_current_id'] = 900;

/* ---------- ۱) رندر گالری و هیرو ---------- */
ob_start();
es_get_template('template-parts/single-product/gallery.php', ['pid' => 900]);
$gal = ob_get_clean();
ob_start();
es_get_template('template-parts/single-product/hero.php', ['pid' => 900]);
$hero = ob_get_clean();

check(str_contains($gal, 'data-gallery-main-img="true"'), 'گالری: هوک main-img');
check(str_contains($gal, 'data-gallery-root="true"'), 'گالری: هوک root');
check(substr_count($gal, 'data-gallery-thumb="') === 4, 'گالری: چهار thumb از galerry_produ+thumbnail');
check(str_contains($gal, '>۱ / ۴</span>'), 'گالری: شمارنده فارسی «۱ / ۴»');
check(str_contains($gal, 'data-gallery-lightbox-img="true"') && str_contains($gal, 'data-gallery-lightbox-close="true"')
    && str_contains($gal, 'data-gallery-lightbox-prev="true"') && str_contains($gal, 'data-gallery-lightbox-next="true"')
    && str_contains($gal, 'data-gallery-expand="true"'), 'گالری: هوک‌های lightbox و expand حفظ شده');
check(substr_count($gal, 'data-gallery-lightbox-dot="') === 4, 'گالری: چهار دات لایت‌باکس');
check(str_contains($gal, 'aria-label="تصویر 1"') && str_contains($gal, 'aria-label="تصویر 4"'), 'دات‌ها: aria-label با ارقام لاتین (عین میرور)');
check(str_contains($gal, 'لینیرلایت و والواشر</span>'), 'گالری: چیپ دسته روی تصویر اصلی');

/* ---------- ۲) مقایسه بایت‌دقیق گالری با برش میرور ---------- */
$mirror = (string) file_get_contents($root . '/uploads/static-mirror/product/wall-washer.html');
$g0 = strpos($mirror, '<div custom="0"><div data-gallery-root="true" class="space-y-3.5">');
$g1 = strpos($mirror, '<div custom="0.06">', $g0);
$gal_ref = substr($mirror, $g0, $g1 - $g0);
$U = 'https://test.example/wp-content/uploads/';
$gal_ref = str_replace('../images/light-element.jpg', $U . 'light-element.jpg', $gal_ref);
$gal_ref = str_replace('../images/pexels/pexels-35318900.jpg', $U . 'pexels-35318900.jpg', $gal_ref);
$gal_ref = str_replace('../images/pexels/pexels-36052966.jpg', $U . 'pexels-36052966.jpg', $gal_ref);
$gal_ref = str_replace('../images/pexels/pexels-29570545.jpg', $U . 'pexels-29570545.jpg', $gal_ref);
check($gal === $gal_ref, 'بایت‌دقیق: گالری = برش میرور (با جایگزینی URL)');

/* ---------- ۳) مقایسه بایت‌دقیق هیرو ---------- */
$h0 = strpos($mirror, '<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">');
$h1 = strpos($mirror, '</section>', $h0) + strlen('</section>');
$hero_ref = substr($mirror, $h0, $h1 - $h0);
$hero_ref = str_replace('href="../index.html"', 'href="https://test.example/"', $hero_ref);
$hero_ref = str_replace('href="../shop.html"', 'href="https://test.example/shop/"', $hero_ref);
$hero_ref = str_replace('href="../product_cat/wall-washer.html"', 'href="https://test.example/product_cat/wall-washer/"', $hero_ref);
check($hero === $hero_ref, 'بایت‌دقیق: هیرو = برش میرور (breadcrumb/H1/zireonvan)');
check(!str_contains($hero, 'dir="ltr"'), 'بدون english_titl → عنصر انگلیسی غایب');

/* ---------- ۴) english_titl وقتی ست شده ---------- */
$GLOBALS['__wp_post_meta'][901] = ['_thumbnail_id' => '71', 'english_titl' => 'RGB Wall Washer IP68', 'zireonvan' => 'زیرعنوان'];
$GLOBALS['__es_stub_titles'][901] = $TITLE;
ob_start();
es_get_template('template-parts/single-product/hero.php', ['pid' => 901]);
$hero_en = ob_get_clean();
check(str_contains($hero_en, '<p dir="ltr" class="mt-2 max-w-3xl font-mono text-[12.5px] font-bold tracking-wide text-slate-400">RGB Wall Washer IP68</p>'),
    'english_titl ست شده → عنصر انگلیسی dir="ltr" زیر H1');

/* ---------- ۵) fallback زنجیره‌ای ---------- */
$GLOBALS['__wp_post_meta'][902] = [];
$GLOBALS['__es_stub_titles'][902] = 'محصول بدون گالری';
ob_start();
es_get_template('template-parts/single-product/gallery.php', ['pid' => 902]);
$gal_empty = ob_get_clean();
check(str_contains($gal_empty, 'assets/images/light-tree.jpg'), 'بدون هیچ تصویر → placeholder میرور (light-tree.jpg)');
check(substr_count($gal_empty, 'data-gallery-thumb="') === 1, 'بدون گالری → یک thumb');

/* گالری بدون thumbnail اما با galerry_produ */
$GLOBALS['__wp_post_meta'][903] = ['galerry_produ' => '72'];
$GLOBALS['__es_stub_titles'][903] = 'فقط گالری';
ob_start();
es_get_template('template-parts/single-product/gallery.php', ['pid' => 903]);
$gal_g = ob_get_clean();
check(substr_count($gal_g, 'data-gallery-thumb="') === 1 && str_contains($gal_g, $U . 'pexels-35318900.jpg'),
    'بدون thumbnail + galerry_produ → تصویر گالری به‌عنوان اصلی');

/* منبع قدیمی image-2 */
$GLOBALS['__wp_post_meta'][904] = ['_thumbnail_id' => '71', 'image-2' => '73'];
$GLOBALS['__es_stub_titles'][904] = 'منبع قدیمی';
ob_start();
es_get_template('template-parts/single-product/gallery.php', ['pid' => 904]);
$gal_legacy = ob_get_clean();
check(substr_count($gal_legacy, 'data-gallery-thumb="') === 2, 'منبع قدیمی image-2 در زنجیره (۲ تصویر)');

/* ---------- ۶) wrapper single-product.php ---------- */
if (!function_exists('woocommerce_content')) {
    function woocommerce_content(): void {
    }
}
$GLOBALS['__es_theme_dir'] = $root . '/temp/phase-06-woocommerce-and-products/tests/fixtures/wrapper-shell';
$GLOBALS['__es_locate_override'] = [
    'template-parts/single-product/hero.php'     => $root . '/template-parts/single-product/hero.php',
    'template-parts/single-product/gallery.php'  => $root . '/template-parts/single-product/gallery.php',
    // ۶.۵: wrapper col-span-5 sticky حالا داخل رگیون purchase.php است
    'template-parts/single-product/purchase.php' => $root . '/template-parts/single-product/purchase.php',
];
ob_start();
include $root . '/single-product.php';
$sp = ob_get_clean();
$GLOBALS['__es_theme_dir'] = $root;
check(str_starts_with($sp, '<main class="bg-slate-50/50 pb-28 lg:pb-0">'), 'wrapper: main با کلاس میرور');
check(str_contains($sp, '<div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-10">'), 'wrapper: گرید ۱۲ستونه');
check(str_contains($sp, '<div class="space-y-4 lg:col-span-7">') && str_contains($sp, '<div class="lg:col-span-5 lg:sticky lg:top-28">'), 'wrapper: ستون گالری + ستون چسبان خرید');
check(str_ends_with(trim($sp), '</main>'), 'wrapper: بستن main');
check(str_contains($sp, 'data-gallery-main-img="true"') && str_contains($sp, 'aria-label="مسیر صفحه"'), 'wrapper: هیرو+گالری در خروجی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
