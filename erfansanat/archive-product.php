<?php
/**
 * Shop archive.
 *
 * WooCommerce's template loader prefers this file over its own archive, so the
 * shop is the approved reference layout instead of an unstyled list.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$erfansanat_term = null;
require get_template_directory() . '/template-parts/archive/part-shop-archive.php';

get_footer( 'shop' );
