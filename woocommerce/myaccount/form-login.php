<?php
/**
 * فرم ورود/ثبت‌نام حساب کاربری — پوسته میرور (کامپوننت‌های فرم checkout.html)
 *
 * بدون تغییر markup اساسی ووکامرس: نام فیلدها/nonce/اکشن استاندارد است و
 * هوک‌های woocommerce_login_form_* حفظ می‌شوند (سازگار با فرم OTP افزونه voorodak).
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

if (!function_exists('WC')) {
    return;
}

$es_login_inp  = 'w-full rounded-xl border border-slate-200 bg-slate-50/70 px-3.5 py-2.5 text-xs font-medium text-slate-800 outline-none focus:border-emerald-500 focus:bg-white';
$es_login_lbl  = 'mb-1.5 block text-xs font-bold text-slate-700';
?>
<div class="mx-auto max-w-4xl rounded-3xl border border-emerald-200/80 bg-white p-6 shadow-xs sm:p-8">
	<?php do_action('woocommerce_login_form_start'); ?>
	<div class="grid gap-8 md:grid-cols-2">

		<!-- ورود -->
		<div class="rounded-2xl border border-slate-200/80 bg-white p-5">
			<h2 class="mb-5 flex items-center gap-2 text-base font-black text-slate-900"><span>ورود مشتریان</span></h2>
			<form class="woocommerce-form woocommerce-form-login login" method="post">

				<?php do_action('woocommerce_login_form_start'); ?>

				<p class="woocommerce-form-row form-row">
					<label class="<?php echo esc_attr($es_login_lbl); ?>" for="username"><?php esc_html_e('نام کاربری یا ایمیل', 'erfansanat'); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text <?php echo esc_attr($es_login_inp); ?>" name="username" id="username" autocomplete="username" value="<?php echo (!empty($_POST['username']) && is_string($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" required="required" />
				</p>
				<p class="woocommerce-form-row form-row mt-3">
					<label class="<?php echo esc_attr($es_login_lbl); ?>" for="password"><?php esc_html_e('گذرواژه', 'erfansanat'); ?>&nbsp;<span class="required">*</span></label>
					<input class="woocommerce-Input woocommerce-Input--password input-text <?php echo esc_attr($es_login_inp); ?>" type="password" name="password" id="password" autocomplete="current-password" required="required" />
				</p>
				<p class="form-row mt-4 flex items-center justify-between gap-3">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox inline-flex items-center gap-2 text-[11.5px] font-bold text-slate-600">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
						<span><?php esc_html_e('مرا به خاطر بسپار', 'erfansanat'); ?></span>
					</label>
					<?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
					<button type="submit" class="woocommerce-button button woocommerce-form-login__submit btn-primary px-5 py-2.5 text-xs" name="login" value="<?php esc_attr_e('ورود', 'erfansanat'); ?>"><?php esc_html_e('ورود', 'erfansanat'); ?></button>
				</p>
				<p class="woocommerce-LostPassword lost_password mt-3 text-[11.5px] font-bold text-emerald-700">
					<a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="hover:underline"><?php esc_html_e('گذرواژه خود را فراموش کرده‌اید؟', 'erfansanat'); ?></a>
				</p>

				<?php do_action('woocommerce_login_form_end'); ?>
			</form>
		</div>

		<!-- ثبت‌نام -->
		<div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-5">
			<h2 class="mb-5 flex items-center gap-2 text-base font-black text-slate-900"><span><?php esc_html_e('ثبت‌نام', 'erfansanat'); ?></span></h2>
			<form method="post" class="woocommerce-form woocommerce-form-register register">

				<?php do_action('woocommerce_register_form_start'); ?>

				<?php if ('no' === get_option('woocommerce_registration_generate_username')) : ?>
					<p class="woocommerce-form-row form-row">
						<label class="<?php echo esc_attr($es_login_lbl); ?>" for="reg_username"><?php esc_html_e('نام کاربری', 'erfansanat'); ?>&nbsp;<span class="required">*</span></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text <?php echo esc_attr($es_login_inp); ?>" name="username" id="reg_username" autocomplete="username" value="<?php echo (!empty($_POST['username']) && is_string($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" required="required" />
					</p>
				<?php endif; ?>

				<p class="woocommerce-form-row form-row">
					<label class="<?php echo esc_attr($es_login_lbl); ?>" for="reg_email"><?php esc_html_e('ایمیل', 'erfansanat'); ?>&nbsp;<span class="required">*</span></label>
					<input type="email" class="woocommerce-Input woocommerce-Input--email input-text <?php echo esc_attr($es_login_inp); ?>" name="email" id="reg_email" autocomplete="email" value="<?php echo (!empty($_POST['email']) && is_string($_POST['email'])) ? esc_attr(wp_unslash($_POST['email'])) : ''; ?>" required="required" />
				</p>

				<?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>
					<p class="woocommerce-form-row form-row mt-3">
						<label class="<?php echo esc_attr($es_login_lbl); ?>" for="reg_password"><?php esc_html_e('گذرواژه', 'erfansanat'); ?>&nbsp;<span class="required">*</span></label>
						<input type="password" class="woocommerce-Input woocommerce-Input--password input-text <?php echo esc_attr($es_login_inp); ?>" name="password" id="reg_password" autocomplete="new-password" required="required" />
					</p>
				<?php endif; ?>

				<?php do_action('woocommerce_register_form'); ?>

				<p class="woocommerce-form-row form-row mt-4">
					<?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
					<button type="submit" class="woocommerce-button button woocommerce-form-register__submit btn-primary w-full justify-center px-5 py-2.5 text-xs" name="register" value="<?php esc_attr_e('ثبت‌نام', 'erfansanat'); ?>"><?php esc_html_e('ثبت‌نام', 'erfansanat'); ?></button>
				</p>

				<?php do_action('woocommerce_register_form_end'); ?>
			</form>
		</div>

	</div>
	<?php do_action('woocommerce_login_form_end'); ?>
</div>
