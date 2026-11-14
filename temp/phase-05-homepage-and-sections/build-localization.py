# -*- coding: utf-8 -*-
import json
d=json.load(open('temp/phase-05-homepage-and-sections/ref-localization-svg.json',encoding='utf-8'))
arrow=d['arrow']; notch=d['notch']
tpl=r'''<?php
/**
 * Localization section (P05-T011). DOM parity with the reference green
 * gradient card. Heading/content/image are editable via section fields and
 * the badges/gallery are filterable (AC-01). Empty fields fall back to
 * defaults so the section is never removed unless disabled (AC-03).
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'localization' );
$es_type = (string) ( $es_section['type'] ?? 'localization' );

$es_heading = (string) ( $es_fields['heading'] ?? 'مرجع بومی‌سازی سازه‌های نوری و تجهیزات روشنایی شهری در ایران' );
$es_content = (string) ( $es_fields['content'] ?? '' );
if ( '' === trim( $es_content ) ) {
	$es_content = 'شرکت دانش‌بنیان عرفان صنعت اصفهان با بهره‌گیری از خطوط تمام‌اتوماتیک تزریق پلیمر، واحد قالب‌سازی دقیق و آزمایشگاه کنترل کیفیت، محصولات خود را با استاندارد حفاظتی واقعی IP68 و کدهای رسمی سامانه ستاد و ایران‌کد (GS1) تولید و به سراسر کشور عرضه می‌نماید.';
}

$es_image_id = (int) ( $es_fields['image'] ?? 0 );
$es_image    = $es_image_id > 0 ? (string) wp_get_attachment_image_url( $es_image_id, 'large' ) : '';
if ( ! $es_image ) {
	$es_image = ES_ASSETS_URI . 'images/hero.jpg';
}

$es_eyebrow   = (string) apply_filters( 'es_localization_eyebrow', 'اصالت و هویت صنعتی کارخانه' );
$es_founded   = (string) apply_filters( 'es_localization_founded', 'تاسیس کارخانه از سال ۱۳۸۰ خورشیدی' );
$es_contact   = (string) apply_filters( 'es_localization_contact_url', home_url( '/contact/' ) );
$es_badges    = (array) apply_filters(
	'es_localization_badges',
	array(
		array( 'title' => 'ISO 9001:2015', 'subtitle' => 'مدیریت کیفیت' ),
		array( 'title' => 'درجه حفاظت IP68', 'subtitle' => 'تزریق مستقیم رزین' ),
		array( 'title' => 'ایران‌کد GS1', 'subtitle' => 'ثبت رسمی سامانه ستاد' ),
	)
);
$es_gallery   = (array) apply_filters(
	'es_localization_gallery',
	array(
		array( 'image' => ES_ASSETS_URI . 'images/light-tunnel.jpg', 'alt' => 'پروژه‌های تونل نوری عرفان صنعت', 'caption_top' => 'سازه و المان شهری', 'caption_main' => 'تونل‌های نوری پیکسلی و کمانی', 'span' => 'col-span-7' ),
		array( 'image' => ES_ASSETS_URI . 'images/light-tree.jpg', 'alt' => 'تولید درختان نوری هوشمند', 'caption_top' => 'تولید انحصاری', 'caption_main' => 'درختان نوری هوشمند NRF', 'span' => 'col-span-5' ),
	)
);
?>
<section aria-label="درباره تاسیس شرکت دانش‌بنیان عرفان صنعت اصفهان" class="es-section es-section--localization mx-auto max-w-[1300px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="relative overflow-hidden rounded-[2.25rem] border-0 bg-gradient-to-l from-[#064e3b] via-[#065f46] to-[#047857] text-white shadow-[0_24px_60px_-24px_rgba(4,120,87,0.38)] sm:rounded-[2.5rem]">
		<img src="<?php echo esc_url( $es_image ); ?>" alt="<?php echo esc_attr( $es_heading ); ?>" class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-14 mix-blend-luminosity"/>
		<div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(52,211,153,0.22),transparent_55%)]"></div>
		<div class="absolute -top-0.5 -right-0.5 z-20">
			<div class="relative rounded-bl-[1.75rem] bg-[#f6f8f7] px-6 py-2.5 sm:rounded-bl-[2rem] sm:px-8 sm:py-3">
				<div class="flex items-center gap-2 text-xs sm:text-sm font-black text-emerald-700">
					<span class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( $es_eyebrow ); ?></span>
				</div>
				__NOTCH0__
				__NOTCH1__
			</div>
		</div>
		<div class="relative z-10 border-0 px-6 pt-15 pb-15 sm:px-10 sm:pt-16 sm:pb-16 lg:px-12">
			<div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-10">
				<div class="text-right lg:col-span-6">
					<div class="inline-flex flex-wrap items-center gap-2.5 rounded-2xl bg-white/10 px-3.5 py-1.5 backdrop-blur-xs">
						<span dir="ltr" class="font-mono text-lg font-black tracking-[0.14em] text-white sm:text-xl">ESE</span>
						<span class="h-3.5 w-px bg-white/25"></span>
						<span class="text-xs font-extrabold text-emerald-100 sm:text-sm"><?php echo esc_html( $es_founded ); ?></span>
					</div>
					<h3 class="mt-4 text-xl font-black leading-snug text-white sm:text-2xl [text-wrap:balance]"><?php echo esc_html( $es_heading ); ?></h3>
					<p class="mt-3 text-right text-xs leading-7 font-medium text-emerald-50/90 sm:text-[13.5px] sm:leading-7"><?php echo esc_html( wp_strip_all_tags( $es_content ) ); ?></p>
					<?php if ( ! empty( $es_badges ) ) : ?>
						<div class="mt-5 grid grid-cols-1 gap-2 xs:grid-cols-3 xs:gap-2.5">
							<?php foreach ( $es_badges as $es_badge ) : ?>
								<div class="rounded-xl bg-white/10 px-2.5 py-2 backdrop-blur-xs xs:px-3 xs:py-2.5">
									<div class="text-[11.5px] xs:text-xs font-black text-white tabular-nums"><?php echo esc_html( (string) ( $es_badge['title'] ?? '' ) ); ?></div>
									<div class="mt-0.5 text-[10px] xs:text-[10.5px] font-semibold text-emerald-100/85"><?php echo esc_html( (string) ( $es_badge['subtitle'] ?? '' ) ); ?></div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="grid grid-cols-12 gap-4 lg:col-span-6">
					<?php foreach ( $es_gallery as $es_g ) : ?>
						<div class="group relative <?php echo esc_attr( (string) ( $es_g['span'] ?? 'col-span-6' ) ); ?> h-48 overflow-hidden rounded-2xl bg-emerald-950/40 shadow-lg sm:h-56">
							<img src="<?php echo esc_url( (string) ( $es_g['image'] ?? '' ) ); ?>" alt="<?php echo esc_attr( (string) ( $es_g['alt'] ?? '' ) ); ?>" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"/>
							<div class="absolute inset-0 bg-gradient-to-t from-slate-950/75 via-transparent to-transparent"></div>
							<div class="absolute right-3.5 bottom-3 text-right">
								<div class="text-[10.5px] font-bold text-emerald-300"><?php echo esc_html( (string) ( $es_g['caption_top'] ?? '' ) ); ?></div>
								<div class="text-xs font-extrabold text-white"><?php echo esc_html( (string) ( $es_g['caption_main'] ?? '' ) ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div class="absolute -bottom-0.5 -left-0.5 z-20">
			<div class="relative rounded-tr-[1.75rem] bg-[#f6f8f7] px-6 py-2.5 sm:rounded-tr-[2rem] sm:px-8 sm:py-3">
				<a href="<?php echo esc_url( $es_contact ); ?>" class="inline-flex items-center gap-2 text-xs font-black text-emerald-700 transition-colors hover:text-emerald-900 sm:text-sm">
					<span>ارتباط مستقیم با کارخانه</span>
					__ARROW__
				</a>
				__NOTCH2__
				__NOTCH3__
			</div>
		</div>
	</div>
</section>
'''
tpl=tpl.replace('__ARROW__',arrow)
for i,n in enumerate(notch): tpl=tpl.replace('__NOTCH%d__'%i,n)
open('erfansanat/template-parts/sections/home/section-localization.php','w',encoding='utf-8').write(tpl)
print("written",len(tpl),"bytes; leftover:",__import__('re').findall(r'__[A-Z0-9_]+__',tpl))
