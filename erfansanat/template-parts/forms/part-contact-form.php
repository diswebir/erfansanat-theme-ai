<?php
/**
 * The contact form.
 *
 * A plain POST to `admin-post.php`, so it works with JavaScript off. `main.js`
 * only enhances the acknowledgement: it does not send anything itself, and it
 * cannot turn a rejected submission into a success message the way the
 * reference's client-only handler did.
 *
 * The three hidden fields are all load-bearing:
 * `es_nonce` proves the request came from this page, `es_started` lets the server
 * reject a submission faster than a person could type, and `es_website` is a
 * honeypot no real visitor ever sees.
 *
 * @package ErfanSanat
 *
 * @var string $erfansanat_form_kind `contact`, `quote` or `callback`.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Forms\FormHandler;

$erfansanat_form_kind   = isset( $erfansanat_form_kind ) ? (string) $erfansanat_form_kind : 'contact';
$erfansanat_form_status = FormHandler::status();
$erfansanat_current     = is_user_logged_in() ? wp_get_current_user() : null;
$erfansanat_field_class = 'w-full rounded-xl border border-slate-200 bg-slate-50 py-3.5 pr-11 pl-4 text-[14px] text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10';
$erfansanat_plain_class = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-[14px] text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10';
?>
<?php if ( '' !== $erfansanat_form_status['message'] ) : ?>
	<div role="status"
		data-contact-status="<?php echo esc_attr( $erfansanat_form_status['state'] ); ?>"
		class="mt-8 rounded-2xl border px-5 py-4 text-[13px] font-bold leading-7 <?php echo 'ok' === $erfansanat_form_status['state'] ? 'border-emerald-200 bg-emerald-50 text-emerald-900' : 'border-rose-200 bg-rose-50 text-rose-900'; ?>">
		<?php echo esc_html( $erfansanat_form_status['message'] ); ?>
	</div>
<?php endif; ?>

<form
	data-contact-form="true"
	method="post"
	action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
	class="mt-8 flex flex-col gap-5"
	novalidate="novalidate">
	<input type="hidden" name="action" value="<?php echo esc_attr( FormHandler::ACTION ); ?>" />
	<input type="hidden" name="es_form" value="<?php echo esc_attr( $erfansanat_form_kind ); ?>" />
	<input type="hidden" name="es_nonce" value="<?php echo esc_attr( wp_create_nonce( FormHandler::NONCE ) ); ?>" />
	<input type="hidden" name="es_started" value="<?php echo esc_attr( (string) time() ); ?>" />

	<div class="hidden" aria-hidden="true" style="position:absolute;left:-9999px;height:0;overflow:hidden;">
		<label for="es-website-<?php echo esc_attr( $erfansanat_form_kind ); ?>"><?php esc_html_e( 'این فیلد را خالی بگذارید', 'erfansanat' ); ?></label>
		<input type="text" id="es-website-<?php echo esc_attr( $erfansanat_form_kind ); ?>" name="es_website" value="" tabindex="-1" autocomplete="off" />
	</div>

	<div class="grid gap-5 sm:grid-cols-2">
		<label class="flex flex-col gap-2">
			<span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'نام و نام خانوادگی *', 'erfansanat' ); ?></span>
			<div class="relative">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute right-4 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
				<input required type="text" name="name" maxlength="120" autocomplete="name" placeholder="<?php esc_attr_e( 'نام شما', 'erfansanat' ); ?>" value="<?php echo esc_attr( $erfansanat_current instanceof \WP_User ? (string) $erfansanat_current->display_name : '' ); ?>" class="<?php echo esc_attr( $erfansanat_field_class ); ?>" />
			</div>
		</label>

		<label class="flex flex-col gap-2">
			<span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'شماره تماس *', 'erfansanat' ); ?></span>
			<div class="relative">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute right-4 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>
				<input required type="tel" name="phone" maxlength="32" dir="ltr" autocomplete="tel" inputmode="tel" placeholder="۰۹۱۲ …" class="<?php echo esc_attr( $erfansanat_field_class ); ?>" />
			</div>
		</label>
	</div>

	<div class="grid gap-5 sm:grid-cols-2">
		<label class="flex flex-col gap-2">
			<span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'ایمیل', 'erfansanat' ); ?></span>
			<input type="email" name="email" maxlength="190" dir="ltr" autocomplete="email" placeholder="name@example.com" value="<?php echo esc_attr( $erfansanat_current instanceof \WP_User ? (string) $erfansanat_current->user_email : '' ); ?>" class="<?php echo esc_attr( $erfansanat_plain_class ); ?>" />
		</label>

		<label class="flex flex-col gap-2">
			<span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'سازمان / شهرداری', 'erfansanat' ); ?></span>
			<input type="text" name="org" maxlength="160" autocomplete="organization" placeholder="<?php esc_attr_e( 'نام سازمان یا شرکت (اختیاری)', 'erfansanat' ); ?>" class="<?php echo esc_attr( $erfansanat_plain_class ); ?>" />
		</label>
	</div>

	<label class="flex flex-col gap-2">
		<span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'توضیحات پروژه *', 'erfansanat' ); ?></span>
		<textarea required name="message" rows="4" maxlength="4000" placeholder="<?php esc_attr_e( 'نوع پروژه (میدان، خیابان، پل…)، محل اجرا و بازهٔ زمانی مورد نظر را بنویسید.', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-[14px] leading-7 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"></textarea>
	</label>

	<button type="submit" class="mt-1 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-700 px-5 py-3.5 text-[13.5px] font-extrabold text-white shadow-lg shadow-emerald-700/20 transition-colors hover:bg-emerald-800">
		<?php esc_html_e( 'ارسال درخواست', 'erfansanat' ); ?>
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
	</button>

	<p class="text-[11.5px] leading-6 text-slate-500">
		<?php esc_html_e( 'با ارسال این فرم، با تماس کارشناسان عرفان صنعت موافقت می‌کنید. اطلاعات شما فقط برای پاسخ به همین درخواست استفاده می‌شود.', 'erfansanat' ); ?>
	</p>
</form>

<div data-contact-success="true" class="mt-8 hidden flex-col items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-6">
	<span class="grid h-11 w-11 place-items-center rounded-full bg-emerald-600 text-white">
		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
	</span>
	<p class="text-[14px] font-extrabold text-emerald-900"><?php esc_html_e( 'درخواست شما ثبت شد.', 'erfansanat' ); ?></p>
	<p class="text-[12.5px] leading-7 text-emerald-800"><?php esc_html_e( 'کارشناسان ما در اولین فرصت با شما تماس می‌گیرند. برای پیگیری سریع‌تر می‌توانید با شمارهٔ زیر تماس بگیرید.', 'erfansanat' ); ?></p>
	<button type="button" data-contact-reset="true" class="rounded-xl border border-emerald-300 bg-white px-4 py-2 text-[12px] font-extrabold text-emerald-800 transition-colors hover:bg-emerald-100">
		<?php esc_html_e( 'ارسال پیام دیگر', 'erfansanat' ); ?>
	</button>
</div>
