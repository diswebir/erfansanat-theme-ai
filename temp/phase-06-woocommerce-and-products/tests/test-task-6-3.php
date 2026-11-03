<?php
/**
 * Test Task 6.3 — cart/checkout/my-account woocommerce wrappers.
 * Run: php-wasm-cli temp/phase-06-woocommerce-and-products/tests/test-task-6-3.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c ? "[PASS] " : "[FAIL] ") . $l . "\n"; if (!$c) $fail++; }

$cart = "$THEME/woocommerce/cart.php";
$co   = "$THEME/woocommerce/checkout.php";
$acc  = "$THEME/woocommerce/my-account.php";
check('woocommerce/cart.php exists', is_file($cart));
check('woocommerce/checkout.php exists', is_file($co));
check('woocommerce/my-account.php exists', is_file($acc));

$c = file_get_contents($cart);
check('cart invokes woocommerce cart action', strpos($c, "woocommerce_cart") !== false);
check('cart keeps theme shell (get_header/footer + main)', strpos($c, 'get_header()') !== false && strpos($c, '<main') !== false && strpos($c, 'get_footer()') !== false);

$o = file_get_contents($co);
check('checkout invokes woocommerce_checkout', strpos($o, 'woocommerce_checkout') !== false);
check('checkout keeps theme shell', strpos($o, 'get_header()') !== false && strpos($o, 'get_footer()') !== false);

$m = file_get_contents($acc);
check('my-account invokes woocommerce_account_content', strpos($m, 'woocommerce_account_content') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
