<?php
/**
 * Test Task 1.1 — Theme skeleton & OOP bootstrap
 * Run: php-wasm-cli temp/phase-01-core-and-assets/tests/test-task-1-1.php
 * Pure static checks (no WP runtime in harness): file existence + content assertions.
 */
$THEME = dirname(__DIR__, 3); // repo root = theme root
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

// 1. style.css exists with a valid WP theme header
$style = $THEME . '/style.css';
check('style.css exists', file_exists($style));
$styleTxt = file_exists($style) ? file_get_contents($style) : '';
check('style.css has "Theme Name:" header', (bool) preg_match('/^\s*\*\s*Theme Name:/m', $styleTxt));
check('style.css has "Text Domain:" header', (bool) preg_match('/^\s*\*\s*Text Domain:\s*erfansanat/m', $styleTxt));

// 2. functions.php exists and bootstraps the OOP core
$func = $THEME . '/functions.php';
check('functions.php exists', file_exists($func));
$funcTxt = file_exists($func) ? file_get_contents($func) : '';
check('functions.php requires inc/Autoloader.php', strpos($funcTxt, 'Autoloader.php') !== false);
check('functions.php boots Theme class', strpos($funcTxt, 'Theme::instance') !== false || strpos($funcTxt, 'ErfanSanat\\Theme') !== false);

// 3. add_theme_support calls required by pass conditions
foreach (['title-tag', 'post-thumbnails', 'woocommerce', 'html5'] as $support) {
    check("add_theme_support('$support') registered", strpos($funcTxt, "'$support'") !== false || strpos(file_get_contents($THEME . '/inc/Theme.php') ?: '', "'$support'") !== false);
}

// 4. OOP architecture: Autoloader + Theme class with ErfanSanat\Theme namespace
$autoload = $THEME . '/inc/Autoloader.php';
check('inc/Autoloader.php exists', file_exists($autoload));
$autoTxt = file_exists($autoload) ? file_get_contents($autoload) : '';
check('Autoloader declares namespace ErfanSanat\\Theme', strpos($autoTxt, 'namespace ErfanSanat\\Theme;') !== false);
check('Autoloader registers spl_autoload', strpos($autoTxt, 'spl_autoload_register') !== false);
$themeCls = $THEME . '/inc/Theme.php';
check('inc/Theme.php exists', file_exists($themeCls));
$themeTxt = file_exists($themeCls) ? file_get_contents($themeCls) : '';
check('Theme class in namespace ErfanSanat\\Theme', strpos($themeTxt, 'namespace ErfanSanat\\Theme;') !== false && strpos($themeTxt, 'class Theme') !== false);
check('Theme registers after_setup_theme hook', strpos($themeTxt, 'after_setup_theme') !== false);

// 5. index.php fallback template with the loop
$index = $THEME . '/index.php';
check('index.php exists', file_exists($index));
$indexTxt = file_exists($index) ? file_get_contents($index) : '';
check('index.php has have_posts()/the_post() loop', strpos($indexTxt, 'have_posts()') !== false && strpos($indexTxt, 'the_post()') !== false);
check('index.php calls get_header()/get_footer()', strpos($indexTxt, 'get_header()') !== false && strpos($indexTxt, 'get_footer()') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
