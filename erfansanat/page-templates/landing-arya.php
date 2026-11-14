<?php
/**
 * Arya landing controller (P07-T028). References: static-mirror/index.html +
 * services.html design vocabulary — no new design system (AC-03).
 *
 * Renders the page's editable arya sections (hero/features/specs/faq/cta)
 * through the SectionManager/Renderer loop, plus a sticky landing scroll menu:
 * the assigned `landing` menu via LandingWalker when present, otherwise an
 * automatic fallback built from the enabled sections. Scroll-spy runs in
 * navigation.js ([data-landing-link]); native smooth scrolling ships in the
 * mirror stylesheet. The global contact CTA/footer come from get_footer().
 *
 * Template Name: لندینگ آریا
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\NavWalkers\LandingWalker;
use ErfanSanat\Theme\Sections\Renderer;
use ErfanSanat\Theme\Sections\Schema;
use ErfanSanat\Theme\Sections\SectionManager;

get_header();

$es_arya_id = (int) get_queried_object_id();
if ( $es_arya_id <= 0 ) {
	$es_arya_id = (int) get_the_ID();
}

// Landing scroll menu: assigned menu first, section-based fallback otherwise.
$es_landing_menu = LandingWalker::render();
if ( '' === $es_landing_menu && $es_arya_id > 0 ) {
	$es_labels = array();
	foreach ( Schema::sections( 'arya' ) as $es_type => $es_def ) {
		$es_labels[ $es_type ] = (string) $es_def['title'];
	}
	$es_landing_menu = '<ul id="es-landing-fallback" class="es-landing-menu flex flex-wrap items-center gap-1" data-landing-menu="true">';
	foreach ( SectionManager::get_sections( $es_arya_id, 'arya' ) as $es_section ) {
		if ( empty( $es_section['enabled'] ) ) {
			continue;
		}
		$es_anchor = (string) ( $es_section['id'] ?? '' );
		$es_label  = $es_labels[ $es_section['type'] ?? '' ] ?? '';
		if ( '' === $es_anchor || '' === $es_label ) {
			continue;
		}
		$es_landing_menu .= '<li><a href="#' . esc_attr( $es_anchor ) . '" data-landing-link="true" class="es-landing-link inline-flex items-center whitespace-nowrap rounded-xl px-3 py-2 text-[13px] font-extrabold text-slate-700 transition-colors hover:bg-slate-50 hover:text-emerald-700">' . esc_html( $es_label ) . '</a></li>';
	}
	$es_landing_menu .= '</ul>';
}
?>
<main class="min-h-screen bg-[#f8faf9]">
	<?php echo Renderer::render( $es_arya_id, 'arya' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sections escape their own output. ?>
	<?php if ( '' !== $es_landing_menu ) : ?>
		<div class="pointer-events-none fixed inset-x-0 bottom-4 z-40 px-5">
			<nav aria-label="<?php esc_attr_e( 'منوی بخش‌های لندینگ', 'erfansanat' ); ?>" class="pointer-events-auto mx-auto w-fit max-w-full overflow-x-auto rounded-2xl border border-emerald-100 bg-white/95 p-1.5 shadow-[0_18px_45px_-18px_rgba(5,150,105,0.35)] backdrop-blur-md">
				<?php echo $es_landing_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- walker/fallback escape their own output. ?>
			</nav>
		</div>
	<?php endif; ?>
</main>
<?php
get_footer();
