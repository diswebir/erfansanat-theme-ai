<?php
/**
 * taxonomy-project_type.php — project type archive (/project_type/...).
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }
get_header();
$term = get_queried_object();
?>
<main id="main" class="site-main">
    <section class="relative overflow-hidden bg-[#f6f8f7] pb-10 pt-28 lg:pt-36">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <h1 class="text-2xl font-black text-slate-900 sm:text-3xl"><?php echo esc_html(single_term_title('', false)); ?></h1>
            <?php if ($term && $term->description) : ?><p class="mt-2 max-w-3xl text-sm text-slate-600"><?php echo esc_html($term->description); ?></p><?php endif; ?>
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
        <?php if (have_posts()) : ?>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php while (have_posts()) : the_post(); get_template_part('template-parts/cards/card', 'project'); endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p class="text-sm font-bold text-slate-500"><?php esc_html_e('پروژه‌ای در این دسته یافت نشد.', 'erfansanat'); ?></p>
        <?php endif; ?>
    </section>
</main>
<?php get_footer();
