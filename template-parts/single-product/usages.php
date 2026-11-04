<?php
/**
 * کاربردهای محصول — چیپ‌های usage_product + لینک آرشیو ترم‌های usage_countr (ادغام dedup؛ برش بایت‌دقیق میرور)
 * فرگمنت با temp/gen_p64.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Data\RepeaterEngine;

$es_pid = (int) ($args['pid'] ?? 0);

$es_terms = [];
foreach (RepeaterEngine::read($es_pid, 'usage_countr')['rows'] as $es_row) {
    $es_tid = (int) ($es_row['id'] ?? 0);
    if ($es_tid < 1) {
        continue;
    }
    $es_term = get_term($es_tid, 'project_type');
    if (is_wp_error($es_term)) {
        continue;
    }
    $es_name = (string) ($es_term->name ?? '');
    if ($es_name === '') {
        continue;
    }
    $es_link = function_exists('get_term_link') ? get_term_link($es_term) : '';
    $es_terms[] = ['name' => $es_name, 'url' => is_string($es_link) ? $es_link : ''];
}
$es_term_names = array_column($es_terms, 'name');

$es_chips = [];
foreach (RepeaterEngine::read($es_pid, 'usage_product')['rows'] as $es_row) {
    $es_text = trim((string) ($es_row['add-new-usage'] ?? ''));
    if ($es_text === '' || in_array($es_text, $es_term_names, true)) {
        continue; // ادغام: ترم‌های همنام فقط یک‌بار به‌صورت لینک‌دار
    }
    $es_chips[] = $es_text;
}
if ($es_chips === [] && $es_terms === []) {
    return;
}

$ES_USE_HEAD = <<<'ESP64F'
<div class="rounded-2xl border border-emerald-100 bg-emerald-50/40 p-6"><h3 class="mb-4 flex items-center gap-2 text-base font-extrabold text-slate-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layers h-5 w-5 text-emerald-700" aria-hidden="true"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"></path><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"></path><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"></path></svg><span>کاربردهای اصلی در پروژه‌ها</span></h3><div class="flex flex-wrap gap-2">
ESP64F;
$ES_USE_CHIP = <<<'ESP64G'
<span class="rounded-xl border border-emerald-200/80 bg-white px-3.5 py-2 text-xs font-bold text-emerald-950 shadow-2xs">{ES_TEXT}</span>
ESP64G;
$ES_USE_MID = <<<'ESP64H'
</div></div><div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-6"><div class="mb-3.5 flex items-center gap-2 text-[13.5px] font-black text-slate-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-kanban h-4.5 w-4.5 text-emerald-700" aria-hidden="true"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"></path><path d="M8 10v4"></path><path d="M12 10v2"></path><path d="M16 10v6"></path></svg><span>آرشیو پروژه‌های مرتبط با این محصول:</span></div><div class="flex flex-wrap gap-2">
ESP64H;
$ES_USE_LINK = <<<'ESP64I'
<a href="{ES_URL}" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-3.5 py-2 text-xs font-extrabold text-emerald-900 shadow-2xs transition-all hover:border-emerald-500 hover:bg-emerald-700 hover:text-white"><span>{ES_NAME}</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-3.5 w-3.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a>
ESP64I;
$ES_USE_TAIL = <<<'ESP64J'
</div></div></div></div></div>
ESP64J;

echo $ES_USE_HEAD;
foreach ($es_chips as $es_text) {
    echo strtr($ES_USE_CHIP, ['{ES_TEXT}' => esc_html($es_text)]);
}
echo $ES_USE_MID;
foreach ($es_terms as $es_t) {
    echo strtr($ES_USE_LINK, ['{ES_URL}' => esc_url((string) $es_t['url']), '{ES_NAME}' => esc_html((string) $es_t['name'])]);
}
echo $ES_USE_TAIL;
