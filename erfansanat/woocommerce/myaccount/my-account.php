<?php
/**
 * My Account shell (P06-T026). Preserves the account navigation (AC-01) and the
 * standard Woo account hooks/endpoints (AC-02). It is intentionally separate
 * from the theme auth panel so the two never conflict (AC-03).
 * Logged-in customers see their real orders (SC-01) and can save a default
 * address (SC-02).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

get_header();

$es_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};

$es_logged_in = is_user_logged_in();
$es_notice    = '';

// SC-02: save default address (real Woo uses WC()->customer; theme/shim path uses user meta).
if ( $es_logged_in && isset( $_POST['es_save_address'] ) ) {
	check_admin_referer( 'es_save_address', 'es_address_nonce' );
	$es_addr_fields = array( 'first_name', 'last_name', 'phone', 'address_1', 'city', 'postcode', 'state' );
	foreach ( array( 'billing', 'shipping' ) as $es_type ) {
		foreach ( $es_addr_fields as $es_field ) {
			$es_key = $es_type . '_' . $es_field;
			if ( isset( $_POST[ $es_key ] ) ) {
				update_user_meta( get_current_user_id(), $es_key, sanitize_text_field( wp_unslash( $_POST[ $es_key ] ) ) );
			}
		}
	}
	$es_notice = 'آدرس پیش‌فرض شما ذخیره شد.';
	do_action( 'es_account_address_saved', get_current_user_id() );
}

// Current endpoint/tab.
$es_tab = get_query_var( 'es_account_tab' );
if ( ! $es_tab && isset( $_GET['tab'] ) ) {
	$es_tab = sanitize_key( wp_unslash( $_GET['tab'] ) );
}
$es_tab = $es_tab ?: 'dashboard';

$es_account_url = get_permalink();
$es_nav_items   = array(
	'dashboard' => 'پیشخوان',
	'orders'    => 'سفارش‌ها',
	'addresses' => 'آدرس‌ها',
	'details'   => 'جزئیات حساب',
);
$es_nav_urls = array();
foreach ( array_keys( $es_nav_items ) as $es_slug ) {
	$es_endpoint = ( 'addresses' === $es_slug ) ? 'edit-address' : ( ( 'details' === $es_slug ) ? 'edit-account' : $es_slug );
	$es_nav_urls[ $es_slug ] = function_exists( 'wc_get_account_endpoint_url' ) ? wc_get_account_endpoint_url( $es_endpoint ) : add_query_arg( 'tab', $es_slug, $es_account_url );
}
$es_logout_url = function_exists( 'wc_logout_url' ) ? wc_logout_url() : wp_logout_url( $es_account_url );
?>

<main class="bg-slate-50/50" data-myaccount-page="true">
	<section class="mx-auto max-w-[1200px] px-5 py-14 lg:px-8 lg:py-16">
		<h1 class="mb-8 text-[1.8rem] font-black text-slate-950"><?php esc_html_e( 'حساب کاربری من', 'erfansanat' ); ?></h1>

		<?php if ( ! $es_logged_in ) : ?>
			<div class="rounded-[1.5rem] border border-slate-200 bg-white p-8 text-center" data-myaccount-guest="true">
				<p class="text-[14.5px] font-extrabold text-slate-700"><?php esc_html_e( 'برای مشاهده حساب کاربری وارد شوید.', 'erfansanat' ); ?></p>
				<p class="mt-1.5 text-[12.5px] font-bold text-slate-500"><?php esc_html_e( 'جهت پیگیری سفارش‌ها و دریافت پیش‌فاکتور، ابتدا وارد حساب خود شوید.', 'erfansanat' ); ?></p>
				<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
				<a href="<?php echo esc_url( home_url( '/auth/' ) ); ?>" class="mt-5 inline-flex rounded-xl bg-emerald-700 px-6 py-2.5 text-[12.5px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'ورود / ثبت‌نام', 'erfansanat' ); ?></a>
			</div>
		<?php else : ?>
			<?php if ( $es_notice ) : ?>
				<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-[12.5px] font-extrabold text-emerald-900" data-myaccount-notice><?php echo esc_html( $es_notice ); ?></div>
			<?php endif; ?>

			<div class="grid items-start gap-8 lg:grid-cols-12">
				<nav class="lg:col-span-3" data-myaccount-nav="true" aria-label="<?php esc_attr_e( 'ناوبری حساب کاربری', 'erfansanat' ); ?>">
					<?php do_action( 'woocommerce_account_navigation' ); ?>
					<ul class="space-y-1.5 rounded-[1.25rem] border border-slate-200 bg-white p-3">
						<?php foreach ( $es_nav_items as $es_slug => $es_label ) : ?>
							<li>
								<a href="<?php echo esc_url( $es_nav_urls[ $es_slug ] ); ?>" data-account-tab="<?php echo esc_attr( $es_slug ); ?>" class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-[12.5px] font-extrabold transition <?php echo $es_tab === $es_slug ? 'bg-emerald-700 text-white' : 'text-slate-700 hover:bg-emerald-50 hover:text-emerald-800'; ?>" <?php echo $es_tab === $es_slug ? 'aria-current="page"' : ''; ?>>
									<?php echo esc_html( $es_label ); ?>
								</a>
							</li>
						<?php endforeach; ?>
						<li>
							<a href="<?php echo esc_url( $es_logout_url ); ?>" class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-[12.5px] font-extrabold text-red-600 transition hover:bg-red-50"><?php esc_html_e( 'خروج از حساب', 'erfansanat' ); ?></a>
						</li>
					</ul>
				</nav>

				<div class="lg:col-span-9" data-myaccount-content="true">
					<?php
					do_action( 'woocommerce_account_content' );

					if ( 'orders' === $es_tab ) :
						$es_orders = get_posts( array( 'post_type' => 'shop_order', 'post_status' => 'any', 'numberposts' => -1, 'meta_key' => '_customer_user', 'meta_value' => get_current_user_id() ) );
						?>
						<div class="rounded-[1.5rem] border border-slate-200 bg-white p-6" data-myaccount-orders="true">
							<h2 class="mb-4 text-base font-black text-slate-900"><?php esc_html_e( 'سفارش‌های من', 'erfansanat' ); ?></h2>
							<?php if ( empty( $es_orders ) ) : ?>
								<p class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-10 text-center text-[12.5px] font-bold text-slate-500"><?php esc_html_e( 'سفارشی یافت نشد.', 'erfansanat' ); ?></p>
							<?php else : ?>
								<div class="space-y-2.5">
									<?php foreach ( $es_orders as $es_order_post ) : ?>
										<div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/60 px-4 py-3">
											<span class="text-[12.5px] font-extrabold text-slate-800"><?php echo esc_html( sprintf( 'سفارش #%s', $es_fa( $es_order_post->ID ) ) ); ?></span>
											<span class="text-[11.5px] font-bold text-slate-500"><?php echo esc_html( $es_fa( substr( $es_order_post->post_date, 0, 10 ) ) ); ?></span>
											<span class="text-[12.5px] font-black text-emerald-800 tabular-nums"><?php echo esc_html( $es_fa( number_format( (float) get_post_meta( $es_order_post->ID, '_order_total', true ), 0, '.', ',' ) ) . ' تومان' ); ?></span>
											<a href="<?php echo esc_url( home_url( '/thankyou/?order_id=' . $es_order_post->ID ) ); ?>" class="text-[11.5px] font-extrabold text-emerald-700 hover:underline"><?php esc_html_e( 'مشاهده رسید', 'erfansanat' ); ?></a>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php elseif ( 'addresses' === $es_tab ) : ?>
						<?php
						$es_uid  = get_current_user_id();
						$es_addr = static function ( $key ) use ( $es_uid ) { return (string) get_user_meta( $es_uid, 'billing_' . $key, true ); };
						?>
						<form method="post" class="rounded-[1.5rem] border border-slate-200 bg-white p-6" data-myaccount-address="true">
							<?php wp_nonce_field( 'es_save_address', 'es_address_nonce' ); ?>
							<h2 class="mb-4 text-base font-black text-slate-900"><?php esc_html_e( 'آدرس پیش‌فرض (صورتحساب و ارسال)', 'erfansanat' ); ?></h2>
							<div class="grid gap-3 sm:grid-cols-2">
								<div><label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'نام', 'erfansanat' ); ?></label><input type="text" name="billing_first_name" value="<?php echo esc_attr( $es_addr( 'first_name' ) ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold outline-none focus:border-emerald-500" /></div>
								<div><label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'نام خانوادگی', 'erfansanat' ); ?></label><input type="text" name="billing_last_name" value="<?php echo esc_attr( $es_addr( 'last_name' ) ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold outline-none focus:border-emerald-500" /></div>
								<div><label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'شماره تماس', 'erfansanat' ); ?></label><input type="tel" name="billing_phone" value="<?php echo esc_attr( $es_addr( 'phone' ) ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold outline-none focus:border-emerald-500" /></div>
								<div><label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'شهر', 'erfansanat' ); ?></label><input type="text" name="billing_city" value="<?php echo esc_attr( $es_addr( 'city' ) ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold outline-none focus:border-emerald-500" /></div>
								<div class="sm:col-span-2"><label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'آدرس کامل', 'erfansanat' ); ?></label><textarea name="billing_address_1" rows="2" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold outline-none focus:border-emerald-500"><?php echo esc_textarea( $es_addr( 'address_1' ) ); ?></textarea></div>
							</div>
							<button type="submit" name="es_save_address" value="1" class="mt-4 rounded-xl bg-emerald-700 px-6 py-2.5 text-[12.5px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'ذخیره آدرس', 'erfansanat' ); ?></button>
						</form>
					<?php elseif ( 'details' === $es_tab ) : ?>
						<?php $es_user = wp_get_current_user(); ?>
						<div class="rounded-[1.5rem] border border-slate-200 bg-white p-6" data-myaccount-details="true">
							<h2 class="mb-4 text-base font-black text-slate-900"><?php esc_html_e( 'جزئیات حساب', 'erfansanat' ); ?></h2>
							<div class="grid gap-3 text-[13px] font-bold text-slate-700 sm:grid-cols-2">
								<div><span class="text-slate-400"><?php esc_html_e( 'نام نمایشی:', 'erfansanat' ); ?></span> <?php echo esc_html( $es_user->display_name ); ?></div>
								<div><span class="text-slate-400"><?php esc_html_e( 'ایمیل:', 'erfansanat' ); ?></span> <?php echo esc_html( $es_user->user_email ); ?></div>
							</div>
							<?php do_action( 'woocommerce_edit_account_form' ); ?>
						</div>
					<?php else : ?>
						<?php get_template_part( 'woocommerce/myaccount/dashboard' ); ?>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
