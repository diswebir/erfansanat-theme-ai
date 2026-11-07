<?php
/** Accessible WooCommerce login form retaining extension hooks (including OTP). */
/** @version 9.2.0 */

defined( 'ABSPATH' ) || exit;
if ( is_user_logged_in() ) { return; }
$hidden = isset( $hidden ) ? (bool) $hidden : false;
$message = isset( $message ) ? $message : '';
$redirect = isset( $redirect ) ? $redirect : wc_get_page_permalink( 'myaccount' );
?>
<form class="woocommerce-form woocommerce-form-login login rounded-2xl border border-slate-200 bg-white p-5 sm:p-7" method="post" <?php echo $hidden ? 'style="display:none;"' : ''; ?>>
	<?php do_action( 'woocommerce_login_form_start' ); ?>
	<?php if ( $message ) : ?><div class="mb-4 text-sm leading-7 text-slate-700"><?php echo wp_kses_post( wpautop( wptexturize( $message ) ) ); ?></div><?php endif; ?>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"><label for="username"><?php esc_html_e( 'Username or email', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label><input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" required aria-required="true"></p>
	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"><label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label><input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true"></p>
	<?php do_action( 'woocommerce_login_form' ); ?>
	<p class="form-row flex flex-wrap items-center justify-between gap-3"><label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme"><input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever"><span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span></label><?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?><input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ); ?>"><button type="submit" class="woocommerce-button button woocommerce-form-login__submit rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white" name="login" value="<?php esc_attr_e( 'Login', 'woocommerce' ); ?>"><?php esc_html_e( 'Login', 'woocommerce' ); ?></button></p>
	<p class="lost_password text-xs"><a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a></p>
	<?php do_action( 'woocommerce_login_form_end' ); ?>
</form>
