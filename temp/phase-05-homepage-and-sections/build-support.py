# -*- coding: utf-8 -*-
import re, json
sec = open('temp/phase-05-homepage-and-sections/ref-section-support.html', encoding='utf-8').read()
# Big Iran map SVG.
map_svg = re.search(r'<svg viewBox="0 0 680 620".*?</svg>', sec, re.S).group(0)
# Corner curve SVGs (viewBox 0 0 32 32), in document order.
corners = re.findall(r'<svg viewBox="0 0 32 32".*?</svg>', sec, re.S)
assert len(corners) == 4, len(corners)
icons = {}
for s in re.findall(r'<svg.*?</svg>', sec, re.S):
    m = re.search(r'lucide lucide-([\w-]+)', s)
    if m and m.group(1) not in icons:
        icons[m.group(1)] = s
json.dump({'map': map_svg, 'corners': corners, 'icons': icons},
          open('temp/phase-05-homepage-and-sections/ref-support-svg.json', 'w', encoding='utf-8'), ensure_ascii=False)

tpl = r'''<?php
/**
 * Support & warranty section (P05-T017). DOM parity with static-mirror
 * (green gradient card + Iran service-network map). Heading/content/phone
 * come from the section fields (AC-01); the warranty stat links to the
 * refund_returns page via Menus::page_url (AC-02); reference classes are
 * preserved (AC-03). The mirror's double `tel:tel:` href bug is fixed to a
 * single normalized `tel:` link (SC-02).
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'support' );
$es_type = (string) ( $es_section['type'] ?? 'support' );

$es_heading = (string) ( $es_fields['heading'] ?? 'پشتیبانی فنی و گارانتی تعویض' );
$es_content = (string) ( $es_fields['content'] ?? '' );
if ( '' === trim( $es_content ) ) {
	$es_content = '<p>' . esc_html( (string) apply_filters( 'es_support_default_content', 'همراهی عرفان صنعت با شهرداری‌ها و پیمانکاران پس از تحویل کالا آغاز می‌شود. ارائه نقشه‌های اجرایی، اعزام تیم نصب، تامین قطعات اصلی و ۲۴ ماه گارانتی تعویض سریع در تمامی ۳۱ استان کشور تضمین شده است.' ) ) . '</p>';
}

$es_phone         = (string) ( $es_fields['phone'] ?? '031-91091011' );
$es_tel           = preg_replace( '/[^0-9+]/', '', $es_phone );
$es_fa_digits     = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_phone_display = strtr( $es_phone, $es_fa_digits );

$es_eyebrow  = (string) apply_filters( 'es_support_eyebrow', 'شبکه خدمات پس از فروش ESE' );
$es_coverage = (string) apply_filters( 'es_support_coverage', 'پوشش سراسری ۳۱ استان کشور با مرکزیت کارخانه اصفهان' );

// AC-02: the warranty stat links to the refund_returns page.
$es_warranty_url = Menus::page_url( 'refund_returns' );
$es_stats        = (array) apply_filters( 'es_support_stats', array(
	array( 'title' => 'ارسال مستقیم', 'description' => 'از کارخانه مرکزی اصفهان', 'url' => '' ),
	array( 'title' => '۲۴ ماه گارانتی', 'description' => 'ضمانت تعویض بی‌قیدوشرط', 'url' => $es_warranty_url ),
	array( 'title' => '۵ سال پشتیبانی', 'description' => 'تامین قطعات در ۳۱ استان', 'url' => '' ),
) );
?>
<section id="support" aria-label="خدمات پس از فروش عرفان صنعت در سراسر ایران" class="es-section es-section--support mx-auto max-w-[1300px] px-4 py-6 sm:px-6 sm:py-8 lg:px-8" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="relative overflow-hidden rounded-[2.25rem] border-0 bg-gradient-to-l from-[#064e3b] via-[#065f46] to-[#047857] text-white shadow-[0_24px_60px_-22px_rgba(4,120,87,0.38)] sm:rounded-[2.5rem]">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_25%_50%,rgba(52,211,153,0.22),transparent_62%)]"></div>
		<div class="absolute -top-0.5 -right-0.5 z-20">
			<div class="relative rounded-bl-[1.75rem] bg-[#f6f8f7] px-6 py-2.5 sm:rounded-bl-[2rem] sm:px-8 sm:py-3">
				<div class="flex items-center gap-2 text-xs sm:text-sm font-black text-emerald-700">
					<span class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( $es_eyebrow ); ?></span>
				</div>
				__CORNER1__
				__CORNER2__
			</div>
		</div>
		<div class="relative z-10 border-0 grid items-center gap-8 px-6 pt-15 pb-15 sm:px-10 sm:pt-16 sm:pb-16 lg:grid-cols-12 lg:px-12">
			<div class="text-right lg:col-span-6">
				<div class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-3 py-1.5 text-xs font-extrabold text-emerald-100 backdrop-blur-xs">
					__SHIELD__
					<span><?php echo esc_html( $es_coverage ); ?></span>
				</div>
				<h2 class="mt-3.5 text-xl font-black text-white sm:text-2xl lg:text-[1.75rem]"><?php echo esc_html( $es_heading ); ?></h2>
				<div class="mt-3 max-w-xl text-right text-xs leading-7 font-medium text-emerald-50/90 sm:text-[13.5px] sm:leading-7"><?php echo wp_kses_post( $es_content ); ?></div>
				<div class="mt-5 grid grid-cols-1 gap-2.5 sm:grid-cols-3">
					<?php foreach ( $es_stats as $es_stat ) :
						$es_stat = (array) $es_stat;
						$es_surl = (string) ( $es_stat['url'] ?? '' );
						$es_scls = 'rounded-xl bg-white/10 px-3.5 py-3 backdrop-blur-xs';
						?>
						<?php if ( '' !== $es_surl ) : ?>
							<a href="<?php echo esc_url( $es_surl ); ?>" class="<?php echo esc_attr( $es_scls ); ?> transition-colors hover:bg-white/15">
						<?php else : ?>
							<div class="<?php echo esc_attr( $es_scls ); ?>">
						<?php endif; ?>
							<div class="text-xs font-black text-white tabular-nums"><?php echo esc_html( (string) ( $es_stat['title'] ?? '' ) ); ?></div>
							<div class="mt-0.5 text-[11px] font-medium text-emerald-100/85"><?php echo esc_html( (string) ( $es_stat['description'] ?? '' ) ); ?></div>
						<?php if ( '' !== $es_surl ) : ?>
							</a>
						<?php else : ?>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
			<div class="flex justify-center lg:col-span-6">
				<div class="relative w-full max-w-[440px]">
					__MAP_SVG__
				</div>
			</div>
		</div>
		<div class="absolute -bottom-0.5 -left-0.5 z-20">
			<div class="relative rounded-tr-[1.75rem] bg-[#f6f8f7] px-6 py-2.5 sm:rounded-tr-[2rem] sm:px-8 sm:py-3">
				<a href="<?php echo esc_url( 'tel:' . $es_tel ); ?>" class="inline-flex items-center gap-2 text-xs font-black text-emerald-700 tabular-nums transition-colors hover:text-emerald-900 sm:text-sm">
					<span dir="ltr"><?php echo esc_html( $es_phone_display ); ?></span>
					__PHONE_CALL__
				</a>
				__CORNER3__
				__CORNER4__
			</div>
		</div>
	</div>
</section>
'''
tpl = tpl.replace('__MAP_SVG__', map_svg)
for i, c in enumerate(corners, 1):
    tpl = tpl.replace('__CORNER%d__' % i, c)
tpl = tpl.replace('__SHIELD__', icons['shield-check']).replace('__PHONE_CALL__', icons['phone-call'])
open('erfansanat/template-parts/sections/home/section-support.php', 'w', encoding='utf-8').write(tpl)
print("written", len(tpl), "bytes; leftover:", re.findall(r'__[A-Z0-9_]+__', tpl))
