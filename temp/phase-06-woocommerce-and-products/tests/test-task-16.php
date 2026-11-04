<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.16 product tag and brand archives ===\n";
foreach (['product_tag' => 'برچسب QA', 'product_brand' => 'برند QA'] as $taxonomy => $name) {
    if (!taxonomy_exists($taxonomy)) register_taxonomy($taxonomy, 'product', ['public' => true, 'hierarchical' => $taxonomy === 'product_brand']);
    $term = get_term_by('name', $name, $taxonomy);
    if (!$term instanceof WP_Term) { $created = wp_insert_term($name, $taxonomy, ['description' => 'توضیحات ' . $name]); if (!is_wp_error($created)) $term = get_term((int) $created['term_id'], $taxonomy); }
    if ($term instanceof WP_Term) { $GLOBALS['es_product_archive_term'] = $term; ob_start(); get_template_part('template-parts/product/taxonomy-header'); $html = (string) ob_get_clean(); $assert(str_contains($html, $name) && str_contains($html, 'توضیحات ' . $name), $taxonomy . ' archive title/description'); }
}
$assert(is_file($themeRoot . '/woocommerce/taxonomy-product_tag.php'), 'product tag template exists');
$assert(is_file($themeRoot . '/woocommerce/taxonomy-product_brand.php'), 'product brand template exists');
$finishTest();
