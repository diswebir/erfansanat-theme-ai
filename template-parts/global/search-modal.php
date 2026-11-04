<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
?>
<div data-search-modal class="hidden border-t border-emerald-100 bg-white/98 py-5 shadow-2xl backdrop-blur-xl"><div class="mx-auto max-w-[900px] px-5"><div class="flex items-center gap-2"><input type="search" data-global-search-input class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="جستجوی محصول، پروژه یا مقاله…" autocomplete="off" /><button type="button" data-search-modal-close class="grid h-11 w-11 shrink-0 place-items-center rounded-xl border" aria-label="بستن جستجو">×</button></div><div class="mt-4 grid gap-4 md:grid-cols-2"><div><h3 class="mb-2 text-xs font-extrabold text-slate-400">محصولات</h3><div data-global-search-products class="space-y-2"></div></div><div><h3 class="mb-2 text-xs font-extrabold text-slate-400">پروژه‌ها</h3><div data-global-search-projects class="space-y-2"></div></div></div></div></div>
