<?php
/**
 * Test 3.13 — HreflangMeta: متاباکس پست + فرم ترم + خروجی head با x-default و dedupe
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-13.php
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
echo "== Test 3.13: HreflangMeta ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
$GLOBALS['__wp_current_user_can'] = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\HreflangMeta;

// ---- ثبت متاباکس روی همه پست‌تایپ‌ها ----
do_action('add_meta_boxes');
$hb_screens = (array) ($GLOBALS['__wp_hooks']['meta_boxes']['es-hreflang-meta']['screens'] ?? [$GLOBALS['__wp_hooks']['meta_boxes']['es-hreflang-meta']['screen'] ?? '']);
foreach (['post', 'page', 'product', 'projects', 'services'] as $pt) {
    check(in_array($pt, $hb_screens, true), "متاباکس روی {$pt}");
}

// فرم ترم: هوک‌های سه تاکسونومی
$hooks = $GLOBALS['__wp_hooks']['actions'];
foreach (['product_cat', 'city', 'project_type'] as $tax) {
    check(isset($hooks["{$tax}_edit_form_fields"]) || isset($hooks["{$tax}_add_form_fields"]), "فرم ترم {$tax} ثبت شد");
    check(isset($hooks["edited_{$tax}"]) || isset($hooks["created_{$tax}"]), "ذخیره ترم {$tax} ثبت شد");
}

// ---- رندر متاباکس: ۴ فیلد با کلیدهای دقیق ----
$post = new \stdClass();
$post->ID = 200;
$post->post_type = 'product';
$GLOBALS['__es_stub_post_types'][200] = 'product';
ob_start();
HreflangMeta::render($post);
$ui = (string) ob_get_clean();
foreach (['hrefmeta_fa', 'hrefmeta_en', 'hrefmeta_ar', 'same_key_hrefl'] as $key) {
    check(str_contains($ui, 'name="opt_href[' . $key . ']"'), "فیلد {$key}");
}

// ---- ذخیره پست: نانس + esc_url ----
$_POST = [
    'es_hreflang_meta_nonce' => 'test-nonce-es_hreflang_meta',
    'opt_href' => [
        'hrefmeta_fa'    => 'https://erfansanat.com/product/x/',
        'hrefmeta_en'    => 'https://erfansanat.com/en/product/x/',
        'hrefmeta_ar'    => 'javascript:alert(1)',
        'same_key_hrefl' => 'https://erfansanat.com/product/x/',
        'evil'           => 'nope',
    ],
];
$GLOBALS['__es_stub_post_types'][200] = 'product';
HreflangMeta::save(200, $post);
check(($GLOBALS['__wp_post_meta'][200]['hrefmeta_fa'] ?? '') === 'https://erfansanat.com/product/x/', 'ذخیره fa');
check(($GLOBALS['__wp_post_meta'][200]['hrefmeta_ar'] ?? '') === '' || str_starts_with((string) ($GLOBALS['__wp_post_meta'][200]['hrefmeta_ar'] ?? ''), 'http'), 'URL نامعتبر خنثی (esc_url_raw)');
check(!isset($GLOBALS['__wp_post_meta'][200]['evil']), 'کلید خارج لیست ذخیره نمی‌شود');

// بدون نانس → رد
$GLOBALS['__wp_post_meta'][200] = [];
$_POST = ['opt_href' => ['hrefmeta_fa' => 'https://x.com/fa/']];
HreflangMeta::save(200, $post);
check(empty($GLOBALS['__wp_post_meta'][200]), 'بدون نانس ذخیره نمی‌شود');

// ---- ذخیره ترم ----
$_POST = [
    'opt_href' => ['hrefmeta_fa' => 'https://erfansanat.com/product-cat/pump/'],
];
$GLOBALS['__wp_term_meta'] = [];
HreflangMeta::save_term(77);
check(($GLOBALS['__wp_term_meta'][77]['hrefmeta_fa'] ?? '') === 'https://erfansanat.com/product-cat/pump/', 'ذخیره متای ترم product_cat');

// ---- خروجی head پست: fa/en + x-default از same_key_hrefl ----
$GLOBALS['__wp_post_meta'][200] = [
    'hrefmeta_fa'    => 'https://erfansanat.com/product/x/',
    'hrefmeta_en'    => 'https://erfansanat.com/en/product/x/',
    'hrefmeta_ar'    => '',
    'same_key_hrefl' => 'https://erfansanat.com/product/x/',
];
$GLOBALS['__wp_is_admin'] = false;
$GLOBALS['__es_stub_current_post_type'] = 'product';
$GLOBALS['__es_stub_queried_object_id'] = 200;
HreflangMeta::render_head(); // دوبار برای dedupe
ob_start();
HreflangMeta::render_head();
$head = (string) ob_get_clean();
check(trim($head) === '', 'dedupe: بار دوم چیزی چاپ نمی‌شود');
$links_for = HreflangMeta::current_links();
check(($links_for['fa'] ?? '') === 'https://erfansanat.com/product/x/', 'لینک fa');
check(($links_for['en'] ?? '') === 'https://erfansanat.com/en/product/x/', 'لینک en');
check(!isset($links_for['ar']), 'بدون مقدار ar → بدون لینک ar');
check(($links_for['x-default'] ?? '') === 'https://erfansanat.com/product/x/', 'x-default از same_key_hrefl');

// x-default fallback به fa
$GLOBALS['__wp_hooks']['removed_filters'] ??= [];
\ErfanSanat\Theme\Admin\HreflangMeta::class; // no-op
$GLOBALS['__wp_post_meta'][201] = ['hrefmeta_fa' => 'https://erfansanat.com/product/y/'];
$GLOBALS['__es_stub_queried_object_id'] = 201;
$links2 = HreflangMeta::current_links();
check(($links2['x-default'] ?? '') === 'https://erfansanat.com/product/y/', 'x-default fallback → fa');

// بدون متا → خروجی خالی
$GLOBALS['__wp_post_meta'][202] = [];
$GLOBALS['__es_stub_queried_object_id'] = 202;
check(HreflangMeta::current_links() === [], 'بدون متا → بدون خروجی');

// ---- خروجی ترم ----
$GLOBALS['__es_stub_current_post_type'] = '';
$GLOBALS['__wp_term_meta'][77]['hrefmeta_en'] = 'https://erfansanat.com/en/product-cat/pump/';
$GLOBALS['__es_stub_queried_object'] = (object) ['term_id' => 77];
$GLOBALS['__es_stub_is_tax'] = true;
$links3 = HreflangMeta::current_links();
check(($links3['en'] ?? '') === 'https://erfansanat.com/en/product-cat/pump/', 'لینک ترم از متای ترم');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
