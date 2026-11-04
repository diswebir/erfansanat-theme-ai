<?php
/**
 * Printable proforma invoice (P06-T025). Owner-only (AC-03): rendered only for
 * the order's customer, a shop manager, or a request carrying the valid order
 * key. Prints real order data (AC-01) in a clean RTL print layout (AC-02).
 *
 * @package ErfanSanat
 *
 * @var array $args { order: WC_Order|ES_Shim_Order, order_key?: string }
 */

defined( 'ABSPATH' ) || exit;

$es_order = isset( $args['order'] ) && is_object( $args['order'] ) ? $args['order'] : null;
if ( ! $es_order ) {
	return;
}

$es_customer_id = method_exists( $es_order, 'get_customer_id' ) ? (int) $es_order->get_customer_id() : 0;
$es_current_id  = get_current_user_id();
$es_order_key   = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : ( $args['order_key'] ?? '' );
$es_stored_key  = (string) get_post_meta( $es_order->get_id(), '_order_key', true );

// AC-03: owner, shop manager, or valid order key.
$es_is_owner = ( $es_customer_id > 0 && $es_current_id === $es_customer_id );
$es_is_admin = current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );
$es_has_key  = ( '' !== $es_order_key && '' !== $es_stored_key && hash_equals( $es_stored_key, $es_order_key ) );
$es_allowed  = $es_is_owner || $es_is_admin || $es_has_key;

if ( ! $es_allowed ) {
	?>
	<div class="rounded-2xl border border-red-200 bg-red-50 px-6 py-8 text-center" data-proforma-denied="true">
		<p class="text-[14px] font-extrabold text-red-700"><?php esc_html_e( 'شما اجازه مشاهده این پیش‌فاکتور را ندارید.', 'erfansanat' ); ?></p>
		<p class="mt-1.5 text-[12px] font-bold text-red-500"><?php esc_html_e( 'این سند تنها برای صاحب سفارش یا کاربر مجاز قابل دسترسی است.', 'erfansanat' ); ?></p>
	</div>
	<?php
	return;
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

$es_items    = method_exists( $es_order, 'get_items' ) ? $es_order->get_items() : array();
$es_subtotal = 0.0;
foreach ( $es_items as $es_item ) {
	$es_subtotal += (float) $es_item['subtotal'];
}
$es_tax     = $es_subtotal * 0.10;
$es_grand   = $es_subtotal + $es_tax;
$es_billing = method_exists( $es_order, 'get_billing_address' ) ? $es_order->get_billing_address() : array();
$es_company = get_bloginfo( 'name' );
$es_phone   = '۰۳۱-۹۱۰۹۱۰۱۱';
$es_method  = method_exists( $es_order, 'get_payment_method' ) ? $es_order->get_payment_method() : '';
$es_method_labels = array(
	'mellat'   => 'پرداخت آنلاین — درگاه بانک ملت',
	'proforma' => 'پیش‌فاکتور رسمی — حواله پایا/ساتنا',
	'credit'   => 'خرید اعتباری — سامانه ستاد',
);
?>

<div data-proforma="true" dir="rtl" class="es-proforma mx-auto max-w-[820px] rounded-[1.25rem] border border-slate-200 bg-white p-8 text-slate-900 shadow-xs print:border-0 print:p-0 print:shadow-none">
	<style>
		@media print {
			body * { visibility: hidden; }
			.es-proforma, .es-proforma * { visibility: visible; }
			.es-proforma { position: absolute; inset: 0; width: 100%; max-width: none; border: 0; box-shadow: none; }
			.es-proforma-no-print { display: none !important; }
		}
		.es-proforma { font-family: Vazirmatn, Tahoma, sans-serif; }
		.es-proforma table { border-collapse: collapse; width: 100%; }
		.es-proforma th, .es-proforma td { border: 1px solid #e2e8f0; padding: 8px 10px; text-align: right; font-size: 12.5px; }
		.es-proforma th { background: #f0fdf4; font-weight: 800; }
	</style>

	<div class="flex items-start justify-between gap-4 border-b-2 border-emerald-700 pb-5">
		<div>
			<h2 class="text-xl font-black text-emerald-900"><?php echo esc_html( $es_company ); ?></h2>
			<p class="mt-1 text-[12px] font-bold text-slate-500"><?php esc_html_e( 'پیش‌فاکتور رسمی فروش کالا و خدمات روشنایی صنعتی', 'erfansanat' ); ?></p>
			<p class="mt-0.5 text-[11.5px] font-bold text-slate-500"><?php echo esc_html( sprintf( 'تلفن: %s', $es_phone ) ); ?></p>
		</div>
		<div class="text-left text-[12px] font-bold text-slate-600">
			<div><?php esc_html_e( 'شماره سفارش:', 'erfansanat' ); ?> <span class="font-black text-emerald-800"><?php echo esc_html( $es_fa( $es_order->get_order_number() ) ); ?></span></div>
			<div class="mt-1"><?php esc_html_e( 'تاریخ:', 'erfansanat' ); ?> <span><?php echo esc_html( $es_fa( substr( (string) $es_order->get_date_created(), 0, 10 ) ) ); ?></span></div>
		</div>
	</div>

	<div class="mt-5 grid gap-2 rounded-xl bg-slate-50 p-4 text-[12.5px] font-bold text-slate-700 sm:grid-cols-2">
		<div><span class="text-slate-400"><?php esc_html_e( 'خریدار:', 'erfansanat' ); ?></span> <?php echo esc_html( $es_billing['first_name'] ?? '' ); ?></div>
		<div><span class="text-slate-400"><?php esc_html_e( 'تماس:', 'erfansanat' ); ?></span> <?php echo esc_html( $es_fa( $es_billing['phone'] ?? '' ) ); ?></div>
		<div class="sm:col-span-2"><span class="text-slate-400"><?php esc_html_e( 'آدرس:', 'erfansanat' ); ?></span> <?php echo esc_html( trim( ( $es_billing['address_1'] ?? '' ) . ' ' . ( $es_billing['city'] ?? '' ) ) ); ?></div>
		<div class="sm:col-span-2"><span class="text-slate-400"><?php esc_html_e( 'روش پرداخت:', 'erfansanat' ); ?></span> <?php echo esc_html( $es_method_labels[ $es_method ] ?? $es_method ); ?></div>
	</div>

	<table class="mt-5">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ردیف', 'erfansanat' ); ?></th>
				<th><?php esc_html_e( 'شرح کالا / خدمات', 'erfansanat' ); ?></th>
				<th><?php esc_html_e( 'تعداد', 'erfansanat' ); ?></th>
				<th><?php esc_html_e( 'فی (تومان)', 'erfansanat' ); ?></th>
				<th><?php esc_html_e( 'مبلغ (تومان)', 'erfansanat' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $es_items as $es_i => $es_item ) : ?>
				<tr>
					<td><?php echo esc_html( $es_fa( $es_i + 1 ) ); ?></td>
					<td><?php echo esc_html( $es_item['name'] ); ?></td>
					<td><?php echo esc_html( $es_fa( $es_item['qty'] ) ); ?></td>
					<td><?php echo esc_html( $es_fa( number_format( (float) $es_item['price'], 0, '.', ',' ) ) ); ?></td>
					<td><?php echo esc_html( $es_fa( number_format( (float) $es_item['subtotal'], 0, '.', ',' ) ) ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="mt-5 flex justify-end">
		<div class="w-full max-w-[300px] space-y-1.5 text-[12.5px] font-bold text-slate-700">
			<div class="flex justify-between"><span><?php esc_html_e( 'جمع اقلام:', 'erfansanat' ); ?></span><span><?php echo esc_html( $es_money( $es_subtotal ) ); ?></span></div>
			<div class="flex justify-between"><span><?php esc_html_e( 'مالیات بر ارزش افزوده (۱۰٪):', 'erfansanat' ); ?></span><span><?php echo esc_html( $es_money( $es_tax ) ); ?></span></div>
			<div class="flex justify-between border-t border-slate-200 pt-1.5 text-[14px] font-black text-emerald-900"><span><?php esc_html_e( 'مبلغ قابل پرداخت:', 'erfansanat' ); ?></span><span><?php echo esc_html( $es_money( $es_grand ) ); ?></span></div>
		</div>
	</div>

	<div class="mt-8 flex items-end justify-between text-[11.5px] font-bold text-slate-500">
		<p class="max-w-[26rem] leading-6"><?php esc_html_e( 'این پیش‌فاکتور به منزله تأیید نهایی خرید نبوده و صرفاً جهت اطلاع از شرایط قیمت و پرداخت صادر شده است. مهر و امضای شرکت پس از تسویه نهایی اعمال می‌گردد.', 'erfansanat' ); ?></p>
		<div class="text-center">
			<div class="mb-6"><?php esc_html_e( 'مهر و امضای شرکت', 'erfansanat' ); ?></div>
			<div class="h-px w-32 bg-slate-300"></div>
		</div>
	</div>

	<div class="es-proforma-no-print mt-6 text-center">
		<button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-[12.5px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'چاپ پیش‌فاکتور', 'erfansanat' ); ?></button>
	</div>
</div>
