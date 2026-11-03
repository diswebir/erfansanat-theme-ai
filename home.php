<?php
/**
 * home.php — blog index (from static blog.html).
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }
get_header();
?>
<main id="main" class="site-main">
    <section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <h1 class="text-2xl font-black text-slate-900 sm:text-3xl lg:text-4xl"><?php esc_html_e('وبلاگ و مقالات', 'erfansanat'); ?></h1>
        </div>
    </section>
    <section class="bg-white py-14 lg:py-16">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <?php if (have_posts()) : ?>
                <div class="grid gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <?php while (have_posts()) : the_post(); get_template_part('template-parts/cards/card', 'post'); endwhile; ?>
                </div>
                <?php the_posts_pagination(); ?>
            <?php else : ?>
                <p class="text-sm font-bold text-slate-500"><?php esc_html_e('مقاله‌ای یافت نشد.', 'erfansanat'); ?></p>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php get_footer();
