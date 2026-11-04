<?php
/** Blog posts index, using the configured WordPress posts page query. */
defined('ABSPATH') || exit;
get_header();
get_template_part('template-parts/blog/archive');
get_footer();
