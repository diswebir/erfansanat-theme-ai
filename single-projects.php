<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
do_action('before_project_content');
?>
<main id="primary" class="site-main mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8"><div class="relative z-10">
<?php while (have_posts()) : the_post(); $GLOBALS['es_project_id'] = (int) get_the_ID(); ?>
<article id="project-<?php the_ID(); ?>" <?php post_class('project-page'); ?>>
<?php get_template_part('template-parts/project/header'); get_template_part('template-parts/project/gallery-info'); get_template_part('template-parts/project/content-related'); ?>
</article>
<?php endwhile; unset($GLOBALS['es_project_id']); ?>
</div></main>
<?php do_action('after_project_content'); get_footer(); ?>
