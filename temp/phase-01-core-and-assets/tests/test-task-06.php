<?php
/**
 * Test 1.6 — انتقال بی‌نقص دارایی‌های میرور به assets/ قالب
 * اجرا: /home/user/tools/php -f temp/phase-01-core-and-assets/tests/test-task-06.php
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
echo "== Test 1.6: انتقال دارایی‌های میرور ==\n";

$A = $root . '/assets';

// ---- فایل‌های کلیدی با اندازه واقعی ----
check(is_file($A . '/css/styles.css') && (filesize($A . '/css/styles.css') ?? 0) > 150000, 'styles.css موجود (>۱۵۰KB)');
check(is_file($A . '/js/main.js') && (filesize($A . '/js/main.js') ?? 0) > 90000, 'main.js موجود (>۹۰KB)');

// ---- فونت‌ها با نام دقیق ----
foreach (['vazirmatn-arabic.woff2', 'vazirmatn-latin.woff2', 'vazirmatn-latin-ext.woff2'] as $font) {
    check(is_file($A . '/fonts/' . $font) && (filesize($A . '/fonts/' . $font) ?? 0) > 10000, "فونت {$font} موجود");
}

// ---- ساختار پوشه‌ها مطابق میرور ----
check(is_dir($A . '/images'), 'پوشه images موجود');
check(is_dir($A . '/images/pexels'), 'پوشه images/pexels موجود');
check(is_dir($A . '/media'), 'پوشه media موجود');
check(is_file($A . '/media/hero-video.mp4') && (filesize($A . '/media/hero-video.mp4') ?? 0) > 1000000, 'hero-video.mp4 در assets/media منتقل شد (>۱MB)');

// ---- تصاویر ----
$jpgs = glob($A . '/images/*.jpg') ?: [];
check(count($jpgs) >= 9, 'هر ۹ تصویر JPG محلی منتقل شده (یافت‌شده: ' . count($jpgs) . ')');
check(count((glob($A . '/images/*.svg') ?: [])) >= 1, 'SVG لوگو در کنار JPGها منتقل شده');
check(is_file($A . '/images/ese-logo.svg'), 'لوگوی ese-logo.svg موجود');
$pexels = glob($A . '/images/pexels/*') ?: [];
check(count($pexels) >= 10, 'حداقل ۱۰ تصویر pexels منتقل شده (یافت‌شده: ' . count($pexels) . ')');

// ---- تطبیق بایت‌به‌بایت با میرور (انتقال بی‌نقص) ----
$mirror = $root . '/uploads/static-mirror/assets';
check(md5_file($A . '/css/styles.css') === md5_file($mirror . '/css/styles.css'), 'styles.css عیناً با میرور یکسان است');
check(md5_file($A . '/js/main.js') === md5_file($mirror . '/js/main.js'), 'main.js عیناً با میرور یکسان است');
check(md5_file($A . '/fonts/vazirmatn-arabic.woff2') === md5_file($mirror . '/fonts/vazirmatn-arabic.woff2'), 'فونت عربی عیناً یکسان');

// ---- رزولوشن مسیرهای نسبی url(../fonts/…) داخل CSS ----
$css = (string) file_get_contents($A . '/css/styles.css');
preg_match_all('/url\((["\']?)\.\.\/fonts\/([^)"\'#?]+)/', $css, $matches);
$font_refs = array_values(array_unique($matches[2] ?? []));
check(count($font_refs) > 0, 'مسیرهای نسبی فونت داخل CSS یافت شد (' . count($font_refs) . ' مرجع)');
foreach ($font_refs as $ref) {
    check(is_file($A . '/fonts/' . $ref), "مسیر url(../fonts/{$ref}) به فایل موجود می‌رسد");
}

// ویدیو نیز عیناً یکسان با میرور
check(md5_file($A . '/media/hero-video.mp4') === md5_file($root . '/uploads/static-mirror/images/hero-video.mp4'), 'hero-video.mp4 عیناً یکسان');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
