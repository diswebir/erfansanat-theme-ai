<?php
/** Login/registration page preserving WooCommerce and SMS-plugin form hooks. */
/** @version 9.9.0 */

defined( 'ABSPATH' ) || exit;
do_action( 'woocommerce_before_customer_login_form' );
$registration_enabled = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
?>
<section class="relative overflow-hidden border-b border-emerald-100 bg-slate-50 pb-10 pt-28 lg:pb-12"><div class="mx-auto max-w-[1300px] px-5 lg:px-8"><h1 class="text-3xl font-black text-slate-950 sm:text-4xl">ورود یا ثبت‌نام</h1><p class="mt-2 text-sm text-slate-600">برای پیگیری سفارش‌ها و مدیریت حساب وارد شوید.</p></div></section>
<section class="woocommerce-account mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16">
<?php if ( $registration_enabled ) : ?><div class="u-columns col2-set grid items-start gap-8 lg:grid-cols-2" id="customer_login"><?php endif; ?>
	<div class="u-column1 col-1 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7"><h2 class="mb-5 text-lg font-extrabold text-slate-900"><?php esc_html_e( 'ورود', 'erfansanat-theme' ); ?></h2>
		<form class="woocommerce-form woocommerce-form-login login space-y-4" method="post" novalidate>
			<?php do_action( 'woocommerce_login_form_start' ); ?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"><label for="username"><?php esc_html_e( 'نام کاربری یا ایمیل', 'erfansanat-theme' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label><input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true"></p>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"><label for="password"><?php esc_html_e( 'رمز عبور', 'erfansanat-theme' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label><input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true"></p>
			<?php do_action( 'woocommerce_login_form' ); ?>
			<p class="form-row flex flex-wrap items-center justify-between gap-3"><label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme"><input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever"><span><?php esc_html_e( 'مرا به خاطر بسپار', 'erfansanat-theme' ); ?></span></label><?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?><button type="submit" class="woocommerce-button button woocommerce-form-login__submit rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'ورود', 'erfansanat-theme' ); ?></button></p>
			<p class="woocommerce-LostPassword lost_password text-xs"><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'رمز عبور را فراموش کرده‌اید؟', 'erfansanat-theme' ); ?></a></p>
			<?php do_action( 'woocommerce_login_form_end' ); ?>
		</form>
	</div>
<?php if ( $registration_enabled ) : ?>
	<div class="u-column2 col-2 rounded-2xl border border-slate-200 bg-white p-5 sm:p-7"><h2 class="mb-5 text-lg font-extrabold text-slate-900"><?php esc_html_e( 'ثبت‌نام', 'erfansanat-theme' ); ?></h2>
		<form method="post" class="woocommerce-form woocommerce-form-register register space-y-4" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
			<?php do_action( 'woocommerce_register_form_start' ); ?>
			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?><p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"><label for="reg_username"><?php esc_html_e( 'نام کاربری', 'erfansanat-theme' ); ?> <span class="required" aria-hidden="true">*</span></label><input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ! empty( $_POST['username'] ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true"></p><?php endif; ?>
			<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"><label for="reg_email"><?php esc_html_e( 'نشانی ایمیل', 'erfansanat-theme' ); ?> <span class="required" aria-hidden="true">*</span></label><input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ! empty( $_POST['email'] ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true"></p>
			<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?><p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"><label for="reg_password"><?php esc_html_e( 'رمز عبور', 'erfansanat-theme' ); ?> <span class="required" aria-hidden="true">*</span></label><input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true"></p><?php else : ?><p class="text-xs leading-6 text-slate-500"><?php esc_html_e( 'پیوند تنظیم رمز عبور به نشانی ایمیل شما ارسال می‌شود.', 'erfansanat-theme' ); ?></p><?php endif; ?>
			<?php do_action( 'woocommerce_register_form' ); ?>
			<p class="woocommerce-form-row form-row"><?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?><button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'ثبت‌نام', 'erfansanat-theme' ); ?></button></p>
			<?php do_action( 'woocommerce_register_form_end' ); ?>
		</form>
	</div>
</div>
<?php endif; ?>
</section>
<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
