<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.17 WooCommerce cart page ===\n";
if (!class_exists('ES_Test_Cart_Product')) { class ES_Test_Cart_Product { public function get_name(): string { return 'چراغ تستی سبد'; } public function get_price(): string { return '200000'; } public function get_permalink(): string { return home_url('/product/qa/'); } public function get_image(): string { return '<img src="https://example.com/p.jpg" alt="">'; } } }
if (!class_exists('ES_Test_Cart')) { class ES_Test_Cart { public function get_cart(): array { return ['hashqa' => ['product_id' => 22, 'quantity' => 2, 'data' => new ES_Test_Cart_Product(), 'line_total' => 400000, 'line_tax' => 0]]; } public function get_cart_contents_count(): int { return 2; } public function get_subtotal(): string { return '400000'; } public function get_cart_tax(): string { return '0'; } public function get_total($context = 'view'): string { return '400000'; } } }
$GLOBALS['es_wc_cart'] = new ES_Test_Cart();
ob_start(); include $themeRoot . '/woocommerce/cart/cart.php'; $html = (string) ob_get_clean();
foreach (['data-cart-page="true"', 'data-cart-item-row', 'data-cart-qty-val', 'data-cart-qty-dec', 'data-cart-qty-inc', 'data-cart-remove', 'data-cart-count="true"', 'data-cart-subtotal="true"', 'data-cart-tax="true"', 'data-cart-grandtotal="true"', 'data-cart-items-list="true"', 'چراغ تستی سبد'] as $needle) $assert(str_contains($html, $needle), 'cart contract ' . $needle);
unset($GLOBALS['es_wc_cart']); $finishTest();
