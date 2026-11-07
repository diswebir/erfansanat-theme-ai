<?php
/** Fixed-order WooCommerce product attribute table. */

defined( 'ABSPATH' ) || exit;
$product = \ErfanSanat\Theme\Woo\ProductTemplate::product( $args['product'] ?? null );
$attributes = \ErfanSanat\Theme\Woo\AttributeData::rows( $product );
if ( ! $attributes ) { return; }
?>
<section class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-7" aria-labelledby="es-product-specs-title">
	<h2 id="es-product-specs-title" class="mb-5 text-lg font-extrabold text-slate-900">مشخصات فنی محصول</h2>
	<dl class="divide-y divide-slate-100">
		<?php foreach ( $attributes as $attribute ) : ?>
			<div class="grid gap-2 py-3 first:pt-0 last:pb-0 sm:grid-cols-2 sm:gap-5"><dt class="text-xs font-bold text-slate-500"><?php echo esc_html( $attribute['label'] ); ?></dt><dd class="flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-800"><?php if ( '' !== $attribute['swatch'] ) : ?><span aria-hidden="true" class="h-4 w-4 rounded-full border border-slate-300" style="background-color:<?php echo esc_attr( $attribute['swatch'] ); ?>"></span><?php endif; ?><span><?php echo esc_html( $attribute['value'] ); ?></span></dd></div>
		<?php endforeach; ?>
	</dl>
</section>
