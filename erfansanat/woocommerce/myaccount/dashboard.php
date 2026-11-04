<?php
/**
 * My-account dashboard (P06-T026). Welcome panel + recent orders + account
 * shortcuts. Preserves the woocommerce_my_account_dashboard hook (AC-02) and
 * renders real order data for the logged-in customer (SC-01).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_user = wp_get_current_user();
$es_fa   = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};

$es_orders = get_posts( array(
	'post_type'   => 'shop_order',
	'post_status' => 'any',
	'numberposts' => 5,
	'meta_key'    => '_customer_user',
	'meta_value'  => get_current_user_id(),
) );

$es_orders_url   = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'orders' ) : add_query_arg( 'tab', 'orders', get_permalink() );
$es_address_url  = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'edit-address' ) : add_query_arg( 'tab', 'addresses', get_permalink() );
$es_details_url  = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( 'edit-account' ) : add_query_arg( 'tab', 'details', get_permalink() );
?>

<div data-myaccount-dashboard="true" class="space-y-6">
	<?php do_action( 'woocommerce_before_my_account_dashboard' ); ?>

	<div class="rounded-[1.5rem] border border-emerald-100 bg-gradient-to-l from-emerald-50 to-white p-6">
		<h2 class="text-lg font-black text-emerald-950"><?php echo esc_html( sprintf( 'سلام، %s', $es_user->display_name ?: $es_user->user_login ) ); ?></h2>
		<p class="mt-1.5 text-[12.5px] font-bold leading-6 text-slate-600"><?php esc_html_e( 'از پیشخوان حساب خود می‌توانید سفارش‌ها را پیگیری کنید، آدرس‌های ارسال را مدیریت کنید و پیش‌فاکتورهای رسمی را دریافت نمایید.', 'erfansanat' ); ?></p>
		<div class="mt-4 flex flex-wrap gap-2.5">
			<a href="<?php echo esc_url( $es_orders_url ); ?>" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 px-4 py-2 text-[12px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'سفارش‌های من', 'erfansanat' ); ?></a>
			<a href="<?php echo esc_url( $es_address_url ); ?>" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-[12px] font-extrabold text-slate-700 transition hover:border-emerald-300"><?php esc_html_e( 'آدرس‌ها', 'erfansanat' ); ?></a>
			<a href="<?php echo esc_url( $es_details_url ); ?>" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-4 py-2 text-[12px] font-extrabold text-slate-700 transition hover:border-emerald-300"><?php esc_html_e( 'جزئیات حساب', 'erfansanat' ); ?></a>
		</div>
	</div>

	<div class="rounded-[1.5rem] border border-slate-200 bg-white p-6">
		<h3 class="mb-4 text-sm font-black text-slate-900"><?php esc_html_e( 'آخرین سفارش‌ها', 'erfansanat' ); ?></h3>
		<?php if ( empty( $es_orders ) ) : ?>
			<p class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center text-[12.5px] font-bold text-slate-500"><?php esc_html_e( 'هنوز سفارشی ثبت نکرده‌اید.', 'erfansanat' ); ?></p>
		<?php else : ?>
			<div class="space-y-2.5" data-myaccount-orders="true">
				<?php foreach ( $es_orders as $es_order_post ) : ?>
					<?php
					$es_oid    = $es_order_post->ID;
					$es_total  = (string) get_post_meta( $es_oid, '_order_total', true );
					$es_status = (string) get_post_meta( $es_oid, '_order_status', true );
					$es_status_labels = array( 'pending' => 'در انتظار پرداخت', 'on-hold' => 'در انتظار تأیید', 'processing' => 'در حال پردازش', 'completed' => 'تکمیل شده', 'cancelled' => 'لغو شده' );
					?>
					<div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
						<div class="text-[12.5px] font-extrabold text-slate-800"><?php echo esc_html( sprintf( 'سفارش #%s', $es_fa( $es_oid ) ) ); ?></div>
						<div class="text-[11.5px] font-bold text-slate-500"><?php echo esc_html( $es_fa( substr( $es_order_post->post_date, 0, 10 ) ) ); ?></div>
						<div class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11px] font-extrabold text-emerald-800"><?php echo esc_html( $es_status_labels[ $es_status ] ?? $es_status ); ?></div>
						<div class="text-[12.5px] font-black text-emerald-800 tabular-nums"><?php echo esc_html( $es_fa( number_format( (float) $es_total, 0, '.', ',' ) ) . ' تومان' ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<?php do_action( 'woocommerce_account_dashboard' ); ?>
	<?php do_action( 'woocommerce_after_my_account_dashboard' ); ?>
</div>
