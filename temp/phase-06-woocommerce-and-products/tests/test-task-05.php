<?php
/**
 * Test 6.5 — ناحیه خرید (بایت‌دقیق آنلاین) + حالت استعلامی + PurchaseFlow
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-05.php
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
echo "== Test 6.5: single-product purchase + PurchaseFlow ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

/* ---------- مرجع میرور ---------- */
$mirror = (string) file_get_contents($root . '/uploads/static-mirror/product/wall-washer.html');
$rg0 = strpos($mirror, '<div class="lg:col-span-5 lg:sticky lg:top-28">');
$rg1 = strpos($mirror, '</section>', $rg0);
$REGION_REF = substr($mirror, $rg0, $rg1 - $rg0);

/* ---------- فیکسچر: محصول آنلاین مطابق میرور ---------- */
$TITLE = 'والواشر RGB و تک‌رنگ صنعتی (IP68)';
$GLOBALS['__es_stub_titles'] = [900 => $TITLE];
$GLOBALS['__es_stub_posts'] = [(object) ['ID' => 900, 'post_name' => 'wall-washer', 'post_content' => 'x']];
$GLOBALS['__wp_post_meta'] = [
    900 => [
        '_price'            => '2890000',
        '_regular_price'    => '3250000',
        '_sku'              => 'ESE-WW-036',
        '_wc_average_rating' => '4.95',
        '_thumbnail_id'     => '71',
        'true_sale'         => 'true',
        'es_min_qty'        => '4',
        'es_step_qty'       => '2',
        '_product_attributes' => [
            'pa_led'    => ['label' => 'نوع LED', 'is_taxonomy' => 0, 'value' => 'پیکسل RGB پرقدرت فول‌کالر / پاور LED'],
            'pa_color'  => ['label' => 'تنوع رنگ', 'is_taxonomy' => 0, 'value' => 'بیش از ۱۶ میلیون رنگ و تک‌رنگ'],
            'pa_energy' => ['label' => 'مصرف انرژی', 'is_taxonomy' => 0, 'value' => '۳۶ وات در هر متر'],
        ],
    ],
];
$GLOBALS['__es_stub_post_term_map'] = [
    900 => ['product_cat' => [(object) ['name' => 'لینیرلایت و والواشر', 'slug' => 'wall-washer', 'taxonomy' => 'product_cat']]],
];
$GLOBALS['__es_stub_attachment_urls'] = [71 => 'https://test.example/wp-content/uploads/light-element.jpg'];
$GLOBALS['__es_stub_wc_page_ids'] = ['cart' => 11];
$GLOBALS['__es_stub_permalinks'] = [11 => 'https://test.example/cart/'];

/* ---------- ۱) بایت‌دقیق: ناحیه خرید آنلاین ---------- */
ob_start();
es_get_template('template-parts/single-product/purchase.php', ['pid' => 900]);
$purchase = ob_get_clean();
$ref = str_replace('href="../cart.html"', 'href="https://test.example/cart/"', $REGION_REF);
$ref = str_replace('href="tel:031-91091011"', 'href="tel:03191091011"', $ref);
$ref = str_replace('data-product-img="/images/light-element.jpg" data-product-image="/images/light-element.jpg"',
    'data-product-img="https://test.example/wp-content/uploads/light-element.jpg" data-product-image="https://test.example/wp-content/uploads/light-element.jpg"', $ref);
$ref = str_replace('data-product-price="0"', 'data-product-price="2890000"', $ref);
check($purchase === $ref, 'بایت‌دقیق: ناحیه خرید آنلاین = برش میرور (قیمت/حداقل/امتیاز/SKU/toast)');

/* ---------- ۲) حالت استعلامی ---------- */
$GLOBALS['__wp_post_meta'][901] = [
    'true_sale' => 'false',
    '_sku'      => 'ESE-Q-001',
    '_thumbnail_id' => '71',
    '_price'    => '',
];
$GLOBALS['__es_stub_titles'][901] = 'محصول استعلامی';
$GLOBALS['__es_stub_posts'][] = (object) ['ID' => 901, 'post_name' => 'inquiry-product', 'post_content' => ''];
ob_start();
es_get_template('template-parts/single-product/purchase.php', ['pid' => 901]);
$inq = ob_get_clean();
check(str_contains($inq, '>استعلامی</span>'), 'استعلامی: قیمت → «استعلامی»');
check(str_contains($inq, 'استعلام قیمت و دریافت مشاوره'), 'استعلامی: متن دکمه تغییر کرد');
check(str_contains($inq, 'data-pdp-inquiry="true"') && !str_contains($inq, 'data-pdp-add-to-cart="true"'), 'استعلامی: دکمه خرید → لینک استعلام');
check(!str_contains($inq, 'line-through') && !str_contains($inq, 'حداقل سفارش'), 'استعلامی: قیمت خط‌خورده و چیپ حداقل حذف');
check(str_contains($inq, 'استعلام قیمت و مشاوره'), 'استعلامی: متن وضعیت هدر');

/* ---------- ۳) true_sale=true ولی بدون قیمت → استعلامی ---------- */
$GLOBALS['__wp_post_meta'][902] = ['true_sale' => 'true', '_price' => '', '_sku' => 'X1', '_thumbnail_id' => '71'];
$GLOBALS['__es_stub_titles'][902] = 'بی‌قیمت';
$GLOBALS['__es_stub_posts'][] = (object) ['ID' => 902, 'post_name' => 'no-price', 'post_content' => ''];
ob_start();
es_get_template('template-parts/single-product/purchase.php', ['pid' => 902]);
$nop = ob_get_clean();
check(str_contains($nop, '>استعلامی</span>'), 'true_sale=true ولی قیمت خالی → نمایش استعلامی');
check(str_contains($nop, 'data-pdp-add-to-cart="true"'), 'بی‌قیمت: دکمه استاندارد حفظ شد (فقط قیمت «استعلامی»)');

/* ---------- ۴) فیلترهای PurchaseFlow ---------- */
$prod_on  = new stdClass();
$prod_on->ID = 900;
$prod_off = new stdClass();
$prod_off->ID = 901;
$pf = \ErfanSanat\Theme\Woo\PurchaseFlow::class;
check($pf::is_purchasable(true, $prod_on) === true, 'فیلتر: محصول آنلاین purchasable می‌ماند');
check($pf::is_purchasable(true, $prod_off) === false, 'فیلتر: true_sale=false → purchasable=false');
check($pf::button_text('خرید', $prod_off) === 'استعلام قیمت', 'فیلتر: متن دکمه استعلامی → «استعلام قیمت»');
check($pf::button_text('خرید', $prod_on) === 'خرید', 'فیلتر: متن دکمه آنلاین دست‌نخورده');

/* ---------- ۵) ثبت فیلترها در بوت ---------- */
$boot_filters = $GLOBALS['__wp_hooks']['filters'] ?? [];
check(isset($boot_filters['woocommerce_is_purchasable']), 'بوت: فیلتر woocommerce_is_purchasable ثبت شد');
check(isset($boot_filters['woocommerce_product_add_to_cart_text']), 'بوت: فیلتر متن دکمه ثبت شد');

/* ---------- ۶) فیلتر es_product_purchase_args ---------- */
add_filter('es_product_purchase_args', static function (array $data, int $pid): array {
    if ($pid === 910) {
        $data['status'] = 'وضعیت سفارشی';
    }
    return $data;
}, 10, 2);
$GLOBALS['__wp_post_meta'][910] = $GLOBALS['__wp_post_meta'][900];
$GLOBALS['__es_stub_titles'][910] = $TITLE;
$GLOBALS['__es_stub_posts'][] = (object) ['ID' => 910, 'post_name' => 'wall-washer', 'post_content' => 'x'];
ob_start();
es_get_template('template-parts/single-product/purchase.php', ['pid' => 910]);
$fx = ob_get_clean();
check(str_contains($fx, 'وضعیت سفارشی'), 'فیلتر es_product_purchase_args: وضعیت سفارشی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
