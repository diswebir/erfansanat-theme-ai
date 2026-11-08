<?php
/**
 * سکشن فرصت‌های شغلی و فرم استخدام — برش بایت‌دقیق میرور recruitment.html (تسک ۷.۸)
 * تب‌های واحدها + موقعیت‌ها (Repeater) + فرم استخدام (data-contact-form — تا ۸.۶ نمایشی)
 * رندر: SectionManager → رپر template-parts/sections/ → این فایل
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

/** @var array $args {section_id: string, fields: array<string, mixed>, post_id: int} */

$es_f = $args['fields'] ?? [];

$ESRJB = <<<'ESRJ7B'
<button type="button" data-recruit-tab-btn="{ES_RJ_KEY}" class="rounded-xl px-4 py-2.5 text-[12.5px] font-extrabold transition-all cursor-pointer whitespace-nowrap {ES_RJ_BS}">{ES_RJ_BTN}</button>
ESRJ7B;

$ESRJP = <<<'ESRJ7P'
<div data-recruit-tab-panel="{ES_RJ_KEY}" class="{ES_RJ_PS}"><div class="mb-8 rounded-2xl border border-emerald-100 bg-emerald-50/50 p-6"><div class="flex flex-wrap items-center justify-between gap-3"><div><span class="rounded-lg bg-emerald-700 px-3 py-1 text-[11px] font-extrabold text-white">{ES_RJ_BADGE}</span><h3 class="mt-2.5 text-xl font-black text-slate-900">{ES_RJ_TITLE}</h3></div></div><p class="mt-2.5 text-[14px] leading-8 text-slate-600">{ES_RJ_DESC}</p></div><div class="grid gap-6 md:grid-cols-2">{ES_RJ_JOBS}</div></div>
ESRJ7P;

$ESRJJ = <<<'ESRJ7J'
<article class="card-light flex flex-col justify-between rounded-[1.75rem] border border-slate-200/90 bg-white p-6"><div><div class="mb-4 flex flex-wrap items-center gap-2 text-[11.5px] font-bold"><span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1 text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-briefcase h-3.5 w-3.5" aria-hidden="true"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path><rect width="20" height="14" x="2" y="6" rx="2"></rect></svg>{ES_RJ_TYPE}</span><span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-3.5 w-3.5" aria-hidden="true"><path d="M12 6v6l4 2"></path><circle cx="12" cy="12" r="10"></circle></svg>{ES_RJ_EXP}</span></div><h4 class="text-[17px] font-black text-slate-900">{ES_RJ_TITLE}</h4><div class="mt-2 flex items-center gap-1.5 text-[12.5px] font-semibold text-slate-500"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg><span>{ES_RJ_LOC}</span></div><div class="mt-5 border-t border-slate-100 pt-4"><div class="mb-2.5 text-[12.5px] font-extrabold text-slate-800">مهارت‌ها و شرایط احراز:</div><ul class="space-y-2">{ES_RJ_SKILLS}</ul></div></div><a href="#apply-form" class="mt-6 inline-flex items-center justify-center rounded-xl bg-emerald-50 px-4 py-3 text-[13px] font-extrabold text-emerald-900 transition-colors hover:bg-emerald-700 hover:text-white">ارسال رزومه برای این موقعیت</a></article>
ESRJ7J;

$ESRJS = <<<'ESRJ7S'
<li class="flex items-start gap-2 text-[13px] leading-6 text-slate-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check mt-1 h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg><span>{ES_RJ_SK}</span></li>
ESRJ7S;

$esrj_7 = <<<'ESRJ7'
<section class="mx-auto max-w-7xl px-5 py-14 lg:px-8 lg:py-20" data-recruit-tabs="true"><div custom="0"><div class="mb-10 flex flex-wrap items-center justify-between gap-4"><div><h2 class="text-2xl font-black text-slate-950">{ES_RJ_H2}</h2><p class="mt-1.5 text-[13.5px] font-medium text-slate-600">{ES_RJ_HD}</p></div><div class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xs">{ES_RJ_TABS}</div></div></div>{ES_RJ_PANELS}<div class="mt-16" custom="0.1"><div id="apply-form" class="scroll-mt-28 rounded-[2rem] border border-slate-200 bg-white p-7 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.10)] sm:p-10"><div class="grid gap-10 lg:grid-cols-12"><div class="space-y-4 lg:col-span-5"><span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3.5 py-1.5 text-[12px] font-extrabold text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 lucide-building-2 h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M10 12h4"></path><path d="M10 8h4"></path><path d="M14 21v-3a2 2 0 0 0-4 0v3"></path><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"></path><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"></path></svg>{ES_RJ_HB}</span><h2 class="text-xl font-black text-slate-950 sm:text-2xl">{ES_RJ_H2F}</h2><p class="text-[14px] leading-8 text-slate-600">{ES_RJ_HDF}</p><div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-[12.5px] leading-7 text-slate-600"><div><strong class="text-slate-900">{ES_RJ_AL}</strong>{ES_RJ_ADDR}</div><div class="mt-1"><strong class="text-slate-900">{ES_RJ_HL}</strong>{ES_RJ_HOURS}</div></div></div><div class="lg:col-span-7"><form data-contact-form="true" class="flex flex-col gap-4"{ES_RJ_FX}><div class="grid gap-4 sm:grid-cols-2"><div><label class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700">{ES_RJ_LN}</label><input type="text" required="" placeholder="{ES_RJ_PN}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none"/></div><div><label class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700">{ES_RJ_LT}</label><input type="tel" required="" dir="ltr" placeholder="{ES_RJ_PT}" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-right text-[13.5px] text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none"/></div></div><div class="grid gap-4 sm:grid-cols-2"><div><label class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700">{ES_RJ_LO}</label><select required="" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] font-semibold text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none">{ES_RJ_ORGS}</select></div><div><label class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700">{ES_RJ_LE}</label><select class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] font-semibold text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none"><option value="1-3">۱ تا ۳ سال</option><option value="3-5">۳ تا ۵ سال</option><option value="5+">بیش از ۵ سال</option><option value="junior">کارآموزی / تازه فارغ‌التحصیل</option></select></div></div><div><label class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700">{ES_RJ_LM}</label><textarea rows="4" required="" placeholder="{ES_RJ_PM}" class="w-full rounded-xl border border-slate-200 bg-slate-50 p-4 text-[13.5px] text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none"></textarea></div><button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-800 px-6 py-3.5 text-[14px] font-black text-white shadow-sm transition-colors hover:bg-emerald-900 cursor-pointer"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-send h-4 w-4" aria-hidden="true"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"></path><path d="m21.854 2.147-10.94 10.939"></path></svg><span>{ES_RJ_ST}</span></button></form><div data-contact-success="true" class="hidden flex-col items-center justify-center rounded-2xl border border-emerald-200 bg-emerald-50/70 p-8 text-center"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check h-12 w-12 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg><h3 class="mt-3 text-lg font-black text-slate-900">{ES_RJ_OKT}</h3><p class="mt-1.5 text-[13.5px] text-slate-600">{ES_RJ_OKD}</p><button type="button" data-contact-reset="true" class="mt-5 rounded-xl bg-emerald-800 px-5 py-2.5 text-[12.5px] font-bold text-white hover:bg-emerald-900 cursor-pointer">{ES_RJ_OKB}</button></div></div></div></div></div></section>
ESRJ7;

// دکمه‌های تب + پنل‌ها + optionهای فرم
$es_tabs_out = '';
$es_panels_out = '';
$es_orgs_out = '';
$es_jobs_all = array_values(is_array($es_f['jobs'] ?? null) ? $es_f['jobs'] : []);
foreach (array_values(is_array($es_f['tabs'] ?? null) ? $es_f['tabs'] : []) as $es_tab) {
    $es_key = (string) ($es_tab['key'] ?? '');
    $es_open = (int) ($es_tab['open'] ?? 0) === 1;
    $es_btn = (string) ($es_tab['btn'] ?? '');
    $es_tabs_out .= strtr($ESRJB, [
        '{ES_RJ_KEY}' => esc_attr($es_key),
        '{ES_RJ_BS}' => $es_open ? 'bg-emerald-800 text-white shadow-xs' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-900',
        '{ES_RJ_BTN}' => $es_btn,
    ]);
    $es_orgs_out .= '<option value="' . $es_btn . '">' . $es_btn . '</option>';

    $es_jobs_out = '';
    foreach ($es_jobs_all as $es_row) {
        if ((string) ($es_row['tab'] ?? '') !== $es_key) {
            continue;
        }
        $es_skills_out = '';
        foreach (preg_split('/\r\n|\r|\n/', trim((string) ($es_row['skills'] ?? ''))) as $es_sk) {
            if ($es_sk === '') {
                continue;
            }
            $es_skills_out .= strtr($ESRJS, ['{ES_RJ_SK}' => $es_sk]);
        }
        $es_jobs_out .= strtr($ESRJJ, [
            '{ES_RJ_TYPE}' => (string) ($es_row['type'] ?? ''),
            '{ES_RJ_EXP}' => (string) ($es_row['exp'] ?? ''),
            '{ES_RJ_TITLE}' => (string) ($es_row['title'] ?? ''),
            '{ES_RJ_LOC}' => (string) ($es_row['loc'] ?? ''),
            '{ES_RJ_SKILLS}' => $es_skills_out,
        ]);
    }
    $es_panels_out .= strtr($ESRJP, [
        '{ES_RJ_KEY}' => esc_attr($es_key),
        '{ES_RJ_PS}' => $es_open ? 'block' : 'hidden',
        '{ES_RJ_BADGE}' => (string) ($es_tab['badge'] ?? ''),
        '{ES_RJ_TITLE}' => (string) ($es_tab['title'] ?? ''),
        '{ES_RJ_DESC}' => (string) ($es_tab['desc'] ?? ''),
        '{ES_RJ_JOBS}' => $es_jobs_out,
    ]);
}

echo strtr($esrj_7, [
    '{ES_RJ_H2}' => (string) ($es_f['h2'] ?? ''),
    '{ES_RJ_HD}' => (string) ($es_f['desc'] ?? ''),
    '{ES_RJ_TABS}' => $es_tabs_out,
    '{ES_RJ_PANELS}' => $es_panels_out,
    '{ES_RJ_ORGS}' => $es_orgs_out,
    '{ES_RJ_FX}' => (string) apply_filters('es_recruit_form_extra', '', $es_f),
    '{ES_RJ_ST}' => (string) ($es_f['submit'] ?? ''),
    '{ES_RJ_OKT}' => (string) ($es_f['ok_t'] ?? ''),
    '{ES_RJ_OKD}' => (string) ($es_f['ok_d'] ?? ''),
    '{ES_RJ_OKB}' => (string) ($es_f['ok_btn'] ?? ''),
    '{ES_RJ_HB}' => (string) ($es_f['hr_badge'] ?? ''),
    '{ES_RJ_H2F}' => (string) ($es_f['hr_h2'] ?? ''),
    '{ES_RJ_HDF}' => (string) ($es_f['hr_desc'] ?? ''),
    '{ES_RJ_AL}' => (string) ($es_f['addr_label'] ?? ''),
    '{ES_RJ_ADDR}' => (string) ($es_f['addr'] ?? ''),
    '{ES_RJ_HL}' => (string) ($es_f['hours_label'] ?? ''),
    '{ES_RJ_HOURS}' => (string) ($es_f['hours'] ?? ''),
    '{ES_RJ_LN}' => (string) ($es_f['l_name'] ?? ''),
    '{ES_RJ_PN}' => (string) ($es_f['ph_name'] ?? ''),
    '{ES_RJ_LT}' => (string) ($es_f['l_tel'] ?? ''),
    '{ES_RJ_PT}' => (string) ($es_f['ph_tel'] ?? ''),
    '{ES_RJ_LO}' => (string) ($es_f['l_org'] ?? ''),
    '{ES_RJ_LE}' => (string) ($es_f['l_exp'] ?? ''),
    '{ES_RJ_LM}' => (string) ($es_f['l_msg'] ?? ''),
    '{ES_RJ_PM}' => (string) ($es_f['ph_msg'] ?? ''),
]);
