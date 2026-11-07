<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$features = es_p6_source( 'template-parts/product/features.php' );
$usage = es_p6_source( 'template-parts/product/usage.php' );
$faq = es_p6_source( 'template-parts/product/faq.php' );
$js = es_p6_source( 'assets/js/main.js' );
check( 'چهار repeater قدیمی با کلیدهای اصلی و ساختار title/value خوانده می‌شوند', fn() => es_p6_has_all( $features . $usage . $faq, array( "'product-feature'", "'product-feature2_copy'", "'usage_product'", "'faq'", "'add-new-feature'", "'vaule_of_variable'", "'add-new-usage'", "'name_question'", "'answer_question'" ) ) );
check( 'آکاردئون FAQ از data-faq hooks پشتیبانی می‌کند و JS موجود آن را می‌گرداند', fn() => es_p6_has_all( $faq, array( 'data-faq-list', 'data-faq-toggle', 'data-faq-icon', 'data-faq-body' ) ) && str_contains( $js, 'data-faq-toggle' ) );
check( 'repeaterهای خالی هیچ سکشن خالی تولید نمی‌کنند', fn() => es_p6_has_all( $features . $usage . $faq, array( 'if ( ! $items ) { return; }', 'if ( ! $uses && ! $terms ) { return; }' ) ) );
check( 'پاسخ‌ها و HTML مجاز ردیف‌ها با wp_kses_post محدود می‌شوند', fn() => substr_count( $features . $usage . $faq, 'wp_kses_post(' ) >= 3 );
summary();
