<?php
/**
 * Dynamic project archive; the shared partial renders the current main query and its pagination.
 *
 * @package ErfanSanat\Theme
 */

defined('ABSPATH') || exit;

get_header();
get_template_part('template-parts/projects/archive');
get_footer();
