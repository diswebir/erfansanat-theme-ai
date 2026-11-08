<?php
/**
 * دراور موبایل فیلتر پروژه‌ها — برش بایت‌دقیق میرور (gen_p73)
 * args: total, archive_url, active_type, active_city, all_active
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$es_args = $args ?? [];
$es_total = max(0, (int) ($es_args['total'] ?? 0));
$es_archive_url = (string) ($es_args['archive_url'] ?? '');
$es_active_type = (string) ($es_args['active_type'] ?? '');
$es_active_city = (string) ($es_args['active_city'] ?? '');
$es_all_active = !empty($es_args['all_active']);

$ESP73_DH = <<<'ESP73DH'
<div data-archive-drawer="true" role="dialog" aria-modal="true" aria-label="فیلتر و آرشیو پروژه‌ها" class="hidden fixed inset-0 z-50 items-end justify-center bg-slate-950/60 p-0 backdrop-blur-xs sm:items-center sm:p-4 lg:hidden"><div data-archive-drawer-backdrop="true" class="absolute inset-0"></div><div class="relative z-10 flex max-h-[86vh] w-full max-w-lg flex-col overflow-hidden rounded-t-[2rem] bg-white shadow-2xl sm:rounded-[2rem]"><div class="flex items-center justify-between border-b border-slate-100 px-5 py-4"><div class="flex items-center gap-2.5"><span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sliders-horizontal h-4.5 w-4.5" aria-hidden="true"><path d="M10 5H3"></path><path d="M12 19H3"></path><path d="M14 3v4"></path><path d="M16 17v4"></path><path d="M21 12h-9"></path><path d="M21 19h-5"></path><path d="M21 5h-7"></path><path d="M8 10v4"></path><path d="M8 12H3"></path></svg></span><div><h3 class="text-[15px] font-black text-slate-900">فیلتر و آرشیو پروژه‌ها</h3><p class="text-[11.5px] font-semibold text-slate-500">انتخاب نوع سازه یا شهر محل اجرا</p></div></div><button type="button" data-archive-drawer-close="true" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-800 cursor-pointer" aria-label="بستن پنجره فیلتر"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-4.5 w-4.5" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg></button></div><div class="flex-1 space-y-5 overflow-y-auto p-5"><div><label for="project-modal-search" class="mb-2 block text-[12px] font-extrabold text-slate-700">جستجو در نام پروژه یا کارفرما</label><div class="relative"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input id="project-modal-search" type="search" data-project-search="true" placeholder="مثال: نیم هرم اهواز، تونل نوری..." class="w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-3.5 pr-10 text-[13px] font-medium text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none" value=""/></div></div><div><div class="mb-2.5 flex items-center gap-2 text-[12.5px] font-extrabold text-slate-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-kanban h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"></path><path d="M8 10v4"></path><path d="M12 10v2"></path><path d="M16 10v6"></path></svg><span>آرشیو بر اساس نوع پروژه</span></div><div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
ESP73DH;
$ESP73_DM = <<<'ESP73DM'
</div></div><div class="border-t border-slate-100 pt-4"><div class="mb-2.5 flex items-center gap-2 text-[12.5px] font-extrabold text-slate-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg><span>آرشیو بر اساس شهر و موقعیت اجرا</span></div><div class="grid grid-cols-2 gap-2">
ESP73DM;
$ESP73_DT = <<<'ESP73DT'
</div></div></div><div class="flex items-center gap-3 border-t border-slate-100 bg-slate-50 px-5 py-3.5"><button type="button" data-archive-drawer-close="true" class="flex-1 rounded-xl bg-emerald-700 py-3 text-[13px] font-black text-white shadow-sm transition-colors hover:bg-emerald-800 cursor-pointer">مشاهده نتایج (<span data-project-count="true">{ES_COUNT}</span> پروژه)</button><a href="{ES_ARCHIVE_URL}" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-[12.5px] font-bold text-slate-600 transition-colors hover:bg-slate-100">حذف فیلتر</a></div></div></div>
ESP73DT;
$ESP73_DALL_ON = <<<'ESP73DALLON'
<a href="{ES_ARCHIVE_URL}" class="flex items-center justify-between rounded-xl border px-3.5 py-2.5 text-[12.5px] font-bold transition-all border-emerald-700 bg-emerald-700 text-white"><span>همه پروژه‌ها</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-4 w-4 opacity-100" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></a>
ESP73DALLON;
$ESP73_DALL_OFF = <<<'ESP73DALLOFF'
<a href="{ES_ARCHIVE_URL}" class="flex items-center justify-between rounded-xl border px-3.5 py-2.5 text-[12.5px] font-bold transition-all border-slate-200 bg-slate-50/70 text-slate-700 hover:border-emerald-300"><span>همه پروژه‌ها</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-check h-4 w-4 opacity-0" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg></a>
ESP73DALLOFF;
$ESP73_DROW = <<<'ESP73DROW'
<a href="{ES_URL}" class="flex items-center justify-between rounded-xl border px-3.5 py-2.5 text-[12.5px] font-bold transition-all border-slate-200 bg-slate-50/70 text-slate-700 hover:border-emerald-300"><span>{ES_LABEL}</span><span class="text-[11px] opacity-80">({ES_COUNT})</span></a>
ESP73DROW;
$ESP73_DROW_ON = <<<'ESP73DROWON'
<a href="{ES_URL}" class="flex items-center justify-between rounded-xl border px-3.5 py-2.5 text-[12.5px] font-bold transition-all border-emerald-700 bg-emerald-700 text-white"><span>{ES_LABEL}</span><span class="text-[11px] opacity-80">({ES_COUNT})</span></a>
ESP73DROWON;
$ESP73_DCITY = <<<'ESP73DCITY'
<a href="{ES_URL}" class="flex items-center justify-between rounded-xl border px-3 py-2.5 text-[12px] font-bold transition-all border-slate-200 bg-white text-slate-700 hover:border-emerald-300"><span class="truncate">{ES_LABEL}</span><span class="text-[11px] opacity-75">({ES_COUNT})</span></a>
ESP73DCITY;
$ESP73_DCITY_ON = <<<'ESP73DCITYON'
<a href="{ES_URL}" class="flex items-center justify-between rounded-xl border px-3 py-2.5 text-[12px] font-bold transition-all border-emerald-800 bg-emerald-800 text-white"><span class="truncate">{ES_LABEL}</span><span class="text-[11px] opacity-75">({ES_COUNT})</span></a>
ESP73DCITYON;

$es_drow = static function (object $es_t, bool $es_on, string $tpl_on, string $tpl_off): string {
    return strtr($es_on ? $tpl_on : $tpl_off, [
        '{ES_URL}'   => esc_url((string) get_term_link($es_t)),
        '{ES_LABEL}' => esc_html((string) $es_t->name),
        '{ES_COUNT}' => esc_html(es_fa_num((string) ($es_t->count ?? 0))),
    ]);
};

$es_rows_t = strtr($es_all_active ? $ESP73_DALL_ON : $ESP73_DALL_OFF, ['{ES_ARCHIVE_URL}' => esc_url($es_archive_url)]);
foreach (get_terms(['taxonomy' => 'project_type', 'hide_empty' => false]) as $es_t) {
    if (is_wp_error($es_t) || !is_object($es_t)) {
        continue;
    }
    $es_rows_t .= $es_drow($es_t, (string) $es_t->slug === $es_active_type, $ESP73_DROW_ON, $ESP73_DROW);
}
$es_rows_c = '';
foreach (get_terms(['taxonomy' => 'city', 'hide_empty' => false]) as $es_t) {
    if (is_wp_error($es_t) || !is_object($es_t)) {
        continue;
    }
    $es_rows_c .= $es_drow($es_t, (string) $es_t->slug === $es_active_city, $ESP73_DCITY_ON, $ESP73_DCITY);
}

echo strtr($ESP73_DH, []) . $es_rows_t . strtr($ESP73_DM, []) . $es_rows_c . strtr($ESP73_DT, [
    '{ES_COUNT}'       => esc_html(es_fa_num((string) $es_total)),
    '{ES_ARCHIVE_URL}' => esc_url($es_archive_url),
]);
