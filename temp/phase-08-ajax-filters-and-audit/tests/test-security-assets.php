<?php
/**
 * SECURITY + ZERO-BLOAT ASSETS AUDIT
 *  1. Scan theme PHP/JS: raw superglobals, echo without escaping, eval, external URLs.
 *  2. On real WP: enqueue audit — every script deferred/async, no jQuery, no emoji/embeds.
 */
$ROOT = dirname(__DIR__, 3); // theme root
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

// ---------- 1. static scan ----------
$phpFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($ROOT, FilesystemIterator::SKIP_DOTS));
$rawSuper = $evals = $extUrls = $echoRaw = [];
foreach ($phpFiles as $f) {
    if (!in_array($f->getExtension(), ['php'], true)) continue;
    $rel = str_replace($ROOT.'/', '', $f->getPathname());
    if (strpos($rel, 'temp/') === 0) continue;
    $c = file_get_contents($f->getPathname());
    // strip comments/strings roughly for superglobal scan
    $stripped = preg_replace('#//.*|/\*.*?\*/#s', '', $c);
    $fileGuarded = strpos($c, 'wp_verify_nonce') !== false || strpos($c, 'check_ajax_referer') !== false;
    foreach (preg_split('/\n/', $stripped) as $ln) {
        if (preg_match('/\$_(GET|POST|REQUEST|COOKIE)\s*\[/', $ln) && !$fileGuarded && !preg_match('/sanitize_|wp_unslash|wp_verify_nonce|check_ajax_referer|absint|intval|!empty/', $ln)) {
            $rawSuper[] = "$rel: " . trim($ln);
        }
    }
    if (preg_match('/\b(eval|assert|system|exec|shell_exec|passthru)\s*\(/', $stripped)) $evals[] = $rel;
    foreach (preg_split('/\n/', $c) as $ln) {
        $code = preg_replace('#^\s*(\*|//).*$#', '', $ln); // strip comment lines
        if (preg_match('#https?://(?!localhost|127\.0\.0\.1|erfansanat\.com|wordpress\.org|w\.org|(www\.)?w3\.org|schema\.org|(t\.me|wa\.me|twitter\.com|x\.com|instagram\.com|linkedin\.com|youtube\.com|aparat\.com)|github\.com|developers\.googleapis|\.\.\.)[a-z0-9.-]+#i', $code, $m)) $extUrls[] = "$rel -> {$m[0]}";
    }
}
check('no raw superglobals without nonce (outside Ajax handlers)', $rawSuper === []);
foreach (array_slice($rawSuper,0,5) as $x) echo "   $x\n";
check('no eval/exec/system in theme', $evals === []);
foreach (array_slice($evals,0,5) as $x) echo "   $x\n";
check('no external CDN/resource URLs in PHP (fonts/icons local)', $extUrls === []);
foreach (array_slice($extUrls,0,8) as $x) echo "   $x\n";

$jsFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($ROOT.'/assets/js', FilesystemIterator::SKIP_DOTS));
$jsIssues = [];
foreach ($jsFiles as $f) {
    if ($f->getExtension() !== 'js') continue;
    $c = file_get_contents($f->getPathname());
    $code = preg_replace('#^\s*\*.*$#m', '', preg_replace('#//.*$#m', '', $c));
    if (preg_match('/\beval\s*\(|new\s+Function|document\.write/', $code)) $jsIssues[] = $f->getFilename().': eval/write';
    if (preg_match('#https?://(?!localhost|127\.0\.0\.1|erfansanat\.com|(www\.)?w3\.org|schema\.org|\.\.\.)[a-z0-9.-]+#i', $code, $m)) $jsIssues[] = $f->getFilename().': ext '.$m[0];
}
check('JS clean: no eval/document.write/external URLs', $jsIssues === []);
foreach ($jsIssues as $x) echo "   $x\n";

// escaping: echo of variables not wrapped in esc_*/wp_kses — heuristic on templates
$escIssues = [];
foreach ($phpFiles as $f) {
    if (!in_array($f->getExtension(), ['php'], true)) continue;
    $rel = str_replace($ROOT.'/', '', $f->getPathname());
    if (strpos($rel, 'temp/') === 0 || strpos($rel, 'inc/') === 0) continue;
    foreach (preg_split('/\n/', file_get_contents($f->getPathname())) as $ln) {
        if (preg_match('/echo\s+\$[a-zA-Z_]/', $ln) && !preg_match('/esc_|wp_kses|wp_json|number_format|__\(|_e\(|absint|intval/', $ln)) {
            $escIssues[] = "$rel: " . trim($ln);
        }
    }
}
check('templates: no raw echo of variables (all escaped)', $escIssues === []);
foreach (array_slice($escIssues,0,8) as $x) echo "   $x\n";

// ---------- 2. real-WP enqueue audit ----------
$WP = '/home/user/wp-test';
@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('init');
wp_insert_post(['post_type'=>'page','post_title'=>'h','post_status'=>'publish']);
$pid = wp_insert_post(['post_type'=>'product','post_title'=>'p','post_status'=>'publish']);
query_posts(['page_id'=>1]);
ob_start(); include get_theme_file_path('front-page.php'); ob_end_clean();
global $wp_scripts, $wp_styles;
$blocking = [];
foreach ($wp_scripts->registered as $h => $s) {
    if (in_array($h, $wp_scripts->queue, true)) {
        $extra = $s->extra['strategy'] ?? '';
        if (strpos($extra, 'defer') === false && strpos($extra, 'async') === false && $h !== 'jquery') {
            // only flag if enqueued in head (WP puts footer scripts last) — strategy empty + not footer
            if (empty($s->extra['group']) && strpos($extra,'footer')===false && ($s->src && strpos((string)$s->src,'wp-includes')===false)) $blocking[] = $h;
        }
    }
}
check('no blocking theme scripts (all defer/async)', $blocking === []);
foreach (array_slice($blocking,0,5) as $x) echo "   blocking: $x\n";
check('jQuery NOT enqueued on front-end', !in_array('jquery', $wp_scripts->queue, true));
check('wp-emoji removed', !in_array('wp-emoji', $wp_styles->queue, true) && !in_array('wp-emoji', $wp_scripts->queue, true));
echo $fail===0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail>0?1:0);
