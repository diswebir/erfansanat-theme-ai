<?php
/**
 * woocommerce/checkout.php — checkout styled to the static checkout.html shell.
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }
get_header();
?>
<main id="main" class="site-main">
    <section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
        <div class="mx-auto max-w-7xl px-5 lg:px-8">
            <h1 class="text-2xl font-black text-slate-900 sm:text-3xl"><?php esc_html_e('پرداخت', 'erfansanat'); ?></h1>
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">
        <?php
        wc_print_notices();
        do_action('woocommerce_before_checkout_form');
        wc_get_template('checkout/form-checkout.php');
        do_action('woocommerce_after_checkout_form');
        ?>
    </section>
</main>
<?php get_footer();
