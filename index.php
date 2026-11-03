<?php
/**
 * Fallback template — every request that has no more specific template
 * lands here. Structure mirrors the static mirror's shared shell.
 *
 * @package ErfanSanat\Theme
 */

get_header();
?>
<main id="main" class="site-main">
    <div class="mx-auto max-w-[1300px] px-4 py-12 sm:px-6 lg:px-8">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <?php get_template_part('template-parts/content', get_post_type()); ?>
            <?php endwhile; ?>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <?php get_template_part('template-parts/content', 'none'); ?>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
