<?php
/**
 * Test 5.10 — تماس + نقشه: بازسازی بایت‌دقیق + گزینه‌های مرکزی + iframe sanitize + حالت خالی
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-10.php
 */
declare(strict_types=1);

error_reporting(E_ALL);
$failures = [];
$passes   = 0;
function check(bool $cond, string $label): void {
    global $failures, $passes;
    if ($cond) { $passes++; echo "  PASS  $label\n"; }
    else       { $failures[] = $label; echo "  FAIL  $label\n"; }
}

$root = dirname(ES_SCRIPT_PATH, 4);
echo "== Test 5.10: contact + map ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_post_types'][8146] = 'page';

// ═══ ۱) تماس: بازسازی بایت‌دقیق ═══
ob_start();
es_render_sections(8146, ['only' => 'contact']);
$con = (string) ob_get_clean();
$ref = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/contact.html');
$expected = strtr($ref, [
    'href="tel:031-91091011"' => 'href="tel:031-91091011"',
    'href="contact.html"' => 'href="https://test.example/contact/"',
]) . '>';
check($con === $expected, 'contact: بازسازی بایت‌دقیق');
if ($con !== $expected) {
    $n = min(strlen($con), strlen($expected));
    for ($i = 0; $i < $n; $i++) { if ($con[$i] !== $expected[$i]) break; }
    echo "    diff@$i (got=" . strlen($con) . " exp=" . strlen($expected) . "):\n    EXP: " . substr($expected, max(0, $i-70), 150) . "\n    GOT: " . substr($con, max(0, $i-70), 150) . "\n";
}

// ═══ ۲) تماس: فیلد خالی → گزینه مرکزی ═══
es_option_reset_cache();
$GLOBALS['__wp_options']['erfansanat_theme_options'] = [
    'footer' => [
        'contact_phone' => '03112345678',
        'contact_email' => 'office@example.com',
        'working_hours' => 'شنبه تا چهارشنبه ۸ تا ۱۴',
    ],
];
es_option_reset_cache();
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'contact', 'enabled' => 1, 'order' => 0, 'fields' => [
        'phone' => '', 'email' => '', 'hours' => '', 'phone_url' => '',
        'desc' => 'متن آزمایشی تماس', 'kicker' => 'بالانویس آزمایشی',
    ],
]];
ob_start();
es_render_sections(8146, ['only' => 'contact']);
$con2 = (string) ob_get_clean();
check(str_contains($con2, '>۰۳۱۱۲۳۴۵۶۷۸</a>') && str_contains($con2, 'href="tel:03112345678"'), 'تلفن از گزینه مرکزی + ارقام فارسی');
check(str_contains($con2, '>office@example.com</a>') && str_contains($con2, 'href="mailto:office@example.com"'), 'ایمیل از گزینه مرکزی');
check(str_contains($con2, '>شنبه تا چهارشنبه ۸ تا ۱۴</div>'), 'ساعات از گزینه مرکزی');

// ═══ ۳) تماس: بدون هیچ مقدار → مخفی ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [['id' => 'contact', 'enabled' => 1, 'order' => 0, 'fields' => ['desc' => '', 'phone' => '', 'email' => '', 'hours' => '']]];
$GLOBALS['__wp_options']['erfansanat_theme_options'] = ['footer' => ['contact_phone' => '', 'contact_email' => '']];
es_option_reset_cache();
ob_start();
es_render_sections(8146, ['only' => 'contact']);
check(trim((string) ob_get_clean()) === '', 'contact بدون مقادیر → خروجی خالی');

// ═══ ۴) نقشه: بازسازی بایت‌دقیق (SVG fallback) ═══
$GLOBALS['__wp_options']['erfansanat_theme_options'] = ['footer' => ['contact_phone' => '031-91091011', 'contact_email' => 'info@erfansanat.com', 'contact_address' => '', 'contact_postal_code' => '۸۴۱۸۱۴۸۶۷۸']];
es_option_reset_cache();
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [];
ob_start();
es_render_sections(8146, ['only' => 'map']);
$map = (string) ob_get_clean();
$ref2 = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/map.html');
$expected2 = strtr($ref2, ['href="contact.html"' => 'href="https://test.example/contact/"']) . '>';
check($map === $expected2, 'map: بازسازی بایت‌دقیق (SVG fallback)');
if ($map !== $expected2) {
    $n = min(strlen($map), strlen($expected2));
    for ($i = 0; $i < $n; $i++) { if ($map[$i] !== $expected2[$i]) break; }
    echo "    diff@$i (got=" . strlen($map) . " exp=" . strlen($expected2) . "):\n    EXP: " . substr($expected2, max(0, $i-70), 150) . "\n    GOT: " . substr($map, max(0, $i-70), 150) . "\n";
}

// ═══ ۵) نقشه: iframe ادمین + نشانی از گزینه ═══
$GLOBALS['__wp_options']['erfansanat_theme_options']['footer']['contact_address'] = 'اصفهان، جی، کارخانه آزمایشی';
es_option_reset_cache();
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'map', 'enabled' => 1, 'order' => 0, 'fields' => [
        'address' => '', 'postal' => '',
        'embed' => '<iframe src="https://maps.example.com/embed?q=esfahan" width="600" height="300" loading="lazy"></iframe><script>alert(1)</script>',
    ],
]];
ob_start();
es_render_sections(8146, ['only' => 'map']);
$map2 = (string) ob_get_clean();
check(str_contains($map2, '<iframe src="https://maps.example.com/embed?q=esfahan"'), 'iframe ادمین رندر شد');
check(!str_contains($map2, '<script>') && !str_contains($map2, 'alert(1)'), 'اسکریپت حذف شد (wp_kses)');
check(!str_contains($map2, 'viewBox="0 0 1000 400"'), 'SVG fallback با iframe جایگزین شد');
check(str_contains($map2, '>اصفهان، جی، کارخانه آزمایشی</p>'), 'نشانی از گزینه مرکزی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
