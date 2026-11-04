<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\WooCommerce;

use ErfanSanat\Theme\Contracts\ServiceInterface;

final class MiniCart implements ServiceInterface
{
    public function register(): void
    {
        add_filter('woocommerce_add_to_cart_fragments', [$this, 'fragments']);
        add_action('wp_ajax_erfansanat_update_mini_cart', [$this, 'updateItem']);
        add_action('wp_ajax_nopriv_erfansanat_update_mini_cart', [$this, 'updateItem']);
    }

    public function updateItem(): void
    {
        if (!check_ajax_referer('erfansanat_public_nonce', 'nonce', false)) wp_send_json_error(['message' => 'درخواست نامعتبر است.'], 403);
        if (!function_exists('WC')) wp_send_json_error(['message' => 'سبد خرید در دسترس نیست.'], 503);
        try { $woo = WC(); $cart = is_object($woo) ? ($woo->cart ?? null) : null; } catch (\Throwable) { $cart = null; }
        if (!is_object($cart) || !method_exists($cart, 'get_cart')) wp_send_json_error(['message' => 'سبد خرید در دسترس نیست.'], 503);
        $rawKey = isset($_POST['cart_item_key']) && is_scalar($_POST['cart_item_key']) ? wp_unslash((string) $_POST['cart_item_key']) : '';
        $rawQty = isset($_POST['quantity']) && is_scalar($_POST['quantity']) ? wp_unslash($_POST['quantity']) : 0;
        $key = sanitize_text_field((string) $rawKey);
        $qty = max(0, (int) $rawQty);
        $items = (array) $cart->get_cart();
        if ($key === '' || !array_key_exists($key, $items)) wp_send_json_error(['message' => 'محصول در سبد پیدا نشد.'], 404);
        if ($qty === 0 && method_exists($cart, 'remove_cart_item')) $cart->remove_cart_item($key);
        elseif (method_exists($cart, 'set_quantity')) $cart->set_quantity($key, $qty, true);
        else wp_send_json_error(['message' => 'به‌روزرسانی تعداد پشتیبانی نمی‌شود.'], 501);
        $fragments = apply_filters('woocommerce_add_to_cart_fragments', []);
        wp_send_json_success(['fragments' => is_array($fragments) ? $fragments : []]);
    }

    /** @param array<string,string> $fragments @return array<string,string> */
    public function fragments(array $fragments): array
    {
        $cart = $GLOBALS['es_wc_cart'] ?? null;
        if (!$cart && function_exists('WC')) {
            try { $instance = WC(); $cart = is_object($instance) ? ($instance->cart ?? null) : null; } catch (\Throwable) { $cart = null; }
        }
        if (!is_object($cart)) return $fragments;
        $items = method_exists($cart, 'get_cart') ? (array) $cart->get_cart() : [];
        $count = method_exists($cart, 'get_cart_contents_count') ? (int) $cart->get_cart_contents_count() : 0;
        $total = method_exists($cart, 'get_total') ? (float) $cart->get_total('edit') : 0.0;
        $fragments['div[data-cart-modal-items-list="true"]'] = '<div data-cart-modal-items-list="true" class="space-y-3">' . self::renderItems($items) . '</div>';
        $fragments['span[data-cart-modal-total="true"]'] = '<span data-cart-modal-total="true">' . self::price($total) . '</span>';
        $fragments['span[data-cart-modal-count="true"]'] = '<span data-cart-modal-count="true">(' . esc_html(number_format_i18n($count)) . ')</span>';
        $emptyClass = $count > 0 ? 'hidden' : 'block';
        $fragments['div[data-cart-modal-empty="true"]'] = '<div data-cart-modal-empty="true" class="' . esc_attr($emptyClass) . ' py-8 text-center text-sm text-slate-500">سبد خرید شما خالی است.</div>';
        $fragments['span[data-header-cart-badge]'] = '<span data-header-cart-badge class="absolute -top-1 -left-1 grid h-5 min-w-5 place-items-center rounded-full bg-emerald-600 px-1 text-[10px] font-bold text-white">' . esc_html(number_format_i18n($count)) . '</span>';
        return $fragments;
    }

    /** @param array<string,mixed> $items */
    private static function renderItems(array $items): string
    {
        ob_start();
        foreach ($items as $key => $item) {
            if (!is_array($item) || !is_object($item['data'] ?? null)) continue;
            $product = $item['data']; $qty = max(1, (int) ($item['quantity'] ?? 1));
            $name = method_exists($product, 'get_name') ? (string) $product->get_name() : '';
            $url = method_exists($product, 'get_permalink') ? (string) $product->get_permalink() : '#';
            $image = method_exists($product, 'get_image') ? (string) $product->get_image('thumbnail') : '';
            $price = method_exists($product, 'get_price') ? (float) $product->get_price() : 0.0;
            $line = (float) ($item['line_total'] ?? $price * $qty);
            ?>
            <article data-cart-modal-row="<?php echo esc_attr((string) $key); ?>" data-current-qty="<?php echo esc_attr((string) $qty); ?>" data-initial-qty="<?php echo esc_attr((string) $qty); ?>" data-unit-price="<?php echo esc_attr((string) $price); ?>" data-step-qty="1" class="flex gap-3 rounded-2xl border border-slate-200 p-3"><a href="<?php echo esc_url($url); ?>" class="h-16 w-16 shrink-0 overflow-hidden rounded-xl"><?php echo wp_kses_post($image); ?></a><div class="min-w-0 flex-1"><a href="<?php echo esc_url($url); ?>" class="block truncate text-xs font-bold"><?php echo esc_html($name); ?></a><div class="mt-2 flex items-center justify-between"><div class="flex items-center gap-2"><button type="button" data-cart-modal-dec="<?php echo esc_attr((string) $key); ?>" class="h-6 w-6 rounded border">−</button><span data-cart-modal-qty="<?php echo esc_attr((string) $key); ?>"><?php echo esc_html((string) $qty); ?></span><button type="button" data-cart-modal-inc="<?php echo esc_attr((string) $key); ?>" class="h-6 w-6 rounded border">+</button><button type="button" data-cart-modal-remove="<?php echo esc_attr((string) $key); ?>" class="text-xs text-rose-600">حذف</button></div><strong data-cart-modal-row-total="<?php echo esc_attr((string) $key); ?>" class="text-xs"><?php echo self::price($line); ?></strong></div></div></article>
            <?php
        }
        return (string) ob_get_clean();
    }

    private static function price(float $value): string
    {
        return function_exists('wc_price') ? (string) wc_price($value) : esc_html(number_format_i18n($value) . ' تومان');
    }
}
