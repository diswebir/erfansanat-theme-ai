<?php
/**
 * Product title block (P06-T004). Renders the hero title region: category
 * badge, product name (h1), the legacy subtitle `zireonvan`, the legacy
 * English title `english_titl` (spelling preserved — AC-01), and the excerpt.
 * Empty subtitle / English title add no DOM (AC-02). All values escaped
 * (AC-03). Called by the content-single-product shell in place of its inline
 * title block (registered dependency).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : false;
if ( ! $es_product ) {
	return;
}

$es_id      = $es_product->get_id();
$es_title   = get_the_title( $es_id );
$es_excerpt = get_the_excerpt( $es_id );

// Legacy meta keys — `english_titl` spelling is sacred (AC-01).
$es_zireonvan = (string) get_post_meta( $es_id, 'zireonvan', true );
$es_english   = (string) get_post_meta( $es_id, 'english_titl', true );

$es_cats    = get_the_terms( $es_id, 'product_cat' );
$es_primary = ( $es_cats && ! is_wp_error( $es_cats ) ) ? $es_cats[0] : null;
?>
<div class="pt-6">
	<?php if ( $es_primary ) : ?>
		<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs">
			<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
			<span><?php echo esc_html( $es_primary->name ); ?></span>
		</div>
	<?php endif; ?>
	<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $es_title ); ?></h1>
	<?php if ( '' !== trim( $es_zireonvan ) ) : ?>
		<p class="mt-2 text-[15px] font-bold text-emerald-700 lg:text-[16px]"><?php echo esc_html( $es_zireonvan ); ?></p>
	<?php endif; ?>
	<?php if ( '' !== trim( $es_english ) ) : ?>
		<p class="mt-1 text-[13px] font-semibold tracking-wide text-slate-400" dir="ltr"><?php echo esc_html( $es_english ); ?></p>
	<?php endif; ?>
	<?php if ( '' !== trim( $es_excerpt ) ) : ?>
		<p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]"><?php echo esc_html( $es_excerpt ); ?></p>
	<?php endif; ?>
</div>
