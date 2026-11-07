<?php
/**
 * Test Task 1.2 — DeBloat module removes Gutenberg/emoji/extra head tags.
 * Run: php-wasm-cli temp/phase-01-core-and-assets/tests/test-task-1-2.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

$f = $THEME . '/inc/DeBloat.php';
check('inc/DeBloat.php exists', file_exists($f));
$t = file_exists($f) ? file_get_contents($f) : '';

check('namespace ErfanSanat\\Theme', strpos($t, 'namespace ErfanSanat\\Theme;') !== false);
check('class DeBloat defined', strpos($t, 'class DeBloat') !== false);
check('hooks registered (add_action/add_filter present)', strpos($t, 'add_action') !== false && strpos($t, 'add_filter') !== false);

// Gutenberg block CSS/JS must be dequeued
check('removes wp-block-library', strpos($t, 'wp-block-library') !== false);
check('removes global-styles', strpos($t, 'global-styles') !== false);

// Emoji scripts removed
check('disables emoji (emoji_detection_script)', strpos($t, 'emoji') !== false);

// Extra head cleanup
foreach (['wp_generator', 'rsd_link', 'wlwmanifest', 'wp_shortlink_wp_head', 'rest_output_link_wp_head', 'wp_oembed'] as $tag) {
    check("cleans head tag: $tag", strpos($t, $tag) !== false);
}

// Wired into Theme bootstrap
$themeTxt = file_get_contents($THEME . '/inc/Theme.php');
check('Theme.php wires DeBloat', strpos($themeTxt, 'DeBloat') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
