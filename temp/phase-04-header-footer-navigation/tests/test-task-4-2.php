<?php
/**
 * Test Task 4.2 — custom nav walkers for the 4 menu locations.
 * Run: php-wasm-cli temp/phase-04-header-footer-navigation/tests/test-task-4-2.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

$f = $THEME . '/inc/Nav/MegaMenuWalker.php';
check('inc/Nav/MegaMenuWalker.php exists', is_file($f));
$t = is_file($f) ? file_get_contents($f) : '';

check('namespace ErfanSanat\\Theme\\Nav', strpos($t, 'namespace ErfanSanat\\Theme\\Nav;') !== false);
check('walker extends Walker_Nav_Menu', strpos($t, 'extends Walker_Nav_Menu') !== false || strpos($t, 'extends \\Walker_Nav_Menu') !== false);
check('mega trigger data attr emitted', strpos($t, 'data-desktop-mega-trigger') !== false);
check('mega panel container emitted', strpos($t, 'data-mega-panel') !== false || strpos($t, 'mega-panel') !== false);
check('mobile hooks (data-mobile-acc-btn/panel)', strpos($t, 'data-mobile-acc-btn') !== false && strpos($t, 'data-mobile-acc-panel') !== false);
check('escapes output (esc_attr/esc_html/esc_url)', strpos($t, 'esc_attr') !== false && strpos($t, 'esc_url') !== false);

// header.php uses wp_nav_menu for all 4 locations with walkers
$h = file_get_contents("$THEME/header.php");
foreach (['main-mega-menu', 'mobile-menu', 'tolidat-sherkat', 'landing'] as $loc) {
    $ft = file_get_contents("$THEME/footer.php");
    check("header/footer registers location: $loc", strpos($h, "'$loc'") !== false || strpos($h, '"' . $loc . '"') !== false || strpos($ft, "'$loc'") !== false || strpos($ft, '"' . $loc . '"') !== false);
}
check('wp_nav_menu with walker used', (bool) preg_match('/wp_nav_menu\s*\(/', $h) || (bool) preg_match('/wp_nav_menu\s*\(/', file_get_contents("$THEME/footer.php")));

// Theme registers the 4 locations (already in register_core) — verify
$themeTxt = file_get_contents($THEME . '/inc/Theme.php');
foreach (['main-mega-menu', 'mobile-menu', 'tolidat-sherkat', 'landing'] as $loc) {
    check("Theme registers menu location: $loc", strpos($themeTxt, "'$loc'") !== false);
}
check('header.php instantiates MegaMenuWalker', strpos($h, 'MegaMenuWalker') !== false && strpos($h, 'MobileMenuWalker') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
