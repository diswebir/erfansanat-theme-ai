<?php
/**
 * Shop toolbar + product grid + paging + empty state.
 *
 * The grid is a flat list of `data-product-item` blocks, because the archive
 * script filters, sorts and pages them in the browser; WooCommerce's own
 * `content-product.php` prints an `<li>` with its own classes and cannot carry
 * that payload. The server still renders every published product in a stable
 * order, so paging works with JavaScript disabled too — each page's markup is
 * complete on its own.
 *
 * @package ErfanSanat
 *
 * @var array<int, \WC_Product> $erfansanat_products  Products of the current page.
 * @var int                     $erfansanat_total     Total published products.
 * @var int                     $erfansanat_page      Current page, 1-based.
 * @var int                     $erfansanat_per_page  Items per page.
 * @var string                  $erfansanat_heading   Toolbar title.
 * @var string                  $erfansanat_shop_url  Shop permalink, for the empty state.
 * @var bool                    $erfansanat_empty     Render the empty state instead of a grid.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\ProductData;
use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_products = isset( $erfansanat_products ) && is_array( $erfansanat_products ) ? $erfansanat_products : array();
$erfansanat_total    = isset( $erfansanat_total ) ? (int) $erfansanat_total : 0;
$erfansanat_page     = isset( $erfansanat_page ) ? max( 1, (int) $erfansanat_page ) : 1;
$erfansanat_per_page = isset( $erfansanat_per_page ) ? max( 1, (int) $erfansanat_per_page ) : 9;
$erfansanat_heading  = isset( $erfansanat_heading ) ? (string) $erfansanat_heading : '';
$erfansanat_shop_url = isset( $erfansanat_shop_url ) ? (string) $erfansanat_shop_url : '';
$erfansanat_empty    = isset( $erfansanat_empty ) && (bool) $erfansanat_empty;

$erfansanat_pages = (int) max( 1, (int) ceil( $erfansanat_total / $erfansanat_per_page ) );

$erfansanat_sorts = array(
	'default'    => __( 'پیش‌فرض کارخانه', 'erfansanat' ),
	'popular'    => __( 'پربازدیدترین', 'erfansanat' ),
	'rating'     => __( 'بالاترین امتیاز', 'erfansanat' ),
	'price-asc'  => __( 'ارزان‌ترین', 'erfansanat' ),
	'price-desc' => __( 'گران‌ترین', 'erfansanat' ),
);
?>
<div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-white px-4 py-3.5 shadow-2xs sm:px-5">
	<div class="flex flex-wrap items-center gap-2">
		<span class="text-[13.5px] font-black text-slate-900"><?php echo esc_html( $erfansanat_heading ); ?></span>
		<span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11.5px] font-extrabold text-emerald-800">
			<span data-product-count="true"><?php echo esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) ); ?></span>
			<?php esc_html_e( 'محصول', 'erfansanat' ); ?>
		</span>
	</div>
	<div class="flex flex-wrap items-center gap-2">
		<div class="flex items-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-1.5">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 text-slate-500" aria-hidden="true"><path d="M3 6h18"></path><path d="M7 12h10"></path><path d="M10 18h4"></path></svg>
			<label for="product-sort-select" class="text-[11.5px] font-bold text-slate-500"><?php esc_html_e( 'مرتب‌سازی:', 'erfansanat' ); ?></label>
			<select id="product-sort-select" data-product-sort="true" class="cursor-pointer bg-transparent text-[12px] font-extrabold text-slate-800 focus:outline-none">
				<?php foreach ( $erfansanat_sorts as $erfansanat_value => $erfansanat_label ) : ?>
					<option value="<?php echo esc_attr( $erfansanat_value ); ?>" <?php selected( 'default', $erfansanat_value ); ?>><?php echo esc_html( $erfansanat_label ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<button type="button" data-archive-drawer-open="true" class="inline-flex cursor-pointer items-center gap-2 rounded-xl bg-emerald-700 px-3.5 py-2 text-[12px] font-extrabold text-white shadow-xs transition-colors hover:bg-emerald-800 lg:hidden">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><line x1="21" y1="4" x2="14" y2="4"></line><line x1="10" y1="4" x2="3" y2="4"></line><line x1="21" y1="12" x2="12" y2="12"></line><line x1="8" y1="12" x2="3" y2="12"></line><line x1="21" y1="20" x2="16" y2="20"></line><line x1="12" y1="20" x2="3" y2="20"></line><line x1="14" y1="2" x2="14" y2="6"></line><line x1="8" y1="10" x2="8" y2="14"></line><line x1="16" y1="18" x2="16" y2="22"></line></svg>
			<span><?php esc_html_e( 'فیلتر و دسته‌بندی', 'erfansanat' ); ?></span>
		</button>
	</div>
</div>

<?php if ( $erfansanat_empty ) : ?>
	<div data-product-empty="true" class="block rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 py-16 text-center">
		<p class="text-[14.5px] font-extrabold text-slate-700"><?php esc_html_e( 'محصولی با این مشخصات یافت نشد.', 'erfansanat' ); ?></p>
		<p class="mt-1.5 text-[12.5px] text-slate-500"><?php esc_html_e( 'عبارت جستجو را تغییر دهید یا فیلترهای فعال را پاک کنید.', 'erfansanat' ); ?></p>
		<a href="<?php echo esc_url( $erfansanat_shop_url ); ?>" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-[12.5px] font-bold text-white hover:bg-emerald-800">
			<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
			<span><?php esc_html_e( 'نمایش همه محصولات', 'erfansanat' ); ?></span>
		</a>
	</div>
<?php else : ?>
	<div data-product-grid="true" class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
		<?php foreach ( $erfansanat_products as $erfansanat_product ) : ?>
			<?php
			if ( ! $erfansanat_product instanceof \WC_Product ) {
				continue;
			}
			$erfansanat_payload = ProductData::archive_payload( $erfansanat_product );
			?>
			<div
				data-product-item="true"
				data-product-category="<?php echo esc_attr( $erfansanat_payload['category'] ); ?>"
				data-product-categories="<?php echo esc_attr( $erfansanat_payload['categories'] ); ?>"
				data-product-purchasable="<?php echo esc_attr( $erfansanat_payload['purchasable'] ); ?>"
				data-product-price="<?php echo esc_attr( $erfansanat_payload['price'] ); ?>"
				data-product-ip="<?php echo esc_attr( $erfansanat_payload['ip'] ); ?>"
				data-product-voltage="<?php echo esc_attr( $erfansanat_payload['voltage'] ); ?>"
				data-product-wattage="<?php echo esc_attr( $erfansanat_payload['wattage'] ); ?>"
				data-product-views="<?php echo esc_attr( $erfansanat_payload['views'] ); ?>"
				data-product-rating="<?php echo esc_attr( $erfansanat_payload['rating'] ); ?>"
				data-product-search-text="<?php echo esc_attr( $erfansanat_payload['search_text'] ); ?>"
				class="block"
			>
				<?php
				$erfansanat_product_card = $erfansanat_product;
				include get_template_directory() . '/template-parts/cards/card-product-tile.php';
				?>
			</div>
		<?php endforeach; ?>
	</div>

	<div data-product-empty="true" class="hidden rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 py-16 text-center">
		<p class="text-[14.5px] font-extrabold text-slate-700"><?php esc_html_e( 'محصولی با این مشخصات یافت نشد.', 'erfansanat' ); ?></p>
		<p class="mt-1.5 text-[12.5px] text-slate-500"><?php esc_html_e( 'عبارت جستجو را تغییر دهید یا فیلترهای فعال را پاک کنید.', 'erfansanat' ); ?></p>
		<a href="<?php echo esc_url( $erfansanat_shop_url ); ?>" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-[12.5px] font-bold text-white hover:bg-emerald-800">
			<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
			<span><?php esc_html_e( 'نمایش همه محصولات', 'erfansanat' ); ?></span>
		</a>
	</div>

	<nav
		aria-label="<?php esc_attr_e( 'صفحه‌بندی محصولات', 'erfansanat' ); ?>"
		data-pagination-root="true"
		data-root="products"
		data-per-page="<?php echo esc_attr( (string) $erfansanat_per_page ); ?>"
		data-pagination-server="true"
		class="<?php echo $erfansanat_pages > 1 ? 'flex' : 'hidden'; ?> mt-10 flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/90 bg-white px-5 py-4 shadow-2xs"
	>
		<div class="text-[12.5px] font-bold text-slate-600">
			<?php esc_html_e( 'نمایش صفحه', 'erfansanat' ); ?>
			<span data-pagination-summary="true" class="font-black text-emerald-800">
				<?php
				printf(
					/* translators: 1: current page, 2: total pages, 3: total products. */
					esc_html__( 'صفحه %1$s از %2$s (%3$s مورد)', 'erfansanat' ),
					esc_html( SectionParts::fa_digits( (string) $erfansanat_page ) ),
					esc_html( SectionParts::fa_digits( (string) $erfansanat_pages ) ),
					esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) )
				);
				?>
			</span>
		</div>
		<div class="flex items-center gap-1.5" data-pagination-buttons="true">
			<?php
			$erfansanat_prev_url = $erfansanat_page > 1
				? ( isset( $erfansanat_base_url ) ? (string) $erfansanat_base_url . ( $erfansanat_page - 1 > 1 ? 'page/' . ( $erfansanat_page - 1 ) . '/' : '' ) : '' )
				: '';
			?>
			<a
				href="<?php echo esc_url( '' !== $erfansanat_prev_url ? $erfansanat_prev_url : '#' ); ?>"
				data-page-prev="true"
				<?php echo '' === $erfansanat_prev_url ? 'aria-disabled="true" tabindex="-1"' : ''; ?>
				class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 <?php echo '' === $erfansanat_prev_url ? 'pointer-events-none opacity-40' : ''; ?>">
				<?php SectionParts::arrow( 'h-3.5 w-3.5 rotate-180' ); ?>
				<span><?php esc_html_e( 'قبلی', 'erfansanat' ); ?></span>
			</a>
			<?php $erfansanat_max_buttons = min( 5, $erfansanat_pages ); ?>
			<?php for ( $erfansanat_number = 1; $erfansanat_number <= $erfansanat_max_buttons; $erfansanat_number++ ) : ?>
				<?php
				$erfansanat_number_url = 1 === $erfansanat_number
					? ( isset( $erfansanat_base_url ) ? (string) $erfansanat_base_url : '' )
					: ( isset( $erfansanat_base_url ) ? (string) $erfansanat_base_url . 'page/' . $erfansanat_number . '/' : '' );
				?>
				<a
					href="<?php echo esc_url( '' !== $erfansanat_number_url ? $erfansanat_number_url : '#' ); ?>"
					data-page-num="<?php echo esc_attr( (string) $erfansanat_number ); ?>"
					<?php echo $erfansanat_number === $erfansanat_page ? 'aria-current="page"' : ''; ?>
					class="grid h-9 w-9 place-items-center rounded-xl text-[13px] font-black transition-all <?php echo $erfansanat_number === $erfansanat_page ? 'bg-emerald-700 text-white shadow-sm shadow-emerald-700/25' : 'border border-slate-200 bg-white text-slate-700 hover:border-emerald-400 hover:text-emerald-700'; ?>"><?php echo esc_html( SectionParts::fa_digits( (string) $erfansanat_number ) ); ?></a>
			<?php endfor; ?>
			<?php
			$erfansanat_next_url = $erfansanat_page < $erfansanat_pages && isset( $erfansanat_base_url )
				? (string) $erfansanat_base_url . 'page/' . ( $erfansanat_page + 1 ) . '/'
				: '';
			?>
			<a
				href="<?php echo esc_url( '' !== $erfansanat_next_url ? $erfansanat_next_url : '#' ); ?>"
				data-page-next="true"
				<?php echo '' === $erfansanat_next_url ? 'aria-disabled="true" tabindex="-1"' : ''; ?>
				class="inline-flex items-center gap-1 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:bg-emerald-50 <?php echo '' === $erfansanat_next_url ? 'pointer-events-none opacity-40' : ''; ?>">
				<span><?php esc_html_e( 'بعدی', 'erfansanat' ); ?></span>
				<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
			</a>
		</div>
	</nav>
<?php endif; ?>

<div class="fixed bottom-5 left-1/2 z-40 -translate-x-1/2 lg:hidden">
	<button type="button" data-archive-drawer-open="true" class="flex cursor-pointer items-center gap-2.5 whitespace-nowrap rounded-full border border-emerald-400/30 bg-slate-950/95 px-5 py-3.5 text-[13px] font-black text-white shadow-[0_18px_40px_-10px_rgba(0,0,0,0.55)] backdrop-blur-md transition-transform active:scale-95">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-emerald-400" aria-hidden="true"><line x1="21" y1="4" x2="14" y2="4"></line><line x1="10" y1="4" x2="3" y2="4"></line><line x1="21" y1="12" x2="12" y2="12"></line><line x1="8" y1="12" x2="3" y2="12"></line><line x1="21" y1="20" x2="16" y2="20"></line><line x1="12" y1="20" x2="3" y2="20"></line><line x1="14" y1="2" x2="14" y2="6"></line><line x1="8" y1="10" x2="8" y2="14"></line><line x1="16" y1="18" x2="16" y2="22"></line></svg>
		<span><?php esc_html_e( 'فیلتر و دسته‌بندی', 'erfansanat' ); ?></span>
	</button>
</div>
