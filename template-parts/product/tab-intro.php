<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_product_id'] ?? get_the_ID());
if ($postId < 1) return;
$content = (string) get_post_field('post_content', $postId);
$features = \ErfanSanat\Theme\Helpers\LegacyMeta::getRows($postId, 'product-feature');
$uses = \ErfanSanat\Theme\Helpers\LegacyMeta::getRows($postId, 'usage_product');
$usageTerms = [];
foreach (\ErfanSanat\Theme\Helpers\LegacyMeta::getIds($postId, 'usage_countr') as $termId) { $term = get_term($termId, 'project_type'); if ($term instanceof WP_Term && !is_wp_error($term)) $usageTerms[] = $term; }
?>
<div class="space-y-8 rounded-3xl bg-white p-5 md:p-8" data-product-intro>
    <?php if (trim($content) !== '') : ?><div class="prose prose-slate max-w-none text-sm leading-8"><?php echo apply_filters('the_content', $content); ?></div><?php endif; ?>
    <?php if ($features !== []) : ?><section><h3 class="mb-4 text-lg font-black text-slate-900">ویژگی‌ها و مشخصات محصول</h3><ul class="grid gap-2 sm:grid-cols-2"><?php foreach ($features as $row) : $label = trim((string) ($row['add-new-feature'] ?? '')); if ($label === '') continue; ?><li class="rounded-xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700"><span class="ml-2 text-emerald-700">✓</span><?php echo esc_html($label); ?></li><?php endforeach; ?></ul></section><?php endif; ?>
    <?php if ($uses !== []) : ?><section><h3 class="mb-4 text-lg font-black text-slate-900">کاربردها و موارد مصرف</h3><div class="grid gap-3 sm:grid-cols-2"><?php foreach ($uses as $row) : $label = trim((string) ($row['add-new-usage'] ?? '')); if ($label === '') continue; ?><article class="rounded-2xl border border-slate-200 p-4 text-sm leading-6 text-slate-700"><?php echo esc_html($label); ?></article><?php endforeach; ?></div></section><?php endif; ?>
    <?php if ($usageTerms !== []) : ?><section><h3 class="mb-3 text-lg font-black text-slate-900">مناسب برای پروژه‌های</h3><ul class="flex flex-wrap gap-2"><?php foreach ($usageTerms as $term) : ?><li><a href="<?php echo esc_url(get_term_link($term)); ?>" class="rounded-full bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-800"><?php echo esc_html($term->name); ?></a></li><?php endforeach; ?></ul></section><?php endif; ?>
</div>
