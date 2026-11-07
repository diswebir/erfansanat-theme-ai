<?php
/**
 * فیلدهای سفارشی checkout — نوع خریدار و شناسه ملی/کد اقتصادی (میرور checkout.html)
 *
 * buyer_type (radio میرور) و billing_es_tax_id فیلدهای optional هستند تا فرآیند
 * درگاه‌ها و چک‌اوت استاندارد ووکامرس نشکند (خط‌قرمز ۶.۱۰)؛ مقادیر در متای سفارش ذخیره
 * می‌شوند: es_buyer_type / es_tax_id.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Woo;

final class CheckoutFields {

    public static function init(): void {
        /**
         * فیلد اختیاری شناسه ملی — روی فرم billing استاندارد سوار می‌شود.
         */
        add_filter('woocommerce_billing_fields', [self::class, 'billing_fields'], 20);

        /**
         * ذخیره در متای سفارش هنگام ساخت سفارش (پیش از پرداخت).
         */
        add_action('woocommerce_checkout_create_order', [self::class, 'save_order_meta'], 10, 2);
    }

    /**
     * افزودن فیلد اختیاری شناسه ملی/کد اقتصادی به فیلدهای billing.
     *
     * @param array<string, mixed> $fields فیلدهای استاندارد ووکامرس
     * @return array<string, mixed>
     */
    public static function billing_fields(array $fields): array {
        $fields['billing_es_tax_id'] = [
            'label'    => __('شناسه ملی / کد اقتصادی (اختیاری - ویژه فاکتور رسمی)', 'erfansanat'),
            'required' => false,
            'class'    => ['form-row-wide'],
            'priority' => 95,
            'type'     => 'text',
        ];
        return $fields;
    }

    /**
     * ذخیره buyer_type و tax id در متای سفارش — هر دو اختیاری/ایمن.
     *
     * @param object $order سفارش ووکامرس
     * @param array<string, mixed> $data داده‌های posted چک‌اوت
     */
    public static function save_order_meta($order, array $data = []): void {
        if (!is_object($order) || !method_exists($order, 'update_meta_data')) {
            return;
        }
        $es_buyer = isset($_POST['buyer_type']) && is_string($_POST['buyer_type'])
            ? sanitize_text_field((string) wp_unslash($_POST['buyer_type']))
            : '';
        if ($es_buyer !== '') {
            $order->update_meta_data('es_buyer_type', $es_buyer);
        }
        $es_tax = isset($_POST['billing_es_tax_id']) && is_string($_POST['billing_es_tax_id'])
            ? sanitize_text_field((string) wp_unslash($_POST['billing_es_tax_id']))
            : '';
        if ($es_tax === '' && isset($data['billing_es_tax_id']) && is_string($data['billing_es_tax_id'])) {
            $es_tax = sanitize_text_field((string) $data['billing_es_tax_id']);
        }
        if ($es_tax !== '') {
            $order->update_meta_data('es_tax_id', $es_tax);
        }
    }
}
