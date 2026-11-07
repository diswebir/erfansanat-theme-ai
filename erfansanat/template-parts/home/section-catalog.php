<?php
/**
 * Home section S08: the catalogue band.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_fields = isset( $erfansanat_section_fields ) && is_array( $erfansanat_section_fields ) ? $erfansanat_section_fields : array();
$erfansanat_link   = (array) SectionParts::field( $erfansanat_fields, 'link', array() );
$erfansanat_media  = (int) SectionParts::field( $erfansanat_fields, 'media_id', 0 );
$erfansanat_cover  = $erfansanat_media > 0 ? (string) wp_get_attachment_image_url( $erfansanat_media, 'large' ) : SectionParts::asset( 'images/light-tree.jpg' );
$erfansanat_bg     = SectionParts::asset( 'images/light-tunnel.jpg' );
$erfansanat_url    = ! empty( $erfansanat_link['url'] ) ? (string) $erfansanat_link['url'] : home_url( '/shop/' );
?>
<div class="mx-auto max-w-[1300px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
	<div class="relative overflow-hidden rounded-3xl border-0 bg-gradient-to-l from-[#064e3b] via-[#065f46] to-[#047857] text-white shadow-[0_20px_50px_-22px_rgba(4,120,87,0.32)]">
		<?php if ( '' !== $erfansanat_bg ) : ?>
			<img src="<?php echo esc_url( $erfansanat_bg ); ?>" alt="<?php echo esc_attr( (string) SectionParts::field( $erfansanat_fields, 'title', '' ) ); ?>" loading="lazy" decoding="async" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-15" />
		<?php endif; ?>
		<div class="pointer-events-none absolute inset-0 bg-gradient-to-l from-[#064e3b]/95 via-[#065f46]/90 to-[#047857]/85"></div>

		<div class="relative z-10 grid items-center gap-6 px-6 py-8 sm:px-10 sm:py-10 lg:grid-cols-12 lg:px-12">
			<div class="text-right lg:col-span-8">
				<?php if ( '' !== (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ) ) : ?>
					<div class="inline-flex items-center gap-2 text-xs font-extrabold text-emerald-200">
						<span><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'eyebrow', '' ) ); ?></span>
					</div>
				<?php endif; ?>
				<h3 class="mt-2.5 text-xl font-black text-white sm:text-2xl"><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'title', '' ) ); ?></h3>
				<p class="mt-2.5 max-w-2xl text-right text-xs leading-7 font-medium text-emerald-50/90 sm:text-[13.5px] sm:leading-7"><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'subtitle', '' ) ); ?></p>
				<div class="mt-6 flex flex-wrap items-center gap-4">
					<a href="<?php echo esc_url( $erfansanat_url ); ?>" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-xs font-black text-emerald-900 transition-colors hover:bg-emerald-50 sm:text-sm">
						<span><?php echo esc_html( (string) ( $erfansanat_link['title'] ?? __( 'مشاهده و دریافت کاتالوگ محصولات', 'erfansanat' ) ) ); ?></span>
					</a>
					<?php if ( '' !== (string) SectionParts::field( $erfansanat_fields, 'note', '' ) ) : ?>
						<span class="text-xs font-bold text-emerald-100 tabular-nums"><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'note', '' ) ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<div class="flex justify-center lg:col-span-4 lg:justify-end">
				<a href="<?php echo esc_url( $erfansanat_url ); ?>" class="group relative block h-44 w-64 overflow-hidden rounded-2xl bg-slate-900 p-5 shadow-xl transition-transform duration-300 hover:-translate-y-1">
					<?php if ( '' !== $erfansanat_cover ) : ?>
						<img src="<?php echo esc_url( $erfansanat_cover ); ?>" alt="<?php esc_attr_e( 'جلد کاتالوگ محصولات', 'erfansanat' ); ?>" loading="lazy" decoding="async" class="absolute inset-0 h-full w-full object-cover opacity-55 transition-transform duration-500 group-hover:scale-105" />
					<?php endif; ?>
					<div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/55 to-transparent"></div>
					<div class="relative z-10 flex h-full flex-col justify-between text-right">
						<div class="flex items-center justify-between">
							<span class="rounded-lg bg-emerald-500 px-2.5 py-0.5 text-[10px] font-black text-slate-950 tabular-nums"><?php echo esc_html( (string) SectionParts::field( $erfansanat_fields, 'badge', '' ) ); ?></span>
							<span class="font-mono text-xs font-black text-white/80"><?php echo esc_html( (string) \ErfanSanat\Theme\Settings::get( 'brand_mark', 'ESE' ) ); ?></span>
						</div>
						<div>
							<div class="text-[11px] font-bold text-emerald-300"><?php esc_html_e( 'کاتالوگ فنی و اجرایی', 'erfansanat' ); ?></div>
							<div class="mt-0.5 flex items-center justify-between text-sm font-black text-white">
								<span><?php esc_html_e( 'محصولات نورپردازی شهری', 'erfansanat' ); ?></span>
							</div>
						</div>
					</div>
				</a>
			</div>
		</div>
	</div>
</div>
