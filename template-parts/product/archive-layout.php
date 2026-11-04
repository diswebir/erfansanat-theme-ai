<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$title = isset($GLOBALS['es_archive_title']) ? (string) $GLOBALS['es_archive_title'] : 'فروشگاه محصولات';
$description = isset($GLOBALS['es_archive_description']) ? (string) $GLOBALS['es_archive_description'] : '';
$query = $GLOBALS['es_archive_query'] ?? $GLOBALS['wp_query'];
if (!$query instanceof WP_Query) $query = new WP_Query(['post_type' => 'product', 'post_status' => 'publish']);
$count = (int) $query->found_posts;
$attributeFilters = [];
foreach (\ErfanSanat\Theme\Filters\FilterRegistry::attributes() as $taxonomy => $label) {
    if (!\ErfanSanat\Theme\Filters\FilterRegistry::isEnabled('attr_' . $taxonomy) || !taxonomy_exists($taxonomy)) continue;
    $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]);
    if (is_array($terms) && $terms !== []) $attributeFilters[$taxonomy] = ['label' => $label, 'terms' => $terms];
}
$categoryTerms = taxonomy_exists('product_cat') && \ErfanSanat\Theme\Filters\FilterRegistry::isEnabled('product_cat') ? get_terms(['taxonomy' => 'product_cat', 'hide_empty' => true]) : [];
?>
<section class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8" data-products-archive="true">
    <?php if (empty($GLOBALS['es_archive_hide_header'])) : ?><header class="mb-8 rounded-3xl bg-white p-6"><p class="mb-2 text-xs font-bold text-emerald-700">عرفان صنعت اصفهان</p><h1 class="text-2xl font-black text-slate-900 md:text-3xl"><?php echo esc_html($title); ?></h1><?php if ($description !== '') : ?><div class="mt-3 max-w-4xl text-sm leading-7 text-slate-600"><?php echo wp_kses_post(wpautop($description)); ?></div><?php endif; ?></header><?php endif; ?>
    <div class="mb-4 flex justify-end lg:hidden"><button type="button" data-archive-drawer-open="true" aria-expanded="false" aria-controls="product-archive-filter-drawer" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-800 shadow-sm">فیلتر محصولات</button></div>
    <div class="grid gap-6 es-product-archive-columns">
        <aside class="hidden rounded-3xl border border-slate-200 bg-white p-5 lg:block" aria-label="فیلتر محصولات">
            <?php if (\ErfanSanat\Theme\Filters\FilterRegistry::isEnabled('search')) : ?><label class="mb-5 block text-sm font-bold">جستجو در محصولات<input type="search" data-product-search="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="نام محصول یا ویژگی"></label><?php endif; ?>
            <?php if (\ErfanSanat\Theme\Filters\FilterRegistry::isEnabled('true_sale')) : ?><fieldset class="mb-5 space-y-2"><legend class="mb-2 text-sm font-black">نوع خرید</legend><?php foreach (['all' => 'همه محصولات', 'online' => 'خرید آنلاین', 'call' => 'استعلام قیمت'] as $mode => $label) : ?><label class="flex items-center gap-2 text-xs font-semibold"><input type="radio" name="purchase-mode" data-product-purchase-mode="<?php echo esc_attr($mode); ?>" value="<?php echo esc_attr($mode); ?>" <?php checked($mode, 'all'); ?>><?php echo esc_html($label); ?></label><?php endforeach; ?></fieldset><?php endif; ?>
            <?php if (\ErfanSanat\Theme\Filters\FilterRegistry::isEnabled('price')) : ?><div class="mb-5"><label class="flex items-center justify-between text-sm font-black" for="product-price-range">حداکثر قیمت <span data-product-price-label="true">۳,۰۰۰,۰۰۰ تومان</span></label><input id="product-price-range" type="range" min="0" max="3000000" step="50000" value="3000000" data-product-price-range="true" class="mt-3 w-full accent-emerald-700"></div><?php endif; ?>
            <?php if (is_array($categoryTerms) && $categoryTerms !== []) : ?><fieldset class="mb-5"><legend class="mb-2 text-sm font-black">دسته‌بندی</legend><?php foreach ($categoryTerms as $term) : if (!$term instanceof WP_Term) continue; ?><label class="mb-2 flex items-center gap-2 text-xs"><input type="checkbox" data-product-category-filter="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></label><?php endforeach; ?></fieldset><?php endif; ?>
            <?php foreach ($attributeFilters as $taxonomy => $filter) : ?><fieldset class="mb-5"><legend class="mb-2 text-sm font-black"><?php echo esc_html($filter['label']); ?></legend><?php foreach ($filter['terms'] as $term) : if (!$term instanceof WP_Term) continue; ?><label class="mb-2 flex items-center gap-2 text-xs"><input type="checkbox" data-product-attribute-filter="<?php echo esc_attr($taxonomy); ?>" data-product-attribute-value="<?php echo esc_attr($term->slug); ?>" <?php if ($taxonomy === 'pa_ip-rating') : ?>data-product-ip-filter="<?php echo esc_attr($term->name); ?>"<?php elseif ($taxonomy === 'pa_input-voltage') : ?>data-product-voltage-filter="<?php echo esc_attr($term->name); ?>"<?php endif; ?>><?php echo esc_html($term->name); ?></label><?php endforeach; ?></fieldset><?php endforeach; ?>
        </aside>
        <div>
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3"><p class="text-sm text-slate-600"><strong data-product-count="true"><?php echo esc_html(number_format_i18n($count)); ?></strong> محصول</p><?php if (\ErfanSanat\Theme\Filters\FilterRegistry::isEnabled('sort')) : ?><label class="text-xs font-bold">مرتب‌سازی<select data-product-sort="true" class="mr-2 rounded-xl border border-slate-200 px-3 py-2"><option value="default">پیش‌فرض</option><option value="price-asc">ارزان‌ترین</option><option value="price-desc">گران‌ترین</option><option value="rating">پرامتیازترین</option><option value="views">پربازدیدترین</option></select></label><?php endif; ?></div>
            <div data-product-grid="true" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); $GLOBALS['es_card_post'] = get_post(); get_template_part('template-parts/cards/card-product'); endwhile; wp_reset_postdata(); unset($GLOBALS['es_card_post']); endif; ?>
            </div>
            <div data-product-empty="true" class="<?php echo $count > 0 ? 'hidden' : ''; ?> mt-6 rounded-2xl bg-slate-50 p-8 text-center text-sm text-slate-500">محصولی مطابق فیلترهای انتخابی پیدا نشد.</div>
            <?php if ($query->max_num_pages > 1) : ?><nav class="mt-8" aria-label="صفحه‌بندی محصولات"><?php echo wp_kses_post(paginate_links(['total' => (int) $query->max_num_pages, 'current' => max(1, (int) get_query_var('paged')), 'type' => 'list'])); ?></nav><?php endif; ?>
        </div>
    </div>
    <?php get_template_part('template-parts/global/archive-filter-drawer', null, ['type' => 'product', 'category_terms' => $categoryTerms, 'attribute_filters' => $attributeFilters]); ?>
</section>
