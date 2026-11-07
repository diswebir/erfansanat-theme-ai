<?php
/** Product content, specifications, project links, reviews, and extension tabs. */

defined( 'ABSPATH' ) || exit;
$product = \ErfanSanat\Theme\Woo\ProductTemplate::product( $args['product'] ?? null );
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $product );
if ( ! $product || ! $product_id ) { return; }
$raw_projects = \ErfanSanat\Theme\Woo\ProductTemplate::meta( $product_id, 'project-ids', array() );
$has_projects = false;
if ( is_array( $raw_projects ) ) {
	foreach ( $raw_projects as $project_value ) {
		if ( is_array( $project_value ) ) { $project_value = $project_value['id'] ?? $project_value['value'] ?? ''; }
		if ( absint( $project_value ) > 0 ) { $has_projects = true; break; }
	}
}
$reviews_enabled = ! function_exists( 'get_option' ) || 'yes' === get_option( 'woocommerce_enable_reviews', 'yes' );
$reviews_open = function_exists( 'comments_open' ) ? comments_open( $product_id ) : $reviews_enabled;
$show_reviews = $reviews_enabled && $reviews_open;
$tabs = function_exists( 'apply_filters' ) ? apply_filters( 'woocommerce_product_tabs', array() ) : array();
if ( ! is_array( $tabs ) ) { $tabs = array(); }
$standard_keys = array( 'description', 'additional_information', 'reviews' );
$extra_tabs = array();
foreach ( $tabs as $key => $tab ) {
	if ( ! in_array( $key, $standard_keys, true ) && is_array( $tab ) && is_callable( $tab['callback'] ?? null ) ) { $extra_tabs[ $key ] = $tab; }
}
?>
<section class="mx-auto max-w-6xl px-5 pb-12 lg:px-8" aria-label="اطلاعات کامل محصول">
	<div data-prod-tabs-container="true" class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
		<div class="flex gap-1 overflow-x-auto border-b border-slate-100 bg-slate-50/70 p-2" role="tablist" aria-label="بخش‌های اطلاعات محصول">
			<button type="button" data-prod-tab-btn="desc" aria-selected="true" class="shrink-0 rounded-xl bg-emerald-800 px-4 py-2.5 text-xs font-extrabold text-white shadow-sm"><span data-prod-tab-icon aria-hidden="true">▤</span> توضیحات</button>
			<button type="button" data-prod-tab-btn="specs" aria-selected="false" class="shrink-0 rounded-xl px-4 py-2.5 text-xs font-extrabold text-slate-600"><span data-prod-tab-icon aria-hidden="true">⚙</span> مشخصات</button>
			<?php if ( $has_projects ) : ?><button type="button" data-prod-tab-btn="projects" aria-selected="false" class="shrink-0 rounded-xl px-4 py-2.5 text-xs font-extrabold text-slate-600"><span data-prod-tab-icon aria-hidden="true">▦</span> پروژه‌ها</button><?php endif; ?>
			<?php if ( $show_reviews ) : ?><button type="button" data-prod-tab-btn="reviews" aria-selected="false" class="shrink-0 rounded-xl px-4 py-2.5 text-xs font-extrabold text-slate-600"><span data-prod-tab-icon aria-hidden="true">☆</span> دیدگاه‌ها</button><?php endif; ?>
			<?php foreach ( $extra_tabs as $key => $tab ) : ?><button type="button" data-prod-tab-btn="extension-<?php echo esc_attr( (string) $key ); ?>" aria-selected="false" class="shrink-0 rounded-xl px-4 py-2.5 text-xs font-extrabold text-slate-600"><?php echo wp_kses_post( $tab['title'] ?? '' ); ?></button><?php endforeach; ?>
		</div>
		<div class="p-5 sm:p-8">
			<div id="product-details" data-prod-tab-panel="desc" class="block" role="tabpanel"><div class="max-w-full text-sm leading-8 text-slate-700"><?php $description = function_exists( 'get_post_field' ) ? (string) get_post_field( 'post_content', $product_id ) : ''; if ( '' !== trim( $description ) ) { echo apply_filters( 'the_content', $description ); } ?></div><?php get_template_part( 'template-parts/product/features', null, array( 'product' => $product ) ); ?><?php get_template_part( 'template-parts/product/usage', null, array( 'product' => $product ) ); ?><?php get_template_part( 'template-parts/product/catalog', null, array( 'product' => $product ) ); ?></div>
			<div data-prod-tab-panel="specs" class="hidden" hidden><?php get_template_part( 'template-parts/product/specs', null, array( 'product' => $product ) ); ?><?php get_template_part( 'template-parts/product/legal-ids', null, array( 'product' => $product ) ); ?></div>
			<?php if ( $has_projects ) : ?><div data-prod-tab-panel="projects" class="hidden" hidden><?php get_template_part( 'template-parts/product/related-projects', null, array( 'product' => $product ) ); ?></div><?php endif; ?>
			<?php if ( $show_reviews ) : ?><div data-prod-tab-panel="reviews" class="hidden" hidden><?php get_template_part( 'template-parts/product/reviews', null, array( 'product' => $product ) ); ?></div><?php endif; ?>
			<?php foreach ( $extra_tabs as $key => $tab ) : ?><div data-prod-tab-panel="extension-<?php echo esc_attr( (string) $key ); ?>" class="hidden" hidden><?php call_user_func( $tab['callback'], $key, $tab ); ?></div><?php endforeach; ?>
		</div>
	</div>
</section>
