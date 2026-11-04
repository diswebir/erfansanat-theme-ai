<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_product_id'] ?? get_the_ID());
if ($postId < 1) return;
$title = get_the_title($postId);
$subtitle = trim((string) get_post_meta($postId, 'zireonvan', true));
$englishTitle = trim((string) get_post_meta($postId, 'english_titl', true));
$terms = get_the_terms($postId, taxonomy_exists('product_cat') ? 'product_cat' : 'category');
?>
<header class="mb-6 space-y-3" data-product-header>
    <nav class="mb-4 text-xs font-medium text-slate-500" aria-label="breadcrumb">
        <ol class="flex flex-wrap items-center gap-2"><li><a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-emerald-700">خانه</a></li><li aria-hidden="true">/</li><li><a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>" class="hover:text-emerald-700">محصولات</a></li>
        <?php if (is_array($terms) && $terms !== []) : ?><li aria-hidden="true">/</li><li><?php echo esc_html($terms[0]->name); ?></li><?php endif; ?><li aria-hidden="true">/</li><li class="font-bold text-slate-800" aria-current="page"><?php echo esc_html($title); ?></li></ol>
    </nav>
    <?php if (is_array($terms) && $terms !== []) : ?><span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-800"><?php echo esc_html($terms[0]->name); ?></span><?php endif; ?>
    <h1 class="text-2xl font-black leading-tight text-slate-900 md:text-3xl"><?php echo esc_html($title); ?></h1>
    <?php if ($subtitle !== '') : ?><p class="text-sm leading-7 text-slate-600"><?php echo esc_html($subtitle); ?></p><?php endif; ?>
    <?php if ($englishTitle !== '') : ?><p class="text-xs font-semibold uppercase tracking-wide text-slate-500" lang="en"><?php echo esc_html($englishTitle); ?></p><?php endif; ?>
</header>
