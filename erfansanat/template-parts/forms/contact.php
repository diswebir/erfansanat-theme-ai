<?php
/**
 * Contact form — reference DOM with real names, nonce, honeypot and an AJAX
 * handler (P08-T013). Included by template-parts/sections/contact/section-form.php
 * (registered dependency). Success/error boxes keep the reference classes so
 * main.js reset behaviour keeps working; the inline script wires the real
 * submission through the es_contact_submit endpoint and runs first, so the
 * legacy fake-success listener never fires.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

$es_cf_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};
$es_cf_input = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-[14px] text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10';
wp_enqueue_script( 'es-main' );
$es_cf_js = <<<JS
(function () {
	"use strict";
	function ready(fn) { document.readyState !== "loading" ? fn() : document.addEventListener("DOMContentLoaded", fn); }
	ready(function () {
		if (window.__esContactFormInit) return;
		var config = window.ES_AJAX || {};
		document.querySelectorAll('[data-contact-form="true"]').forEach(function (form) {
			var nonceField = form.querySelector('input[name="nonce"]');
			form.addEventListener("submit", function (e) {
				e.stopImmediatePropagation(); // the legacy fake-success listener must not run.
				e.preventDefault();
				var errorBox = form.querySelector('[data-contact-error="true"]');
				var successBox = document.querySelector('[data-contact-success="true"]');
				var resetBtn = document.querySelector('[data-contact-reset="true"]');
				var setBusy = function (on) { form.classList.toggle("opacity-60", on); };
				var showError = function (msg) {
					if (!errorBox) { window.alert(msg); return; }
					errorBox.textContent = msg; // textContent: escaped by construction (AC-03).
					errorBox.classList.remove("hidden");
				};
				var body = new URLSearchParams(new FormData(form));
				body.set("action", "es_contact_submit");
				if (nonceField) body.set("nonce", nonceField.value);
				if (!config.url) { showError("ارسال در دسترس نیست. لطفاً صفحه را دوباره بارگذاری کنید."); return; }
				setBusy(true);
				fetch(config.url, {
					method: "POST",
					credentials: "same-origin",
					headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
					body: body.toString()
				}).then(function (res) { return res.json(); }).then(function (payload) {
					setBusy(false);
					if (payload && payload.success) {
						form.reset();
						if (errorBox) errorBox.classList.add("hidden");
						form.classList.add("hidden");
						form.classList.remove("flex");
						if (successBox) { successBox.classList.remove("hidden"); successBox.classList.add("flex"); }
						return;
					}
					var data = (payload && payload.data) || {};
					var msg = typeof data === "string" ? data : (data.message || "ارسال ناموفق بود. دوباره تلاش کنید.");
					showError(msg);
				}).catch(function () {
					setBusy(false);
					showError("خطای شبکه؛ اتصال خود را بررسی کنید.");
				});
			});
			if (resetBtn) {
				resetBtn.addEventListener("click", function () {
					var errorBox = form.querySelector('[data-contact-error="true"]');
					if (errorBox) errorBox.classList.add("hidden");
				});
			}
		});
	});
})();
JS;
wp_add_inline_script( 'es-main', $es_cf_js, 'after' );
?>
<form data-contact-form="true" class="flex mt-8 flex-col gap-5" onsubmit="return false;">
	<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'es_ajax_contact_submit' ) ); ?>" />
	<input type="hidden" name="page" value="<?php echo esc_attr( (string) ( get_post_type() ? home_url( add_query_arg( array() ) ) : '' ) ); ?>" />
	<input type="text" name="es_cf_hp" value="" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true" />
	<div data-contact-error="true" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[12.5px] font-bold leading-6 text-red-800"></div>
	<div class="grid gap-5 sm:grid-cols-2">
		<label class="flex flex-col gap-2"><span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'نام و نام خانوادگی *', 'erfansanat' ); ?></span><div class="relative"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user absolute right-4 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg><input required type="text" name="es_cf_name" placeholder="<?php esc_attr_e( 'نام شما', 'erfansanat' ); ?>" class="<?php echo esc_attr( $es_cf_input ); ?>"/></div></label>
		<label class="flex flex-col gap-2"><span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'شماره تماس *', 'erfansanat' ); ?></span><div class="relative"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call absolute right-4 top-1/2 h-4.5 w-4.5 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a8 8 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><input required type="tel" dir="rtl" name="es_cf_phone" placeholder="<?php esc_attr_e( '۰۹۱۲ ...', 'erfansanat' ); ?>" class="<?php echo esc_attr( $es_cf_input ); ?>"/></div></label>
	</div>
	<label class="flex flex-col gap-2"><span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'سازمان / شهرداری', 'erfansanat' ); ?></span><input type="text" name="es_cf_organization" placeholder="<?php esc_attr_e( 'نام سازمان یا شرکت (اختیاری)', 'erfansanat' ); ?>" class="<?php echo esc_attr( $es_cf_input ); ?>"/></label>
	<label class="flex flex-col gap-2"><span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'توضیحات پروژه *', 'erfansanat' ); ?></span><textarea required rows="4" name="es_cf_message" placeholder="<?php esc_attr_e( 'نوع پروژه (میدان، خیابان، تونل نوری و...)، محل اجرا و توضیحات لازم', 'erfansanat' ); ?>" class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3.5 text-[14px] leading-7 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"></textarea></label>
	<button type="submit" class="group flex items-center justify-center gap-2.5 rounded-full bg-emerald-600 px-8 py-4 text-[15px] font-extrabold text-white transition-all hover:bg-emerald-700 hover:shadow-[0_10px_36px_-10px_rgba(150,105,105,.6)] cursor-pointer"><?php esc_html_e( 'ارسال درخواست مشاوره', 'erfansanat' ); ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send h-4.5 w-4.5 transition-transform group-hover:-translate-x-1" aria-hidden="true"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"></path><path d="m21.854 2.147-10.94 10.939"></path></svg></button>
</form>
