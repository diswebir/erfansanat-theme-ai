<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$source = es_p6_source( 'inc/Woo/WooSupport.php' );
$constants = es_p6_source( 'inc/Constants.php' );
check( 'HPOS اعلام می‌شود و پشتیبانی Blocks مطابق تصمیم G3 خاموش است', fn() => es_p6_has_all( $source, array( "'custom_order_tables'", "'cart_checkout_blocks'", 'declare_compatibility' ) ) && preg_match( "/custom_order_tables'.*?true/s", $source ) && preg_match( "/cart_checkout_blocks'.*?false/s", $source ) );
check( 'بدون WooCommerce فراخوانی API اجرا نمی‌شود', fn() => str_contains( $source, "function_exists( 'is_product' )" ) && str_contains( $source, 'class_exists( $features_util )' ) && str_contains( $source, "function_exists( 'wp_enqueue_script' )" ) );
check( 'پوشه override و baseline قالب 11.2.2 ثبت شده‌اند', fn() => es_p6_has_files( array( 'woocommerce/archive-product.php', 'woocommerce/single-product.php', 'woocommerce/cart/cart.php' ) ) && str_contains( $constants, "ES_WC_TEMPLATE_VERSION', '11.2.2'" ) );
summary();
