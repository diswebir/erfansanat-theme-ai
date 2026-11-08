<?php
/**
 * Blog index (the page assigned to "Posts page" in Settings → Reading).
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

get_header();

require get_template_directory() . '/template-parts/blog/part-blog-archive.php';

get_footer();
