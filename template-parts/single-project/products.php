<?php
/**
 * سکشن محصولات پروژه (mortabet_mahsole_2) — برش بایت‌دقیق میرور؛ مخفی اگر خالی
 * فرگمنت با temp/gen_p71.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_P = <<<'ESP71P'
<section id="used-products" class="mx-auto max-w-6xl scroll-mt-28 px-5 pb-14 lg:px-8"><div class="mb-7 flex flex-wrap items-end justify-between gap-4"><div><div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-boxes h-4 w-4" aria-hidden="true"><path d="M2.97 12.92A2 2 0 0 0 2 14.63v3.24a2 2 0 0 0 .97 1.71l3 1.8a2 2 0 0 0 2.06 0L12 19v-5.5l-5-3-4.03 2.42Z"></path><path d="m7 16.5-4.74-2.85"></path><path d="m7 16.5 5-3"></path><path d="M7 16.5v5.17"></path><path d="M12 13.5V19l3.97 2.38a2 2 0 0 0 2.06 0l3-1.8a2 2 0 0 0 .97-1.71v-3.24a2 2 0 0 0-.97-1.71L17 10.5l-5 3Z"></path><path d="m17 16.5-5-3"></path><path d="m17 16.5 4.74-2.85"></path><path d="M17 16.5v5.17"></path><path d="M7.97 4.42A2 2 0 0 0 7 6.13v4.37l5 3 5-3V6.13a2 2 0 0 0-.97-1.71l-3-1.8a2 2 0 0 0-2.06 0l-3 1.8Z"></path><path d="M12 8 7.26 5.15"></path><path d="m12 8 4.74-2.85"></path><path d="M12 13.5V8"></path></svg><span>تولیدات کارخانه عرفان صنعت در این پروژه</span></div><h2 class="text-2xl font-extrabold text-slate-900">محصولات و تجهیزات استفاده‌شده</h2></div><a href="{ES_SHOP_URL}" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900"><span>مشاهده همه محصولات</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a></div><div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">{ES_PRODUCT_CARDS}</div></section>
ESP71P;
$es_args = $args ?? [];
$es_cards = (array) ($es_args['cards'] ?? []);
if ($es_cards === []) {
    return; // سکشن خالی مخفی
}
echo strtr($ES_P, [
    '{ES_SHOP_URL}'       => esc_url(es_page_url('shop')),
    '{ES_PRODUCT_CARDS}'  => (string) $es_args['cards_html'],
]);
