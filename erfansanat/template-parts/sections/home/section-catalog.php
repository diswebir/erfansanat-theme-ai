<?php
/**
 * Catalog CTA section (P05-T014). DOM parity with the reference green
 * gradient card. Heading/description/button label/file come from the section
 * fields (AC-01); the download link is escaped (AC-02); when no file is set
 * the CTA button is removed and the cover is no longer a link (AC-03).
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'catalog' );
$es_type = (string) ( $es_section['type'] ?? 'catalog' );

$es_heading      = (string) ( $es_fields['heading'] ?? 'کاتالوگ جامع محصولات عرفان صنعت اصفهان (ویرایش ۱۴۰۴)' );
$es_description  = (string) ( $es_fields['description'] ?? 'مشخصات فنی کامل، ابعاد سازه، توان مصرفی، درجه حفاظت IP68 و راهنمای نصب بیش از ۱۲۵ محصول تخصصی نورپردازی شهری در کاتالوگ رسمی کارخانه گردآوری شده است.' );
$es_button_label = (string) ( $es_fields['button_label'] ?? 'دانلود کاتالوگ' );

$es_file_id  = (int) ( $es_fields['file_id'] ?? 0 );
$es_file_url = $es_file_id > 0 ? (string) wp_get_attachment_url( $es_file_id ) : '';
$es_has_file = '' !== $es_file_url;

$es_eyebrow   = (string) apply_filters( 'es_catalog_eyebrow', 'مرجع مشخصات فنی · کدهای سامانه ستاد · کد ملی کالا GS1' );
$es_note      = (string) apply_filters( 'es_catalog_note', 'شامل +۱۲۵ محصول تخصصی · آماده ارائه به شهرداری‌ها' );
$es_bg_image  = (string) apply_filters( 'es_catalog_bg_image', ES_ASSETS_URI . 'images/light-tunnel.jpg' );
$es_cover_img = (string) apply_filters( 'es_catalog_cover_image', ES_ASSETS_URI . 'images/light-tree.jpg' );
$es_version   = (string) apply_filters( 'es_catalog_version', 'نسخه ۱۴۰۴' );
?>
<section aria-label="دانلود کاتالوگ جامع محصولات عرفان صنعت" class="es-section es-section--catalog mx-auto max-w-[1300px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="relative overflow-hidden rounded-3xl border-0 bg-gradient-to-l from-[#064e3b] via-[#065f46] to-[#047857] text-white shadow-[0_20px_50px_-22px_rgba(4,120,87,0.32)]">
		<img src="<?php echo esc_url( $es_bg_image ); ?>" alt="<?php echo esc_attr( $es_heading ); ?>" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-15"/>
		<div class="pointer-events-none absolute inset-0 bg-gradient-to-l from-[#064e3b]/95 via-[#065f46]/90 to-[#047857]/85"></div>
		<div class="relative z-10 grid items-center gap-6 px-6 py-8 sm:px-10 sm:py-10 lg:grid-cols-12 lg:px-12">
			<div class="text-right lg:col-span-8">
				<div class="inline-flex items-center gap-2 text-xs font-extrabold text-emerald-200">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-4 w-4 text-emerald-300" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
					<span><?php echo esc_html( $es_eyebrow ); ?></span>
				</div>
				<h3 class="mt-2.5 text-xl font-black text-white sm:text-2xl"><?php echo esc_html( $es_heading ); ?></h3>
				<?php if ( '' !== trim( $es_description ) ) : ?>
					<p class="mt-2.5 max-w-2xl text-right text-xs leading-7 font-medium text-emerald-50/90 sm:text-[13.5px] sm:leading-7"><?php echo esc_html( $es_description ); ?></p>
				<?php endif; ?>
				<div class="mt-6 flex flex-wrap items-center gap-4">
					<?php if ( $es_has_file ) : ?>
						<a href="<?php echo esc_url( $es_file_url ); ?>" download class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-xs font-black text-emerald-900 transition-colors hover:bg-emerald-50 sm:text-sm">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M12 15V3"></path><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5"></path></svg>
							<span><?php echo esc_html( $es_button_label ); ?></span>
						</a>
					<?php endif; ?>
					<span class="text-xs font-bold text-emerald-100 tabular-nums"><?php echo esc_html( $es_note ); ?></span>
				</div>
			</div>
			<div class="flex justify-center lg:col-span-4 lg:justify-end">
				<?php if ( $es_has_file ) : ?>
					<a href="<?php echo esc_url( $es_file_url ); ?>" download class="group relative block h-44 w-64 overflow-hidden rounded-2xl bg-slate-900 p-5 shadow-xl transition-transform duration-300 hover:-translate-y-1">
				<?php else : ?>
					<div class="group relative block h-44 w-64 overflow-hidden rounded-2xl bg-slate-900 p-5 shadow-xl">
				<?php endif; ?>
					<img src="<?php echo esc_url( $es_cover_img ); ?>" alt="<?php echo esc_attr( $es_heading ); ?>" class="absolute inset-0 h-full w-full object-cover opacity-55 transition-transform duration-500 group-hover:scale-105"/>
					<div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/55 to-transparent"></div>
					<div class="relative z-10 flex h-full flex-col justify-between text-right">
						<div class="flex items-center justify-between">
							<span class="rounded-lg bg-emerald-500 px-2.5 py-0.5 text-[10px] font-black text-slate-950 tabular-nums"><?php echo esc_html( $es_version ); ?></span>
							<span class="font-mono text-xs font-black text-white/80">ESE</span>
						</div>
						<div>
							<div class="text-[11px] font-bold text-emerald-300">کاتالوگ فنی و اجرایی</div>
							<div class="mt-0.5 flex items-center justify-between text-sm font-black text-white">
								<span>محصولات نورپردازی شهری</span>
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 text-emerald-300" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg>
							</div>
						</div>
					</div>
				<?php if ( $es_has_file ) : ?>
					</a>
				<?php else : ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
