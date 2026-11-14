<?php
/**
 * About history timeline (P07-T020). Optional section: renders nothing when
 * the timeline repeater is empty (no placeholder section).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();
$es_id      = (string) ( $es_section['id'] ?? 'history' );

$es_heading  = (string) ( $es_fields['heading'] ?? 'تاریخچه و دستاوردها' );
$es_timeline = ! empty( $es_fields['timeline'] ) && is_array( $es_fields['timeline'] ) ? $es_fields['timeline'] : array();
if ( ! $es_timeline ) {
	return;
}
?>
<section class="border-t border-emerald-100 bg-white py-16 lg:py-20" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="history">
	<div class="mx-auto max-w-7xl px-5 lg:px-8">
		<h2 class="mb-12 text-center text-2xl font-extrabold text-slate-900 lg:text-3xl"><?php echo esc_html( $es_heading ); ?></h2>
		<ol class="relative mx-auto max-w-3xl space-y-8 border-r-2 border-emerald-100 pr-8">
			<?php foreach ( $es_timeline as $es_event ) : ?>
				<li class="relative">
					<span class="absolute -right-[2.45rem] top-1 grid h-5 w-5 place-items-center rounded-full border-2 border-emerald-500 bg-white"><span class="h-2 w-2 rounded-full bg-emerald-600"></span></span>
					<div class="card-light rounded-2xl p-5">
						<span class="text-[13px] font-black text-emerald-700"><?php echo esc_html( (string) ( $es_event['year'] ?? '' ) ); ?></span>
						<h3 class="mt-1 text-[15.5px] font-extrabold text-slate-900"><?php echo esc_html( (string) ( $es_event['title'] ?? '' ) ); ?></h3>
						<?php if ( ! empty( $es_event['description'] ) ) : ?><p class="mt-2 text-[13px] leading-7 text-slate-600"><?php echo esc_html( (string) $es_event['description'] ); ?></p><?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
