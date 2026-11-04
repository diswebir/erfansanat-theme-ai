<?php
/**
 * Test 4.1 — refهای هدر/فوتر/سایدبار byte-exact میرور + پوشش data-hookها در HEADER_MAP
 * اجرا: /home/user/tools/php -f temp/phase-04-header-footer-navigation/tests/test-task-01.php
 */
declare(strict_types=1);

$failures = [];
$passes   = 0;
function check(bool $cond, string $label): void {
    global $failures, $passes;
    if ($cond) { $passes++; echo "  PASS  $label\n"; }
    else       { $failures[] = $label; echo "  FAIL  $label\n"; }
}

$root  = dirname(ES_SCRIPT_PATH, 4);
$mirror = $root . '/uploads/static-mirror/index.html';
$refs  = $root . '/temp/phase-04-header-footer-navigation/refs';
echo "== Test 4.1: مراجع هدر/فوتر ==\n";

check(file_exists($mirror), 'میرور استخراج‌شده موجود است');
$html = (string) file_get_contents($mirror);

// ── مرزهای بلوک‌ها به‌صورت داینامیک پیدا می‌شوند (نه offset ثابت) ──
$h0 = strpos($html, '<header data-navbar-header');
$h1 = strpos($html, '</header>');
$f0 = strpos($html, '<footer');
$f1 = strpos($html, '</footer>');
$m0 = $h1 + strlen('</header>');
$m1 = strpos($html, '</aside>');
check($h0 !== false && $f0 !== false && $m1 !== false, 'بلوک‌ها در میرور یافت شدند');

$header_ref = (string) file_get_contents($refs . '/header-ref.html');
$footer_ref = (string) file_get_contents($refs . '/footer-ref.html');
$sidebar_ref = (string) file_get_contents($refs . '/mobile-sidebar-ref.html');

check($header_ref === substr($html, $h0, $h1 + 9 - $h0), 'header-ref.html byte-exact میرور');
check($footer_ref === substr($html, $f0, $f1 + 9 - $f0), 'footer-ref.html byte-exact میرور');
check($sidebar_ref === substr($html, $m0, $m1 + 8 - $m0), 'mobile-sidebar-ref.html byte-exact میرور');

// ── HEADER_MAP همه data-hookهای refs را پوشش دهد ──
$map = (string) file_get_contents($refs . '/HEADER_MAP.md');
preg_match_all('/data-([a-z-]+)/', $header_ref . $sidebar_ref . $footer_ref, $m);
$hooks = array_values(array_unique($m[1]));
$missing = [];
foreach ($hooks as $hook) {
    if (!str_contains($map, $hook)) {
        $missing[] = $hook;
    }
}
check($missing === [], 'HEADER_MAP همه ' . count($hooks) . ' data-hook را پوشش می‌دهد' . ($missing === [] ? '' : ' — کمبود: ' . implode(',', $missing)));

// ── نقاط داینامیک کلیدی مستند شده‌اند ──
foreach (['es_logo_url', 'es_page_url', 'header-cart-badge', 'quick_links', 'es_contact_args', 'main-mega-menu', 'mobile-menu', 'tolidat-sherkat'] as $needle) {
    check(str_contains($map, $needle), "نقشه شامل {$needle}");
}

// ── ساختار مگا: ۴ trigger + ۴ panel + تب‌های محصولات ──
check(substr_count($header_ref, 'data-desktop-mega-trigger=') === 4, '۴ مگا trigger (products/projects/services/corporate)');
check(substr_count($header_ref, 'data-desktop-mega-panel=') === 4, '۴ مگا panel');
check(substr_count($header_ref, 'data-mega-prod-tab=') === 4, '۴ تب محصولات');
check(substr_count($sidebar_ref, 'data-mobile-acc-btn=') === 4, '۴ آکاردئون موبایل');
check(substr_count($footer_ref, 'data-scroll-top') === 1, 'دکمه scroll-top در فوتر');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
