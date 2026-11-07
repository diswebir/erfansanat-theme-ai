<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$source = es_p6_source( 'template-parts/product/legal-ids.php' );
$js = es_p6_source( 'assets/js/product-enhancements.js' );
$keys = array( "'gs1_id'", "'national_code'", "'hs_id'", "'isic_id'", "'cpc_id'", "'product_id'" );
check( 'هر شش شناسه رسمی از کلیدهای اصلی متا خوانده می‌شوند', fn() => es_p6_has_all( $source, $keys ) );
check( 'شناسه خالی سطر و سکشن ایجاد نمی‌کند و مقدار با esc_html/esc_attr امن است', fn() => str_contains( $source, 'if ( ! $values ) { return; }' ) && str_contains( $source, 'esc_html( $item[\'value\'] )' ) && str_contains( $source, 'esc_attr( $item[\'value\'] )' ) );
check( 'کپی clipboard دارای fallback و پیام دسترس‌پذیر است', fn() => es_p6_has_all( $source . $js, array( 'data-copy-button', 'data-copy-value', 'navigator.clipboard.writeText', 'fallbackCopy(value)', 'data-copy-status', 'aria-live="polite"' ) ) );
summary();
