<?php
/**
 * Test 5.8 — aftersale + customers: بازسازی بایت‌دقیق + رپیتر + حالت خالی
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-08.php
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
echo "== Test 5.8: aftersale + customers ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

const ES_URI = 'https://test.example/wp-content/themes/erfansanat/';

$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_post_types'][8146] = 'page';

// ═══ ۱) aftersale: بازسازی بایت‌دقیق ═══
ob_start();
es_render_sections(8146, ['only' => 'aftersale']);
$aft = (string) ob_get_clean();
$ref = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/aftersale.html');
check($aft === $ref . '>', 'aftersale: بازسازی بایت‌دقیق (نقشه SVG محفوظ)');
if ($aft !== $ref . '>') {
    $exp = $ref . '>';
    $n = min(strlen($aft), strlen($exp));
    for ($i = 0; $i < $n; $i++) { if ($aft[$i] !== $exp[$i]) break; }
    echo "    diff@$i (got=" . strlen($aft) . " exp=" . strlen($exp) . "):\n    EXP: " . substr($exp, max(0, $i-60), 130) . "\n    GOT: " . substr($aft, max(0, $i-60), 130) . "\n";
}

// ═══ ۲) aftersale: فیلدهای سفارشی ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'aftersale', 'enabled' => 1, 'order' => 0, 'fields' => [
        'badge' => 'برچسب آزمایشی', 'title' => 'عنوان آزمایشی', 'desc' => 'متن آزمایشی',
        's2_title' => '۳۶ ماه گارانتی', 'phone' => '۰۲۱-۱۲۳۴۵۶۷۸', 'phone_url' => 'tel:02112345678',
    ],
]];
ob_start();
es_render_sections(8146, ['only' => 'aftersale']);
$aft2 = (string) ob_get_clean();
check(str_contains($aft2, '<span>برچسب آزمایشی</span>') && str_contains($aft2, '>عنوان آزمایشی</h2>'), 'فیلدهای سفارشی aftersale');
check(str_contains($aft2, '>۳۶ ماه گارانتی</div>'), 'کارت ۲ سفارشی');
check(str_contains($aft2, 'href="tel:02112345678"') && str_contains($aft2, '<span dir="ltr">۰۲۱-۱۲۳۴۵۶۷۸</span>'), 'تلفن سفارشی');
check(substr_count($aft2, 'viewBox="0 0 580') >= 1 || str_contains($aft2, '<svg viewBox'), 'نقشه SVG حفظ شده');

// ═══ ۳) customers: بازسازی بایت‌دقیق ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [];
ob_start();
es_render_sections(8146, ['only' => 'customers']);
$cus = (string) ob_get_clean();
$ref2 = file_get_contents($root . '/temp/phase-05-homepage-and-sections/refs/customers.html');
check($cus === $ref2 . '>', 'customers: بازسازی بایت‌دقیق (۱۲ کارت)');
if ($cus !== $ref2 . '>') {
    $exp = $ref2 . '>';
    $n = min(strlen($cus), strlen($exp));
    for ($i = 0; $i < $n; $i++) { if ($cus[$i] !== $exp[$i]) break; }
    echo "    diff@$i (got=" . strlen($cus) . " exp=" . strlen($exp) . "):\n    EXP: " . substr($exp, max(0, $i-60), 130) . "\n    GOT: " . substr($cus, max(0, $i-60), 130) . "\n";
}

// ═══ ۴) customers: رپیتر داینامیک ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [[
    'id' => 'customers', 'enabled' => 1, 'order' => 0, 'fields' => [
        'rows' => [
            ['title' => 'سازمان الف', 'sub' => 'توضیح الف', 'icon' => 'landmark'],
            ['title' => 'سازمان ب', 'sub' => 'توضیح ب', 'icon' => 'sparkles'],
            ['title' => 'سازمان ج', 'sub' => 'توضیح ج', 'icon' => 'landmark'],
        ],
    ],
]];
ob_start();
es_render_sections(8146, ['only' => 'customers']);
$cus2 = (string) ob_get_clean();
check(substr_count($cus2, 'card-light') === 3, '۳ کارت داینامیک');
check(str_contains($cus2, '>۰۱</span>') && str_contains($cus2, '>۰۳</span>'), 'شماره‌گذاری فارسی ۰۱..۰۳');
check(str_contains($cus2, '>سازمان الف</div>') && str_contains($cus2, '>توضیح ب</div>'), 'عنوان/توضیح ردیف‌ها');
check(str_contains($cus2, 'lucide-sparkles h-4.5 w-4.5'), 'آیکون سفارشی ردیف');

// ═══ ۵) customers خالی → مخفی ═══
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [['id' => 'customers', 'enabled' => 1, 'order' => 0, 'fields' => ['rows' => []]]];
ob_start();
es_render_sections(8146, ['only' => 'customers']);
check(trim((string) ob_get_clean()) === '', 'customers خالی → خروجی خالی');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
