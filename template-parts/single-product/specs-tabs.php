<?php
/**
 * تب‌های محصول + جدول مشخصات pa_* + شناسه‌های قانونی (برش بایت‌دقیق میرور product/wall-washer.html)
 * فرگمنت با temp/gen_p63.py تولید شده است.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

$es_pid = (int) ($args['pid'] ?? 0);
$es_title = get_the_title($es_pid);

// ردیف‌های مشخصات از اتریبیوت‌های ووکامرس (_product_attributes) — خالی‌ها حذف
$es_rows = [];
$es_attrs_raw = get_post_meta($es_pid, '_product_attributes', true);
if (is_array($es_attrs_raw)) {
    foreach ($es_attrs_raw as $es_key => $es_def) {
        if (!is_array($es_def)) {
            continue;
        }
        $es_lbl = (string) ($es_def['label'] ?? '');
        if ($es_lbl === '') {
            $es_lbl = (string) $es_key;
        }
        if (!empty($es_def['is_taxonomy'])) {
            $es_terms = get_the_terms($es_pid, (string) $es_key);
            $es_vals = is_array($es_terms) ? array_map(static fn($t): string => (string) ($t->name ?? ''), $es_terms) : [];
            $es_vals = array_values(array_filter($es_vals, static fn(string $v): bool => $v !== ''));
            $es_val = $es_vals === [] ? '' : implode('، ', $es_vals);
        } else {
            $es_val = trim((string) ($es_def['value'] ?? ''));
        }
        if ($es_val !== '') {
            $es_rows[] = ['label' => $es_lbl, 'value' => $es_val];
        }
    }
}

// شناسه‌های قانونی (فقط مقدار-دارها به ترتیب میرور)
$es_legal = [];
foreach ([
    ['product_id', 'شناسه فنی محصول در کارخانه', 'text-slate-900'],
    ['gs1_id', 'کد بین‌الملل GS1 (GTIN)', 'text-emerald-800'],
    ['national_code', 'کد ملی کالا (ایران‌کد)', 'text-slate-900'],
    ['hs_id', 'کد تعرفه هماهنگ گمرکی (HS Code)', 'text-slate-900'],
    ['isic_id', 'کد آیسیک صنعتی (ISIC)', 'text-slate-900'],
    ['cpc_id', 'کد طبقه‌بندی مرکزی محصول (CPC)', 'text-slate-900'],
] as $es_lg) {
    $es_lv = trim((string) get_post_meta($es_pid, $es_lg[0], true));
    if ($es_lv !== '') {
        $es_legal[] = ['label' => $es_lg[1], 'value' => $es_lv, 'cls' => $es_lg[2]];
    }
}

$es_rev_count = (int) get_comments_number($es_pid);
$es_data = apply_filters('es_product_specs_args', [
    'rows'        => $es_rows,
    'legal'       => $es_legal,
    'has_desc'    => false,
    'has_reviews' => false,
    'rev_count'   => $es_rev_count,
], $es_pid);
$es_rows   = (array) ($es_data['rows'] ?? []);
$es_legal  = (array) ($es_data['legal'] ?? []);
$es_has_desc = (bool) ($es_data['has_desc'] ?? false);
$es_has_reviews = (bool) ($es_data['has_reviews'] ?? false);
$es_rev_count = (int) ($es_data['rev_count'] ?? 0);

// فقط تب‌های دارای محتوا؛ فعال = اولین تب رندرشده
$es_tabs = [];
if ($es_has_desc) {
    $es_tabs[] = 'desc';
}
if ($es_rows !== [] || $es_legal !== []) {
    $es_tabs[] = 'specs';
}
if ($es_has_reviews && $es_rev_count > 0) {
    $es_tabs[] = 'reviews';
}
if ($es_tabs === []) {
    return;
}
$es_cls_active = 'bg-emerald-800 text-white shadow-sm';
$es_cls_idle = 'text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900';
$es_icon_active = 'text-white';
$es_icon_idle = 'text-emerald-700';

$ES_HEAD_A = <<<'ESP63A'
<section id="product-details" data-prod-tabs-container="true" class="mx-auto max-w-6xl scroll-mt-28 px-5 pb-14 lg:px-8"><div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-xs"><div class="flex gap-2 overflow-x-auto no-scrollbar border-b border-slate-100 bg-slate-50/70 p-3 sm:flex-wrap sm:px-6">
ESP63A;
$ES_BTN_DESC = <<<'ESP63B'
<button type="button" data-prod-tab-btn="desc" class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap {ES_BTN_CLS}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-4 w-4 {ES_ICON_CLS}" aria-hidden="true" data-prod-tab-icon="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg><span>{ES_LABEL}</span></button>
ESP63B;
$ES_BTN_SPECS = <<<'ESP63C'
<button type="button" data-prod-tab-btn="specs" class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap {ES_BTN_CLS}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-list h-4 w-4 {ES_ICON_CLS}" aria-hidden="true" data-prod-tab-icon="true"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><path d="M12 11h4"></path><path d="M12 16h4"></path><path d="M8 11h.01"></path><path d="M8 16h.01"></path></svg><span>{ES_LABEL}</span></button>
ESP63C;
$ES_BTN_REVIEWS = <<<'ESP63D'
<button type="button" data-prod-tab-btn="reviews" class="flex shrink-0 items-center gap-2 rounded-2xl px-3.5 py-2 sm:px-4 sm:py-2.5 text-xs sm:text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap {ES_BTN_CLS}"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-circle h-4 w-4 {ES_ICON_CLS}" aria-hidden="true" data-prod-tab-icon="true"><path d="M2.992 16.342a2 2 0 0 1 .094 1.167l-1.065 3.29a1 1 0 0 0 1.236 1.168l3.413-.998a2 2 0 0 1 1.099.092 10 10 0 1 0-4.777-4.719"></path></svg><span>نظرات کاربران ({ES_REV_COUNT})</span></button>
ESP63D;
$ES_HEAD_B = <<<'ESP63E'
</div><div class="p-6 sm:p-8 lg:p-10">
ESP63E;
$ES_SPECS_HEAD = <<<'ESP63F'
<div data-prod-tab-panel="specs" class="hidden"><div class="space-y-8"><div><div class="mb-5 flex flex-wrap items-center justify-between gap-2"><div><h2 class="text-xl font-extrabold text-slate-900">جدول کامل مشخصات فنی و مهندسی {ES_TITLE}</h2><p class="mt-1 text-xs font-medium text-slate-500">تولید و تست‌شده مطابق استانداردهای کنترل کیفیت کارخانه عرفان صنعت اصفهان</p></div></div><div class="overflow-hidden rounded-2xl border border-slate-200">
ESP63F;
$ES_ROW = <<<'ESP63G'
<div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3 sm:gap-4 {ES_BG} border-b border-slate-100 last:border-b-0"><div class="text-xs font-bold text-slate-500">{ES_LBL}</div><div class="text-sm font-extrabold text-slate-900 sm:col-span-2">{ES_VAL}</div></div>
ESP63G;
$ES_MID = <<<'ESP63H'
<div class="rounded-2xl border border-emerald-200/80 bg-emerald-50/40 p-6"><div class="mb-4"><h3 class="flex items-center gap-2 text-base font-black text-emerald-950"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-barcode h-5 w-5 text-emerald-700" aria-hidden="true"><path d="M3 5v14"></path><path d="M8 5v14"></path><path d="M12 5v14"></path><path d="M17 5v14"></path><path d="M21 5v14"></path></svg><span>شناسه‌های رسمی کالا (ویژه سامانه ستاد، تدارکات دولتی و ایران‌کد)</span></h3><p class="mt-1 text-xs font-medium text-slate-600">کدهای ثبت‌شده محصول جهت درج در اسناد مناقصات، فاکتور رسمی و سامانه تدارکات الکترونیکی دولت</p></div><div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
ESP63H;
$ES_CARD = <<<'ESP63I'
<div class="rounded-xl border border-emerald-100 bg-white p-3.5"><span class="block text-[11px] font-bold text-slate-500">{ES_LBL}</span><span dir="ltr" class="mt-1 block font-mono text-sm font-black {ES_VAL_CLS} tabular-nums">{ES_VAL}</span></div>
ESP63I;
$ES_SPECS_TAIL = <<<'ESP63J'
</div></div></div></div>
ESP63J;

echo $ES_HEAD_A;
$es_first_done = false;
foreach ($es_tabs as $es_tab) {
    $es_active = !$es_first_done;
    $es_first_done = true;
    $es_bcls = $es_active ? $es_cls_active : $es_cls_idle;
    $es_icls = $es_active ? $es_icon_active : $es_icon_idle;
    if ($es_tab === 'desc') {
        echo strtr($ES_BTN_DESC, ['{ES_LABEL}' => 'معرفی و کاربردها', '{ES_BTN_CLS}' => $es_bcls, '{ES_ICON_CLS}' => $es_icls]);
    } elseif ($es_tab === 'specs') {
        echo strtr($ES_BTN_SPECS, ['{ES_LABEL}' => 'مشخصات فنی و شناسه\u200cها', '{ES_BTN_CLS}' => $es_bcls, '{ES_ICON_CLS}' => $es_icls]);
    } else {
        echo strtr($ES_BTN_REVIEWS, ['{ES_REV_COUNT}' => es_fa_num((string) $es_rev_count), '{ES_BTN_CLS}' => $es_bcls, '{ES_ICON_CLS}' => $es_icls]);
    }
}
echo $ES_HEAD_B;
if (in_array('specs', $es_tabs, true)) {
    echo strtr($ES_SPECS_HEAD, ['{ES_TITLE}' => esc_html($es_title)]);
    foreach ($es_rows as $es_ri => $es_row) {
        echo strtr($ES_ROW, ['{ES_BG}' => $es_ri % 2 === 0 ? 'bg-slate-50/70' : 'bg-white', '{ES_LBL}' => esc_html((string) $es_row['label']), '{ES_VAL}' => esc_html((string) $es_row['value'])]);
    }
    echo '</div></div>';
    echo $ES_MID;
    foreach ($es_legal as $es_lgc) {
        echo strtr($ES_CARD, ['{ES_LBL}' => esc_html((string) $es_lgc['label']), '{ES_VAL}' => esc_html((string) $es_lgc['value']), '{ES_VAL_CLS}' => (string) $es_lgc['cls']]);
    }
    echo $ES_SPECS_TAIL;
}
echo '</div></div></section>';
