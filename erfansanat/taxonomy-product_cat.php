<?php
/**
 * Product category archive.
 *
 * WooCommerce prefers `taxonomy-product_cat-{slug}.php` first, then this file,
 * then `archive-product.php`. The category page is the same body as the shop
 * with a category hero, so both call one shared part.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$erfansanat_term = get_queried_object();
$erfansanat_term = $erfansanat_term instanceof \WP_Term ? $erfansanat_term : null;
require get_template_directory() . '/template-parts/archive/part-shop-archive.php';

get_footer( 'shop' );
