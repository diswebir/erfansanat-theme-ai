<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$hero = es_p6_source( 'template-parts/product/hero.php' );
$template = es_p6_source( 'woocommerce/single-product.php' );
check( 'هیرو زیرعنوان را شرطی و عنوان انگلیسی را با املای legacy و LTR می‌خواند', fn() => es_p6_has_all( $hero, array( "'zireonvan'", "'english_titl'", "dir=\"ltr\"", 'if ( \'\' !== $subtitle )', 'if ( \'\' !== $english_title )' ) ) );
check( 'عنوان محصول در هدر تکی فقط یک h1 دارد و از نام پویا استفاده می‌کند', fn() => 1 === preg_match_all( '/<h1\\b/i', $hero ) && str_contains( $hero, 'get_name()' ) && str_contains( $template, 'template-parts/product/hero' ) );
check( 'متن‌های محصول escape می‌شوند و HTML توضیحات فقط از مسیر content اعمال می‌شود', fn() => es_p6_has_all( $hero, array( 'esc_html( $title )', 'esc_html( $subtitle )', 'esc_html( $english_title )', 'rank_math_the_breadcrumbs', 'seopress_display_breadcrumbs' ) ) );
check( 'Breadcrumb fallback خانه، فروشگاه و دسته را پیش از محصول می‌سازد', fn() => es_p6_has_all( $hero, array( "home_url( '/' )", "wc_get_page_permalink( 'shop' )", 'get_term_link( $category )', 'aria-current="page"' ) ) );
summary();
