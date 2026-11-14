<?php
/**
 * Client feedback form (P08-T016). Standalone, reusable: rating 1..5 +
 * message, submitted via es_feedback_submit, stored with pending moderation.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

wp_enqueue_script( 'es-main' );
$es_fb_js = <<<JS
(function () {
	"use strict";
	function ready(fn) { document.readyState !== "loading" ? fn() : document.addEventListener("DOMContentLoaded", fn); }
	ready(function () {
		if (window.__esFeedbackFormInit) return;
		window.__esFeedbackFormInit = true;
		document.querySelectorAll('[data-feedback-form="true"]').forEach(function (form) {
			var errorBox = form.querySelector('[data-feedback-error="true"]');
			var nonceField = form.querySelector('input[name="nonce"]');
			var showError = function (msg) { if (errorBox) { errorBox.textContent = msg; errorBox.classList.remove("hidden"); } };
			form.addEventListener("submit", function (e) {
				e.preventDefault();
				var config = window.ES_AJAX || {};
				if (!config.url) { showError("ارسال در دسترس نیست. لطفاً صفحه را دوباره بارگذاری کنید."); return; }
				var body = new URLSearchParams(new FormData(form));
				body.set("action", "es_feedback_submit");
				if (nonceField) body.set("nonce", nonceField.value);
				form.classList.add("opacity-60");
				fetch(config.url, {
					method: "POST",
					credentials: "same-origin",
					headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
					body: body.toString()
				}).then(function (res) { return res.json(); }).then(function (payload) {
					form.classList.remove("opacity-60");
					if (payload && payload.success) {
						form.reset();
						if (errorBox) errorBox.classList.add("hidden");
						var ok = form.querySelector('[data-feedback-success="true"]');
						if (ok) { ok.classList.remove("hidden"); setTimeout(function () { ok.classList.add("hidden"); }, 6000); }
						return;
					}
					var data = (payload && payload.data) || {};
					showError(typeof data === "string" ? data : (data.message || "ارسال ناموفق بود. دوباره تلاش کنید."));
				}).catch(function () { form.classList.remove("opacity-60"); showError("خطای شبکه؛ اتصال خود را بررسی کنید."); });
			});
		});
	});
})();
JS;
wp_add_inline_script( 'es-main', $es_fb_js, 'after' );
?>
<form data-feedback-form="true" class="flex flex-col gap-4" onsubmit="return false;">
	<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'es_ajax_feedback_submit' ) ); ?>" />
	<input type="text" name="es_fb_hp" value="" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true" />
	<div data-feedback-error="true" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[12.5px] font-bold leading-6 text-red-800"></div>
	<div data-feedback-success="true" class="hidden rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[12.5px] font-bold leading-6 text-emerald-800"><?php esc_html_e( 'دیدگاه شما ثبت شد؛ پس از بررسی منتشر می‌شود.', 'erfansanat' ); ?></div>
	<div class="grid gap-4 sm:grid-cols-2">
		<label class="flex flex-col gap-1.5"><span class="text-[12.5px] font-bold text-slate-700"><?php esc_html_e( 'نام شما *', 'erfansanat' ); ?></span><input required type="text" name="es_fb_name" placeholder="<?php esc_attr_e( 'نام شما', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"/></label>
		<label class="flex flex-col gap-1.5"><span class="text-[12.5px] font-bold text-slate-700"><?php esc_html_e( 'امتیاز شما *', 'erfansanat' ); ?></span><select required name="es_fb_rating" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] font-semibold text-slate-800 outline-none transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10">
			<option value="5"><?php esc_html_e( '۵ — عالی', 'erfansanat' ); ?></option>
			<option value="4"><?php esc_html_e( '۴ — خیلی خوب', 'erfansanat' ); ?></option>
			<option value="3"><?php esc_html_e( '۳ — خوب', 'erfansanat' ); ?></option>
			<option value="2"><?php esc_html_e( '۲ — متوسط', 'erfansanat' ); ?></option>
			<option value="1"><?php esc_html_e( '۱ — ضعیف', 'erfansanat' ); ?></option>
		</select></label>
	</div>
	<label class="flex flex-col gap-1.5"><span class="text-[12.5px] font-bold text-slate-700"><?php esc_html_e( 'دیدگاه شما *', 'erfansanat' ); ?></span><textarea required rows="4" name="es_fb_message" placeholder="<?php esc_attr_e( 'تجربه همکاری با عرفان صنعت را بنویسید', 'erfansanat' ); ?>" class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] leading-6 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"></textarea></label>
	<button type="submit" class="inline-flex items-center justify-center gap-2 rounded-full bg-emerald-600 px-7 py-3.5 text-[14px] font-extrabold text-white transition-all hover:bg-emerald-700 cursor-pointer"><?php esc_html_e( 'ثبت دیدگاه', 'erfansanat' ); ?></button>
</form>
