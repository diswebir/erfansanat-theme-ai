<?php
/**
 * Product applications (P06-T011). Renders the `usage_product` repeater as
 * chips and the `usage_countr` project_type terms as archive links, inside the
 * description tab (`es_product_usage`). Empty data removes the whole section
 * (AC-03); term links use the real Persian permalink (AC-02).
 *
 * @package ErfanSanat
 * @var WC_Product|ES_Shim_Product $product
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\LegacyMeta;

$es_product = isset( $product ) && is_object( $product ) ? $product : null;
if ( ! $es_product && isset( $args['product'] ) && is_object( $args['product'] ) ) {
	$es_product = $args['product'];
}
if ( ! $es_product || ! method_exists( $es_product, 'get_id' ) ) {
	return;
}
$es_id = (int) $es_product->get_id();

$es_usages = array();
foreach ( LegacyMeta::get( $es_id, 'usage_product' ) as $es_item ) {
	$es_text = is_array( $es_item ) ? trim( (string) ( $es_item['add-new-usage'] ?? '' ) ) : trim( (string) $es_item );
	if ( '' !== $es_text ) {
		$es_usages[] = $es_text;
	}
}

$es_links = array();
foreach ( LegacyMeta::get( $es_id, 'usage_countr' ) as $es_term_id ) {
	$es_term = get_term( (int) $es_term_id, 'project_type' );
	if ( ! $es_term || is_wp_error( $es_term ) ) {
		continue;
	}
	$es_link = get_term_link( $es_term );
	if ( is_wp_error( $es_link ) ) {
		continue;
	}
	$es_links[] = array( 'name' => $es_term->name, 'url' => $es_link );
}

if ( ! $es_usages && ! $es_links ) {
	return; // AC-03.
}
?>
<div class="grid gap-6 lg:grid-cols-2" data-product-usage>
	<?php if ( $es_usages ) : ?>
		<div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-6">
			<h3 class="mb-4 flex items-center gap-2 text-base font-extrabold text-slate-900">
				<svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2v20"/><path d="M2 12h20"/><path d="m4.93 4.93 14.14 14.14"/><path d="m19.07 4.93-14.14 14.14"/></svg>
				<span><?php esc_html_e( 'کاربردهای اصلی در پروژه‌ها', 'erfansanat' ); ?></span>
			</h3>
			<div class="flex flex-wrap gap-2">
				<?php foreach ( $es_usages as $es_text ) : ?>
					<span class="rounded-xl border border-emerald-200/80 bg-white px-3.5 py-2 text-xs font-bold text-emerald-950 shadow-2xs"><?php echo esc_html( $es_text ); ?></span>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $es_links ) : ?>
		<div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-6">
			<div class="mb-3.5 flex items-center gap-2 text-[13.5px] font-black text-slate-900">
				<svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
				<span><?php esc_html_e( 'آرشیو پروژه‌های مرتبط با این محصول:', 'erfansanat' ); ?></span>
			</div>
			<div class="flex flex-wrap gap-2">
				<?php foreach ( $es_links as $es_link ) : ?>
					<a href="<?php echo esc_url( $es_link['url'] ); ?>" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-3.5 py-2 text-xs font-extrabold text-emerald-800 transition hover:bg-emerald-50">
						<span><?php echo esc_html( sprintf( 'پروژه‌های %s', $es_link['name'] ) ); ?></span>
						<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>
</div>
