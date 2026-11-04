<?php
/**
 * Home section S11: after-sales support band.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_items  = (array) SectionParts::field( $erfansanat_fields, 'items', array() );
$erfansanat_phone  = SectionParts::phone();
$erfansanat_tel    = SectionParts::phone_href();
?>
<div class="mx-auto max-w-[1300px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
	<div class="relative overflow-hidden rounded-[2.25rem] border-0 bg-gradient-to-l from-[#064e3b] via-[#065f46] to-[#047857] text-white shadow-[0_24px_60px_-22px_rgba(4,120,87,0.38)] sm:rounded-[2.5rem]">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_50%,rgba(52,211,153,0.22),transparent_62%)]"></div>

		<div class="absolute -top-0.5 -right-0.5 z-20">
			<div class="relative rounded-bl-[1.75rem] bg-[#f6f8f7] px-6 py-2.5 sm:rounded-bl-[2rem] sm:px-8 sm:py-3">
				<div class="flex items-center gap-2 text-xs sm:text-sm font-black text-emerald-700">
					<span class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ) ); ?></span>
				</div>
			</div>
		</div>

		<div class="relative z-10 grid items-center gap-8 px-6 pt-16 pb-15 sm:px-10 sm:pt-16 sm:pb-16 lg:grid-cols-12 lg:px-12">
			<div class="text-right lg:col-span-6">
				<?php if ( '' !== (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ) ) : ?>
					<div class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-3 py-1.5 text-xs font-extrabold text-emerald-100 backdrop-blur-xs">
						<span><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ) ); ?></span>
					</div>
				<?php endif; ?>

				<h2 class="mt-3.5 text-xl font-black text-white sm:text-2xl lg:text-[1.75rem]">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped by title_markup().
					echo SectionParts::title_markup( (string) SectionParts::field( $erfansanat_fields, 'title', '' ) );
					?>
				</h2>

				<?php if ( array() !== $erfansanat_items ) : ?>
					<div class="mt-5 grid grid-cols-1 gap-2.5 sm:grid-cols-3">
						<?php foreach ( $erfansanat_items as $erfansanat_item ) : ?>
							<?php $erfansanat_item = is_array( $erfansanat_item ) ? $erfansanat_item : array(); ?>
							<div class="rounded-xl bg-white/10 px-3.5 py-3 backdrop-blur-xs">
								<div class="text-xs font-black text-white tabular-nums"><?php echo esc_html( (string) ( $erfansanat_item['value'] ?? '' ) ); ?></div>
								<div class="mt-0.5 text-[11px] font-medium text-emerald-100/85"><?php echo esc_html( (string) ( $erfansanat_item['title'] ?? '' ) ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="flex justify-center lg:col-span-6">
				<div class="relative grid w-full max-w-[440px] grid-cols-3 gap-3">
					<?php
					$erfansanat_tiles = array(
						array(
							'icon'  => 'shield',
							'value' => __( '۲۴ ماه', 'erfansanat' ),
							'label' => __( 'گارانتی تعویض', 'erfansanat' ),
						),
						array(
							'icon'  => 'truck',
							'value' => __( '۳۱ استان', 'erfansanat' ),
							'label' => __( 'پوشش سراسری', 'erfansanat' ),
						),
						array(
							'icon'  => 'support',
							'value' => __( '۵ سال', 'erfansanat' ),
							'label' => __( 'پشتیبانی قطعات', 'erfansanat' ),
						),
					);
					foreach ( $erfansanat_tiles as $erfansanat_tile ) :
						?>
						<div class="rounded-2xl border border-white/15 bg-white/10 p-4 text-center backdrop-blur-md">
							<span class="mx-auto grid h-10 w-10 place-items-center rounded-xl border border-emerald-400/40 bg-emerald-950/60 text-emerald-300">
								<?php SectionParts::icon( $erfansanat_tile['icon'], 'h-5 w-5' ); ?>
							</span>
							<div class="mt-2.5 text-sm font-black text-white tabular-nums"><?php echo esc_html( $erfansanat_tile['value'] ); ?></div>
							<div class="mt-0.5 text-[10.5px] font-bold text-emerald-100/85"><?php echo esc_html( $erfansanat_tile['label'] ); ?></div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php if ( '' !== $erfansanat_tel ) : ?>
			<div class="absolute -bottom-0.5 -left-0.5 z-20">
				<div class="relative rounded-tr-[1.75rem] bg-[#f6f8f7] px-6 py-2.5 sm:rounded-tr-[2rem] sm:px-8 sm:py-3">
					<a href="<?php echo esc_url( $erfansanat_tel ); ?>" class="inline-flex items-center gap-2 text-xs font-black text-emerald-700 tabular-nums transition-colors hover:text-emerald-900 sm:text-sm" dir="ltr">
						<span dir="ltr"><?php echo esc_html( $erfansanat_phone ); ?></span>
					</a>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
