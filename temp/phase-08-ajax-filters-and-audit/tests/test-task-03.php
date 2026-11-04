<?php
/** Automated QA Test Script — Task 8.3: Live project and post search. */
declare(strict_types=1);

$themeRoot = dirname(__DIR__, 3);
$failures = [];
$assert = static function (bool $condition, string $label) use (&$failures): void {
    echo $condition ? "  [PASS] {$label}\n" : "  [FAIL] {$label}\n";
    if (!$condition) $failures[] = $label;
};
require_once '/opt/wp-testbed/wp-load.php';
$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.3: Live project and post search ===\n";

$class = 'ErfanSanat\\Theme\\Ajax\\LiveSearchProjectsPosts';
$assert(is_file($themeRoot . '/inc/Ajax/LiveSearchProjectsPosts.php'), 'LiveSearchProjectsPosts source exists');
$assert(class_exists($class), 'LiveSearchProjectsPosts autoloads');
$service = new $class();
$routes = apply_filters('erfan_sanat_ajax_routes', []);
$assert(isset($routes['content_search']) && is_callable($routes['content_search']), 'content search route is registered');
$script = (string) file_get_contents($themeRoot . '/assets/js/main.js');
$assert(str_contains($script, 'product_search') && str_contains($script, 'content_search'), 'global search client requests both AJAX result groups');
$assert(str_contains($script, 'ajaxNonce') && str_contains($script, 'erfan_sanat_ajax'), 'global search client sends the dedicated nonce and endpoint action');
$assert(str_contains($script, 'searchDebounceTimer') && str_contains($script, '[data-global-search-input]'), 'global search client debounces the existing static search input selector');

$projectId = wp_insert_post(['post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'Bridge Lighting Search QA', 'post_content' => 'Project body']);
$postId = wp_insert_post(['post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'Lighting Journal Search QA', 'post_content' => 'Article body']);
$draftId = wp_insert_post(['post_type' => 'post', 'post_status' => 'draft', 'post_title' => 'Lighting Draft Search QA']);
if (is_wp_error($projectId) || is_wp_error($postId) || is_wp_error($draftId) || !$projectId || !$postId || !$draftId) {
    fwrite(STDERR, "Could not create search fixtures.\n");
    exit(1);
}

$result = $service->search(['query' => 'Search QA']);
$groups = $result['data']['groups'] ?? [];
$assert(in_array($projectId, $groups['projects']['items'] ?? [], true), 'published projects are returned in the projects group');
$assert(in_array($postId, $groups['posts']['items'] ?? [], true), 'published posts are returned in the posts group');
$assert(!in_array($draftId, $groups['posts']['items'] ?? [], true), 'unpublished posts are excluded');
$assert(str_contains($groups['projects']['html'] ?? '', 'data-card-project='), 'projects render with existing project-card markup');
$assert(str_contains($groups['posts']['html'] ?? '', 'data-card-post='), 'posts render with existing post-card markup');
$assert(str_contains($result['html'] ?? '', 'data-search-group="projects"') && str_contains($result['html'] ?? '', 'data-search-group="posts"'), 'combined HTML keeps project and post results visibly grouped');
$assert(($result['count'] ?? 0) >= 2, 'combined result count includes both groups');

$empty = $service->search(['query' => '']);
$assert(($empty['count'] ?? -1) === 0 && ($empty['html'] ?? 'x') === '', 'empty query returns empty groups without warnings');

wp_delete_post($projectId, true);
wp_delete_post($postId, true);
wp_delete_post($draftId, true);
if ($failures !== []) {
    fwrite(STDERR, "[FAILED] " . implode('; ', $failures) . "\n");
    exit(1);
}
echo "[PASSED] Task 8.3 checks completed successfully.\n";
