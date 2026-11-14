<?php
/**
 * Recruitment (استخدام) form — reference DOM with real names, nonce, honeypot
 * and AJAX submission through es_recruitment_submit (P08-T015). Optional resume
 * upload is validated server-side (extensions + size). Included by
 * page-templates/recruitment.php (registered dependency).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Forms\RecruitmentForm;

wp_enqueue_script( 'es-main' );
$es_rf_units  = isset( $args['units'] ) && is_array( $args['units'] ) ? $args['units'] : array();
$es_rf_list   = array();
foreach ( $es_rf_units as $es_rf_tab ) {
	$key = (string) ( $es_rf_tab['key'] ?? '' );
	if ( isset( RecruitmentForm::UNITS[ $key ] ) ) {
		$es_rf_list[ $key ] = (string) $es_rf_tab['label'];
	}
}
foreach ( RecruitmentForm::UNITS as $es_rf_key => $es_rf_label ) {
	$es_rf_list[ $es_rf_key ] = $es_rf_list[ $es_rf_key ] ?? $es_rf_label;
}
$es_rf_js = <<<JS
(function () {
	"use strict";
	function ready(fn) { document.readyState !== "loading" ? fn() : document.addEventListener("DOMContentLoaded", fn); }
	ready(function () {
		if (window.__esRecruitFormInit) return;
		window.__esRecruitFormInit = true;
		document.querySelectorAll('[data-recruit-form="true"]').forEach(function (form) {
			var errorBox = form.querySelector('[data-recruit-error="true"]');
			var successBox = document.querySelector('[data-recruit-success="true"]');
			var resetBtn = document.querySelector('[data-recruit-reset="true"]');
			var nonceField = form.querySelector('input[name="nonce"]');
			var showError = function (msg) { if (errorBox) { errorBox.textContent = msg; errorBox.classList.remove("hidden"); } };
			form.addEventListener("submit", function (e) {
				e.preventDefault();
				var config = window.ES_AJAX || {};
				if (!config.url) { showError("ارسال در دسترس نیست. لطفاً صفحه را دوباره بارگذاری کنید."); return; }
				var body = new FormData(form);
				body.set("action", "es_recruitment_submit");
				if (nonceField) body.set("nonce", nonceField.value);
				form.classList.add("opacity-60");
				fetch(config.url, { method: "POST", credentials: "same-origin", body: body }).then(function (res) { return res.json(); }).then(function (payload) {
					form.classList.remove("opacity-60");
					if (payload && payload.success) {
						form.reset();
						if (errorBox) errorBox.classList.add("hidden");
						form.classList.add("hidden");
						if (successBox) { successBox.classList.remove("hidden"); successBox.classList.add("flex"); }
						return;
					}
					var data = (payload && payload.data) || {};
					showError(typeof data === "string" ? data : (data.message || "ارسال ناموفق بود. دوباره تلاش کنید."));
				}).catch(function () { form.classList.remove("opacity-60"); showError("خطای شبکه؛ اتصال خود را بررسی کنید."); });
			});
			if (resetBtn) resetBtn.addEventListener("click", function () { if (errorBox) errorBox.classList.add("hidden"); });
		});
	});
})();
JS;
wp_add_inline_script( 'es-main', $es_rf_js, 'after' );
?>
<form data-recruit-form="true" class="flex flex-col gap-4" onsubmit="return false;">
	<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'es_ajax_recruitment_submit' ) ); ?>" />
	<input type="text" name="es_rf_hp" value="" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true" />
	<div data-recruit-error="true" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[12.5px] font-bold leading-6 text-red-800"></div>
	<div class="grid gap-4 sm:grid-cols-2">
		<div><label class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700"><?php esc_html_e( 'نام و نام خانوادگی *', 'erfansanat' ); ?></label><input type="text" name="es_rf_name" required placeholder="<?php esc_attr_e( 'مثال: علی رضایی', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-[13.5px] text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none"/></div>
		<div><label class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700"><?php esc_html_e( 'شماره تلفن همراه *', 'erfansanat' ); ?></label><input type="tel" name="es_rf_phone" required dir="ltr" placeholder="0913..." class="w-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-[13.5px] text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none"/></div>
	</div>
	<div class="grid gap-4 sm:grid-cols-2">
		<div><label class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700"><?php esc_html_e( 'واحد سازمانی مورد نظر *', 'erfansanat' ); ?></label><select name="es_rf_unit" required class="w-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-[13.5px] font-semibold text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none">
			<?php foreach ( $es_rf_list as $es_rf_key => $es_rf_label ) : ?><option value="<?php echo esc_attr( $es_rf_key ); ?>"><?php echo esc_html( $es_rf_label ); ?></option><?php endforeach; ?>
		</select></div>
		<div><label class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700"><?php esc_html_e( 'میزان سابقه کار مرتبط', 'erfansanat' ); ?></label><select name="es_rf_experience" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-[13.5px] font-semibold text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none"><option value="1-3"><?php esc_html_e( '۱ تا ۳ سال', 'erfansanat' ); ?></option><option value="3-5"><?php esc_html_e( '۳ تا ۵ سال', 'erfansanat' ); ?></option><option value="5+"><?php esc_html_e( 'بیش از ۵ سال', 'erfansanat' ); ?></option><option value="junior"><?php esc_html_e( 'کارآموزی / تازه فارغ‌التحصیل', 'erfansanat' ); ?></option></select></div>
	</div>
	<div><label class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700"><?php esc_html_e( 'خلاصه سوابق، مهارت‌ها و لینک رزومه *', 'erfansanat' ); ?></label><textarea name="es_rf_background" rows="4" required placeholder="<?php esc_attr_e( 'تحصیلات، مهارت‌های تخصصی و سوابق کاری خود را بنویسید...', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-[13.5px] text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none"></textarea></div>
	<div><label class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700"><?php esc_html_e( 'فایل رزومه (اختیاری — PDF/DOC/DOCX/RTF/TXT تا ۲ مگابایت)', 'erfansanat' ); ?></label><input type="file" name="es_rf_resume" accept=".pdf,.doc,.docx,.rtf,.txt" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-3 text-[12.5px] text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-[12px] file:font-extrabold file:text-emerald-800"/></div>
	<button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-800 px-6 py-3.5 text-[14px] font-black text-white shadow-sm transition-colors hover:bg-emerald-900 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send h-4 w-4" aria-hidden="true"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"></path><path d="m21.854 2.147-10.94 10.939"></path></svg><span><?php esc_html_e( 'ثبت و ارسال درخواست استخدام', 'erfansanat' ); ?></span></button>
</form>
<div data-recruit-success="true" class="hidden flex-col items-center justify-center rounded-2xl border border-emerald-200 bg-emerald-50/70 p-8 text-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-12 w-12 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg><h3 class="mt-3 text-lg font-black text-slate-900"><?php esc_html_e( 'رزومه و درخواست همکاری شما با موفقیت ثبت شد', 'erfansanat' ); ?></h3><p class="mt-1.5 text-[13.5px] text-slate-600"><?php esc_html_e( 'واحد منابع انسانی عرفان صنعت پس از بررسی اطلاعات، با شما تماس خواهد گرفت.', 'erfansanat' ); ?></p><button type="button" data-recruit-reset="true" class="mt-5 rounded-xl bg-emerald-800 px-5 py-2.5 text-[12.5px] font-bold text-white hover:bg-emerald-900 cursor-pointer"><?php esc_html_e( 'ارسال درخواست جدید', 'erfansanat' ); ?></button></div>
