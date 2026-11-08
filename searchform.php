<?php
/**
 * فرم جستجوی عمومی — کلاس‌های اینپوت بایت‌دقیق میرور blog.html
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES76_SF = <<<'ESP76SF'
<form role="search" method="get" class="es-searchform" action="{ES_ACTION}"><div class="relative mx-auto max-w-[30rem]"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search absolute right-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input type="text" name="s" placeholder="جستجو در سایت…" class="w-full rounded-2xl border border-emerald-200 bg-white py-4 pl-5 pr-13 text-[14px] font-medium text-slate-800 shadow-[0_18px_40px_-22px_rgba(0,80,40,0.18)] outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15" value="{ES_S}"/></div></form>
ESP76SF;

echo strtr($ES76_SF, [
    '{ES_ACTION}' => esc_url(home_url('/')),
    '{ES_S}'      => esc_attr((string) get_query_var('s')),
]);
