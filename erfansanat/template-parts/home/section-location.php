<?php
/**
 * Home section S15: factory address and map card.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_link   = (array) SectionParts::field( $erfansanat_fields, 'link', array() );
$erfansanat_media  = (int) SectionParts::field( $erfansanat_fields, 'media_id', 0 );
$erfansanat_map    = $erfansanat_media > 0 ? (string) wp_get_attachment_image_url( $erfansanat_media, 'large' ) : '';
$erfansanat_url    = ! empty( $erfansanat_link['url'] ) ? (string) $erfansanat_link['url'] : home_url( '/contact/' );
?>
<div class="mx-auto max-w-[1300px] px-4 pt-2 pb-16 sm:px-6 sm:pb-20 lg:px-8">
	<div class="relative overflow-hidden rounded-3xl border border-slate-200/85 bg-[#eef2f0] p-5 sm:p-8">
		<div class="relative z-10 grid items-center gap-6 lg:grid-cols-12">
			<div class="rounded-2xl border border-emerald-900/20 bg-gradient-to-b from-[#06130d] to-[#0a1e14] p-6 text-white shadow-xl lg:col-span-5">
				<div class="text-xs font-extrabold text-emerald-300"><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ) ); ?></div>
				<h3 class="mt-1.5 text-lg font-black text-white"><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'title', '' ) ); ?></h3>
				<p class="mt-2.5 text-xs leading-6 font-medium text-slate-300"><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ) ); ?></p>
				<div class="mt-4 flex flex-wrap items-center justify-between gap-2 border-t border-white/10 pt-3.5 text-xs">
					<?php if ( '' !== (string) SectionParts::field( $erfansanat_fields, 'postal', '' ) ) : ?>
						<span class="font-bold text-slate-300 tabular-nums">
							<?php
							printf(
								/* translators: %s: postal code. */
								esc_html__( 'کدپستی: %s', 'erfansanat' ),
								esc_html( (string) SectionParts::field( $erfansanat_fields, 'postal', '' ) )
							);
							?>
						</span>
					<?php endif; ?>
					<a href="<?php echo esc_url( $erfansanat_url ); ?>" class="inline-flex items-center gap-1.5 font-extrabold text-emerald-300 hover:text-white">
						<span><?php echo esc_html( (string) ( $erfansanat_link['title'] ?? __( 'مسیریابی و ثبت درخواست بازدید', 'erfansanat' ) ) ); ?></span>
					</a>
				</div>
			</div>

			<div class="flex min-h-[160px] items-center justify-center lg:col-span-7">
				<?php if ( '' !== $erfansanat_map ) : ?>
					<img src="<?php echo esc_url( $erfansanat_map ); ?>" alt="<?php echo esc_attr( (string) SectionParts::field( $erfansanat_fields, 'title', '' ) ); ?>" loading="lazy" decoding="async" class="w-full rounded-2xl border border-slate-200 object-cover" />
				<?php else : ?>
					<a href="<?php echo esc_url( $erfansanat_url ); ?>" class="group flex items-center gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-3 shadow-md transition-transform hover:-translate-y-0.5">
						<span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-emerald-600 text-white">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>
						</span>
						<div class="text-right">
							<div class="text-xs font-black text-slate-900"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'brand_name', '' ) ); ?></div>
							<div class="mt-0.5 text-[11px] font-bold text-emerald-700"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'address', '' ) ); ?> · <?php esc_html_e( 'مشاهده جزئیات تماس', 'erfansanat' ); ?></div>
						</div>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
