<?php
/**
 * Dynamic WooCommerce product detail page with legacy Erfan Sanat metadata support.
 *
 * @package ErfanSanat\Theme
 */

defined('ABSPATH') || exit;

get_header();

global $product;
if (!isset($product)) {
    $product = null;
}
$productId = (int) get_queried_object_id();
if (!$productId) {
    $productId = (int) get_the_ID();
}
if (function_exists('wc_get_product') && (!isset($product) || !$product || !is_object($product) || !method_exists($product, 'get_id') || (int) $product->get_id() !== $productId)) {
    $product = wc_get_product($productId);
}

$productPost = $productId ? get_post($productId) : null;
if (!$productPost instanceof WP_Post || 'product' !== $productPost->post_type) :
    ?>
    <main class="mx-auto max-w-6xl px-5 py-24 lg:px-8" id="product-details">
        <p class="rounded-2xl border border-slate-200 bg-white p-6 text-center text-slate-600"><?php echo esc_html__('این محصول در دسترس نیست.', 'erfansanat-theme'); ?></p>
    </main>
    <?php
    get_footer();
    return;
endif;

$productMeta = class_exists(\ErfanSanat\Theme\Admin\ProductMetaboxes::class)
    ? \ErfanSanat\Theme\Admin\ProductMetaboxes::get_product_data($productId)
    : [];
$productTitle = get_the_title($productId);
$productSubtitle = isset($productMeta['zireonvan']) && is_scalar($productMeta['zireonvan']) ? trim((string) $productMeta['zireonvan']) : '';
$productEnglishTitle = isset($productMeta['english_titl']) && is_scalar($productMeta['english_titl']) ? trim((string) $productMeta['english_titl']) : '';
$beforeSaleNotice = isset($productMeta['before-sale-c']) && is_scalar($productMeta['before-sale-c']) ? trim((string) $productMeta['before-sale-c']) : '';
$catalogUrl = isset($productMeta['product-catalog-file']) && is_scalar($productMeta['product-catalog-file'])
    ? esc_url((string) $productMeta['product-catalog-file'])
    : '';
$canBuyOnline = isset($productMeta['true_sale']) && 'true' === strtolower(trim((string) $productMeta['true_sale']));

$toRows = static function ($value): array {
    if (is_string($value)) {
        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            $value = $decoded;
        } elseif (function_exists('maybe_unserialize')) {
            $unserialized = maybe_unserialize($value);
            $value = is_array($unserialized) ? $unserialized : [];
        }
    }
    if (!is_array($value)) {
        return [];
    }
    return array_values(array_filter($value, 'is_array'));
};
$featureRows = $toRows($productMeta['product-feature'] ?? []);
$keyFeatureRows = $toRows($productMeta['product-feature2_copy'] ?? []);
$usageRows = $toRows($productMeta['usage_product'] ?? []);
$faqRows = $toRows($productMeta['faq'] ?? []);

$identifierLabels = [
    'gs1_id' => __('شناسه GS1 / GTIN', 'erfansanat-theme'),
    'national_code' => __('کد ملی کالا', 'erfansanat-theme'),
    'hs_id' => __('شناسه HS', 'erfansanat-theme'),
    'isic_id' => __('شناسه ISIC', 'erfansanat-theme'),
    'cpc_id' => __('شناسه CPC', 'erfansanat-theme'),
    'product_id' => __('شناسه محصول', 'erfansanat-theme'),
];
$identifiers = [];
foreach ($identifierLabels as $metaKey => $label) {
    $value = $productMeta[$metaKey] ?? '';
    if (is_scalar($value) && '' !== trim((string) $value)) {
        $identifiers[$metaKey] = ['label' => $label, 'value' => (string) $value];
    }
}

$attributeLabels = [
    'pa_after-sales-support' => __('خدمات پس از فروش', 'erfansanat-theme'),
    'pa_beam-angle' => __('زاویه تابش', 'erfansanat-theme'),
    'pa_body-material' => __('جنس بدنه', 'erfansanat-theme'),
    'pa_brand' => __('برند', 'erfansanat-theme'),
    'pa_bulb-type' => __('جنس حباب', 'erfansanat-theme'),
    'pa_cable-material' => __('جنس سیم', 'erfansanat-theme'),
    'pa_certifications' => __('گواهینامه‌ها', 'erfansanat-theme'),
    'pa_color' => __('رنگ نور', 'erfansanat-theme'),
    'pa_diameter' => __('قطر', 'erfansanat-theme'),
    'pa_diffuser-color' => __('رنگ حباب', 'erfansanat-theme'),
    'pa_input-voltage' => __('ولتاژ ورودی', 'erfansanat-theme'),
    'pa_intensity' => __('شدت نور', 'erfansanat-theme'),
    'pa_ip-rating' => __('درجه حفاظت IP', 'erfansanat-theme'),
    'pa_led-chip-type' => __('نوع چیپ LED', 'erfansanat-theme'),
    'pa_lifespan' => __('طول عمر مفید', 'erfansanat-theme'),
    'pa_size' => __('طول / تراکم', 'erfansanat-theme'),
    'pa_warranty' => __('گارانتی', 'erfansanat-theme'),
    'pa_wattage' => __('توان مصرفی', 'erfansanat-theme'),
];
$attributes = [];
if (is_object($product) && method_exists($product, 'get_attribute')) {
    foreach ($attributeLabels as $taxonomy => $label) {
        $value = trim((string) $product->get_attribute($taxonomy));
        if ('' !== $value) {
            $attributes[$taxonomy] = ['label' => $label, 'value' => $value];
        }
    }
}

// ProductMetaboxes reads the exact legacy `galerry_produ` key first, then WooCommerce and older gallery fields.
$galleryIds = [];
if (class_exists(\ErfanSanat\Theme\Admin\ProductMetaboxes::class)) {
    $galleryIds = \ErfanSanat\Theme\Admin\ProductMetaboxes::get_product_gallery_ids($productId);
}
if (is_object($product) && method_exists($product, 'get_gallery_image_ids')) {
    $galleryIds = array_merge($galleryIds, (array) $product->get_gallery_image_ids());
}
$featuredImageId = function_exists('get_post_thumbnail_id') ? (int) get_post_thumbnail_id($productId) : 0;
if ($featuredImageId) {
    array_unshift($galleryIds, $featuredImageId);
}
$galleryIds = array_values(array_unique(array_filter(array_map('absint', $galleryIds))));
$galleryImages = [];
foreach (array_slice($galleryIds, 0, 20) as $imageId) {
    $imageUrl = wp_get_attachment_image_url($imageId, 'large');
    if (!$imageUrl) {
        $imageUrl = wp_get_attachment_url($imageId);
    }
    if (!$imageUrl) {
        continue;
    }
    $galleryImages[] = ['id' => $imageId, 'url' => $imageUrl];
}

$projectIds = isset($productMeta['project-ids']) && is_array($productMeta['project-ids'])
    ? array_slice(array_values(array_unique(array_filter(array_map('absint', $productMeta['project-ids'])))), 0, 12)
    : [];
$relatedProjects = $projectIds ? get_posts([
    'post_type' => 'projects',
    'post_status' => 'publish',
    'post__in' => $projectIds,
    'orderby' => 'post__in',
    'posts_per_page' => count($projectIds),
    'no_found_rows' => true,
    'suppress_filters' => false,
]) : [];
$usageTermIds = isset($productMeta['usage_countr']) && is_array($productMeta['usage_countr'])
    ? array_slice(array_values(array_unique(array_filter(array_map('absint', $productMeta['usage_countr'])))), 0, 20)
    : [];
$usageTerms = $usageTermIds ? get_terms([
    'taxonomy' => 'project_type',
    'include' => $usageTermIds,
    'hide_empty' => false,
]) : [];
if (is_wp_error($usageTerms)) {
    $usageTerms = [];
}

$description = trim((string) get_post_field('post_content', $productId));
$shortDescription = '';
if (is_object($product) && method_exists($product, 'get_short_description')) {
    $shortDescription = trim((string) $product->get_short_description());
}
if ('' === $shortDescription) {
    $shortDescription = trim((string) get_post_field('post_excerpt', $productId));
}
$hasDescription = '' !== $description || '' !== $shortDescription || $featureRows || $keyFeatureRows || $usageRows || $relatedProjects || $usageTerms;
$hasSpecs = $identifiers || $attributes || $keyFeatureRows || $featureRows;
$hasReviews = comments_open($productId) || (int) get_comments_number($productId) > 0;
$categories = get_the_terms($productId, 'product_cat');
if (!is_array($categories) || is_wp_error($categories)) {
    $categories = [];
}
$primaryCategory = $categories ? reset($categories) : null;
$contactPhone = function_exists('es_option') ? (string) es_option('contact.phone', '031-91091011') : '031-91091011';
$salesPhone = function_exists('es_option') ? (string) es_option('contact.sales_phone', $contactPhone) : $contactPhone;
$contactEmail = function_exists('es_option') ? (string) es_option('contact.email', 'info@erfansanat.com') : 'info@erfansanat.com';
$phoneHref = static function (string $phone): string {
    return preg_replace('/[^+0-9]/', '', $phone) ?: '';
};

if (function_exists('do_action')) {
    do_action('woocommerce_before_single_product');
}
?>
<main class="product type-product relative z-10 pb-20" id="product-details-main">
    <nav class="mx-auto max-w-6xl px-5 pt-8 lg:px-8" aria-label="<?php echo esc_attr__('مسیر راهنما', 'erfansanat-theme'); ?>">
        <ol class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
            <li><a class="transition-colors hover:text-emerald-700" href="<?php echo esc_url(home_url('/')); ?>"><?php echo esc_html__('صفحه اصلی', 'erfansanat-theme'); ?></a></li>
            <li aria-hidden="true">/</li>
            <li><a class="transition-colors hover:text-emerald-700" href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/')); ?>"><?php echo esc_html__('فروشگاه محصولات', 'erfansanat-theme'); ?></a></li>
            <?php if ($primaryCategory instanceof WP_Term) : ?>
                <?php $categoryUrl = get_term_link($primaryCategory); ?>
                <?php if (!is_wp_error($categoryUrl)) : ?><li aria-hidden="true">/</li><li><a class="transition-colors hover:text-emerald-700" href="<?php echo esc_url($categoryUrl); ?>"><?php echo esc_html($primaryCategory->name); ?></a></li><?php endif; ?>
            <?php endif; ?>
            <li aria-hidden="true">/</li>
            <li class="font-bold text-slate-800" aria-current="page"><?php echo esc_html($productTitle); ?></li>
        </ol>
    </nav>

    <section class="mx-auto grid max-w-6xl gap-8 px-5 pb-10 pt-6 lg:grid-cols-2 lg:gap-12 lg:px-8 lg:pt-9" aria-labelledby="product-title">
        <div class="min-w-0">
            <?php if ($galleryImages) : ?>
                <div class="space-y-3.5" data-gallery-root="true">
                    <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <img src="<?php echo esc_url($galleryImages[0]['url']); ?>" alt="<?php echo esc_attr($productTitle); ?>" class="h-[16rem] w-full cursor-zoom-in object-cover sm:h-[24rem] lg:h-[27rem]" loading="eager" data-gallery-main-img="true" />
                        <?php if (count($galleryImages) > 1) : ?>
                            <button type="button" class="absolute right-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-xl border border-white/25 bg-slate-900/70 text-xl text-white" aria-label="<?php echo esc_attr__('تصویر قبلی', 'erfansanat-theme'); ?>" data-gallery-prev="true">‹</button>
                            <button type="button" class="absolute left-3 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-xl border border-white/25 bg-slate-900/70 text-xl text-white" aria-label="<?php echo esc_attr__('تصویر بعدی', 'erfansanat-theme'); ?>" data-gallery-next="true">›</button>
                        <?php endif; ?>
                        <span class="absolute bottom-3 right-3 rounded-xl border border-white/20 bg-slate-900/75 px-3 py-1.5 text-xs font-bold text-white" data-gallery-counter="true">۱ / <?php echo esc_html((string) count($galleryImages)); ?></span>
                        <button type="button" class="absolute bottom-3 left-3 rounded-xl border border-white/25 bg-white/90 px-3 py-1.5 text-xs font-bold text-slate-800" data-gallery-expand="true"><?php echo esc_html__('بزرگ‌نمایی', 'erfansanat-theme'); ?></button>
                    </div>
                    <?php if (count($galleryImages) > 1) : ?>
                        <div class="grid grid-cols-4 gap-2 sm:grid-cols-5">
                            <?php foreach ($galleryImages as $index => $image) : ?>
                                <button type="button" class="relative h-16 overflow-hidden rounded-xl border-2 border-transparent opacity-80 transition hover:opacity-100 sm:h-20" aria-label="<?php echo esc_attr(sprintf(__('نمایش تصویر %d', 'erfansanat-theme'), $index + 1)); ?>" data-gallery-thumb="<?php echo esc_attr((string) $index); ?>" data-gallery-src="<?php echo esc_url($image['url']); ?>">
                                    <img src="<?php echo esc_url($image['url']); ?>" alt="" class="h-full w-full object-cover" loading="lazy" />
                                    <span class="pointer-events-none absolute inset-0 rounded-lg ring-2 ring-inset ring-emerald-500 <?php echo 0 === $index ? 'block' : 'hidden'; ?>" data-gallery-thumb-ring="true"></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/90 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr__('نمایش تصویر محصول', 'erfansanat-theme'); ?>" data-gallery-lightbox="true">
                        <button type="button" class="absolute right-5 top-5 grid h-11 w-11 place-items-center rounded-full border border-white/20 bg-white/10 text-2xl text-white" aria-label="<?php echo esc_attr__('بستن', 'erfansanat-theme'); ?>" data-gallery-lightbox-close="true">×</button>
                        <?php if (count($galleryImages) > 1) : ?>
                            <button type="button" class="absolute right-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-2xl text-white" aria-label="<?php echo esc_attr__('تصویر قبلی', 'erfansanat-theme'); ?>" data-gallery-lightbox-prev="true">‹</button>
                            <button type="button" class="absolute left-5 top-1/2 grid h-11 w-11 -translate-y-1/2 place-items-center rounded-full border border-white/20 bg-white/10 text-2xl text-white" aria-label="<?php echo esc_attr__('تصویر بعدی', 'erfansanat-theme'); ?>" data-gallery-lightbox-next="true">›</button>
                        <?php endif; ?>
                        <img src="<?php echo esc_url($galleryImages[0]['url']); ?>" alt="<?php echo esc_attr($productTitle); ?>" class="max-h-[84vh] max-w-[90vw] rounded-2xl object-contain shadow-2xl" data-gallery-lightbox-img="true" />
                        <?php if (count($galleryImages) > 1) : ?><div class="absolute bottom-6 flex items-center gap-2" aria-label="<?php echo esc_attr__('انتخاب تصویر', 'erfansanat-theme'); ?>"><?php foreach ($galleryImages as $index => $image) : ?><button type="button" class="h-2 rounded-full <?php echo 0 === $index ? 'w-7 bg-emerald-400' : 'w-2 bg-white/40 hover:bg-white/70'; ?>" aria-label="<?php echo esc_attr(sprintf(__('تصویر %d', 'erfansanat-theme'), $index + 1)); ?>" data-gallery-lightbox-dot="<?php echo esc_attr((string) $index); ?>"></button><?php endforeach; ?></div><?php endif; ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="grid min-h-64 place-items-center rounded-3xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500"><?php echo esc_html__('تصویری برای این محصول ثبت نشده است.', 'erfansanat-theme'); ?></div>
            <?php endif; ?>
        </div>

        <div class="flex min-w-0 flex-col">
            <div class="mb-3 flex flex-wrap items-center gap-2">
                <?php foreach ($categories as $category) : ?>
                    <?php if ($category instanceof WP_Term) : ?>
                        <?php $categoryUrl = get_term_link($category); ?>
                        <?php if (!is_wp_error($categoryUrl)) : ?><a class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[11px] font-bold text-emerald-800" href="<?php echo esc_url($categoryUrl); ?>"><?php echo esc_html($category->name); ?></a><?php endif; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php if (is_object($product) && method_exists($product, 'get_sku') && $product->get_sku()) : ?><span class="rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-600"><?php echo esc_html(sprintf(__('کد محصول: %s', 'erfansanat-theme'), $product->get_sku())); ?></span><?php endif; ?>
            </div>
            <h1 id="product-title" class="text-2xl font-black leading-relaxed text-slate-950 sm:text-3xl"><?php echo esc_html($productTitle); ?></h1>
            <?php if ($productSubtitle) : ?><p class="mt-1 text-base font-bold leading-7 text-emerald-800"><?php echo esc_html($productSubtitle); ?></p><?php endif; ?>
            <?php if ($productEnglishTitle) : ?><p class="mt-1 text-sm font-medium text-slate-500" lang="en" dir="ltr"><?php echo esc_html($productEnglishTitle); ?></p><?php endif; ?>

            <?php if (function_exists('woocommerce_template_single_rating')) : ?><div class="mt-3"><?php woocommerce_template_single_rating(); ?></div><?php elseif (is_object($product) && method_exists($product, 'get_average_rating') && $product->get_average_rating()) : ?><p class="mt-3 text-sm font-bold text-amber-700"><?php echo esc_html(sprintf(__('امتیاز: %s از ۵', 'erfansanat-theme'), $product->get_average_rating())); ?></p><?php endif; ?>

            <?php if ($canBuyOnline && function_exists('woocommerce_template_single_price')) : ?><div class="mt-4 text-xl font-black text-emerald-800"><?php woocommerce_template_single_price(); ?></div><?php endif; ?>
            <?php if ($beforeSaleNotice) : ?><div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-7 text-amber-950"><?php echo nl2br(esc_html($beforeSaleNotice)); ?></div><?php endif; ?>
            <?php if ($shortDescription) : ?><div class="mt-4 text-sm leading-8 text-slate-600"><?php echo wp_kses_post(wpautop($shortDescription)); ?></div><?php endif; ?>

            <?php if ($identifiers) : ?>
                <dl class="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-3">
                    <?php foreach (array_slice($identifiers, 0, 3, true) as $identifier) : ?><div class="rounded-xl border border-slate-200 bg-white px-3 py-2"><dt class="text-[10px] font-semibold text-slate-500"><?php echo esc_html($identifier['label']); ?></dt><dd class="mt-1 break-all text-xs font-black text-slate-800"><?php echo esc_html($identifier['value']); ?></dd></div><?php endforeach; ?>
                </dl>
            <?php endif; ?>

            <div class="mt-5 flex flex-wrap items-center gap-3">
                <?php if ($canBuyOnline && function_exists('woocommerce_template_single_add_to_cart')) : ?>
                    <div class="min-w-0 flex-1 rounded-2xl bg-emerald-700 p-3 text-white shadow-md sm:p-4">
                        <?php woocommerce_template_single_add_to_cart(); ?>
                    </div>
                <?php elseif (!$canBuyOnline) : ?>
                    <?php if ($salesPhone) : ?><a class="inline-flex min-h-12 flex-1 items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-5 py-3 text-sm font-black text-white shadow-md transition hover:bg-emerald-800" href="<?php echo esc_url('tel:' . $phoneHref($salesPhone)); ?>"><span aria-hidden="true">☎</span><?php echo esc_html__('استعلام قیمت و مشاوره خرید', 'erfansanat-theme'); ?></a><?php endif; ?>
                    <?php if ($contactEmail && is_email($contactEmail)) : ?><a class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-emerald-200 bg-white px-5 py-3 text-sm font-bold text-emerald-800 transition hover:bg-emerald-50" href="<?php echo esc_url('mailto:' . sanitize_email($contactEmail)); ?>"><?php echo esc_html__('ارسال درخواست', 'erfansanat-theme'); ?></a><?php endif; ?>
                <?php endif; ?>
                <?php if ($catalogUrl) : ?><a class="inline-flex min-h-12 items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-bold text-slate-700 transition hover:border-emerald-300 hover:text-emerald-800" href="<?php echo esc_url($catalogUrl); ?>" target="_blank" rel="noopener noreferrer" download><span aria-hidden="true">↓</span><?php echo esc_html__('دریافت کاتالوگ', 'erfansanat-theme'); ?></a><?php endif; ?>
            </div>
            <?php if ($contactPhone && !$canBuyOnline) : ?><p class="mt-3 text-xs text-slate-500"><?php echo esc_html__('تلفن دفتر:', 'erfansanat-theme'); ?> <a class="font-bold text-emerald-800" href="<?php echo esc_url('tel:' . $phoneHref($contactPhone)); ?>"><?php echo esc_html($contactPhone); ?></a></p><?php endif; ?>
        </div>
    </section>

    <?php if ($hasDescription || $hasSpecs || $hasReviews) : ?>
        <section id="product-details" data-prod-tabs-container="true" class="mx-auto max-w-6xl scroll-mt-28 px-5 pb-12 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200/80 bg-white shadow-sm">
                <div class="flex gap-2 overflow-x-auto border-b border-slate-100 bg-slate-50/70 p-3 sm:flex-wrap sm:px-6" role="tablist">
                    <?php $firstTab = $hasDescription ? 'desc' : ($hasSpecs ? 'specs' : 'reviews'); ?>
                    <?php if ($hasDescription) : ?><button type="button" role="tab" aria-selected="<?php echo 'desc' === $firstTab ? 'true' : 'false'; ?>" class="flex shrink-0 items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-extrabold transition <?php echo 'desc' === $firstTab ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900'; ?>" data-prod-tab-btn="desc"><span data-prod-tab-icon="true" class="text-emerald-700 <?php echo 'desc' === $firstTab ? 'text-white' : ''; ?>" aria-hidden="true">✦</span><?php echo esc_html__('معرفی و کاربردها', 'erfansanat-theme'); ?></button><?php endif; ?>
                    <?php if ($hasSpecs) : ?><button type="button" role="tab" aria-selected="<?php echo 'specs' === $firstTab ? 'true' : 'false'; ?>" class="flex shrink-0 items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-extrabold transition <?php echo 'specs' === $firstTab ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900'; ?>" data-prod-tab-btn="specs"><span data-prod-tab-icon="true" class="text-emerald-700 <?php echo 'specs' === $firstTab ? 'text-white' : ''; ?>" aria-hidden="true">▤</span><?php echo esc_html__('مشخصات فنی و شناسه‌ها', 'erfansanat-theme'); ?></button><?php endif; ?>
                    <?php if ($hasReviews) : ?><button type="button" role="tab" aria-selected="<?php echo 'reviews' === $firstTab ? 'true' : 'false'; ?>" class="flex shrink-0 items-center gap-2 rounded-2xl px-4 py-2.5 text-xs font-extrabold transition <?php echo 'reviews' === $firstTab ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-emerald-100/60 hover:text-emerald-900'; ?>" data-prod-tab-btn="reviews"><span data-prod-tab-icon="true" class="text-emerald-700 <?php echo 'reviews' === $firstTab ? 'text-white' : ''; ?>" aria-hidden="true">◎</span><?php echo esc_html__('نظرات کاربران', 'erfansanat-theme'); ?></button><?php endif; ?>
                </div>

                <?php if ($hasDescription) : ?>
                    <div class="p-5 sm:p-8 <?php echo 'desc' === $firstTab ? 'block' : 'hidden'; ?>" role="tabpanel" data-prod-tab-panel="desc">
                        <h2 class="text-xl font-extrabold text-slate-900"><?php echo esc_html(sprintf(__('معرفی %s', 'erfansanat-theme'), $productTitle)); ?></h2>
                        <?php if ($description) : ?><div class="prose prose-slate mt-4 max-w-none text-sm leading-8"><?php echo apply_filters('the_content', $description); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress content filters sanitize and format post content. ?></div><?php endif; ?>
                        <?php if ($usageRows) : ?><div class="mt-7"><h3 class="mb-3 text-base font-extrabold text-slate-900"><?php echo esc_html__('کاربردها و موارد مصرف', 'erfansanat-theme'); ?></h3><ul class="grid gap-2 sm:grid-cols-2"><?php foreach ($usageRows as $row) : ?><?php $usage = isset($row['add-new-usage']) && is_scalar($row['add-new-usage']) ? trim((string) $row['add-new-usage']) : ''; ?><?php if ($usage) : ?><li class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-700"><span class="ml-2 text-emerald-700" aria-hidden="true">✓</span><?php echo esc_html($usage); ?></li><?php endif; ?><?php endforeach; ?></ul></div><?php endif; ?>
                        <?php if ($usageTerms) : ?><div class="mt-7"><h3 class="mb-3 text-base font-extrabold text-slate-900"><?php echo esc_html__('انواع پروژه‌های مرتبط', 'erfansanat-theme'); ?></h3><div class="flex flex-wrap gap-2"><?php foreach ($usageTerms as $term) : ?><?php if ($term instanceof WP_Term) : ?><?php $termUrl = get_term_link($term); ?><?php if (!is_wp_error($termUrl)) : ?><a class="rounded-xl border border-emerald-200 bg-white px-3 py-2 text-xs font-bold text-emerald-800" href="<?php echo esc_url($termUrl); ?>"><?php echo esc_html($term->name); ?></a><?php endif; ?><?php endif; ?><?php endforeach; ?></div></div><?php endif; ?>
                        <?php if ($relatedProjects) : ?><div class="mt-7"><h3 class="mb-3 text-base font-extrabold text-slate-900"><?php echo esc_html__('پروژه‌های اجراشده با این محصول', 'erfansanat-theme'); ?></h3><div class="grid gap-3 sm:grid-cols-2"><?php foreach ($relatedProjects as $project) : ?><a class="rounded-2xl border border-slate-200 bg-white p-4 transition hover:border-emerald-300 hover:shadow-sm" href="<?php echo esc_url(get_permalink($project)); ?>"><span class="text-sm font-extrabold text-slate-900"><?php echo esc_html(get_the_title($project)); ?></span><span class="mt-1 block text-xs font-bold text-emerald-700"><?php echo esc_html__('مشاهده پروژه', 'erfansanat-theme'); ?> ←</span></a><?php endforeach; ?></div></div><?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($hasSpecs) : ?>
                    <div class="p-5 sm:p-8 <?php echo 'specs' === $firstTab ? 'block' : 'hidden'; ?>" role="tabpanel" data-prod-tab-panel="specs">
                        <h2 class="text-xl font-extrabold text-slate-900"><?php echo esc_html__('مشخصات فنی و شناسه‌های محصول', 'erfansanat-theme'); ?></h2>
                        <?php if ($identifiers) : ?><div class="mt-5"><h3 class="mb-3 text-sm font-black text-emerald-950"><?php echo esc_html__('شناسه‌های رسمی کالا', 'erfansanat-theme'); ?></h3><dl class="grid gap-2 sm:grid-cols-2"><?php foreach ($identifiers as $identifier) : ?><div class="flex flex-col gap-1 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between"><dt class="text-xs font-semibold text-slate-500"><?php echo esc_html($identifier['label']); ?></dt><dd class="break-all text-sm font-black text-slate-800"><?php echo esc_html($identifier['value']); ?></dd></div><?php endforeach; ?></dl></div><?php endif; ?>
                        <?php if ($attributes) : ?><div class="mt-6"><h3 class="mb-3 text-sm font-black text-emerald-950"><?php echo esc_html__('ویژگی‌های فنی', 'erfansanat-theme'); ?></h3><dl class="grid gap-2 sm:grid-cols-2"><?php foreach ($attributes as $attribute) : ?><div class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 px-4 py-3"><dt class="text-xs font-semibold text-slate-500"><?php echo esc_html($attribute['label']); ?></dt><dd class="text-sm font-bold text-slate-800"><?php echo esc_html($attribute['value']); ?></dd></div><?php endforeach; ?></dl></div><?php endif; ?>
                        <?php if ($keyFeatureRows) : ?><div class="mt-6"><h3 class="mb-3 text-sm font-black text-emerald-950"><?php echo esc_html__('ویژگی‌های کلیدی', 'erfansanat-theme'); ?></h3><dl class="grid gap-2 sm:grid-cols-2"><?php foreach ($keyFeatureRows as $row) : ?><?php $label = isset($row['add-new-feature']) && is_scalar($row['add-new-feature']) ? trim((string) $row['add-new-feature']) : ''; $value = isset($row['vaule_of_variable']) && is_scalar($row['vaule_of_variable']) ? trim((string) $row['vaule_of_variable']) : ''; ?><?php if ($label || $value) : ?><div class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 px-4 py-3"><dt class="text-xs font-semibold text-slate-500"><?php echo esc_html($label); ?></dt><dd class="text-sm font-bold text-slate-800"><?php echo esc_html($value); ?></dd></div><?php endif; ?><?php endforeach; ?></dl></div><?php endif; ?>
                        <?php if ($featureRows) : ?><div class="mt-6"><h3 class="mb-3 text-sm font-black text-emerald-950"><?php echo esc_html__('مشخصات تکمیلی', 'erfansanat-theme'); ?></h3><ul class="grid gap-2 sm:grid-cols-2"><?php foreach ($featureRows as $row) : ?><?php $feature = isset($row['add-new-feature']) && is_scalar($row['add-new-feature']) ? trim((string) $row['add-new-feature']) : ''; ?><?php if ($feature) : ?><li class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700"><?php echo esc_html($feature); ?></li><?php endif; ?><?php endforeach; ?></ul></div><?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if ($hasReviews) : ?>
                    <div class="p-5 sm:p-8 <?php echo 'reviews' === $firstTab ? 'block' : 'hidden'; ?>" role="tabpanel" data-prod-tab-panel="reviews">
                        <?php if (function_exists('wc_get_template')) : ?>
                            <?php wc_get_template('single-product-reviews.php'); ?>
                        <?php else : ?>
                            <?php comments_template(); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php get_template_part('template-parts/forms/client-feedback', null, ['product_id' => $productId]); ?>
    <?php if (!$canBuyOnline) : ?>
        <?php get_template_part('template-parts/forms/quote', null, ['product_id' => $productId]); ?>
    <?php endif; ?>

    <?php if ($faqRows) : ?>
        <?php $renderableFaqs = array_values(array_filter($faqRows, static function (array $row): bool { return !empty($row['name_question']) && !empty($row['answer_question']); })); ?>
        <?php if ($renderableFaqs) : ?>
            <section class="mx-auto max-w-6xl px-5 pb-12 lg:px-8" aria-labelledby="product-faq-title">
                <h2 id="product-faq-title" class="mb-5 text-xl font-black text-slate-900"><?php echo esc_html__('پرسش‌های متداول این محصول', 'erfansanat-theme'); ?></h2>
                <div class="space-y-3" data-faq-list="true">
                    <?php foreach ($renderableFaqs as $index => $faq) : ?>
                        <?php $question = isset($faq['name_question']) && is_scalar($faq['name_question']) ? trim((string) $faq['name_question']) : ''; $answer = isset($faq['answer_question']) && is_scalar($faq['answer_question']) ? trim((string) $faq['answer_question']) : ''; ?>
                        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                            <button type="button" class="flex w-full items-center justify-between gap-4 p-4 text-right text-sm font-extrabold text-slate-800" aria-expanded="false" data-faq-toggle="<?php echo esc_attr((string) $index); ?>"><span><?php echo esc_html($question); ?></span><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-100 text-lg text-emerald-800 transition" aria-hidden="true" data-faq-icon="<?php echo esc_attr((string) $index); ?>">+</span></button>
                            <?php if ($answer) : ?><div class="hidden border-t border-slate-100 px-4 py-4 text-sm leading-7 text-slate-600" data-faq-body="<?php echo esc_attr((string) $index); ?>"><?php echo nl2br(esc_html($answer)); ?></div><?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    $relatedProductIds = function_exists('wc_get_related_products')
        ? (array) wc_get_related_products($productId, 4, [$productId])
        : [];
    $relatedProductIds = array_slice(array_values(array_unique(array_filter(array_map('absint', $relatedProductIds)))), 0, 4);
    $relatedProductPosts = $relatedProductIds ? get_posts([
        'post_type' => 'product',
        'post_status' => 'publish',
        'post__in' => $relatedProductIds,
        'orderby' => 'post__in',
        'posts_per_page' => count($relatedProductIds),
        'no_found_rows' => true,
        'suppress_filters' => false,
    ]) : [];
    ?>
    <?php if ($relatedProductPosts) : ?>
        <section class="mx-auto max-w-6xl px-5 pb-12 lg:px-8" aria-labelledby="related-products-title">
            <h2 id="related-products-title" class="mb-5 text-xl font-black text-slate-900"><?php echo esc_html__('محصولات مرتبط', 'erfansanat-theme'); ?></h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <?php foreach ($relatedProductPosts as $relatedPost) : ?>
                    <?php $relatedId = (int) $relatedPost->ID; $relatedImageId = (int) get_post_thumbnail_id($relatedId); $relatedImage = $relatedImageId ? wp_get_attachment_image_url($relatedImageId, 'medium') : ''; $relatedProduct = function_exists('wc_get_product') ? wc_get_product($relatedId) : null; ?>
                    <a class="group overflow-hidden rounded-2xl border border-slate-200 bg-white transition hover:-translate-y-0.5 hover:border-emerald-300 hover:shadow-md" href="<?php echo esc_url(get_permalink($relatedId)); ?>">
                        <?php if ($relatedImage) : ?><img class="h-40 w-full object-cover transition group-hover:scale-[1.02]" src="<?php echo esc_url($relatedImage); ?>" alt="<?php echo esc_attr(get_the_title($relatedId)); ?>" loading="lazy" /><?php endif; ?>
                        <span class="block p-4"><span class="block text-sm font-extrabold text-slate-900"><?php echo esc_html(get_the_title($relatedId)); ?></span><?php if (is_object($relatedProduct) && method_exists($relatedProduct, 'get_price_html') && $relatedProduct->get_price_html()) : ?><span class="mt-2 block text-xs font-bold text-emerald-800"><?php echo wp_kses_post($relatedProduct->get_price_html()); ?></span><?php endif; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php
if (function_exists('do_action')) {
    do_action('woocommerce_after_single_product');
}
get_footer();
