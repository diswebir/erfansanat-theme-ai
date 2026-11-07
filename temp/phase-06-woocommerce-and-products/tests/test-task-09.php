<?php
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
require_once __DIR__ . '/support.php';
$projects = es_p6_source( 'template-parts/product/related-projects.php' );
$usage = es_p6_source( 'template-parts/product/usage.php' );
check( 'project-ids با WP_Query از نوع projects و ترتیب post__in خوانده می‌شود', fn() => es_p6_has_all( $projects, array( "'project-ids'", "'post_type' => 'projects'", '\'post__in\' => $ids', '\'orderby\' => \'post__in\'' ) ) );
check( 'ID نامعتبر/ناموجود و رابطه خالی حذف می‌شوند', fn() => es_p6_has_all( $projects, array( 'if ( $id > 0', '\'publish\' === ( $post->post_status', 'if ( ! $projects ) { return; }' ) ) );
check( 'کارت پروژه مشترک و کاربردهای usage_countr به project_type لینک می‌شوند', fn() => str_contains( $projects, 'template-parts/cards/card-project' ) && es_p6_has_all( $usage, array( "'usage_countr'", 'get_term( $term_id, \'project_type\' )', 'get_term_link( $term )' ) ) );
summary();
