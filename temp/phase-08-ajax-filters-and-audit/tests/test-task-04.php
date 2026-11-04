<?php
/** Automated QA Test Script — Task 8.4: Product filter query builder. */
declare(strict_types=1);
$root = dirname(__DIR__, 3);
$failures = [];
$assert = static function (bool $condition, string $label) use (&$failures): void { echo $condition ? "  [PASS] {$label}\n" : "  [FAIL] {$label}\n"; if (!$condition) $failures[] = $label; };
require_once '/opt/wp-testbed/wp-load.php';
$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.4: Product filter query builder ===\n";
$class = 'ErfanSanat\\Theme\\Filters\\ProductFilterQuery';
$assert(is_file($root . '/inc/Filters/ProductFilterQuery.php'), 'ProductFilterQuery source exists');
$assert(class_exists($class), 'ProductFilterQuery autoloads');

$categorySlug = 'phase8-query-qa-' . strtolower(wp_generate_password(6, false, false));
$category = wp_insert_term('Phase 8 Query QA ' . $categorySlug, 'product_cat', ['slug' => $categorySlug]);
if (is_wp_error($category)) { fwrite(STDERR, $category->get_error_message() . "\n"); exit(1); }
$ids = [];
foreach ([['Search QA Low Lamp', '1000'], ['Search QA High Lamp', '5000'], ['Unrelated QA Lamp', '700']] as [$title, $price]) {
    $id = wp_insert_post(['post_type' => 'product', 'post_status' => 'publish', 'post_title' => $title]);
    $ids[] = (int) $id;
    update_post_meta($id, '_price', $price);
    if ($title !== 'Unrelated QA Lamp') wp_set_object_terms($id, [(int) $category['term_id']], 'product_cat');
}
$args = $class::buildArgs(['search' => 'Search QA', 'product_cat' => $categorySlug, 'min_price' => '500', 'max_price' => '2000', 'sort' => 'price-asc']);
$assert(($args['post_type'] ?? '') === 'product' && ($args['post_status'] ?? '') === 'publish', 'query is constrained to published products');
$assert(($args['s'] ?? '') === 'Search QA', 'search input is sanitized into WP_Query');
$assert(isset($args['tax_query'][0]['taxonomy']) && $args['tax_query'][0]['taxonomy'] === 'product_cat', 'product_cat slug becomes a taxonomy query');
$assert(isset($args['meta_query'][0]['key']) && $args['meta_query'][0]['key'] === '_price' && $args['meta_query'][0]['compare'] === 'BETWEEN', 'price range uses exact _price metadata');
$assert(($args['meta_key'] ?? '') === '_price' && ($args['orderby'] ?? '') === 'meta_value_num' && ($args['order'] ?? '') === 'ASC', 'price-asc sort is numeric and ascending');
$query = new WP_Query($args);
$assert(count(array_intersect($ids, wp_list_pluck($query->posts, 'ID'))) === 1 && (int) $query->posts[0]->ID === $ids[0], 'combined search/category/price filters return only the expected product');
$badSort = $class::buildArgs(['sort' => 'sql injection DESC']);
$assert(($badSort['orderby'] ?? '') === 'menu_order title' && ($badSort['order'] ?? '') === 'ASC', 'unsupported sort values fall back to a safe default');
$empty = $class::buildArgs(['min_price' => '-10', 'max_price' => 'not-a-price']);
$assert(!isset($empty['meta_query']), 'invalid and negative price bounds are ignored safely');
foreach ($ids as $id) wp_delete_post($id, true);
wp_delete_term((int) $category['term_id'], 'product_cat');
if ($failures) { fwrite(STDERR, "[FAILED] " . implode('; ', $failures) . "\n"); exit(1); }
echo "[PASSED] Task 8.4 checks completed successfully.\n";
