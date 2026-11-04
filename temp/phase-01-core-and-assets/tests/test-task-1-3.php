<?php
/**
 * Test Task 1.3 — assets transferred + AssetManager conditional enqueue.
 * Run: php-wasm-cli temp/phase-01-core-and-assets/tests/test-task-1-3.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

// 1. Assets exist in theme with identical structure to the static mirror
foreach ([
    'assets/css/styles.css',
    'assets/js/main.js',
    'assets/fonts/vazirmatn-arabic.woff2',
    'assets/fonts/vazirmatn-latin-ext.woff2',
    'assets/fonts/vazirmatn-latin.woff2',
    'assets/images/ese-logo.svg',
    'assets/images/hero.jpg',
    'assets/images/hero-video.mp4',
] as $rel) {
    check("asset exists: $rel", is_file("$THEME/$rel"));
}

// 2. CSS url(...) font references resolve on disk (no broken relative paths)
$css = file_get_contents("$THEME/assets/css/styles.css");
preg_match_all('/url\([\'"]?(\.\.\/[^\'")]+)[\'"]?\)/', $css, $m);
$allOk = true;
foreach (array_unique($m[1]) as $rel) {
    $target = $THEME . '/assets/css/' . $rel; // resolve relative to css dir
    $target = realpath(dirname("$THEME/assets/css/styles.css") . '/' . $rel);
    if ($target === false || !is_file($target)) { $allOk = false; echo "  broken: $rel\n"; }
}
check('all url(../...) references in CSS resolve to real files', $allOk && count($m[1]) > 0);

// 3. AssetManager class + wiring
$f = "$THEME/inc/AssetManager.php";
check('inc/AssetManager.php exists', is_file($f));
$t = is_file($f) ? file_get_contents($f) : '';
check('namespace ErfanSanat\\Theme', strpos($t, 'namespace ErfanSanat\\Theme;') !== false);
check('enqueues styles.css', strpos($t, 'assets/css/styles.css') !== false);
check('enqueues main.js with footer+defer', strpos($t, 'assets/js/main.js') !== false && (strpos($t, 'strategy') !== false && strpos($t, 'defer') !== false) || strpos($t, 'wp_script_add_data') !== false || strpos($t, 'defer_main') !== false);
check('filemtime versioning', strpos($t, 'filemtime') !== false);
check('hooked to wp_enqueue_scripts', strpos($t, 'wp_enqueue_scripts') !== false);
$themeTxt = file_get_contents("$THEME/inc/Theme.php");
check('Theme.php wires AssetManager', strpos($themeTxt, 'AssetManager') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
