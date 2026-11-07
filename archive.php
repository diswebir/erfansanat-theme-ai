<?php
/** Generic post-type archive fallback; projects and other specific archives take precedence. */
defined('ABSPATH') || exit;
get_header();
get_template_part('template-parts/blog/archive');
get_footer();
