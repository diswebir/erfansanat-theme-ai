<?php
/**
 * single-projects.php — project case-study page. Connects gallery meta
 * (second-image..six-image incl. legacy `foure-image`), finish-pro-time,
 * city, project_type and related products (mortabet_mahsole_2).
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }

get_header();
while (have_posts()) : the_post();
    $pid      = get_the_ID();
    $images   = [];
    foreach (['second-image', 'three-image', 'foure-image', 'five-image', 'six-image'] as $k) {
        $v = get_post_meta($pid, $k, true);
        if ($v) { $images[] = absint($v); }
    }
    $finish   = get_post_meta($pid, 'finish-pro-time', true);
    $cities   = get_the_terms($pid, 'city');
    $types    = get_the_terms($pid, 'project_type');
    $prodIds  = get_post_meta($pid, 'mortabet_mahsole_2', true);
    $prodIds  = is_string($prodIds) ? (json_decode($prodIds, true) ?: []) : (array) $prodIds;
?>
<main id="main" class="site-main">
    <section class="relative bg-[#f6f8f7] pt-28 pb-10 sm:pt-32 lg:pt-36">
        <div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
            <h1 class="text-2xl font-black text-slate-900 sm:text-3xl lg:text-4xl"><?php the_title(); ?></h1>
            <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold">
                <?php if ($cities && !is_wp_error($cities)) : foreach ($cities as $c) : ?>
                    <a href="<?php echo esc_url(get_term_link($c)); ?>" class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700"><?php echo esc_html($c->name); ?></a>
                <?php endforeach; endif; ?>
                <?php if ($types && !is_wp_error($types)) : foreach ($types as $t) : ?>
                    <a href="<?php echo esc_url(get_term_link($t)); ?>" class="rounded-full bg-slate-100 px-3 py-1 text-slate-600"><?php echo esc_html($t->name); ?></a>
                <?php endforeach; endif; ?>
                <?php if ($finish) : ?><span class="rounded-full bg-white px-3 py-1 text-slate-500"><?php echo esc_html__('تاریخ تکمیل: ', 'erfansanat') . esc_html($finish); ?></span><?php endif; ?>
            </div>
        </div>
    </section>

    <?php if (has_post_thumbnail() || $images) : ?>
    <section class="py-10" data-gallery="true">
        <div class="mx-auto grid max-w-[1300px] gap-4 px-4 sm:grid-cols-2 sm:px-6 lg:px-8">
            <?php if (has_post_thumbnail()) : ?>
                <img src="<?php echo esc_url(get_the_post_thumbnail_url($pid, 'large')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="h-72 w-full rounded-3xl object-cover" />
            <?php endif; ?>
            <?php foreach ($images as $imgId) : ?>
                <img src="<?php echo esc_url(wp_get_attachment_image_url($imgId, 'large')); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="h-64 w-full rounded-3xl object-cover" loading="lazy" />
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="pb-10">
        <div class="mx-auto max-w-[1300px] px-4 prose sm:px-6 lg:px-8"><?php the_content(); ?></div>
    </section>

    <?php if ($prodIds) : ?>
    <section class="py-10">
        <div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-black text-slate-900"><?php esc_html_e('محصولات استفاده‌شده در این پروژه', 'erfansanat'); ?></h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <?php
                $pq = new WP_Query(['post_type' => 'product', 'post__in' => array_map('absint', $prodIds), 'posts_per_page' => -1, 'no_found_rows' => true]);
                while ($pq->have_posts()) : $pq->the_post();
                    get_template_part('template-parts/cards/card', 'product');
                endwhile; wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="pb-16"><?php comments_template(); ?></section>
</main>
<?php endwhile; ?>
<?php get_footer();
