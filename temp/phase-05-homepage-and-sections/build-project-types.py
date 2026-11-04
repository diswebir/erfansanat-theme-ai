# -*- coding: utf-8 -*-
import json
arrow=json.load(open('temp/phase-05-homepage-and-sections/ref-project-types-svg.json',encoding='utf-8'))['arrow']
tpl=r'''<?php
/**
 * Project types section (P05-T008). DOM parity with static-mirror/index.html
 * (id=projects). Cards are built from real project_type terms (AC-02): the
 * link goes to the type archive (SC-01/SC-02), the image resolves from the
 * term thumbnail or the first project in the type, and the counter reflects
 * the real project count. Count/order come from settings (AC-03).
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'project-types' );
$es_type = (string) ( $es_section['type'] ?? 'project-types' );

$es_heading    = (string) ( $es_fields['heading'] ?? 'انواع پروژه‌های نورپردازی' );
$es_subheading = (string) ( $es_fields['subheading'] ?? '' );
$es_per_page   = (int) ( $es_fields['per_page'] ?? 8 );
$es_per_page   = max( 1, min( 24, $es_per_page ) );
$es_orderby    = (string) apply_filters( 'es_project_types_orderby', 'count' );
$es_order      = (string) apply_filters( 'es_project_types_order', 'DESC' );
$es_eyebrow    = (string) apply_filters( 'es_project_types_eyebrow', 'نمونه‌کارهای شاخص' );

$es_terms = get_terms(
	array(
		'taxonomy'   => 'project_type',
		'number'     => $es_per_page,
		'hide_empty' => false,
		'orderby'    => $es_orderby,
		'order'      => $es_order,
	)
);
if ( is_wp_error( $es_terms ) ) {
	$es_terms = array();
}

$es_fa_digits = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );

/**
 * Resolve a real image for a project type: term thumbnail meta, else the
 * first published project's featured image in the type, else a theme asset.
 */
$es_type_image = static function ( $term ) {
	$thumb_id = (int) get_term_meta( $term->term_id, 'thumbnail', true );
	if ( $thumb_id > 0 ) {
		$url = wp_get_attachment_image_url( $thumb_id, 'large' );
		if ( $url ) {
			return (string) $url;
		}
	}
	$projects = get_posts(
		array(
			'post_type'      => 'projects',
			'post_status'    => 'publish',
			'numberposts'    => 1,
			'fields'         => 'ids',
			'tax_query'      => array(
				array( 'taxonomy' => 'project_type', 'field' => 'term_id', 'terms' => $term->term_id ),
			),
		)
	);
	if ( ! empty( $projects ) ) {
		$url = get_the_post_thumbnail_url( (int) $projects[0], 'large' );
		if ( $url ) {
			return (string) $url;
		}
	}
	return ES_ASSETS_URI . 'images/light-tunnel.jpg';
};

/**
 * Render one project-type card. $large toggles the featured (col-span-3)
 * sizing versus the stacked (col-span-2) sizing, mirroring the reference.
 */
$es_render_card = static function ( $term, $index, $large ) use ( $es_type_image, $es_fa_digits ) {
	$link  = get_term_link( $term );
	$link  = is_wp_error( $link ) ? '#' : (string) $link;
	$image = $es_type_image( $term );
	$num   = strtr( str_pad( (string) $index, 2, '0', STR_PAD_LEFT ), $es_fa_digits );
	$count = (int) $term->count;
	$eyebrow = (string) apply_filters( 'es_project_type_card_eyebrow', strtr( (string) $count, $es_fa_digits ) . ' پروژه اجراشده', $term );
	$min_h = $large ? 'min-h-[340px] sm:min-h-[420px] lg:min-h-[540px]' : 'min-h-[235px] sm:min-h-[258px]';
	$h3    = $large ? 'text-xl sm:text-2xl lg:text-3xl' : 'text-lg sm:text-xl';
	$pcls  = $large ? 'max-w-lg text-xs sm:text-[13px] font-medium leading-6 text-slate-200/90' : ' text-xs sm:text-[13px] font-medium leading-6 text-slate-200/90';
	?>
	<a href="<?php echo esc_url( $link ); ?>" class="card-glow group relative block h-full w-full overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 <?php echo esc_attr( $min_h ); ?>">
		<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $term->name ); ?>" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105"/>
		<div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/35 to-slate-950/15"></div>
		<div class="absolute inset-x-0 top-0 flex items-center justify-between p-5 sm:p-6">
			<span class="grid h-10 w-10 place-items-center rounded-2xl border border-white/25 bg-slate-950/60 text-white backdrop-blur-md transition-colors duration-300 group-hover:border-emerald-400 group-hover:bg-emerald-600">__ARROW__</span>
			<span class="text-sm font-black tracking-wider text-white/75 tabular-nums"><?php echo esc_html( $num ); ?></span>
		</div>
		<div class="absolute inset-x-0 bottom-0 flex flex-col gap-1.5 p-5 sm:p-7">
			<span class="text-xs font-bold text-emerald-300"><?php echo esc_html( $eyebrow ); ?></span>
			<h3 class="font-black tracking-tight text-white <?php echo esc_attr( $h3 ); ?>"><?php echo esc_html( $term->name ); ?></h3>
			<?php if ( '' !== trim( (string) $term->description ) ) : ?>
				<p class="<?php echo esc_attr( $pcls ); ?>"><?php echo esc_html( wp_strip_all_tags( (string) $term->description ) ); ?></p>
			<?php endif; ?>
		</div>
	</a>
	<?php
};

$es_first = ! empty( $es_terms ) ? array_shift( $es_terms ) : null;
$es_rest  = $es_terms;
?>
<section id="projects" class="es-section es-section--project-types relative z-10 mx-auto max-w-[1300px] scroll-mt-24 px-4 pt-12 pb-8 sm:px-6 sm:pt-16 sm:pb-10 lg:px-8" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="flex max-w-2xl flex-col gap-3 items-center text-center mx-auto">
		<div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]">
			<span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
			<span><?php echo esc_html( $es_eyebrow ); ?></span>
		</div>
		<h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]"><?php echo esc_html( $es_heading ); ?></h2>
		<?php if ( '' !== trim( $es_subheading ) ) : ?>
			<p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8"><?php echo esc_html( $es_subheading ); ?></p>
		<?php endif; ?>
	</div>
	<?php if ( $es_first || ! empty( $es_rest ) ) : ?>
		<div class="mt-8 sm:mt-10 grid gap-4 sm:gap-5 lg:grid-cols-5 lg:items-stretch">
			<?php if ( $es_first ) : ?>
				<div class="flex h-full flex-col lg:col-span-3">
					<?php $es_render_card( $es_first, 1, true ); ?>
				</div>
			<?php endif; ?>
			<?php if ( ! empty( $es_rest ) ) : ?>
				<div class="flex h-full flex-col gap-4 sm:gap-5 lg:col-span-2">
					<?php foreach ( $es_rest as $es_i => $es_term ) : ?>
						<div class="flex flex-1 flex-col">
							<?php $es_render_card( $es_term, $es_i + 2, false ); ?>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</section>
'''
tpl=tpl.replace('__ARROW__',arrow)
open('erfansanat/template-parts/sections/home/section-project-types.php','w',encoding='utf-8').write(tpl)
print("written",len(tpl),"bytes; leftover:",__import__('re').findall(r'__[A-Z0-9_]+__',tpl))
