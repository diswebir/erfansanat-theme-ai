<?php
/**
 * AJAX category filter block (P08-T006). Extends the reference shop sidebar/
 * drawer with shareable category checkboxes + a clear-filters button, keeping
 * the reference checkbox vocabulary (accent-emerald-600 rounded-lg rows).
 * product-filters.js swaps the grid without refresh and mirrors state in the
 * URL. Included by product-archive-shell.php (sidebar + drawer).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

// Grid enhancer; depends on es-main so window.ES_AJAX exists first.
wp_enqueue_script(
	'es-product-filters',
	ES_ASSETS_URI . 'js/product-filters.js',
	array( 'es-main' ),
	(string) ( is_file( ES_ASSETS_DIR . 'js/product-filters.js' ) ? filemtime( ES_ASSETS_DIR . 'js/product-filters.js' ) : ES_VERSION ),
	true
);

$es_pf_cats = taxonomy_exists( 'product_cat' )
	? get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0 ) )
	: array();
$es_pf_cats = is_wp_error( $es_pf_cats ) ? array() : $es_pf_cats;

$es_pf_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};
?>
<div class="border-t border-slate-100 pt-4" data-product-filters-block="true">
	<div class="mb-2.5 flex items-center justify-between">
		<span class="flex items-center gap-2 text-[12px] font-extrabold text-slate-800">
			<svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
			<span><?php esc_html_e( 'فیلتر دسته‌بندی (بدون رفرش)', 'erfansanat' ); ?></span>
		</span>
	</div>
	<div class="space-y-1.5">
		<?php foreach ( $es_pf_cats as $es_pf_cat ) : ?>
			<label class="flex cursor-pointer items-center justify-between gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-[11.5px] font-bold text-slate-700 transition hover:border-emerald-300">
				<span class="flex items-center gap-2">
					<input type="checkbox" data-product-cat-filter="<?php echo esc_attr( (string) $es_pf_cat->term_id ); ?>" class="h-3.5 w-3.5 accent-emerald-600" />
					<span><?php echo esc_html( $es_pf_cat->name ); ?></span>
				</span>
				<span class="text-[10.5px] font-extrabold text-slate-400"><?php echo esc_html( $es_pf_fa( $es_pf_cat->count ) ); ?></span>
			</label>
		<?php endforeach; ?>
	</div>
	<button type="button" data-product-filters-clear="true" class="mt-3 flex w-full items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-[11.5px] font-extrabold text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
		<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
		<?php esc_html_e( 'حذف همه فیلترها', 'erfansanat' ); ?>
	</button>
</div>
