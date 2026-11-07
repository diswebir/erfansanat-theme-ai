<?php
/**
 * Test 6.10 — checkout + thank-you: فرم میرور با فیلدهای استاندارد + toggle خریدار + متا + رسید
 * اجرا: /home/user/tools/php -f temp/phase-06-woocommerce-and-products/tests/test-task-10.php
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
echo "== Test 6.10: checkout ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

if (!function_exists('is_wc_endpoint_url')) {
    function is_wc_endpoint_url(string $endpoint = ''): bool { return ($GLOBALS['__es_endpoint10'] ?? '') === $endpoint; }
}
if (!function_exists('wc_get_checkout_url')) {
    function wc_get_checkout_url(): string { return 'https://test.example/checkout/'; }
}
if (!function_exists('wc_get_order')) {
    function wc_get_order(int $id): ?object { return $GLOBALS['__es_order10'][$id] ?? null; }
}
if (!function_exists('wc_add_notice')) {
    function wc_add_notice(string $m, string $t = 'notice'): void {}
}
if (!function_exists('wp_unslash')) {
    function wp_unslash(mixed $v): mixed { return $v; }
}
if (!function_exists('woocommerce_checkout_payment')) {
    function woocommerce_checkout_payment(): void { echo '<div class="payment_box">gateway-fields</div>'; }
}
if (!function_exists('wc_get_template')) {
    function wc_get_template(string $t, array $args = []): void {}
}
if (!function_exists('wc_print_notices')) {
    function wc_print_notices(): void {}
}

class ES_Cart10 {
    public function get_cart(): array { return $GLOBALS['__es_cart10_items'] ?? []; }
    public function get_total(): string { return '۵۹۰,۰۰۰ تومان'; }
    public function get_product_subtotal(object $p, int $q): string { return '۵۹۰,۰۰۰ تومان'; }
    public function get_cross_sells(): array { return []; }
    public function get_cart_contents_count(): int { return 1; }
    public function get_cart_subtotal(): string { return '۵۹۰,۰۰۰ تومان'; }
    public function get_product_price(object $p): string { return '۵۹۰,۰۰۰ تومان'; }
}
class ES_Prod10 {
    public function __construct(private string $n) {}
    public function get_name(): string { return $this->n; }
}
class ES_Checkout10 {
    public function get_value(string $k): string { return $GLOBALS['__es_checkout_vals10'][$k] ?? ''; }
}
class ES_GW10 {
    public function get_available_payment_gateways(): array { return ['mlt' => (object) ['id' => 'mlt'], 'invoice' => (object) ['id' => 'invoice']]; }
}
class ES_WC10 {
    public object $cart;
    private object $co;
    private object $gateways;
    public function __construct() {
        $this->cart = new ES_Cart10();
        $this->co = new ES_Checkout10();
        $this->gateways = new ES_GW10();
    }
    public function checkout(): ES_Checkout10 { return $this->co; }
    public function payment_gateways(): ES_GW10 { return $this->gateways; }
}
function WC(): ES_WC10 { return $GLOBALS['__es_wc10']; }
$GLOBALS['__es_wc10'] = new ES_WC10();
$GLOBALS['__es_cart10_items'] = [
    'K1' => ['key' => 'K1', 'product_id' => 201, 'quantity' => 6, 'data' => new ES_Prod10('ریسه سوزنی شهری ضد آب (IP68)')],
];
$GLOBALS['__es_checkout_vals10'] = ['billing_first_name' => 'مهندس رضایی'];

/* سفارش thank-you */
class ES_Order10 {
    public array $meta = [];
    public function get_order_number(): string { return '1404'; }
    public function get_billing_first_name(): string { return 'مهندس'; }
    public function get_billing_last_name(): string { return 'رضایی'; }
    public function get_payment_method_title(): string { return 'پرداخت آنلاین بانک ملت'; }
    public function update_meta_data(string $k, mixed $v): void { $this->meta[$k] = $v; }
}
$GLOBALS['__es_order10'] = [77 => new ES_Order10()];

class FakeWP10 {
    public array $query_vars = [];
}

/* ---------- ۱) رندر فرم checkout ---------- */
$GLOBALS['__es_endpoint10'] = '';
$GLOBALS['wp'] = new FakeWP10();
ob_start();
include $root . '/woocommerce/checkout/form-checkout.php';
$form = (string) ob_get_clean();

$M = (string) file_get_contents($root . '/uploads/static-mirror/checkout.html');
$m0 = strpos($M, '<main class="bg-slate-50/50" data-checkout-page="true">') + strlen('<main class="bg-slate-50/50" data-checkout-page="true">');
$hero0 = strpos($M, '<section class="relative overflow-h', $m0);
$hero1 = strpos($M, '<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">', $hero0);
$form0 = strpos($M, '<form data-checkout-form="true"', $hero1);
$form_close = strpos($M, '</form></section>', $form0) + strlen('</form></section>');

$hero_ref = str_replace(['<a href="index.html"', '<a href="shop.html"'], ['<a href="https://test.example/"', '<a href="https://test.example/shop/"'], substr($M, $hero0, $hero1 - $hero0));
check(str_starts_with($form, $hero_ref), 'بایت‌دقیق: هیرو checkout (breadcrumb داینامیک)');

$FORM_OPEN_REF = '<form name="checkout" method="post" action="https://test.example/checkout/" class="checkout woocommerce-checkout grid items-start gap-8 lg:grid-cols-12" novalidate>';
check(str_contains($form, $FORM_OPEN_REF), 'فرم استاندارد: name/action/class WC');
check(str_contains($form, 'name="buyer_type" checked="" value="حقیقی / پیمانکار"') && str_contains($form, 'name="buyer_type" value="حقوقی / شهرداری و سازمان دولتی"'), 'toggle خریدار: radioهای buyer_type میرور (حقیقی/حقوقی)');
check(str_contains($form, 'name="billing_first_name"') && str_contains($form, 'value="مهندس رضایی"'), 'فیلد نام: billing_first_name استاندارد + value از checkout');
check(str_contains($form, 'name="billing_last_name"'), 'فیلد اجباری billing_last_name (انحراف مستند)');
check(str_contains($form, 'name="billing_phone"') && str_contains($form, 'dir="ltr"') && str_contains($form, 'placeholder="0912..."'), 'موبایل: billing_phone با مارک‌آپ میرور');
$es_tax_pos = (int) strpos($form, 'name="billing_es_tax_id"');
$es_tax_input = substr($form, max(0, $es_tax_pos - 420), 420);
check(str_contains($form, 'name="billing_es_tax_id"') && !str_contains($es_tax_input, ' required'), 'شناسه ملی: billing_es_tax_id اختیاری');
check(str_contains($form, 'name="billing_address_1"') && str_contains($form, 'name="billing_city"'), 'نشانی/شهر: billing استاندارد');
check(str_contains($form, 'name="order_comments"'), 'توضیحات: order_comments استاندارد');
check(str_contains($form, 'name="payment_method" checked="" value="mlt"') && str_contains($form, 'name="payment_method" value="invoice"'), 'payment: اسلاگ درگاه واقعی (mlt/invoice)');
check(str_contains($form, '<div class="payment_box">gateway-fields</div>'), 'payment: بلوک استاندارد درگاه رندر شد');
check(str_contains($form, 'href="' . es_page_url('shop') . '"') && str_contains($form, '<span data-checkout-total="true"'), 'aside: بازگشت به فروشگاه + جمع داینامیک');
check(str_contains($form, '۵۹۰,۰۰۰ تومان') && str_contains($form, 'ریسه سوزنی شهری ضد آب (IP68)'), 'aside: اقلام سبد رندر شد');
check(str_contains($form, 'type="submit"') && str_contains($form, 'ثبت قطعی سفارش'), 'دکمه ثبت میرور submit');

/* ---------- ۲) thank-you (رسید) ---------- */
$GLOBALS['__es_endpoint10'] = 'order-received';
$GLOBALS['wp']->query_vars = ['order-received' => 77];
ob_start();
include $root . '/woocommerce/checkout/form-checkout.php';
$thanks = (string) ob_get_clean();

$rec_ref = str_replace([
    'ESE-1403-8942',
    'خریدار محترم</div>',
    'درگاه هوشمند بانک ملت</div>',
], [
    '#1404',
    'مهندس رضایی</div>',
    'پرداخت آنلاین بانک ملت</div>',
], substr($M, $hero1, $form0 - $hero1));
$rec_ref = str_replace(['<a href="index.html"', '<a href="shop.html"'], ['<a href="https://test.example/"', '<a href="https://test.example/shop/"'], $rec_ref);
$rec_ref .= '</section>';
check($thanks === $hero_ref . $rec_ref, 'بایت‌دقیق: thank-you = هیرو + رسید (کد/نام/روش سفارش)');
if ($thanks !== $hero_ref . $rec_ref) {
    $a = $hero_ref . $rec_ref;
    $n = min(strlen($thanks), strlen($a));
    for ($i = 0; $i < $n; $i++) { if ($thanks[$i] !== $a[$i]) break; }
    echo 'DBG len ', strlen($thanks), ' vs ', strlen($a), ' @', $i, "\nREF: ", substr($a, max(0, $i - 60), 120), "\nOUT: ", substr($thanks, max(0, $i - 60), 120), "\n";
}
check(str_contains($thanks, 'data-checkout-receipt="true"') && !str_contains($thanks, 'name="billing_first_name"'), 'thank-you: رسید بدون فرم');

/* ---------- ۳) CheckoutFields ---------- */
require_once $root . '/inc/Woo/CheckoutFields.php';
$es_fields = \ErfanSanat\Theme\Woo\CheckoutFields::billing_fields(['billing_first_name' => ['required' => true]]);
check(isset($es_fields['billing_es_tax_id']) && $es_fields['billing_es_tax_id']['required'] === false, 'billing_fields: شناسه ملی optional');
check($es_fields['billing_first_name']['required'] === true, 'billing_fields: فیلدهای استاندارد حفظ');

$GLOBALS['__es_wc_hooks_saved'] = [];
class ES_OrderSave10 {
    public array $meta = [];
    public function update_meta_data(string $k, mixed $v): void { $this->meta[$k] = $v; }
}
$es_order = new ES_OrderSave10();
$_POST['buyer_type'] = 'حقوقی / شهرداری و سازمان دولتی';
$_POST['billing_es_tax_id'] = '10260001234';
\ErfanSanat\Theme\Woo\CheckoutFields::save_order_meta($es_order, ['billing_es_tax_id' => '']);
check(($es_order->meta['es_buyer_type'] ?? '') === 'حقوقی / شهرداری و سازمان دولتی' && ($es_order->meta['es_tax_id'] ?? '') === '10260001234', 'create_order: متای es_buyer_type/es_tax_id ذخیره شد');

unset($_POST['buyer_type'], $_POST['billing_es_tax_id']);
$es_order2 = new ES_OrderSave10();
\ErfanSanat\Theme\Woo\CheckoutFields::save_order_meta($es_order2, []);
check($es_order2->meta === [], 'create_order: بدون POST → متایی ذخیره نمی‌شود');

/* ---------- ۴) بوت ---------- */
check(str_contains((string) file_get_contents($root . '/inc/Theme.php'), 'CheckoutFields::init'), 'بوت: CheckoutFields در Theme.php');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
