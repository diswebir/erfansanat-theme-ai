<?php
/**
 * Global search modal shell.
 *
 * Pre-renders recent products/projects with data-search-item so the bundled
 * main.js can filter client-side immediately; the AJAX engine arrives in
 * Phase 8. Empty states are real messages, never placeholders (AC-02).
 * data-search-modal / open / close / input attributes preserved (AC-01).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;

$es_search_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search pointer-events-none absolute right-4 h-5 w-5 text-emerald-600" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>';
$es_close_icon  = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-4.5 w-4.5" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>';
$es_row_arrow   = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg>';

$es_recent = static function ( $post_type, $limit = 6 ) {
	if ( ! post_type_exists( $post_type ) ) {
		return array();
	}
	return get_posts( array(
		'post_type'   => $post_type,
		'post_status' => 'publish',
		'numberposts' => $limit,
		'orderby'     => 'date',
		'order'       => 'DESC',
	) );
};

$es_products = $es_recent( 'product' );
$es_projects = $es_recent( 'projects' );

/**
 * Render one search result row.
 */
$es_search_row = static function ( $post, $subtitle ) use ( $es_row_arrow ) {
	$title = get_the_title( $post );
	$thumb = get_the_post_thumbnail_url( $post, 'thumbnail' );
	if ( ! $thumb ) {
		$thumb = ES_ASSETS_URI . 'images/hero.jpg';
	}
	$haystack = trim( $title . ' ' . $subtitle . ' ' . wp_strip_all_tags( get_the_excerpt( $post ) ) );
	?>
	<a href="<?php echo esc_url( get_permalink( $post ) ); ?>" data-search-item="<?php echo esc_attr( $haystack ); ?>" class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/70 p-2.5 transition-colors hover:border-emerald-300 hover:bg-emerald-50/50">
		<div class="flex min-w-0 items-center gap-3">
			<img src="<?php echo esc_url( $thumb ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="h-10 w-10 shrink-0 rounded-lg object-cover"/>
			<div class="min-w-0">
				<div class="truncate text-xs font-extrabold text-slate-900"><?php echo esc_html( $title ); ?></div>
				<div class="text-[10.5px] font-semibold text-slate-500"><?php echo esc_html( $subtitle ); ?></div>
			</div>
		</div>
		<?php echo $es_row_arrow; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</a>
	<?php
};

$es_product_subtitle = static function ( $post ) {
	if ( taxonomy_exists( 'product_cat' ) ) {
		$terms = get_the_terms( $post, 'product_cat' );
		if ( $terms && ! is_wp_error( $terms ) ) {
			return implode( ' · ', wp_list_pluck( $terms, 'name' ) );
		}
	}
	return '';
};

$es_project_subtitle = static function ( $post ) {
	$parts = array();
	foreach ( array( 'city', 'project_type' ) as $tax ) {
		if ( taxonomy_exists( $tax ) ) {
			$terms = get_the_terms( $post, $tax );
			if ( $terms && ! is_wp_error( $terms ) ) {
				$parts[] = $terms[0]->name;
			}
		}
	}
	return implode( ' · ', $parts );
};
?>
<div data-search-modal="true" class="hidden border-t border-emerald-100 bg-white/98 py-5 shadow-2xl backdrop-blur-xl">
	<div class="mx-auto max-w-[1300px] px-4 sm:px-5 lg:px-8">
		<div class="relative flex items-center">
			<?php echo $es_search_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<input type="search" data-global-search-input="true" placeholder="جستجوی سریع نام محصول (مثلاً ریسه سوزنی، درخت نوری، پوینت لایت) یا شهر پروژه..." class="w-full rounded-2xl border border-emerald-200 bg-slate-50 py-3.5 pl-12 pr-12 text-sm font-bold text-slate-900 outline-none focus:border-emerald-500 focus:bg-white" value=""/>
			<button type="button" data-search-modal-close="true" aria-label="بستن جستجو" class="absolute left-3 grid h-8 w-8 place-items-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700 cursor-pointer"><?php echo $es_close_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</div>
		<div class="mt-4 grid gap-6 md:grid-cols-2">
			<div>
				<div class="mb-2 flex items-center justify-between text-xs font-extrabold text-slate-500">
					<span>محصولات مرتبط</span>
					<a href="<?php echo esc_url( Menus::page_url( 'shop' ) ); ?>" class="text-emerald-700 hover:underline">همه محصولات ←</a>
				</div>
				<div data-global-search-products="true" class="space-y-2">
					<?php if ( empty( $es_products ) ) : ?>
						<p class="rounded-xl border border-dashed border-slate-200 bg-slate-50/60 p-3 text-[11.5px] font-medium text-slate-500">هنوز محصولی ثبت نشده است. پس از افزودن محصولات، نتایج جستجو اینجا نمایش داده می‌شود.</p>
					<?php else : ?>
						<?php foreach ( $es_products as $es_post ) { $es_search_row( $es_post, $es_product_subtitle( $es_post ) ); } ?>
					<?php endif; ?>
				</div>
			</div>
			<div>
				<div class="mb-2 flex items-center justify-between text-xs font-extrabold text-slate-500">
					<span>پروژه‌های اجراشده</span>
					<a href="<?php echo esc_url( Menus::page_url( 'projects' ) ); ?>" class="text-emerald-700 hover:underline">همه پروژه‌ها ←</a>
				</div>
				<div data-global-search-projects="true" class="space-y-2">
					<?php if ( empty( $es_projects ) ) : ?>
						<p class="rounded-xl border border-dashed border-slate-200 bg-slate-50/60 p-3 text-[11.5px] font-medium text-slate-500">هنوز پروژه‌ای ثبت نشده است. پس از افزودن پروژه‌ها، نتایج جستجو اینجا نمایش داده می‌شود.</p>
					<?php else : ?>
						<?php foreach ( $es_projects as $es_post ) { $es_search_row( $es_post, $es_project_subtitle( $es_post ) ); } ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
