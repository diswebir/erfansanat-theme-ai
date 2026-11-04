<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$term = $GLOBALS['es_product_archive_term'] ?? null;
if (!$term instanceof WP_Term) return;
?>
<header class="mb-8 rounded-3xl bg-white p-6 md:p-8" data-product-taxonomy-header><p class="mb-2 text-xs font-bold text-emerald-700">محصولات</p><h1 class="text-2xl font-black text-slate-900 md:text-3xl"><?php echo esc_html($term->name); ?></h1><?php if (trim($term->description) !== '') : ?><div class="mt-3 text-sm leading-7 text-slate-600"><?php echo wp_kses_post(wpautop($term->description)); ?></div><?php endif; ?></header>
