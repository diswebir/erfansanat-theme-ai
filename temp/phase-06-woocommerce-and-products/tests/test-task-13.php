<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$taxonomies = array( 'woocommerce/taxonomy-product_tag.php', 'woocommerce/taxonomy-product_brand.php', 'taxonomy-product_tag.php', 'taxonomy-product_brand.php' );
$tag = es_p6_source( 'woocommerce/taxonomy-product_tag.php' );
$brand = es_p6_source( 'woocommerce/taxonomy-product_brand.php' );
check( 'برچسب و برند هر دو به آرشیو مشترک هدایت می‌شوند', fn() => es_p6_has_files( $taxonomies ) && str_contains( $tag, 'archive-product.php' ) && str_contains( $brand, 'archive-product.php' ) );
check( 'Hero آرشیو عنوان و توضیح ترم را پویا می‌سازد و گرید حالت خالی دارد', fn() => es_p6_has_all( es_p6_source( 'template-parts/archive/hero.php' ) . es_p6_source( 'template-parts/archive/grid.php' ), array( '$term->name', '$term->description', 'data-product-empty="true"' ) ) );
summary();
