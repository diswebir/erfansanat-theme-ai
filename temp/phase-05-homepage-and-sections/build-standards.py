# -*- coding: utf-8 -*-
import json, re
icons = json.load(open('temp/phase-05-homepage-and-sections/ref-standards-svg.json', encoding='utf-8'))

def phpstr(s):
    return "'" + s.replace("\\", "\\\\").replace("'", "\\'") + "'"

star = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-5.5 w-5.5" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>'
truck = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck h-5.5 w-5.5" aria-hidden="true"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg>'

icon_map = [
    ('badge-check', icons['badge-check']), ('cloud-rain', icons['cloud-rain']),
    ('radio-tower', icons['radio-tower']), ('shield-check', icons['shield-check']),
    ('check', icons['badge-check']), ('shield', icons['shield-check']),
    ('star', star), ('truck', truck),
    ('search', icons['search']), ('pencil-ruler', icons['pencil-ruler']),
    ('wrench', icons['wrench']), ('hard-hat', icons['hard-hat']), ('lightbulb', icons['lightbulb']),
]
php_icons = "\n".join("\t" + phpstr(k) + ' => ' + phpstr(v) + ',' for k, v in icon_map)

tpl = r'''<?php
/**
 * Quality standards section (P05-T016). DOM parity with static-mirror.
 * Cards are editable via the `items` repeater (icon/title/description —
 * AC-01); empty items are skipped (AC-03). The badge line is a defaults-only
 * extra (Schema repeater has no badge subfield) and disappears on saved
 * items. The 5-step process box is static reference DOM with filterable
 * content. Empty repeater falls back to the four reference cards.
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'standards' );
$es_type = (string) ( $es_section['type'] ?? 'standards' );

$es_heading    = (string) ( $es_fields['heading'] ?? 'استانداردهای کیفیت و ماندگاری' );
$es_eyebrow    = (string) apply_filters( 'es_standards_eyebrow', 'مزیت‌های رقابتی کارخانه' );
$es_subheading = (string) apply_filters( 'es_standards_subheading', 'تجهیزات نورپردازی شهری سرمایه‌گذاری بلندمدت هستند؛ هر محصول عرفان صنعت برای سال‌ها کارکرد مداوم در فضای باز مهندسی شده است.' );

// Static lucide SVG markup keyed by slug (filterable). Admin-pickable slugs
// (check/shield/star/truck) resolve alongside the reference card icons.
$es_icon_svgs = (array) apply_filters( 'es_standards_icon_svgs', array(
__PHP_ICONS__
) );

$es_default_items = (array) apply_filters( 'es_standards_default_items', array(
	array( 'icon' => 'badge-check', 'badge' => 'ISO 9001:2015', 'title' => 'مدیریت کیفیت بین‌المللی', 'description' => 'کنترل کیفیت دقیق در تمامی مراحل قالب‌سازی، تزریق پلیمر و مونتاژ بردهای الکترونیکی' ),
	array( 'icon' => 'cloud-rain', 'badge' => 'IP67 / IP68', 'title' => 'عایق‌بندی تزریق مستقیم', 'description' => 'مقاومت کامل در برابر نفوذ آب، رطوبت شدید، گرد و غبار و تابش مستقیم UV خورشید' ),
	array( 'icon' => 'radio-tower', 'badge' => 'NRF + WiFi', 'title' => 'کنترل و همگام‌سازی هوشمند', 'description' => 'مدیریت بی‌سیم افکت‌های نوری و طیف ۱۶ میلیون رنگ بدون نیاز به سیم‌کشی دیتا بین المان‌ها' ),
	array( 'icon' => 'shield-check', 'badge' => '۲۴ ماه گارانتی', 'title' => 'گارانتی تعویض + ۵ سال خدمات', 'description' => 'ضمانت رسمی تعویض کارخانه و تامین پایدار قطعات یدکی برای شهرداری‌ها و پیمانکاران' ),
) );

$es_items = $es_fields['items'] ?? array();
if ( ! is_array( $es_items ) || array() === $es_items ) {
	$es_items = $es_default_items;
}
// AC-03: an item with neither title nor description is not rendered.
$es_items = array_values( array_filter( $es_items, static function ( $it ) {
	return is_array( $it ) && ( '' !== trim( (string) ( $it['title'] ?? '' ) ) || '' !== trim( (string) ( $it['description'] ?? '' ) ) );
} ) );

$es_fa_digits = static function ( $value ) {
	return strtr( (string) $value, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
};

$es_process_heading   = (string) apply_filters( 'es_standards_process_heading', 'فرآیند اجرای پروژه در عرفان صنعت؛' );
$es_process_highlight = (string) apply_filters( 'es_standards_process_highlight', 'از ایده تا درخشش در شهر' );
$es_process_note      = (string) apply_filters( 'es_standards_process_note', '۵ گام استاندارد مهندسی و اجرایی' );
$es_process_steps     = (array) apply_filters( 'es_standards_process_steps', array(
	array( 'icon' => 'search', 'title' => 'مشاوره و نیازسنجی', 'description' => 'بررسی ابعاد و اقلیم پروژه' ),
	array( 'icon' => 'pencil-ruler', 'title' => 'طراحی سه‌بعدی اختصاصی', 'description' => 'شبیه‌سازی قبل از ساخت' ),
	array( 'icon' => 'wrench', 'title' => 'تولید صنعتی در کارخانه', 'description' => 'متریال پلی‌کربنات و IP68' ),
	array( 'icon' => 'hard-hat', 'title' => 'نصب و اجرای ایمن', 'description' => 'توسط تیم فنی مجرب' ),
	array( 'icon' => 'lightbulb', 'title' => 'پشتیبانی و گارانتی', 'description' => '۲۴ ماه ضمانت تعویض' ),
) );
?>
<section id="standards" class="es-section es-section--standards relative z-10 py-12 sm:py-16 lg:py-20" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
		<div class="flex max-w-2xl flex-col gap-3 items-center text-center mx-auto">
			<div>
				<div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]">
					<span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( $es_eyebrow ); ?></span>
				</div>
			</div>
			<div>
				<h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]"><?php echo esc_html( $es_heading ); ?></h2>
			</div>
			<?php if ( '' !== trim( $es_subheading ) ) : ?>
				<div>
					<p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8"><?php echo esc_html( $es_subheading ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php if ( array() !== $es_items ) : ?>
			<div class="mt-8 sm:mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
				<?php foreach ( $es_items as $es_item ) :
					$es_item  = (array) $es_item;
					$es_slug  = (string) ( $es_item['icon'] ?? 'check' );
					$es_svg   = (string) ( $es_icon_svgs[ $es_slug ] ?? $es_icon_svgs['badge-check'] );
					$es_badge = (string) ( $es_item['badge'] ?? '' );
					$es_title = (string) ( $es_item['title'] ?? '' );
					$es_desc  = (string) ( $es_item['description'] ?? '' );
					?>
					<div>
						<div class="card-light flex h-full flex-col justify-between rounded-2xl p-5 sm:p-6">
							<div>
								<div class="flex items-center justify-between gap-2">
									<span class="grid h-11 w-11 place-items-center rounded-xl border border-emerald-100 bg-emerald-50/70 text-emerald-700"><?php echo $es_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG. ?></span>
									<?php if ( '' !== $es_badge ) : ?>
										<span class="font-mono text-xs font-extrabold text-emerald-700 tabular-nums"><?php echo esc_html( $es_badge ); ?></span>
									<?php endif; ?>
								</div>
								<?php if ( '' !== $es_title ) : ?>
									<h3 class="mt-4 text-base font-black text-slate-900"><?php echo esc_html( $es_title ); ?></h3>
								<?php endif; ?>
								<?php if ( '' !== $es_desc ) : ?>
									<p class="mt-2 text-xs sm:text-[13px] font-medium leading-6 text-slate-600"><?php echo esc_html( $es_desc ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<p class="mt-8 text-center text-sm font-medium text-slate-500">استانداردی برای نمایش وجود ندارد.</p>
		<?php endif; ?>
		<div class="mt-8 sm:mt-10">
			<div class="rounded-3xl border border-slate-200/85 bg-white p-6 sm:p-8 lg:p-10">
				<div class="flex flex-col items-center justify-between gap-2 border-b border-slate-100 pb-6 sm:flex-row">
					<h3 class="text-base sm:text-lg font-black text-slate-900"><?php echo esc_html( $es_process_heading ); ?> <span class="text-emerald-700"><?php echo esc_html( $es_process_highlight ); ?></span></h3>
					<span class="text-xs font-bold text-slate-500"><?php echo esc_html( $es_process_note ); ?></span>
				</div>
				<div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-5">
					<?php foreach ( $es_process_steps as $es_si => $es_step ) :
						$es_step = (array) $es_step;
						$es_ssvg = (string) ( $es_icon_svgs[ (string) ( $es_step['icon'] ?? 'check' ) ] ?? $es_icon_svgs['badge-check'] );
						?>
						<div class="flex items-center gap-3.5 rounded-2xl border border-slate-100 bg-[#f6f8f7]/80 p-3.5 lg:flex-col lg:items-start lg:p-4">
							<div class="flex w-full items-center justify-between">
								<span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-white text-emerald-700 shadow-2xs"><?php echo $es_ssvg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG. ?></span>
								<span class="text-xs font-black text-emerald-700/70 tabular-nums"><?php echo esc_html( 'گام ' . $es_fa_digits( $es_si + 1 ) ); ?></span>
							</div>
							<div>
								<div class="text-xs sm:text-[13px] font-extrabold text-slate-900"><?php echo esc_html( (string) ( $es_step['title'] ?? '' ) ); ?></div>
								<div class="mt-0.5 text-[11px] font-medium text-slate-500"><?php echo esc_html( (string) ( $es_step['description'] ?? '' ) ); ?></div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>
</section>
'''
tpl = tpl.replace('__PHP_ICONS__', php_icons)
open('erfansanat/template-parts/sections/home/section-standards.php', 'w', encoding='utf-8').write(tpl)
print("written", len(tpl), "bytes; leftover:", re.findall(r'__[A-Z0-9_]+__', tpl))
