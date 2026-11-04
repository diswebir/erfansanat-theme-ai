<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.19 checkout template uses WooCommerce checkout pipeline ===\n";
$file = $themeRoot . '/woocommerce/checkout/form-checkout.php';
$assert(is_file($file), 'Woo checkout override exists');
$source = (string) file_get_contents($file);
foreach (['data-checkout-page="true"', 'data-checkout-form="true"', 'data-wc-checkout="true"', 'woocommerce_checkout_billing', 'woocommerce_checkout_shipping', 'woocommerce_checkout_order_review', 'woocommerce-process-checkout-nonce'] as $needle) $assert(str_contains($source, $needle), 'checkout contract ' . $needle);
$js = (string) file_get_contents($themeRoot . '/assets/js/main.js');
$assert(str_contains($js, 'getAttribute("data-wc-checkout") !== "true"'), 'legacy receipt script does not intercept real Woo checkout');
$finishTest();
