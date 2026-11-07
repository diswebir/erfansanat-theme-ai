<?php
/**
 * Shared product-archive shell (P06-T016). Renders the reference shop layout:
 * hero, filter sidebar, toolbar (sort + count + drawer trigger), the product
 * grid, client-side pagination shell, empty state and the mobile filter drawer.
 *
 * Filtering/sorting/pagination are driven client-side by the immutable main.js
 * against the data-* contract, so the server renders the full matching product
 * set into the grid. Used by archive-product.php and the product taxonomy
 * templates (P06-T018 / T020).
 *
 * @package ErfanSanat
 * @var array $args {
 *   @type WP_Query $query             Product query (full matching set).
 *   @type string   $heading           H1 plain part.
 *   @type string   $heading_gradient  H1 gradient part.
 *   @type string   $breadcrumb_label  Current breadcrumb label.
 *   @type string   $initial_category  Active category name (default "همه").
 *   @type int    $per_page            Client-side page size (default 9).
 *   @type string   $toolbar_title     Toolbar heading.
 *   @type bool   $show_hero           Whether to render the hero (default true).
 * }
 */

defined( 'ABSPATH' ) || exit;

$es_query            = isset( $args['query'] ) && $args['query'] instanceof WP_Query ? $args['query'] : null;
$es_heading          = isset( $args['heading'] ) ? $args['heading'] : 'فروشگاه محصولات';
$es_heading_gradient = isset( $args['heading_gradient'] ) ? $args['heading_gradient'] : 'نورپردازی شهری و صنعتی';
$es_breadcrumb       = isset( $args['breadcrumb_label'] ) ? $args['breadcrumb_label'] : 'فروشگاه محصولات';
$es_initial          = isset( $args['initial_category'] ) ? $args['initial_category'] : 'همه';
$es_per_page         = isset( $args['per_page'] ) ? (int) $args['per_page'] : 9;
$es_toolbar_title    = isset( $args['toolbar_title'] ) ? $args['toolbar_title'] : 'همه محصولات تولیدی عرفان صنعت';
$es_show_hero        = ! isset( $args['show_hero'] ) || $args['show_hero'];
$es_hero_subtitle    = isset( $args['hero_subtitle'] ) ? $args['hero_subtitle'] : 'مجموعه‌ای کامل از تجهیزات نورپردازی پیشرفته؛ از پوینت لایت پیکسل، ریسه‌های بلوطی، فندقی و سوزنی IP68 تا درختان نوری هوشمند، خورشیدنماها و باکس کنترل مرکزی';
$es_hero_badge       = isset( $args['hero_badge'] ) ? $args['hero_badge'] : __( 'فروشگاه رسمی تولیدات کارخانه عرفان صنعت', 'erfansanat' );
$es_hero_image       = isset( $args['hero_image'] ) ? $args['hero_image'] : ES_ASSETS_URI . 'images/light-tree.jpg';
$es_total            = $es_query ? (int) $es_query->post_count : 0;

$es_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};

// Sidebar category tree (top-level + children), with live counts.
$es_top_cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0 ) );
$es_top_cats = is_wp_error( $es_top_cats ) ? array() : $es_top_cats;

$es_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : '';
if ( ! $es_shop_url ) {
	$es_shop_url = get_post_type_archive_link( 'product' );
}
if ( ! $es_shop_url ) {
	$es_shop_url = home_url( '/shop/' );
}

/** Render one category link row (used in sidebar + drawer). */
$es_cat_row = static function ( $term, $active, $child = false ) use ( $es_fa ) {
	$link = get_term_link( $term );
	if ( is_wp_error( $link ) ) {
		return;
	}
	$is_active = ( $active === $term->name );
	if ( $child ) {
		$cls = 'flex items-center justify-between rounded-lg px-2.5 py-1.5 text-[11.5px] font-bold transition-all text-slate-600 hover:bg-emerald-50/70 hover:text-emerald-800';
		if ( $is_active ) {
			$cls = 'flex items-center justify-between rounded-lg px-2.5 py-1.5 text-[11.5px] font-bold transition-all bg-emerald-50 text-emerald-800';
		}
		?>
		<a href="<?php echo esc_url( $link ); ?>" class="<?php echo esc_attr( $cls ); ?>">
			<span><?php echo esc_html( $term->name ); ?></span>
			<span class="text-[10.5px] opacity-75"><?php echo esc_html( $es_fa( $term->count ) ); ?></span>
		</a>
		<?php
		return;
	}
	$cls = 'flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800';
	$badge = 'rounded-md px-2 py-0.5 text-[11px] font-extrabold bg-slate-100 text-slate-500';
	$dot = 'h-2 w-2 shrink-0 rounded-full bg-emerald-500/70';
	if ( $is_active ) {
		$cls = 'flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all bg-emerald-700 text-white shadow-sm shadow-emerald-700/25';
		$badge = 'rounded-md px-2 py-0.5 text-[11px] font-extrabold bg-white/20 text-white';
		$dot = 'h-2 w-2 rounded-full bg-white';
	}
	?>
	<a href="<?php echo esc_url( $link ); ?>" class="<?php echo esc_attr( $cls ); ?>">
		<span class="flex items-center gap-2">
			<span class="<?php echo esc_attr( $dot ); ?>"></span>
			<span class="leading-5"><?php echo esc_html( $term->name ); ?></span>
		</span>
		<span class="<?php echo esc_attr( $badge ); ?>"><?php echo esc_html( $es_fa( $term->count ) ); ?></span>
	</a>
	<?php
	$children = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => $term->term_id ) );
	if ( $children && ! is_wp_error( $children ) ) {
		echo '<div class="mr-3.5 space-y-1 border-r-2 border-emerald-100 pr-2.5 pt-0.5">';
		foreach ( $children as $child ) {
			$clink = get_term_link( $child );
			if ( is_wp_error( $clink ) ) {
				continue;
			}
			$cactive = ( $active === $child->name );
			$ccls = $cactive
				? 'flex items-center justify-between rounded-lg px-2.5 py-1.5 text-[11.5px] font-bold transition-all bg-emerald-50 text-emerald-800'
				: 'flex items-center justify-between rounded-lg px-2.5 py-1.5 text-[11.5px] font-bold transition-all text-slate-600 hover:bg-emerald-50/70 hover:text-emerald-800';
			echo '<a href="' . esc_url( $clink ) . '" class="' . esc_attr( $ccls ) . '"><span>' . esc_html( $child->name ) . '</span><span class="text-[10.5px] opacity-75">' . esc_html( $es_fa( $child->count ) ) . '</span></a>';
		}
		echo '</div>';
	}
};

/** Shared filter controls (sidebar + drawer). */
$es_filter_controls = static function ( $uid, $active ) use ( $es_fa, $es_top_cats, $es_shop_url, $es_cat_row ) {
	?>
	<div>
		<label for="<?php echo esc_attr( $uid ); ?>-search" class="mb-2 block text-[12px] font-extrabold text-slate-700"><?php esc_html_e( 'جستجو در نام یا کد محصول', 'erfansanat' ); ?></label>
		<div class="relative">
			<svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
			<input id="<?php echo esc_attr( $uid ); ?>-search" type="search" data-product-search="true" placeholder="<?php esc_attr_e( 'نام محصول، کد GS1 یا ویژگی...', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-3.5 pr-9 text-[12.5px] font-bold text-slate-800 outline-none focus:border-emerald-500 focus:bg-white" />
		</div>
	</div>
	<div class="border-t border-slate-100 pt-4">
		<div class="mb-2.5 flex items-center gap-2 text-[12px] font-extrabold text-slate-800">
			<svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="8" r="6"/><path d="M18.09 10.37A6 6 0 1 1 10.34 18"/><path d="M7 6h1v4"/><path d="m16.71 13.88.7.71-2.82 2.82"/></svg>
			<span><?php esc_html_e( 'وضعیت خرید و استعلام', 'erfansanat' ); ?></span>
		</div>
		<div class="grid grid-cols-3 gap-1.5 rounded-xl bg-slate-100 p-1">
			<?php
			// main.js reads these as radios (change + .checked); "call" = inquiry.
			$es_modes = array(
				'all'    => __( 'همه', 'erfansanat' ),
				'online' => __( 'خرید آنلاین', 'erfansanat' ),
				'call'   => __( 'استعلامی', 'erfansanat' ),
			);
			foreach ( $es_modes as $es_mode => $es_mode_label ) :
				?>
				<label class="cursor-pointer">
					<input type="radio" name="es-purchase-mode-<?php echo esc_attr( $uid ); ?>" data-product-purchase-mode="<?php echo esc_attr( $es_mode ); ?>" class="peer sr-only" <?php checked( 'all', $es_mode ); ?> />
					<span class="block rounded-lg py-2 text-center text-[11.5px] font-extrabold transition-all text-slate-600 hover:text-slate-900 peer-checked:bg-emerald-700 peer-checked:text-white peer-checked:shadow-2xs"><?php echo esc_html( $es_mode_label ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="border-t border-slate-100 pt-4">
		<div class="mb-2 flex items-center justify-between text-[12px] font-extrabold text-slate-800">
			<span><?php esc_html_e( 'سقف بودجه (فیلتر قیمت)', 'erfansanat' ); ?></span>
			<span class="text-[11.5px] font-bold text-emerald-700" data-product-price-label="true"><?php esc_html_e( 'همه قیمت‌ها', 'erfansanat' ); ?></span>
		</div>
		<input type="range" min="500000" max="15000000" step="500000" value="15000000" data-product-price-range="true" class="h-2 w-full cursor-pointer appearance-none rounded-lg bg-emerald-100 accent-emerald-600" />
		<div class="mt-1 flex justify-between text-[10.5px] font-semibold text-slate-400">
			<span><?php esc_html_e( '۵۰۰ هزار تومان', 'erfansanat' ); ?></span>
			<span><?php esc_html_e( '۱۵ میلیون تومان+', 'erfansanat' ); ?></span>
		</div>
	</div>
	<div class="grid grid-cols-2 gap-2.5 border-t border-slate-100 pt-4">
		<div>
			<span class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'درجه حفاظت (IP)', 'erfansanat' ); ?></span>
			<div class="space-y-1.5">
				<?php
				// main.js reads these as checkboxes carrying the IP value.
				$es_ips = array( 'IP68' => 'IP68 (ضد غوطه‌وری)', 'IP67' => 'IP67 (ضدآب کامل)', 'IP65' => 'IP65 (فضای باز)' );
				foreach ( $es_ips as $es_ip => $es_ip_label ) :
					?>
					<label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-[11.5px] font-bold text-slate-700 transition hover:border-emerald-300">
						<input type="checkbox" data-product-ip-filter="<?php echo esc_attr( $es_ip ); ?>" class="h-3.5 w-3.5 accent-emerald-600" />
						<span><?php echo esc_html( $es_ip_label ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
		<div>
			<span class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'ولتاژ ورودی', 'erfansanat' ); ?></span>
			<div class="space-y-1.5">
				<?php
				$es_volts = array( '24V' => '۲۴ ولت DC (ایمن)', '220V' => '۲۲۰ ولت شهری' );
				foreach ( $es_volts as $es_volt => $es_volt_label ) :
					?>
					<label class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1.5 text-[11.5px] font-bold text-slate-700 transition hover:border-emerald-300">
						<input type="checkbox" data-product-voltage-filter="<?php echo esc_attr( $es_volt ); ?>" class="h-3.5 w-3.5 accent-emerald-600" />
						<span><?php echo esc_html( $es_volt_label ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<div class="border-t border-slate-100 pt-4">
		<div class="mb-3 flex items-center gap-2 text-[12.5px] font-extrabold text-slate-800">
			<svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>
			<span><?php esc_html_e( 'دسته‌بندی محصولات (product_cat)', 'erfansanat' ); ?></span>
		</div>
		<nav aria-label="<?php esc_attr_e( 'آرشیو دسته‌بندی محصولات', 'erfansanat' ); ?>" class="space-y-1.5">
			<?php
			$all_active = ( 'همه' === $active );
			$all_cls = $all_active
				? 'flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all bg-emerald-700 text-white shadow-sm shadow-emerald-700/25'
				: 'flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800';
			$all_badge = $all_active ? 'rounded-md px-2 py-0.5 text-[11px] font-extrabold bg-white/20 text-white' : 'rounded-md px-2 py-0.5 text-[11px] font-extrabold bg-slate-100 text-slate-500';
			$all_dot = $all_active ? 'h-2 w-2 rounded-full bg-white' : 'h-2 w-2 shrink-0 rounded-full bg-emerald-500/70';
			$all_count = array_sum( wp_list_pluck( $es_top_cats, 'count' ) );
			?>
			<a href="<?php echo esc_url( $es_shop_url ); ?>" class="<?php echo esc_attr( $all_cls ); ?>">
				<span class="flex items-center gap-2"><span class="<?php echo esc_attr( $all_dot ); ?>"></span><span><?php esc_html_e( 'همه محصولات تولیدی', 'erfansanat' ); ?></span></span>
				<span class="<?php echo esc_attr( $all_badge ); ?>"><?php echo esc_html( $es_fa( $all_count ) ); ?></span>
			</a>
			<?php foreach ( $es_top_cats as $es_cat ) : ?>
				<div class="space-y-1"><?php $es_cat_row( $es_cat, $active ); ?></div>
			<?php endforeach; ?>
		</nav>
	</div>
	<?php
};
?>

<?php if ( $es_show_hero ) : ?>
<section class="relative overflow-hidden bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-36"><div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(1000px 500px at 78% -6%, rgba(0,177,64,0.1), transparent 60%)"></div><div class="light-dot-grid absolute inset-0 opacity-50"></div><div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-emerald-200/50 blur-[110px]"></div><div class="absolute -left-28 bottom-10 h-72 w-72 rounded-full bg-teal-100/60 blur-[110px]"></div><div class="absolute left-[10%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/25 to-transparent"></div></div><div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8"><nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800"><?php echo esc_html( $es_breadcrumb ); ?></span></span></nav><div class="grid items-center gap-12 pb-8 pt-10 lg:grid-cols-[1.02fr_0.98fr] lg:gap-14 lg:pb-12 lg:pt-12"><div><div class="mb-6 inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-white py-1.5 pl-5 pr-1.5 shadow-sm"><span class="grid h-8 w-8 place-items-center rounded-full bg-emerald-600 text-white shadow-md shadow-emerald-600/25"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck h-4 w-4" aria-hidden="true"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg></span><span class="text-[13px] font-extrabold text-emerald-800"><?php echo esc_html( $es_hero_badge ); ?></span></div><h1 class="text-[2.3rem] font-black leading-[1.28] tracking-[-0.015em] text-slate-950 sm:text-[3rem] lg:text-[3.4rem] lg:leading-[1.22]">فروشگاه محصولات <br/><span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent"><?php echo esc_html( $es_heading_gradient ); ?></span></h1><p class="mt-6 max-w-[36rem] text-[15.5px] font-medium leading-9 text-slate-600 lg:text-[16px]"><?php echo esc_html( $es_hero_subtitle ); ?></p><div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4"><div class="flex items-center gap-2.5 rounded-2xl border border-emerald-100 bg-white px-3 py-2.5 shadow-xs"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg></span><span class="text-[11.5px] font-extrabold leading-4 text-slate-700">استاندارد IP67 / IP68</span></div><div class="flex items-center gap-2.5 rounded-2xl border border-emerald-100 bg-white px-3 py-2.5 shadow-xs"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg></span><span class="text-[11.5px] font-extrabold leading-4 text-slate-700">دارای کد GS1 و ستاد</span></div><div class="flex items-center gap-2.5 rounded-2xl border border-emerald-100 bg-white px-3 py-2.5 shadow-xs"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg></span><span class="text-[11.5px] font-extrabold leading-4 text-slate-700">۲۴ ماه گارانتی تعویض</span></div><div class="flex items-center gap-2.5 rounded-2xl border border-emerald-100 bg-white px-3 py-2.5 shadow-xs"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg></span><span class="text-[11.5px] font-extrabold leading-4 text-slate-700">۵ سال خدمات پس از فروش</span></div></div></div><div class="relative mx-auto w-full max-w-[30rem]"><div aria-hidden="true" class="absolute left-1/2 top-1/2 h-[70%] w-[70%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-200/50 blur-[80px]"></div><div class="relative overflow-hidden rounded-[2.5rem] border-4 border-white shadow-[0_40px_80px_-30px_rgba(0,80,40,0.35)]"><img src="<?php echo esc_url( $es_hero_image ); ?>" alt="" class="h-[22rem] w-full object-cover sm:h-[25rem] lg:h-[28rem]"/><div class="absolute inset-0 bg-gradient-to-t from-emerald-950/45 via-transparent to-transparent"></div><div class="absolute bottom-4 inset-x-4 flex items-center justify-between rounded-2xl border border-white/50 bg-white/85 px-4 py-3 backdrop-blur-md"><div class="flex flex-col"><span class="text-[11px] font-bold text-slate-500">پیشنهاد ویژه کارخانه</span><span class="text-[14px] font-black text-slate-900">استعلام قیمت روز + مشاوره رایگان</span></div><span class="rounded-xl bg-emerald-700 px-3.5 py-2 text-[12.5px] font-black text-white shadow-lg">تماس بگیرید</span></div></div><span class="absolute -top-4 right-8 flex items-center gap-2 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 shadow-xl"><span class="relative flex h-2.5 w-2.5"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-500 opacity-70"></span><span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-600"></span></span><span class="text-[12.5px] font-extrabold text-slate-900">موجود در انبار کارخانه</span></span><span class="absolute -bottom-5 left-6 flex items-center gap-2.5 rounded-2xl bg-emerald-800 px-4 py-3 text-white shadow-[0_20px_40px_-16px_rgba(0,80,40,0.5)]"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check h-5 w-5 text-amber-300" aria-hidden="true"><path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z"></path><path d="m9 12 2 2 4-4"></path></svg><span class="flex flex-col leading-tight"><span class="text-[13px] font-black">۲۴ ماه گارانتی</span><span class="text-[10.5px] font-medium text-emerald-200">۵ سال خدمات پس از فروش</span></span></span></div></div></div><div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div></section>
<?php endif; ?>

<section id="archive" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-12 lg:px-8 lg:py-16" data-products-archive="true" data-initial-category="<?php echo esc_attr( $es_initial ); ?>" data-items-per-page="<?php echo (int) $es_per_page; ?>">
	<div class="grid items-start gap-8 lg:grid-cols-12">
		<aside aria-label="<?php esc_attr_e( 'فیلتر و دسته‌بندی محصولات', 'erfansanat' ); ?>" class="hidden lg:col-span-4 lg:block xl:col-span-3">
			<div class="sticky top-28 space-y-5 rounded-[1.75rem] border border-slate-200/90 bg-white p-5 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.10)]">
				<div class="flex items-center justify-between border-b border-slate-100 pb-4">
					<div class="flex items-center gap-2.5">
						<span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
							<svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/></svg>
						</span>
						<div>
							<h2 class="text-[14.5px] font-black text-slate-900"><?php esc_html_e( 'فیلترهای هوشمند فروشگاه', 'erfansanat' ); ?></h2>
							<p class="text-[11.5px] font-semibold text-slate-500"><span data-product-count="true"><?php echo esc_html( $es_fa( $es_total ) ); ?></span> <?php esc_html_e( 'محصول منطبق', 'erfansanat' ); ?></p>
						</div>
					</div>
				</div>
				<?php $es_filter_controls( 'product-sidebar', $es_initial ); ?>
				<?php get_template_part( 'template-parts/archive/product-filters' ); // P08-T006: AJAX category filters + clear. ?>
			</div>
		</aside>

		<div class="lg:col-span-8 xl:col-span-9">
			<?php get_template_part( 'template-parts/archive/toolbar', null, array( 'total' => $es_total, 'toolbar_title' => $es_toolbar_title ) ); // P06-T021. ?>

			<div data-product-grid="true" class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3">
				<?php if ( $es_query && $es_query->have_posts() ) : ?>
					<?php while ( $es_query->have_posts() ) : $es_query->the_post(); ?>
						<?php get_template_part( 'woocommerce/content', 'product' ); // P06-T017. ?>
					<?php endwhile; wp_reset_postdata(); ?>
				<?php endif; ?>
			</div>

			<div data-product-empty="true" class="<?php echo $es_total ? 'hidden' : 'block'; ?> rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 py-16 text-center">
				<p class="text-[14.5px] font-extrabold text-slate-700"><?php esc_html_e( 'محصولی با این مشخصات یافت نشد.', 'erfansanat' ); ?></p>
				<p class="mt-1.5 text-[12.5px] text-slate-500"><?php esc_html_e( 'عبارت جستجو را تغییر دهید یا فیلترهای فعال را پاک کنید.', 'erfansanat' ); ?></p>
				<a href="<?php echo esc_url( $es_shop_url ); ?>" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-[12.5px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'مشاهده همه محصولات', 'erfansanat' ); ?></a>
			</div>

			<?php get_template_part( 'template-parts/archive/pagination', null, array( 'total' => $es_total, 'per_page' => $es_per_page ) ); // P06-T021. ?>
		</div>
	</div>

	<div data-archive-drawer="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'فیلتر و دسته‌بندی محصولات', 'erfansanat' ); ?>" class="hidden fixed inset-0 z-50 items-end justify-center bg-slate-950/60 p-0 backdrop-blur-xs sm:items-center sm:p-4 lg:hidden">
		<div data-archive-drawer-backdrop="true" class="absolute inset-0"></div>
		<div class="relative z-10 flex max-h-[86vh] w-full max-w-lg flex-col overflow-hidden rounded-t-[2rem] bg-white shadow-2xl sm:rounded-[2rem]">
			<div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
				<div class="flex items-center gap-2.5">
					<span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700">
						<svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="21" x2="14" y1="4" y2="4"/><line x1="10" x2="3" y1="4" y2="4"/><line x1="21" x2="12" y1="12" y2="12"/><line x1="8" x2="3" y1="12" y2="12"/><line x1="21" x2="16" y1="20" y2="20"/><line x1="12" x2="3" y1="20" y2="20"/><line x1="14" x2="14" y1="2" y2="6"/><line x1="8" x2="8" y1="10" y2="14"/><line x1="16" x2="16" y1="18" y2="22"/></svg>
					</span>
					<h2 class="text-[14.5px] font-black text-slate-900"><?php esc_html_e( 'فیلتر و دسته‌بندی', 'erfansanat' ); ?></h2>
				</div>
				<button type="button" data-archive-drawer-close="true" aria-label="<?php esc_attr_e( 'بستن فیلترها', 'erfansanat' ); ?>" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50">
					<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
				</button>
			</div>
			<div class="space-y-5 overflow-y-auto px-5 py-5">
				<?php $es_filter_controls( 'product-drawer', $es_initial ); ?>
				<?php get_template_part( 'template-parts/archive/product-filters' ); // P08-T006 (drawer copy). ?>
			</div>
			<div class="border-t border-slate-100 px-5 py-4">
				<button type="button" data-archive-drawer-close="true" class="w-full rounded-xl bg-emerald-700 px-4 py-3 text-[13px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'اعمال فیلترها', 'erfansanat' ); ?></button>
			</div>
		</div>
	</div>
</section>
