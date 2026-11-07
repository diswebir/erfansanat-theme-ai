<?php
/**
 * WooCommerce transactional pages use their native page content, blocks, shortcodes, and hooks.
 * Special content pages use editable SectionManager templates; other regular pages retain the theme fallback.
 *
 * @package ErfanSanat\Theme
 */

defined('ABSPATH') || exit;

$woocommercePageContext = '';
if (function_exists('is_cart') && is_cart()) {
    $woocommercePageContext = 'cart';
} elseif (function_exists('is_checkout') && is_checkout()) {
    $woocommercePageContext = 'checkout';
} elseif (function_exists('is_account_page') && is_account_page()) {
    $woocommercePageContext = 'account';
}

if ('' === $woocommercePageContext) {
    $pagePost = get_post(get_queried_object_id());
    $pageSlug = $pagePost instanceof WP_Post ? strtolower(trim(rawurldecode((string) $pagePost->post_name), '/')) : '';
    $specialPageAliases = [
        'درباره-ما' => 'about',
        'تماس-با-ما' => 'contact',
        'سوالات-متداول' => 'faq',
        'پرسش-های-متداول' => 'faq',
        'مجوزها-و-افتخارات' => 'licenses-and-honors',
        'فرصت-های-شغلی' => 'recruitment',
        'شرایط-گارانتی' => 'refund_returns',
        'بازگشت-کالا' => 'refund_returns',
    ];
    $specialPageSlug = $specialPageAliases[$pageSlug] ?? $pageSlug;
    $specialPagePath = [
        'about' => 'page-templates/about.php',
        'contact' => 'page-templates/contact.php',
        'faq' => 'page-templates/faq.php',
        'licenses-and-honors' => 'page-templates/licenses-and-honors.php',
        'recruitment' => 'page-templates/recruitment.php',
        'refund_returns' => 'page-templates/refund_returns.php',
        'arya' => 'page-templates/arya.php',
        'smartelement' => 'page-templates/smartelement.php',
    ];
    if (isset($specialPagePath[$specialPageSlug]) && is_file(get_theme_file_path($specialPagePath[$specialPageSlug]))) {
        require get_theme_file_path($specialPagePath[$specialPageSlug]);
        return;
    }
    require get_theme_file_path('index.php');
    return;
}

get_header();
do_action('woocommerce_before_main_content');
?>
<main id="primary" class="woocommerce-page-shell woocommerce-page-shell--<?php echo esc_attr($woocommercePageContext); ?> mx-auto max-w-6xl px-5 pb-16 pt-10 lg:px-8" data-woocommerce-page="<?php echo esc_attr($woocommercePageContext); ?>" data-woocommerce-cart-url="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/')); ?>">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('woocommerce-page-content'); ?>>
                <header class="woocommerce-page-header mb-7">
                    <nav class="mb-3 text-xs font-semibold text-slate-500" aria-label="<?php echo esc_attr__('مسیر راهنما', 'erfansanat-theme'); ?>"><a class="hover:text-emerald-700" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('صفحه اصلی', 'erfansanat-theme'); ?></a><span class="px-2" aria-hidden="true">/</span><span aria-current="page"><?php echo esc_html(get_the_title()); ?></span></nav>
                    <h1 class="text-2xl font-black leading-relaxed text-slate-950 sm:text-3xl"><?php echo esc_html(get_the_title()); ?></h1>
                </header>
                <div class="woocommerce-page-shortcode-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    <?php else : ?>
        <p class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-600"><?php echo esc_html__('محتوای این صفحه در دسترس نیست.', 'erfansanat-theme'); ?></p>
    <?php endif; ?>
</main>
<?php
do_action('woocommerce_after_main_content');
get_footer();
