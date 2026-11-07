<?php
/** WordPress integration contract for task 7.2: blog, search, archives, views and 404. */
$root = dirname(__DIR__, 3);
$contracts = [
    ['home.php', 'template-parts/blog/archive'],
    ['archive.php', 'template-parts/blog/archive'],
    ['category.php', 'template-parts/blog/archive'],
    ['template-parts/blog/archive.php', 'the_posts_pagination'],
    ['single.php', 'get_view_count'],
    ['single.php', 'فهرست مطالب'],
    ['single.php', "apply_filters('the_content'"],
    ['single.php', 'comments_template()'],
    ['single.php', 'twitter.com/intent/tweet'],
    ['search.php', 'get_search_query'],
    ['search.php', 'name="s"'],
    ['404.php', 'name="s"'],
    ['inc/Admin/ContentMeta.php', 'record_public_view'],
    ['inc/Admin/ContentMeta.php', 'crawler|spider'],
];
$failed = 0;
foreach ($contracts as [$relative, $needle]) {
    $path = $root . '/' . $relative;
    $source = is_file($path) ? file_get_contents($path) : false;
    if ($source === false || strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL blog template contract missing `{$needle}` in {$relative}\n");
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
$_SERVER['REQUEST_URI'] = '/blog/';
$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '192.0.2.71';
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 Task 7.2 Integration Test';
require $wpRoot . '/wp-load.php';
wp_set_current_user(1);

$prefix = 'task-72-' . strtolower(wp_generate_password(7, false, false));
$category = wp_insert_term('QA Category ' . $prefix, 'category', ['slug' => $prefix]);
if (is_wp_error($category)) {
    fwrite(STDERR, "FAIL could not create blog category fixture\n");
    exit(1);
}
$categoryId = (int) $category['term_id'];
$createdPosts = [];
$makePost = static function (string $slug, string $title, string $content, array $extra = []) use (&$createdPosts, $prefix, $categoryId): int {
    $id = wp_insert_post(array_merge([
        'post_type' => 'post',
        'post_status' => 'publish',
        'post_title' => $title,
        'post_name' => $prefix . '-' . $slug,
        'post_content' => $content,
        'post_excerpt' => 'Excerpt for ' . $title,
        'comment_status' => 'open',
        'post_category' => [$categoryId],
    ], $extra));
    if (is_wp_error($id) || !$id) {
        return 0;
    }
    $createdPosts[] = (int) $id;
    return (int) $id;
};
$mainPost = $makePost('article', 'QA Blog Article ' . $prefix, '<h2>راهنمای آغاز</h2><p>Blog content with <strong>safe markup</strong>.</p><h3>جزئیات فنی</h3><p>Useful engineering information.</p>');
$relatedPost = $makePost('related', 'QA Related Article ' . $prefix, '<p>Related reading content.</p>');
$otherPost = wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'QA Searchable ' . $prefix, 'post_name' => $prefix . '-search', 'post_content' => 'Unique search token ' . $prefix]);
if (is_wp_error($otherPost) || !$mainPost || !$relatedPost) {
    fwrite(STDERR, "FAIL could not create blog post fixtures\n");
    exit(1);
}
$otherPost = (int) $otherPost;
$createdPosts[] = $otherPost;

$metaClass = 'ErfanSanat\\Theme\\Admin\\ContentMeta';
update_post_meta($mainPost, 'number-viewc', '8');
$initialViews = $metaClass::get_view_count($mainPost);
wp_set_current_user(0);
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (compatible; Human Test Visitor)';
$_SERVER['REMOTE_ADDR'] = '192.0.2.72';
$_COOKIE = [];
$firstView = $metaClass::record_public_view($mainPost);
$duplicateView = $metaClass::record_public_view($mainPost);
if (!$firstView || $duplicateView || $initialViews !== 8 || $metaClass::get_view_count($mainPost) !== 9) {
    fwrite(STDERR, "FAIL safe view counting did not increment exactly once from the legacy fallback\n");
    $failed++;
}
$secondPost = $makePost('bot', 'QA Bot Counter ' . $prefix, 'Bot counter test.');
$_SERVER['HTTP_USER_AGENT'] = 'Googlebot/2.1';
if ($metaClass::record_public_view($secondPost) || $metaClass::get_view_count($secondPost) !== 0) {
    fwrite(STDERR, "FAIL crawler visit was counted\n");
    $failed++;
}
wp_set_current_user(1);
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 Human Editor';
$editorPost = $makePost('editor', 'QA Editor Counter ' . $prefix, 'Editor counter test.');
if ($metaClass::record_public_view($editorPost) || $metaClass::get_view_count($editorPost) !== 0) {
    fwrite(STDERR, "FAIL editor preview visit was counted\n");
    $failed++;
}

$renderTemplate = static function (string $template, array $queryArgs, string $context = 'archive', int $queriedId = 0): string {
    global $wp_query, $wp_the_query, $post;
    $wp_query = new WP_Query($queryArgs);
    $wp_the_query = $wp_query;
    $post = null;
    if ('single' === $context) {
        $wp_query->is_single = true;
        $wp_query->is_singular = true;
        $wp_query->is_archive = false;
        $wp_query->queried_object = get_post($queriedId);
        $wp_query->queried_object_id = $queriedId;
    } elseif ('search' === $context) {
        $wp_query->is_search = true;
        $wp_query->is_archive = false;
        $wp_query->query_vars['s'] = (string) ($queryArgs['s'] ?? '');
    } elseif ('category' === $context) {
        $wp_query->is_category = true;
        $wp_query->is_archive = true;
        $wp_query->queried_object = get_term((int) ($queryArgs['cat'] ?? 0), 'category');
    } else {
        $wp_query->is_home = true;
        $wp_query->is_archive = true;
    }
    ob_start();
    if ('404' === $template) {
        include dirname(__DIR__, 3) . '/404.php';
    } else {
        include dirname(__DIR__, 3) . '/' . $template;
    }
    $output = (string) ob_get_clean();
    wp_reset_postdata();
    return $output;
};

$singleHtml = $renderTemplate('single.php', ['post_type' => 'post', 'p' => $mainPost, 'post_status' => 'publish'], 'single', $mainPost);
foreach (['QA Blog Article ' . $prefix, 'راهنمای آغاز', 'جزئیات فنی', 'مقالات مرتبط', 'QA Related Article ' . $prefix, 'comments', 'twitter.com/intent/tweet'] as $needle) {
    if (strpos($singleHtml, $needle) === false) {
        fwrite(STDERR, "FAIL single post did not render requested article feature: {$needle}\n");
        $failed++;
    }
}
if (!preg_match('~<a href="#([^"]+)"[^>]*>راهنمای آغاز</a>~u', $singleHtml, $tocMatch) || strpos($singleHtml, 'id="' . ($tocMatch[1] ?? '') . '"') === false) {
    fwrite(STDERR, "FAIL table-of-contents links do not point to their generated heading IDs\n");
    $failed++;
}

$homeHtml = $renderTemplate('home.php', ['post_type' => 'post', 'post_status' => 'publish', 'post__in' => [$mainPost, $relatedPost], 'posts_per_page' => 1, 'orderby' => 'post__in']);
if (strpos($homeHtml, 'QA Blog Article ' . $prefix) === false || strpos($homeHtml, 'page-numbers') === false) {
    fwrite(STDERR, "FAIL blog index did not render WordPress query cards and native pagination\n");
    $failed++;
}
$genericArchiveHtml = $renderTemplate('archive.php', ['post_type' => 'post', 'post_status' => 'publish', 'post__in' => [$mainPost, $relatedPost], 'posts_per_page' => 2, 'orderby' => 'post__in']);
if (strpos($genericArchiveHtml, 'QA Blog Article ' . $prefix) === false || strpos($genericArchiveHtml, 'QA Related Article ' . $prefix) === false) {
    fwrite(STDERR, "FAIL generic archive did not render dynamic WordPress posts\n");
    $failed++;
}
$categoryHtml = $renderTemplate('category.php', ['post_type' => 'post', 'cat' => $categoryId, 'post_status' => 'publish', 'posts_per_page' => 10], 'category');
if (strpos($categoryHtml, $categoryId ? 'QA Blog Article ' . $prefix : '') === false || strpos($categoryHtml, 'QA Related Article ' . $prefix) === false) {
    fwrite(STDERR, "FAIL category archive did not display posts in the selected WordPress category\n");
    $failed++;
}
$searchHtml = $renderTemplate('search.php', ['post_type' => ['post', 'projects', 'product'], 's' => $prefix, 'post_status' => 'publish', 'posts_per_page' => 10], 'search');
if (strpos($searchHtml, $prefix) === false || strpos($searchHtml, 'QA Searchable ' . $prefix) === false || strpos($searchHtml, 'name="s"') === false) {
    fwrite(STDERR, "FAIL search results did not render query, results, and an accessible search form\n");
    $failed++;
}
$notFoundHtml = $renderTemplate('404', [], '404');
if (strpos($notFoundHtml, '۴۰۴') === false || strpos($notFoundHtml, 'name="s"') === false || strpos($notFoundHtml, 'صفحه‌ای که دنبالش هستید پیدا نشد') === false) {
    fwrite(STDERR, "FAIL 404 page is missing useful navigation/search content\n");
    $failed++;
}

foreach (array_reverse($createdPosts) as $postId) {
    wp_delete_post($postId, true);
}
wp_delete_term($categoryId, 'category');

if ($failed !== 0) {
    exit(1);
}
fwrite(STDOUT, "PASS blog index/category/search/404, generated TOC, share links, related posts/comments, and bot-safe deduplicated view counting\n");
