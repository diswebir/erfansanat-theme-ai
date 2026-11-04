<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.5 project_type and city archives ===\n";
foreach (['project_type' => 'پل شهری QA', 'city' => 'تهران QA'] as $taxonomy => $name) {
    $term = get_term_by('name', $name, $taxonomy);
    if (!$term instanceof WP_Term) { $created = wp_insert_term($name, $taxonomy); if (!is_wp_error($created)) $term = get_term((int) $created['term_id'], $taxonomy); }
    $assert($term instanceof WP_Term, $taxonomy . ' term available');
    if (!$term instanceof WP_Term) continue;
    $term->description = 'راهنمای آرشیو ' . $name;
    $projectId = $makeProject('پروژه ' . $name);
    wp_set_object_terms($projectId, [(int) $term->term_id], $taxonomy);
    $GLOBALS['es_project_archive_term'] = $term;
    $GLOBALS['es_project_archive_query'] = new WP_Query(['post_type' => 'projects', 'post_status' => 'publish', 'tax_query' => [['taxonomy' => $taxonomy, 'field' => 'term_id', 'terms' => [(int) $term->term_id]]]]);
    ob_start(); get_template_part('template-parts/project/archive-grid'); $html = (string) ob_get_clean();
    $assert(str_contains($html, $name) && str_contains($html, 'راهنمای آرشیو ' . $name), $taxonomy . ' term header');
    $selector = $taxonomy === 'city' ? 'data-initial-location="' : 'data-initial-type="';
    $assert(str_contains($html, $selector . esc_attr($name) . '"'), $taxonomy . ' initial filter state');
    wp_delete_post($projectId, true);
}
$assert(is_file($themeRoot . '/taxonomy-project_type.php'), 'project_type archive template exists');
$assert(is_file($themeRoot . '/taxonomy-city.php'), 'city archive template exists');
unset($GLOBALS['es_project_archive_term'], $GLOBALS['es_project_archive_query']); $finishTest();
