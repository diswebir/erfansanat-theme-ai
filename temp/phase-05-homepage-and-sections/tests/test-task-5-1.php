<?php
/**
 * Test Task 5.1 — homepage sections cut into parts + front-page with SectionManager.
 * Run: php-wasm-cli temp/phase-05-homepage-and-sections/tests/test-task-5-1.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;

function check(string $label, bool $cond): void {
    global $fail;
    echo ($cond ? "[PASS] " : "[FAIL] ") . $label . "\n";
    if (!$cond) { $fail++; }
}

$fp = "$THEME/front-page.php";
check('front-page.php exists', is_file($fp));
$t = is_file($fp) ? file_get_contents($fp) : '';
check('front-page calls get_header()/get_footer()', strpos($t, 'get_header()') !== false && strpos($t, 'get_footer()') !== false);
check('front-page uses es_sections (SectionManager)', strpos($t, 'es_sections') !== false);
check('front-page renders parts via get_template_part loop', strpos($t, 'get_template_part') !== false && strpos($t, 'foreach') !== false);
check('front-page keeps <main id="main" class="site-main">', strpos($t, '<main id="main"') !== false);

// all section parts exist and are non-trivial
$parts = glob("$THEME/template-parts/home/section-*.php");
check('15 home section parts exist', count($parts) === 15);
$total = 0;
foreach ($parts as $p) { $total += filesize($p); }
check('section parts are non-trivial (>200KB total markup)', $total > 200000);

// every part is referenced in front-page.php
$fpTxt = file_get_contents($fp);
$unref = 0;
foreach ($parts as $p) {
    $base = basename($p);
    if (strpos($fpTxt, $base) === false) { $unref++; echo "  unreferenced: $base\n"; }
}
check('all section parts referenced by front-page.php', $unref === 0);

// asset URLs inside parts are theme-relative
$hero = file_get_contents("$THEME/template-parts/home/section-01-hero.php");
check('hero part uses ERFANSANAT_THEME_URI for assets', strpos($hero, 'ERFANSANAT_THEME_URI') !== false || strpos($hero, 'data-home') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
