<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.15 product category SEO and FAQ ===\n";
if (!taxonomy_exists('product_cat')) register_taxonomy('product_cat', 'product', ['public' => true, 'hierarchical' => true]);
$term = get_term_by('name', 'دسته QA', 'product_cat'); if (!$term instanceof WP_Term) { $created = wp_insert_term('دسته QA', 'product_cat', ['description' => 'توضیح سئوی دسته QA']); if (!is_wp_error($created)) $term = get_term((int) $created['term_id'], 'product_cat'); }
$assert($term instanceof WP_Term, 'category term available');
if ($term instanceof WP_Term) { update_term_meta($term->term_id, 'faq', ['item-0' => ['name_question' => 'سوال دسته QA', 'answer_question' => 'پاسخ دسته QA']]); $GLOBALS['es_product_category_term'] = $term; }
$html = ''; if (isset($GLOBALS['es_product_category_term'])) { ob_start(); get_template_part('template-parts/product/category-intro'); $html = (string) ob_get_clean(); }
foreach (['دسته QA', 'توضیح سئوی دسته QA', 'سوال دسته QA', 'پاسخ دسته QA'] as $needle) $assert(str_contains($html, $needle), 'category SEO/FAQ ' . $needle);
$assert(is_file($themeRoot . '/woocommerce/taxonomy-product_cat.php'), 'category taxonomy template exists');
$finishTest();
