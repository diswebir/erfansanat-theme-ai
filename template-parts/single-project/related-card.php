<?php
/**
 * کارت لایت پروژه مرتبط — برش بایت‌دقیق میرور
 * فرگمنت با temp/gen_p71.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_R = <<<'ESP71R'
<a href="{ES_URL}" class="group flex flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-50/50 transition-all duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:bg-white hover:shadow-md"><div class="relative h-48 overflow-hidden"><img src="{ES_IMG}" alt="{ES_TITLE}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"/><span class="absolute right-3.5 top-3.5 rounded-full bg-slate-950/75 px-3 py-1 text-[10.5px] font-bold text-white backdrop-blur-md">{ES_CHIP}</span></div><div class="flex flex-1 flex-col justify-between p-5"><div><h3 class="text-base font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700">{ES_TITLE}</h3><p class="mt-1.5 line-clamp-2 text-xs leading-6 text-slate-500">{ES_DESC}</p></div><div class="mt-4 flex items-center justify-between border-t border-slate-200/60 pt-3 text-xs font-bold"><span class="flex items-center gap-1 text-slate-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>{ES_CITY}</span><span class="font-extrabold text-emerald-700">مشاهده پروژه ←</span></div></div></a>
ESP71R;
$es_args = $args ?? [];
echo strtr($ES_R, [
    '{ES_URL}'   => esc_url((string) ($es_args['url'] ?? '')),
    '{ES_IMG}'   => esc_url((string) ($es_args['img'] ?? '')),
    '{ES_TITLE}' => esc_html((string) ($es_args['title'] ?? '')),
    '{ES_CHIP}'  => esc_html((string) ($es_args['chip'] ?? '')),
    '{ES_DESC}'  => esc_html((string) ($es_args['desc'] ?? '')),
    '{ES_CITY}'  => esc_html((string) ($es_args['city'] ?? '')),
]);
