<?php
/**
 * Shop sidebar: search, purchase mode, budget, IP/voltage and the category tree.
 *
 * Every control is a hook the archive script already binds to, and every option
 * is derived from the products and term tables rather than from the mirror's
 * sample values. The drawer copy is the desktop card, so mobile and desktop can
 * never drift apart.
 *
 * @package ErfanSanat
 *
 * @var array<int, \WP_Term> $erfansanat_category_tree Flat category list, already ordered.
 * @var int                  $erfansanat_total         Published product count.
 * @var \WP_Term|null        $erfansanat_current       Current term, when on a category page.
 * @var string               $erfansanat_shop_url      Shop permalink.
 * @var array<int, string>   $erfansanat_ips           Distinct IP ratings.
 * @var array<int, string>   $erfansanat_voltages      Distinct input voltages.
 * @var bool                 $erfansanat_drawer        True on the mobile copy.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_category_tree = isset( $erfansanat_category_tree ) && is_array( $erfansanat_category_tree ) ? $erfansanat_category_tree : array();
$erfansanat_total         = isset( $erfansanat_total ) ? (int) $erfansanat_total : 0;
$erfansanat_current       = isset( $erfansanat_current ) && $erfansanat_current instanceof \WP_Term ? $erfansanat_current : null;
$erfansanat_shop_url      = isset( $erfansanat_shop_url ) ? (string) $erfansanat_shop_url : '';
$erfansanat_ips           = isset( $erfansanat_ips ) && is_array( $erfansanat_ips ) ? $erfansanat_ips : array();
$erfansanat_voltages      = isset( $erfansanat_voltages ) && is_array( $erfansanat_voltages ) ? $erfansanat_voltages : array();
$erfansanat_drawer        = isset( $erfansanat_drawer ) && (bool) $erfansanat_drawer;

$erfansanat_field = 'block w-full rounded-xl border border-slate-200 bg-slate-50 px-2.5 py-2 text-[12px] font-bold text-slate-700 focus:border-emerald-500 focus:outline-none';
?>
<div class="sticky top-28 space-y-5 rounded-[1.75rem] border border-slate-200/90 bg-white p-5 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.10)]">
	<div class="flex items-center justify-between border-b border-slate-100 pb-4">
		<div class="flex items-center gap-2.5">
			<span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5" aria-hidden="true"><line x1="21" y1="4" x2="14" y2="4"></line><line x1="10" y1="4" x2="3" y2="4"></line><line x1="21" y1="12" x2="12" y2="12"></line><line x1="8" y1="12" x2="3" y2="12"></line><line x1="21" y1="20" x2="16" y2="20"></line><line x1="12" y1="20" x2="3" y2="20"></line><line x1="14" y1="2" x2="14" y2="6"></line><line x1="8" y1="10" x2="8" y2="14"></line><line x1="16" y1="18" x2="16" y2="22"></line></svg>
			</span>
			<div>
				<h2 class="text-[14.5px] font-black text-slate-900"><?php esc_html_e( 'فیلترهای هوشمند فروشگاه', 'erfansanat' ); ?></h2>
				<p class="text-[11.5px] font-semibold text-slate-500">
					<span data-product-count="true"><?php echo esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) ); ?></span>
					<?php esc_html_e( 'محصول منطبق', 'erfansanat' ); ?>
				</p>
			</div>
		</div>
		<?php if ( $erfansanat_drawer ) : ?>
			<button type="button" data-archive-drawer-close="true" aria-label="<?php esc_attr_e( 'بستن فیلترها', 'erfansanat' ); ?>" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 text-slate-500 transition-colors hover:bg-slate-50">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
			</button>
		<?php endif; ?>
	</div>

	<div>
		<label for="product-sidebar-search<?php echo $erfansanat_drawer ? '-drawer' : ''; ?>" class="mb-2 block text-[12px] font-extrabold text-slate-700"><?php esc_html_e( 'جستجو در نام یا کد محصول', 'erfansanat' ); ?></label>
		<div class="relative">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
			<input id="product-sidebar-search<?php echo $erfansanat_drawer ? '-drawer' : ''; ?>" type="search" data-product-search="true" placeholder="<?php esc_attr_e( 'نام محصول، کد GS1 یا ویژگی…', 'erfansanat' ); ?>" value="" class="w-full rounded-xl border border-slate-200 bg-slate-50/70 py-2.5 pl-3.5 pr-10 text-[13px] font-medium text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none" />
		</div>
	</div>

	<div class="border-t border-slate-100 pt-4">
		<div class="mb-2.5 flex items-center gap-2 text-[12px] font-extrabold text-slate-800">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 text-emerald-700" aria-hidden="true"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
			<span><?php esc_html_e( 'وضعیت خرید و استعلام', 'erfansanat' ); ?></span>
		</div>
		<div class="grid grid-cols-3 gap-1.5 rounded-xl bg-slate-100 p-1" role="group" aria-label="<?php esc_attr_e( 'وضعیت خرید', 'erfansanat' ); ?>">
			<button type="button" data-product-purchase-mode="all" class="cursor-pointer rounded-lg bg-emerald-700 py-2 text-[11.5px] font-extrabold text-white shadow-2xs transition-all"><?php esc_html_e( 'همه', 'erfansanat' ); ?></button>
			<button type="button" data-product-purchase-mode="online" class="cursor-pointer rounded-lg py-2 text-[11.5px] font-extrabold text-slate-600 transition-all hover:text-slate-900"><?php esc_html_e( 'خرید آنلاین', 'erfansanat' ); ?></button>
			<button type="button" data-product-purchase-mode="inquiry" class="cursor-pointer rounded-lg py-2 text-[11.5px] font-extrabold text-slate-600 transition-all hover:text-slate-900"><?php esc_html_e( 'استعلامی', 'erfansanat' ); ?></button>
		</div>
		<span data-product-active-purch-badge="true" class="mt-2 hidden items-center gap-1 rounded-lg bg-emerald-50 px-2 py-1 text-[11px] font-bold text-emerald-800"><?php esc_html_e( 'فقط محصولات قابل خرید آنلاین', 'erfansanat' ); ?></span>
	</div>

	<div class="border-t border-slate-100 pt-4">
		<div class="mb-2 flex items-center justify-between text-[12px] font-extrabold text-slate-800">
			<span><?php esc_html_e( 'سقف بودجه (فیلتر قیمت)', 'erfansanat' ); ?></span>
			<span data-product-price-label="true" class="text-[11.5px] font-bold text-emerald-700"><?php esc_html_e( 'همه قیمت‌ها', 'erfansanat' ); ?></span>
		</div>
		<input type="range" min="500000" max="15000000" step="500000" data-product-price-range="true" aria-label="<?php esc_attr_e( 'سقف بودجه', 'erfansanat' ); ?>" class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-emerald-100 accent-emerald-700" value="15000000" />
		<div class="mt-1 flex justify-between text-[10.5px] font-semibold text-slate-400">
			<span><?php esc_html_e( '۵۰۰ هزار تومان', 'erfansanat' ); ?></span>
			<span><?php esc_html_e( '۱۵ میلیون تومان+', 'erfansanat' ); ?></span>
		</div>
	</div>

	<div class="grid grid-cols-2 gap-2.5 border-t border-slate-100 pt-4">
		<div>
			<label for="product-ip<?php echo $erfansanat_drawer ? '-drawer' : ''; ?>" class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'درجه حفاظت (IP)', 'erfansanat' ); ?></label>
			<select id="product-ip<?php echo $erfansanat_drawer ? '-drawer' : ''; ?>" data-product-ip-filter="true" class="<?php echo esc_attr( $erfansanat_field ); ?>">
				<option value="all" selected><?php esc_html_e( 'همه استانداردها', 'erfansanat' ); ?></option>
				<?php foreach ( $erfansanat_ips as $erfansanat_ip ) : ?>
					<option value="<?php echo esc_attr( $erfansanat_ip ); ?>"><?php echo esc_html( $erfansanat_ip ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div>
			<label for="product-voltage<?php echo $erfansanat_drawer ? '-drawer' : ''; ?>" class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'ولتاژ ورودی', 'erfansanat' ); ?></label>
			<select id="product-voltage<?php echo $erfansanat_drawer ? '-drawer' : ''; ?>" data-product-voltage-filter="true" class="<?php echo esc_attr( $erfansanat_field ); ?>">
				<option value="all" selected><?php esc_html_e( 'همه ولتاژها', 'erfansanat' ); ?></option>
				<?php foreach ( $erfansanat_voltages as $erfansanat_voltage ) : ?>
					<option value="<?php echo esc_attr( $erfansanat_voltage ); ?>"><?php echo esc_html( $erfansanat_voltage ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<?php if ( array() !== $erfansanat_category_tree ) : ?>
		<div class="border-t border-slate-100 pt-4">
			<div class="mb-3 flex items-center gap-2 text-[12.5px] font-extrabold text-slate-800">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
				<span><?php esc_html_e( 'دسته‌بندی محصولات', 'erfansanat' ); ?></span>
			</div>
			<nav aria-label="<?php esc_attr_e( 'آرشیو دسته‌بندی محصولات', 'erfansanat' ); ?>" class="space-y-1.5">
				<a href="<?php echo esc_url( $erfansanat_shop_url ); ?>" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all <?php echo null === $erfansanat_current ? 'bg-emerald-700 text-white shadow-sm shadow-emerald-700/20' : 'text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800'; ?>">
					<span class="flex items-center gap-2">
						<span class="h-2 w-2 rounded-full <?php echo null === $erfansanat_current ? 'bg-white' : 'bg-emerald-500/70'; ?>"></span>
						<span><?php esc_html_e( 'همه محصولات تولیدی', 'erfansanat' ); ?></span>
					</span>
					<span class="rounded-md px-2 py-0.5 text-[11px] font-extrabold <?php echo null === $erfansanat_current ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'; ?>"><?php echo esc_html( SectionParts::fa_digits( (string) $erfansanat_total ) ); ?></span>
				</a>
				<?php foreach ( $erfansanat_category_tree as $erfansanat_term ) : ?>
					<?php
					$erfansanat_is_current = null !== $erfansanat_current && (int) $erfansanat_current->term_id === (int) $erfansanat_term->term_id;
					$erfansanat_is_child   = (int) $erfansanat_term->parent > 0;
					$erfansanat_link       = (string) get_term_link( $erfansanat_term );
					?>
					<div class="<?php echo $erfansanat_is_child ? 'mr-3.5 border-r-2 border-emerald-100 pr-2.5 pt-0.5' : ''; ?>">
						<a href="<?php echo esc_url( $erfansanat_link ); ?>" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all <?php echo $erfansanat_is_current ? 'bg-emerald-700 text-white shadow-sm shadow-emerald-700/20' : 'text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800'; ?>">
							<span class="flex items-center gap-2">
								<span class="h-2 w-2 shrink-0 rounded-full <?php echo $erfansanat_is_current ? 'bg-white' : 'bg-emerald-500/70'; ?>"></span>
								<span class="leading-5"><?php echo esc_html( $erfansanat_term->name ); ?></span>
							</span>
							<span class="rounded-md px-2 py-0.5 text-[11px] font-extrabold <?php echo $erfansanat_is_current ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500'; ?>"><?php echo esc_html( SectionParts::fa_digits( (string) (int) $erfansanat_term->count ) ); ?></span>
						</a>
					</div>
				<?php endforeach; ?>
			</nav>
		</div>
	<?php endif; ?>

	<div class="rounded-2xl border border-emerald-200/70 bg-emerald-50/60 p-4">
		<div class="flex flex-wrap items-center justify-between gap-3 border-emerald-200/60">
			<span class="text-[12.5px] font-bold text-slate-600"><?php esc_html_e( 'استعلام قیمت عمده و دریافت پیش‌فاکتور رسمی:', 'erfansanat' ); ?></span>
			<a href="<?php echo esc_url( SectionParts::phone_href() ); ?>" class="inline-flex items-center gap-2 whitespace-nowrap rounded-xl bg-emerald-800 px-4 py-2 text-[12.5px] font-black text-white transition-colors hover:bg-emerald-900">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
				<span dir="ltr"><?php echo esc_html( SectionParts::phone() ); ?></span>
			</a>
		</div>
	</div>
</div>
