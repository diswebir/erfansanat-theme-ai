<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$not_found = es_p6_source( 'woocommerce/404.php' );
$page = es_p6_source( 'template-parts/general/page-content.php' );
check( 'قالب 404 کد وضعیت HTTP 404 و no-cache می‌دهد', fn() => es_p6_has_all( $not_found, array( 'status_header( 404 )', 'nocache_headers()', 'خطای ۴۰۴' ) ) && es_p6_has_files( array( '404.php' ) ) );
check( 'برگه خالی بدون Notice/Fatal، fallback متن قابل‌فهم نشان می‌دهد', fn() => str_contains( $page, "if ( ! have_posts() ) { return; }" ) && str_contains( $page, 'محتوایی برای نمایش در این برگه ثبت نشده است.' ) );
check( 'صفحه عمومی فقط محتوای داینامیک دارد و classهای بصری از لیست موجودند', fn() => str_contains( $page, 'the_content()' ) && str_contains( $page, 'the_title()' ) && str_contains( $page, 'card-light' ) === false && str_contains( $not_found, 'wc_get_page_permalink( \'shop\' )' ) );
summary();
