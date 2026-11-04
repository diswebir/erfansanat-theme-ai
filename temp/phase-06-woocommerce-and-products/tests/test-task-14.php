<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.14 shop archive and product grid ===\n";
if (!taxonomy_exists('product_cat')) register_taxonomy('product_cat', 'product', ['public' => true, 'hierarchical' => true]);
if (!taxonomy_exists('pa_color')) register_taxonomy('pa_color', 'product', ['public' => true, 'hierarchical' => false]);
$id = $makeProduct();
$color = get_term_by('name', 'QA Archive Color', 'pa_color'); if (!$color instanceof WP_Term) { $created = wp_insert_term('QA Archive Color', 'pa_color'); if (!is_wp_error($created)) $color = get_term((int) $created['term_id'], 'pa_color'); }
if ($color instanceof WP_Term) wp_set_object_terms($id, [(int) $color->term_id], 'pa_color');
$GLOBALS['wp_query'] = new WP_Query(['post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 12]); $GLOBALS['wp_query']->is_archive = true;
$GLOBALS['es_archive_title'] = 'فروشگاه محصولات'; $GLOBALS['es_archive_query'] = $GLOBALS['wp_query'];
ob_start(); get_template_part('template-parts/product/archive-layout'); $html = (string) ob_get_clean();
$assert(is_file($themeRoot . '/woocommerce/archive-product.php'), 'WooCommerce archive template exists');
foreach (['data-products-archive="true"', 'data-product-search="true"', 'data-product-purchase-mode="all"', 'data-product-purchase-mode="online"', 'data-product-price-range="true"', 'data-product-sort="true"', 'data-product-count="true"', 'data-product-grid="true"', 'data-product-item', 'data-product-attribute-filter="pa_color"', 'data-product-attributes=', 'محصول تستی'] as $needle) $assert(str_contains($html, $needle), 'shop archive contract ' . $needle);
$assert(str_contains($html, 'data-product-empty="true"'), 'empty state component');
$originalOptions = get_option('erfansanat_theme_options', []);
$changedOptions = is_array($originalOptions) ? $originalOptions : [];
$changedOptions['enabled_filters'] = is_array($changedOptions['enabled_filters'] ?? null) ? $changedOptions['enabled_filters'] : [];
$changedOptions['enabled_filters']['attr_pa_color'] = false;
update_option('erfansanat_theme_options', $changedOptions); \ErfanSanat\Theme\Admin\OptionsStore::flushCache();
ob_start(); get_template_part('template-parts/product/archive-layout'); $disabledHtml = (string) ob_get_clean();
$assert(!str_contains($disabledHtml, 'data-product-attribute-filter="pa_color"'), 'attribute filter respects admin enabled_filters toggle');
if ($originalOptions === false) delete_option('erfansanat_theme_options'); else update_option('erfansanat_theme_options', $originalOptions);
\ErfanSanat\Theme\Admin\OptionsStore::flushCache();
wp_delete_post($id, true); $finishTest();
