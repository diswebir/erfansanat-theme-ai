<?php
/**
 * Test 4.2 — header.php shell: language_attributes/body_class/wp_body_open/wp_head + hreflang شرطی با فیلتر
 * اجرا: /home/user/tools/php -f temp/phase-04-header-footer-navigation/tests/test-task-02.php
 */
declare(strict_types=1);

error_reporting(E_ALL);
$failures = [];
$passes   = 0;
function check(bool $cond, string $label): void {
    global $failures, $passes;
    if ($cond) { $passes++; echo "  PASS  $label\n"; }
    else       { $failures[] = $label; echo "  FAIL  $label\n"; }
}

$root = dirname(ES_SCRIPT_PATH, 4);
echo "== Test 4.2: shell هدر ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\HreflangMeta;

// ── منبع header.php ──
$src = (string) file_get_contents($root . '/header.php');
check(str_contains($src, 'language_attributes()'), 'language_attributes');
check(str_contains($src, 'body_class()'), 'body_class');
check(str_contains($src, 'wp_body_open()'), 'wp_body_open');
check(str_contains($src, 'wp_head()'), 'wp_head');
check(str_contains($src, 'bloginfo(\'charset\')') || str_contains($src, 'bloginfo("charset")'), 'charset');

// ── رندر واقعی header.php (بدون پارت‌ها — silent) ──
$GLOBALS['__es_stub_locale'] = 'fa_IR';
language_attributes_reset();
ob_start();
include $root . '/header.php';
$out = (string) ob_get_clean();
check(str_starts_with(ltrim($out), '<!doctype html>'), 'doctype اول خروجی');
check(str_contains($out, '<html dir="rtl" lang="fa-IR">'), 'language_attributes رندر شد');
check(preg_match('/<body[^>]*class="/', $out) === 1, 'body_class در تگ body');
check(str_contains($out, 'wp-body-open-marker'), 'wp_body_open اجرا شد');
check(str_contains($out, 'wp-head-marker'), 'wp_head اجرا شد');

// ── hreflang: فقط با متا؛ فیلتر غیرفعال‌سازی ──
$GLOBALS['__wp_is_admin'] = false;
$GLOBALS['__es_stub_current_post_type'] = 'product';
$GLOBALS['__es_stub_queried_object_id'] = 500;
$GLOBALS['__wp_post_meta'][500] = [
    'hrefmeta_fa'    => 'https://erfansanat.com/product/x/',
    'hrefmeta_en'    => 'https://erfansanat.com/en/product/x/',
    'same_key_hrefl' => 'https://erfansanat.com/product/x/',
];
ob_start();
do_action('wp_head');
$head = (string) ob_get_clean();
check(str_contains($head, 'hreflang="fa"'), 'hreflang fa از متا');
check(str_contains($head, 'hreflang="x-default"'), 'x-default');

// فیلتر → بدون خروجی
$GLOBALS['__wp_hooks']['filters']['es_disable_builtin_hreflang'] = [['cb' => static fn(): bool => true, 'priority' => 10]];
ob_start();
HreflangMeta::render_head();
$head2 = (string) ob_get_clean();
check(trim($head2) === '', 'فیلتر es_disable_builtin_hreflang → بدون خروجی (هماهنگی hreflangpch)');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
