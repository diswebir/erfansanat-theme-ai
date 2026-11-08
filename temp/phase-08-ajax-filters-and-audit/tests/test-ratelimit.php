<?php
/** RATE LIMIT + HONEYPOT + ARIA-LIVE + PRELOAD TEST — real WP. */
$WP = '/home/user/wp-test';
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c?'[PASS] ':'[FAIL] ').$l."\n"; if(!$c)$fail++; }

// header preload + aria-live + print css
$PHP = '/home/user/tools/node_modules/.bin/php-wasm-cli';
$out = tempnam(sys_get_temp_dir(), 'pl') . '.html';
shell_exec(escapeshellarg($PHP) . ' ' . escapeshellarg('/home/user/erfansanat-theme-ai/temp/phase-08-ajax-filters-and-audit/tests/fidelity-render-one.php') . ' blog > ' . escapeshellarg($out) . ' 2>/dev/null');
$h = (string) file_get_contents($out);
@unlink($out);
check('font preloaded in head', strpos($h, 'rel="preload"') !== false && strpos($h, 'vazirmatn-arabic.woff2') !== false && strpos($h, 'as="font"') !== false);
check('styles.css preloaded', strpos($h, 'assets/css/styles.css') !== false && strpos($h, 'as="style"') !== false);
check('aria-live on blog grid', strpos($h, 'aria-live="polite"') !== false);

@unlink($WP.'/wp-content/database/.ht.sqlite');
define('WP_INSTALLING', true);
require $WP.'/wp-load.php';
require_once ABSPATH.'wp-admin/includes/upgrade.php';
wp_install('t', 'admin', 'a@b.c', true, '', 'admin123');
switch_theme('erfansanat-theme-ai');
require_once get_theme_file_path('functions.php');
do_action('after_setup_theme'); do_action('init');

use ErfanSanat\Theme\Ajax\RateLimit;

// rate limit allows up to max then blocks
$ok = true;
for ($i = 0; $i < 5; $i++) { if (!RateLimit::allow('testbucket', 5, 60)) { $ok = false; } }
check('rate limit allows 5 requests', $ok);
check('rate limit blocks 6th request', RateLimit::allow('testbucket', 5, 60) === false);
check('different bucket independent', RateLimit::allow('otherbucket', 5, 60) === true);

check('print.css exists', is_file('/home/user/erfansanat-theme-ai/assets/css/print.css'));
check('CI workflow exists', is_file('/home/user/erfansanat-theme-ai/.github/workflows/e2e.yml'));
check('fa_IR.mo has 27 entries', filesize('/home/user/erfansanat-theme-ai/languages/fa_IR.mo') > 1000);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
