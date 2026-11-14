# -*- coding: utf-8 -*-
import json
wifi=json.load(open('temp/phase-05-homepage-and-sections/ref-products-svg.json',encoding='utf-8'))['wifi']
tpl=r'''<?php
/**
 * Featured products section (P05-T013). DOM parity with static-mirror
 * (id=products). Products are rendered with the standard product card
 * (P05-T003) via WP_Query: only published, in-stock products (AC-03),
 * count/category from settings (AC-02 / SC-01). Each card keeps the full
 * data-product-* attribute set (SC-02).
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'products' );
$es_type = (string) ( $es_section['type'] ?? 'products' );

$es_heading    = (string) ( $es_fields['heading'] ?? 'منتخب محصولات' );
$es_subheading = (string) ( $es_fields['subheading'] ?? 'تولید صنعتی تجهیزات نورپردازی شهری با عایق‌بندی تزریق مستقیم، بدنه پلی‌کربنات نشکن و کنترلرهای مرکزی بی‌سیم.' );
$es_count      = (int) ( $es_fields['count'] ?? 8 );
$es_count      = max( 1, min( 24, $es_count ) );
$es_eyebrow    = (string) apply_filters( 'es_products_section_eyebrow', 'تولیدات تخصصی کارخانه' );
$es_category   = (string) apply_filters( 'es_products_section_category', '' );

$es_query_args = array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => $es_count,
	'no_found_rows'  => true,
	'orderby'        => 'date',
	'order'          => 'DESC',
	// AC-03: hide out-of-stock products (missing meta defaults to in-stock).
	'meta_query'     => array(
		'relation' => 'OR',
		array( 'key' => '_stock_status', 'compare' => 'NOT EXISTS' ),
		array( 'key' => '_stock_status', 'value' => 'outofstock', 'compare' => '!=' ),
	),
);
if ( '' !== $es_category ) {
	$es_query_args['tax_query'] = array(
		array( 'taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => $es_category ),
	);
}
$es_query = new WP_Query( $es_query_args );
?>
<section id="products" class="es-section es-section--products relative z-10 py-12 sm:py-16 lg:py-20" style="scroll-margin-top:5rem" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="mx-auto max-w-[1300px] px-4 sm:px-6 lg:px-8">
		<div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
			<div class="flex max-w-2xl flex-col gap-3 items-start text-right">
				<div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]">
					<span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( $es_eyebrow ); ?></span>
				</div>
				<h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]"><?php echo esc_html( $es_heading ); ?></h2>
				<?php if ( '' !== trim( $es_subheading ) ) : ?>
					<p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8"><?php echo esc_html( $es_subheading ); ?></p>
				<?php endif; ?>
			</div>
			<div class="shrink-0">
				<div class="inline-flex items-center gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-3 shadow-2xs">
					<span class="grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-emerald-700">__WIFI__</span>
					<div class="leading-tight">
						<div class="text-xs font-extrabold text-slate-900">کنترل هوشمند یکپارچه</div>
						<div class="mt-0.5 text-[11px] font-medium text-slate-500">پشتیبانی از پروتکل بی‌سیم NRF و WiFi</div>
					</div>
				</div>
			</div>
		</div>
		<div class="mt-8 sm:mt-10 grid gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4">
			<?php if ( $es_query->have_posts() ) : ?>
				<?php
				while ( $es_query->have_posts() ) :
					$es_query->the_post();
					get_template_part( 'template-parts/cards/card-product', null, array( 'product_id' => get_the_ID() ) );
				endwhile;
				wp_reset_postdata();
				?>
			<?php else : ?>
				<p class="col-span-full rounded-2xl border border-dashed border-slate-200 bg-white p-6 text-center text-sm font-semibold text-slate-500">محصولی برای نمایش وجود ندارد.</p>
			<?php endif; ?>
		</div>
	</div>
</section>
'''
tpl=tpl.replace('__WIFI__',wifi)
open('erfansanat/template-parts/sections/home/section-products.php','w',encoding='utf-8').write(tpl)
print("written",len(tpl),"bytes; leftover:",__import__('re').findall(r'__[A-Z0-9_]+__',tpl))
