<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header(); do_action('before_project_content');
$term = get_queried_object();
$GLOBALS['es_project_archive_term'] = $term instanceof WP_Term ? $term : null;
$GLOBALS['es_project_archive_query'] = $GLOBALS['wp_query'] ?? null;
?>
<main id="primary" class="site-main"><div class="relative z-10"><?php get_template_part('template-parts/project/archive-grid'); ?></div></main>
<?php unset($GLOBALS['es_project_archive_term'], $GLOBALS['es_project_archive_query']); do_action('after_project_content'); get_footer(); ?>
