<?php
/**
 * Template Name: Auth (Login / Register)
 *
 * Authentication page (P06-T027). Keeps the login/register plugin hooks
 * (voorodak / fl-panel / WooCommerce) intact (AC-01), validates the post-login
 * redirect against the site host (AC-02 / SC-02), and ships a testable SMS OTP
 * shim with correct error messaging (AC-03 / SC-01). Real SMS plugins override
 * the shim via the documented hooks/filters.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

/**
 * Restrict a redirect target to the current site (AC-02 / SC-02).
 */
if ( ! function_exists( 'es_auth_safe_redirect' ) ) {
	function es_auth_safe_redirect( $url ) {
		$url      = (string) $url;
		$fallback = home_url( '/' );
		if ( '' === $url ) {
			return $fallback;
		}
		// Site-relative paths (single leading slash) resolve against this site.
		if ( preg_match( '#^/[^/\\\\]#', $url ) ) {
			$url = home_url( $url );
		}
		// wp_validate_redirect only allows the site's own host (and allowlisted hosts).
		$validated = wp_validate_redirect( $url, $fallback );
		return $validated ?: $fallback;
	}
}

/**
 * Request an SMS OTP (shim). Real plugins hook `es_auth_send_otp` to deliver the
 * code through their own gateway; the shim stores it in a transient.
 */
if ( ! function_exists( 'es_auth_otp_request' ) ) {
	function es_auth_otp_request( $phone ) {
		$phone = preg_replace( '/\D/', '', (string) $phone );
		if ( strlen( $phone ) < 10 ) {
			return new WP_Error( 'es_otp_phone', 'شماره موبایل معتبر نیست.' );
		}
		$code = (string) apply_filters( 'es_auth_otp_code', (string) wp_rand( 10000, 99999 ), $phone );
		set_transient( 'es_otp_' . md5( $phone ), $code, 5 * MINUTE_IN_SECONDS );
		// Real SMS gateway integration point (voorodak / fl-panel).
		do_action( 'es_auth_send_otp', $phone, $code );
		return $code;
	}
}

/**
 * Verify an SMS OTP and log the customer in (shim). Returns user ID or WP_Error.
 */
if ( ! function_exists( 'es_auth_otp_verify' ) ) {
	function es_auth_otp_verify( $phone, $code ) {
		$phone = preg_replace( '/\D/', '', (string) $phone );
		$code  = preg_replace( '/\D/', '', (string) $code );
		$key   = 'es_otp_' . md5( $phone );
		$stored = get_transient( $key );
		if ( false === $stored || '' === $stored ) {
			return new WP_Error( 'es_otp_expired', 'کد تأیید منقضی شده است؛ لطفاً کد جدید درخواست کنید.' );
		}
		if ( ! hash_equals( (string) $stored, $code ) ) {
			return new WP_Error( 'es_otp_invalid', 'کد تأیید واردشده نادرست است.' );
		}
		delete_transient( $key );

		// Find or create the customer by phone (login meta).
		$users = get_users( array( 'meta_key' => 'es_phone', 'meta_value' => $phone, 'number' => 1 ) );
		if ( ! empty( $users ) ) {
			$user_id = (int) $users[0]->ID;
		} else {
			$user_id = wp_insert_user( array(
				'user_login' => 'user_' . $phone,
				'user_pass'  => wp_generate_password( 24 ),
				'role'       => 'customer',
			) );
			if ( is_wp_error( $user_id ) ) {
				return $user_id;
			}
			update_user_meta( $user_id, 'es_phone', $phone );
		}
		do_action( 'es_auth_otp_verified', $user_id, $phone );
		return (int) $user_id;
	}
}

get_header();

$es_redirect_to = es_auth_safe_redirect( isset( $_REQUEST['redirect_to'] ) ? wp_unslash( $_REQUEST['redirect_to'] ) : '' );
$es_active_tab  = isset( $_GET['tab'] ) && 'register' === $_GET['tab'] ? 'register' : 'login';
$es_error       = '';
$es_info        = '';
$es_logged_in   = is_user_logged_in();

// Handle OTP request / verify (shim path; real plugins may short-circuit).
if ( isset( $_POST['es_otp_request'] ) ) {
	check_admin_referer( 'es_auth_otp', 'es_auth_nonce' );
	$es_phone = isset( $_POST['es_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['es_phone'] ) ) : '';
	$es_sent  = es_auth_otp_request( $es_phone );
	if ( is_wp_error( $es_sent ) ) {
		$es_error = $es_sent->get_error_message();
	} else {
		$es_info = 'کد تأیید به شماره ' . esc_html( $es_phone ) . ' ارسال شد.';
	}
} elseif ( isset( $_POST['es_otp_verify'] ) ) {
	check_admin_referer( 'es_auth_otp', 'es_auth_nonce' );
	$es_phone = isset( $_POST['es_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['es_phone'] ) ) : '';
	$es_code  = isset( $_POST['es_otp_code'] ) ? sanitize_text_field( wp_unslash( $_POST['es_otp_code'] ) ) : '';
	$es_user_id = es_auth_otp_verify( $es_phone, $es_code );
	if ( is_wp_error( $es_user_id ) ) {
		$es_error = $es_user_id->get_error_message();
	} else {
		wp_set_current_user( $es_user_id );
		wp_set_auth_cookie( $es_user_id, true );
		do_action( 'es_auth_login_success', $es_user_id );
		wp_safe_redirect( es_auth_safe_redirect( $es_redirect_to ) );
		exit;
	}
}
?>

<main class="bg-slate-50/50" data-auth-page="true">
	<section class="mx-auto max-w-[480px] px-5 py-16 lg:py-24">
		<div class="rounded-[1.75rem] border border-slate-200 bg-white p-7 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.12)]">
			<h1 class="text-center text-xl font-black text-slate-950"><?php echo $es_active_tab === 'register' ? esc_html__( 'ایجاد حساب کاربری', 'erfansanat' ) : esc_html__( 'ورود به حساب کاربری', 'erfansanat' ); ?></h1>
			<p class="mt-1.5 text-center text-[12px] font-bold text-slate-500"><?php esc_html_e( 'برای پیگیری سفارش‌ها و دریافت پیش‌فاکتور رسمی وارد شوید.', 'erfansanat' ); ?></p>

			<?php if ( $es_logged_in ) : ?>
				<div class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-6 text-center" data-auth-loggedin="true">
					<p class="text-[13.5px] font-extrabold text-emerald-900"><?php esc_html_e( 'شما وارد حساب خود شده‌اید.', 'erfansanat' ); ?></p>
					<a href="<?php echo esc_url( home_url( '/my-account/' ) ); ?>" class="mt-4 inline-flex rounded-xl bg-emerald-700 px-5 py-2.5 text-[12.5px] font-extrabold text-white"><?php esc_html_e( 'رفتن به حساب کاربری', 'erfansanat' ); ?></a>
				</div>
			<?php else : ?>
				<div class="mt-6 grid grid-cols-2 gap-1.5 rounded-2xl bg-slate-100 p-1.5">
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'login', get_permalink() ) ); ?>" class="rounded-xl px-4 py-2 text-center text-[12.5px] font-extrabold transition <?php echo $es_active_tab === 'login' ? 'bg-white text-emerald-800 shadow-xs' : 'text-slate-500'; ?>"><?php esc_html_e( 'ورود', 'erfansanat' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'tab', 'register', get_permalink() ) ); ?>" class="rounded-xl px-4 py-2 text-center text-[12.5px] font-extrabold transition <?php echo $es_active_tab === 'register' ? 'bg-white text-emerald-800 shadow-xs' : 'text-slate-500'; ?>"><?php esc_html_e( 'ثبت‌نام', 'erfansanat' ); ?></a>
				</div>

				<?php if ( $es_error ) : ?>
					<div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[12px] font-bold text-red-700" data-auth-error><?php echo esc_html( $es_error ); ?></div>
				<?php endif; ?>
				<?php if ( $es_info ) : ?>
					<div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[12px] font-bold text-emerald-800" data-auth-info><?php echo esc_html( $es_info ); ?></div>
				<?php endif; ?>

				<?php if ( $es_active_tab === 'register' ) : ?>
					<form method="post" action="<?php echo esc_url( site_url( 'wp-login.php?action=register', 'login_post' ) ); ?>" class="mt-5 space-y-3" data-auth-register="true">
						<?php do_action( 'register_form' ); ?>
						<?php do_action( 'woocommerce_register_form' ); ?>
						<div><label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'نام کاربری', 'erfansanat' ); ?></label><input type="text" name="user_login" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold outline-none focus:border-emerald-500" /></div>
						<div><label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'ایمیل', 'erfansanat' ); ?></label><input type="email" name="user_email" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold outline-none focus:border-emerald-500" /></div>
						<?php do_action( 'fl_panel_register_fields' ); ?>
						<?php do_action( 'voorodak_register_fields' ); ?>
						<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $es_redirect_to ); ?>" />
						<button type="submit" class="w-full rounded-xl bg-emerald-700 px-5 py-3 text-[13px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'ثبت‌نام', 'erfansanat' ); ?></button>
						<?php do_action( 'register_form_end' ); ?>
					</form>
				<?php else : ?>
					<form method="post" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" class="mt-5 space-y-3" data-auth-login="true">
						<?php do_action( 'login_form' ); ?>
						<?php do_action( 'woocommerce_login_form_start' ); ?>
						<div><label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'نام کاربری یا ایمیل', 'erfansanat' ); ?></label><input type="text" name="log" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold outline-none focus:border-emerald-500" /></div>
						<div><label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'رمز عبور', 'erfansanat' ); ?></label><input type="password" name="pwd" required class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold outline-none focus:border-emerald-500" /></div>
						<?php do_action( 'woocommerce_login_form' ); ?>
						<?php do_action( 'fl_panel_login_fields' ); ?>
						<?php do_action( 'voorodak_login_fields' ); ?>
						<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $es_redirect_to ); ?>" />
						<input type="hidden" name="testcookie" value="1" />
						<button type="submit" name="wp-submit" class="w-full rounded-xl bg-emerald-700 px-5 py-3 text-[13px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'ورود', 'erfansanat' ); ?></button>
						<?php do_action( 'login_form_end' ); ?>
						<?php do_action( 'woocommerce_login_form_end' ); ?>
					</form>

					<div class="my-5 flex items-center gap-3 text-[11px] font-bold text-slate-400"><span class="h-px flex-1 bg-slate-200"></span><?php esc_html_e( 'یا ورود با کد پیامکی', 'erfansanat' ); ?><span class="h-px flex-1 bg-slate-200"></span></div>

					<form method="post" class="space-y-3" data-auth-otp="true">
						<?php wp_nonce_field( 'es_auth_otp', 'es_auth_nonce' ); ?>
						<div><label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'شماره موبایل', 'erfansanat' ); ?></label><input type="tel" name="es_phone" required placeholder="۰۹۲۰۰۰۰۰۰۰" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold outline-none focus:border-emerald-500" /></div>
						<div><label class="mb-1.5 block text-[11.5px] font-extrabold text-slate-700"><?php esc_html_e( 'کد تأیید پیامکی', 'erfansanat' ); ?></label><input type="text" name="es_otp_code" inputmode="numeric" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-[12.5px] font-bold outline-none focus:border-emerald-500" /></div>
						<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $es_redirect_to ); ?>" />
						<div class="grid grid-cols-2 gap-2">
							<button type="submit" name="es_otp_request" value="1" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-[12px] font-extrabold text-emerald-800 transition hover:bg-emerald-100"><?php esc_html_e( 'ارسال کد', 'erfansanat' ); ?></button>
							<button type="submit" name="es_otp_verify" value="1" class="rounded-xl bg-emerald-700 px-4 py-2.5 text-[12px] font-extrabold text-white transition hover:bg-emerald-800"><?php esc_html_e( 'ورود با کد', 'erfansanat' ); ?></button>
						</div>
						<?php do_action( 'es_auth_otp_form_end' ); ?>
					</form>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php
get_footer();
