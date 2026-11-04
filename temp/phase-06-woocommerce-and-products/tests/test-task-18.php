<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.18 mini-cart modal and WC fragments ===\n";
if (!class_exists('ES_Test_Mini_Product')) { class ES_Test_Mini_Product { public function get_name(): string { return 'چراغ مینی‌کارت'; } public function get_price(): string { return '50000'; } public function get_permalink(): string { return home_url('/product/mini/'); } public function get_image(): string { return '<img src="https://example.com/m.jpg" alt="">'; } } }
if (!class_exists('ES_Test_Mini_Cart')) { class ES_Test_Mini_Cart { public function get_cart(): array { return ['mini-hash' => ['product_id' => 33, 'quantity' => 2, 'data' => new ES_Test_Mini_Product(), 'line_total' => 100000]]; } public function get_cart_contents_count(): int { return 2; } public function get_total($context = 'view'): string { return '100000'; } } }
$GLOBALS['es_wc_cart'] = new ES_Test_Mini_Cart();
$service = new \ErfanSanat\Theme\WooCommerce\MiniCart(); $service->register();
$assert(has_filter('woocommerce_add_to_cart_fragments', [$service, 'fragments']) !== false, 'WC fragments filter registered');
$assert(has_action('wp_ajax_erfansanat_update_mini_cart', [$service, 'updateItem']) !== false && has_action('wp_ajax_nopriv_erfansanat_update_mini_cart', [$service, 'updateItem']) !== false, 'authenticated and guest cart update AJAX actions registered');
$fragments = $service->fragments([]);
foreach (['div[data-cart-modal-items-list="true"]', 'span[data-cart-modal-total="true"]', 'span[data-cart-modal-count="true"]', 'div[data-cart-modal-empty="true"]', 'span[data-header-cart-badge]'] as $selector) $assert(isset($fragments[$selector]), 'fragment selector ' . $selector);
$assert(str_contains($fragments['div[data-cart-modal-items-list="true"]'], 'data-cart-modal-row="mini-hash"') && str_contains($fragments['div[data-cart-modal-items-list="true"]'], 'چراغ مینی‌کارت'), 'modal row HTML uses live cart data');
$js = (string) file_get_contents($themeRoot . '/assets/js/main.js');
$assert(str_contains($js, 'erfansanat_update_mini_cart') && str_contains($js, 'cart_item_key'), 'modal quantity controls call server-side Woo cart AJAX');
$serviceSource = (string) file_get_contents($themeRoot . '/inc/WooCommerce/MiniCart.php');
$assert(str_contains($serviceSource, 'check_ajax_referer') && str_contains($serviceSource, 'set_quantity'), 'server-side quantity updates verify nonce and mutate WC cart');
$html = (string) file_get_contents($themeRoot . '/template-parts/global/cart-modal.php');
foreach (['data-cart-modal="true"', 'data-cart-modal-live="true"', 'data-cart-modal-backdrop="true"', 'data-cart-modal-items-list="true"', 'data-cart-modal-close="true"'] as $needle) $assert(str_contains($html, $needle), 'modal static hook ' . $needle);
unset($GLOBALS['es_wc_cart']); $finishTest();
