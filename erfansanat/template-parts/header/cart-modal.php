<?php
/**
 * Cart / proforma modal shell.
 *
 * Reads the real WooCommerce cart when present (count/total/rows); otherwise
 * renders the genuine empty state (AC-02/AC-03). data-cart-modal-* attributes
 * mirror the reference (AC-01). Add/remove/qty AJAX wiring arrives in Phase 6.
 * The cart payload is filterable (es_cart_modal_data) for integrations/tests.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;

$es_cart = array(
	'items'      => array(),
	'count'      => 0,
	'total_html' => '',
);

if ( function_exists( 'WC' ) && WC()->cart ) {
	$es_cart['count']      = (int) WC()->cart->get_cart_contents_count();
	$es_cart['total_html'] = (string) WC()->cart->get_cart_total();
	foreach ( WC()->cart->get_cart() as $es_key => $es_item ) {
		$_product = isset( $es_item['data'] ) ? $es_item['data'] : null;
		if ( ! $_product ) {
			continue;
		}
		$es_cart['items'][] = array(
			'key'           => (string) $es_key,
			'title'         => $_product->get_name(),
			'permalink'     => get_permalink( $_product->get_id() ),
			'thumb'         => get_the_post_thumbnail_url( $_product->get_id(), 'thumbnail' ) ?: ES_ASSETS_URI . 'images/hero.jpg',
			'qty'           => (int) $es_item['quantity'],
			'price_html'    => (string) WC()->cart->get_product_price( $_product ),
			'row_total_html'=> (string) WC()->cart->get_product_subtotal( $_product, $es_item['quantity'] ),
		);
	}
}

/** Filter the cart modal payload (items/count/total_html). */
$es_cart = apply_filters( 'es_cart_modal_data', $es_cart );

$es_is_empty   = empty( $es_cart['items'] );
$es_count      = (int) $es_cart['count'];
$es_total_html = ( '' !== $es_cart['total_html'] ) ? $es_cart['total_html'] : number_format_i18n( 0 ) . ' تومان';

$es_close_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-4 w-4" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>';
$es_bag_icon   = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag h-5 w-5" aria-hidden="true"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg>';
$es_minus      = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-minus h-3.5 w-3.5" aria-hidden="true"><path d="M5 12h14"></path></svg>';
$es_plus       = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-3.5 w-3.5" aria-hidden="true"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>';
$es_trash      = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash-2 h-3.5 w-3.5" aria-hidden="true"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>';

// Quick-add suggestions: recent real products.
$es_quick = array();
if ( post_type_exists( 'product' ) ) {
	$es_quick = get_posts( array( 'post_type' => 'product', 'post_status' => 'publish', 'numberposts' => 3 ) );
}
?>
<div data-cart-modal="true" data-cart-real="true" data-cart-ajax-url="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" data-cart-nonce="<?php echo esc_attr( wp_create_nonce( \ErfanSanat\Theme\Core\CartFragments::NONCE_ACTION ) ); ?>" aria-hidden="true" class="fixed inset-0 z-[95] items-center justify-center p-3 sm:p-5 hidden">
	<div data-cart-modal-backdrop="true" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"></div>
	<div role="dialog" aria-modal="true" aria-label="سبد خرید و پیش‌فاکتور" class="relative z-10 flex max-h-[90dvh] w-full max-w-[680px] flex-col overflow-hidden rounded-[1.75rem] border border-emerald-100 bg-white shadow-[0_32px_80px_-20px_rgba(15,23,42,0.4)] sm:rounded-[2rem]">

		<div class="flex shrink-0 items-center justify-between border-b border-slate-100 bg-emerald-50/50 px-5 py-4 sm:px-6">
			<div class="flex items-center gap-3">
				<div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-emerald-700 text-white shadow-xs"><?php echo $es_bag_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<div>
					<div class="flex items-center gap-2">
						<h2 class="text-sm font-black text-slate-900 sm:text-base">سبد خرید و لیست پیش‌فاکتور</h2>
						<span data-cart-modal-count="true" class="rounded-lg bg-emerald-100 px-2 py-0.5 text-[11px] font-black text-emerald-900 tabular-nums"><?php echo esc_html( number_format_i18n( $es_count ) . ' کالا' ); ?></span>
					</div>
					<p class="mt-0.5 text-[11px] font-medium text-slate-500">امکان تغییر تعداد، حذف یا افزودن محصولات و صدور پیش‌فاکتور رسمی کارخانه</p>
				</div>
			</div>
			<button type="button" data-cart-modal-close="true" aria-label="بستن مودال سبد خرید" class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-slate-200 bg-white text-slate-600 transition-colors hover:bg-rose-50 hover:text-rose-600 cursor-pointer"><?php echo $es_close_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</div>

		<div class="min-h-0 flex-1 overflow-y-auto p-4 sm:p-6 space-y-5">
			<div data-cart-modal-items-list="true" class="space-y-3<?php echo $es_is_empty ? ' hidden' : ''; ?>">
				<?php foreach ( $es_cart['items'] as $es_item ) : ?>
					<div data-cart-modal-row="<?php echo esc_attr( $es_item['key'] ); ?>" data-current-qty="<?php echo esc_attr( (string) $es_item['qty'] ); ?>" class="flex flex-col items-center justify-between gap-3 rounded-2xl border border-slate-200/80 bg-slate-50/60 p-3 transition-all hover:border-emerald-200 hover:bg-white sm:flex-row sm:gap-4 sm:p-3.5">
						<div class="flex w-full items-center gap-3 sm:w-auto sm:min-w-0 sm:flex-1">
							<a href="<?php echo esc_url( $es_item['permalink'] ); ?>" class="h-16 w-16 shrink-0 overflow-hidden rounded-xl border border-slate-200/80 bg-white"><img src="<?php echo esc_url( $es_item['thumb'] ); ?>" alt="<?php echo esc_attr( $es_item['title'] ); ?>" class="h-full w-full object-cover"/></a>
							<div class="min-w-0 flex-1">
								<a href="<?php echo esc_url( $es_item['permalink'] ); ?>" class="truncate block text-xs font-black text-slate-900 hover:text-emerald-700 sm:text-[13.5px]"><?php echo esc_html( $es_item['title'] ); ?></a>
								<div class="mt-1 flex items-center gap-2 text-[11px]"><span class="font-semibold text-slate-400">فی واحد:</span><span class="font-bold text-slate-700 tabular-nums"><?php echo wp_kses_post( $es_item['price_html'] ); ?></span></div>
							</div>
						</div>
						<div class="flex w-full items-center justify-between gap-3 sm:w-auto sm:justify-end">
							<div class="inline-flex items-center rounded-xl border border-slate-200 bg-white p-0.5 shadow-2xs">
								<button type="button" data-cart-modal-dec="<?php echo esc_attr( $es_item['key'] ); ?>" aria-label="کاهش تعداد" class="grid h-7 w-7 place-items-center rounded-lg text-slate-600 transition-colors hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer"><?php echo $es_minus; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
								<span data-cart-modal-qty="<?php echo esc_attr( $es_item['key'] ); ?>" class="min-w-9 px-1.5 text-center font-mono text-xs font-black text-slate-900 tabular-nums"><?php echo esc_html( number_format_i18n( $es_item['qty'] ) ); ?></span>
								<button type="button" data-cart-modal-inc="<?php echo esc_attr( $es_item['key'] ); ?>" aria-label="افزایش تعداد" class="grid h-7 w-7 place-items-center rounded-lg text-slate-600 transition-colors hover:bg-emerald-50 hover:text-emerald-700 cursor-pointer"><?php echo $es_plus; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
							</div>
							<div class="min-w-[110px] text-left"><div data-cart-modal-row-total="<?php echo esc_attr( $es_item['key'] ); ?>" class="text-xs font-black text-slate-900 tabular-nums"><?php echo wp_kses_post( $es_item['row_total_html'] ); ?></div></div>
							<button type="button" data-cart-modal-remove="<?php echo esc_attr( $es_item['key'] ); ?>" aria-label="حذف از سبد" class="grid h-7 w-7 shrink-0 place-items-center rounded-lg border border-slate-200 text-slate-400 transition-colors hover:bg-rose-600 hover:text-white cursor-pointer"><?php echo $es_trash; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<div data-cart-modal-empty="true" class="<?php echo $es_is_empty ? 'flex' : 'hidden'; ?> flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50/70 px-4 py-8 text-center">
				<p class="mt-2.5 text-sm font-black text-slate-800">سبد خرید شما در حال حاضر خالی است</p>
				<p class="mt-1 text-xs font-medium text-slate-500">می‌توانید از فروشگاه محصول مورد نظر خود را اضافه کنید و پیش‌فاکتور رسمی دریافت نمایید.</p>
				<a href="<?php echo esc_url( Menus::page_url( 'shop' ) ); ?>" class="mt-3.5 inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-extrabold text-white hover:bg-emerald-800"><span>مشاهده فروشگاه محصولات</span></a>
			</div>

			<?php if ( ! empty( $es_quick ) ) : ?>
				<div class="rounded-2xl border border-emerald-100/90 bg-emerald-50/35 p-3.5 sm:p-4">
					<div class="mb-2.5 flex items-center justify-between">
						<span class="text-xs font-black text-emerald-950">افزودن سریع محصولات پرکاربرد به سبد خرید:</span>
						<a href="<?php echo esc_url( Menus::page_url( 'shop' ) ); ?>" class="text-[11px] font-extrabold text-emerald-700 hover:underline">مشاهده همه محصولات فروشگاه ←</a>
					</div>
					<div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
						<?php foreach ( $es_quick as $es_post ) : ?>
							<div class="flex items-center justify-between gap-2 rounded-xl border border-white bg-white/95 p-2 shadow-2xs">
								<div class="flex min-w-0 items-center gap-2">
									<img src="<?php echo esc_url( get_the_post_thumbnail_url( $es_post, 'thumbnail' ) ?: ES_ASSETS_URI . 'images/hero.jpg' ); ?>" alt="<?php echo esc_attr( get_the_title( $es_post ) ); ?>" class="h-9 w-9 shrink-0 rounded-lg object-cover"/>
									<div class="min-w-0"><div class="truncate text-[11px] font-extrabold text-slate-800"><?php echo esc_html( get_the_title( $es_post ) ); ?></div></div>
								</div>
								<button type="button" data-cart-modal-add="<?php echo esc_attr( (string) $es_post->ID ); ?>" class="inline-flex shrink-0 items-center gap-1 rounded-lg bg-emerald-700 px-2.5 py-1.5 text-[10.5px] font-extrabold text-white transition-colors hover:bg-emerald-800 cursor-pointer"><span>افزودن</span></button>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<div class="shrink-0 border-t border-slate-100 bg-slate-50 px-5 py-4 sm:px-6">
			<div class="mb-3.5 flex items-center justify-between">
				<div>
					<span class="text-xs font-bold text-slate-500">جمع کل اقلام انتخابی:</span>
					<span class="mr-2 text-[11px] font-semibold text-slate-400">(امکان صدور فاکتور رسمی در مرحله بعد)</span>
				</div>
				<div data-cart-modal-total="true" class="text-base font-black text-emerald-800 sm:text-lg tabular-nums"><?php echo wp_kses_post( $es_total_html ); ?></div>
			</div>
			<div class="grid grid-cols-1 gap-2.5 sm:grid-cols-12">
				<a href="<?php echo esc_url( Menus::page_url( 'checkout' ) ); ?>" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 text-xs font-black text-white shadow-sm transition-colors hover:bg-emerald-800 sm:col-span-7 sm:text-sm"><span>تکمیل خرید و صدور پیش‌فاکتور</span></a>
				<a href="<?php echo esc_url( Menus::page_url( 'cart' ) ); ?>" class="inline-flex h-11 items-center justify-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 text-xs font-extrabold text-slate-700 transition-colors hover:border-emerald-300 hover:text-emerald-800 sm:col-span-5"><span>نمای کامل سبد خرید</span></a>
			</div>
		</div>

	</div>
</div>
