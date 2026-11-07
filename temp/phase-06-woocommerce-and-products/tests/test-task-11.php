<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$source = es_p6_source( 'template-parts/product/related.php' );
check( 'بلوک محصول مرتبط در نبود نتایج حذف می‌شود', fn() => str_contains( $source, 'if ( ! $ids ) { return; }' ) );
check( 'محصول جاری صریحاً حذف و پس از unique/filter از لیست کنار گذاشته می‌شود', fn() => es_p6_has_all( $source, array( 'wc_get_related_products(', 'array( $product_id )', '$id !== $product_id' ) ) );
check( 'حداکثر چهار محصول با ترتیب پایدار و کارت مشترک رندر می‌شود', fn() => es_p6_has_all( $source, array( 'crc32( $product_id', 'array_slice( $ids, 0, 4 )', "template-parts/cards/card-product" ) ) );
summary();
