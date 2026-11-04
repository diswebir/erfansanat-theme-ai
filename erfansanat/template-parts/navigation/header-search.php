<?php
/**
 * Header search button and its inline panel.
 *
 * The panel is a real GET form to the WordPress search, so it works without
 * JavaScript; main.js only toggles it open. `data-global-search-input` is kept
 * because the reference live search binds to it in a later phase.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$erfansanat_search_id = wp_unique_id( 'erfansanat-search-' );
?>
<button type="button"
	data-search-modal-open="true"
	aria-label="<?php esc_attr_e( 'جستجو در محصولات و پروژه‌ها', 'erfansanat' ); ?>"
	aria-expanded="false"
	aria-controls="erfansanat-search-panel"
	class="grid h-9 w-9 place-items-center rounded-xl border transition-all sm:h-10 sm:w-10 cursor-pointer border-slate-200 bg-slate-50/80 text-slate-700 hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800">
	<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
</button>

<div id="erfansanat-search-panel"
	data-search-modal="true"
	class="hidden border-t border-emerald-100 bg-white/98 py-5 shadow-2xl backdrop-blur-xl">
	<div class="mx-auto max-w-[1300px] px-4 sm:px-5 lg:px-8">
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative flex items-center">
			<label class="screen-reader-text" for="<?php echo esc_attr( $erfansanat_search_id ); ?>">
				<?php esc_html_e( 'جستجو', 'erfansanat' ); ?>
			</label>
			<input type="search"
				id="<?php echo esc_attr( $erfansanat_search_id ); ?>"
				name="s"
				value="<?php echo esc_attr( get_search_query() ); ?>"
				data-global-search-input="true"
				placeholder="<?php esc_attr_e( 'جستجوی سریع نام محصول یا پروژه…', 'erfansanat' ); ?>"
				class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-11 text-sm text-slate-800 outline-none focus:border-emerald-400" />
			<button type="submit" class="absolute right-2 grid h-9 w-9 place-items-center rounded-lg bg-emerald-600 text-white hover:bg-emerald-700" aria-label="<?php esc_attr_e( 'اجرای جستجو', 'erfansanat' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
			</button>
		</form>
		<p class="mt-2 text-[11px] text-slate-500">
			<?php esc_html_e( 'جستجو در محصولات، پروژه‌ها، خدمات و مقالات.', 'erfansanat' ); ?>
		</p>
	</div>
</div>
