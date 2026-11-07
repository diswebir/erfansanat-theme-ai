<?php
/**
 * Test 6.9 — سبد خرید ووکامرس: cart.php میرور + فرم استاندارد + خلاصه/خالی/اعلان‌ها
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-09.php
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
echo "== Test 6.9: cart ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

/* ---------- استاب‌های ووکامرس (فقط همین تست) ---------- */
if (!function_exists('is_cart')) {
    function is_cart(): bool { return (bool) ($GLOBALS['__wp_is_cart'] ?? false); }
}
if (!function_exists('is_checkout')) {
    function is_checkout(): bool { return (bool) ($GLOBALS['__wp_is_checkout'] ?? false); }
}
if (!function_exists('is_account_page')) {
    function is_account_page(): bool { return (bool) ($GLOBALS['__wp_is_account'] ?? false); }
}
if (!function_exists('wc_get_cart_url')) {
    function wc_get_cart_url(): string { return 'https://test.example/cart/'; }
}
if (!function_exists('wc_get_checkout_url')) {
    function wc_get_checkout_url(): string { return 'https://test.example/checkout/'; }
}
if (!function_exists('wc_get_cart_remove_url')) {
    function wc_get_cart_remove_url(string|int $key): string { return 'https://test.example/cart/?remove_item=' . $key . '&_wpnonce=abc'; }
}
if (!function_exists('wc_add_notice')) {
    function wc_add_notice(string $m, string $t = 'notice'): void { $GLOBALS['__es_notices9'][$t][] = $m; }
}
if (!function_exists('wc_get_notices')) {
    function wc_get_notices(): array { return $GLOBALS['__es_notices9'] ?? []; }
}
if (!function_exists('wc_clear_notices')) {
    function wc_clear_notices(): void { $GLOBALS['__es_notices9'] = []; }
}
if (!function_exists('wc_get_product')) {
    function wc_get_product(int $id): ?object { return $GLOBALS['__es_products9'][$id] ?? null; }
}
if (!function_exists('wp_get_post_terms')) {
    function wp_get_post_terms(int $pid, string $tax): array {
        return $GLOBALS['__es_stub_post_term_map'][$pid][$tax] ?? [];
    }
}
if (!function_exists('add_query_arg')) {
    function add_query_arg(string $key, string $val, string $url): string {
        return $url . (str_contains($url, '?') ? '&' : '?') . $key . '=' . $val;
    }
}
if (!function_exists('wp_safe_redirect')) {
    function wp_safe_redirect(string $url): void { $GLOBALS['__es_redirect9'] = $url; }
}

class ES_FakeCart9 {
    public array $items = [];
    public bool $emptied = false;
    public function get_cart(): array { return $this->items; }
    public function get_cart_contents_count(): int { return count($this->items); }
    public function get_cross_sells(): array { return []; }
    public function get_cart_subtotal(): string { return '۰ تومان'; }
    public function get_total(): string { return '۰ تومان'; }
    public function get_product_price(object $p): string { return '۰ تومان'; }
    public function get_product_subtotal(object $p, int $q): string { return '۰ تومان'; }
    public function empty_cart(): void { $this->emptied = true; }
}

class ES_FakeProduct9 {
    public function __construct(private array $c) {}
    public function get_name(): string { return $this->c['name']; }
    public function get_slug(): string { return $this->c['slug']; }
    public function get_sku(): string { return $this->c['sku']; }
    public function get_price(): string { return '0'; }
    public function get_price_html(): string { return $this->c['price_html']; }
    public function get_min_purchase_quantity(): int { return $this->c['min']; }
    public function get_permalink(): string { return $this->c['url']; }
}

class ES_Woo9 {
    public object $cart;
    public function __construct() { $this->cart = new ES_FakeCart9(); }
}
function WC(): ES_Woo9 { return $GLOBALS['__es_wc9']; }
$GLOBALS['__es_wc9'] = new ES_Woo9();

/* ---------- فیکسچر (عین میرور cart.html) ---------- */
$GLOBALS['__es_wc9']->cart->items = [
    'AB12' => ['key' => 'AB12', 'product_id' => 201, 'quantity' => 6,
        'data' => new ES_FakeProduct9(['name' => 'ریسه سوزنی شهری ضد آب (IP68)', 'slug' => 'needle-string-lights', 'sku' => 'ESE-NSL-7101', 'min' => 10, 'url' => 'product/needle-string-lights.html', 'price_html' => '۰ تومان']),
        'es_variant' => 'آفتابی گرم · ۶ متری ۲۲۰ ولت'],
    'CD34' => ['key' => 'CD34', 'product_id' => 202, 'quantity' => 50,
        'data' => new ES_FakeProduct9(['name' => 'پوینت لایت پیکسل ۴ سانتی فول‌کالر', 'slug' => 'point-light-pixel', 'sku' => 'ESE-PLP-040', 'min' => 50, 'url' => 'product/point-light-pixel.html', 'price_html' => '۰ تومان']),
        'es_variant' => 'فول‌کالر هوشمند (RGB) · قاب PLT'],
];
$GLOBALS['__es_stub_post_term_map'] = [
    201 => ['product_cat' => [(object) ['name' => 'ریسه ال ای دی مدل سوزنی']], 'pa_ip-rating' => [(object) ['name' => 'IP68']]],
    202 => ['product_cat' => [(object) ['name' => 'پوینت لایت پیکسل LED']], 'pa_ip-rating' => [(object) ['name' => 'IP68']]],
];
$GLOBALS['__wp_post_meta'] = [
    201 => ['_thumbnail_id' => '31'],
    202 => ['_thumbnail_id' => '32'],
    203 => ['_thumbnail_id' => '41'],
    204 => ['_thumbnail_id' => '42'],
    205 => ['_thumbnail_id' => '43'],
];
$GLOBALS['__es_stub_attachment_urls'] = [
    31 => 'images/string-lights.jpg',
    32 => 'images/light-tunnel.jpg',
    41 => '/images/light-tunnel.jpg',
    42 => '/images/light-sphere.jpg',
    43 => '/images/string-lights.jpg',
];
$GLOBALS['__es_stub_posts'] = [
    (object) ['ID' => 203, 'post_type' => 'product'],
    (object) ['ID' => 204, 'post_type' => 'product'],
    (object) ['ID' => 205, 'post_type' => 'product'],
];
$GLOBALS['__es_stub_permalinks'] = [
    203 => 'product/point-light-pixel.html',
    204 => 'product/point-light-pixel-6cm.html',
    205 => 'product/needle-string-lights.html',
];
$GLOBALS['__es_stub_titles'] = [
    203 => 'پوینت لایت پیکسل ۴ سانتی فول‌کالر',
    204 => 'پوینت لایت پیکسل ۶ سانتی پرقدرت',
    205 => 'ریسه سوزنی شهری ضد آب (IP68)',
];
$GLOBALS['__es_products9'] = [
    203 => new ES_FakeProduct9(['name' => 'پوینت لایت پیکسل ۴ سانتی فول‌کالر', 'slug' => 'point-light-pixel', 'sku' => '', 'min' => 50, 'url' => 'product/point-light-pixel.html', 'price_html' => '۹۸,۰۰۰ تومان']),
    204 => new ES_FakeProduct9(['name' => 'پوینت لایت پیکسل ۶ سانتی پرقدرت', 'slug' => 'point-light-pixel-6cm', 'sku' => '', 'min' => 50, 'url' => 'product/point-light-pixel-6cm.html', 'price_html' => '۱۳۵,۰۰۰ تومان']),
    205 => new ES_FakeProduct9(['name' => 'ریسه سوزنی شهری ضد آب (IP68)', 'slug' => 'needle-string-lights', 'sku' => '', 'min' => 10, 'url' => 'product/needle-string-lights.html', 'price_html' => '۵۹۰,۰۰۰ تومان']),
];

add_filter('woocommerce_quantity_input_step', static function ($step, $product = null) {
    if (!is_object($product) || !method_exists($product, 'get_min_purchase_quantity')) {
        return $step;
    }
    return (int) $product->get_min_purchase_quantity() >= 50 ? 10 : 5; // فیکسچر میرور: سوزنی ۵ / پوینت ۱۰
}, 10, 2);

/* ---------- ساخت مرجع: میرور + سه انحراف مستند ---------- */
$M = (string) file_get_contents($root . '/uploads/static-mirror/cart.html');
$MAIN_OPEN = '<main class="bg-slate-50/50" data-cart-page="true">';
$i1 = strpos($M, $MAIN_OPEN) + strlen($MAIN_OPEN);
$row1 = strpos($M, '<div data-cart-row="true"');
$row2 = strpos($M, '<div data-cart-row="true"', $row1 + 200);
$i2 = strpos($M, '<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">', $i1);
$cont_close = strpos($M, '</div><div custom="0.08">', $row2) + strlen('</div>');
$row2_core = substr($M, $row2, $cont_close - strlen('</div>') - $row2);
$aside0 = strpos($M, '<aside class="lg:col-span-4', $cont_close);
$aside_end = strpos($M, '</aside>', $aside0) + strlen('</aside>');
$main_close = strpos($M, '</main>', $aside_end);

function es9_row_swaps(string $html, array $f): string {
    $b0 = strpos($html, '<button type="button" data-cart-row-remove="true"');
    $b1 = strpos($html, '>', $b0) + 1;
    $html = substr($html, 0, $b0) . '<a href="' . $f['remove'] . '"' . substr($html, $b0 + 21, $b1 - $b0 - 21) . substr($html, $b1);
    $b2 = strpos($html, '</button>', $b0);
    $html = substr($html, 0, $b2) . '</a>' . substr($html, $b2 + 9);
    $s0 = strpos($html, '<span data-cart-row-qty');
    $s1 = strpos($html, '</span>', $s0) + 7;
    $inp = '<input type="number" data-cart-row-qty="true" name="cart[' . $f['key'] . '][qty]" value="' . $f['qty'] . '" min="' . $f['min'] . '" max="" step="' . $f['step'] . '" inputmode="numeric" aria-label="تعداد" class="min-w-12 bg-transparent px-2 text-center font-mono text-sm font-black text-slate-900 tabular-nums outline-none"/>';
    return substr($html, 0, $s0) . $inp . substr($html, $s1);
}
function es9_head_swaps(string $html): string {
    $b0 = strpos($html, '<button type="button" data-cart-reset-default="true"');
    $b1 = strpos($html, '>', $b0) + 1;
    $html = substr($html, 0, $b0) . '<a href="' . add_query_arg('empty-cart', '1', wc_get_cart_url()) . '"' . substr($html, $b0 + 21, $b1 - $b0 - 21) . substr($html, $b1);
    $b2 = strpos($html, '</button>', $b0);
    $html = substr($html, 0, $b2) . '</a>' . substr($html, $b2 + 9);
    return $html . '<form action="' . wc_get_cart_url() . '" method="post" class="woocommerce-cart-form">';
}
function es9_qa_swaps(string $html, array $f): string {
    $b0 = strpos($html, '<button type="button" data-quick-add-cart="true"');
    $b1 = strpos($html, '>', $b0) + 1;
    $html = substr($html, 0, $b0) . '<a href="' . $f['add'] . '"' . substr($html, $b0 + 21, $b1 - $b0 - 21) . substr($html, $b1);
    $b2 = strpos($html, '</button>', $b0);
    return substr($html, 0, $b2) . '</a>' . substr($html, $b2 + 9);
}
$QA_MARK = '<div class="flex items-center justify-between gap-3 rounded-2xl border border-slate-100 bg-slate-50/70 p-3">';
$g_end = strpos($M, '">', strpos($M, '<div class="grid gap-3 s')) + 2;
$q_end = strpos($M, '</div></div></div></div></div>', $g_end) + strlen('</div>');
$qa_parts = explode($QA_MARK, substr($M, $g_end, $q_end - $g_end));
$qa_parts = array_slice($qa_parts, 1);
$UPD = '<button type="submit" name="update_cart" value="1" class="hidden" aria-label="بروزرسانی سبد خرید" tabindex="-1"></button>';
/* ---------- ۱) رندر کامل سبد پر ---------- */
ob_start();
include $root . '/woocommerce/cart/cart.php';
$out = (string) ob_get_clean();
// مرجع دقیق: هیرو + head+form + ردیف‌ها + بستن/دکمه + quickadd + aside + foot
$hero_ref = str_replace(['<a href="index.html"', '<a href="shop.html"'], ['<a href="https://test.example/"', '<a href="https://test.example/shop/"'], substr($M, $i1, $i2 - $i1));
$REF_FULL = $hero_ref
    . es9_head_swaps(substr($M, $i2, $row1 - $i2))
    . es9_row_swaps(substr($M, $row1, $row2 - $row1), ['key' => 'AB12', 'qty' => '6', 'min' => '10', 'step' => '5', 'remove' => str_replace('&', '&amp;', wc_get_cart_remove_url('AB12'))])
    . es9_row_swaps($row2_core, ['key' => 'CD34', 'qty' => '50', 'min' => '50', 'step' => '10', 'remove' => str_replace('&', '&amp;', wc_get_cart_remove_url('CD34'))])
    . '</div>' . '<input type="hidden" id="_wpnonce" name="_wpnonce" value="' . wp_create_nonce('woocommerce-cart') . '">' . $UPD . '</form>'
    . str_replace('<a href="shop.html"', '<a href="' . es_page_url('shop') . '"', substr($M, $cont_close, $g_end - $cont_close))
    . es9_qa_swaps($QA_MARK . $qa_parts[0], ['add' => wc_get_cart_url() . '?add-to-cart=203'])
    . es9_qa_swaps($QA_MARK . $qa_parts[1], ['add' => wc_get_cart_url() . '?add-to-cart=204'])
    . es9_qa_swaps($QA_MARK . $qa_parts[2], ['add' => wc_get_cart_url() . '?add-to-cart=205'])
    . substr($M, $q_end, $aside0 - $q_end)
    . str_replace('<a href="checkout.html"', '<a href="https://test.example/checkout/"', substr($M, $aside0, $aside_end - $aside0))
    . substr($M, $aside_end, $main_close - $aside_end);
check($out === $REF_FULL, 'بایت‌دقیق: رندر کامل سبد (۲ ردیف + quickadd + خلاصه) با سه انحراف مستند');
if ($out !== $REF_FULL && ($i ?? 0) >= 0) { }
if ($out !== $REF_FULL) {
    $n = min(strlen($out), strlen($REF_FULL));
    for ($i = 0; $i < $n; $i++) { if ($out[$i] !== $REF_FULL[$i]) break; }
    echo 'DBG2 @', $i, "\nREF: ", substr($REF_FULL, max(0,$i-70), 140), "\nOUT: ", substr($out, max(0,$i-70), 140), "\n";
}
if ($out !== $REF_FULL) {
    $n = min(strlen($out), strlen($REF_FULL));
    for ($i = 0; $i < $n; $i++) {
        if ($out[$i] !== $REF_FULL[$i]) { break; }
    }
    echo 'DBG len ', strlen($out), ' vs ', strlen($REF_FULL), ' firstdiff @', $i, "\n";
    echo 'REF: ', substr($REF_FULL, max(0, $i - 60), 120), "\n";
    echo 'OUT: ', substr($out, max(0, $i - 60), 120), "\n";
}

check(str_contains($out, '<span data-cart-page-count="true" class="rounded-lg bg-emerald-50 px-2.5 py-0.5 text-xs font-extrabold text-emerald-800">۲ کالا</span>'), 'شمارنده: ۲ کالا (ارقام فارسی)');
check(str_contains($out, 'name="cart[AB12][qty]"') && str_contains($out, 'name="cart[CD34][qty]"'), 'فرم استاندارد: name="cart[key][qty]"');
check(str_contains($out, 'name="update_cart" value="1"'), 'دکمه استاندارد update_cart');
check(str_contains($out, 'href="https://test.example/cart/?remove_item=AB12&amp;_wpnonce=abc"') && str_contains($out, 'data-cart-row-remove="true"'), 'حذف: لینک استاندارد + data-hook میرور');
check(str_contains($out, 'href="' . add_query_arg('empty-cart', '1', wc_get_cart_url()) . '" data-cart-reset-default="true"'), 'بازنشانی سبد: ?empty-cart=1 + data-hook');
check(str_contains($out, 'data-cart-subtotal="true"') && str_contains($out, 'data-cart-grand-total="true"'), 'خلاصه: subtotal/grand-total هوک‌های میرور');
check(str_contains($out, 'href="https://test.example/checkout/"') && str_contains($out, 'data-cart-slug="point-light-pixel"'), 'CTA checkout + ردیف با slug');
check(substr_count($out, 'data-quick-add-cart=') === 3, 'افزودن سریع: ۳ دکمه (count=' . substr_count($out, 'data-quick-add-cart=') . ')');
check(str_contains($out, 'add-to-cart=203') && str_contains($out, 'add-to-cart=204') && str_contains($out, 'add-to-cart=205'), 'افزودن سریع: لینک‌های add-to-cart سه محصول');
check(str_contains($out, 'data-product-img="/images/light-tunnel.jpg"') && str_contains($out, '۹۸,۰۰۰ تومان'), 'quickadd: تصویر/قیمت میرور');
check(str_contains($out, 'حداقل سفارش: ۱۰ · گام افزایش: مضرب ۵') && str_contains($out, 'حداقل سفارش: ۵۰ · گام افزایش: مضرب ۱۰'), 'چیپ min/step با ارقام فارسی');
check(str_contains($out, 'SKU: ESE-NSL-7101') && str_contains($out, '<span class="text-emerald-700">IP68</span>'), 'خط متا: SKU + IP');
check(str_contains($out, 'ویژگی انتخابی: <span class="text-slate-700">فول‌کالر هوشمند (RGB) · قاب PLT</span>'), 'ویژگی انتخابی ردیف دوم');

/* ---------- ۲) سبد خالی ---------- */
$GLOBALS['__es_wc9']->cart->items = [];
ob_start();
include $root . '/woocommerce/cart/cart.php';
$out_empty = (string) ob_get_clean();
check(str_contains($out_empty, 'data-cart-empty="true"') && !str_contains($out_empty, 'data-cart-row="true"'), 'سبد خالی: جعبه empty بدون ردیف');
check(str_contains($out_empty, 'href="' . es_page_url('shop') . '"') && str_contains($out_empty, 'بازگشت به فروشگاه محصولات'), 'سبد خالی: دکمه بازگشت به فروشگاه');

/* ---------- ۳) اعلان‌های ووکامرس ---------- */
$GLOBALS['__es_wc9']->cart->items = [];
$GLOBALS['__es_notices9'] = ['error' => ['سبد به‌روزرسانی نشد.']];
ob_start();
include $root . '/woocommerce/cart/cart.php';
$out_err = (string) ob_get_clean();
check(str_contains($out_err, 'woocommerce-error') && str_contains($out_err, 'border-rose-200 bg-rose-50') && str_contains($out_err, 'سبد به‌روزرسانی نشد.'), 'اعلان خطا با استایل میرور');
check(wc_get_notices() === [], 'اعلان پس از رندر پاک شد');

/* ---------- ۴) Setup: main_attrs + هندلر empty-cart ---------- */
require_once $root . '/inc/Woo/Setup.php';
$GLOBALS['__wp_is_cart'] = true; $GLOBALS['__wp_is_checkout'] = false; $GLOBALS['__wp_is_account'] = false;
check(\ErfanSanat\Theme\Woo\Setup::main_attrs() === ' class="bg-slate-50/50" data-cart-page="true"', 'Setup: main_attrs سبد عین میرور');
$GLOBALS['__wp_is_cart'] = false; $GLOBALS['__wp_is_checkout'] = true;
check(\ErfanSanat\Theme\Woo\Setup::main_attrs() === ' class="bg-slate-50/50" data-checkout-page="true"', 'Setup: main_attrs checkout عین میرور');
$GLOBALS['__wp_is_checkout'] = false; $GLOBALS['__wp_is_account'] = true;
check(\ErfanSanat\Theme\Woo\Setup::main_attrs() === ' class="bg-slate-50/50" data-account-page="true"', 'Setup: main_attrs حساب کاربری');
check(str_contains((string) file_get_contents($root . '/woocommerce.php'), 'Setup::main_attrs()'), 'wrapper: main_attrs در woocommerce.php');

// مسیرهای زودبازگشت هندلر (بدون خروج)
$GLOBALS['__wp_is_account'] = false; $GLOBALS['__wp_is_cart'] = false;
unset($_GET['empty-cart']);
\ErfanSanat\Theme\Woo\Setup::handle_empty_cart();
check(!($GLOBALS['__es_wc9']->cart->emptied), 'هندلر: خارج از سبد → کاری نمی‌کند');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
