<?php
/**
 * Mobile filter drawer.
 *
 * The script looks these hooks up on the archive root itself, so the drawer must
 * stay inside `[data-products-archive]`. The panel reuses the desktop card.
 *
 * @package ErfanSanat
 *
 * @var array<int, \WP_Term> $erfansanat_category_tree Category list.
 * @var int                  $erfansanat_total         Published product count.
 * @var \WP_Term|null        $erfansanat_current       Current term.
 * @var string               $erfansanat_shop_url      Shop permalink.
 * @var array<int, string>   $erfansanat_ips           Distinct IP ratings.
 * @var array<int, string>   $erfansanat_voltages      Distinct input voltages.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;
?>
<div data-archive-drawer-backdrop="true" class="fixed inset-0 z-40 hidden bg-slate-950/55 backdrop-blur-sm lg:hidden"></div>
<div data-archive-drawer="true" class="fixed inset-y-0 right-0 z-50 hidden w-80 flex-col overflow-y-auto bg-slate-50 p-4 lg:hidden">
	<div class="mb-3 flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3">
		<span class="text-[13px] font-black text-slate-900"><?php esc_html_e( 'فیلتر و دسته‌بندی محصولات', 'erfansanat' ); ?></span>
		<button type="button" data-archive-drawer-close="true" aria-label="<?php esc_attr_e( 'بستن فیلترها', 'erfansanat' ); ?>" class="grid h-9 w-9 cursor-pointer place-items-center rounded-xl border border-slate-200 text-slate-500 transition-colors hover:bg-slate-50">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
		</button>
	</div>

	<?php
	$erfansanat_drawer = true;
	require get_template_directory() . '/template-parts/archive/part-shop-filters.php';
	?>
</div>
