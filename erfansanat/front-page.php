<?php
/**
 * Front page controller (P05-T001).
 *
 * Renders the home page's sections through the SectionManager/Renderer loop:
 * admin order + enabled flags are honored, and the default 13-section order is
 * the fallback when nothing is configured. The global contact CTA and footer
 * are appended by get_footer() and are never duplicated here.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Sections\Renderer;

get_header();

$es_front_id = (int) get_queried_object_id();
?>
<main>
	<?php echo Renderer::render( $es_front_id, 'home' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sections escape their own output. ?>
</main>
<?php
get_footer();
