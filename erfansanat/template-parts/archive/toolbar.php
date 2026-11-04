<?php
/**
 * Archive toolbar (P06-T021). Result count (real query total, AC-03), the sort
 * select and the mobile filter-drawer trigger. Sorting is driven client-side by
 * the immutable main.js via data-product-sort (AC-01). Extracted from the shop
 * shell so every product archive shares one toolbar.
 *
 * @package ErfanSanat
 * @var array $args { @type int $total @type string $toolbar_title }
 */

defined( 'ABSPATH' ) || exit;

$es_total         = isset( $args['total'] ) ? (int) $args['total'] : 0;
$es_toolbar_title = isset( $args['toolbar_title'] ) ? $args['toolbar_title'] : __( 'همه محصولات تولیدی عرفان صنعت', 'erfansanat' );

$es_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};
?>
<div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3.5 shadow-2xs sm:px-5" data-archive-toolbar>
	<div class="flex flex-wrap items-center gap-2">
		<span class="text-[13.5px] font-black text-slate-900"><?php echo esc_html( $es_toolbar_title ); ?></span>
		<span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-extrabold text-emerald-800"><span data-product-count="true"><?php echo esc_html( $es_fa( $es_total ) ); ?></span> <?php esc_html_e( 'محصول', 'erfansanat' ); ?></span>
	</div>
	<div class="flex flex-wrap items-center gap-2">
		<div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5">
			<svg class="h-4 w-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m3 16 4 4 4-4"/><path d="M7 20V4"/><path d="m21 8-4-4-4 4"/><path d="M17 4v16"/></svg>
			<label for="product-sort-select" class="text-[11.5px] font-bold text-slate-500"><?php esc_html_e( 'مرتب‌سازی:', 'erfansanat' ); ?></label>
			<select id="product-sort-select" data-product-sort="true" class="bg-transparent text-[12px] font-extrabold text-slate-800 focus:outline-none cursor-pointer">
				<option value="default" selected><?php esc_html_e( 'پیش‌فرض کارخانه', 'erfansanat' ); ?></option>
				<option value="popular"><?php esc_html_e( 'پربازدیدترین', 'erfansanat' ); ?></option>
				<option value="rating"><?php esc_html_e( 'بالاترین امتیاز', 'erfansanat' ); ?></option>
				<option value="price-asc"><?php esc_html_e( 'ارزان‌ترین', 'erfansanat' ); ?></option>
				<option value="price-desc"><?php esc_html_e( 'گران‌ترین', 'erfansanat' ); ?></option>
			</select>
		</div>
		<button type="button" data-archive-drawer-open="true" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-3.5 py-2 text-[12px] font-extrabold text-white shadow-sm transition hover:bg-emerald-800 lg:hidden">
			<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/></svg>
			<span><?php esc_html_e( 'فیلتر و دسته‌بندی', 'erfansanat' ); ?></span>
		</button>
	</div>
</div>
