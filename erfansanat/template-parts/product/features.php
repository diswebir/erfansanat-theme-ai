<?php
/**
 * Product features (P06-T009). Renders the `product-feature` repeater as a
 * highlight list and the `product-feature2_copy` repeater as name/value pairs
 * inside the specs tab (`es_product_specs_panel`, priority 5). Legacy storage
 * (PHP-serialized / JetEngine JSON) is read through LegacyMeta; the misspelled
 * `vaule_of_variable` key is honoured (AC-02). Empty items add no row (AC-03).
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

$es_highlights = array();
foreach ( LegacyMeta::get( $es_id, 'product-feature' ) as $es_item ) {
	$es_text = is_array( $es_item ) ? trim( (string) ( $es_item['add-new-feature'] ?? '' ) ) : trim( (string) $es_item );
	if ( '' !== $es_text ) {
		$es_highlights[] = $es_text;
	}
}

$es_pairs = array();
foreach ( LegacyMeta::get( $es_id, 'product-feature2_copy' ) as $es_item ) {
	if ( ! is_array( $es_item ) ) {
		continue;
	}
	$es_label = trim( (string) ( $es_item['add-new-feature'] ?? '' ) );
	$es_value = trim( (string) ( $es_item['vaule_of_variable'] ?? '' ) );
	if ( '' !== $es_label && '' !== $es_value ) {
		$es_pairs[] = array( 'label' => $es_label, 'value' => $es_value );
	}
}

if ( ! $es_highlights && ! $es_pairs ) {
	return; // AC-03.
}
?>
<div class="space-y-6" data-product-features>
	<?php if ( $es_highlights ) : ?>
		<div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-5 sm:p-6">
			<div class="mb-4 flex items-center gap-2 text-sm font-extrabold text-slate-900">
				<svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>
				<span><?php esc_html_e( 'مزایا و ویژگی‌های برجسته محصول', 'erfansanat' ); ?></span>
			</div>
			<div class="grid gap-3 sm:grid-cols-2">
				<?php foreach ( $es_highlights as $es_text ) : ?>
					<div class="flex items-start gap-2.5 rounded-xl border border-slate-200/60 bg-white px-3.5 py-2.5 text-[13px] font-semibold leading-6 text-slate-700">
						<svg class="mt-0.5 h-4 w-4 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
						<span><?php echo esc_html( $es_text ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $es_pairs ) : ?>
		<div class="overflow-hidden rounded-2xl border border-slate-200" data-product-feature-pairs>
			<?php foreach ( $es_pairs as $es_i => $es_pair ) : ?>
				<div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 <?php echo 0 === $es_i % 2 ? 'bg-slate-50/70' : 'bg-white'; ?> border-b border-slate-100 last:border-b-0">
					<div class="text-xs font-bold text-slate-500"><?php echo esc_html( $es_pair['label'] ); ?></div>
					<div class="text-sm font-extrabold text-slate-900 sm:col-span-2"><?php echo esc_html( $es_pair['value'] ); ?></div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
