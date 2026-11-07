<?php
/**
 * WordPress integration contract for task 7.1: project detail and project archives.
 * Run through PHP WASM or native PHP with WP_TEST_ROOT set.
 */
$root = dirname(__DIR__, 3);
$contracts = [
    ['single-projects.php', 'get_project_gallery_ids'],
    ['single-projects.php', 'finish-pro-time'],
    ['single-projects.php', 'mortabet_mahsole_2'],
    ['single-projects.php', 'data-gallery-root="true"'],
    ['single-projects.php', 'the_content()'],
    ['archive-projects.php', 'template-parts/projects/archive'],
    ['taxonomy-project_type.php', 'template-parts/projects/archive'],
    ['taxonomy-city.php', 'template-parts/projects/archive'],
    ['template-parts/projects/archive.php', 'get_the_posts_pagination'],
    ['template-parts/projects/archive.php', 'data-projects-archive="true"'],
    ['template-parts/projects/archive.php', 'data-project-item="true"'],
    ['template-parts/projects/archive.php', 'data-project-grid="true"'],
    ['template-parts/projects/archive.php', 'taxonomy' . "'" . ' =>'],
    ['assets/js/main.js', 'data-projects-archive="true"'],
];
$failed = 0;
foreach ($contracts as [$relative, $needle]) {
    $path = $root . '/' . $relative;
    $source = is_file($path) ? file_get_contents($path) : false;
    if ($source === false || strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL project template contract missing `{$needle}` in {$relative}\n");
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
$_SERVER['REQUEST_URI'] = '/projects/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

if (!post_type_exists('product')) {
    register_post_type('product', ['public' => true, 'has_archive' => true, 'rewrite' => ['slug' => 'product'], 'supports' => ['title', 'editor', 'thumbnail']]);
}
if (!post_type_exists('projects')) {
    register_post_type('projects', ['public' => true, 'has_archive' => true, 'rewrite' => ['slug' => 'projects'], 'supports' => ['title', 'editor', 'excerpt', 'thumbnail']]);
}
foreach (['project_type', 'city'] as $taxonomy) {
    if (!taxonomy_exists($taxonomy)) {
        register_taxonomy($taxonomy, ['projects'], ['public' => true, 'hierarchical' => true, 'show_in_rest' => true, 'rewrite' => ['slug' => $taxonomy]]);
    }
}

$createdPosts = [];
$createdTerms = [];
$attachmentIds = [];
$prefix = 'task-71-' . strtolower(wp_generate_password(7, false, false));
$makeTerm = static function (string $taxonomy, string $suffix) use (&$createdTerms, $prefix) {
    $term = wp_insert_term('QA ' . $suffix . ' ' . $prefix, $taxonomy, ['slug' => $prefix . '-' . $suffix]);
    if (is_wp_error($term)) {
        return false;
    }
    $createdTerms[] = [(int) $term['term_id'], $taxonomy];
    return get_term((int) $term['term_id'], $taxonomy);
};
$typeTerm = $makeTerm('project_type', 'type');
$otherTypeTerm = $makeTerm('project_type', 'other');
$cityTerm = $makeTerm('city', 'city');
if (!$typeTerm instanceof WP_Term || !$otherTypeTerm instanceof WP_Term || !$cityTerm instanceof WP_Term) {
    fwrite(STDERR, "FAIL could not create project taxonomy fixtures\n");
    exit(1);
}

$makeAttachment = static function (string $suffix) use (&$createdPosts, &$attachmentIds, $prefix): int {
    $id = wp_insert_post([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'post_title' => 'QA image ' . $suffix . ' ' . $prefix,
        'post_mime_type' => 'image/jpeg',
        'guid' => 'https://example.test/uploads/' . $prefix . '-' . $suffix . '.jpg',
    ]);
    if (is_wp_error($id) || !$id) {
        return 0;
    }
    $id = (int) $id;
    $createdPosts[] = $id;
    $attachmentIds[] = $id;
    update_post_meta($id, '_wp_attached_file', $prefix . '-' . $suffix . '.jpg');
    update_post_meta($id, '_wp_attachment_metadata', ['file' => $prefix . '-' . $suffix . '.jpg', 'width' => 800, 'height' => 600, 'sizes' => []]);
    return $id;
};
$coverImageId = $makeAttachment('cover');
$galleryImageId = $makeAttachment('gallery');
if (!$coverImageId || !$galleryImageId) {
    fwrite(STDERR, "FAIL could not create attachment fixtures\n");
    exit(1);
}

$makeProject = static function (string $suffix, string $title, string $content = '') use (&$createdPosts, $prefix): int {
    $id = wp_insert_post([
        'post_type' => 'projects',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $prefix . '-' . $suffix,
        'post_content' => $content,
        'post_excerpt' => $content,
    ]);
    if (is_wp_error($id) || !$id) {
        return 0;
    }
    $createdPosts[] = (int) $id;
    return (int) $id;
};
$projectA = $makeProject('main', 'QA Project Alpha ' . $prefix, 'Dynamic project description from WordPress content.');
$projectB = $makeProject('related', 'QA Project Related ' . $prefix, 'Related project content.');
$projectC = $makeProject('other', 'QA Project Other ' . $prefix, 'Other taxonomy archive entry.');
$productId = wp_insert_post(['post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'QA Used Product ' . $prefix, 'post_name' => $prefix . '-product', 'post_content' => 'Test product description']);
if (is_wp_error($productId) || !$projectA || !$projectB || !$projectC) {
    fwrite(STDERR, "FAIL could not create project/product post fixtures\n");
    exit(1);
}
$productId = (int) $productId;
$createdPosts[] = $productId;
foreach ([$projectA, $projectB] as $projectId) {
    wp_set_object_terms($projectId, [(int) $typeTerm->term_id], 'project_type');
    wp_set_object_terms($projectId, [(int) $cityTerm->term_id], 'city');
}
wp_set_object_terms($projectC, [(int) $otherTypeTerm->term_id], 'project_type');
set_post_thumbnail($projectA, $coverImageId);
update_post_meta($projectA, '_es_project_gallery', [$galleryImageId]);
update_post_meta($projectA, 'finish-pro-time', '1404/02/12');
update_post_meta($projectA, 'mortabet_mahsole_2', wp_json_encode([$productId]));
update_post_meta($projectA, 'foure-image', $coverImageId);

$renderTemplate = static function (string $template, array $queryArgs, $queriedObject = null, string $context = 'archive'): string {
    global $wp_query, $wp_the_query, $post;
    $wp_query = new WP_Query($queryArgs);
    $wp_the_query = $wp_query;
    $post = null;
    if ($queriedObject instanceof WP_Term) {
        $wp_query->queried_object = $queriedObject;
        $wp_query->queried_object_id = (int) $queriedObject->term_id;
        $wp_query->is_tax = true;
        $wp_query->is_archive = true;
        $wp_query->is_singular = false;
        $wp_query->query_vars['taxonomy'] = $queriedObject->taxonomy;
        $wp_query->query_vars['term'] = $queriedObject->slug;
    } elseif ('single' === $context) {
        $wp_query->is_single = true;
        $wp_query->is_singular = true;
        $wp_query->is_archive = false;
        $wp_query->queried_object = get_post((int) ($queryArgs['p'] ?? 0));
        $wp_query->queried_object_id = (int) ($queryArgs['p'] ?? 0);
    } else {
        $wp_query->is_post_type_archive = true;
        $wp_query->is_archive = true;
        $wp_query->is_singular = false;
        $wp_query->queried_object = get_post_type_object('projects');
    }
    ob_start();
    include dirname(__DIR__, 3) . '/' . $template;
    $output = (string) ob_get_clean();
    wp_reset_postdata();
    return $output;
};

$singleHtml = $renderTemplate('single-projects.php', ['post_type' => 'projects', 'p' => $projectA, 'post_status' => 'publish'], null, 'single');
foreach (['QA Project Alpha ' . $prefix, '1404/02/12', $typeTerm->name, $cityTerm->name, 'data-gallery-root="true"', 'used-products', 'QA Used Product ' . $prefix, 'Dynamic project description from WordPress content.'] as $needle) {
    if (strpos($singleHtml, $needle) === false) {
        fwrite(STDERR, "FAIL single project output is missing dynamic detail: {$needle}\n");
        $failed++;
    }
}
if (strpos($singleHtml, 'javascript:') !== false) {
    fwrite(STDERR, "FAIL single project rendered unsafe fixture content\n");
    $failed++;
}

$archiveHtml = $renderTemplate('archive-projects.php', [
    'post_type' => 'projects', 'post_status' => 'publish', 'post__in' => [$projectA, $projectB, $projectC],
    'posts_per_page' => 1, 'paged' => 1, 'orderby' => 'post__in',
]);
foreach (['data-projects-archive="true"', 'data-project-grid="true"', 'data-project-item="true"', 'QA Project Alpha ' . $prefix, 'page-numbers', 'next page'] as $needle) {
    if (strpos($archiveHtml, $needle) === false) {
        fwrite(STDERR, "FAIL project archive output is missing dynamic loop/pagination contract: {$needle}\n");
        $failed++;
    }
}

$typeHtml = $renderTemplate('taxonomy-project_type.php', [
    'post_type' => 'projects', 'post_status' => 'publish', 'project_type' => $typeTerm->slug, 'posts_per_page' => 10,
], $typeTerm);
foreach ([$typeTerm->name, 'data-initial-type="' . esc_attr($typeTerm->name) . '"', 'QA Project Alpha ' . $prefix, 'QA Project Related ' . $prefix] as $needle) {
    if (strpos($typeHtml, $needle) === false) {
        fwrite(STDERR, "FAIL project type archive did not render its term-filtered projects: {$needle}\n");
        $failed++;
    }
}
if (strpos($typeHtml, 'QA Project Other ' . $prefix) !== false) {
    fwrite(STDERR, "FAIL project type archive leaked an unrelated project\n");
    $failed++;
}

$cityHtml = $renderTemplate('taxonomy-city.php', [
    'post_type' => 'projects', 'post_status' => 'publish', 'city' => $cityTerm->slug, 'posts_per_page' => 10,
], $cityTerm);
foreach ([$cityTerm->name, 'data-initial-location="' . esc_attr($cityTerm->name) . '"', 'QA Project Alpha ' . $prefix, 'QA Project Related ' . $prefix] as $needle) {
    if (strpos($cityHtml, $needle) === false) {
        fwrite(STDERR, "FAIL city archive did not render its term-filtered projects: {$needle}\n");
        $failed++;
    }
}
if (strpos($cityHtml, 'QA Project Other ' . $prefix) !== false) {
    fwrite(STDERR, "FAIL city archive leaked a project assigned to another city/type\n");
    $failed++;
}

foreach (array_reverse($createdPosts) as $postId) {
    wp_delete_post($postId, true);
}
foreach (array_reverse($createdTerms) as [$termId, $taxonomy]) {
    wp_delete_term($termId, $taxonomy);
}

if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS project detail gallery/meta/related products, dynamic archive cards, project type and city queries, native pagination, escaping, and empty-data handling\n");
