<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$postId = (int) ($GLOBALS['es_product_id'] ?? get_the_ID());
if ($postId < 1 || (string) get_post_meta($postId, 'true_sale', true) === 'false') return;
$product = $GLOBALS['product'] ?? null;
$priceRaw = $product instanceof \WC_Product ? (float) $product->get_price() : (float) get_post_meta($postId, '_price', true);
$priceHtml = function_exists('wc_price') ? wc_price($priceRaw) : esc_html(number_format_i18n($priceRaw) . ' تومان');
$min = $product instanceof \WC_Product && method_exists($product, 'get_min_purchase_quantity') ? max(1, (int) $product->get_min_purchase_quantity()) : 1;
$step = $product instanceof \WC_Product && method_exists($product, 'get_max_purchase_quantity') ? 1 : 1;
$stock = $product instanceof \WC_Product ? $product->is_in_stock() : ((string) get_post_meta($postId, '_stock_status', true) !== 'outofstock');
$permalink = get_permalink($postId);
?>
<section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm" aria-label="خرید آنلاین محصول" data-purchase-panel>
    <div class="mb-4 flex items-start justify-between gap-3"><div><p class="text-xs font-bold text-slate-500">قیمت محصول</p><p class="mt-1 text-2xl font-black text-emerald-800"><?php echo wp_kses_post($priceHtml); ?></p></div><span class="rounded-full <?php echo $stock ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'; ?> px-3 py-1 text-xs font-bold"><?php echo $stock ? 'موجود' : 'ناموجود'; ?></span></div>
    <?php if (class_exists('WC_Product_Variable') && $product instanceof \WC_Product_Variable) : get_template_part('template-parts/product/variations'); elseif ($stock) : ?><form class="cart flex flex-wrap items-center gap-3" action="<?php echo esc_url($permalink); ?>" method="post" enctype="multipart/form-data" data-product-min="<?php echo esc_attr((string) $min); ?>" data-product-step="<?php echo esc_attr((string) $step); ?>">
        <?php if (function_exists('woocommerce_quantity_input') && $product instanceof \WC_Product) : echo woocommerce_quantity_input(['min_value' => $min, 'step' => $step, 'input_value' => $min, 'classes' => ['input-text', 'qty', 'text']], $product, false); else : ?><label class="sr-only" for="quantity-<?php echo esc_attr((string) $postId); ?>">تعداد</label><input id="quantity-<?php echo esc_attr((string) $postId); ?>" class="input-text qty text w-24 rounded-xl border px-3 py-2 text-center" type="number" name="quantity" min="<?php echo esc_attr((string) $min); ?>" step="<?php echo esc_attr((string) $step); ?>" value="<?php echo esc_attr((string) $min); ?>"><?php endif; ?>
        <button type="submit" name="add-to-cart" value="<?php echo esc_attr((string) $postId); ?>" class="single_add_to_cart_button button alt flex-1 rounded-xl bg-emerald-700 px-5 py-3 text-sm font-extrabold text-white hover:bg-emerald-800"><?php echo esc_html($product instanceof \WC_Product ? $product->single_add_to_cart_text() : 'افزودن به سبد خرید'); ?></button>
    </form><?php else : ?><p class="rounded-xl bg-rose-50 p-3 text-sm font-bold text-rose-700">این محصول در حال حاضر موجود نیست.</p><?php endif; ?>
</section>
