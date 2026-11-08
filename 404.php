<?php
/**
 * صفحه ۴۰۴ — ساخت از کامپوننت‌های بایت‌دقیق میرور (backdrop/کرامب/فرم/قرص‌های لینک)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

get_header();
echo '<main class="min-h-screen bg-[#f8faf9]">';
$ES76_F = <<<'ESP76F'
<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36"><div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div><div class="light-dot-grid absolute inset-0 opacity-50"></div><div class="absolute -left-40 top-1/4 h-96 w-96 rounded-full bg-emerald-200/45 blur-[110px]"></div><div class="absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-teal-100/65 blur-[110px]"></div><div class="absolute left-[8%] top-0 h-full w-px bg-gradient-to-b from-transparent via-emerald-300/25 to-transparent"></div><div class="absolute right-[12%] top-0 h-full w-px bg-gradient-to-b from-transparent via-teal-300/20 to-transparent"></div></div><div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8"><nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="{ES_HOME_URL}" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span>صفحه اصلی</a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800">۴۰۴</span></span></nav></div><div class="relative z-10 mx-auto max-w-3xl px-5 pb-16 pt-10 text-center lg:pb-20 lg:pt-12"><div class="text-[5.5rem] font-black leading-none tracking-tight text-emerald-800/15 sm:text-[7.5rem]">۴۰۴</div><h1 class="mx-auto mt-6 max-w-4xl text-center text-[1.9rem] font-black leading-[1.5] tracking-[-0.012em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.4] mt-2">این صفحه در دسترس نیست</h1><p class="mx-auto mt-5 max-w-2xl text-center text-[15px] font-medium leading-9 text-slate-600">آدرس واردشده وجود ندارد یا جابه‌جا شده است. از جستجو استفاده کنید یا از دسترسی سریع زیر کمک بگیرید.</p><div class="mt-8">{ES_FORM}</div><div class="mt-6 flex flex-wrap items-center justify-center gap-2">{ES_QUICK}</div></div><div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div></section>
ESP76F;
$ES76_LON = <<<'ESP76L'
<a href="{URL}" class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors bg-emerald-700 text-white">{LABEL}</a>
ESP76L;
$ES76_LOFF = <<<'ESP76L'
<a href="{URL}" class="rounded-xl px-3.5 py-2 text-[12px] font-bold transition-colors border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300">{LABEL}</a>
ESP76L;

$es_quick = strtr($ES76_LON, ['{URL}' => esc_url(home_url('/')), '{LABEL}' => esc_html('صفحه اصلی')])
    . strtr($ES76_LOFF, ['{URL}' => esc_url(es_page_url('shop')), '{LABEL}' => esc_html('فروشگاه محصولات')])
    . strtr($ES76_LOFF, ['{URL}' => esc_url(home_url('/projects/')), '{LABEL}' => esc_html('پروژه‌ها')])
    . strtr($ES76_LOFF, ['{URL}' => esc_url(es_page_url('blog')), '{LABEL}' => esc_html('مقالات')]);

echo strtr($ES76_F, [
    '{ES_HOME_URL}' => esc_url(home_url('/')),
    '{ES_FORM}'     => get_search_form(),
    '{ES_QUICK}'    => $es_quick,
]);
echo '</main>';
get_footer();
