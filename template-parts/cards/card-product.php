<?php
/**
 * Shared WooCommerce product card used by homepage product shelves.
 *
 * @package ErfanSanat\Theme
 *
 * @var array<string, mixed> $args Template arguments.
 */

$productId = isset($args['post_id']) ? absint($args['post_id']) : 0;
$productPost = $productId ? get_post($productId) : null;
if (!$productPost instanceof WP_Post || 'product' !== $productPost->post_type) {
    return;
}

$index = isset($args['index']) ? absint($args['index']) : 0;
$variant = isset($args['variant']) ? sanitize_key((string) $args['variant']) : 'featured';
$imageUrl = \ErfanSanat\Theme\HomepageData::get_post_image_url($productId);
$productUrl = get_permalink($productId);
$title = get_the_title($productId);
$terms = get_the_terms($productId, 'product_cat');
$categoryLabel = is_array($terms) && isset($terms[0]) ? $terms[0]->name : '';
$description = get_the_excerpt($productId);
if ('' === trim($description)) {
    $description = sanitize_text_field((string) get_post_meta($productId, 'zireonvan', true));
}
$priceHtml = '';
if (function_exists('wc_get_product')) {
    $wcProduct = wc_get_product($productId);
    if (is_object($wcProduct) && method_exists($wcProduct, 'get_price_html')) {
        $priceHtml = (string) $wcProduct->get_price_html();
    }
}
$wrapperClass = 0 === $index ? 'sm:col-span-2' : '';
$cardSizeClass = 0 === $index ? 'min-h-[320px] sm:min-h-[380px]' : 'min-h-[260px] sm:min-h-[295px]';
$cardClass = 'card-glow group relative flex h-full flex-col justify-between overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 p-5 sm:p-6 ' . $cardSizeClass;
?>
<?php if ('featured' === $variant && 0 === $index) : ?><div class="<?php echo esc_attr($wrapperClass); ?>"><?php endif; ?>
<a href="<?php echo esc_url($productUrl); ?>" class="<?php echo esc_attr($cardClass); ?>" aria-label="<?php echo esc_attr($title); ?>">
    <?php if ($imageUrl) : ?><img src="<?php echo esc_url($imageUrl); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" /><?php endif; ?>
    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/45 to-slate-950/20" aria-hidden="true"></div>
    <div class="relative z-10 flex items-center justify-between gap-2">
        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-white/25 bg-slate-950/60 text-white backdrop-blur-md transition-colors duration-300 group-hover:border-emerald-400 group-hover:bg-emerald-600" aria-hidden="true">↗</span>
        <div class="flex flex-wrap items-center justify-end gap-2 text-[11px] font-bold text-white/90">
            <?php if ($categoryLabel) : ?><span class="rounded-lg border border-white/20 bg-slate-950/60 px-2 py-0.5 text-emerald-100 backdrop-blur-xs"><?php echo esc_html($categoryLabel); ?></span><?php endif; ?>
            <?php if ($priceHtml) : ?><span class="rounded-lg border border-white/20 bg-slate-950/60 px-2 py-0.5 backdrop-blur-xs"><?php echo wp_kses_post($priceHtml); ?></span><?php endif; ?>
        </div>
    </div>
    <div class="relative z-10 mt-auto pt-12">
        <h3 class="text-lg font-black leading-7 text-white transition-colors group-hover:text-emerald-200"><?php echo esc_html($title); ?></h3>
        <?php if ($description) : ?><p class="mt-1.5 line-clamp-2 text-xs leading-6 font-medium text-white/75"><?php echo esc_html(wp_trim_words($description, 22)); ?></p><?php endif; ?>
    </div>
</a>
<?php if ('featured' === $variant && 0 === $index) : ?></div><?php endif; ?>
