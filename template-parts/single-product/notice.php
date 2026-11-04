<?php
/**
 * نوار اطلاعیه قبل از خرید (before-sale-c) — جعبه آمبر میرور؛ مخفی اگر خالی
 * فرگمنت با temp/gen_p64.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$es_pid = (int) ($args['pid'] ?? 0);
$es_notice = trim((string) ($args['text'] ?? get_post_meta($es_pid, 'before-sale-c', true)));
if ($es_notice === '') {
    return;
}

$ES_NOTICE = <<<'ESP64K'
<div class="rounded-2xl border border-amber-200/90 bg-amber-50/70 p-5"><div class="flex items-center gap-2 text-xs font-black text-amber-950"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-alert h-4 w-4 shrink-0 text-amber-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" x2="12" y1="8" y2="12"></line><line x1="12" x2="12.01" y1="16" y2="16"></line></svg><span>راهنما و نکات قبل از ثبت سفارش:</span></div><p class="mt-1.5 text-[12.5px] leading-7 text-amber-900/90">{ES_TEXT}</p></div>
ESP64K;

echo strtr($ES_NOTICE, ['{ES_TEXT}' => esc_html($es_notice)]);
