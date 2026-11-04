<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.13 related products ===\n";
if (!taxonomy_exists('product_cat')) register_taxonomy('product_cat', 'product', ['public' => true, 'hierarchical' => true]);
$id = $makeProduct();
$related = wp_insert_post(['post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول مکمل QA']);
$term = get_term_by('name', 'QA Lighting', 'product_cat'); if (!$term instanceof WP_Term) { $r = wp_insert_term('QA Lighting', 'product_cat'); if (!is_wp_error($r)) $term = get_term((int) $r['term_id'], 'product_cat'); }
if ($term instanceof WP_Term) { wp_set_object_terms($id, [(int) $term->term_id], 'product_cat'); wp_set_object_terms($related, [(int) $term->term_id], 'product_cat'); }
$html = $renderPart('related-products');
$assert(str_contains($html, 'data-related-products'), 'related products wrapper rendered');
$assert(str_contains($html, 'محصول مکمل QA') && str_contains($html, 'data-card-product'), 'related item uses standard product card');
wp_delete_post($related, true); wp_delete_post($id, true); $finishTest();
