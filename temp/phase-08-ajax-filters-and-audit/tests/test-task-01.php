<?php
/**
 * Automated QA Test Script — Task 8.1: Theme AJAX foundation.
 * Phase: phase-08-ajax-filters-and-audit
 */
declare(strict_types=1);

$themeRoot = dirname(__DIR__, 3);
$wpRoot = '/opt/wp-testbed';
$failures = [];
$assert = static function (bool $condition, string $label) use (&$failures): void {
    echo $condition ? "  [PASS] {$label}\n" : "  [FAIL] {$label}\n";
    if (!$condition) $failures[] = $label;
};

require_once $wpRoot . '/wp-load.php';
$GLOBALS['wp']->main();
echo "=== [TEST] Task 8.1: Theme AJAX foundation ===\n";

$class = 'ErfanSanat\\Theme\\Ajax\\AjaxController';
$assert(is_file($themeRoot . '/inc/Ajax/AjaxController.php'), 'AjaxController source exists');
$assert(class_exists($class), 'AjaxController autoloads and is registered');
$assert(has_action('wp_ajax_erfan_sanat_ajax') !== false, 'logged-in AJAX endpoint is registered');
$assert(has_action('wp_ajax_nopriv_erfan_sanat_ajax') !== false, 'public AJAX endpoint is registered');
$assert(wp_verify_nonce(wp_create_nonce('erfan_sanat_ajax'), 'erfan_sanat_ajax') !== false, 'expected AJAX nonce action is valid');

$controller = new $class();
$clean = $controller->sanitizeInput([
    'search' => " <b>LED</b>\n<script>alert(1)</script> ",
    'filters' => ['product_cat' => '<img src=x onerror=alert(1)>lighting', 'price' => ['min' => '12.50']],
    'discard key' => 'value',
    'nonce' => 'sensitive-token',
    'action' => 'erfan_sanat_ajax',
]);
$assert(($clean['search'] ?? null) === 'LED', 'text input strips markup and normalizes unsafe text');
$assert(($clean['filters']['product_cat'] ?? null) === 'lighting', 'nested text inputs are sanitized recursively');
$assert(($clean['filters']['price']['min'] ?? null) === '12.50', 'nested numeric strings remain usable');
$assert(($clean['discardkey'] ?? null) === 'value', 'unsafe array keys are normalized before use');
$assert(!array_key_exists('nonce', $clean) && !array_key_exists('action', $clean), 'transport secrets and action are excluded from route input');

$success = $controller->successResponse(['items' => ['one']], '<article>safe markup</article>', 1);
$assert(($success['success'] ?? null) === true && ($success['data']['items'][0] ?? null) === 'one', 'success response includes standard success/data envelope');
$assert(($success['html'] ?? null) === '<article>safe markup</article>' && ($success['count'] ?? null) === 1, 'success response exposes html and count at top level');
$error = $controller->errorResponse('Invalid request', 403);
$assert(($error['success'] ?? null) === false && ($error['data']['message'] ?? null) === 'Invalid request', 'error response has a safe standard envelope');
$assert(($error['html'] ?? null) === '' && ($error['count'] ?? null) === 0, 'error response has empty html/count');

wp_register_script($class::SCRIPT_HANDLE, '/assets/main.js');
$controller->addAjaxNonce();
$inline = wp_scripts()->get_data($class::SCRIPT_HANDLE, 'before');
$inlineText = is_array($inline) ? implode(' ', $inline) : (is_string($inline) ? $inline : '');
$assert(str_contains($inlineText, 'ajaxNonce'), 'frontend runtime receives a dedicated AJAX nonce without replacing its existing nonce');
$assert(wp_verify_nonce(wp_create_nonce('erfan_sanat_ajax'), 'erfan_sanat_ajax') !== false, 'frontend nonce uses the exact endpoint nonce action');

if ($failures !== []) {
    fwrite(STDERR, "[FAILED] " . implode('; ', $failures) . "\n");
    exit(1);
}
echo "[PASSED] Task 8.1 checks completed successfully.\n";
