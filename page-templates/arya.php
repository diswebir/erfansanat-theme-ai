<?php
/** Template Name: Arya Smart Door Opener Landing */

defined('ABSPATH') || exit;
get_header();
if (class_exists(\ErfanSanat\Theme\Navigation\Navigation::class)) {
    echo \ErfanSanat\Theme\Navigation\Navigation::render_landing(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
get_template_part('template-parts/pages/section-page');
get_footer();
