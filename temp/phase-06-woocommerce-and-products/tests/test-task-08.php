<?php
/**
 * Test 6.8 — آرشیو دسته/تگ محصول: هیرو ترم، FAQ متا، topbar/empty مشترک، پارشال‌های شل
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-08.php
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
echo "== Test 6.8: taxonomy product_cat/tag ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

$mirror = (string) file_get_contents($root . '/uploads/static-mirror/product_cat/wall-washer.html');
$h0 = strpos($mirror, '<section class="relative overflow-hidden bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-36">');
$h1 = strpos($mirror, '<section id="archive"', $h0);
$HERO_REF = substr($mirror, $h0, $h1 - $h0);

/* ---------- فیکسچر ترم ۷۷ (والواشر) ---------- */
$GLOBALS['__es_stub_terms'] = [
    77 => (object) ['term_id' => 77, 'name' => 'لینیرلایت و والواشر LED نمای ساختمان', 'slug' => 'wall-washer',
        'taxonomy' => 'product_cat', 'description' => 'والواشرهای خطی پرقدرت و لینیر لایت‌های معماری با بدنه آلومینیوم اکسترود صنعتی، درجه حفاظت IP68، نمایش ۱۶ میلیون رنگ و لنزهای اپتیکی دقیق.', 'count' => 3],
    78 => (object) ['term_id' => 78, 'name' => 'ریسه و تزئینات نورپردازی شهری', 'slug' => 'strings', 'taxonomy' => 'product_cat', 'description' => '', 'count' => 2],
];
$GLOBALS['__wp_term_meta'] = [
    77 => ['thumbnail' => '91', 'faq' => [
        'item-0' => ['name_question' => 'نحوه استعلام قیمت و دریافت پیش‌فاکتور رسمی برای شهرداری‌ها چگونه است؟', 'answer_question' => 'از طریق تماس با شماره ۰۳۱-۹۱۰۹۱۰۱۱ یا ارسال مشخصات پروژه، در کمتر از ۲ ساعت کاری پیش‌فاکتور رسمی دریافت نمایید.'],
        'item-1' => ['name_question' => 'آیا امکان تولید سفارشی با ابعاد اختصاصی وجود دارد؟', 'answer_question' => 'بله؛ به‌عنوان تولیدکننده مستقیم، سفارشی‌سازی کامل متراژ و رنگ نور فراهم است.'],
    ]],
    78 => [],
];
$GLOBALS['__es_stub_attachment_urls'] = [91 => 'https://test.example/wp-content/uploads/cat-wall-washer.jpg'];
$GLOBALS['__wp_options']['erfansanat_theme_options'] = ['archives' => ['products_per_page' => '20']];

/* ---------- ۱) هیرو دسته بایت‌دقیق ---------- */
ob_start();
es_get_template('template-parts/archive/hero-cat.php', [
    'h1_a' => 'تولید و قیمت کارخانه ', 'h1_b' => 'لینیرلایت و والواشر LED نمای ساختمان',
    'desc' => 'والواشرهای خطی پرقدرت و لینیر لایت‌های معماری با بدنه آلومینیوم اکسترود صنعتی، درجه حفاظت IP68، نمایش ۱۶ میلیون رنگ و لنزهای اپتیکی دقیق.',
    'img'  => 'https://test.example/wp-content/uploads/cat-wall-washer.jpg',
    'label' => 'لینیرلایت و والواشر',
]);
$hero = ob_get_clean();
$ref_h = str_replace([
    'src="../images/light-element.jpg"',
    'href="../index.html"',
    'href="../shop.html"',
], [
    'src="https://test.example/wp-content/uploads/cat-wall-washer.jpg"',
    'href="https://test.example/"',
    'href="https://test.example/shop/"',
], $HERO_REF);
check($hero === $ref_h, 'بایت‌دقیق: هیرو دسته (H1 سئو/توضیح/تصویر ترم)');
check(str_contains($hero, '<a href="https://test.example/"') && str_contains($hero, '<a href="https://test.example/shop/"'), 'breadcrumb: خانه و فروشگاه لینک داینامیک');

/* ---------- ۲) FAQ دسته از متای ترم ---------- */
ob_start();
es_get_template('template-parts/archive/category-faq.php', [
    'label' => 'لینیرلایت و والواشر',
    'rows'  => es_get_category_faq(77),
]);
$faq = ob_get_clean();
check(str_contains($faq, '<div class="mt-10 border-t border-slate-100 pt-8" data-faq-list="true">'), 'FAQ: رپر میرور');
check(str_contains($faq, 'سوالات متداول درباره لینیرلایت و والواشر</h3>'), 'FAQ: عنوان با لیبل ترم');
check(str_contains($faq, 'data-faq-toggle="0"') && str_contains($faq, 'data-faq-toggle="1"'), 'FAQ: دو ردیف از متا');
check(str_contains($faq, '>نحوه استعلام قیمت و دریافت پیش‌فاکتور رسمی برای شهرداری‌ها چگونه است؟</span>'), 'FAQ: سوال اول از متا');
check(str_contains($faq, 'کمتر از ۲ ساعت کاری پیش‌فاکتور رسمی دریافت نمایید.</div>'), 'FAQ: جواب اول از متا (escape شده)');
check(str_contains($faq, 'transition-transform rotate-45 bg-emerald-800 text-white"'), 'FAQ: آیکون باز ردیف اول');
check(preg_match('/data-faq-icon="1" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg transition-transform bg-emerald-100 text-emerald-800"/', $faq) === 1, 'FAQ: آیکون بسته ردیف دوم (کلاس میرور cat)');
check(str_contains($faq, 'data-faq-body="0" class="block border-t') && str_contains($faq, 'data-faq-body="1" class="hidden border-t'), 'FAQ: بدنه اول باز/دوم بسته');

/* ---------- ۳) FAQ خالی → مخفی ---------- */
ob_start();
es_get_template('template-parts/archive/category-faq.php', ['label' => 'ریسه', 'rows' => es_get_category_faq(78)]);
$faq_empty = ob_get_clean();
check($faq_empty === '', 'FAQ: بدون متا → مخفی');

/* ---------- ۴) topbar مشترک (لیبل ترم + sort فعال) ---------- */
ob_start();
es_get_template('template-parts/archive/topbar.php', ['total' => 3, 'label' => 'لینیرلایت و والواشر LED نمای ساختمان', 'sort' => 'popular']);
$top = ob_get_clean();
$SHOPM = (string) file_get_contents($root . '/uploads/static-mirror/shop.html');
$t0 = strpos($SHOPM, '<div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3.5 shadow-2xs sm:px-5">');
$t1 = strpos($SHOPM, '<div data-product-grid="true"', $t0);
$TOP_REF = substr($SHOPM, $t0, $t1 - $t0);
$TOP_REF = str_replace('<span data-product-count="true">۲۰</span> محصول', '<span data-product-count="true">۳</span> محصول', $TOP_REF);
$TOP_REF = str_replace('<span class="text-[13.5px] font-black text-slate-900">همه محصولات تولیدی عرفان صنعت</span>',
    '<span class="text-[13.5px] font-black text-slate-900">لینیرلایت و والواشر LED نمای ساختمان</span>', $TOP_REF);
$TOP_REF = str_replace('<option value="popular">پربازدیدترین</option>',
    '<option value="popular" selected="">پربازدیدترین</option>', $TOP_REF);
check($top === $TOP_REF, 'بایت‌دقیق: topbar مشترک (شمارنده/لیبل ترم/sort انتخابی)');

/* ---------- ۵) empty مشترک ---------- */
ob_start();
es_get_template('template-parts/archive/product-empty.php', ['show' => true]);
$empty_on = ob_get_clean();
ob_start();
es_get_template('template-parts/archive/product-empty.php', ['show' => false]);
$empty_off = ob_get_clean();
$e0 = strpos($SHOPM, '<div data-product-empty="true"');
$e1 = strpos($SHOPM, '<div class="fixed bottom-5 left-1/2 z-40 -translate-x-1/2 lg:hidden">', $e0);
$EMPTY_REF = substr($SHOPM, $e0, $e1 - $e0);
$EMPTY_REF = str_replace('href="shop.html"', 'href="https://test.example/shop/"', $EMPTY_REF);
$EMPTY_REF = str_replace('class="hidden rounded-2xl', 'class="block rounded-2xl', $EMPTY_REF);
check($empty_on === $EMPTY_REF, 'بایت‌دقیق: جعبه حالت خالی (نمایش)');
check(str_contains($empty_off, 'data-product-empty="true" class="hidden rounded-2xl'), 'حالت خالی: hidden وقتی محصول هست');

/* ---------- ۶) شل taxonomy-product_cat: ثبت‌های ساختاری ---------- */
$cat_shell = (string) file_get_contents($root . '/taxonomy-product_cat.php');
check(str_contains($cat_shell, "es_get_template('template-parts/archive/hero-cat.php'"), 'شل cat: هیرو اختصاصی');
check(str_contains($cat_shell, "es_get_template('template-parts/archive/product-filters.php'"), 'شل cat: سایدبار فیلترهای ۶.۷');
check(str_contains($cat_shell, "es_get_template('template-parts/archive/topbar.php'") && str_contains($cat_shell, "es_get_template('template-parts/archive/pagination.php'"), 'شل cat: topbar/صفحه‌بندی مشترک');
check(str_contains($cat_shell, 'es_get_category_faq($es_tid)'), 'شل cat: FAQ از متای ترم');
check(str_contains($cat_shell, 'es_get_category_thumb($es_tid)'), 'شل cat: تصویر هیرو از متای ترم');
check(str_contains($cat_shell, "get_template_part('archive-product')"), 'شل cat: بدون ترم → fallback به shop');
check(str_contains($cat_shell, '\'base_url\' => (string) get_term_link($es_term)'), 'شل cat: صفحه‌بندی با URL ترم');
check(substr_count($cat_shell, '<section') === 1 && str_contains($cat_shell, '<section id="archive"'), 'شل cat: archive-open تک (دوبله‌سکشن ممنوع)');
check(str_contains($cat_shell, 'esc_attr($es_label)'), 'شل cat: لیبل ترم داینامیک در archive-open');
check(!str_contains($cat_shell, 'pb-16 lg:pb-24'), 'شل cat: سکشن ساختگی shop حذف شده');
check(str_contains($cat_shell, 'mt-16 lg:mt-20'), 'شل cat: رپر بیرونی FAQ');
$hero_part = (string) file_get_contents($root . '/template-parts/archive/hero-cat.php');
check(!str_contains($hero_part, 'section id="archive"'), 'پارشیال hero: بدون archive-open (مرز تمیز)');

$tag_shell = (string) file_get_contents($root . '/taxonomy-product_tag.php');
check(str_contains($tag_shell, "es_get_template('template-parts/cards/card-product.php'") && str_contains($tag_shell, 'pagination.php'), 'شل tag: گرید + صفحه‌بندی');
check(!str_contains($tag_shell, 'product-filters.php') && !str_contains($tag_shell, 'category-faq.php'), 'شل tag: بدون سایدبار/FAQ (ساده)');

/* ---------- ۷) breadcrumb هیرو (خانه → دسته) ---------- */
check(str_contains($HERO_REF, 'aria-label="مسیر صفحه"') && str_contains($HERO_REF, 'href="../index.html"'), 'breadcrumb: مسیر صفحه در هیرو میرور');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
