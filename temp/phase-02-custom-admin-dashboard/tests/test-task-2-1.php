<?php
/**
 * Test Task 2.1 — Custom admin dashboard (ThemeOptions) security + storage.
 * Run: php-wasm-cli temp/phase-02-custom-admin-dashboard/tests/test-task-2-1.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

$f = $THEME . '/inc/Admin/ThemeOptions.php';
check('inc/Admin/ThemeOptions.php exists', is_file($f));
$t = is_file($f) ? file_get_contents($f) : '';

check('namespace ErfanSanat\\Theme\\Admin', strpos($t, 'namespace ErfanSanat\\Theme\\Admin;') !== false);
check('class ThemeOptions defined', strpos($t, 'class ThemeOptions') !== false);

// Security: capability + nonce on menu registration, save, and ajax
check('registers admin menu (add_menu_page)', strpos($t, 'add_menu_page') !== false);
check('capability check manage_options', strpos($t, "manage_options") !== false);
check('nonce verification present', strpos($t, 'wp_verify_nonce') !== false || strpos($t, 'check_admin_referer') !== false);
check('AJAX save handler registered', strpos($t, 'wp_ajax_') !== false);

// Storage: single option + cached helper
check('single option erfansanat_theme_options', strpos($t, 'erfansanat_theme_options') !== false);
check('es_option() helper defined', strpos($t, 'function es_option') !== false);
check('static cache in helper (static $cache)', strpos($t, 'static $cache') !== false);

// RTL modern admin page
check('admin_enqueue_scripts for admin assets', strpos($t, 'admin_enqueue_scripts') !== false);
check('dir=rtl handling', strpos($t, 'rtl') !== false);

// Wired into Theme bootstrap
$themeTxt = file_get_contents($THEME . '/inc/Theme.php');
check('Theme.php wires Admin (is_admin guard)', strpos($themeTxt, 'is_admin()') !== false && strpos($themeTxt, 'ThemeOptions') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
