<?php
/**
 * Quote (استعلام) popup form (P08-T014). A hidden modal shell + reference-
 * styled fields with real names, nonce and honeypot. Opened by inquiry CTAs
 * via [data-quote-open] buttons (product single uses data-quote-open="product"
 * — dependency recorded). Submission goes through es_quote_submit; success/
 * error states keep the reference card vocabulary.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

wp_enqueue_script( 'es-main' );
$es_qf_js = <<<JS
(function () {
	"use strict";
	function ready(fn) { document.readyState !== "loading" ? fn() : document.addEventListener("DOMContentLoaded", fn); }
	ready(function () {
		if (window.__esQuoteFormInit) return;
		window.__esQuoteFormInit = true;
		var modal = document.querySelector('[data-quote-modal="true"]');
		if (!modal) return;
		var form = modal.querySelector('[data-quote-form="true"]');
		var backdrop = modal.querySelector('[data-quote-backdrop="true"]');
		var closeBtns = modal.querySelectorAll('[data-quote-close="true"]');
		var open = function (kind, id, title) {
			var pid = modal.querySelector('input[name="es_qf_product_id"]');
			var qid = modal.querySelector('input[name="es_qf_project_id"]');
			var prod = modal.querySelector('input[name="es_qf_product"]');
			if (pid) pid.value = kind === "product" ? (id || "") : "";
			if (qid) qid.value = kind === "project" ? (id || "") : "";
			if (prod && title) prod.value = title;
			modal.classList.remove("hidden");
			modal.classList.add("flex");
		};
		var close = function () { modal.classList.add("hidden"); modal.classList.remove("flex"); };
		document.addEventListener("click", function (e) {
			var btn = e.target && e.target.closest ? e.target.closest('[data-quote-open]') : null;
			if (btn) { e.preventDefault(); open(btn.getAttribute("data-quote-open"), btn.getAttribute("data-quote-id"), btn.getAttribute("data-quote-title")); }
		});
		closeBtns.forEach(function (b) { b.addEventListener("click", close); });
		if (backdrop) backdrop.addEventListener("click", close);
		document.addEventListener("keydown", function (e) { if (e.key === "Escape") close(); });
		form.addEventListener("submit", function (e) {
			e.preventDefault();
			var config = window.ES_AJAX || {};
			var errorBox = form.querySelector('[data-quote-error="true"]');
			var successBox = modal.querySelector('[data-quote-success="true"]');
			var nonceField = form.querySelector('input[name="nonce"]');
			var showError = function (msg) { if (errorBox) { errorBox.textContent = msg; errorBox.classList.remove("hidden"); } };
			var body = new URLSearchParams(new FormData(form));
			body.set("action", "es_quote_submit");
			if (nonceField) body.set("nonce", nonceField.value);
			if (!config.url) { showError("ارسال در دسترس نیست. لطفاً صفحه را دوباره بارگذاری کنید."); return; }
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
					form.classList.add("hidden");
					if (successBox) { successBox.classList.remove("hidden"); successBox.classList.add("flex"); }
					return;
				}
				var data = (payload && payload.data) || {};
				showError(typeof data === "string" ? data : (data.message || "ارسال ناموفق بود. دوباره تلاش کنید."));
			}).catch(function () { form.classList.remove("opacity-60"); showError("خطای شبکه؛ اتصال خود را بررسی کنید."); });
		});
	});
})();
JS;
wp_add_inline_script( 'es-main', $es_qf_js, 'after' );
?>
<div data-quote-modal="true" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-950/60 p-0 backdrop-blur-xs sm:p-4" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'فرم استعلام قیمت', 'erfansanat' ); ?>">
	<div data-quote-backdrop="true" class="absolute inset-0"></div>
	<div class="relative z-10 max-h-[90dvh] w-full overflow-y-auto rounded-3xl border border-slate-200/90 bg-white p-6 shadow-[0_24px_60px_-18px_rgba(15,23,42,0.35)] sm:max-w-[440px]">
		<div class="mb-4 flex items-center justify-between">
			<h2 class="text-base font-black text-slate-900"><?php esc_html_e( 'استعلام قیمت روز', 'erfansanat' ); ?></h2>
			<button type="button" data-quote-close="true" aria-label="<?php esc_attr_e( 'بستن', 'erfansanat' ); ?>" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 text-slate-500 transition hover:bg-slate-50">
				<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
			</button>
		</div>
		<div data-quote-success="true" class="hidden mb-4 flex-col items-center gap-2 rounded-2xl border border-emerald-200 bg-emerald-50 p-6 text-center">
			<span class="grid h-12 w-12 place-items-center rounded-full bg-emerald-600 text-white"><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg></span>
			<h3 class="text-sm font-extrabold text-emerald-900"><?php esc_html_e( 'استعلام شما ثبت شد!', 'erfansanat' ); ?></h3>
			<p class="text-[12.5px] leading-6 text-emerald-800"><?php esc_html_e( 'کارشناسان فروش عرفان صنعت به‌زودی با شما تماس می‌گیرند.', 'erfansanat' ); ?></p>
		</div>
		<form data-quote-form="true" class="flex flex-col gap-4" onsubmit="return false;">
			<input type="hidden" name="nonce" value="<?php echo esc_attr( wp_create_nonce( 'es_ajax_quote_submit' ) ); ?>" />
			<input type="hidden" name="es_qf_product_id" value="" />
			<input type="hidden" name="es_qf_project_id" value="" />
			<input type="text" name="es_qf_hp" value="" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true" />
			<div data-quote-error="true" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-[12.5px] font-bold leading-6 text-red-800"></div>
			<label class="flex flex-col gap-1.5"><span class="text-[12.5px] font-bold text-slate-700"><?php esc_html_e( 'نام و نام خانوادگی *', 'erfansanat' ); ?></span><input required type="text" name="es_qf_name" placeholder="<?php esc_attr_e( 'نام شما', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"/></label>
			<label class="flex flex-col gap-1.5"><span class="text-[12.5px] font-bold text-slate-700"><?php esc_html_e( 'شماره تماس *', 'erfansanat' ); ?></span><input required type="tel" dir="rtl" name="es_qf_phone" placeholder="<?php esc_attr_e( '۰۹۱۲ ...', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"/></label>
			<label class="flex flex-col gap-1.5"><span class="text-[12.5px] font-bold text-slate-700"><?php esc_html_e( 'محصول / خدمت مورد نظر', 'erfansanat' ); ?></span><input type="text" name="es_qf_product" placeholder="<?php esc_attr_e( 'نام محصول (اختیاری — از فرم پر می‌شود)', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"/></label>
			<label class="flex flex-col gap-1.5"><span class="text-[12.5px] font-bold text-slate-700"><?php esc_html_e( 'تعداد / متراژ درخواستی', 'erfansanat' ); ?></span><input type="text" name="es_qf_quantity" placeholder="<?php esc_attr_e( 'مثلاً ۲۴ عدد یا ۱۲۰ متر', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"/></label>
			<label class="flex flex-col gap-1.5"><span class="text-[12.5px] font-bold text-slate-700"><?php esc_html_e( 'توضیحات *', 'erfansanat' ); ?></span><textarea required rows="3" name="es_qf_message" placeholder="<?php esc_attr_e( 'جزئیات استعلام را بنویسید', 'erfansanat' ); ?>" class="w-full resize-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] leading-6 text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"></textarea></label>
			<button type="submit" class="flex items-center justify-center gap-2 rounded-full bg-emerald-600 px-6 py-3.5 text-[14px] font-extrabold text-white transition-all hover:bg-emerald-700 cursor-pointer"><?php esc_html_e( 'ارسال درخواست استعلام', 'erfansanat' ); ?></button>
		</form>
	</div>
</div>
