<?php
/**
 * Projects mega menu panel: type / city tabs with per-term project counters.
 *
 * Terms come from the real project_type and city taxonomies (slug-agnostic).
 * The active tab follows the mega_projects_group option; counters reflect the
 * actual published-project count of each term (AC-03).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Options\Tabs\MegaMenuTab;

$es_config  = MegaMenuTab::projects_config();
$es_per_tab = max( 1, (int) $es_config['per_tab'] );
$es_group   = ( 'city' === $es_config['group'] ) ? 'city' : 'type';

$es_layers  = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layers h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"></path><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"></path><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"></path></svg>';
$es_map_pin = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>';
$es_chevron = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 %s" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>';
$es_arrow   = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>';

$es_get_terms = static function ( $taxonomy, $limit ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'number'     => $limit,
		)
	);
	return is_wp_error( $terms ) ? array() : $terms;
};

$es_type_terms = $es_get_terms( 'project_type', $es_per_tab );
$es_city_terms = $es_get_terms( 'city', $es_per_tab );

/**
 * Render one term link with its project counter.
 */
$es_term_link = static function ( $term, $group ) use ( $es_arrow ) {
	$url   = get_term_link( $term );
	$url   = is_wp_error( $url ) ? home_url( '/projects/' ) : $url;
	$count = (int) $term->count;
	$arrow = str_replace( 'group-hover/cat:', 'group-hover/' . $group . ':', $es_arrow );
	$arrow = str_replace( 'text-slate-300 transition-transform', 'text-slate-300', $arrow );
	?>
	<a href="<?php echo esc_url( $url ); ?>" data-project-count="<?php echo esc_attr( (string) $count ); ?>" class="group/<?php echo esc_attr( $group ); ?> flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-950">
		<span class="truncate"><?php echo esc_html( $term->name ); ?></span>
		<span class="flex shrink-0 items-center gap-1.5">
			<span class="text-[10px] font-black text-emerald-700 tabular-nums"><?php echo esc_html( number_format_i18n( $count ) ); ?></span>
			<?php echo $arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
		</span>
	</a>
	<?php
};
?>
<div data-desktop-mega-panel="projects" class="absolute inset-x-4 top-full z-50 mx-auto max-w-[740px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
	<div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]">
		<div class="grid grid-cols-12">

			<div class="col-span-4 flex flex-col justify-between border-l border-slate-100 bg-slate-50/70 p-3.5">
				<div>
					<div class="mb-2 px-1 text-[10.5px] font-extrabold text-slate-400">تفکیک پروژه‌ها</div>
					<div class="space-y-1">
						<button type="button" data-mega-proj-tab="type" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right transition-all cursor-pointer <?php echo ( 'type' === $es_group ) ? 'bg-white text-emerald-950 shadow-2xs ring-1 ring-emerald-200' : 'text-slate-700 hover:bg-white/70'; ?>">
							<?php echo $es_layers; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span class="flex-1 text-xs font-extrabold">بر اساس نوع سازه</span>
							<?php echo sprintf( $es_chevron, ( 'type' === $es_group ) ? 'text-emerald-700' : 'text-slate-300' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
						<button type="button" data-mega-proj-tab="city" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right transition-all cursor-pointer <?php echo ( 'city' === $es_group ) ? 'bg-white text-emerald-950 shadow-2xs ring-1 ring-emerald-200' : 'text-slate-700 hover:bg-white/70'; ?>">
							<?php echo $es_map_pin; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span class="flex-1 text-xs font-extrabold">بر اساس شهر اجرا</span>
							<?php echo sprintf( $es_chevron, ( 'city' === $es_group ) ? 'text-emerald-700' : 'text-slate-300' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</button>
					</div>
				</div>
				<a href="<?php echo esc_url( Menus::page_url( 'projects' ) ); ?>" class="mt-3 flex items-center justify-center gap-1.5 rounded-xl bg-emerald-700 px-3 py-2 text-[11.5px] font-extrabold text-white hover:bg-emerald-800"><span>همه پروژه‌ها</span><?php echo $es_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</div>

			<div class="col-span-8 p-4">
				<div data-mega-proj-panel="type" class="<?php echo ( 'type' === $es_group ) ? 'block' : 'hidden'; ?>">
					<div class="mb-2.5 text-xs font-black text-slate-900">آرشیو پروژه‌ها بر اساس نوع سازه نوری</div>
					<div class="grid grid-cols-2 gap-1.5">
						<?php foreach ( $es_type_terms as $es_term ) { $es_term_link( $es_term, 'pt' ); } ?>
					</div>
				</div>
				<div data-mega-proj-panel="city" class="<?php echo ( 'city' === $es_group ) ? 'block' : 'hidden'; ?>">
					<div class="mb-2.5 text-xs font-black text-slate-900">آرشیو پروژه‌ها بر اساس شهر محل اجرا</div>
					<div class="grid grid-cols-3 gap-1.5">
						<?php foreach ( $es_city_terms as $es_term ) { $es_term_link( $es_term, 'pc' ); } ?>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
