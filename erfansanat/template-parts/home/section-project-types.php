<?php
/**
 * Home section S02: featured project types (one tall card, two stacked).
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_items  = (array) SectionParts::field( $erfansanat_fields, 'items', array() );

if ( array() === $erfansanat_items ) {
	return;
}
?>
<div id="projects" class="mx-auto max-w-[1300px] scroll-mt-24 px-4 sm:px-6 lg:px-8">
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
	<div class="mt-8 sm:mt-10 grid gap-4 sm:gap-5 lg:grid-cols-5 lg:items-stretch">
		<?php
		$erfansanat_first = is_array( $erfansanat_items[0] ?? null ) ? $erfansanat_items[0] : array();
		$erfansanat_rest  = array_slice( $erfansanat_items, 1, 2 );
		?>
		<div class="flex h-full flex-col lg:col-span-3">
			<a href="<?php echo esc_url( (string) ( $erfansanat_first['url'] ?? '' ) ); ?>" class="card-glow group relative block h-full w-full overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 min-h-[340px] sm:min-h-[420px] lg:min-h-[540px]">
				<?php $erfansanat_img = SectionParts::asset( (string) ( $erfansanat_first['image'] ?? '' ) ); ?>
				<?php if ( '' !== $erfansanat_img ) : ?>
					<img src="<?php echo esc_url( $erfansanat_img ); ?>" alt="<?php echo esc_attr( (string) ( $erfansanat_first['title'] ?? '' ) ); ?>" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" />
				<?php endif; ?>
				<div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/35 to-slate-950/15"></div>
				<div class="absolute inset-x-0 top-0 flex items-center justify-between p-5 sm:p-6">
					<span class="grid h-10 w-10 place-items-center rounded-2xl border border-white/25 bg-slate-950/60 text-white backdrop-blur-md transition-colors duration-300 group-hover:border-emerald-400 group-hover:bg-emerald-600">
						<?php SectionParts::arrow( 'h-4.5 w-4.5' ); ?>
					</span>
					<span class="text-sm font-black tracking-wider text-white/75 tabular-nums">۰۱</span>
				</div>
				<div class="absolute inset-x-0 bottom-0 flex flex-col gap-1.5 p-5 sm:p-7">
					<span class="text-xs font-bold text-emerald-300"><?php echo esc_html( (string) ( $erfansanat_first['tag'] ?? '' ) ); ?></span>
					<h3 class="font-black tracking-tight text-white text-xl sm:text-2xl lg:text-3xl"><?php echo esc_html( (string) ( $erfansanat_first['title'] ?? '' ) ); ?></h3>
					<p class="max-w-lg text-xs sm:text-[13px] font-medium leading-6 text-slate-200/90"><?php echo esc_html( (string) ( $erfansanat_first['description'] ?? '' ) ); ?></p>
				</div>
			</a>
		</div>
		<div class="flex h-full flex-col gap-4 sm:gap-5 lg:col-span-2">
			<?php foreach ( $erfansanat_rest as $erfansanat_offset => $erfansanat_item ) : ?>
				<?php $erfansanat_item = is_array( $erfansanat_item ) ? $erfansanat_item : array(); ?>
				<div class="flex flex-1 flex-col">
					<a href="<?php echo esc_url( (string) ( $erfansanat_item['url'] ?? '' ) ); ?>" class="card-glow group relative block h-full w-full overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 min-h-[235px] sm:min-h-[258px]">
						<?php $erfansanat_img = SectionParts::asset( (string) ( $erfansanat_item['image'] ?? '' ) ); ?>
						<?php if ( '' !== $erfansanat_img ) : ?>
							<img src="<?php echo esc_url( $erfansanat_img ); ?>" alt="<?php echo esc_attr( (string) ( $erfansanat_item['title'] ?? '' ) ); ?>" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" />
						<?php endif; ?>
						<div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/35 to-slate-950/15"></div>
						<div class="absolute inset-x-0 top-0 flex items-center justify-between p-5 sm:p-6">
							<span class="grid h-10 w-10 place-items-center rounded-2xl border border-white/25 bg-slate-950/60 text-white backdrop-blur-md transition-colors duration-300 group-hover:border-emerald-400 group-hover:bg-emerald-600">
								<?php SectionParts::arrow( 'h-4.5 w-4.5' ); ?>
							</span>
							<span class="text-sm font-black tracking-wider text-white/75 tabular-nums"><?php echo esc_html( sprintf( '%02d', ( (int) $erfansanat_offset ) + 2 ) ); ?></span>
						</div>
						<div class="absolute inset-x-0 bottom-0 flex flex-col gap-1.5 p-5 sm:p-7">
							<span class="text-xs font-bold text-emerald-300"><?php echo esc_html( (string) ( $erfansanat_item['tag'] ?? '' ) ); ?></span>
							<h3 class="font-black tracking-tight text-white text-lg sm:text-xl"><?php echo esc_html( (string) ( $erfansanat_item['title'] ?? '' ) ); ?></h3>
							<p class="text-xs sm:text-[13px] font-medium leading-6 text-slate-200/90"><?php echo esc_html( (string) ( $erfansanat_item['description'] ?? '' ) ); ?></p>
						</div>
					</a>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
