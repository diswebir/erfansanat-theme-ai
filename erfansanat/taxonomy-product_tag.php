<?php
/**
 * Product tag archive.
 *
 * A tag has no FAQ or tagline of its own in the legacy data, so it renders the
 * same archive body with the term name as the heading.
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
