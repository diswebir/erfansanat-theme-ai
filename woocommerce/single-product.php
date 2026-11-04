<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
do_action('woocommerce_before_main_content');
?>
<main id="primary" class="site-main mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8" itemscope itemtype="https://schema.org/Product">
    <?php while (have_posts()) : the_post(); $GLOBALS['es_product_id'] = (int) get_the_ID(); ?>
        <?php do_action('woocommerce_before_single_product'); ?>
        <article id="product-<?php the_ID(); ?>" <?php post_class('product-page'); ?>>
            <?php get_template_part('template-parts/product/header'); ?>
            <section class="grid gap-8 lg:grid-cols-2" aria-label="اطلاعات محصول">
                <?php get_template_part('template-parts/product/gallery'); ?>
                <div class="space-y-5">
                    <?php get_template_part('template-parts/product/key-features'); ?>
                    <?php if ((string) get_post_meta(get_the_ID(), 'true_sale', true) === 'false') : ?>
                        <?php get_template_part('template-parts/product/inquiry-panel'); ?>
                    <?php else : ?>
                        <?php get_template_part('template-parts/product/purchase-panel'); ?>
                    <?php endif; ?>
                    <?php get_template_part('template-parts/product/notices'); ?>
                </div>
            </section>
            <section id="product-details" class="mt-12" data-prod-tabs-container="true">
                <div class="mb-5 flex flex-wrap gap-2 border-b border-slate-200" role="tablist">
                    <?php foreach (['intro' => 'معرفی و کاربردها', 'specs' => 'مشخصات فنی', 'reviews' => 'نظرات کاربران'] as $tab => $label) : ?><button type="button" role="tab" data-prod-tab-btn="<?php echo esc_attr($tab); ?>" class="rounded-t-xl px-4 py-3 text-sm font-bold"><?php echo esc_html($label); ?></button><?php endforeach; ?>
                </div>
                <div data-prod-tab-panel="intro"><?php get_template_part('template-parts/product/tab-intro'); ?></div>
                <div data-prod-tab-panel="specs"><?php get_template_part('template-parts/product/tab-specs'); ?></div>
                <div data-prod-tab-panel="reviews"><?php get_template_part('template-parts/product/faq-reviews'); ?></div>
            </section>
            <?php get_template_part('template-parts/product/related-projects'); ?>
            <?php get_template_part('template-parts/product/faq-reviews'); ?>
            <?php get_template_part('template-parts/product/related-products'); ?>
        </article>
        <?php do_action('woocommerce_after_single_product'); ?>
    <?php endwhile; unset($GLOBALS['es_product_id']); ?>
</main>
<?php do_action('woocommerce_after_main_content'); get_footer(); ?>
