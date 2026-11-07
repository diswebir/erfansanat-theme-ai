<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$archive = es_p6_source( 'woocommerce/archive-product.php' );
$grid = es_p6_source( 'template-parts/archive/grid.php' );
$faq = es_p6_source( 'template-parts/archive/product-cat-faq.php' );
check( 'فروشگاه و taxonomy مشترک hero/grid پویا را رندر می‌کنند', fn() => es_p6_has_all( $archive . $grid, array( 'template-parts/archive/hero', 'template-parts/archive/grid', 'data-products-archive="true"', 'data-product-grid="true"' ) ) );
check( 'صفحه‌بندی WooCommerce حفظ شده و کارت‌ها در loop واقعی لود می‌شوند', fn() => es_p6_has_all( $grid . $archive, array( "wc_get_template_part( 'content', 'product' )", 'woocommerce_pagination()', 'woocommerce_before_shop_loop', 'woocommerce_after_shop_loop' ) ) && str_contains( $archive, 'woocommerce_after_shop_loop' ) );
check( 'FAQ دسته از term meta و data-faq accordion تغذیه می‌شود', fn() => es_p6_has_all( $faq, array( "'faq'", 'data-faq-list', 'data-faq-toggle', 'data-faq-body', 'wp_kses_post( $item[\'answer\'] )' ) ) );
check( 'حالت بدون محصول دارای empty state و کلاس مخفی قابل کنترل با JS است', fn() => es_p6_has_all( $grid, array( 'data-product-empty="true"', 'محصولی پیدا نشد', 'data-product-count="true"' ) ) );
summary();
