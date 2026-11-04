<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$term = $GLOBALS['es_product_category_term'] ?? null;
if (!$term instanceof WP_Term) return;
$thumbnailId = (int) get_term_meta($term->term_id, 'thumbnail', true);
$thumbnail = $thumbnailId > 0 ? wp_get_attachment_image_url($thumbnailId, 'large') : false;
$faqs = \ErfanSanat\Theme\Helpers\LegacyMeta::parseRows(get_term_meta($term->term_id, 'faq', true));
$faqs = array_values(array_filter($faqs, static fn(array $row): bool => trim((string) ($row['name_question'] ?? '')) !== '' && trim((string) ($row['answer_question'] ?? '')) !== ''));
?>
<header class="mb-8 rounded-3xl bg-white p-6 md:p-8" data-product-category-header>
    <?php if (is_string($thumbnail) && $thumbnail !== '') : ?><img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr($term->name); ?>" class="mb-5 max-h-72 w-full rounded-2xl object-cover"><?php endif; ?>
    <p class="mb-2 text-xs font-bold text-emerald-700">دسته‌بندی محصولات</p><h1 class="text-2xl font-black text-slate-900 md:text-3xl"><?php echo esc_html($term->name); ?></h1>
    <?php if (trim($term->description) !== '') : ?><div class="mt-3 text-sm leading-7 text-slate-600"><?php echo wp_kses_post(wpautop($term->description)); ?></div><?php endif; ?>
    <?php if ($faqs !== []) : ?><div class="mt-6" data-faq-list="true"><h2 class="mb-3 text-lg font-black">سوالات متداول دسته</h2><div class="space-y-2"><?php foreach ($faqs as $index => $faq) : ?><article class="rounded-xl border border-slate-200"><button type="button" data-faq-toggle="<?php echo esc_attr((string) $index); ?>" class="w-full p-3 text-right text-sm font-bold"><?php echo esc_html((string) $faq['name_question']); ?><span data-faq-icon="<?php echo esc_attr((string) $index); ?>">+</span></button><div data-faq-body="<?php echo esc_attr((string) $index); ?>" class="hidden px-3 pb-3 text-sm leading-6"><?php echo wp_kses_post(wpautop((string) $faq['answer_question'])); ?></div></article><?php endforeach; ?></div></div><?php endif; ?>
</header>
