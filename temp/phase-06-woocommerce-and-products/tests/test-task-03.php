<?php
/**
 * Task 6.3 — سبد/پرداخت/حساب کاربری + auth
 * TDD skeleton (Stage 1). Currently RED until the task is implemented.
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-03.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

echo "== Task 6.3: سبد/پرداخت/حساب کاربری + auth ==\n";

es_check( 'cart.php override', (bool) (es_file('woocommerce/cart/cart.php')) );
es_check( 'form-checkout.php override', (bool) (es_file('woocommerce/checkout/form-checkout.php')) );
es_check( 'myaccount dir', (bool) (es_file('woocommerce/myaccount/my-account.php') || es_dir('woocommerce/myaccount')) );
es_check( 'auth template', (bool) (es_file('page-templates/auth.php')) );
es_check( 'checkout hook preserved', (bool) (es_contains('woocommerce/checkout/form-checkout.php','woocommerce_checkout')) );

es_summary();
