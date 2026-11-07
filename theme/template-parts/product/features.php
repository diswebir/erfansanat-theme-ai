<?php
/** Product highlights and extended key/value features from legacy repeaters. */

defined( 'ABSPATH' ) || exit;
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $args['product'] ?? null );
if ( ! $product_id ) { return; }
$highlights = \ErfanSanat\Theme\Woo\ProductTemplate::rows( $product_id, 'product-feature' );
$specifications = \ErfanSanat\Theme\Woo\ProductTemplate::rows( $product_id, 'product-feature2_copy' );
$items = array();
foreach ( $highlights as $row ) {
	$value = $row['add-new-feature'] ?? '';
	if ( is_scalar( $value ) && '' !== trim( (string) $value ) ) { $items[] = array( 'label' => '', 'value' => (string) $value ); }
}
foreach ( $specifications as $row ) {
	$label = $row['add-new-feature'] ?? '';
	$value = $row['vaule_of_variable'] ?? '';
	if ( is_scalar( $label ) && is_scalar( $value ) && ( '' !== trim( (string) $label ) || '' !== trim( (string) $value ) ) ) { $items[] = array( 'label' => (string) $label, 'value' => (string) $value ); }
}
if ( ! $items ) { return; }
?>
<section class="mb-7" aria-label="ویژگی‌های محصول"><h3 class="mb-4 text-base font-extrabold text-slate-900">ویژگی‌ها و مشخصات تکمیلی</h3><div class="grid gap-3 sm:grid-cols-2"><?php foreach ( $items as $item ) : ?><div class="rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4"><div class="flex items-start gap-3"><span aria-hidden="true" class="mt-1 grid h-7 w-7 shrink-0 place-items-center rounded-lg bg-emerald-100 text-xs font-black text-emerald-800">✓</span><div><?php if ( '' !== $item['label'] ) : ?><strong class="block text-xs font-extrabold text-slate-800"><?php echo esc_html( $item['label'] ); ?></strong><?php endif; ?><div class="mt-1 text-sm leading-7 text-slate-600"><?php echo wp_kses_post( $item['value'] ); ?></div></div></div></div><?php endforeach; ?></div></section>
