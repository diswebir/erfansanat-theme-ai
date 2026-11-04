<?php
/**
 * Single product content shell (P06-T002). Reference layout: breadcrumb +
 * title hero, gallery/summary grid, then the product-details tab container
 * (desc/specs/reviews) — AC-01. Standard Woo hooks fire at the canonical
 * points (AC-02); each region uses a hook-with-fallback so later Phase-6
 * tasks (gallery T003, summary T004-T008, specs T009-T010, reviews T014,
 * related T012/T013/T015) can replace the shell fallback by hooking in.
 * Invalid products return early; out-of-stock products render safely (AC-03).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : false;
if ( ! $es_product ) {
	return; // AC-03: never fatal on an invalid product.
}

$es_id        = $es_product->get_id();
$es_title     = get_the_title( $es_id );
$es_excerpt   = get_the_excerpt( $es_id );
$es_cats      = get_the_terms( $es_id, 'product_cat' );
$es_primary   = ( $es_cats && ! is_wp_error( $es_cats ) ) ? $es_cats[0] : null;
$es_sku       = $es_product->get_sku();
$es_rating    = $es_product->get_average_rating();
$es_in_stock  = $es_product->is_in_stock();
$es_true_sale = (string) get_post_meta( $es_id, 'true_sale', true );
$es_is_online = ( 'true' === $es_true_sale );
$es_img       = get_the_post_thumbnail_url( $es_id, 'large' );
if ( ! $es_img ) {
	$es_img = ES_ASSETS_URI . 'images/hero.jpg';
}

$es_fa_digits = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_fa        = static function ( $v ) use ( $es_fa_digits ) { return strtr( (string) $v, $es_fa_digits ); };
$es_rating_fa = $es_fa( number_format( (float) $es_rating, 2 ) );

$es_shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
$es_home_url = home_url( '/' );
$es_cat_url  = $es_primary ? get_term_link( $es_primary ) : '';

/**
 * Hook: woocommerce_before_single_product.
 */
do_action( 'woocommerce_before_single_product' );
?>
<main class="bg-slate-50/50 pb-28 lg:pb-0">

	<!-- Hero: breadcrumb + category badge + title + subtitle (AC-01 order). -->
	<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-45"></div>
			<div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
			<nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur-md">
				<a href="<?php echo esc_url( $es_home_url ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">
					<span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>صفحه اصلی
				</a>
				<span class="flex min-w-0 items-center gap-1.5">
					<?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<a href="<?php echo esc_url( $es_shop_url ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700">فروشگاه محصولات</a>
				</span>
				<?php if ( $es_primary && ! is_wp_error( $es_cat_url ) ) : ?>
					<span class="flex min-w-0 items-center gap-1.5">
						<?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a href="<?php echo esc_url( $es_cat_url ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php echo esc_html( $es_primary->name ); ?></a>
					</span>
				<?php endif; ?>
				<span class="flex min-w-0 items-center gap-1.5">
					<?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span class="max-w-[16rem] truncate rounded-lg bg-emerald-50 px-2 py-0.5 font-extrabold text-emerald-800"><?php echo esc_html( $es_title ); ?></span>
				</span>
			</nav>
			<div class="pt-6">
				<?php if ( $es_primary ) : ?>
					<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs">
						<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
						<span><?php echo esc_html( $es_primary->name ); ?></span>
					</div>
				<?php endif; ?>
				<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $es_title ); ?></h1>
				<?php if ( '' !== trim( $es_excerpt ) ) : ?>
					<p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]"><?php echo esc_html( $es_excerpt ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<!-- Gallery + summary grid. -->
	<section class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
		<div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-10">
			<div class="space-y-4 lg:col-span-7">
				<?php
				ob_start();
				/** Hook: woocommerce_before_single_product_summary (gallery — T003). */
				do_action( 'woocommerce_before_single_product_summary' );
				$es_gallery = (string) ob_get_clean();
				if ( '' !== trim( $es_gallery ) ) {
					echo $es_gallery; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					// Shell fallback: basic gallery root + main image (T003 enriches).
					?>
					<div data-gallery-root="true" class="space-y-3.5">
						<div class="group relative overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-900 shadow-sm">
							<img src="<?php echo esc_url( $es_img ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" data-gallery-main-img="true" class="h-[16rem] w-full cursor-zoom-in object-cover sm:h-[24rem] lg:h-[27rem]"/>
							<div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-slate-950/55 via-transparent to-transparent"></div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
			<div class="lg:col-span-5 lg:sticky lg:top-28">
				<?php
				ob_start();
				/** Hook: woocommerce_single_product_summary (title/price/cart — T004-T008). */
				do_action( 'woocommerce_single_product_summary' );
				$es_summary = (string) ob_get_clean();
				if ( '' !== trim( $es_summary ) ) {
					echo $es_summary; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					// Shell fallback: basic summary card (T004-T008 enrich).
					?>
					<div class="overflow-hidden rounded-3xl border border-emerald-200/80 bg-white shadow-[0_20px_50px_-24px_rgba(5,150,105,0.16)]">
						<div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-emerald-50/60 px-5 py-3.5 sm:px-6">
							<div class="flex items-center gap-2">
								<span class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-500/20"></span>
								<span class="text-xs font-extrabold text-emerald-950">تولید سفارشی کارخانه</span>
							</div>
							<div class="flex items-center gap-2 text-[11.5px] font-bold text-slate-600">
								<?php if ( $es_rating > 0 ) : ?>
									<span class="inline-flex items-center gap-1 rounded-full bg-white px-2.5 py-0.5 text-amber-600 ring-1 ring-amber-200"><?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $es_rating_fa ); ?></span></span>
								<?php endif; ?>
								<?php if ( '' !== $es_sku ) : ?>
									<span dir="ltr" class="rounded-md bg-white px-2 py-0.5 font-mono text-[11px] font-bold text-slate-600 ring-1 ring-slate-200/80"><?php echo esc_html( $es_sku ); ?></span>
								<?php endif; ?>
							</div>
						</div>
						<div class="space-y-5 p-5 sm:p-6">
							<div>
								<div class="flex flex-wrap items-baseline justify-between gap-2">
									<?php if ( $es_is_online && '' !== $es_product->get_price() ) : ?>
										<span class="text-2xl font-black text-emerald-800"><?php echo wp_kses_post( $es_product->get_price_html() ); ?></span>
									<?php else : ?>
										<span class="text-2xl font-black text-emerald-800">استعلام قیمت روز</span>
									<?php endif; ?>
								</div>
								<p class="mt-1 text-[11.5px] leading-5 text-slate-500">فروش مستقیم از کارخانه عرفان صنعت اصفهان بدون واسطه (همراه با فاکتور رسمی)</p>
								<?php if ( ! $es_in_stock ) : ?>
									<p class="mt-2 text-xs font-bold text-amber-700">این محصول در حال حاضر ناموجود است؛ برای استعلام موجودی تماس بگیرید.</p>
								<?php endif; ?>
							</div>
							<div class="space-y-2.5 pt-1">
								<a href="<?php echo esc_url( home_url( '/checkout/' ) ); ?>" class="group relative inline-flex w-full items-center justify-center gap-2.5 rounded-2xl bg-gradient-to-l from-emerald-700 to-emerald-600 px-5 py-3.5 text-sm font-extrabold text-white shadow-sm transition-colors hover:from-emerald-800 hover:to-emerald-700">
									<?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<span>درخواست پیش‌فاکتور رسمی و استعلام قیمت</span>
								</a>
								<div class="grid grid-cols-2 gap-2.5">
									<a href="<?php echo esc_url( 'tel:03191091011' ); ?>" class="flex items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50/80 px-3 py-2.5 text-xs font-extrabold text-emerald-800 transition-colors hover:bg-emerald-100"><?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>مشاوره: ۰۳۱ - ۹۱۰۹۱۰۱۱</span></a>
									<a href="<?php echo esc_url( home_url( '/cart/' ) ); ?>" class="flex items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs font-extrabold text-slate-700 transition-colors hover:bg-slate-100"><?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>مشاهده سبد خرید</span></a>
								</div>
							</div>
						</div>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</section>

	<!-- Product details tabs (desc/specs/reviews) — AC-01. -->
	<section id="product-details" data-prod-tabs-container="true" class="mx-auto max-w-6xl scroll-mt-28 px-5 pb-14 lg:px-8">
		<div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs">
			<div class="flex gap-2 overflow-x-auto no-scrollbar border-b border-slate-100 bg-slate-50/70 p-3 sm:flex-wrap sm:px-6">
				<button type="button" data-prod-tab-btn="desc" class="flex shrink-0 items-center gap-2 rounded-2xl bg-emerald-800 px-3.5 py-2 text-xs font-extrabold text-white shadow-sm sm:px-4 sm:py-2.5 sm:text-[13px]">
					<span data-prod-tab-icon="desc" class="text-white"><?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span>معرفی و کاربردها</span>
				</button>
				<button type="button" data-prod-tab-btn="specs" class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 text-xs font-extrabold text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900 sm:px-4 sm:py-2.5 sm:text-[13px]">
					<span data-prod-tab-icon="specs" class="text-emerald-700"><?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text h-5 w-5 shrink-0 transition-transform duration-300 group-hover:scale-110" aria-hidden="true"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="M10 9H8"></path><path d="M16 13H8"></path><path d="M16 17H8"></path></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span>مشخصات فنی و شناسه‌ها</span>
				</button>
				<button type="button" data-prod-tab-btn="reviews" class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 text-xs font-extrabold text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900 sm:px-4 sm:py-2.5 sm:text-[13px]">
					<span data-prod-tab-icon="reviews" class="text-emerald-700"><?php echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span>نظرات کاربران</span>
				</button>
			</div>
			<div class="p-6 sm:p-8 lg:p-10">
				<div data-prod-tab-panel="desc" class="block">
					<div class="prose-es max-w-none text-sm leading-8 text-slate-600"><?php echo apply_filters( 'the_content', get_post_field( 'post_content', $es_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php /** Hook: es_product_usage (applications — T011). */ do_action( 'es_product_usage', $es_product ); ?>
				</div>
				<div data-prod-tab-panel="specs" class="hidden">
					<?php /** Hook: es_product_specs_panel (specs table + identifiers — T009/T010/T005). */ do_action( 'es_product_specs_panel', $es_product ); ?>
				</div>
				<div data-prod-tab-panel="reviews" class="hidden">
					<?php /** Hook: es_product_reviews_panel (reviews — T014). */ do_action( 'es_product_reviews_panel', $es_product ); ?>
				</div>
			</div>
		</div>
	</section>

	<?php
	/** Hook: woocommerce_after_single_product_summary (tabs/upsells — standard Woo). */
	do_action( 'woocommerce_after_single_product_summary' );
	/** Hook: es_product_related_projects (T012). */
	do_action( 'es_product_related_projects', $es_product );
	/** Hook: es_product_faq (T013). */
	do_action( 'es_product_faq', $es_product );
	/** Hook: es_product_related_products (T015). */
	do_action( 'es_product_related_products', $es_product );
	?>
</main>
<?php
/** Hook: woocommerce_after_single_product. */
do_action( 'woocommerce_after_single_product' );
