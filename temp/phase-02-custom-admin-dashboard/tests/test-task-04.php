<?php
/**
 * Test 2.4 — تب «فوتر و تماس»: پیش‌فرض‌های بایت‌به‌بایت میرور + سوشال + repeater لینک‌ها
 * اجرا: /home/user/tools/php -f temp/phase-02-custom-admin-dashboard/tests/test-task-04.php
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
echo "== Test 2.4: تب فوتر و تماس ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\ThemeOptions;

$by_key = [];
foreach ((ThemeOptions::tabs()['footer']['fields'] ?? []) as $f) {
    $by_key[$f['key']] = $f;
}

// پیش‌فرض‌های دقیق میرور
check(($by_key['contact_phone']['default'] ?? '') === '031-91091011', 'تلفن = 031-91091011 (JSON-LD میرور)');
check(($by_key['contact_phone_display']['default'] ?? '') === '۰۳۱ - ۹۱۰۹۱۰۱۱ (۱۰ خط ویژه)', 'نمایش فارسی تلفن مطابق فوتر میرور');
check(($by_key['contact_sales_display']['default'] ?? '') === 'مدیر فروش (آقای محمدعلی قربانی): ۰۹۱۳۷۹۷۶۹۱۵', 'تلفن مدیر فروش مطابق فوتر میرور');
check(($by_key['contact_email']['default'] ?? '') === 'info@erfansanat.com', 'ایمیل میرور');
check(($by_key['contact_address']['default'] ?? '') === 'اصفهان، شهرستان خمینی‌شهر، شهرک صنعتی برق و الکترونیک، بلوار الکترونیک، فرعی دوم، پلاک ۱۱۹', 'آدرس کامل میرور');
check(($by_key['contact_postal_code']['default'] ?? '') === '۸۴۱۸۱۴۸۶۷۸', 'کد پستی میرور');
check(($by_key['working_hours']['default'] ?? '') === 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر', 'ساعات کاری میرور');
check(str_contains((string) ($by_key['footer_about']['default'] ?? ''), '۲۴ ماه گارانتی تعویض و ۵ سال خدمات پس از فروش'), 'متن معرفی فوتر میرور');
check(($by_key['footer_copyright']['default'] ?? '') === '© تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.', 'کپی‌رایت میرور');

// سوشال‌ها
check(($by_key['social_instagram']['default'] ?? '') === 'https://instagram.com/erfan_sanat_esfahan', 'اینستاگرام میرور');
check(($by_key['social_telegram']['default'] ?? '') === 'https://t.me/erfansanat', 'تلگرام میرور');
check(($by_key['social_linkedin']['default'] ?? '') === 'https://linkedin.com/company/erfansanat', 'لینکدین میرور');
check(($by_key['social_whatsapp']['default'] ?? '') === '' && ($by_key['social_aparat']['default'] ?? '') === '', 'واتساپ/آپارات خالی (در میرور نیستند)');

// repeater لینک‌های دسترسی سریع — ۸ لینک فوتر میرور
$quick = $by_key['quick_links']['default'] ?? [];
check(count($quick) === 8, 'هشت لینک دسترسی سریع مطابق فوتر میرور (یافت‌شده: ' . count($quick) . ')');
check(($quick[0]['label'] ?? '') === 'فروشگاه محصولات' && ($quick[0]['url'] ?? '') === '/shop/', 'لینک اول: فروشگاه');
check(($quick[7]['label'] ?? '') === 'اخبار و مقالات' && ($quick[7]['url'] ?? '') === '/blog/', 'لینک آخر: وبلاگ');
check(isset($by_key['quick_links']['fields']) && count($by_key['quick_links']['fields']) === 2, 'زیرفیلدهای repeater: label + url');

// خروجی contact_args
es_option_reset_cache();
$args = \ErfanSanat\Theme\Admin\OptionsOutput::contact_args();
check($args['phone'] === '031-91091011' && $args['email'] === 'info@erfansanat.com', 'contact_args: پیش‌فرض‌های میرور');
check(count($args['socials']) === 3, 'contact_args: سه سوشال فعال');
check(isset($args['socials']['instagram']['url']) && !isset($args['socials']['aparat']), 'contact_args: سوشال خالی حذف شد');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
