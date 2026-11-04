<?php
/**
 * Products mega menu panel: category tabs + sub-category/product cards.
 *
 * Data comes from the product_cat taxonomy (slug-agnostic, Persian-safe).
 * Tab count and per-tab item count follow theme options (AC-03). DOM and
 * data-attributes mirror static-mirror/index.html so main.js drives the tabs.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\MegaMenuTab;

$es_config  = MegaMenuTab::products_config();
$es_per_tab = max( 1, (int) $es_config['per_tab'] );
$es_tab_cap = (int) apply_filters( 'es_mega_products_tabs', 6 );

$es_top_terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'parent'     => 0,
		'hide_empty' => false,
		'orderby'    => 'name',
		'order'      => 'ASC',
		'number'     => $es_tab_cap,
	)
);
if ( is_wp_error( $es_top_terms ) ) {
	$es_top_terms = array();
}

$es_icons = array(
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cpu h-4 w-4" aria-hidden="true"><path d="M12 20v2"></path><path d="M12 2v2"></path><path d="M17 20v2"></path><path d="M17 2v2"></path><path d="M2 12h2"></path><path d="M2 17h2"></path><path d="M2 7h2"></path><path d="M20 12h2"></path><path d="M20 17h2"></path><path d="M20 7h2"></path><path d="M7 20v2"></path><path d="M7 2v2"></path><rect x="4" y="4" width="16" height="16" rx="2"></rect><rect x="8" y="8" width="8" height="8" rx="1"></rect></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-4 w-4" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trees h-4 w-4" aria-hidden="true"><path d="M10 10v.2A3 3 0 0 1 8.9 16H5a3 3 0 0 1-1-5.8V10a3 3 0 0 1 6 0Z"></path><path d="M7 16v6"></path><path d="M13 19v3"></path><path d="M12 19h8.3a1 1 0 0 0 .7-1.7L18 14h.3a1 1 0 0 0 .7-1.7L16 9h.2a1 1 0 0 0 .8-1.7L13 3l-1.4 1.5"></path></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-waves h-4 w-4" aria-hidden="true"><path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"></path></svg>',
);

$es_chevron_left = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 %s" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>';
$es_arrow_up_left = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-3.5 w-3.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg>';
$es_arrow_left   = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3 w-3 shrink-0 text-slate-300 transition-transform group-hover/cat:-translate-x-0.5 group-hover/cat:text-emerald-700" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>';

$es_term_url = static function ( $term ) {
	$url = get_term_link( $term );
	return is_wp_error( $url ) ? home_url( '/shop/' ) : $url;
};
?>
<div data-desktop-mega-panel="products" class="absolute inset-x-4 top-full z-50 mx-auto max-w-[780px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100">
	<div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]">
		<div class="grid grid-cols-12">

			<div class="col-span-5 border-l border-slate-100 bg-slate-50/70 p-3.5">
				<div class="mb-2 px-2 text-[10.5px] font-extrabold text-slate-400">دسته‌بندی تولیدات (<?php echo esc_html( number_format_i18n( count( $es_top_terms ) ) ); ?> گروه)</div>
				<div class="space-y-1">
					<?php if ( empty( $es_top_terms ) ) : ?>
						<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right text-xs font-extrabold text-slate-700 hover:bg-white/70">مشاهده فروشگاه</a>
					<?php else : ?>
						<?php foreach ( $es_top_terms as $es_i => $es_term ) : ?>
							<?php $es_active = ( 0 === $es_i ); ?>
							<button type="button" data-mega-prod-tab="<?php echo esc_attr( (string) $es_i ); ?>" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right transition-all cursor-pointer <?php echo $es_active ? 'bg-white text-emerald-950 shadow-2xs ring-1 ring-emerald-200' : 'text-slate-700 hover:bg-white/70'; ?>">
								<span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg transition-colors <?php echo $es_active ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-700'; ?>"><?php echo $es_icons[ $es_i % count( $es_icons ) ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
								<span class="min-w-0 flex-1 truncate text-xs font-extrabold"><?php echo esc_html( $es_term->name ); ?></span>
								<?php echo sprintf( $es_chevron_left, $es_active ? 'text-emerald-700' : 'text-slate-300' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							</button>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>

			<div class="col-span-7 flex flex-col justify-between p-4">
				<?php if ( empty( $es_top_terms ) ) : ?>
					<div data-mega-prod-panel="0" class="block">
						<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="group/cat flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-950"><span class="truncate">همه محصولات</span><?php echo $es_arrow_left; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					</div>
				<?php else : ?>
					<?php foreach ( $es_top_terms as $es_i => $es_term ) : ?>
						<?php
						$es_active = ( 0 === $es_i );
						$es_url    = $es_term_url( $es_term );

						$es_children = get_terms(
							array(
								'taxonomy'   => 'product_cat',
								'parent'     => $es_term->term_id,
								'hide_empty' => false,
								'orderby'    => 'name',
								'order'      => 'ASC',
								'number'     => $es_per_tab,
							)
						);
						if ( is_wp_error( $es_children ) ) {
							$es_children = array();
						}

						// Fall back to recent products in this category when it has no children.
						$es_product_fallback = array();
						if ( empty( $es_children ) && post_type_exists( 'product' ) ) {
							$es_product_fallback = get_posts(
								array(
									'post_type'   => 'product',
									'numberposts' => $es_per_tab,
									'post_status' => 'publish',
									'tax_query'   => array(
										array(
											'taxonomy' => 'product_cat',
											'field'    => 'term_id',
											'terms'    => $es_term->term_id,
										),
									),
								)
							);
						}
						?>
						<div data-mega-prod-panel="<?php echo esc_attr( (string) $es_i ); ?>" class="<?php echo $es_active ? 'block' : 'hidden'; ?>">
							<div class="mb-3 flex items-center justify-between border-b border-slate-100 pb-2.5">
								<a href="<?php echo esc_url( $es_url ); ?>" class="text-xs font-black text-slate-900 hover:text-emerald-700"><?php echo esc_html( $es_term->name ); ?></a>
								<a href="<?php echo esc_url( $es_url ); ?>" class="inline-flex items-center gap-1 text-[11px] font-extrabold text-emerald-700 hover:text-emerald-800"><span>مشاهده همه</span><?php echo $es_arrow_up_left; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
							</div>
							<div class="grid grid-cols-2 gap-1.5">
								<?php foreach ( $es_children as $es_child ) : ?>
									<a href="<?php echo esc_url( $es_term_url( $es_child ) ); ?>" class="group/cat flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-950"><span class="truncate"><?php echo esc_html( $es_child->name ); ?></span><?php echo $es_arrow_left; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
								<?php endforeach; ?>
								<?php foreach ( $es_product_fallback as $es_product ) : ?>
									<a href="<?php echo esc_url( get_permalink( $es_product ) ); ?>" class="group/cat flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-950"><span class="truncate"><?php echo esc_html( get_the_title( $es_product ) ); ?></span><?php echo $es_arrow_left; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>

		</div>
	</div>
</div>
