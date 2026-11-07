<?php
/** Generic WordPress page template. */
defined( 'ABSPATH' ) || exit;
get_header();
get_template_part( 'template-parts/general/page-content' );
get_footer();
