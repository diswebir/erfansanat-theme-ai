<?php
/**
 * taxonomy-product_cat.php — product category archive (slug: /product_cat/...).
 * Shows category thumbnail, description and the category FAQ (term meta `faq`),
 * then the product grid.
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }

get_header();
$term = get_queried_object();
$term = ($term && !is_wp_error($term)) ? $term : null;
$thumbId = $term ? get_term_meta($term->term_id, 'thumbnail', true) : 0;
$faqRaw  = $term ? get_term_meta($term->term_id, 'faq', true) : [];
$faqRaw  = is_string($faqRaw) ? (json_decode($faqRaw, true) ?: maybe_unserialize($faqRaw)) : (array) $faqRaw;
?>
<main id="main" class="site-main">
    <section class="relative overflow-hidden bg-[#f6f8f7] pb-10 pt-28 lg:pt-36">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <?php if ($thumbId) : ?>
                <img src="<?php echo esc_url(wp_get_attachment_image_url($thumbId, 'large')); ?>" alt="<?php echo esc_attr($term->name); ?>" class="mb-4 h-28 w-28 rounded-3xl object-cover" />
            <?php endif; ?>
            <h1 class="text-2xl font-black text-slate-900 sm:text-3xl lg:text-4xl"><?php echo esc_html($term ? $term->name : ''); ?></h1>
            <?php if ($term && $term->description) : ?><div class="mt-3 max-w-3xl text-sm leading-relaxed text-slate-600"><?php echo wp_kses_post($term->description); ?></div><?php endif; ?>
        </div>
    </section>

    <?php if ($faqRaw) : ?>
    <section class="mx-auto max-w-7xl px-5 py-10 lg:px-8" data-faq="true">
        <h2 class="text-xl font-black text-slate-900"><?php esc_html_e('سوالات متداول این دسته', 'erfansanat'); ?></h2>
        <div class="mt-4 divide-y divide-slate-100 rounded-3xl border border-slate-200 bg-white">
            <?php foreach ($faqRaw as $row) : if (is_array($row) && !empty($row['name_question'])) : ?>
                <details class="group p-4">
                    <summary class="cursor-pointer text-sm font-black text-slate-900"><?php echo esc_html($row['name_question']); ?></summary>
                    <p class="mt-2 text-sm leading-relaxed text-slate-600"><?php echo esc_html($row['answer_question'] ?? ''); ?></p>
                </details>
            <?php endif; endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="mx-auto max-w-7xl px-5 py-12 lg:px-8">
        <?php if (have_posts()) : ?>
            <div class="grid gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <?php while (have_posts()) : the_post(); get_template_part('template-parts/cards/card', 'product'); endwhile; ?>
            </div>
            <?php the_posts_pagination(); ?>
        <?php else : ?>
            <p class="text-sm font-bold text-slate-500"><?php esc_html_e('محصولی در این دسته یافت نشد.', 'erfansanat'); ?></p>
        <?php endif; ?>
    </section>
</main>
<?php get_footer();
