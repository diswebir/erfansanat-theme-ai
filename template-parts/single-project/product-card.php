<?php
/**
 * کارت محصول سینگل پروژه (کارت لایت میرور) — برش بایت‌دقیق
 * فرگمنت با temp/gen_p71.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_C = <<<'ESP71C'
<div custom="{ES_CUSTOM}"><a href="{ES_URL}" class="group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs transition-all duration-300 hover:-translate-y-1 hover:border-emerald-300 hover:shadow-lg"><div class="relative h-52 overflow-hidden"><img src="{ES_IMG}" alt="{ES_TITLE}" loading="lazy" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105"/><span class="absolute right-3.5 top-3.5 rounded-full bg-slate-950/75 px-3 py-1 text-[11px] font-bold text-white backdrop-blur-md">{ES_CHIP}</span></div><div class="flex flex-1 flex-col justify-between p-5"><div><h3 class="text-base font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700">{ES_TITLE}</h3><p class="mt-1.5 line-clamp-2 text-xs leading-6 text-slate-500">{ES_DESC}</p></div><div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-xs font-extrabold text-emerald-700"><span>مشاهده مشخصات فنی محصول</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></div></div></a></div>
ESP71C;
$es_args = $args ?? [];
echo strtr($ES_C, [
    '{ES_CUSTOM}' => esc_attr((string) ($es_args['custom'] ?? '0')),
    '{ES_URL}'   => esc_url((string) ($es_args['url'] ?? '')),
    '{ES_IMG}'   => esc_url((string) ($es_args['img'] ?? '')),
    '{ES_TITLE}' => esc_html((string) ($es_args['title'] ?? '')),
    '{ES_CHIP}'  => esc_html((string) ($es_args['chip'] ?? '')),
    '{ES_DESC}'  => esc_html((string) ($es_args['desc'] ?? '')),
]);
