<?php
/**
 * Home section S09: services list with a live preview panel.
 *
 * The transferred script switches the preview image, counter, title and description from the
 * `data-service-*` attributes on each row, so those attributes carry the real
 * values here instead of the mirror's hard-coded strings.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_items  = array_values( (array) SectionParts::field( $erfansanat_fields, 'items', array() ) );
$erfansanat_total  = count( $erfansanat_items );

if ( 0 === $erfansanat_total ) {
	return;
}

$erfansanat_first      = is_array( $erfansanat_items[0] ) ? $erfansanat_items[0] : array();
$erfansanat_first_img  = SectionParts::asset( (string) ( $erfansanat_first['image'] ?? '' ) );
$erfansanat_fa_digits  = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
$erfansanat_to_persian = static function ( int $number ) use ( $erfansanat_fa_digits ): string {
	$out = '';
	foreach ( str_split( (string) $number ) as $erfansanat_digit ) {
		$out .= ctype_digit( $erfansanat_digit ) ? $erfansanat_fa_digits[ (int) $erfansanat_digit ] : $erfansanat_digit;
	}
	return $out;
};
?>
<div id="services" class="mx-auto max-w-[1300px] scroll-mt-20 px-4 sm:px-6 lg:px-8" data-services-section="true">
	<?php
	SectionParts::heading(
		array(
			'eyebrow'  => (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ),
			'title'    => (string) SectionParts::field( $erfansanat_fields, 'title', '' ),
			'subtitle' => (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ),
			'align'    => 'center',
		)
	);
	?>

	<div class="mt-8 sm:mt-10 grid items-stretch gap-6 lg:grid-cols-12 lg:gap-8">

		<div class="flex flex-col justify-between gap-2.5 lg:col-span-6">
			<?php foreach ( $erfansanat_items as $erfansanat_index => $erfansanat_item ) : ?>
				<?php
				$erfansanat_item   = is_array( $erfansanat_item ) ? $erfansanat_item : array();
				$erfansanat_active = 0 === (int) $erfansanat_index;
				$erfansanat_title  = (string) ( $erfansanat_item['title'] ?? '' );
				$erfansanat_desc   = (string) ( $erfansanat_item['description'] ?? '' );
				$erfansanat_image  = SectionParts::asset( (string) ( $erfansanat_item['image'] ?? '' ) );
				?>
				<button type="button"
					data-service-row="<?php echo esc_attr( (string) $erfansanat_index ); ?>"
					data-service-title="<?php echo esc_attr( $erfansanat_title ); ?>"
					data-service-desc-text="<?php echo esc_attr( $erfansanat_desc ); ?>"
					data-service-img="<?php echo esc_url( $erfansanat_image ); ?>"
					class="group relative flex items-start gap-3 rounded-2xl border px-4 py-3.5 text-right transition-all cursor-pointer <?php echo $erfansanat_active ? 'border-emerald-200 bg-white shadow-sm' : 'border-transparent bg-transparent hover:border-slate-200 hover:bg-white/70'; ?>">
					<span class="absolute right-0 top-1/2 w-1 -translate-y-1/2 rounded-l-full bg-emerald-600 transition-all duration-200 <?php echo $erfansanat_active ? 'h-10' : 'h-0'; ?>" data-service-indicator="<?php echo esc_attr( (string) $erfansanat_index ); ?>"></span>

					<span data-service-num="<?php echo esc_attr( (string) $erfansanat_index ); ?>" class="mt-0.5 text-xs font-black tabular-nums transition-colors <?php echo $erfansanat_active ? 'text-emerald-700' : 'text-slate-400'; ?>">
						<?php echo esc_html( $erfansanat_to_persian( (int) $erfansanat_index + 1 ) ); ?>
					</span>

					<div class="flex flex-1 flex-col gap-1">
						<span class="flex items-center justify-between gap-3">
							<span data-service-heading="<?php echo esc_attr( (string) $erfansanat_index ); ?>" class="text-sm sm:text-base font-extrabold transition-colors <?php echo $erfansanat_active ? 'text-emerald-950' : 'text-slate-800'; ?>">
								<?php echo esc_html( $erfansanat_title ); ?>
							</span>
							<span data-service-row-icon="<?php echo esc_attr( (string) $erfansanat_index ); ?>" class="shrink-0 transition-colors <?php echo $erfansanat_active ? 'text-emerald-600' : 'text-slate-400 group-hover:text-emerald-600'; ?>">
								<?php SectionParts::icon( (string) ( $erfansanat_item['icon'] ?? 'lamp' ), 'h-5 w-5' ); ?>
							</span>
						</span>
						<p data-service-desc="<?php echo esc_attr( (string) $erfansanat_index ); ?>" class="text-xs sm:text-[13px] font-medium leading-6 text-slate-600"><?php echo esc_html( $erfansanat_desc ); ?></p>

						<?php if ( '' !== $erfansanat_image ) : ?>
							<div data-service-mobile-img="<?php echo esc_attr( (string) $erfansanat_index ); ?>" class="<?php echo $erfansanat_active ? 'block' : 'hidden'; ?> overflow-hidden lg:hidden">
								<img src="<?php echo esc_url( $erfansanat_image ); ?>" alt="<?php echo esc_attr( $erfansanat_title ); ?>" loading="lazy" decoding="async" class="mt-3 h-40 w-full rounded-xl border border-slate-200 object-cover" />
							</div>
						<?php endif; ?>
					</div>
				</button>
			<?php endforeach; ?>
		</div>

		<div class="hidden lg:col-span-6 lg:block">
			<div class="relative h-full min-h-[520px] overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 shadow-sm">
				<?php if ( '' !== $erfansanat_first_img ) : ?>
					<img data-service-preview-img="true" src="<?php echo esc_url( $erfansanat_first_img ); ?>" alt="<?php echo esc_attr( (string) ( $erfansanat_first['title'] ?? '' ) ); ?>" class="absolute inset-0 h-full w-full object-cover transition-opacity duration-300" />
				<?php endif; ?>
				<div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/25 to-transparent"></div>

				<span data-service-preview-counter="true" class="absolute left-6 top-6 rounded-xl border border-white/20 bg-slate-950/65 px-3.5 py-1.5 text-xs font-extrabold text-emerald-300 tabular-nums backdrop-blur-md">
					<?php echo esc_html( $erfansanat_to_persian( 1 ) . ' / ' . $erfansanat_to_persian( $erfansanat_total ) ); ?>
				</span>

				<div class="absolute inset-x-0 bottom-0 flex items-center gap-4 p-7">
					<span class="grid h-13 w-13 shrink-0 place-items-center rounded-2xl border border-emerald-400/40 bg-emerald-950/80 text-emerald-300 backdrop-blur-md">
						<?php SectionParts::icon( (string) ( $erfansanat_first['icon'] ?? 'lamp' ), 'h-6 w-6' ); ?>
					</span>
					<div class="min-w-0">
						<div data-service-preview-title="true" class="text-lg font-black text-white sm:text-xl"><?php echo esc_html( (string) ( $erfansanat_first['title'] ?? '' ) ); ?></div>
						<p data-service-preview-desc="true" class="mt-1 text-xs leading-6 font-medium text-slate-200/90 sm:text-[13px]"><?php echo esc_html( (string) ( $erfansanat_first['description'] ?? '' ) ); ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
