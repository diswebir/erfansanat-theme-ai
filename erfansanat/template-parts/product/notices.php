<?php
/**
 * Before-sale notice + catalog download (P06-T008). Renders into the purchase
 * card via `es_product_summary_after`. Both blocks are optional: an empty
 * notice or catalog adds NO DOM (AC-03). The catalog URL is escaped and offered
 * as a download; external links are hardened (AC-02).
 *
 * Legacy keys: `before-sale-c` (text), `product-catalog-file` (Media URL or ID).
 *
 * @package ErfanSanat
 * @var WC_Product|ES_Shim_Product $product
 */

defined( 'ABSPATH' ) || exit;

$es_product = isset( $product ) ? $product : ( isset( $args['product'] ) ? $args['product'] : null );
if ( ! is_object( $es_product ) || ! method_exists( $es_product, 'get_id' ) ) {
	global $product;
	$es_product = isset( $product ) && is_object( $product ) ? $product : null;
}
if ( ! is_object( $es_product ) || ! method_exists( $es_product, 'get_id' ) ) {
	return;
}
$es_id = (int) $es_product->get_id();

$es_notice  = trim( (string) get_post_meta( $es_id, 'before-sale-c', true ) );
$es_catalog = trim( (string) get_post_meta( $es_id, 'product-catalog-file', true ) );

// Catalog may be stored as an attachment ID; resolve to a URL.
if ( '' !== $es_catalog && ctype_digit( $es_catalog ) ) {
	$es_catalog = (string) wp_get_attachment_url( (int) $es_catalog );
}
$es_catalog = esc_url( $es_catalog );

if ( '' === $es_notice && '' === $es_catalog ) {
	return; // AC-03: nothing to render.
}

$es_is_external = '' !== $es_catalog && ! preg_match( '#^' . preg_quote( home_url(), '#' ) . '#', $es_catalog );
?>
<div class="mt-4 space-y-3" data-product-notices>
	<?php if ( '' !== $es_notice ) : ?>
		<div class="flex items-start gap-2.5 rounded-2xl border border-amber-200/80 bg-amber-50/60 p-4 text-[12.5px] font-semibold leading-6 text-amber-900" data-before-sale-notice>
			<svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
			<p class="whitespace-pre-line"><?php echo esc_html( $es_notice ); ?></p>
		</div>
	<?php endif; ?>

	<?php if ( '' !== $es_catalog ) : ?>
		<a href="<?php echo $es_catalog; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>"
			class="flex items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50/50 px-4 py-2.5 text-xs font-extrabold text-emerald-800 transition hover:bg-emerald-100"
			data-product-catalog
			download
			<?php if ( $es_is_external ) : ?>target="_blank" rel="noopener noreferrer"<?php endif; ?>>
			<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
			<span><?php esc_html_e( 'دانلود کاتالوگ محصول', 'erfansanat' ); ?></span>
		</a>
	<?php endif; ?>
</div>
