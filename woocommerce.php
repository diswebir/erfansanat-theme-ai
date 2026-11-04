<?php
/** WooCommerce page wrapper; individual theme templates own their markup. */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<main id="primary" class="site-main min-h-screen">
    <?php woocommerce_content(); ?>
</main>
<?php
get_footer();
