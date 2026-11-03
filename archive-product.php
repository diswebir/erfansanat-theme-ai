<?php
/**
 * archive-product.php — shop/catalog archive (from static shop.html/products.html).
 * Renders the product grid with standard cards + pagination.
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }

get_header();
?>
<main id="main" class="site-main">
    <section class="relative overflow-hidden bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-36">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <h1 class="text-2xl font-black text-slate-900 sm:text-3xl lg:text-4xl"><?php post_type_archive_title(); ?></h1>
            <?php if (function_exists('woocommerce_breadcrumb')) { woocommerce_breadcrumb(); } ?>
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">
        <?php if (!function_exists('woocommerce_product_loop') || woocommerce_product_loop()) : ?>
            <div class="grid gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <?php
                while (have_posts()) : the_post();
                    get_template_part('template-parts/cards/card', 'product');
                endwhile;
                ?>
            </div>
            <?php if (function_exists('woocommerce_pagination')) { woocommerce_pagination(); } else { the_posts_pagination(); } ?>
        <?php else : ?>
            <p class="text-sm font-bold text-slate-500"><?php esc_html_e('محصولی یافت نشد.', 'erfansanat'); ?></p>
        <?php endif; ?>
    </section>
</main>
<?php get_footer();
