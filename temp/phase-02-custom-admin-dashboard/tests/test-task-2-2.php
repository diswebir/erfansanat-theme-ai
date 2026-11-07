<?php
/**
 * Test Task 2.2 — global management tabs with static-matching defaults.
 * Run: php-wasm-cli temp/phase-02-custom-admin-dashboard/tests/test-task-2-2.php
 */
$THEME = dirname(__DIR__, 3);
$STATIC = '/home/user/static-mirror/static-mirror';
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

$t = file_get_contents("$THEME/inc/Admin/ThemeOptions.php");

// defaults() method with static-fallback values
check('defaults() method exists', strpos($t, 'function defaults') !== false);
foreach (['logo', 'announcement', 'email', 'phone', 'address', 'social', 'footer_text', 'brand_color'] as $key) {
    check("default key present: $key", strpos($t, "'$key'") !== false || strpos($t, '"' . $key . '"') !== false);
}

// Defaults must equal live static content where verifiable
$staticIndex = file_get_contents("$STATIC/index.html");
check('default email == info@erfansanat.com (from static)', strpos($t, 'info@erfansanat.com') !== false && strpos($staticIndex, 'info@erfansanat.com') !== false);
check('default logo points to ese-logo.svg', strpos($t, 'ese-logo.svg') !== false);

// Render has fields for each tab (input/select/textarea named options[...])
check('render outputs named option fields', strpos($t, "options[") !== false);
foreach (['general', 'header', 'footer', 'brand'] as $tab) {
    check("tab panel rendered: $tab", strpos($t, "data-panel=\"$tab\"") !== false);
}
check('media uploader wired (wp_enqueue_media)', strpos($t, 'wp_enqueue_media') !== false);

// es_option falls back to defaults
check('es_option falls back to defaults()', strpos($t, 'defaults()') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
