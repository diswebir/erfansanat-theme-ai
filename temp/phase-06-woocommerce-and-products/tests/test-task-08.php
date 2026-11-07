<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$source = es_p6_source( 'template-parts/product/catalog.php' );
check( 'کاتالوگ فقط زمانی که URL رسانه موجود است رندر می‌شود', fn() => str_contains( $source, "'product-catalog-file'" ) && str_contains( $source, 'if ( \'\' === $url ) { return; }' ) );
check( 'لینک از esc_url و rel امن و ویژگی download استفاده می‌کند', fn() => es_p6_has_all( $source, array( 'href="<?php echo esc_url( $url ); ?>"', 'rel="noopener noreferrer"', 'download' ) ) );
check( 'حجم فایل محلی با size_format و اندازه‌گیری فایل محاسبه می‌شود', fn() => es_p6_has_all( $source, array( 'attachment_url_to_postid', 'get_attached_file', 'filesize( $file_path )', 'size_format(' ) ) );
summary();
