<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$nav = es_p6_source( 'woocommerce/myaccount/navigation.php' );
$login = es_p6_source( 'woocommerce/myaccount/form-login.php' );
$checkout_login = es_p6_source( 'woocommerce/global/form-login.php' );
$orders = es_p6_source( 'woocommerce/myaccount/orders.php' );
check( 'منوی حساب از endpointهای پویای WooCommerce ساخته شده است', fn() => es_p6_has_all( $nav, array( 'wc_get_account_menu_items()', 'wc_get_account_endpoint_url( $endpoint )', 'wc_is_current_account_menu_item' ) ) && str_contains( es_p6_source( 'woocommerce/myaccount/my-account.php' ), 'woocommerce_account_content' ) );
check( 'فرم ورود/ثبت‌نام nonce و hookهای Voorodak/SMS را حفظ می‌کند', fn() => es_p6_has_all( $login . $checkout_login, array( 'woocommerce_login_form_start', 'woocommerce_login_form', 'woocommerce_login_form_end', 'woocommerce_register_form', 'woocommerce-login-nonce', 'woocommerce-register-nonce' ) ) );
check( 'سفارش‌ها ستون‌ها، actionها، وضعیت و pagination واقعی را نشان می‌دهند', fn() => es_p6_has_all( $orders, array( 'wc_get_account_orders_columns()', 'wc_get_order( $customer_order )', 'woocommerce_my_account_my_orders_column_', 'wc_get_account_orders_actions( $order )', 'max_num_pages' ) ) );
summary();
