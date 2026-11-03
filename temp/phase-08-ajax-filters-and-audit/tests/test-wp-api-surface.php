<?php
/**
 * WP API SURFACE TEST — every WP function the theme calls must exist in
 * real WordPress core (cloned from github.com/WordPress/WordPress).
 * Catches typos / invented functions. Theme's own namespaced functions,
 * PHP builtins and stub-defined functions are whitelisted.
 *
 * Run: php-wasm-cli temp/phase-08-ajax-filters-and-audit/tests/test-wp-api-surface.php
 */

$THEME = dirname(__DIR__, 3);
$WPCORE = '/home/user/wp-core';

$fail = 0;
function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

// 1. Build the WP function list from core source
$wpFuncs = [];
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($WPCORE . '/wp-includes', FilesystemIterator::SKIP_DOTS));
foreach ($rii as $f) {
    if ($f->getExtension() !== 'php') { continue; }
    preg_match_all('/^\s*function\s+([a-zA-Z0-9_]+)\s*\(/m', file_get_contents($f->getPathname()), $m);
    foreach ($m[1] as $fn) { $wpFuncs[strtolower($fn)] = true; }
}
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($WPCORE . '/wp-admin/includes', FilesystemIterator::SKIP_DOTS));
foreach ($rii as $f) {
    if ($f->getExtension() !== 'php') { continue; }
    preg_match_all('/^\s*function\s+([a-zA-Z0-9_]+)\s*\(/m', file_get_contents($f->getPathname()), $m);
    foreach ($m[1] as $fn) { $wpFuncs[strtolower($fn)] = true; }
}
check('WP core function list built (' . count($wpFuncs) . ' functions)', count($wpFuncs) > 1000);

// WooCommerce functions (wc_*) live in the plugin, not core — whitelist prefix
$wcPrefixes = ['wc_', 'woocommerce_'];

// theme's own global helpers
$ownFuncs = ['es_option', 'es_sections', 'es_hreflang_tags'];

// 2. Scan theme PHP files for called functions
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($THEME, FilesystemIterator::SKIP_DOTS));
$unknown = [];
$checked = 0;
foreach ($rii as $f) {
    $p = $f->getPathname();
    if ($f->getExtension() !== 'php') { continue; }
    if (strpos($p, '/temp/') !== false || strpos($p, '/.git/') !== false) { continue; }
    $code = file_get_contents($p);
    // strip comments and strings crudely
    $code = preg_replace('/\/\*.*?\*\//s', '', $code);
    $code = preg_replace('/\/\/[^\n]*/', '', $code);
    $code = preg_replace('/#[^\n]*/', '', $code);
    $code = preg_replace("/'(?:[^'\\\\]|\\\\.)*'/", "''", $code);
    $code = preg_replace('/"(?:[^"\\\\]|\\\\.)*"/', '""', $code);
    $code = preg_replace('/function\s+[a-zA-Z0-9_]+\s*\(/', 'function (', $code); // strip definitions
    preg_match_all('/(?<![>:\\$a-zA-Z0-9_])([a-zA-Z_][a-zA-Z0-9_]*)\s*\(/', $code, $m);
    foreach (array_unique($m[1]) as $fn) {
        $l = strtolower($fn);
        $checked++;
        if (isset($wpFuncs[$l])) { continue; }                       // real WP function
        if (function_exists($fn)) { continue; }                        // PHP builtin
        if (in_array($l, $ownFuncs, true)) { continue; }               // theme helper
        $isWc = false;
        foreach ($wcPrefixes as $pre) { if (strpos($l, $pre) === 0) { $isWc = true; break; } }
        if ($isWc) { continue; }                                       // WooCommerce plugin API
        // keywords/control structures are not functions
        if (in_array($l, ['wp_query','wp_post','wp_error','self','static','parent','walker_nav_menu','megamenuwalker','mobilemenuwalker','project_type','led'], true)) { continue; } // classes + taxonomy label
        if (in_array($l, ['if','foreach','while','for','switch','return','echo','print','isset','empty','unset','include','require','function','class','new','fn','match','list','array','exit','die','global','static','public','private','protected','abstract','final','try','catch','throw','use','namespace','extends','implements','instanceof','clone','yield','declare'], true)) { continue; }
        $unknown[] = "$fn  ($p)";
    }
}
check("all called functions resolve to WP core / builtin / own ($checked calls scanned)", $unknown === []);
foreach (array_slice($unknown, 0, 20) as $u) { echo "  UNKNOWN: $u\n"; }

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
