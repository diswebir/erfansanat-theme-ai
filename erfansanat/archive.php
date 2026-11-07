<?php
/**
 * Generic archive fallback.
 *
 * Routes date, author and tag archives through the blog grid so none of them
 * falls back to a bare WordPress loop with the theme's header around it.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

get_header();

$erfansanat_active_category = '';
require get_template_directory() . '/template-parts/blog/part-blog-archive.php';

get_footer();
