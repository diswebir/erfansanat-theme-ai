<?php
/**
 * سایدبار فیلتر و آرشیو پروژه‌ها — برش بایت‌دقیق میرور projects.html
 * فرگمنت با temp/gen_p72.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_ASIDE = <<<'ESP72A'
<aside aria-label="فیلتر و آرشیو پروژه‌ها" class="hidden lg:col-span-4 lg:block xl:col-span-3"><div class="sticky top-28 space-y-5 rounded-[1.75rem] border border-slate-200/90 bg-white p-5 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.10)]"><div class="flex items-center justify-between border-b border-slate-100 pb-4"><div class="flex items-center gap-2.5"><span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sliders-horizontal h-4.5 w-4.5" aria-hidden="true"><path d="M10 5H3"></path><path d="M12 19H3"></path><path d="M14 3v4"></path><path d="M16 17v4"></path><path d="M21 12h-9"></path><path d="M21 19h-5"></path><path d="M21 5h-7"></path><path d="M8 10v4"></path><path d="M8 12H3"></path></svg></span><div><h2 class="text-[14.5px] font-black text-slate-900">فیلتر و آرشیو پروژه‌ها</h2><p class="text-[11.5px] font-semibold text-slate-500"><span data-project-count="true">{ES_COUNT}</span> پروژه در لیست</p></div></div></div><div><label for="project-sidebar-search" class="mb-2 block text-[12px] font-extrabold text-slate-700">جستجو در پروژه‌ها</label><div class="relative"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search pointer-events-none absolute right-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input id="project-sidebar-search" type="search" data-project-search="true" placeholder="نام پروژه، شهر یا کارفرما..." class="w-full rounded-xl border border-slate-200 bg-slate-50/70 py-2.5 pl-3.5 pr-10 text-[13px] font-medium text-slate-800 placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none" value=""/></div></div><div class="border-t border-slate-100 pt-4"><div class="mb-3 flex items-center gap-2 text-[12.5px] font-extrabold text-slate-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-kanban h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"></path><path d="M8 10v4"></path><path d="M12 10v2"></path><path d="M16 10v6"></path></svg><span>نوع پروژه (project_type)</span></div><nav aria-label="آرشیو نوع پروژه" class="space-y-1.5">{ES_TYPE_ROWS}</nav></div><div class="border-t border-slate-100 pt-4"><div class="mb-3 flex items-center gap-2 text-[12.5px] font-extrabold text-slate-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg><span>موقعیت پروژه (city)</span></div><nav aria-label="آرشیو شهر اجرای پروژه" class="grid grid-cols-2 gap-1.5">{ES_CITY_ROWS}</nav></div><div class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-4"><div class="text-[12.5px] font-extrabold text-emerald-950">مشاوره اجرای پروژه در شهر شما</div><p class="mt-1 text-[11.5px] leading-6 text-slate-600">طراحی سه‌بعدی، تولید سفارشی و اجرای کامل پروژه‌های شهری در سراسر ایران</p><a href="tel:031-91091011" class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-800 px-3.5 py-2.5 text-[12px] font-black text-white transition-colors hover:bg-emerald-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-3.5 w-3.5" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><span>۰۳۱ - ۹۱۰۹۱۰۱۱</span></a></div></div></aside>
ESP72A;

$ES_ROW_ON = <<<'ESP72RON'
<a href="{ES_URL}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all bg-emerald-700 text-white shadow-sm shadow-emerald-700/20"><span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-white"></span><span>{ES_LABEL}</span></span><span class="rounded-md px-2 py-0.5 text-[11px] font-extrabold bg-white/20 text-white">{ES_COUNT}</span></a>
ESP72RON;

$ES_ROW_OFF = <<<'ESP72ROFF'
<a href="{ES_URL}" class="flex items-center justify-between rounded-xl px-3.5 py-2.5 text-[12.5px] font-bold transition-all text-slate-700 hover:bg-emerald-50/70 hover:text-emerald-800"><span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-emerald-500/60"></span><span>{ES_LABEL}</span></span><span class="rounded-md px-2 py-0.5 text-[11px] font-extrabold bg-slate-100 text-slate-500">{ES_COUNT}</span></a>
ESP72ROFF;

$ES_ROW_CITY = <<<'ESP72RC'
<a href="{ES_URL}" class="flex items-center justify-between rounded-xl border px-2.5 py-2 text-[11.5px] font-bold transition-all border-slate-200/80 bg-slate-50/70 text-slate-700 hover:border-emerald-300 hover:bg-emerald-50/50"><span class="truncate">{ES_NAME}</span><span class="rounded px-1.5 py-0.5 text-[10px] font-extrabold bg-white text-slate-500">{ES_COUNT}</span></a>
ESP72RC;
$es_args = $args ?? [];
$es_count = max(0, (int) ($es_args['count'] ?? 0));
$es_active_type = (string) ($es_args['active_type'] ?? '');
$es_active_city = (string) ($es_args['active_city'] ?? '');
$es_projects_url = function_exists('es_page_url') ? es_page_url('projects') : home_url('/projects/');

$es_row = static function (string $tpl, string $url, string $label, string $count): string {
    return strtr($tpl, [
        '{ES_URL}'   => esc_url($url),
        '{ES_LABEL}' => esc_html($label),
        '{ES_NAME}'  => esc_html($label),
        '{ES_COUNT}' => esc_html($count),
    ]);
};

$es_types_html = $es_active_type === ''
    ? $es_row($ES_ROW_ON, $es_projects_url, 'همه انواع پروژه‌ها', es_fa_num((string) $es_count))
    : $es_row($ES_ROW_OFF, $es_projects_url, 'همه انواع پروژه‌ها', es_fa_num((string) $es_count));
foreach (get_terms(['taxonomy' => 'project_type', 'hide_empty' => false]) as $es_t) {
    if (is_wp_error($es_t) || !is_object($es_t)) {
        continue;
    }
    $es_on = (string) $es_t->slug === $es_active_type;
    $es_types_html .= $es_row($es_on ? $ES_ROW_ON : $ES_ROW_OFF, (string) get_term_link($es_t), (string) $es_t->name, es_fa_num((string) ($es_t->count ?? 0)));
}

$es_cities_html = '';
foreach (get_terms(['taxonomy' => 'city', 'hide_empty' => false]) as $es_t) {
    if (is_wp_error($es_t) || !is_object($es_t)) {
        continue;
    }
    $es_cities_html .= $es_row($ES_ROW_CITY, (string) get_term_link($es_t), (string) $es_t->name, es_fa_num((string) ($es_t->count ?? 0)));
}

echo strtr($ES_ASIDE, [
    '{ES_COUNT}'     => esc_html(es_fa_num((string) $es_count)),
    '{ES_TYPE_ROWS}' => $es_types_html,
    '{ES_CITY_ROWS}' => $es_cities_html,
]);
