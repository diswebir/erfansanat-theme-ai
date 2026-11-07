<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$source = es_p6_source( 'template-parts/product/purchase-box.php' );
$mobile = es_p6_source( 'template-parts/product/mobile-purchase.php' );
check( 'true_sale فعال فقط فرم استاندارد WooCommerce را اجرا می‌کند', fn() => str_contains( $source, "'true', '1', 'yes'" ) && str_contains( $source, 'woocommerce_template_single_add_to_cart()' ) );
check( 'حالت استعلام فرم سبد ندارد و تماس امن تلفنی را ارائه می‌کند', fn() => str_contains( $source, 'استعلام قیمت تلفنی' ) && str_contains( $source, 'href="tel:' ) && str_contains( $mobile, 'استعلام' ) && ! str_contains( $source, 'woocommerce_template_single_add_to_cart(); } else' ) );
check( 'محصول متغیر و افزونه‌های سواچ/تعداد با فرم بومی WooCommerce باقی می‌مانند', fn() => str_contains( $source, 'woocommerce_template_single_add_to_cart' ) && str_contains( $source, 'woocommerce-product-add-to-cart' ) );
check( 'اطلاعیه before-sale-c فقط در صورت وجود و با wp_kses_post نمایش داده می‌شود', fn() => es_p6_has_all( $source, array( "'before-sale-c'", 'if ( \'\' !== $notice )', 'wp_kses_post( $notice )' ) ) );
summary();
