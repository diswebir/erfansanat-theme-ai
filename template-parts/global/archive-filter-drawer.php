<?php
/** Mobile filter drawer shared by product, project, and blog archives. */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$type = isset($args['type']) && is_string($args['type']) ? sanitize_key($args['type']) : '';
if (!in_array($type, ['product', 'project', 'blog'], true)) return;
$labels = ['product' => 'فیلتر محصولات', 'project' => 'فیلتر پروژه‌ها', 'blog' => 'فیلتر مقالات'];
$title = $labels[$type];
$drawerId = $type . '-archive-filter-drawer';
?>
<div id="<?php echo esc_attr($drawerId); ?>" data-archive-drawer="true" class="hidden fixed inset-0 z-50 items-stretch justify-start" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr($title); ?>">
    <button type="button" data-archive-drawer-backdrop="true" class="absolute inset-0 h-full w-full es-archive-filter-drawer-backdrop" aria-label="بستن فیلترها"></button>
    <div class="relative z-10 flex h-full w-full max-w-sm flex-col overflow-y-auto bg-white p-5 shadow-2xl">
        <header class="mb-5 flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-lg font-black text-slate-900"><?php echo esc_html($title); ?></h2>
            <button type="button" data-archive-drawer-close="true" class="grid h-10 w-10 place-items-center rounded-xl border border-slate-200 text-slate-700" aria-label="بستن فیلترها">×</button>
        </header>
        <div class="flex-1 space-y-5">
            <?php if ($type === 'product') : ?>
                <?php if (\ErfanSanat\Theme\Filters\FilterRegistry::isEnabled('search')) : ?><label class="block text-sm font-bold">جستجو در محصولات<input type="search" data-product-search="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="نام محصول یا ویژگی"></label><?php endif; ?>
                <?php if (\ErfanSanat\Theme\Filters\FilterRegistry::isEnabled('true_sale')) : ?><fieldset class="space-y-2"><legend class="mb-2 text-sm font-black">نوع خرید</legend><?php foreach (['all' => 'همه محصولات', 'online' => 'خرید آنلاین', 'call' => 'استعلام قیمت'] as $mode => $label) : ?><label class="flex items-center gap-2 text-xs font-semibold"><input type="radio" name="purchase-mode-mobile" data-product-purchase-mode="<?php echo esc_attr($mode); ?>" value="<?php echo esc_attr($mode); ?>" <?php checked($mode, 'all'); ?>><?php echo esc_html($label); ?></label><?php endforeach; ?></fieldset><?php endif; ?>
                <?php if (\ErfanSanat\Theme\Filters\FilterRegistry::isEnabled('price')) : ?><label class="block text-sm font-black">حداکثر قیمت <span data-product-price-label="true">۳,۰۰۰,۰۰۰ تومان</span><input type="range" min="0" max="3000000" step="50000" value="3000000" data-product-price-range="true" class="mt-3 w-full accent-emerald-700"></label><?php endif; ?>
                <?php $categories = $args['category_terms'] ?? []; if (is_array($categories) && $categories !== []) : ?><fieldset><legend class="mb-2 text-sm font-black">دسته‌بندی</legend><?php foreach ($categories as $term) : if (!$term instanceof WP_Term) continue; ?><label class="mb-2 flex items-center gap-2 text-xs"><input type="checkbox" data-product-category-filter="<?php echo esc_attr($term->slug); ?>"><?php echo esc_html($term->name); ?></label><?php endforeach; ?></fieldset><?php endif; ?>
                <?php $attributes = $args['attribute_filters'] ?? []; if (is_array($attributes)) : foreach ($attributes as $taxonomy => $filter) : ?><fieldset><legend class="mb-2 text-sm font-black"><?php echo esc_html((string) ($filter['label'] ?? '')); ?></legend><?php foreach (($filter['terms'] ?? []) as $term) : if (!$term instanceof WP_Term) continue; ?><label class="mb-2 flex items-center gap-2 text-xs"><input type="checkbox" data-product-attribute-filter="<?php echo esc_attr((string) $taxonomy); ?>" data-product-attribute-value="<?php echo esc_attr($term->slug); ?>" <?php if ($taxonomy === 'pa_ip-rating') : ?>data-product-ip-filter="<?php echo esc_attr($term->name); ?>"<?php elseif ($taxonomy === 'pa_input-voltage') : ?>data-product-voltage-filter="<?php echo esc_attr($term->name); ?>"<?php endif; ?>><?php echo esc_html($term->name); ?></label><?php endforeach; ?></fieldset><?php endforeach; endif; ?>
            <?php elseif ($type === 'project') : ?>
                <label class="block text-xs font-bold">جستجو در پروژه‌ها<input type="search" data-project-search="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="نام پروژه، شهر یا نوع سازه"></label>
                <label class="block text-xs font-bold">نوع پروژه<select data-project-type-filter="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"><option value="همه">همه انواع</option><?php foreach (($args['types'] ?? []) as $term) : if ($term instanceof WP_Term) : ?><option value="<?php echo esc_attr($term->name); ?>" <?php selected((string) ($args['initial_type'] ?? 'همه'), $term->name); ?>><?php echo esc_html($term->name); ?></option><?php endif; endforeach; ?></select></label>
                <label class="block text-xs font-bold">شهر / محل اجرا<select data-project-location-filter="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"><option value="همه">همه شهرها</option><?php foreach (($args['cities'] ?? []) as $term) : if ($term instanceof WP_Term) : ?><option value="<?php echo esc_attr($term->name); ?>" <?php selected((string) ($args['initial_location'] ?? 'همه'), $term->name); ?>><?php echo esc_html($term->name); ?></option><?php endif; endforeach; ?></select></label>
                <label class="block text-xs font-bold">مرتب‌سازی<select data-project-sort="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm"><option value="newest">جدیدترین</option><option value="popular">پربازدیدترین</option><option value="rating">بالاترین امتیاز</option></select></label>
            <?php else : ?>
                <label class="block text-xs font-bold">جستجو در مقالات<input type="search" data-blog-search="true" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="عنوان یا موضوع مقاله"></label>
                <div><p class="mb-2 text-xs font-bold">دسته‌بندی مقاله</p><div class="flex flex-wrap gap-2"><button type="button" data-blog-cat-btn="همه" class="rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white">همه</button><?php foreach (($args['categories'] ?? []) as $category) : if ($category instanceof WP_Term) : ?><button type="button" data-blog-cat-btn="<?php echo esc_attr($category->name); ?>" class="rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-600"><?php echo esc_html($category->name); ?></button><?php endif; endforeach; ?></div></div>
            <?php endif; ?>
        </div>
        <footer class="mt-5 flex gap-3 border-t border-slate-100 pt-4"><button type="button" data-archive-drawer-clear="true" class="flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm font-bold text-slate-700">پاک‌کردن فیلتر</button><button type="button" data-archive-drawer-apply="true" class="flex-1 rounded-xl bg-emerald-700 px-4 py-3 text-sm font-extrabold text-white">اعمال فیلتر</button></footer>
    </div>
</div>
