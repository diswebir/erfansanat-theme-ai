<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$form = es_p6_source( 'woocommerce/checkout/form-checkout.php' );
$review = es_p6_source( 'woocommerce/checkout/review-order.php' );
$thankyou = es_p6_source( 'woocommerce/checkout/thankyou.php' );
check( 'checkout فرم واقعی و هوک‌های customer details/order review را حفظ می‌کند', fn() => es_p6_has_all( $form, array( 'woocommerce-checkout', 'woocommerce_checkout_before_customer_details', 'woocommerce_checkout_billing', 'woocommerce_checkout_shipping', 'woocommerce_checkout_after_customer_details', 'woocommerce_checkout_order_review', 'woocommerce_checkout_after_order_review' ) ) && ! str_contains( $form, 'data-checkout-form' ) );
check( 'review order شامل cart، کوپن، ارسال، مالیات، جمع و hookهای gateway است', fn() => es_p6_has_all( $review, array( 'woocommerce_review_order_before_cart_contents', 'woocommerce_review_order_after_cart_contents', 'woocommerce_review_order_before_shipping', 'woocommerce_review_order_after_order_total', 'wc_cart_totals_order_total_html' ) ) );
check( 'صفحه تشکر اطلاعات سفارش و hookهای پرداخت را به‌صورت امن نشان می‌دهد', fn() => es_p6_has_all( $thankyou, array( 'woocommerce_before_thankyou', 'woocommerce_thankyou_', 'woocommerce_thankyou', 'get_order_number()', 'get_formatted_order_total()', 'wp_kses_post' ) ) );
summary();
