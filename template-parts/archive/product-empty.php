<?php
/**
 * جعبه حالت خالی آرشیو — برش بایت‌دقیق میرور shop.html
 * فرگمنت با temp/gen_p68.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_EMPTY = <<<'ESP68E'
<div data-product-empty="true" class="{ES_EMPTY_CLS} rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 py-16 text-center"><p class="text-[14.5px] font-extrabold text-slate-700">محصولی با این مشخصات یافت نشد.</p><p class="mt-1.5 text-[12.5px] text-slate-500">عبارت جستجو را تغییر دهید یا فیلترهای فعال را پاک کنید.</p><a href="{ES_SHOP_URL}" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-[12.5px] font-bold text-white hover:bg-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rotate-ccw h-3.5 w-3.5" aria-hidden="true"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path></svg><span>نمایش همه محصولات</span></a></div></div></div>
ESP68E;
$es_args = $args ?? [];
echo strtr($ES_EMPTY, [
    '{ES_EMPTY_CLS}' => !empty($es_args['show']) ? 'block' : 'hidden',
    '{ES_SHOP_URL}'  => esc_url((string) ($es_args['shop_url'] ?? es_page_url('shop'))),
]);
