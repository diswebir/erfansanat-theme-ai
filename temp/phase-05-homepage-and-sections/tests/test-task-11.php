<?php
/**
 * Test 5.11 — کارت‌های استاندارد product/project/post + سکشن quality
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-11.php
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
echo "== Test 5.11: cards + quality ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Frontend\Icons;

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_post_types'][8146] = 'page';

// ═══ ۰) سکشن quality: بازسازی بایت‌دقیق ═══
ob_start();
es_render_sections(8146, ['only' => 'quality']);
$q = (string) ob_get_clean();
$refq = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/quality.html');
check($q === $refq . '>', 'quality: بازسازی بایت‌دقیق (۴ ویژگی + ۵ گام)');
if ($q !== $refq . '>') {
    $exp = $refq . '>';
    $n = min(strlen($q), strlen($exp));
    for ($i = 0; $i < $n; $i++) { if ($q[$i] !== $exp[$i]) break; }
    echo "    diff@$i (got=" . strlen($q) . " exp=" . strlen($exp) . "):\n    EXP: " . substr($exp, max(0, $i-70), 150) . "\n    GOT: " . substr($q, max(0, $i-70), 150) . "\n";
}

// ═══ ۱) کارت محصول — خرید آنلاین (فیکسچر کارت اول میرور) ═══
$p1 = (object) [
    'ID' => 501, 'post_type' => 'product',
    'post_title' => 'پوینت لایت پیکسل ۴ سانتی فول\u200cکالر',
    'post_excerpt' => 'پیکسل فول\u200cکالر با گواهی IP68 برای تونل\u200cهای نوری، گوی\u200cهای نورانی و نمای رسانه\u200cای ساختمان',
    'post_content' => '', 'post_date' => '2025-01-01 00:00:00',
];
$GLOBALS['__es_stub_permalinks'][501] = 'https://test.example/product/point-light-pixel/';
$GLOBALS['__wp_post_meta'][501] = [
    '_thumbnail_id' => 51, '_price' => '98000', '_regular_price' => '112000', '_sale_price' => '98000',
    '_sku' => 'ESE-PLP-040', 'true_sale' => 'true', 'es_min_qty' => '50',
    'number2-viewc' => '3840', '_wc_average_rating' => '4.95',
];
$GLOBALS['__es_stub_attachment_urls'][51] = ES_URI . 'assets/images/light-tunnel.jpg';
$GLOBALS['__es_stub_post_term_map'][501] = [
    'product_cat' => [(object) ['name' => 'پوینت لایت پیکسل LED']],
    'pa_ip' => [(object) ['name' => 'IP68']],
    'pa_voltage' => [(object) ['name' => '12V/24V DC']],
];
ob_start();
es_get_template('template-parts/cards/card-product.php', ['post' => $p1, 'custom' => '0.03']);
$c1 = (string) ob_get_clean();
check(str_contains($c1, 'data-product-item="true"') && str_contains($c1, 'data-product-purchasable="true"'), 'پوش اتریبیوت‌های فیلتر');
check(str_contains($c1, 'data-product-price="98000"') && str_contains($c1, 'data-product-views="3840"') && str_contains($c1, 'data-product-rating="4.95"'), 'داده‌های عددی خام (لاتین) برای JS');
check(str_contains($c1, 'data-product-ip="IP68"') && str_contains($c1, 'data-product-voltage="12V/24V DC"'), 'ip/voltage از تاکسونومی محصول');
check(str_contains($c1, '>پوینت لایت پیکسل LED</span>'), 'چیپ دسته');
check(str_contains($c1, '>🛒 خرید آنلاین</span>'), 'badge آنلاین');
check(str_contains($c1, '>ESE-PLP-040</span>'), 'sku');
check(str_contains($c1, '>۴.۹۵</span>') && str_contains($c1, '>۳۸۴۰</span>'), 'rating/views فارسی');
check(str_contains($c1, '<div class="text-[11px] font-semibold text-slate-400 line-through">۱۱۲,۰۰۰ تومان</div>'), 'قیمت قبلی خط‌خورده');
check(str_contains($c1, '<div class="text-[14px] font-black text-emerald-700">۹۸,۰۰۰ تومان</div>'), 'قیمت فروش');
check(str_contains($c1, '>حداقل: ۵۰</span>'), 'حداقل سفارش');
check(str_contains($c1, 'src="' . ES_URI . 'assets/images/light-tunnel.jpg"'), 'تصویر شاخص');
check(str_contains($c1, '<div custom="0.03">'), 'تأخیر ورود');

// ═══ ۲) کارت محصول — استعلامی + گالری fallback ═══
$p2 = (object) [
    'ID' => 502, 'post_type' => 'product',
    'post_title' => 'درخت نوری دلسا',
    'post_excerpt' => 'سازه مستحکم ۴۰۰ کیلوگرمی با ولتاژ ایمن ۲۴ ولت DC و ریسه\u200cهای سوزنی چیپ اپیستار',
    'post_content' => '', 'post_date' => '2025-01-01 00:00:00',
];
$GLOBALS['__es_stub_permalinks'][502] = 'https://test.example/product/delsa-light-tree/';
$GLOBALS['__wp_post_meta'][502] = [
    '_product_image_gallery' => '52', '_sku' => 'ESE-TR-DELSA', 'true_sale' => 'false',
    'number2-viewc' => '3150', '_wc_average_rating' => '4.93',
];
$GLOBALS['__es_stub_attachment_urls'][52] = ES_URI . 'assets/images/light-tree.jpg';
$GLOBALS['__es_stub_post_term_map'][502] = [
    'product_cat' => [(object) ['name' => 'درخت نوری']],
    'pa_ip' => [(object) ['name' => 'IP67']],
    'pa_voltage' => [(object) ['name' => '24V DC']],
];
ob_start();
es_get_template('template-parts/cards/card-product.php', ['post' => $p2, 'custom' => '0.13']);
$c2 = (string) ob_get_clean();
check(str_contains($c2, 'data-product-purchasable="false"') && str_contains($c2, 'data-product-price="0"'), 'پوش استعلامی');
check(str_contains($c2, '>استعلام پروژه</span>'), 'badge استعلام');
check(str_contains($c2, '>تولید سفارشی / استعلام قیمت</span>') && str_contains($c2, '>مشاوره فنی</span>'), 'فوتر استعلامی');
check(str_contains($c2, 'src="' . ES_URI . 'assets/images/light-tree.jpg"'), 'fallback اولین گالری');
check(!str_contains($c2, 'line-through'), 'بدون قیمت در حالت استعلام');

// ═══ ۳) بدون تصویر → placeholder میرور ═══
$p3 = (object) ['ID' => 503, 'post_type' => 'product', 'post_title' => 'تست بدون تصویر', 'post_excerpt' => '', 'post_content' => '', 'post_date' => '2025-01-01 00:00:00'];
$GLOBALS['__es_stub_permalinks'][503] = 'https://test.example/product/noimg/';
$GLOBALS['__wp_post_meta'][503] = ['true_sale' => 'true', '_price' => '1000'];
ob_start();
es_get_template('template-parts/cards/card-product.php', ['post' => $p3]);
$c3 = (string) ob_get_clean();
check(str_contains($c3, 'src="' . ES_URI . 'assets/images/light-tree.jpg"'), 'placeholder بدون تصویر');
check(!str_contains($c3, 'line-through'), 'بدون تخفیف → بدون خط قبلی');
check(str_contains($c3, 'data-product-category=""') || str_contains($c3, 'data-product-ip=""'), 'اتریبیوت‌های خالی بدون خطا');

// ═══ ۴) کارت پروژه ═══
$pr1 = (object) ['ID' => 601, 'post_type' => 'projects', 'post_title' => 'اجرای پروژه نیم هرم نوری اهواز', 'post_excerpt' => '', 'post_content' => '', 'post_date' => '2025-01-01 00:00:00'];
$GLOBALS['__es_stub_permalinks'][601] = 'https://test.example/projects/ahvaz-semi-pyramid-light/';
$GLOBALS['__wp_post_meta'][601] = ['finish-pro-time' => '۱۵ بهمن ۱۴۰۳', 'number2-viewc' => '1580', 'es_rating' => '4.95', '_thumbnail_id' => 61];
$GLOBALS['__es_stub_attachment_urls'][61] = ES_URI . 'assets/images/light-element.jpg';
$GLOBALS['__es_stub_post_term_map'][601] = [
    'project_type' => [(object) ['name' => 'المان\u200cهای نورانی'], (object) ['name' => 'نورپردازی شهری']],
    'city' => [(object) ['name' => 'اهواز']],
];
ob_start();
es_get_template('template-parts/cards/card-project.php', ['post' => $pr1, 'custom' => '0.04']);
$pc1 = (string) ob_get_clean();
check(str_contains($pc1, 'data-project-item="true"') && str_contains($pc1, 'data-project-type="المان\u200cهای نورانی"'), 'پوش پروژه');
check(str_contains($pc1, 'data-project-types="المان\u200cهای نورانی|نورپردازی شهری"'), 'همه انواع با جداکننده |');
check(str_contains($pc1, 'data-project-location="اهواز"'), 'شهر');
check(str_contains($pc1, '>المان\u200cهای نورانی</span>'), 'چیپ نوع');
check(str_contains($pc1, '>اهواز</span>'), 'نام شهر در فوتر');
check(str_contains($pc1, '>۱۵ بهمن ۱۴۰۳</span>'), 'تاریخ اتمام شمسی');
check(str_contains($pc1, '>۴.۹۵</span>') && str_contains($pc1, '>۱۵۸۰</span>'), 'rating/views پروژه');
check(str_contains($pc1, 'card-glow group relative block overflow-hidden'), 'کلاس کارت پروژه (card-glow)');

// ═══ ۵) کارت مقاله ═══
$b1 = (object) ['ID' => 701, 'post_type' => 'post', 'post_title' => 'آشنایی جامع با انواع تونل نوری و راهنمای انتخاب بهترین تونل نوری شهری', 'post_excerpt' => 'تونل نوری یکی از جذاب\u200cترین سازه\u200cهای نورپردازی شهری است.', 'post_content' => str_repeat('متن ', 500), 'post_date' => '2025-08-03 10:00:00'];
$GLOBALS['__es_stub_permalinks'][701] = 'https://test.example/blog/types-of-light-tunnel/';
$GLOBALS['__wp_post_meta'][701] = ['_thumbnail_id' => 71, 'es_read_time' => '9'];
$GLOBALS['__es_stub_attachment_urls'][71] = ES_URI . 'assets/images/light-tunnel.jpg';
$GLOBALS['__es_stub_post_term_map'][701] = ['category' => [(object) ['name' => 'مجله علمی']]];
$GLOBALS['__es_stub_post_dates'][701] = '۱۲ مرداد ۱۴۰۴';
ob_start();
es_get_template('template-parts/cards/card-post.php', ['post' => $b1, 'featured' => true, 'custom' => '0.05']);
$bc1 = (string) ob_get_clean();
check(str_contains($bc1, 'data-blog-item="true"') && str_contains($bc1, 'data-blog-category="مجله علمی"'), 'پوش مقاله');
check(str_contains($bc1, '>★ ویژه</span>'), 'نشان ویژه');
check(str_contains($bc1, '>۱۲ مرداد ۱۴۰۴</span>'), 'تاریخ فارسی');
check(str_contains($bc1, '>۹ دقیقه مطالعه</span>'), 'زمان مطالعه از متا');
check(str_contains($bc1, '>ادامه مطلب'), 'CTA ادامه مطلب');

// بدون ویژه + برآورد زمان مطالعه
$GLOBALS['__wp_post_meta'][701]['es_read_time'] = '';
ob_start();
es_get_template('template-parts/cards/card-post.php', ['post' => $b1]);
$bc2 = (string) ob_get_clean();
check(!str_contains($bc2, '★ ویژه'), 'بدون نشان ویژه');
check(str_contains($bc2, '>۳ دقیقه مطالعه</span>'), 'برآورد زمان (۲۵۰۰/۲۲۵→۱۲؟ سقف منطقی)');
check(str_contains($bc2, 'src="' . ES_URI . 'assets/images/light-tunnel.jpg"'), 'تصویر مقاله');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
