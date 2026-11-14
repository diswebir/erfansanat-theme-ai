# -*- coding: utf-8 -*-
import json
icons=json.load(open('temp/phase-05-homepage-and-sections/ref-about-svg.json',encoding='utf-8'))
tpl=r'''<?php
/**
 * About + stats section (P05-T010). DOM parity with static-mirror/index.html
 * (id=about). Heading/intro/image/stats are editable via the section fields
 * (AC-02); each stat carries data-counter-target / data-counter-suffix so the
 * immutable main.js initCounters animates it (AC-03 / SC-02).
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'about' );
$es_type = (string) ( $es_section['type'] ?? 'about' );

$es_heading = (string) ( $es_fields['heading'] ?? 'بیش از ۲۵ سال تجربه مهندسی در تولید سازه‌های نوری شهری' );
$es_content = (string) ( $es_fields['content'] ?? '' );
if ( '' === trim( $es_content ) ) {
	$es_content = '<p>شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE) با تمرکز بر طراحی و تولید صنعتی تجهیزات روشنایی و المان‌های نوری شهری، طیف کاملی از محصولات استاندارد را به شهرداری‌ها، سازمان‌های زیباسازی و پیمانکاران عمرانی ارائه می‌دهد. تمامی تولیدات کارخانه با عایق‌بندی تزریق مستقیم IP68 و سازگاری کامل با اقلیم‌های مختلف کشور تولید می‌شوند.</p>';
}

$es_image_id = (int) ( $es_fields['image'] ?? 0 );
$es_image    = $es_image_id > 0 ? (string) wp_get_attachment_image_url( $es_image_id, 'large' ) : '';
if ( ! $es_image ) {
	$es_image = ES_ASSETS_URI . 'images/factory.jpg';
}

$es_eyebrow      = (string) apply_filters( 'es_about_eyebrow', 'درباره شرکت دانش‌بنیان عرفان صنعت اصفهان' );
$es_about_url    = (string) apply_filters( 'es_about_url', home_url( '/about/' ) );
$es_caption_top  = (string) apply_filters( 'es_about_image_caption_top', 'خط تولید صنعتی · شهرک صنعتی جی اصفهان' );
$es_caption_main = (string) apply_filters( 'es_about_image_caption_main', 'تولید تخصصی المان‌های نوری و ریسه‌های تزریق مستقیم' );
$es_features     = (array) apply_filters(
	'es_about_features',
	array(
		'طراحی، تولید و اجرای صفر تا صد پروژه‌های روشنایی شهری و میادین',
		'دارای گواهینامه بین‌المللی ISO 9001:2015 و ثبت رسمی در سامانه ستاد',
		'۲۴ ماه گارانتی تعویض بی‌قیدوشرط کارخانه + ۵ سال خدمات پس از فروش',
	)
);

// Stats: section field, else the mirror defaults (filterable).
$es_default_stats = array(
	array( 'number' => '۸۵۰+', 'label' => 'پروژه تکمیل‌شده' ),
	array( 'number' => '۱۰۰+', 'label' => 'نیروی متخصص' ),
	array( 'number' => '۲۸۹+', 'label' => 'کارفرمای سازمانی' ),
	array( 'number' => '۱۲۵+', 'label' => 'محصول تخصصی' ),
);
$es_stats = ! empty( $es_fields['stats'] ) && is_array( $es_fields['stats'] )
	? $es_fields['stats']
	: (array) apply_filters( 'es_about_default_stats', $es_default_stats );

$es_fa_to_ascii = array( '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9' );
$es_ascii_to_fa = array_flip( $es_fa_to_ascii );

/**
 * Parse a stat number into an integer counter target + suffix + Persian
 * display, so initCounters (parseInt on data-counter-target) works (AC-03).
 */
$es_parse_stat = static function ( $stat ) use ( $es_fa_to_ascii, $es_ascii_to_fa ) {
	$raw   = (string) ( $stat['number'] ?? '0' );
	$ascii = strtr( $raw, $es_fa_to_ascii );
	if ( preg_match( '/(\d+)(.*)$/u', $ascii, $m ) ) {
		$target = (int) $m[1];
		$suffix = trim( $m[2] );
		if ( '' === $suffix ) {
			$suffix = '+';
		}
	} else {
		$target = 0;
		$suffix = '+';
	}
	return array(
		'target'  => $target,
		'suffix'  => $suffix,
		'display' => strtr( (string) $target, $es_ascii_to_fa ) . $suffix,
		'label'   => (string) ( $stat['label'] ?? '' ),
	);
};
?>
<section id="about" class="es-section es-section--about relative z-10 py-12 sm:py-16 lg:py-20" style="scroll-margin-top:5rem" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="mx-auto grid max-w-[1300px] items-center gap-10 px-4 sm:px-6 lg:grid-cols-12 lg:gap-12 lg:px-8">
		<div class="flex flex-col items-start gap-5 lg:col-span-6">
			<div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]">
				<span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
				<span><?php echo esc_html( $es_eyebrow ); ?></span>
			</div>
			<h2 class="text-2xl font-black leading-[1.35] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]"><?php echo esc_html( $es_heading ); ?></h2>
			<div class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8"><?php echo wp_kses_post( $es_content ); ?></div>
			<?php if ( ! empty( $es_features ) ) : ?>
				<div class="w-full">
					<ul class="grid gap-2.5">
						<?php foreach ( $es_features as $es_feature ) : ?>
							<li class="flex items-center gap-3 rounded-xl border border-slate-200/80 bg-white px-4 py-3 text-xs sm:text-[13.5px] font-bold text-slate-800">__CIRCLE_CHECK__<span><?php echo esc_html( (string) $es_feature ); ?></span></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			<div class="flex w-full flex-col gap-3 pt-1 sm:w-auto sm:flex-row sm:items-center">
				<a href="<?php echo esc_url( $es_about_url ); ?>" class="group inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-6 py-3.5 text-xs sm:text-sm font-black text-white transition-colors hover:bg-emerald-800">
					<span>آشنایی با خط تولید کارخانه</span>
					__ARROW_UL__
				</a>
				<div class="inline-flex items-center gap-2.5 rounded-xl border border-slate-200/90 bg-white px-4 py-2.5">
					__AWARD__
					<div class="leading-tight">
						<div class="text-xs font-extrabold text-slate-900">ISO 9001:2015</div>
						<div class="text-[11px] font-medium text-slate-500">تضمین کیفیت بین‌المللی</div>
					</div>
				</div>
			</div>
		</div>
		<div class="lg:col-span-6">
			<div class="relative overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950">
				<img src="<?php echo esc_url( $es_image ); ?>" alt="<?php echo esc_attr( $es_heading ); ?>" loading="lazy" class="h-56 w-full object-cover sm:h-64"/>
				<div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/20 to-transparent"></div>
				<div class="absolute inset-x-0 bottom-0 flex items-center justify-between p-5">
					<div>
						<div class="text-xs font-bold text-emerald-300"><?php echo esc_html( $es_caption_top ); ?></div>
						<div class="mt-0.5 text-sm sm:text-base font-black text-white"><?php echo esc_html( $es_caption_main ); ?></div>
					</div>
					<span class="hidden rounded-lg bg-white/15 px-2.5 py-1 font-mono text-xs font-black text-white backdrop-blur-md sm:inline-block">ESE FACTORY</span>
				</div>
			</div>
			<?php if ( ! empty( $es_stats ) ) : ?>
				<div class="mt-4 grid grid-cols-2 gap-3.5 sm:gap-4">
					<?php foreach ( $es_stats as $es_stat ) : ?>
						<?php $es_s = $es_parse_stat( $es_stat ); ?>
						<div class="card-light flex flex-col justify-between rounded-2xl p-4 sm:p-5">
							<span data-counter-target="<?php echo (int) $es_s['target']; ?>" data-counter-suffix="<?php echo esc_attr( $es_s['suffix'] ); ?>" class="text-2xl sm:text-3xl font-black tracking-tight text-emerald-700 tabular-nums lg:text-4xl"><?php echo esc_html( $es_s['display'] ); ?></span>
							<div class="mt-2">
								<div class="text-xs sm:text-[13.5px] font-extrabold text-slate-900"><?php echo esc_html( $es_s['label'] ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
'''
tpl=tpl.replace('__CIRCLE_CHECK__',icons['circle-check']).replace('__ARROW_UL__',icons['arrow-up-left']).replace('__AWARD__',icons['award'])
open('erfansanat/template-parts/sections/home/section-about.php','w',encoding='utf-8').write(tpl)
print("written",len(tpl),"bytes; leftover:",__import__('re').findall(r'__[A-Z0-9_]+__',tpl))
