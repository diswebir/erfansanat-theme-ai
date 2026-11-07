<?php
/** Account dashboard links with WooCommerce logout and endpoint URLs. */
/** @version 4.4.0 */

defined( 'ABSPATH' ) || exit;
$allowed_html = array( 'a' => array( 'href' => array() ), 'strong' => array() );
?>
<div class="woocommerce-MyAccount-dashboard space-y-5">
	<p class="text-sm leading-7 text-slate-700"><?php printf( wp_kses( __( 'سلام %1$s (شما نیستید؟ <a href="%2$s">خروج</a>)', 'woocommerce' ), $allowed_html ), '<strong>' . esc_html( $current_user->display_name ?? '' ) . '</strong>', esc_url( wc_logout_url() ) ); ?></p>
	<p class="text-sm leading-7 text-slate-600"><?php $dashboard_desc = wc_shipping_enabled() ? __( 'از پیشخوان حساب می‌توانید <a href="%1$s">سفارش‌های اخیر</a> را ببینید، <a href="%2$s">نشانی‌های ارسال و صورتحساب</a> را مدیریت کنید و <a href="%3$s">اطلاعات حساب</a> را ویرایش کنید.', 'woocommerce' ) : __( 'از پیشخوان حساب می‌توانید <a href="%1$s">سفارش‌های اخیر</a> را ببینید، <a href="%2$s">نشانی صورتحساب</a> را مدیریت کنید و <a href="%3$s">اطلاعات حساب</a> را ویرایش کنید.', 'woocommerce' ); printf( wp_kses( $dashboard_desc, $allowed_html ), esc_url( wc_get_endpoint_url( 'orders' ) ), esc_url( wc_get_endpoint_url( 'edit-address' ) ), esc_url( wc_get_endpoint_url( 'edit-account' ) ) ); ?></p>
	<?php do_action( 'woocommerce_account_dashboard' ); do_action( 'woocommerce_before_my_account' ); do_action( 'woocommerce_after_my_account' ); ?>
</div>
