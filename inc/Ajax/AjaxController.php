<?php
/**
 * Public theme AJAX endpoint, nonce and response contract.
 *
 * @package ErfanSanat\Theme\Ajax
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Ajax;

use ErfanSanat\Theme\Contracts\ServiceInterface;
use Throwable;

final class AjaxController implements ServiceInterface
{
    public const ACTION = 'erfan_sanat_ajax';
    public const NONCE_ACTION = 'erfan_sanat_ajax';
    public const SCRIPT_HANDLE = 'erfansanat-main';

    public function register(): void
    {
        add_action('wp_ajax_' . self::ACTION, [$this, 'handle']);
        add_action('wp_ajax_nopriv_' . self::ACTION, [$this, 'handle']);
        add_action('wp_enqueue_scripts', [$this, 'addAjaxNonce'], 21);
    }

    /** Add the phase-specific nonce without replacing Phase 1's existing runtime config. */
    public function addAjaxNonce(): void
    {
        $nonce = wp_create_nonce(self::NONCE_ACTION);
        $inline = 'window.esThemeData=window.esThemeData||{};window.esThemeData.ajaxNonce=' . wp_json_encode($nonce) . ';';
        wp_add_inline_script(self::SCRIPT_HANDLE, $inline, 'before');
    }

    /**
     * Dispatch one allow-listed route and always return the documented JSON shape.
     */
    public function handle(): void
    {
        if (!check_ajax_referer(self::NONCE_ACTION, 'nonce', false)) {
            wp_send_json($this->errorResponse('درخواست نامعتبر است.'), 403);
        }

        $raw = isset($_POST) && is_array($_POST) ? wp_unslash($_POST) : [];
        $input = $this->sanitizeInput($raw);
        $route = isset($input['route']) && is_string($input['route']) ? sanitize_key($input['route']) : '';
        if ($route === 'recruitment_submission' && isset($_FILES) && is_array($_FILES)) $input['_files'] = $_FILES;
        $routes = apply_filters('erfan_sanat_ajax_routes', []);
        $handler = is_array($routes) ? ($routes[$route] ?? null) : null;

        if ($route === '' || !is_callable($handler)) {
            wp_send_json($this->errorResponse('درخواست پشتیبانی نمی‌شود.'), 400);
        }

        try {
            $result = $handler($input);
        } catch (Throwable) {
            wp_send_json($this->errorResponse('در پردازش درخواست خطایی رخ داد.'), 500);
        }

        if ($result instanceof \WP_Error) {
            $status = $result->get_error_code() === 'form_rate_limited' ? 429 : 400;
            wp_send_json($this->errorResponse($result->get_error_message()), $status);
        }
        if (!is_array($result)) {
            wp_send_json($this->errorResponse('پاسخ سرویس معتبر نیست.'), 500);
        }

        $data = isset($result['data']) && is_array($result['data']) ? $result['data'] : [];
        $html = isset($result['html']) && is_string($result['html']) ? $result['html'] : '';
        $count = isset($result['count']) && is_numeric($result['count']) ? absint($result['count']) : 0;
        wp_send_json($this->successResponse($data, $html, $count));
    }

    /**
     * Recursively sanitize request values while excluding transport secrets.
     *
     * @param array<string|int,mixed> $input
     * @return array<string|int,mixed>
     */
    public function sanitizeInput(array $input): array
    {
        $clean = [];
        foreach ($input as $key => $value) {
            $safeKey = is_int($key) ? $key : sanitize_key((string) $key);
            if ($safeKey === '' || in_array($safeKey, ['action', 'nonce', '_ajax_nonce', 'security'], true)) {
                continue;
            }
            $safeValue = $this->sanitizeValue($value, 0);
            if ($safeValue !== null) {
                $clean[$safeKey] = $safeValue;
            }
        }
        return $clean;
    }

    /** @param array<string,mixed> $data @return array{success:true,data:array<string,mixed>,html:string,count:int} */
    public function successResponse(array $data = [], string $html = '', int $count = 0): array
    {
        return [
            'success' => true,
            'data' => $data,
            'html' => $html,
            'count' => max(0, $count),
        ];
    }

    /** @return array{success:false,data:array{message:string},html:string,count:int} */
    public function errorResponse(string $message): array
    {
        return [
            'success' => false,
            'data' => ['message' => sanitize_text_field($message)],
            'html' => '',
            'count' => 0,
        ];
    }

    private function sanitizeValue(mixed $value, int $depth): mixed
    {
        if ($depth > 5) {
            return null;
        }
        if (is_array($value)) {
            $clean = [];
            foreach ($value as $key => $item) {
                $safeKey = is_int($key) ? $key : sanitize_key((string) $key);
                if ($safeKey === '') {
                    continue;
                }
                $safeValue = $this->sanitizeValue($item, $depth + 1);
                if ($safeValue !== null) {
                    $clean[$safeKey] = $safeValue;
                }
            }
            return $clean;
        }
        if (is_string($value)) {
            return sanitize_text_field($value);
        }
        if (is_int($value) || is_float($value) || is_bool($value)) {
            return $value;
        }
        return null;
    }
}
