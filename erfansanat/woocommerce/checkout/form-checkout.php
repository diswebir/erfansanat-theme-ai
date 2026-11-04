<?php
/**
 * Checkout form (P06-T024). Reference layout (data-checkout-page + 12-col grid)
 * with the standard woocommerce_checkout_* hooks (AC-02) and configurable
 * real/legal buyer fields (AC-03). On a valid POST it validates required fields
 * (SC-01) and creates a real order via wc_create_order (SC-02). The client-side
 * receipt preview is handled by main.js (data-checkout-receipt).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_cart  = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart : null;
$es_items = $es_cart ? $es_cart->get_cart() : array();
$es_total = $es_cart ? (float) $es_cart->get_cart_contents_total() : 0;

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

// Configurable buyer types (AC-03) and legal-only fields.
$es_buyer_types   = apply_filters( 'es_checkout_buyer_types', array(
	'real'  => 'خریدار حقیقی',
	'legal' => 'خریدار حقوقی / شرکتی',
) );
$es_legal_fields  = apply_filters( 'es_checkout_legal_fields', array(
	'customer_tax_id' => 'شناسه ملی / کد اقتصادی شرکت',
	'customer_org'    => 'نام حقوقی شرکت / سازمان',
) );
$es_required      = apply_filters( 'es_checkout_required_fields', array( 'customer_name', 'customer_phone', 'customer_address' ) );
$es_payment_methods = apply_filters( 'es_checkout_payment_methods', array(
	'mellat'   => 'درگاه هوشمند بانک ملت (پرداخت آنلاین شاپرک)',
	'proforma' => 'صدور پیش‌فاکتور رسمی ۱۰٪ ارزش افزوده (حواله پایا/ساتنا)',
	'credit'   => 'خرید اعتباری پروژه‌ای و سامانه تدارکات دولت (ستاد)',
) );

// Process checkout POST (real Woo handles its own; this is the theme/shim path).
$es_errors = array();
$es_order  = null;
if ( isset( $_POST['es_checkout_submit'] ) ) {
	check_admin_referer( 'woocommerce-process-checkout', 'woocommerce-process-checkout-nonce' );

	$es_data = array();
	foreach ( array( 'buyer_type', 'customer_name', 'customer_phone', 'customer_address', 'customer_city', 'customer_notes', 'customer_tax_id', 'customer_org', 'payment_method' ) as $es_field ) {
		$es_data[ $es_field ] = isset( $_POST[ $es_field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $es_field ] ) ) : '';
	}
	if ( ! isset( $es_buyer_types[ $es_data['buyer_type'] ] ) ) {
		$es_data['buyer_type'] = 'real';
	}
	$es_labels = array(
		'customer_name'    => 'نام و نام خانوادگی',
		'customer_phone'   => 'شماره تماس',
		'customer_address' => 'آدرس تحویل',
	);
	foreach ( $es_required as $es_field ) {
		if ( '' === trim( $es_data[ $es_field ] ) ) {
			$es_errors[ $es_field ] = sprintf( 'فیلد «%s» الزامی است.', $es_labels[ $es_field ] ?? $es_field );
		}
	}
	if ( 'legal' === $es_data['buyer_type'] && '' === trim( $es_data['customer_tax_id'] ) ) {
		$es_errors['customer_tax_id'] = 'برای خریدار حقوقی، شناسه ملی / کد اقتصادی الزامی است.';
	}

	if ( empty( $es_errors ) && function_exists( 'wc_create_order' ) ) {
		$es_order = wc_create_order( array( 'status' => 'pending' ) );
		foreach ( $es_items as $es_item ) {
			$es_product = isset( $es_item['data'] ) && is_object( $es_item['data'] ) ? $es_item['data'] : wc_get_product( $es_item['product_id'] );
			if ( $es_product ) {
				$es_order->add_product( $es_product, (int) $es_item['quantity'] );
			}
		}
		$es_order->set_address( array(
			'first_name' => $es_data['customer_name'],
			'phone'      => $es_data['customer_phone'],
			'address_1'  => $es_data['customer_address'],
			'city'       => $es_data['customer_city'],
		) );
		update_post_meta( $es_order->get_id(), '_es_buyer_type', $es_data['buyer_type'] );
		update_post_meta( $es_order->get_id(), '_es_payment_method', $es_data['payment_method'] );
		update_post_meta( $es_order->get_id(), '_es_customer_notes', $es_data['customer_notes'] );
		if ( 'legal' === $es_data['buyer_type'] ) {
			update_post_meta( $es_order->get_id(), '_es_tax_id', $es_data['customer_tax_id'] );
			update_post_meta( $es_order->get_id(), '_es_org', $es_data['customer_org'] );
		}
		$es_order->calculate_totals();
		$es_order->update_status( 'proforma' === $es_data['payment_method'] ? 'on-hold' : 'pending' );
		if ( $es_cart ) {
			$es_cart->empty_cart();
		}
		do_action( 'es_checkout_order_created', $es_order->get_id(), $es_data );
	}
}

$es_field_value = static function ( $key ) {
	return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
};
$es_buyer_selected = $es_field_value( 'buyer_type' ) ?: 'real';
?>

<main class="bg-slate-50/50" data-checkout-page="true">
	<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-45"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
			<h1 class="mt-2 text-[2rem] font-black leading-[1.3] text-slate-950 sm:text-[2.6rem]"><?php esc_html_e( 'ثبت نهایی سفارش و پرداخت', 'erfansanat' ); ?></h1>
			<p class="mt-3 max-w-[40rem] text-[14px] font-medium leading-8 text-slate-600"><?php esc_html_e( 'مشخصات تحویل‌گیرنده و روش پرداخت را تکمیل کنید؛ پیش‌فاکتور رسمی با مهر و امضای شرکت صادر می‌شود.', 'erfansanat' ); ?></p>
		</div>
	</section>

	<section class="mx-auto max-w-[1300px] px-5 py-12 lg:px-8 lg:py-16">
		<?php do_action( 'woocommerce_before_checkout_form', $es_cart ); ?>

		<?php if ( $es_order ) : ?>
			<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-6 py-5 text-[13.5px] font-extrabold text-emerald-900" data-checkout-success>
				<?php echo esc_html( sprintf( 'سفارش شما با شماره %s ثبت شد. کارشناسان ما جهت هماهنگی ارسال تماس می‌گیرند.', $es_fa( $es_order->get_order_number() ) ) ); ?>
			</div>
		<?php endif; ?>

		<?php if ( $es_errors ) : ?>
			<div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-6 py-5" data-checkout-errors>
				<ul class="list-inside list-disc space-y-1 text-[12.5px] font-bold text-red-700">
					<?php foreach ( $es_errors as $es_error ) : ?>
						<li><?php echo esc_html( $es_error ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<form data-checkout-form="true" method="post" class="grid items-start gap-8 lg:grid-cols-12 <?php echo $es_order ? 'hidden' : ''; ?>">
			<?php wp_nonce_field( 'woocommerce-process-checkout', 'woocommerce-process-checkout-nonce' ); ?>
			<div class="space-y-6 lg:col-span-7">
				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs">
					<h2 class="mb-4 text-base font-black text-slate-900"><?php esc_html_e( '۱. نوع خریدار و صورت‌حساب', 'erfansanat' ); ?></h2>
					<div class="grid gap-3 sm:grid-cols-2">
						<?php foreach ( $es_buyer_types as $es_value => $es_label ) : ?>
							<label class="flex cursor-pointer items-center gap-2.5 rounded-2xl border border-slate-200 bg-slate-50/60 px-4 py-3 text-[13px] font-extrabold text-slate-800 transition has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50">
								<input type="radio" name="buyer_type" value="<?php echo esc_attr( $es_value ); ?>" data-buyer-type-radio="true" <?php checked( $es_buyer_selected, $es_value ); ?> class="h-4 w-4 accent-emerald-700" />
								<span><?php echo esc_html( $es_label ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
					<div data-legal-fields="true" class="mt-4 grid gap-3 sm:grid-cols-2 <?php echo 'real' === $es_buyer_selected ? 'opacity-60' : ''; ?>">
						<?php foreach ( $es_legal_fields as $es_key => $es_label ) : ?>
							<div>
								<label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php echo esc_html( $es_label ); ?></label>
								<input type="text" name="<?php echo esc_attr( $es_key ); ?>" value="<?php echo esc_attr( $es_field_value( $es_key ) ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold text-slate-800 outline-none focus:border-emerald-500" />
								<?php if ( isset( $es_errors[ $es_key ] ) ) : ?><p class="mt-1 text-[11px] font-bold text-red-600"><?php echo esc_html( $es_errors[ $es_key ] ); ?></p><?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs">
					<h2 class="mb-4 text-base font-black text-slate-900"><?php esc_html_e( '۲. مشخصات تحویل‌گیرنده و مقصد ارسال', 'erfansanat' ); ?></h2>
					<div class="grid gap-3 sm:grid-cols-2">
						<div>
							<label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'نام و نام خانوادگی *', 'erfansanat' ); ?></label>
							<input type="text" name="customer_name" required value="<?php echo esc_attr( $es_field_value( 'customer_name' ) ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold text-slate-800 outline-none focus:border-emerald-500" />
						</div>
						<div>
							<label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'شماره تماس *', 'erfansanat' ); ?></label>
							<input type="tel" name="customer_phone" required value="<?php echo esc_attr( $es_field_value( 'customer_phone' ) ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold text-slate-800 outline-none focus:border-emerald-500" />
						</div>
						<div class="sm:col-span-2">
							<label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'آدرس تحویل *', 'erfansanat' ); ?></label>
							<textarea name="customer_address" required rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold text-slate-800 outline-none focus:border-emerald-500"><?php echo esc_textarea( $es_field_value( 'customer_address' ) ); ?></textarea>
						</div>
						<div>
							<label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'شهر / استان', 'erfansanat' ); ?></label>
							<input type="text" name="customer_city" value="<?php echo esc_attr( $es_field_value( 'customer_city' ) ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold text-slate-800 outline-none focus:border-emerald-500" />
						</div>
						<div>
							<label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'توضیحات سفارش', 'erfansanat' ); ?></label>
							<input type="text" name="customer_notes" value="<?php echo esc_attr( $es_field_value( 'customer_notes' ) ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold text-slate-800 outline-none focus:border-emerald-500" />
						</div>
					</div>
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</div>

				<div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xs">
					<h2 class="mb-4 text-base font-black text-slate-900"><?php esc_html_e( '۳. انتخاب روش پرداخت یا صدور پیش‌فاکتور', 'erfansanat' ); ?></h2>
					<div class="space-y-2.5">
						<?php foreach ( $es_payment_methods as $es_value => $es_label ) : ?>
							<label class="flex cursor-pointer items-center gap-2.5 rounded-2xl border border-slate-200 bg-slate-50/60 px-4 py-3 text-[12.5px] font-extrabold text-slate-800 transition has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50">
								<input type="radio" name="payment_method" value="<?php echo esc_attr( $es_value ); ?>" <?php checked( $es_field_value( 'payment_method' ) ?: 'mellat', $es_value ); ?> class="h-4 w-4 accent-emerald-700" />
								<span><?php echo esc_html( $es_label ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
					<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
			</div>

			<div class="lg:col-span-5">
				<div class="sticky top-28 overflow-hidden rounded-[1.5rem] border border-slate-200/90 bg-white shadow-[0_18px_45px_-28px_rgba(15,23,42,0.10)]">
					<div class="border-b border-slate-100 bg-emerald-50/60 px-6 py-4">
						<h2 class="text-sm font-black text-emerald-950"><?php esc_html_e( 'مرور اقلام سفارش شما', 'erfansanat' ); ?></h2>
					</div>
					<div class="space-y-3 p-6" data-checkout-items-list="true">
						<?php foreach ( $es_items as $es_item ) : ?>
							<?php
							$es_product = isset( $es_item['data'] ) && is_object( $es_item['data'] ) ? $es_item['data'] : wc_get_product( $es_item['product_id'] );
							if ( ! $es_product ) {
								continue;
							}
							?>
							<div class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-3.5 py-2.5">
								<div class="min-w-0">
									<div class="truncate text-[12.5px] font-extrabold text-slate-800"><?php echo esc_html( $es_product->get_name() ); ?></div>
									<div class="text-[11px] font-bold text-slate-500"><?php echo esc_html( $es_fa( (int) $es_item['quantity'] ) ); ?> × <?php echo esc_html( $es_money( (float) $es_product->get_price() ) ); ?></div>
								</div>
								<div class="shrink-0 text-[12.5px] font-black text-emerald-800 tabular-nums"><?php echo esc_html( $es_money( (float) $es_product->get_price() * (int) $es_item['quantity'] ) ); ?></div>
							</div>
						<?php endforeach; ?>
					</div>
					<div class="border-t border-slate-100 px-6 py-4">
						<div class="flex items-baseline justify-between">
							<span class="text-xs font-extrabold text-slate-800"><?php esc_html_e( 'مبلغ قابل پرداخت:', 'erfansanat' ); ?></span>
							<span data-checkout-total="true" class="text-xl font-black text-emerald-800 tabular-nums"><?php echo esc_html( $es_money( $es_total ) ); ?></span>
						</div>
						<?php do_action( 'woocommerce_checkout_order_review' ); ?>
						<button type="submit" name="es_checkout_submit" value="1" class="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-500 px-5 py-3.5 text-sm font-extrabold text-white shadow-md transition hover:shadow-lg">
							<span><?php esc_html_e( 'ثبت نهایی سفارش و دریافت پیش‌فاکتور', 'erfansanat' ); ?></span>
						</button>
					</div>
				</div>
			</div>
		</form>

		<div data-checkout-receipt="true" class="hidden rounded-[1.5rem] border border-emerald-200 bg-white p-8 shadow-xs">
			<h2 class="text-lg font-black text-emerald-900"><?php esc_html_e( 'رسید ثبت سفارش و پیش‌فاکتور رسمی', 'erfansanat' ); ?></h2>
			<div class="mt-5 grid gap-3 text-[13px] font-bold text-slate-700 sm:grid-cols-2">
				<div><span class="text-slate-400"><?php esc_html_e( 'مشتری:', 'erfansanat' ); ?></span> <span data-receipt-customer="true"></span></div>
				<div><span class="text-slate-400"><?php esc_html_e( 'کد پیگیری:', 'erfansanat' ); ?></span> <span data-receipt-code="true" class="font-black text-emerald-800"></span></div>
				<div class="sm:col-span-2"><span class="text-slate-400"><?php esc_html_e( 'روش پرداخت:', 'erfansanat' ); ?></span> <span data-receipt-method="true"></span></div>
			</div>
		</div>

		<?php do_action( 'woocommerce_after_checkout_form', $es_cart ); ?>
	</section>
</main>

<?php
get_footer();
