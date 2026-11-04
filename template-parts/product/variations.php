<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$product = $GLOBALS['product'] ?? null;
if (!is_object($product) || !method_exists($product, 'get_variation_attributes') || !method_exists($product, 'get_available_variations')) return;
$attributes = $product->get_variation_attributes();
if (!is_array($attributes) || $attributes === []) return;
$variations = $product->get_available_variations();
$productId = method_exists($product, 'get_id') ? (int) $product->get_id() : get_the_ID();
$min = method_exists($product, 'get_min_purchase_quantity') ? max(1, (int) $product->get_min_purchase_quantity()) : 1;
?>
<form class="variations_form cart" action="<?php echo esc_url(get_permalink($productId)); ?>" method="post" enctype="multipart/form-data" data-product_id="<?php echo esc_attr((string) $productId); ?>" data-product_variations="<?php echo esc_attr(wp_json_encode($variations)); ?>" data-product-min="<?php echo esc_attr((string) $min); ?>" data-product-step="1">
    <div class="space-y-4 variations">
        <?php foreach ($attributes as $attributeName => $options) : if (!is_array($options)) continue; $attributeName = (string) $attributeName; $selectName = 'attribute_' . sanitize_title($attributeName); $label = function_exists('wc_attribute_label') ? wc_attribute_label($attributeName) : ucwords(str_replace(['pa_', '-', '_'], ['', ' ', ' '], $attributeName)); ?>
            <div class="space-y-2" data-opt-group data-opt-attribute="<?php echo esc_attr($selectName); ?>">
                <div class="flex items-center justify-between"><label class="text-sm font-bold text-slate-800" for="<?php echo esc_attr($selectName); ?>"><?php echo esc_html($label); ?></label><span data-opt-selected-label="true" class="text-xs text-slate-500">انتخاب کنید</span></div>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($options as $option) : $value = (string) $option; $optionLabel = $value; $color = '';
                        if (taxonomy_exists($attributeName)) { $term = get_term_by('slug', $value, $attributeName); if ($term instanceof WP_Term) { $optionLabel = $term->name; $color = sanitize_hex_color((string) get_term_meta($term->term_id, 'product_attribute_color', true)) ?: ''; } }
                        $isColor = $attributeName === 'pa_color' && $color !== ''; ?>
                        <button type="button" data-opt-btn data-opt-value="<?php echo esc_attr($value); ?>" data-opt-value-label="<?php echo esc_attr($optionLabel); ?>" data-opt-is-color="<?php echo $isColor ? 'true' : 'false'; ?>" data-opt-attribute="<?php echo esc_attr($selectName); ?>" class="<?php echo $isColor ? 'h-9 w-9 rounded-full border border-slate-200 ring-1 ring-slate-200' : 'rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:border-emerald-400'; ?>" <?php if ($isColor) : ?>style="background-color:<?php echo esc_attr($color); ?>" aria-label="<?php echo esc_attr($optionLabel); ?>"<?php endif; ?>><?php if (!$isColor) echo esc_html($optionLabel); ?></button>
                    <?php endforeach; ?>
                </div>
                <?php if (function_exists('wc_dropdown_variation_attribute_options')) : wc_dropdown_variation_attribute_options(['options' => $options, 'attribute' => $attributeName, 'product' => $product, 'class' => 'variations-select sr-only', 'show_option_none' => 'انتخاب کنید']); else : ?><select id="<?php echo esc_attr($selectName); ?>" name="<?php echo esc_attr($selectName); ?>" class="variations-select sr-only" aria-label="<?php echo esc_attr($label); ?>"><option value="">انتخاب کنید</option><?php foreach ($options as $option) : ?><option value="<?php echo esc_attr((string) $option); ?>"><?php echo esc_html((string) $option); ?></option><?php endforeach; ?></select><?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <a class="reset_variations mt-3 inline-block text-xs text-slate-500 underline" href="#reset">پاک کردن انتخاب‌ها</a>
    <input type="hidden" name="variation_id" class="variation_id" value="0">
    <div class="mt-4 flex flex-wrap items-center gap-3"><?php if (function_exists('woocommerce_quantity_input') && $product instanceof \WC_Product) : echo woocommerce_quantity_input(['min_value' => $min, 'step' => 1, 'input_value' => $min, 'classes' => ['input-text', 'qty', 'text', 'w-24']], $product, false); else : ?><label class="sr-only" for="quantity-<?php echo esc_attr((string) $productId); ?>">تعداد</label><input id="quantity-<?php echo esc_attr((string) $productId); ?>" class="input-text qty text w-24 rounded-xl border px-3 py-2 text-center" type="number" name="quantity" min="<?php echo esc_attr((string) $min); ?>" step="1" value="<?php echo esc_attr((string) $min); ?>"><?php endif; ?><button type="submit" name="add-to-cart" value="<?php echo esc_attr((string) $productId); ?>" class="single_add_to_cart_button button alt rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white" disabled>افزودن به سبد خرید</button></div>
</form>
