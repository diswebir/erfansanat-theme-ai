<?php
/**
 * WooCommerce product category archive.
 *
 * @package ErfanSanat\Theme
 */

defined('ABSPATH') || exit;

$archiveTerm = get_queried_object();
get_header();
do_action('woocommerce_before_main_content');
get_template_part(
    'template-parts/woocommerce/product-archive',
    null,
    [
        'context' => 'product_cat',
        'term' => $archiveTerm instanceof WP_Term && 'product_cat' === $archiveTerm->taxonomy ? $archiveTerm : null,
    ]
);
do_action('woocommerce_after_main_content');
do_action('woocommerce_sidebar');
get_footer();
