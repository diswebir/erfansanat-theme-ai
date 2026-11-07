<?php
/**
 * سکشن پروژه‌های مرتبط — برش بایت‌دقیق میرور؛ مخفی اگر خالی
 * فرگمنت با temp/gen_p71.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$ES_RP = <<<'ESP71RP'
<section class="border-t border-slate-200/80 bg-white py-14"><div class="mx-auto max-w-6xl px-5 lg:px-8"><div class="mb-8 flex flex-wrap items-end justify-between gap-4"><div><div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-folder-kanban h-4 w-4" aria-hidden="true"><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"></path><path d="M8 10v4"></path><path d="M12 10v2"></path><path d="M16 10v6"></path></svg><span>نمونه‌کارهای اجرایی در سراسر ایران</span></div><h2 class="text-2xl font-extrabold text-slate-900">سایر پروژه‌های شاخص عرفان صنعت</h2></div><a href="{ES_PROJECTS_URL}" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900"><span>مشاهده همه پروژه‌ها</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a></div><div class="grid gap-6 md:grid-cols-3">{ES_RELATED_CARDS}</div></div></section>
ESP71RP;
$es_args = $args ?? [];
if ((string) ($es_args['cards_html'] ?? '') === '') {
    return; // سکشن خالی مخفی
}
echo strtr($ES_RP, [
    '{ES_PROJECTS_URL}'   => esc_url(es_page_url('projects')),
    '{ES_RELATED_CARDS}'  => (string) $es_args['cards_html'],
]);
