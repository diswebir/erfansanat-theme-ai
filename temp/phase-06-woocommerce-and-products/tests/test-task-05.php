<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$data = es_p6_source( 'inc/Woo/AttributeData.php' );
$partial = es_p6_source( 'template-parts/product/specs.php' );
$expected = array( 'after-sales-support', 'beam-angle', 'body-material', 'brand', 'bulb-type', 'cable-material', 'certifications', 'color', 'diameter', 'diffuser-color', 'input-voltage', 'intensity', 'ip-rating', 'led-chip-type', 'lifespan', 'size', 'warranty', 'wattage' );
check( 'هر ۱۸ taxonomy با ترتیب مرجع و پشتیبانی از attributeهای افزوده وجود دارند', fn() => es_p6_has_all( $data, array_map( static fn( $key ) => 'pa_' . $key, $expected ) ) && str_contains( $data, 'foreach ( $by_name as $name => $attribute )' ) );
check( 'خروجی ویژگی خالی را حذف می‌کند و لیبل از wc_attribute_label می‌آید', fn() => es_p6_has_all( $data, array( 'if ( \'\' === $value )', 'wc_attribute_label( $name, $product )' ) ) );
check( 'سواچ pa_color از term meta رنگ می‌گیرد و fallback متنی دارد', fn() => es_p6_has_all( $data, array( '\'pa_color\' === $name', 'get_term_meta( (int) $terms[0]->term_id, \'color\', true )', "'swatch' => ''" ) ) && str_contains( $partial, 'attribute[\'swatch\']' ) );
check( 'مشخصات از get_attributes می‌آیند، نه از متای خام محصول', fn() => str_contains( $data, 'get_attributes()' ) && ! str_contains( $data, 'get_post_meta(' ) && str_contains( $partial, 'if ( ! $attributes ) { return; }' ) );
summary();
