<?php
/**
 * Shared WooCommerce product archive renderer for shop, category, and tag views.
 *
 * @package ErfanSanat\Theme
 *
 * @var array<string, mixed> $args Archive context and optional term.
 */

$context = isset($args['context']) ? sanitize_key((string) $args['context']) : 'shop';
$archiveTerm = isset($args['term']) && $args['term'] instanceof WP_Term ? $args['term'] : null;
$archiveQuery = isset($GLOBALS['wp_query']) && $GLOBALS['wp_query'] instanceof WP_Query ? $GLOBALS['wp_query'] : null;
$queriedProducts = $archiveQuery && is_array($archiveQuery->posts) ? $archiveQuery->posts : [];
$categoryTerms = get_terms([
    'taxonomy' => 'product_cat',
    'hide_empty' => false,
    'number' => 80,
    'orderby' => 'name',
    'order' => 'ASC',
]);
if (is_wp_error($categoryTerms) || !is_array($categoryTerms)) {
    $categoryTerms = [];
}
$tagTerms = [];
if ('product_tag' === $context) {
    $tagTerms = get_terms([
        'taxonomy' => 'product_tag',
        'hide_empty' => false,
        'number' => 80,
        'orderby' => 'name',
        'order' => 'ASC',
    ]);
    if (is_wp_error($tagTerms) || !is_array($tagTerms)) {
        $tagTerms = [];
    }
}

$archiveTitle = $archiveTerm instanceof WP_Term
    ? $archiveTerm->name
    : (function_exists('woocommerce_page_title') ? (string) woocommerce_page_title(false) : __('فروشگاه محصولات نورپردازی', 'erfansanat-theme'));
if ('' === trim($archiveTitle)) {
    $archiveTitle = __('فروشگاه محصولات نورپردازی', 'erfansanat-theme');
}
$termDescription = $archiveTerm instanceof WP_Term ? trim((string) term_description($archiveTerm->term_id, $archiveTerm->taxonomy)) : '';
$archiveDescription = '';
if (function_exists('do_action') && has_action('woocommerce_archive_description')) {
    ob_start();
    do_action('woocommerce_archive_description');
    $archiveDescription = (string) ob_get_clean();
}
if ('' === trim($archiveDescription) && $termDescription) {
    $archiveDescription = wp_kses_post($termDescription);
}

$termThumbnail = '';
$termFaqs = [];
if ($archiveTerm instanceof WP_Term && 'product_cat' === $archiveTerm->taxonomy) {
    $thumbnailValue = get_term_meta($archiveTerm->term_id, 'thumbnail', true);
    if (is_numeric($thumbnailValue) && (int) $thumbnailValue > 0) {
        $termThumbnail = wp_get_attachment_image_url((int) $thumbnailValue, 'large') ?: wp_get_attachment_url((int) $thumbnailValue);
    } elseif (is_string($thumbnailValue) && '' !== trim($thumbnailValue)) {
        $termThumbnail = esc_url_raw($thumbnailValue);
    }
    $faqValue = get_term_meta($archiveTerm->term_id, 'faq', true);
    if (class_exists(\ErfanSanat\Theme\Admin\TermMetaboxes::class)) {
        $termFaqs = \ErfanSanat\Theme\Admin\TermMetaboxes::decode_legacy_value($faqValue);
    } elseif (is_array($faqValue)) {
        $termFaqs = $faqValue;
    } elseif (is_string($faqValue)) {
        $decodedFaqs = json_decode($faqValue, true);
        if (!is_array($decodedFaqs)) {
            $decodedFaqs = maybe_unserialize($faqValue);
        }
        $termFaqs = is_array($decodedFaqs) ? $decodedFaqs : [];
    }
    $termFaqs = array_values(array_filter($termFaqs, static function ($row): bool {
        return is_array($row) && !empty($row['name_question']) && !empty($row['answer_question']);
    }));
}

$ipTerms = taxonomy_exists('pa_ip-rating') ? get_terms(['taxonomy' => 'pa_ip-rating', 'hide_empty' => false, 'number' => 30]) : [];
if (is_wp_error($ipTerms) || !is_array($ipTerms) || !$ipTerms) {
    $ipTerms = [];
    foreach (['IP68', 'IP67', 'IP65'] as $fallbackIp) {
        $ipTerms[] = (object) ['name' => $fallbackIp, 'slug' => sanitize_title($fallbackIp)];
    }
}
$voltageTerms = taxonomy_exists('pa_input-voltage') ? get_terms(['taxonomy' => 'pa_input-voltage', 'hide_empty' => false, 'number' => 30]) : [];
if (is_wp_error($voltageTerms) || !is_array($voltageTerms) || !$voltageTerms) {
    $voltageTerms = [];
    foreach (['24V DC', '220V AC'] as $fallbackVoltage) {
        $voltageTerms[] = (object) ['name' => $fallbackVoltage, 'slug' => sanitize_title($fallbackVoltage)];
    }
}

$renderTermLinks = static function (array $terms, string $taxonomy, string $selectedSlug = ''): void {
    foreach ($terms as $term) {
        if (!$term instanceof WP_Term) {
            continue;
        }
        $link = get_term_link($term);
        if (is_wp_error($link)) {
            continue;
        }
        $isSelected = $term->slug === $selectedSlug;
        ?>
        <a data-product-category-filter="<?php echo esc_attr('product_cat' === $taxonomy ? $term->slug : ''); ?>" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-bold transition <?php echo $isSelected ? 'bg-emerald-700 text-white' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800'; ?>" href="<?php echo esc_url($link); ?>">
            <span><?php echo esc_html($term->name); ?></span><span class="text-[10px] opacity-70"><?php echo esc_html(number_format_i18n((int) $term->count)); ?></span>
        </a>
        <?php
    }
};
$renderFilters = static function (string $suffix) use ($categoryTerms, $ipTerms, $voltageTerms, $context, $archiveTerm, $renderTermLinks): void {
    ?>
    <div class="space-y-5 rounded-3xl border border-slate-200/80 bg-white p-4 shadow-sm sm:p-5">
        <div>
            <label class="mb-2 block text-xs font-extrabold text-slate-800" for="product-search-<?php echo esc_attr($suffix); ?>"><?php echo esc_html__('جست‌وجو در محصولات', 'erfansanat-theme'); ?></label>
            <input id="product-search-<?php echo esc_attr($suffix); ?>" type="search" class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-xs" placeholder="<?php echo esc_attr__('نام، کد یا ویژگی محصول', 'erfansanat-theme'); ?>" data-product-search="true" />
        </div>
        <fieldset>
            <legend class="mb-2 text-xs font-extrabold text-slate-800"><?php echo esc_html__('وضعیت فروش', 'erfansanat-theme'); ?></legend>
            <div class="grid grid-cols-3 gap-1.5 text-[11px]">
                <?php foreach (['all' => __('همه', 'erfansanat-theme'), 'online' => __('خرید آنلاین', 'erfansanat-theme'), 'inquiry' => __('استعلامی', 'erfansanat-theme')] as $mode => $label) : ?>
                    <label class="flex cursor-pointer items-center justify-center gap-1 rounded-lg border border-slate-200 px-1.5 py-2 text-center font-bold text-slate-700 hover:border-emerald-400"><input class="accent-emerald-700" type="radio" name="purchase-mode-<?php echo esc_attr($suffix); ?>" value="<?php echo esc_attr($mode); ?>" data-product-purchase-mode="<?php echo esc_attr($mode); ?>" <?php checked('all', $mode); ?> /><span><?php echo esc_html($label); ?></span></label>
                <?php endforeach; ?>
            </div>
        </fieldset>
        <div>
            <label class="mb-2 flex items-center justify-between text-xs font-extrabold text-slate-800" for="product-price-<?php echo esc_attr($suffix); ?>"><span><?php echo esc_html__('بازه قیمت', 'erfansanat-theme'); ?></span><span data-product-price-label="true"><?php echo esc_html__('تا ۳ میلیون تومان', 'erfansanat-theme'); ?></span></label>
            <input id="product-price-<?php echo esc_attr($suffix); ?>" type="range" min="0" max="3000000" value="3000000" class="h-2 w-full cursor-pointer accent-emerald-700" data-product-price-range="true" />
        </div>
        <fieldset>
            <legend class="mb-2 text-xs font-extrabold text-slate-800"><?php echo esc_html__('استاندارد IP', 'erfansanat-theme'); ?></legend>
            <div class="space-y-2">
                <?php foreach ($ipTerms as $ipTerm) : ?>
                    <?php $ipValue = isset($ipTerm->name) ? (string) $ipTerm->name : ''; if ('' === $ipValue) { continue; } ?>
                    <label class="flex items-center gap-2 text-xs font-medium text-slate-600"><input type="checkbox" class="accent-emerald-700" value="<?php echo esc_attr($ipValue); ?>" data-product-ip-filter="<?php echo esc_attr($ipValue); ?>" /><span><?php echo esc_html($ipValue); ?></span></label>
                <?php endforeach; ?>
            </div>
        </fieldset>
        <fieldset>
            <legend class="mb-2 text-xs font-extrabold text-slate-800"><?php echo esc_html__('ولتاژ ورودی', 'erfansanat-theme'); ?></legend>
            <div class="space-y-2">
                <?php foreach ($voltageTerms as $voltageTerm) : ?>
                    <?php $voltageValue = isset($voltageTerm->name) ? (string) $voltageTerm->name : ''; if ('' === $voltageValue) { continue; } ?>
                    <label class="flex items-center gap-2 text-xs font-medium text-slate-600"><input type="checkbox" class="accent-emerald-700" value="<?php echo esc_attr($voltageValue); ?>" data-product-voltage-filter="<?php echo esc_attr($voltageValue); ?>" /><span><?php echo esc_html($voltageValue); ?></span></label>
                <?php endforeach; ?>
            </div>
        </fieldset>
        <div>
            <h3 class="mb-2 text-xs font-extrabold text-slate-800"><?php echo esc_html__('دسته‌بندی محصولات', 'erfansanat-theme'); ?></h3>
            <div class="max-h-80 space-y-1 overflow-y-auto">
                <?php if ('product_cat' === $context && $archiveTerm instanceof WP_Term) : ?>
                    <?php $catLink = get_term_link($archiveTerm); ?>
                    <?php if (!is_wp_error($catLink)) : ?><a data-product-category-filter="<?php echo esc_attr($archiveTerm->slug); ?>" class="flex items-center justify-between rounded-xl bg-emerald-700 px-3 py-2.5 text-xs font-bold text-white" href="<?php echo esc_url($catLink); ?>"><?php echo esc_html($archiveTerm->name); ?><span><?php echo esc_html(number_format_i18n((int) $archiveTerm->count)); ?></span></a><?php endif; ?>
                <?php else : ?>
                    <a data-product-category-filter="all" class="flex items-center justify-between rounded-xl px-3 py-2.5 text-xs font-bold text-slate-700 hover:bg-emerald-50" href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>"><?php echo esc_html__('همه محصولات', 'erfansanat-theme'); ?></a>
                <?php endif; ?>
                <?php $renderTermLinks($categoryTerms, 'product_cat', 'product_cat' === $context && $archiveTerm instanceof WP_Term ? $archiveTerm->slug : ''); ?>
            </div>
        </div>
        <?php if ('product_tag' === $context && $archiveTerm instanceof WP_Term) : ?>
            <p class="rounded-xl bg-slate-50 p-3 text-xs text-slate-600"><?php echo esc_html(sprintf(__('برچسب فعال: %s', 'erfansanat-theme'), $archiveTerm->name)); ?></p>
        <?php endif; ?>
    </div>
    <?php
};

$contextMarker = 'product_cat' === $context ? 'data-product-category-archive="true"' : ('product_tag' === $context ? 'data-product-tag-archive="true"' : 'data-product-shop-archive="true"');
$initialCategory = 'product_cat' === $context && $archiveTerm instanceof WP_Term ? $archiveTerm->slug : __('همه', 'erfansanat-theme');
$mainQueryCount = $archiveQuery ? (int) $archiveQuery->post_count : count($queriedProducts);
$archivePageCount = max(1, (int) ceil($mainQueryCount / 9));
$displayCount = $archiveQuery ? (int) $archiveQuery->found_posts : $mainQueryCount;
?>
<main class="relative z-10 mx-auto max-w-7xl px-5 pb-16 pt-9 lg:px-8" <?php echo $contextMarker; ?>>
    <section class="mb-8 overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-br from-white via-emerald-50/70 to-teal-50/70 shadow-sm">
        <div class="grid items-center gap-5 p-6 sm:p-8 <?php echo $termThumbnail ? 'md:grid-cols-[1fr_220px]' : ''; ?>">
            <div>
                <nav class="mb-3 text-xs font-semibold text-slate-500" aria-label="<?php echo esc_attr__('مسیر راهنما', 'erfansanat-theme'); ?>"><a class="hover:text-emerald-700" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('خانه', 'erfansanat-theme'); ?></a><span class="px-2" aria-hidden="true">/</span><a class="hover:text-emerald-700" href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>"><?php echo esc_html__('فروشگاه', 'erfansanat-theme'); ?></a><?php if ($archiveTerm instanceof WP_Term) : ?><span class="px-2" aria-hidden="true">/</span><span aria-current="page"><?php echo esc_html($archiveTerm->name); ?></span><?php endif; ?></nav>
                <h1 class="text-2xl font-black leading-relaxed text-slate-950 sm:text-3xl"><?php echo esc_html($archiveTitle); ?></h1>
                <?php if ($archiveDescription) : ?><div class="mt-3 max-w-3xl text-sm leading-8 text-slate-600"><?php echo wp_kses_post($archiveDescription); ?></div><?php endif; ?>
            </div>
            <?php if ($termThumbnail) : ?><img class="h-40 w-full rounded-2xl border border-white/80 object-cover shadow-sm md:h-44" src="<?php echo esc_url($termThumbnail); ?>" alt="<?php echo esc_attr($archiveTitle); ?>" loading="eager" /><?php endif; ?>
        </div>
    </section>

    <?php if ('product_tag' === $context && $tagTerms) : ?>
        <nav class="mb-6 flex flex-wrap gap-2" aria-label="<?php echo esc_attr__('برچسب‌های محصولات', 'erfansanat-theme'); ?>"><?php foreach ($tagTerms as $tagTerm) : ?><?php if ($tagTerm instanceof WP_Term) : ?><?php $tagLink = get_term_link($tagTerm); ?><?php if (!is_wp_error($tagLink)) : ?><a class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-600 hover:border-emerald-300 hover:text-emerald-800" href="<?php echo esc_url($tagLink); ?>"><?php echo esc_html($tagTerm->name); ?></a><?php endif; ?><?php endif; ?><?php endforeach; ?></nav>
    <?php endif; ?>

    <section class="grid items-start gap-6 lg:grid-cols-[260px_minmax(0,1fr)]" data-products-archive="true" data-initial-category="<?php echo esc_attr($initialCategory); ?>" data-product-archive-category="<?php echo esc_attr('product_cat' === $context && $archiveTerm instanceof WP_Term ? $archiveTerm->slug : ''); ?>" data-product-archive-tag="<?php echo esc_attr('product_tag' === $context && $archiveTerm instanceof WP_Term ? $archiveTerm->slug : ''); ?>" data-items-per-page="9">
        <aside class="hidden lg:block" aria-label="<?php echo esc_attr__('فیلترهای فروشگاه', 'erfansanat-theme'); ?>"><?php $renderFilters('desktop'); ?></aside>

        <div class="min-w-0">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white p-3.5 shadow-sm">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-700"><span class="rounded-full bg-emerald-50 px-3 py-1.5 text-emerald-800"><?php echo esc_html__('محصولات', 'erfansanat-theme'); ?></span><span data-product-count="true"><?php echo esc_html(number_format_i18n($displayCount)); ?></span><span><?php echo esc_html__('مورد', 'erfansanat-theme'); ?></span><span class="hidden rounded-full bg-emerald-100 px-2 py-1 text-[10px] text-emerald-800" data-product-active-purch-badge="true"><?php echo esc_html__('قابل خرید آنلاین', 'erfansanat-theme'); ?></span></div>
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 lg:hidden" data-archive-drawer-open="true"><?php echo esc_html__('فیلترها', 'erfansanat-theme'); ?></button>
                    <?php if (function_exists('do_action') && has_action('woocommerce_before_shop_loop')) : ?>
                        <?php ob_start(); do_action('woocommerce_before_shop_loop'); $loopToolbar = (string) ob_get_clean(); ?>
                        <?php $loopToolbar = str_replace('class="orderby"', 'class="orderby" data-product-sort="true"', $loopToolbar); echo $loopToolbar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output is generated by registered WooCommerce/plugin hooks. ?>
                    <?php else : ?>
                        <label class="flex items-center gap-2 text-xs font-bold text-slate-600"><span><?php echo esc_html__('مرتب‌سازی', 'erfansanat-theme'); ?></span><select class="rounded-lg border border-slate-200 bg-white px-2 py-2 text-xs" name="orderby" data-product-sort="true"><option value="menu_order"><?php echo esc_html__('پیش‌فرض', 'erfansanat-theme'); ?></option><option value="popularity"><?php echo esc_html__('محبوب‌ترین', 'erfansanat-theme'); ?></option><option value="rating"><?php echo esc_html__('بالاترین امتیاز', 'erfansanat-theme'); ?></option><option value="price"><?php echo esc_html__('ارزان‌ترین', 'erfansanat-theme'); ?></option><option value="price-desc"><?php echo esc_html__('گران‌ترین', 'erfansanat-theme'); ?></option></select></label>
                    <?php endif; ?>
                    <button type="button" data-product-filter-clear="true" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600 hover:border-emerald-300 hover:text-emerald-800"><?php echo esc_html__('پاک‌کردن فیلترها', 'erfansanat-theme'); ?></button>
                    <span class="sr-only" role="status" aria-live="polite" data-product-ajax-status="true"></span>
                </div>
            </div>

            <?php if (have_posts()) : ?>
                <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3" data-product-grid="true">
                    <?php $archiveIndex = 0; ?>
                    <?php while (have_posts()) : the_post(); ?>
                        <?php
                        $productId = (int) get_the_ID();
                        $wcProduct = function_exists('wc_get_product') ? wc_get_product($productId) : null;
                        $productPrice = is_object($wcProduct) && method_exists($wcProduct, 'get_price')
                            ? (float) $wcProduct->get_price()
                            : (float) get_post_meta($productId, '_price', true);
                        $saleMeta = get_post_meta($productId, 'true_sale', true);
                        if ('true' === strtolower((string) $saleMeta)) {
                            $isPurchasable = true;
                        } elseif ('false' === strtolower((string) $saleMeta)) {
                            $isPurchasable = false;
                        } else {
                            $isPurchasable = is_object($wcProduct) && method_exists($wcProduct, 'is_purchasable') ? (bool) $wcProduct->is_purchasable() : false;
                        }
                        $ipValue = is_object($wcProduct) && method_exists($wcProduct, 'get_attribute') ? (string) $wcProduct->get_attribute('pa_ip-rating') : '';
                        if ('' === $ipValue) { $ipValue = (string) get_post_meta($productId, 'pa_ip-rating', true); }
                        $voltageValue = is_object($wcProduct) && method_exists($wcProduct, 'get_attribute') ? (string) $wcProduct->get_attribute('pa_input-voltage') : '';
                        if ('' === $voltageValue) { $voltageValue = (string) get_post_meta($productId, 'pa_input-voltage', true); }
                        $views = get_post_meta($productId, 'number2-viewc', true);
                        if ('' === $views) { $views = get_post_meta($productId, 'number-viewc', true); }
                        $rating = is_object($wcProduct) && method_exists($wcProduct, 'get_average_rating') ? (string) $wcProduct->get_average_rating() : (string) get_post_meta($productId, '_wc_average_rating', true);
                        $productCategories = get_the_terms($productId, 'product_cat');
                        if (!is_array($productCategories) || is_wp_error($productCategories)) { $productCategories = []; }
                        $categoryNames = [];
                        foreach ($productCategories as $productCategory) { if ($productCategory instanceof WP_Term) { $categoryNames[] = $productCategory->name; } }
                        $categoryLabel = $categoryNames ? reset($categoryNames) : '';
                        $searchParts = [get_the_title($productId), get_the_excerpt($productId), get_post_meta($productId, 'zireonvan', true), get_post_meta($productId, 'english_titl', true)];
                        if (is_object($wcProduct) && method_exists($wcProduct, 'get_sku')) { $searchParts[] = $wcProduct->get_sku(); }
                        $searchParts = array_merge($searchParts, $categoryNames, [$ipValue, $voltageValue]);
                        $searchText = sanitize_text_field(implode(' ', array_filter(array_map('strval', $searchParts))));
                        ?>
                        <article class="min-w-0" data-product-item="true" data-product-category="<?php echo esc_attr($categoryLabel); ?>" data-product-categories="<?php echo esc_attr(implode('|', $categoryNames)); ?>" data-product-purchasable="<?php echo $isPurchasable ? 'true' : 'false'; ?>" data-product-price="<?php echo esc_attr((string) max(0, (int) round($productPrice))); ?>" data-product-ip="<?php echo esc_attr($ipValue); ?>" data-product-voltage="<?php echo esc_attr($voltageValue); ?>" data-product-views="<?php echo esc_attr(is_numeric($views) ? (string) max(0, (int) $views) : '0'); ?>" data-product-rating="<?php echo esc_attr(is_numeric($rating) ? $rating : '0'); ?>" data-product-search-text="<?php echo esc_attr($searchText); ?>">
                            <?php get_template_part('template-parts/cards/card-product', null, ['post_id' => $productId, 'index' => $archiveIndex, 'variant' => 'archive']); ?>
                        </article>
                        <?php $archiveIndex++; ?>
                    <?php endwhile; ?>
                </div>
                <div class="hidden rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-600" data-product-empty="true"><?php echo esc_html__('محصولی با این فیلترها یافت نشد.', 'erfansanat-theme'); ?></div>
                <div class="mt-7 flex flex-wrap items-center justify-center gap-2" data-pagination-root="true" data-per-page="9" aria-label="<?php echo esc_attr__('صفحه‌بندی محصولات', 'erfansanat-theme'); ?>">
                    <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600" data-page-prev="true"><?php echo esc_html__('قبلی', 'erfansanat-theme'); ?></button>
                    <?php for ($pageNumber = 1; $pageNumber <= min(12, max(1, $archivePageCount)); $pageNumber++) : ?><button type="button" class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-700 text-xs font-black text-white" data-page-btn="<?php echo esc_attr((string) $pageNumber); ?>" <?php echo 1 === $pageNumber ? 'aria-current="page"' : ''; ?>><?php echo esc_html(number_format_i18n($pageNumber)); ?></button><?php endfor; ?>
                    <span class="px-2 text-xs font-semibold text-slate-500" data-pagination-summary="true"></span>
                    <button type="button" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-600" data-page-next="true"><?php echo esc_html__('بعدی', 'erfansanat-theme'); ?></button>
                </div>
                <div class="woocommerce-pagination-fallback mt-7 text-center">
                    <?php
                    $hasWooPagination = function_exists('has_action') && has_action('woocommerce_after_shop_loop', 'woocommerce_pagination');
                    ob_start();
                    do_action('woocommerce_after_shop_loop');
                    $afterLoopMarkup = (string) ob_get_clean();
                    if ('' !== trim($afterLoopMarkup)) {
                        echo $afterLoopMarkup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output is generated by registered WooCommerce/plugin hooks.
                    } elseif (function_exists('woocommerce_pagination')) {
                        woocommerce_pagination();
                    } elseif ($archiveQuery && (int) $archiveQuery->max_num_pages > 1) {
                        echo wp_kses_post(paginate_links(['total' => (int) $archiveQuery->max_num_pages, 'current' => max(1, (int) get_query_var('paged')), 'type' => 'list']));
                    }
                    ?>
                </div>
            <?php else : ?>
                <?php ob_start(); do_action('woocommerce_no_products_found'); $noProductsMarkup = (string) ob_get_clean(); ?>
                <?php if ('' !== trim($noProductsMarkup)) : ?><?php echo $noProductsMarkup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- output is generated by registered WooCommerce/plugin hooks. ?><?php else : ?><p class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-600"><?php echo esc_html__('محصولی برای نمایش در این بخش وجود ندارد.', 'erfansanat-theme'); ?></p><?php endif; ?>
            <?php endif; ?>

            <div class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-950/60 p-0 backdrop-blur-sm sm:items-center" data-archive-drawer="true">
                <button type="button" class="absolute inset-0" aria-label="<?php echo esc_attr__('بستن فیلترها', 'erfansanat-theme'); ?>" data-archive-drawer-backdrop="true"></button>
                <div class="relative z-10 max-h-[85vh] w-full overflow-y-auto rounded-t-3xl bg-white p-4 sm:max-w-lg sm:rounded-3xl sm:p-6">
                    <div class="mb-4 flex items-center justify-between"><h2 class="text-base font-black text-slate-900"><?php echo esc_html__('فیلتر و دسته‌بندی', 'erfansanat-theme'); ?></h2><button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold" data-archive-drawer-close="true"><?php echo esc_html__('بستن', 'erfansanat-theme'); ?></button></div>
                    <?php $renderFilters('mobile'); ?>
                    <button type="button" class="mt-4 w-full rounded-xl bg-emerald-700 px-4 py-3 text-sm font-black text-white" data-archive-drawer-close="true"><?php echo esc_html__('نمایش محصولات', 'erfansanat-theme'); ?> (<span data-product-count="true"><?php echo esc_html(number_format_i18n($displayCount)); ?></span>)</button>
                </div>
            </div>
        </div>
    </section>

    <?php if ($termFaqs) : ?>
        <section class="mx-auto mt-12 max-w-5xl border-t border-slate-200 pt-8" aria-labelledby="product-category-faq-title">
            <h2 id="product-category-faq-title" class="mb-5 text-xl font-black text-slate-950"><?php echo esc_html(sprintf(__('پرسش‌های متداول درباره %s', 'erfansanat-theme'), $archiveTitle)); ?></h2>
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white" data-faq-list="true">
                <?php foreach ($termFaqs as $faqIndex => $faq) : ?>
                    <?php $question = is_scalar($faq['name_question'] ?? null) ? trim((string) $faq['name_question']) : ''; $answer = is_scalar($faq['answer_question'] ?? null) ? trim((string) $faq['answer_question']) : ''; ?>
                    <?php if ('' !== $question && '' !== $answer) : ?>
                        <article class="border-b border-slate-100 last:border-0"><button type="button" class="flex w-full items-center justify-between gap-3 px-5 py-4 text-right text-sm font-extrabold text-slate-800" data-faq-toggle="<?php echo esc_attr((string) $faqIndex); ?>"><span><?php echo esc_html($question); ?></span><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-100 text-lg text-emerald-800" data-faq-icon="<?php echo esc_attr((string) $faqIndex); ?>">+</span></button><div class="hidden border-t border-slate-100 bg-white px-5 py-4 text-sm leading-7 text-slate-600" data-faq-body="<?php echo esc_attr((string) $faqIndex); ?>"><?php echo nl2br(esc_html($answer)); ?></div></article>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>
