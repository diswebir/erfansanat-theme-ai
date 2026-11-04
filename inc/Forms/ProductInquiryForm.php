<?php
/** Product-specific quotation inquiry form handler. */
declare(strict_types=1);

namespace ErfanSanat\Theme\Forms;

use ErfanSanat\Theme\Contracts\ServiceInterface;
use WP_Error;

final class ProductInquiryForm implements ServiceInterface
{
    public function register(): void
    {
        add_filter('erfan_sanat_ajax_routes', [$this, 'registerRoute']);
    }

    /** @param mixed $routes @return array<string,callable> */
    public function registerRoute(mixed $routes): array
    {
        $routes = is_array($routes) ? $routes : [];
        $routes['product_inquiry'] = [$this, 'handle'];
        return $routes;
    }

    /** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
    public function submit(array $input): array|WP_Error
    {
        $productId = isset($input['product_id']) && is_scalar($input['product_id']) ? absint($input['product_id']) : 0;
        $product = $productId > 0 ? get_post($productId) : null;
        if (!$product instanceof \WP_Post || $product->post_type !== 'product' || $product->post_status !== 'publish') {
            return new WP_Error('inquiry_product_invalid', 'محصول برای استعلام در دسترس نیست.');
        }
        if ((string) get_post_meta($productId, 'true_sale', true) !== 'false') {
            return new WP_Error('inquiry_not_required', 'این محصول امکان خرید آنلاین دارد.');
        }

        $fields = (new FormManager())->sanitizeFields($input);
        $name = trim($fields['name'] ?? '');
        $phone = preg_replace('/[^0-9+]/', '', $fields['phone'] ?? '') ?: '';
        $message = trim($fields['message'] ?? '');
        if (strlen($name) < 2) return new WP_Error('inquiry_name_required', 'نام را به‌درستی وارد کنید.');
        if (!preg_match('/^\+?[0-9]{7,15}$/', $phone)) return new WP_Error('inquiry_phone_invalid', 'شماره تماس معتبر وارد کنید.');
        if ($message === '' || (function_exists('mb_strlen') ? mb_strlen($message) : strlen($message)) > 5000) return new WP_Error('inquiry_message_invalid', 'شرح درخواست را بررسی کنید.');

        $fields['name'] = $name;
        $fields['phone'] = $phone;
        $fields['message'] = $message;
        $fields['product_id'] = (string) $productId;
        $fields['product_title'] = sanitize_text_field($product->post_title);
        $id = (new FormManager())->saveSubmission('product-inquiry', $fields, $input);
        if (is_wp_error($id)) return $id;
        $to = sanitize_email((string) get_option('admin_email'));
        if ($to !== '') wp_mail($to, 'استعلام قیمت محصول از وب‌سایت', "محصول: {$product->post_title}\nنام: {$name}\nتلفن: {$phone}\n\n{$message}");
        return ['submission_id' => $id, 'message' => 'درخواست پیش‌فاکتور شما با موفقیت ثبت شد.'];
    }

    /** @param array<string,mixed> $input @return array<string,mixed>|WP_Error */
    public function handle(array $input): array|WP_Error
    {
        $result = $this->submit($input);
        if (is_wp_error($result)) return $result;
        return ['data' => $result, 'html' => '', 'count' => 1];
    }
}
