<?php
/**
 * Contact page controller (P07-T021). Reference: static-mirror/contact.html.
 * Sections (hero + form/info) render through SectionManager; contact details
 * fall back to the shared contact options. The form stays presentation-only
 * until the Phase 8 handler lands (safe note, no action/method, no named
 * inputs → nothing can be submitted or stored).
 *
 * Template Name: تماس با ما
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Sections\Renderer;

get_header();

$es_contact_id = (int) get_queried_object_id();
if ( $es_contact_id <= 0 ) {
	$es_contact_id = (int) get_the_ID();
}
?>
<main class="min-h-screen bg-[#f8faf9]">
	<?php echo Renderer::render( $es_contact_id, 'contact' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sections escape their own output. ?>
</main>
<?php
get_footer();
