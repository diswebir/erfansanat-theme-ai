<?php
/**
 * پنل desc محصول — معرفی + ویژگی‌ها (product-feature) + ویژگی دو بخشی (product-feature2_copy)
 * + اطلاعیه before-sale-c + کاربردها؛ برش بایت‌دقیق میرور product/wall-washer.html
 * فرگمنت با temp/gen_p64.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

use ErfanSanat\Theme\Data\RepeaterEngine;

$es_pid = (int) ($args['pid'] ?? 0);
$es_title = get_the_title($es_pid);
$es_content = (string) get_post_field('post_content', $es_pid);
$es_en = (string) get_post_meta($es_pid, 'english_titl', true);
$es_en_html = $es_en !== '' ? '<span dir="ltr" class="rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-[11px] font-bold text-slate-500">' . esc_html($es_en) . '</span>' : '';

$es_features = [];
foreach (RepeaterEngine::read($es_pid, 'product-feature')['rows'] as $es_row) {
    $es_text = trim((string) ($es_row['add-new-feature'] ?? ''));
    if ($es_text !== '') {
        $es_features[] = $es_text;
    }
}
$es_f2 = [];
foreach (RepeaterEngine::read($es_pid, 'product-feature2_copy')['rows'] as $es_row) {
    $es_ft = trim((string) ($es_row['add-new-feature'] ?? ''));
    $es_fv = trim((string) ($es_row['vaule_of_variable'] ?? ''));
    if ($es_ft !== '' && $es_fv !== '') {
        $es_f2[] = ['t' => $es_ft, 'v' => $es_fv];
    }
}
$es_notice = trim((string) get_post_meta($es_pid, 'before-sale-c', true));

$es_data = apply_filters('es_product_features_args', [
    'content'   => $es_content,
    'features'  => $es_features,
    'feature2'  => $es_f2,
    'notice'    => $es_notice,
], $es_pid);
$es_content  = (string) ($es_data['content'] ?? '');
$es_features = (array) ($es_data['features'] ?? []);
$es_f2       = (array) ($es_data['feature2'] ?? []);
$es_notice   = (string) ($es_data['notice'] ?? '');
if ($es_content === '' && $es_features === [] && $es_f2 === [] && $es_notice === '') {
    return;
}

$ES_DESC_HEAD = <<<'ESP64A'
<div data-prod-tab-panel="desc" class="block"><div class="grid gap-10 lg:grid-cols-12"><div class="space-y-6 lg:col-span-7"><div><div class="flex flex-wrap items-center justify-between gap-2"><h2 class="text-xl font-extrabold text-slate-900">معرفی تخصصی {ES_TITLE}</h2>{ES_EN_HTML}</div>{ES_CONTENT}</div><div class="rounded-2xl border border-slate-200/80 bg-slate-50/50 p-5 sm:p-6">
ESP64A;
$ES_FEAT_HEAD = <<<'ESP64B'
<div class="mb-4 flex items-center gap-2 text-sm font-extrabold text-slate-900"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-4.5 w-4.5 text-emerald-600" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg><span>مزایا و ویژگی‌های برجسته محصول</span></div><div class="grid gap-3 sm:grid-cols-2">
ESP64B;
$ES_FEAT_ROW = <<<'ESP64C'
<div class="flex items-start gap-2.5 rounded-xl border border-slate-200/60 bg-white px-3.5 py-2.5 text-[13px] font-semibold leading-6 text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check mt-1 h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg><span>{ES_TEXT}</span></div>
ESP64C;
$ES_FEAT_MID = <<<'ESP64D'
</div></div>
ESP64D;
$ES_DESC_MID = <<<'ESP64E'
</div><div class="space-y-6 lg:col-span-5">
ESP64E;

echo strtr($ES_DESC_HEAD, ['{ES_TITLE}' => esc_html($es_title), '{ES_EN_HTML}' => $es_en_html, '{ES_CONTENT}' => $es_content]);
if ($es_features !== []) {
    echo $ES_FEAT_HEAD;
    foreach ($es_features as $es_text) {
        echo strtr($ES_FEAT_ROW, ['{ES_TEXT}' => esc_html($es_text)]);
    }
    echo $ES_FEAT_MID;
}
if ($es_f2 !== []) {
    // ویژگی کلیدی دو بخشی — رخداد میرور خالی است؛ الگو از کارت‌های خواهر میرور (کارت چکیده خرید) طراحی شد
    echo '<div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-5 sm:p-6"><div class="mb-4 flex items-center gap-2 text-sm font-extrabold text-slate-900"><span>مشخصات کلیدی در یک نگاه</span></div><div class="grid gap-2.5 sm:grid-cols-2">';
    foreach ($es_f2 as $es_pair) {
        echo '<div class="rounded-xl border border-slate-200/60 bg-white px-3.5 py-2.5"><div class="text-[11px] font-bold text-slate-500">' . esc_html((string) $es_pair['t']) . '</div><div class="mt-0.5 text-[13px] font-extrabold text-slate-800">' . esc_html((string) $es_pair['v']) . '</div></div>';
    }
    echo '</div></div>';
}
es_get_template('template-parts/single-product/notice.php', ['pid' => $es_pid, 'text' => $es_notice]);
echo $ES_DESC_MID;
es_get_template('template-parts/single-product/usages.php', ['pid' => $es_pid]);
