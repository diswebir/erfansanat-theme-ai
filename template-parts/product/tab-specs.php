<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_product_id'] ?? get_the_ID());
if ($postId < 1) return;
$attributes = \ErfanSanat\Theme\Filters\FilterRegistry::attributes();
$rows = [];
foreach ($attributes as $taxonomy => $label) {
    $value = '';
    if (taxonomy_exists($taxonomy)) { $terms = get_the_terms($postId, $taxonomy); if (is_array($terms) && $terms !== []) $value = implode('، ', array_map(static fn(WP_Term $term): string => $term->name, $terms)); }
    if ($value === '') { $legacyValue = get_post_meta($postId, $taxonomy, true); if (is_scalar($legacyValue)) $value = trim((string) $legacyValue); }
    if ($value !== '') $rows[] = [$label, $value];
}
$ids = ['gs1_id' => 'شناسه GS1', 'national_code' => 'ایران‌کد / شناسه ملی', 'hs_id' => 'کد تعرفه HS', 'isic_id' => 'کد ISIC', 'cpc_id' => 'کد CPC'];
$identity = [];
foreach ($ids as $key => $label) { $value = trim((string) get_post_meta($postId, $key, true)); if ($value !== '') $identity[] = [$label, $value]; }
if ($rows === [] && $identity === []) return;
?>
<div class="space-y-8 rounded-3xl bg-white p-5 md:p-8" data-product-specs>
    <?php if ($rows !== []) : ?><section><h3 class="mb-4 text-lg font-black text-slate-900">مشخصات فنی محصول <span class="text-xs font-medium text-slate-500">(۱۸ ویژگی فنی)</span></h3><div class="overflow-hidden rounded-2xl border border-slate-200"><table class="w-full border-collapse text-right text-sm"><tbody><?php foreach ($rows as [$label, $value]) : ?><tr class="border-b border-slate-100 last:border-0"><th scope="row" class="w-1/3 bg-slate-50 px-4 py-3 font-bold text-slate-600"><?php echo esc_html($label); ?></th><td class="px-4 py-3 font-semibold text-slate-800"><?php echo esc_html($value); ?></td></tr><?php endforeach; ?></tbody></table></div></section><?php endif; ?>
    <?php if ($identity !== []) : ?><section><h3 class="mb-4 text-lg font-black text-slate-900">شناسه‌های رسمی و سامانه‌ای</h3><div class="grid gap-3 sm:grid-cols-2"><?php foreach ($identity as [$label, $value]) : ?><div class="rounded-xl border border-slate-200 p-4"><span class="block text-xs font-bold text-slate-500"><?php echo esc_html($label); ?></span><span class="mt-1 block font-mono text-sm font-extrabold text-slate-900"><?php echo esc_html($value); ?></span></div><?php endforeach; ?></div></section><?php endif; ?>
</div>
