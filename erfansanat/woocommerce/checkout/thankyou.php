<?php
/**
 * Order received / thank-you page (P06-T025). Shows the real order summary
 * (AC-01) and embeds the owner-only printable proforma (AC-03). WooCommerce
 * passes $order to this template; a query-var / order_id fallback is provided
 * for direct rendering and tests.
 *
 * @package ErfanSanat
 *
 * @var WC_Order|ES_Shim_Order|null $order
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_order = ( isset( $order ) && is_object( $order ) ) ? $order : null;
if ( ! $es_order ) {
	$es_order_id = get_query_var( 'order-received' );
	if ( ! $es_order_id && isset( $_GET['order_id'] ) ) {
		$es_order_id = (int) $_GET['order_id'];
	}
	if ( $es_order_id && function_exists( 'wc_get_order' ) ) {
		$es_order = wc_get_order( $es_order_id );
	} elseif ( $es_order_id && class_exists( 'ES_Shim_Order' ) ) {
		$es_order = new ES_Shim_Order( (int) $es_order_id );
	}
}

$es_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};
$es_money = static function ( $amount ) use ( $es_fa ) {
	return $es_fa( number_format( (float) $amount, 0, '.', ',' ) ) . ' تومان';
};
?>

<main class="bg-slate-50/50" data-thankyou-page="true">
	<section class="mx-auto max-w-[1000px] px-5 py-14 lg:px-8 lg:py-20">
		<?php if ( ! $es_order ) : ?>
			<div class="rounded-2xl border border-slate-200 bg-white px-6 py-12 text-center">
				<p class="text-[15px] font-extrabold text-slate-700"><?php esc_html_e( 'سفارشی برای نمایش یافت نشد.', 'erfansanat' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/shop/' ) ); ?>" class="mt-5 inline-flex rounded-xl bg-emerald-700 px-5 py-2.5 text-[12.5px] font-extrabold text-white"><?php esc_html_e( 'بازگشت به فروشگاه', 'erfansanat' ); ?></a>
			</div>
		<?php else : ?>
			<div class="mb-8 flex items-center gap-4 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-6 py-6">
				<div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-emerald-700 text-white">
					<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>
				</div>
				<div>
					<h1 class="text-xl font-black text-emerald-950"><?php esc_html_e( 'سفارش شما با موفقیت ثبت شد', 'erfansanat' ); ?></h1>
					<p class="mt-1 text-[12.5px] font-bold text-emerald-800"><?php echo esc_html( sprintf( 'شماره سفارش: %s — کارشناسان ما جهت هماهنگی ارسال تماس می‌گیرند.', $es_fa( $es_order->get_order_number() ) ) ); ?></p>
				</div>
			</div>

			<div class="mb-8 grid gap-3 rounded-2xl border border-slate-200 bg-white p-6 text-[13px] font-bold text-slate-700 sm:grid-cols-3" data-order-summary="true">
				<div><span class="block text-[11px] text-slate-400"><?php esc_html_e( 'شماره سفارش', 'erfansanat' ); ?></span><span class="text-[15px] font-black text-slate-900"><?php echo esc_html( $es_fa( $es_order->get_order_number() ) ); ?></span></div>
				<div><span class="block text-[11px] text-slate-400"><?php esc_html_e( 'تاریخ ثبت', 'erfansanat' ); ?></span><span class="text-[15px] font-black text-slate-900"><?php echo esc_html( $es_fa( substr( (string) $es_order->get_date_created(), 0, 10 ) ) ); ?></span></div>
				<div><span class="block text-[11px] text-slate-400"><?php esc_html_e( 'مبلغ کل', 'erfansanat' ); ?></span><span class="text-[15px] font-black text-emerald-800"><?php echo esc_html( $es_money( (float) $es_order->get_total() ) ); ?></span></div>
			</div>

			<?php
			get_template_part( 'template-parts/checkout/proforma', null, array( 'order' => $es_order ) );
			?>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
