<?php
/**
 * Test 3.9 — گالری چند-منبعی (galerry_produ + fallbackها) + همگام‌سازی ووکامرس + کاتالوگ
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-09.php
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
echo "== Test 3.9: گالری و کاتالوگ محصول ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Data\ProductGallery;

// ---- normalize ----
check(ProductGallery::normalize('10, 20,30') === [10, 20, 30], 'normalize رشته کاما');
check(ProductGallery::normalize(['10', '40']) === [10, 40], 'normalize آرایه رشته‌ای');
check(ProductGallery::normalize('') === [], 'خالی → []');
check(ProductGallery::normalize('abc,-5') === [], 'مقادیر نامعتبر حذف');

// ---- get_ids: ترتیب اولویت منابع ----
$GLOBALS['__wp_post_meta'][80] = [
    'galerry_produ'          => '10,20',
    '_product_image_gallery' => '10,30',
    'gallery-filed'          => '40',
    'image-2'                => '50',
    'image-3'                => '60',
];
check(ProductGallery::get_ids(80) === [10, 20, 30, 40, 50, 60], 'ادغام ۴ منبع با dedup و حفظ ترتیب');

// اولویت: فقط منبع دوم موجود
$GLOBALS['__wp_post_meta'][81] = ['_product_image_gallery' => '77,88'];
check(ProductGallery::get_ids(81) === [77, 88], 'منبع ووکامرس به‌تنهایی');

// ---- sync: همگام‌سازی دوطرفه galerry_produ + گالری ووکامرس ----
ProductGallery::sync(80, [10, 20, 30]);
check(($GLOBALS['__wp_post_meta'][80]['galerry_produ'] ?? '') === '10,20,30', 'sync → galerry_produ رشته کاما');
check(($GLOBALS['__wp_post_meta'][80]['_product_image_gallery'] ?? '') === '10,20,30', 'sync → _product_image_gallery (ووکامرس)');

// ---- ذخیره از متاباکس ----
$GLOBALS['__es_theme_dir'] = $root;
require $root . '/functions.php';
$GLOBALS['__es_stub_post_types'][85] = 'product';
$GLOBALS['__wp_post_meta'][85] = [];
$post = new \stdClass();
$post->ID = 85;
$post->post_type = 'product';
$_POST = [
    'es_product_meta_nonce' => 'test-nonce-es_product_meta',
    'opt_meta' => [
        'galerry_produ'        => '100,200',
        'product-catalog-file' => 'https://erfansanat.com/cat.pdf',
    ],
];
\ErfanSanat\Theme\Admin\ProductMetaboxes::save(85, $post);
check(($GLOBALS['__wp_post_meta'][85]['galerry_produ'] ?? '') === '100,200', 'ذخیره galerry_produ از متاباکس');
check(($GLOBALS['__wp_post_meta'][85]['_product_image_gallery'] ?? '') === '100,200', 'همگام‌سازی خودکار گالری ووکامرس');
check(($GLOBALS['__wp_post_meta'][85]['product-catalog-file'] ?? '') === 'https://erfansanat.com/cat.pdf', 'ذخیره کاتالوگ (url)');

// آیدی عددی کاتالوگ → تبدیل به URL
$GLOBALS['__es_stub_attachment_urls'][300] = 'https://test.example/wp-content/uploads/catalog.pdf';
$_POST = [
    'es_product_meta_nonce' => 'test-nonce-es_product_meta',
    'opt_meta' => ['product-catalog-file' => '300'],
];
\ErfanSanat\Theme\Admin\ProductMetaboxes::save(85, $post);
check(($GLOBALS['__wp_post_meta'][85]['product-catalog-file'] ?? '') === 'https://test.example/wp-content/uploads/catalog.pdf', 'آیدی رسانه کاتالوگ → URL');

// URL مخرب → خالی
$_POST = [
    'es_product_meta_nonce' => 'test-nonce-es_product_meta',
    'opt_meta' => ['product-catalog-file' => 'javascript:evil()'],
];
\ErfanSanat\Theme\Admin\ProductMetaboxes::save(85, $post);
check(($GLOBALS['__wp_post_meta'][85]['product-catalog-file'] ?? 'x') === '', 'کاتالوگ مخرب → خالی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
