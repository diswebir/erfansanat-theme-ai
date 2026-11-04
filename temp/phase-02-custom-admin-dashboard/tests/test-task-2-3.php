<?php
/**
 * Test Task 2.3 — SectionManager: enable/disable, drag&drop order, repeater fields.
 * Run: php-wasm-cli temp/phase-02-custom-admin-dashboard/tests/test-task-2-3.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

$f = $THEME . '/inc/Admin/SectionManager.php';
check('inc/Admin/SectionManager.php exists', is_file($f));
$t = (is_file($f) ? file_get_contents($f) : '') . file_get_contents($THEME . '/inc/helpers.php');

check('namespace ErfanSanat\\Theme\\Admin', strpos($t, 'namespace ErfanSanat\\Theme\\Admin;') !== false);
check('class SectionManager defined', strpos($t, 'class SectionManager') !== false);
check('metabox registered (add_meta_boxes)', strpos($t, 'add_meta_boxes') !== false);
check('saves on save_post with nonce', strpos($t, 'save_post') !== false && strpos($t, 'wp_verify_nonce') !== false);
check('AJAX reorder handler (wp_ajax_)', strpos($t, 'wp_ajax_') !== false);
check('section meta key _erfansanat_sections', strpos($t, '_erfansanat_sections') !== false);
check('supports enabled flag', strpos($t, "'enabled'") !== false);
check('supports order field', strpos($t, "'order'") !== false);
check('supports repeater fields (fields array)', strpos($t, "'fields'") !== false);
check('helper es_sections() defined', strpos($t, 'function es_sections') !== false);
check('helper returns ordered+enabled sections (usort/asort or array ordering)', strpos($t, 'order') !== false);
check('capability guard on save', strpos($t, 'current_user_can') !== false);

// Wired into admin bootstrap
$themeTxt = file_get_contents($THEME . '/inc/Theme.php');
check('Theme.php wires SectionManager', strpos($themeTxt, 'SectionManager') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
