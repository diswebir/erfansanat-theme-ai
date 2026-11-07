<?php
/** Product use cases and linked application taxonomies. */

defined( 'ABSPATH' ) || exit;
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $args['product'] ?? null );
if ( ! $product_id ) { return; }
$rows = \ErfanSanat\Theme\Woo\ProductTemplate::rows( $product_id, 'usage_product' );
$uses = array();
foreach ( $rows as $row ) {
	$value = $row['add-new-usage'] ?? '';
	if ( is_scalar( $value ) && '' !== trim( (string) $value ) ) { $uses[] = (string) $value; }
}
$term_values = \ErfanSanat\Theme\Woo\ProductTemplate::meta( $product_id, 'usage_countr', array() );
if ( ! is_array( $term_values ) ) { $term_values = array(); }
$term_ids = array();
foreach ( $term_values as $value ) {
	if ( is_array( $value ) ) { $value = $value['id'] ?? $value['value'] ?? ''; }
	$id = absint( $value );
	if ( $id && ! in_array( $id, $term_ids, true ) ) { $term_ids[] = $id; }
}
$terms = array();
foreach ( $term_ids as $term_id ) {
	$term = function_exists( 'get_term' ) ? get_term( $term_id, 'project_type' ) : null;
	if ( $term && ! is_wp_error( $term ) && isset( $term->name ) ) {
		$link = function_exists( 'get_term_link' ) ? get_term_link( $term ) : '';
		$terms[] = array( 'name' => (string) $term->name, 'url' => is_string( $link ) ? esc_url( $link ) : '' );
	}
}
if ( ! $uses && ! $terms ) { return; }
?>
<section class="mb-7" aria-label="موارد کاربرد محصول"><h3 class="mb-4 text-base font-extrabold text-slate-900">موارد کاربرد</h3><?php if ( $uses ) : ?><ul class="mb-4 grid gap-2 sm:grid-cols-2"><?php foreach ( $uses as $use ) : ?><li class="flex items-start gap-2 text-sm leading-7 text-slate-700"><span aria-hidden="true" class="mt-2 h-1.5 w-1.5 shrink-0 rounded-full bg-emerald-600"></span><span><?php echo wp_kses_post( $use ); ?></span></li><?php endforeach; ?></ul><?php endif; ?><?php if ( $terms ) : ?><div class="flex flex-wrap gap-2"><?php foreach ( $terms as $term ) : ?><?php if ( '' !== $term['url'] ) : ?><a href="<?php echo esc_url( $term['url'] ); ?>" class="rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800"><?php echo esc_html( $term['name'] ); ?></a><?php else : ?><span class="rounded-full border border-emerald-100 bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-800"><?php echo esc_html( $term['name'] ); ?></span><?php endif; ?><?php endforeach; ?></div><?php endif; ?></section>
