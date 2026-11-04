<?php
/**
 * WooCommerce shop archive, retaining the main product query and extension hooks.
 *
 * @package ErfanSanat\Theme
 */

defined('ABSPATH') || exit;

get_header();
do_action('woocommerce_before_main_content');
get_template_part(
    'template-parts/woocommerce/product-archive',
    null,
    [
        'context' => 'shop',
        'term' => null,
    ]
);
do_action('woocommerce_after_main_content');
do_action('woocommerce_sidebar');
get_footer();
