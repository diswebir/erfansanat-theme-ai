# -*- coding: utf-8 -*-
import json, re
data = json.load(open('temp/phase-05-homepage-and-sections/ref-clients-data.json', encoding='utf-8'))
landmark = data['landmark']

def phpstr(s):
    return "'" + s.replace("\\", "\\\\").replace("'", "\\'") + "'"

php_items = "\n".join("\tarray( 'name' => " + phpstr(it['name']) + ", 'description' => " + phpstr(it['description']) + " )," for it in data['items'])

tpl = r'''<?php
/**
 * Clients & partners section (P05-T018). DOM parity with static-mirror
 * (12 reference cards, grid-cols-2/3/6). Logos come from the `logos` media
 * repeater (image + name — AC-01); an optional per-item `url` wraps the card
 * in a link, and an empty url renders no anchor (AC-03 / SC-02). Logo images
 * are lazy-loaded with alt text (AC-02). Items without an image fall back to
 * the reference landmark icon card. Empty repeater falls back to the 12
 * reference clients.
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'clients' );
$es_type = (string) ( $es_section['type'] ?? 'clients' );

$es_heading    = (string) ( $es_fields['heading'] ?? 'مشتریان و سازمان‌های همکار' );
$es_eyebrow    = (string) apply_filters( 'es_clients_eyebrow', 'شبکه گسترده کارفرمایان' );
$es_subheading = (string) apply_filters( 'es_clients_subheading', 'افتخار همراهی با بیش از ۱۸۰ شهرداری، سازمان زیباسازی و پیمانکار عمرانی در سراسر کشور' );

$es_default_items = (array) apply_filters( 'es_clients_default_logos', array(
__PHP_ITEMS__
) );

$es_items = $es_fields['logos'] ?? array();
if ( ! is_array( $es_items ) || array() === $es_items ) {
	$es_items = $es_default_items;
}
// Skip items with neither a name nor an image.
$es_items = array_values( array_filter( $es_items, static function ( $it ) {
	return is_array( $it ) && ( '' !== trim( (string) ( $it['name'] ?? '' ) ) || (int) ( $it['image'] ?? 0 ) > 0 );
} ) );

$es_fa_digits = static function ( $value ) {
	return strtr( (string) $value, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
};

$es_landmark = '__LANDMARK__';
?>
<section id="clients" aria-label="مشتریان و سازمان‌های همکار عرفان صنعت" class="es-section es-section--clients mx-auto max-w-[1300px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
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
		<div class="mt-8 sm:mt-10 grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-6">
			<?php foreach ( $es_items as $es_i => $es_item ) :
				$es_item = (array) $es_item;
				$es_name = (string) ( $es_item['name'] ?? '' );
				$es_desc = (string) ( $es_item['description'] ?? '' );
				$es_url  = (string) ( $es_item['url'] ?? '' );
				$es_img  = (int) ( $es_item['image'] ?? 0 );
				$es_logo = $es_img > 0 ? wp_get_attachment_image_url( $es_img, 'medium' ) : false;
				$es_num  = $es_fa_digits( str_pad( (string) ( $es_i + 1 ), 2, '0', STR_PAD_LEFT ) );
				$es_ccls = 'card-light flex flex-col items-start justify-between rounded-2xl p-4 text-right';
				?>
				<?php if ( '' !== $es_url ) : ?>
					<a href="<?php echo esc_url( $es_url ); ?>" class="<?php echo esc_attr( $es_ccls ); ?> transition-colors hover:border-emerald-200">
				<?php else : ?>
					<div class="<?php echo esc_attr( $es_ccls ); ?>">
				<?php endif; ?>
					<div class="flex w-full items-center justify-between">
						<?php if ( $es_logo ) : ?>
							<img src="<?php echo esc_url( $es_logo ); ?>" alt="<?php echo esc_attr( $es_name ); ?>" loading="lazy" class="h-9 w-auto max-w-[120px] object-contain"/>
						<?php else : ?>
							<span class="grid h-9 w-9 place-items-center rounded-xl border border-emerald-100 bg-emerald-50/70 text-emerald-700"><?php echo $es_landmark; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted inline SVG. ?></span>
						<?php endif; ?>
						<span class="font-mono text-[11px] font-bold text-slate-400 tabular-nums"><?php echo esc_html( $es_num ); ?></span>
					</div>
					<div class="mt-3">
						<div class="text-xs sm:text-[13px] font-extrabold text-slate-900"><?php echo esc_html( $es_name ); ?></div>
						<?php if ( '' !== $es_desc ) : ?>
							<div class="mt-0.5 line-clamp-1 text-[10.5px] font-medium text-slate-500"><?php echo esc_html( $es_desc ); ?></div>
						<?php endif; ?>
					</div>
				<?php if ( '' !== $es_url ) : ?>
					</a>
				<?php else : ?>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<p class="mt-8 text-center text-sm font-medium text-slate-500">مشتری‌ای برای نمایش وجود ندارد.</p>
	<?php endif; ?>
</section>
'''
tpl = tpl.replace('__PHP_ITEMS__', php_items).replace('__LANDMARK__', landmark.replace("\\", "\\\\").replace("'", "\\'"))
open('erfansanat/template-parts/sections/home/section-clients.php', 'w', encoding='utf-8').write(tpl)
print("written", len(tpl), "bytes; leftover:", re.findall(r'__[A-Z0-9_]+__', tpl))
