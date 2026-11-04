<?php
/**
 * Home section S06: the six-step company timeline.
 *
 * The reference rail is driven by main.js through `data-history-stage`,
 * `data-home2-history-btn` / `-panel` / `-prev` / `-next`, the progress bar and
 * the step label. Every one of those hooks is rendered here so the transferred
 * script keeps working unchanged.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_fields     = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_milestones = (array) SectionParts::field( $erfansanat_fields, 'milestones', array() );
$erfansanat_media      = (int) SectionParts::field( $erfansanat_fields, 'media_id', 0 );
$erfansanat_fallback   = $erfansanat_media > 0 ? (string) wp_get_attachment_image_url( $erfansanat_media, 'erfansanat-hero' ) : SectionParts::asset( 'images/hero.jpg' );
$erfansanat_total      = count( $erfansanat_milestones );

if ( 0 === $erfansanat_total ) {
	return;
}
?>
<div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
	<?php
	SectionParts::heading(
		array(
			'eyebrow'  => (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ),
			'title'    => (string) SectionParts::field( $erfansanat_fields, 'title', '' ),
			'subtitle' => (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ),
		)
	);
	?>

	<div data-history-stage="true" class="mt-8">
		<div class="rounded-3xl border border-slate-200/85 bg-white p-3.5 shadow-2xs sm:p-5">

			<div class="mb-3.5 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
				<div class="flex items-center gap-2 text-xs font-extrabold text-slate-700">
					<span class="h-2.5 w-2.5 rounded-full bg-emerald-600"></span>
					<span><?php esc_html_e( 'مسیر تکامل صنعتی شرکت', 'erfansanat' ); ?></span>
					<span class="text-slate-300">·</span>
					<span data-history-step-label="true" class="font-black text-emerald-700 tabular-nums">
						<?php
						printf(
							/* translators: 1: step number, 2: total steps, 3: year. */
							esc_html__( 'گام %1$s از %2$s (سال %3$s)', 'erfansanat' ),
							'۱',
							esc_html( (string) $erfansanat_total ),
							esc_html( (string) ( $erfansanat_milestones[0]['year'] ?? '' ) )
						);
						?>
					</span>
				</div>
				<div class="flex items-center gap-3">
					<span class="hidden text-[11px] font-bold text-slate-400 sm:inline"><?php esc_html_e( 'با اسکرول یا کلیک بین سال‌ها حرکت کنید', 'erfansanat' ); ?></span>
					<div class="h-2 w-32 overflow-hidden rounded-full bg-slate-100 sm:w-44">
						<div data-history-progress-bar="true" style="width:<?php echo esc_attr( (string) (int) round( 100 / $erfansanat_total ) ); ?>%" class="h-full rounded-full bg-gradient-to-l from-emerald-500 to-emerald-700 transition-all duration-300"></div>
					</div>
				</div>
			</div>

			<div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-6" role="tablist" aria-label="<?php esc_attr_e( 'سال‌های شاخص تاریخچه شرکت', 'erfansanat' ); ?>">
				<?php foreach ( $erfansanat_milestones as $erfansanat_index => $erfansanat_step ) : ?>
					<?php
					$erfansanat_step  = is_array( $erfansanat_step ) ? $erfansanat_step : array();
					$erfansanat_first = 0 === (int) $erfansanat_index;
					?>
					<button type="button" role="tab"
						aria-selected="<?php echo $erfansanat_first ? 'true' : 'false'; ?>"
						data-home2-history-btn="<?php echo esc_attr( (string) $erfansanat_index ); ?>"
						class="group flex flex-col items-start justify-between rounded-2xl border px-3.5 py-2.5 text-right transition-all cursor-pointer <?php echo $erfansanat_first ? 'border-emerald-700 bg-emerald-700 text-white shadow-md' : 'border-slate-200/75 bg-white text-slate-700 hover:border-emerald-300'; ?>">
						<div class="flex w-full items-center justify-between gap-1.5">
							<span class="text-sm sm:text-base font-black tabular-nums"><?php echo esc_html( (string) ( $erfansanat_step['year'] ?? '' ) ); ?></span>
							<span class="inline-flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-black tabular-nums <?php echo $erfansanat_first ? 'bg-white/20 text-white' : 'bg-slate-200/80 text-slate-600'; ?>"><?php echo esc_html( (string) ( (int) $erfansanat_index + 1 ) ); ?></span>
						</div>
						<span class="mt-1 line-clamp-1 text-[10.5px] font-bold <?php echo $erfansanat_first ? 'text-emerald-100' : 'text-slate-500'; ?>"><?php echo esc_html( (string) ( $erfansanat_step['label'] ?? '' ) ); ?></span>
					</button>
				<?php endforeach; ?>
			</div>
		</div>

		<?php foreach ( $erfansanat_milestones as $erfansanat_index => $erfansanat_step ) : ?>
			<?php
			$erfansanat_step  = is_array( $erfansanat_step ) ? $erfansanat_step : array();
			$erfansanat_first = 0 === (int) $erfansanat_index;
			$erfansanat_last  = ( $erfansanat_index + 1 ) === $erfansanat_total;
			?>
			<div data-home2-history-panel="<?php echo esc_attr( (string) $erfansanat_index ); ?>" class="<?php echo $erfansanat_first ? 'grid' : 'hidden'; ?> card-light mt-5 items-center gap-6 rounded-3xl p-6 sm:p-8 lg:grid-cols-12 lg:gap-10">
				<div class="text-right lg:col-span-6">
					<div class="flex flex-wrap items-center gap-2">
						<span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3 py-1 text-xs font-black text-emerald-800 tabular-nums">
							<span>
								<?php
								printf(
									/* translators: %s: milestone year. */
									esc_html__( 'سال %s خورشیدی', 'erfansanat' ),
									esc_html( (string) ( $erfansanat_step['year'] ?? '' ) )
								);
								?>
							</span>
						</span>
						<span class="rounded-xl border border-emerald-200/80 bg-white px-3 py-1 text-[11px] font-extrabold text-emerald-700"><?php echo esc_html( (string) ( $erfansanat_step['label'] ?? '' ) ); ?></span>
					</div>
					<h3 class="mt-3.5 text-xl font-black text-slate-900 sm:text-2xl"><?php echo esc_html( (string) ( $erfansanat_step['title'] ?? '' ) ); ?></h3>
					<?php if ( '' !== (string) ( $erfansanat_step['description'] ?? '' ) ) : ?>
						<p class="mt-3.5 text-right text-xs leading-7 font-medium text-slate-600 sm:text-[13.5px] sm:leading-7">
							<?php echo esc_html( (string) $erfansanat_step['description'] ); ?>
						</p>
					<?php endif; ?>
					<div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-4">
						<span class="text-xs font-extrabold text-slate-500 tabular-nums">
							<?php
							printf(
								/* translators: 1: step number, 2: total steps. */
								esc_html__( 'مرحله %1$s از %2$s', 'erfansanat' ),
								esc_html( (string) ( (int) $erfansanat_index + 1 ) ),
								esc_html( (string) $erfansanat_total )
							);
							?>
						</span>
						<div class="flex items-center gap-2">
							<button type="button" data-home2-history-prev="true" aria-label="<?php esc_attr_e( 'سال قبل', 'erfansanat' ); ?>" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-white px-3 py-1.5 text-xs font-extrabold text-slate-700 transition-colors hover:border-emerald-300 hover:text-emerald-800 disabled:opacity-40">
								<span><?php esc_html_e( 'قبلی', 'erfansanat' ); ?></span>
							</button>
							<button type="button" data-home2-history-next="true" aria-label="<?php esc_attr_e( 'سال بعد', 'erfansanat' ); ?>" class="inline-flex items-center gap-1 rounded-xl bg-emerald-700 px-3 py-1.5 text-xs font-extrabold text-white transition-colors hover:bg-emerald-800 cursor-pointer">
								<span><?php echo $erfansanat_last ? esc_html__( 'بازگشت به ابتدا', 'erfansanat' ) : esc_html__( 'بعدی', 'erfansanat' ); ?></span>
							</button>
						</div>
					</div>
				</div>
				<div class="lg:col-span-6">
					<div class="relative h-60 overflow-hidden rounded-2xl bg-slate-900 sm:h-72">
						<?php if ( '' !== $erfansanat_fallback ) : ?>
							<img src="<?php echo esc_url( $erfansanat_fallback ); ?>" alt="<?php echo esc_attr( (string) ( $erfansanat_step['title'] ?? '' ) ); ?>" loading="lazy" decoding="async" class="h-full w-full object-cover" />
						<?php endif; ?>
						<div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
						<div class="absolute right-4 bottom-4 text-right">
							<div class="text-[11px] font-bold text-emerald-300"><?php echo esc_html( (string) ( $erfansanat_step['label'] ?? '' ) ); ?></div>
							<div class="text-sm font-extrabold text-white"><?php echo esc_html( (string) ( $erfansanat_step['year'] ?? '' ) ); ?></div>
						</div>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
