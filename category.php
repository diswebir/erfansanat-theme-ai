<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
$term = get_queried_object();
$GLOBALS['es_blog_archive_term'] = $term instanceof WP_Term ? $term : null;
$GLOBALS['es_blog_archive_query'] = $GLOBALS['wp_query'] ?? null;
?>
<?php get_template_part('template-parts/post/archive-grid'); ?>
<?php unset($GLOBALS['es_blog_archive_term'], $GLOBALS['es_blog_archive_query']); get_footer(); ?>
