<?php
/**
 * About page controller (P07-T020). Reference: static-mirror/about.html.
 * Renders the page's editable sections (hero/intro/ISO/principles/history)
 * through the SectionManager/Renderer loop; the global contact CTA and footer
 * come from get_footer() and are never duplicated here.
 *
 * Template Name: درباره ما
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Sections\Renderer;

get_header();

$es_about_id = (int) get_queried_object_id();
if ( $es_about_id <= 0 ) {
	$es_about_id = (int) get_the_ID();
}
?>
<main class="min-h-screen bg-[#f8faf9]">
	<?php echo Renderer::render( $es_about_id, 'about' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sections escape their own output. ?>
</main>
<?php
get_footer();
