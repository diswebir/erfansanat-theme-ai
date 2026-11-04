<?php
/**
 * Home section S04: about intro with a feature list and a stat rail.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_fields   = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_features = (array) SectionParts::field( $erfansanat_fields, 'features', array() );
$erfansanat_stats    = (array) SectionParts::field( $erfansanat_fields, 'stats', array() );
$erfansanat_media    = (int) SectionParts::field( $erfansanat_fields, 'media_id', 0 );
$erfansanat_image    = $erfansanat_media > 0 ? (string) wp_get_attachment_image_url( $erfansanat_media, 'erfansanat-hero' ) : SectionParts::asset( 'images/factory.jpg' );
$erfansanat_link     = (array) SectionParts::field( $erfansanat_fields, 'link', array() );
?>
<div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
	<div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-12">

		<div class="flex flex-col gap-5 lg:col-span-6">
			<?php
			SectionParts::heading(
				array(
					'eyebrow'  => (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ),
					'title'    => (string) SectionParts::field( $erfansanat_fields, 'title', '' ),
					'subtitle' => (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ),
				)
			);
			?>

			<?php if ( array() !== $erfansanat_features ) : ?>
				<ul class="flex flex-col gap-2.5">
					<?php foreach ( $erfansanat_features as $erfansanat_feature ) : ?>
						<?php $erfansanat_feature = is_array( $erfansanat_feature ) ? $erfansanat_feature : array(); ?>
						<li class="flex items-start gap-2.5 text-[13px] leading-6 font-semibold text-slate-700">
							<span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-emerald-50 text-emerald-700">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>
							</span>
							<span><?php echo esc_html( (string) ( $erfansanat_feature['title'] ?? '' ) ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( ! empty( $erfansanat_link['url'] ) ) : ?>
				<div class="pt-1">
					<a href="<?php echo esc_url( (string) $erfansanat_link['url'] ); ?>" class="group inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-3 text-xs font-black text-white transition-colors hover:bg-emerald-800 sm:text-[13px]">
						<span><?php echo esc_html( (string) ( $erfansanat_link['title'] ?? '' ) ); ?></span>
						<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>

		<div class="lg:col-span-6">
			<div class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950">
				<?php if ( '' !== $erfansanat_image ) : ?>
					<img src="<?php echo esc_url( $erfansanat_image ); ?>" alt="<?php echo esc_attr( (string) SectionParts::field( $erfansanat_fields, 'title', '' ) ); ?>" loading="lazy" decoding="async" class="h-[320px] w-full object-cover sm:h-[420px]" />
				<?php endif; ?>
				<div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/25 to-transparent"></div>
				<div class="absolute inset-x-0 bottom-0 p-5 sm:p-6">
					<div class="text-[11px] font-extrabold tracking-widest text-emerald-300"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'brand_mark', 'ESE' ) ); ?> FACTORY</div>
					<?php if ( array() !== $erfansanat_stats ) : ?>
						<dl class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
							<?php foreach ( $erfansanat_stats as $erfansanat_stat ) : ?>
								<?php $erfansanat_stat = is_array( $erfansanat_stat ) ? $erfansanat_stat : array(); ?>
								<div class="rounded-xl border border-white/15 bg-white/10 px-3 py-2 backdrop-blur-md">
									<dt class="text-[10.5px] font-bold text-slate-200"><?php echo esc_html( (string) ( $erfansanat_stat['title'] ?? '' ) ); ?></dt>
									<dd class="mt-0.5 text-base font-black text-white tabular-nums sm:text-lg"><?php echo esc_html( (string) ( $erfansanat_stat['value'] ?? '' ) ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
