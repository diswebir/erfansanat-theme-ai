<?php
/**
 * Archive pagination (P06-T021). Client-side pagination shell driven by the
 * immutable main.js (data-pagination-root / data-page-btn / prev / next /
 * summary). Page numbers are generated from the real query total and per-page
 * size (AC-02); main.js shows/hides and re-pages them as filters change.
 *
 * @package ErfanSanat
 * @var array $args { @type int $total @type int $per_page }
 */

defined( 'ABSPATH' ) || exit;

$es_total    = isset( $args['total'] ) ? (int) $args['total'] : 0;
$es_per_page = isset( $args['per_page'] ) ? max( 1, (int) $args['per_page'] ) : 9;

$es_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};

$es_total_pages = max( 1, (int) ceil( $es_total / $es_per_page ) );
?>
<nav aria-label="<?php esc_attr_e( 'صفحه‌بندی محصولات', 'erfansanat' ); ?>" data-pagination-root="true" data-per-page="<?php echo (int) $es_per_page; ?>" class="<?php echo $es_total_pages <= 1 ? 'hidden' : 'flex'; ?> mt-10 flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/80 bg-white px-4 py-3.5 shadow-2xs sm:px-5">
	<div class="text-[12.5px] font-bold text-slate-600" data-pagination-summary="true"><?php echo esc_html( sprintf( 'صفحه %s از %s (%s مورد)', $es_fa( 1 ), $es_fa( $es_total_pages ), $es_fa( $es_total ) ) ); ?></div>
	<div class="flex items-center gap-1.5" data-pagination-buttons="true">
		<button type="button" data-page-prev="true" disabled class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-extrabold text-slate-600 transition hover:bg-emerald-50 disabled:opacity-40">
			<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
			<span><?php esc_html_e( 'قبلی', 'erfansanat' ); ?></span>
		</button>
		<?php for ( $es_p = 1; $es_p <= $es_total_pages; $es_p++ ) : ?>
			<button type="button" data-page-btn="<?php echo (int) $es_p; ?>" class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all cursor-pointer <?php echo 1 === $es_p ? 'bg-emerald-700 text-white shadow-md shadow-emerald-700/25' : 'border border-slate-200 bg-white text-slate-700 hover:border-emerald-400 hover:text-emerald-700'; ?>" <?php echo 1 === $es_p ? 'aria-current="page"' : ''; ?>><?php echo esc_html( $es_fa( $es_p ) ); ?></button>
		<?php endfor; ?>
		<button type="button" data-page-next="true" class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-extrabold text-slate-600 transition hover:bg-emerald-50">
			<span><?php esc_html_e( 'بعدی', 'erfansanat' ); ?></span>
			<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>
		</button>
	</div>
</nav>
