<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$cart = es_p6_source( 'woocommerce/cart/cart.php' );
$empty = es_p6_source( 'woocommerce/cart/cart-empty.php' );
$totals = es_p6_source( 'woocommerce/cart/cart-totals.php' );
$cross = es_p6_source( 'woocommerce/cart/cross-sells.php' );
check( 'سبد، حذف/تعداد/کوپن/به‌روزرسانی و هوک‌های اصلی Woo را حفظ می‌کند', fn() => es_p6_has_all( $cart, array( 'woocommerce_before_cart_table', 'woocommerce_before_cart_contents', 'woocommerce_after_cart_contents', 'woocommerce_after_cart_table', 'woocommerce_cart_actions', 'update_cart', 'apply_coupon', 'woocommerce_cart_collaterals' ) ) );
check( 'قالب سبد خالی اختصاصی است و استاتیک data-cart-page حذف شده', fn() => str_contains( $empty, 'سبد خرید شما خالی است' ) && str_contains( $empty, 'woocommerce_return_to_shop_redirect' ) && ! str_contains( $cart . $empty, 'data-cart-page' ) );
check( 'جمع، تخفیف، ارسال، مالیات و total واقعی WooCommerce محاسبه می‌شوند', fn() => es_p6_has_all( $totals, array( 'wc_cart_totals_subtotal_html', 'wc_cart_totals_coupon_html', 'wc_cart_totals_shipping_html', 'wc_cart_totals_order_total_html', 'woocommerce_proceed_to_checkout' ) ) );
check( 'cross-sells از کارت مشترک استفاده می‌کند و postdata را reset می‌کند', fn() => str_contains( $cross, "wc_get_template_part( 'content', 'product' )" ) && str_contains( $cross, 'wp_reset_postdata()' ) );
summary();
