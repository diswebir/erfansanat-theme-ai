<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
?>
<main id="primary" class="site-main"><article class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
<?php while (have_posts()) : the_post(); get_template_part('template-parts/post/header'); ?>
<?php if (has_post_thumbnail()) : ?><figure class="mb-8 overflow-hidden rounded-3xl"><?php the_post_thumbnail('large', ['class' => 'h-auto w-full object-cover']); ?></figure><?php endif; ?>
<?php get_template_part('template-parts/post/toc'); ?>
<div class="prose prose-slate max-w-none rounded-3xl bg-white p-6 leading-8 md:p-9"><?php the_content(); ?></div>
<?php get_template_part('template-parts/post/share-related-comments'); ?>
<?php endwhile; ?>
</article></main>
<?php get_footer(); ?>
