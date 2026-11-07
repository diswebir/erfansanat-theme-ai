<?php
/**
 * Category archive: the blog grid filtered by one category.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

get_header();

$erfansanat_term                       = get_queried_object();
$GLOBALS['erfansanat_active_category'] = $erfansanat_term instanceof \WP_Term ? (string) $erfansanat_term->name : '';

$erfansanat_active_category = (string) $GLOBALS['erfansanat_active_category'];
require get_template_directory() . '/template-parts/blog/part-blog-archive.php';

get_footer();
