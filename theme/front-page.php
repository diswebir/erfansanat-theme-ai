<?php
/** Front page: render registered home sections, falling back to the page content. */

defined( 'ABSPATH' ) || exit;

get_header();

ob_start();
\ErfanSanat\Theme\Sections\Renderer::render( 'home' );
$section_markup = (string) ob_get_clean();
if ( '' !== trim( $section_markup ) ) {
	echo '<main>' . $section_markup . '</main>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Section templates own their escaped output.
} else {
	echo '<main>';
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_content();
		}
	}
	echo '</main>';
}

get_footer();
