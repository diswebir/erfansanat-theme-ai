<?php
/**
 * WordPress integration contract for task 5.2: dynamic homepage content.
 * Run through PHP WASM or native PHP with WP_TEST_ROOT set.
 */
$root = dirname(__DIR__, 3);
$checks = [
    ['inc/HomepageData.php', 'new \\WP_Query'],
    ['inc/HomepageData.php', 'wc_get_products'],
    ['inc/HomepageData.php', 'get_transient'],
    ['inc/HomepageData.php', 'set_transient'],
    ['inc/HomepageData.php', 'save_post'],
    ['template-parts/cards/card-product.php', 'get_the_title'],
    ['template-parts/cards/card-product.php', 'get_permalink'],
    ['template-parts/cards/card-project.php', 'get_the_title'],
    ['template-parts/cards/card-project.php', 'get_permalink'],
    ['template-parts/cards/card-post.php', 'get_the_title'],
    ['template-parts/cards/card-post.php', 'get_permalink'],
    ['inc/HomepageData.php', 'get_configured_client_count'],
    ['inc/HomepageData.php', 'get_category_image_url'],
    ['template-parts/home/hero.php', 'get_product_categories'],
    ['template-parts/home/hero.php', 'get_projects'],
    ['template-parts/home/projects.php', 'card-project'],
    ['template-parts/home/project-archive.php', 'card-project'],
    ['template-parts/home/products.php', 'card-product'],
    ['template-parts/home/blog-preview.php', 'card-post'],
    ['template-parts/home/about.php', 'get_published_count'],
];
$failed = 0;
foreach ($checks as [$relative, $needle]) {
    $path = $root . '/' . $relative;
    $source = is_file($path) ? file_get_contents($path) : false;
    if ($source === false || strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL dynamic homepage contract missing `{$needle}` in {$relative}\n");
        $failed++;
    }
}
foreach (['product', 'project', 'post'] as $card) {
    $path = $root . '/template-parts/cards/card-' . $card . '.php';
    if (!is_file($path)) {
        fwrite(STDERR, "FAIL missing shared card template: template-parts/cards/card-{$card}.php\n");
        $failed++;
    }
}

$wpRoot = getenv('WP_TEST_ROOT');
if (!$wpRoot) {
    $wpTestDir = getenv('WP_TEST_DIR') ?: $root . '/node_modules/.cache/ese-wp-test';
    $wpRoot = rtrim($wpTestDir, DIRECTORY_SEPARATOR) . '/WordPress-master';
}
if (!is_file($wpRoot . '/wp-load.php')) {
    fwrite(STDERR, "FAIL WordPress test installation not found at {$wpRoot}\n");
    exit(1);
}
$_SERVER['HTTP_HOST'] = 'example.test';
$_SERVER['SERVER_NAME'] = 'example.test';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$class = 'ErfanSanat\\Theme\\HomepageData';
$sectionClass = 'ErfanSanat\\Theme\\Admin\\SectionManager';
if (!class_exists($class) || !class_exists($sectionClass)) {
    fwrite(STDERR, "FAIL HomepageData or SectionManager was not loaded\n");
    exit(1);
}

$pageId = wp_insert_post([
    'post_type' => 'page',
    'post_status' => 'publish',
    'post_title' => 'Task 5.2 Homepage QA',
    'post_name' => 'arena-task-52-home',
]);
if (is_wp_error($pageId) || !$pageId) {
    fwrite(STDERR, "FAIL could not create homepage QA page\n");
    exit(1);
}
update_option('show_on_front', 'page');
update_option('page_on_front', (int) $pageId);

if (!post_type_exists('product')) {
    register_post_type('product', ['public' => true, 'supports' => ['title', 'editor', 'excerpt', 'thumbnail']]);
}
if (!taxonomy_exists('product_cat')) {
    register_taxonomy('product_cat', ['product'], ['public' => true, 'hierarchical' => true]);
}
$categoryTerm = get_term_by('slug', 'task-52-qa-category', 'product_cat');
if (!$categoryTerm) {
    $category = wp_insert_term('Task 5.2 QA category', 'product_cat', ['slug' => 'task-52-qa-category']);
    $categoryTerm = !is_wp_error($category) ? get_term((int) $category['term_id'], 'product_cat') : false;
}
$categoryId = $categoryTerm && !is_wp_error($categoryTerm) ? (int) $categoryTerm->term_id : 0;
if (!$categoryId) {
    fwrite(STDERR, "FAIL could not create the QA product category\n");
    exit(1);
}

$insert = static function (string $type, string $title, string $slug): int {
    $id = wp_insert_post([
        'post_type' => $type,
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $slug,
        'post_content' => 'QA content for ' . $title,
        'post_excerpt' => 'QA excerpt for ' . $title,
        'post_date' => gmdate('Y-m-d H:i:s', time() - wp_rand(10, 100)),
    ]);
    return is_wp_error($id) ? 0 : (int) $id;
};
$projectA = $insert('projects', 'Task 5.2 QA project alpha', 'task-52-project-alpha');
$projectB = $insert('projects', 'Task 5.2 QA project beta', 'task-52-project-beta');
$productA = $insert('product', 'Task 5.2 QA product alpha', 'task-52-product-alpha');
$productB = $insert('product', 'Task 5.2 QA product beta', 'task-52-product-beta');
$articleA = $insert('post', 'Task 5.2 QA article alpha', 'task-52-article-alpha');
$articleB = $insert('post', 'Task 5.2 QA article beta', 'task-52-article-beta');
if (!$projectA || !$projectB || !$productA || !$productB || !$articleA || !$articleB) {
    fwrite(STDERR, "FAIL could not create dynamic homepage fixtures\n");
    exit(1);
}
wp_set_object_terms($productA, [$categoryId], 'product_cat');
wp_set_object_terms($productB, [$categoryId], 'product_cat');
$projectTerm = get_term_by('slug', 'task-52-type', 'project_type');
if (!$projectTerm) {
    $newProjectTerm = wp_insert_term('Task 5.2 QA project type', 'project_type', ['slug' => 'task-52-type']);
    $projectTerm = !is_wp_error($newProjectTerm) ? get_term((int) $newProjectTerm['term_id'], 'project_type') : false;
}
if ($projectTerm && !is_wp_error($projectTerm)) {
    wp_set_object_terms($projectA, [(int) $projectTerm->term_id], 'project_type');
    wp_set_object_terms($projectB, [(int) $projectTerm->term_id], 'project_type');
}
update_post_meta($projectA, 'finish-pro-time', '1404/01/01');
update_post_meta($projectB, 'finish-pro-time', '1404/02/01');

$projects = $class::get_projects(2, ['post__in' => [$projectA, $projectB], 'orderby' => 'post__in']);
$products = $class::get_products(2, ['include' => [$productA, $productB]]);
$articles = $class::get_articles(2, ['post__in' => [$articleA, $articleB], 'orderby' => 'post__in']);
if ($projects !== [$projectA, $projectB]) {
    fwrite(STDERR, "FAIL project query did not honor the bounded WP_Query selection\n");
    $failed++;
}
if (count(array_intersect([$productA, $productB], $products)) !== 2) {
    fwrite(STDERR, "FAIL WooCommerce/fallback product query omitted QA products\n");
    $failed++;
}
if (!function_exists('wc_get_products')) {
    $GLOBALS['es_homepage_test_wc_featured'] = [];
    $GLOBALS['es_homepage_test_wc_products'] = [$productA, $productB];
    $GLOBALS['es_homepage_test_wc_calls'] = 0;
    function wc_get_products($args) {
        $GLOBALS['es_homepage_test_wc_calls']++;
        if (!empty($args['featured'])) {
            return $GLOBALS['es_homepage_test_wc_featured'];
        }
        $ids = $GLOBALS['es_homepage_test_wc_products'];
        if (isset($args['include']) && is_array($args['include'])) {
            $ids = array_values(array_intersect($ids, array_map('absint', $args['include'])));
        }
        return array_slice($ids, 0, max(1, (int) ($args['limit'] ?? 6)));
    }
    $class::invalidate_cache();
    $wooProducts = $class::get_products(2);
    if ($wooProducts !== [$productA, $productB] || $GLOBALS['es_homepage_test_wc_calls'] < 2) {
        fwrite(STDERR, "FAIL WooCommerce featured query/fallback API was not used\n");
        $failed++;
    }
}
if ($articles !== [$articleA, $articleB]) {
    fwrite(STDERR, "FAIL article query did not honor the bounded WP_Query selection\n");
    $failed++;
}
if (count($class::get_projects(1, ['post__in' => [$projectA, $projectB]])) !== 1) {
    fwrite(STDERR, "FAIL project query ignored its requested result limit\n");
    $failed++;
}
if ($class::get_articles(2, ['post__in' => [999999999]]) !== [] || $class::get_products(2, ['include' => [999999999]]) !== []) {
    fwrite(STDERR, "FAIL empty article/product queries did not return empty lists\n");
    $failed++;
}
$categories = $class::get_product_categories(6);
$categoryIds = array_map(static function ($term): int { return (int) $term->term_id; }, $categories);
if (!in_array($categoryId, $categoryIds, true)) {
    fwrite(STDERR, "FAIL dynamic product category query omitted the QA term\n");
    $failed++;
}
if ($class::get_projects(2, ['post__in' => [999999999]]) !== []) {
    fwrite(STDERR, "FAIL empty project query did not return an empty list\n");
    $failed++;
}

// A cached empty result must be invalidated after a newly published project is saved.
$lateSlug = 'task-52-cache-invalidation';
$lateId = wp_insert_post([
    'post_type' => 'projects', 'post_status' => 'draft', 'post_title' => 'Task 5.2 cache invalidation', 'post_name' => $lateSlug,
]);
$lateId = is_wp_error($lateId) ? 0 : (int) $lateId;
if ($lateId) {
    $filterArgs = ['post__in' => [$lateId]];
    $class::get_projects(1, $filterArgs);
    wp_update_post(['ID' => $lateId, 'post_status' => 'publish']);
    if (!in_array($lateId, $class::get_projects(1, $filterArgs), true)) {
        fwrite(STDERR, "FAIL homepage query cache stayed stale after project publish\n");
        $failed++;
    }
}

$schema = $sectionClass::section_schema((int) $pageId);
if (!isset($schema['clients']['fields']['logos'], $schema['about']['fields']['stats'])) {
    fwrite(STDERR, "FAIL editable client or statistic repeater is missing from the home schema\n");
    $failed++;
}
$sections = $sectionClass::get_renderable_sections((int) $pageId);
foreach ($sections as &$section) {
    if (($section['id'] ?? '') === 'clients') {
        $section['fields']['logos'] = [[
            'image' => '',
            'title' => 'Task 5.2 QA editable client',
            'description' => 'Client data saved in the page repeater',
            'link' => ['label' => '', 'url' => '', 'target_blank' => false],
        ]];
    }
    if (($section['id'] ?? '') === 'about' && isset($section['fields']['stats'][2])) {
        $section['fields']['stats'][2]['source'] = 'clients';
    }
}
unset($section);
update_post_meta((int) $pageId, $sectionClass::META_KEY, $sections);
$GLOBALS['post'] = get_post($pageId);
if ($GLOBALS['post'] instanceof WP_Post) {
    setup_postdata($GLOBALS['post']);
}
if (isset($GLOBALS['wp_query']) && is_object($GLOBALS['wp_query'])) {
    $GLOBALS['wp_query']->queried_object = $GLOBALS['post'];
    $GLOBALS['wp_query']->queried_object_id = (int) $pageId;
}
ob_start();
include $root . '/front-page.php';
$rendered = (string) ob_get_clean();
wp_reset_postdata();

foreach ([
    'Task 5.2 QA project alpha', 'Task 5.2 QA project beta',
    'Task 5.2 QA product alpha', 'Task 5.2 QA product beta',
    'Task 5.2 QA article alpha', 'Task 5.2 QA article beta',
    'Task 5.2 QA category', 'Task 5.2 QA editable client',
    'data-home1-categories-grid', 'data-home2-port-tab',
] as $needle) {
    if (strpos($rendered, $needle) === false) {
        fwrite(STDERR, "FAIL rendered homepage missing dynamic output `{$needle}`\n");
        $failed++;
    }
}
foreach (['<main>', '</main>', 'wp-content'] as $needle) {
    if (strpos($rendered, $needle) === false) {
        fwrite(STDERR, "FAIL expected homepage shell/content marker missing: {$needle}\n");
        $failed++;
    }
}
foreach ([
    $class::get_published_count('projects'),
    $class::get_published_count('product'),
    $class::get_configured_client_count((int) $pageId),
] as $dynamicCount) {
    if (strpos($rendered, 'data-counter-target="' . absint($dynamicCount) . '"') === false) {
        fwrite(STDERR, "FAIL homepage statistics did not use the current published/configured count\n");
        $failed++;
    }
}
if (strpos($rendered, 'Lorem ipsum') !== false || strpos($rendered, 'Coming soon') !== false) {
    fwrite(STDERR, "FAIL dynamic homepage emitted placeholder content\n");
    $failed++;
}

if (0 === $failed) {
    fwrite(STDOUT, "PASS Task 5.2 cached queries, shared cards, editable clients, and dynamic homepage integration\n");
}
exit($failed === 0 ? 0 : 1);
