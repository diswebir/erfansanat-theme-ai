<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_product_id'] ?? get_the_ID());
if ($postId < 1) return;
$rows = \ErfanSanat\Theme\Helpers\LegacyMeta::getRows($postId, 'product-feature2_copy');
$features = [];
foreach ($rows as $row) { $label = trim((string) ($row['add-new-feature'] ?? '')); $value = trim((string) ($row['vaule_of_variable'] ?? '')); if ($label !== '' || $value !== '') $features[] = [$label, $value]; }
if ($features === []) return;
?>
<section class="rounded-3xl border border-slate-200 bg-white p-5" data-key-features>
    <h2 class="mb-4 text-base font-black text-slate-900">ویژگی‌های کلیدی محصول</h2>
    <dl class="grid grid-cols-1 gap-2 sm:grid-cols-2"><?php foreach ($features as [$label, $value]) : ?><div class="flex items-center justify-between gap-3 rounded-xl bg-slate-50 px-3 py-3"><dt class="text-xs font-bold text-slate-600"><?php echo esc_html($label); ?></dt><dd class="text-xs font-extrabold text-slate-900"><?php echo esc_html($value); ?></dd></div><?php endforeach; ?></dl>
</section>
