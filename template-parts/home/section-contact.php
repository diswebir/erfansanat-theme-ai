<?php
/**
 * سکشن تماس با ما (contact) — برش بایت‌دقیق میرور + فیلدهای SectionManager
 * مقادیر تماس: فیلد سکشن در اولویت؛ فیلد خالی → گزینه‌های مرکزی (es_contact_args).
 * فرگمنت با temp/gen_cm5.py از refs/contact.html برش خورده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Admin\OptionsOutput;

/** @var array {section_id: string, fields: array<string, mixed>, post_id: int} $args */

$es_f = $args['fields'] ?? [];

/** فیلتر es_contact_args — توسعه‌پذیری سکشن تماس */
$es_args = apply_filters('es_contact_section_args', ['fields' => $es_f, 'section_id' => (string) ($args['section_id'] ?? 'contact')]);
if (is_array($es_args)) {
    $es_f = is_array($es_args['fields'] ?? null) ? $es_args['fields'] : $es_f;
}

$es_c = OptionsOutput::contact_args();

// رزولوشن مقادیر: فیلد → گزینه مرکزی → خالی
$es_phone = trim((string) ($es_f['phone'] ?? ''));
$es_phone = $es_phone !== '' ? $es_phone : es_fa_num((string) $es_c['phone']);
$es_email = trim((string) ($es_f['email'] ?? ''));
$es_email = $es_email !== '' ? $es_email : (string) $es_c['email'];
$es_hours = trim((string) ($es_f['hours'] ?? ''));
$es_hours = $es_hours !== '' ? $es_hours : (string) $es_c['hours'];
$es_phone_url = trim((string) ($es_f['phone_url'] ?? ''));
$es_phone_url = $es_phone_url !== '' ? $es_phone_url : 'tel:' . (string) $es_c['phone'];

// مخفی‌سازی: بدون متن دعوت و بدون هیچ تماس
if ((string) ($es_f['desc'] ?? '') === '' && $es_phone === '' && $es_email === '') {
    return;
}

$ES_CONTACT = <<<'ESC10'
<section id="contact" class="relative scroll-mt-24 px-4 pt-10 pb-6 sm:px-6 sm:pt-14 sm:pb-8 lg:px-8"><div class="mx-auto max-w-[1300px] overflow-hidden rounded-3xl border border-slate-200/85 bg-white p-6 shadow-xs sm:p-10 lg:p-12"><div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-10"><div class="flex flex-col items-start gap-4 lg:col-span-7"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>{ES_KICKER}</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.35] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]">{ES_TITLE_PRE}<span class="text-emerald-700">{ES_HL}</span>{ES_TITLE_POST}</h2></div><div custom="0.12"><p class="max-w-xl text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8">{ES_DESC}</p></div><div class="flex w-full flex-col gap-3 pt-2 sm:w-auto sm:flex-row sm:items-center" custom="0.18"><a href="{ES_CTA1_URL}" class="group inline-flex items-center justify-center gap-2.5 rounded-xl bg-emerald-700 px-6 py-3.5 text-xs sm:text-sm font-black text-white transition-colors hover:bg-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><span>{ES_CTA1}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a><a href="{ES_CTA2_URL}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-[#f6f8f7] px-5 py-3.5 text-xs sm:text-sm font-extrabold text-slate-800 transition-colors hover:border-emerald-300 hover:bg-emerald-50/60"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-check2 lucide-file-check-2 h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M4 22h14a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v4"></path><path d="M14 2v4a2 2 0 0 0 2 2h4"></path><path d="m3 15 2 2 4-4"></path></svg><span>{ES_CTA2}</span></a></div></div><div class="flex flex-col gap-3 lg:col-span-5"><div custom="0.08"><div class="flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4 transition-colors hover:border-emerald-300 hover:bg-white"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-5 w-5" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg></span><div class="min-w-0 flex-1"><div class="text-[11.5px] font-semibold text-slate-500">{ES_PHONE_LABEL}</div><a href="{ES_PHONE_URL}" class="mt-0.5 block truncate text-sm sm:text-base font-black text-slate-900 tabular-nums transition-colors hover:text-emerald-700">{ES_PHONE}</a><div class="mt-0.5 text-[11px] font-medium text-slate-500">{ES_PHONE_SUB}</div></div></div></div><div custom="0.14"><div class="flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4 transition-colors hover:border-emerald-300 hover:bg-white"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail h-5 w-5" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg></span><div class="min-w-0 flex-1"><div class="text-[11.5px] font-semibold text-slate-500">{ES_EMAIL_LABEL}</div><a href="mailto:{ES_EMAIL}" dir="ltr" class="mt-0.5 block truncate text-sm sm:text-base font-black text-slate-900 tabular-nums transition-colors hover:text-emerald-700">{ES_EMAIL}</a><div class="mt-0.5 text-[11px] font-medium text-slate-500">{ES_EMAIL_SUB}</div></div></div></div><div custom="0.2"><div class="flex items-center gap-4 rounded-2xl border border-slate-200/80 bg-[#f6f8f7]/70 p-4 transition-colors hover:border-emerald-300 hover:bg-white"><span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border border-emerald-100 bg-white text-emerald-700 shadow-2xs"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock3 lucide-clock-3 h-5 w-5" aria-hidden="true"><path d="M12 6v6h4"></path><circle cx="12" cy="12" r="10"></circle></svg></span><div class="min-w-0 flex-1"><div class="text-[11.5px] font-semibold text-slate-500">{ES_HOURS_LABEL}</div><div class="mt-0.5 text-sm sm:text-base font-black text-slate-900 tabular-nums">{ES_HOURS}</div><div class="mt-0.5 text-[11px] font-medium text-slate-500">{ES_HOURS_SUB}</div></div></div></div></div></div></div></section>
ESC10;


echo strtr($ES_CONTACT, [
    '{ES_KICKER}'      => (string) ($es_f['kicker'] ?? ''),
    '{ES_TITLE_PRE}'   => (string) ($es_f['title_pre'] ?? ''),
    '{ES_HL}'          => (string) ($es_f['title_hl'] ?? ''),
    '{ES_TITLE_POST}'  => (string) ($es_f['title_post'] ?? ''),
    '{ES_DESC}'        => (string) ($es_f['desc'] ?? ''),
    '{ES_CTA1_URL}'    => es_link_url((string) ($es_f['cta1_url'] ?? '')),
    '{ES_CTA1}'        => (string) ($es_f['cta1_text'] ?? ''),
    '{ES_CTA2_URL}'    => es_link_url((string) ($es_f['cta2_url'] ?? '')),
    '{ES_CTA2}'        => (string) ($es_f['cta2_text'] ?? ''),
    '{ES_PHONE_LABEL}' => (string) ($es_f['phone_label'] ?? ''),
    '{ES_PHONE_URL}'   => $es_phone_url,
    '{ES_PHONE}'       => $es_phone,
    '{ES_PHONE_SUB}'   => (string) ($es_f['phone_sub'] ?? ''),
    '{ES_EMAIL_LABEL}' => (string) ($es_f['email_label'] ?? ''),
    '{ES_EMAIL}'       => $es_email,
    '{ES_EMAIL_SUB}'   => (string) ($es_f['email_sub'] ?? ''),
    '{ES_HOURS_LABEL}' => (string) ($es_f['hours_label'] ?? ''),
    '{ES_HOURS}'       => $es_hours,
    '{ES_HOURS_SUB}'   => (string) ($es_f['hours_sub'] ?? ''),
]);
