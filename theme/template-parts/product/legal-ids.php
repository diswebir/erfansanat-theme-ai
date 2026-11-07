<?php
/** Conditional official product identifiers with accessible copy controls. */

defined( 'ABSPATH' ) || exit;
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $args['product'] ?? null );
if ( ! $product_id ) { return; }
$fields = array(
	'gs1_id' => 'شناسه GS1',
	'national_code' => 'کد ملی کالا',
	'hs_id' => 'شناسه HS',
	'isic_id' => 'شناسه ISIC',
	'cpc_id' => 'شناسه CPC',
	'product_id' => 'شناسه محصول',
);
$values = array();
foreach ( $fields as $key => $label ) {
	$value = \ErfanSanat\Theme\Woo\ProductTemplate::text( $product_id, $key );
	if ( '' !== $value ) { $values[] = array( 'label' => $label, 'value' => $value ); }
}
if ( ! $values ) { return; }
?>
<section class="mt-6 rounded-2xl border border-slate-200/80 bg-white p-5" aria-labelledby="es-product-ids-title"><h3 id="es-product-ids-title" class="mb-4 text-sm font-extrabold text-slate-900">شناسه‌های رسمی کالا</h3><dl class="divide-y divide-slate-100"><?php foreach ( $values as $index => $item ) : ?><div class="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0 last:pb-0"><dt class="text-xs font-bold text-slate-500"><?php echo esc_html( $item['label'] ); ?></dt><dd class="flex items-center gap-2"><code dir="ltr" class="rounded-lg bg-slate-50 px-2.5 py-1.5 text-xs font-bold text-slate-800"><?php echo esc_html( $item['value'] ); ?></code><button type="button" data-copy-button data-copy-value="<?php echo esc_attr( $item['value'] ); ?>" aria-label="کپی <?php echo esc_attr( $item['label'] ); ?>" class="rounded-lg border border-slate-200 px-2 py-1 text-[11px] font-bold text-emerald-800">کپی</button><span data-copy-status aria-live="polite" class="sr-only"></span></dd></div><?php endforeach; ?></dl></section>
