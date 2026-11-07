<?php
/** Runtime acceptance checks for the isolated local site. CLI only. */
defined('ABSPATH') || exit;
if (!defined('WP_CLI') || !WP_CLI) { exit("CLI only\n"); }

function es_assert(bool $condition, string $message): void {
    if (!$condition) { WP_CLI::error('FAIL: ' . $message); }
    WP_CLI::log('PASS: ' . $message);
}

es_assert(version_compare(get_bloginfo('version'), '7.1.0', '>='), 'WordPress >= 7.1');
es_assert(defined('WC_VERSION') && version_compare(WC_VERSION, '10.9.0', '>='), 'WooCommerce >= 10.9');
es_assert(defined('SQLITE_DRIVER_VERSION') && SQLITE_DRIVER_VERSION === '3.1.2', 'SQLite driver 3.1.2');
es_assert(get_locale() === 'fa_IR' && is_rtl(), 'Persian locale and RTL');
es_assert(get_woocommerce_currency() === 'IRT', 'WooCommerce currency IRT');
es_assert(post_type_exists('projects') && post_type_exists('services'), 'projects and services CPTs');
es_assert(taxonomy_exists('city') && taxonomy_exists('project_type'), 'city and project_type taxonomies');
es_assert((int) wp_count_posts('product')->publish === 20, '20 published products');
es_assert((int) wp_count_posts('projects')->publish === 12, '12 published projects');
es_assert((int) wp_count_posts('services')->publish === 6, '6 published services');
es_assert((int) wp_count_posts('post')->publish === 7, '7 published posts');
es_assert((int) wp_count_posts('page')->publish === 14, '14 published pages');
es_assert(count(wc_get_attribute_taxonomies()) === 18, '18 product attributes');
es_assert((int) wp_count_terms(['taxonomy' => 'product_cat', 'hide_empty' => false]) === 15, '15 product categories');
es_assert((int) wp_count_terms(['taxonomy' => 'city', 'hide_empty' => false]) === 12, '12 cities');
es_assert((int) wp_count_terms(['taxonomy' => 'project_type', 'hide_empty' => false]) === 4, '4 project types');
es_assert(count(wc_get_orders(['limit' => -1, 'return' => 'ids'])) === 3, '3 synthetic orders');
es_assert((bool) get_page_by_path('smartelement', OBJECT, 'services'), 'smartelement is a service');

$variable = wc_get_product(wc_get_product_id_by_sku('ES-FIX-001'));
es_assert($variable && $variable->is_type('variable') && count($variable->get_children()) === 3, 'variable product has 3 variations');
$inquiry_id = wc_get_product_id_by_sku('ES-FIX-006');
es_assert(get_post_meta($inquiry_id, 'true_sale', true) === 'false', 'inquiry product true_sale=false');
$gallery = get_post_meta($inquiry_id, 'galerry_produ', true);
es_assert(is_string($gallery) && str_contains($gallery, ','), 'legacy comma-separated gallery preserved');
$usage = get_post_meta($inquiry_id, 'usage_countr', true);
es_assert(is_string($usage) && is_array(unserialize($usage, ['allowed_classes' => false])), 'legacy serialized usage_countr preserved');
$faq = json_decode(get_post_meta($inquiry_id, 'faq', true), true);
es_assert(isset($faq['item-0']['name_question']), 'legacy JSON repeater preserved');
$project = get_page_by_path('ahvaz-semi-pyramid-light', OBJECT, 'projects');
es_assert($project && (int) get_post_meta($project->ID, 'foure-image', true) > 0, 'misspelled foure-image project gallery key');
es_assert(str_starts_with(DB_PATH, dirname(WP_CONTENT_DIR, 2) . '/data/') && !str_contains(DB_PATH, '/wordpress/'), 'SQLite database is outside public docroot');
es_assert(get_option('page_on_front') > 0 && get_option('page_for_posts') > 0, 'static front page and posts page configured');
WP_CLI::success('Local runtime acceptance checks passed.');
