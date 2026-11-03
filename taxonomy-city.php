<?php
/**
 * taxonomy-city.php — city archive (/city/...) with project count.
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }
get_header();
$term = get_queried_object();
$term = ($term && !is_wp_error($term)) ? $term : null;
?>
<main id="main" class="site-main">
    <section class="relative overflow-hidden bg-[#f6f8f7] pb-10 pt-28 lg:pt-36">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <h1 class="text-2xl font-black text-slate-900 sm:text-3xl"><?php echo esc_html(sprintf(__('پروژه‌های %s', 'erfansanat'), single_term_title('', false))); ?></h1>
            <p class="mt-2 text-sm font-bold text-emerald-700"><?php echo esc_html(sprintf(_n('%s پروژه', '%s پروژه', (int) ($term->count ?? 0), 'erfansanat'), number_format_i18n((int) ($term->count ?? 0)))); ?></p>
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
        <?php if (have_posts()) : ?>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <?php while (have_posts()) : the_post(); get_template_part('template-parts/cards/card', 'project'); endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p class="text-sm font-bold text-slate-500"><?php esc_html_e('پروژه‌ای در این شهر یافت نشد.', 'erfansanat'); ?></p>
        <?php endif; ?>
    </section>
</main>
<?php get_footer();
