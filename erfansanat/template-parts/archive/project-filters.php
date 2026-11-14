<?php
/**
 * AJAX project filter block (P08-T010): project_type/city checkbox groups with
 * real per-term counters (facet-aware) + a clear-filters button. project-filters.js
 * swaps the grid without refresh and mirrors state in the URL. Included by
 * project-archive-shell.php (sidebar + drawer) — registered dependency.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

// Grid enhancer; depends on es-main so window.ES_AJAX exists first.
wp_enqueue_script(
	'es-project-filters',
	ES_ASSETS_URI . 'js/project-filters.js',
	array( 'es-main' ),
	(string) ( is_file( ES_ASSETS_DIR . 'js/project-filters.js' ) ? filemtime( ES_ASSETS_DIR . 'js/project-filters.js' ) : ES_VERSION ),
	true
);

$es_prf_groups = array(
	'project_type' => array( 'label' => __( 'نوع پروژه', 'erfansanat' ), 'attr' => 'data-project-type-filter' ),
	'city'         => array( 'label' => __( 'شهر', 'erfansanat' ), 'attr' => 'data-project-city-filter' ),
);
$es_prf_terms = array();
foreach ( array_keys( $es_prf_groups ) as $es_prf_tax ) {
	$es_prf_terms[ $es_prf_tax ] = taxonomy_exists( $es_prf_tax )
		? get_terms( array( 'taxonomy' => $es_prf_tax, 'hide_empty' => true ) )
		: array();
	$es_prf_terms[ $es_prf_tax ] = is_wp_error( $es_prf_terms[ $es_prf_tax ] ) ? array() : $es_prf_terms[ $es_prf_tax ];
}
$es_prf_counts = class_exists( '\ErfanSanat\Theme\Core\SmartFilters' )
	? \ErfanSanat\Theme\Core\SmartFilters::project_term_counts( array() )
	: array();
$es_prf_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};
?>
<div class="border-t border-slate-100 pt-4" data-project-filters-block="true">
	<?php foreach ( $es_prf_groups as $es_prf_tax => $es_prf_group ) : ?>
		<?php if ( ! $es_prf_terms[ $es_prf_tax ] ) { continue; } // AC-03: no values → no UI. ?>
		<div class="mb-4" data-project-filter-group="<?php echo esc_attr( $es_prf_tax ); ?>">
			<div class="mb-2.5 flex items-center justify-between">
				<span class="flex items-center gap-2 text-[12px] font-extrabold text-slate-800">
					<svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
					<span><?php echo esc_html( $es_prf_group['label'] ); ?></span>
				</span>
			</div>
			<div class="space-y-1.5">
				<?php foreach ( $es_prf_terms[ $es_prf_tax ] as $es_prf_term ) : ?>
					<label class="flex cursor-pointer items-center justify-between gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-[11.5px] font-bold text-slate-700 transition hover:border-emerald-300">
						<span class="flex items-center gap-2">
							<input type="checkbox" <?php echo esc_attr( $es_prf_group['attr'] ); ?>="<?php echo esc_attr( (string) $es_prf_term->term_id ); ?>" class="h-3.5 w-3.5 accent-emerald-600" />
							<span><?php echo esc_html( $es_prf_term->name ); ?></span>
						</span>
						<span <?php echo esc_attr( $es_prf_group['attr'] ); ?>-count="<?php echo esc_attr( (string) $es_prf_term->term_id ); ?>" class="text-[10.5px] font-extrabold text-slate-400" data-project-term-count="<?php echo esc_attr( $es_prf_tax . ':' . $es_prf_term->term_id ); ?>"><?php echo esc_html( $es_prf_fa( $es_prf_counts[ $es_prf_tax ][ (int) $es_prf_term->term_id ] ?? 0 ) ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>
	<button type="button" data-project-filters-clear="true" class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-[11.5px] font-extrabold text-slate-600 transition hover:border-red-200 hover:bg-red-50 hover:text-red-700">
		<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
		<?php esc_html_e( 'حذف همه فیلترها', 'erfansanat' ); ?>
	</button>
</div>
