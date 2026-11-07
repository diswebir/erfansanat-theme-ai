<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$gallery = es_p6_source( 'inc/Woo/ProductGallery.php' );
$partial = es_p6_source( 'template-parts/product/gallery.php' );
$js = es_p6_source( 'assets/js/main.js' );
check( 'زنجیره fallback کلیدهای چهارگانه و image-2..6 حفظ شده است', fn() => es_p6_has_all( $gallery, array( "'galerry_produ'", "'_product_image_gallery'", "'gallery-filed'", "'image-2'", "'image-6'" ) ) );
check( 'لایت‌باکس و شمارنده با data-gallery قرارداد JS موجود جفت هستند', fn() => es_p6_has_all( $partial, array( 'data-gallery-root', 'data-gallery-main-img', 'data-gallery-thumb', 'data-gallery-src', 'data-gallery-lightbox', 'data-gallery-lightbox-img', 'data-gallery-counter' ) ) && es_p6_has_all( $js, array( 'data-gallery-lightbox-close', 'data-gallery-lightbox-next', 'data-gallery-prev' ) ) );
check( 'در نبود گالری تصویر شاخص اول می‌شود و نبود تصویر fatal نمی‌دهد', fn() => str_contains( $gallery, 'get_post_thumbnail_id' ) && str_contains( $partial, 'function_exists( \'wp_get_attachment_image_url\' )' ) && str_contains( $partial, 'if ( ! $image_urls ) { return; }' ) );
check( 'تصویر اصلی eager/high-priority است و thumbnailها lazy هستند', fn() => str_contains( $partial, 'fetchpriority="high"' ) && str_contains( $partial, 'loading="lazy"' ) && str_contains( $partial, 'data-gallery-src=' ) );
summary();
