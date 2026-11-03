<?php
/**
 * Test 2.6 — OptionsOutput: es_option تودرتو، تزریق کد سفارشی، CSS vars برند (فقط هنگام تغییر)، آرگومان اعلان
 * اجرا: /home/user/tools/php -f temp/phase-02-custom-admin-dashboard/tests/test-task-06.php
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
echo "== Test 2.6: OptionsOutput و تزریق تنظیمات ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false; // زمینه فرانت
require $root . '/functions.php';

use ErfanSanat\Theme\Admin\OptionsOutput;

// حالت آپشن خالی: هیچ کد سفارشی و هیچ CSS var اضافه
es_option_reset_cache();
ob_start();
OptionsOutput::print_head();
$head = (string) ob_get_clean();
check(!str_contains($head, 'es-brand-vars'), 'با پیش‌فرض میرور، CSS var تزریق نمی‌شود');
check(trim($head) === '', 'با آپشن خالی، head کد سفارشی ندارد');

ob_start();
OptionsOutput::print_footer();
check(trim((string) ob_get_clean()) === '', 'با آپشن خالی، footer کد ندارد');

// تغییر رنگ برند → تزریق فقط متغیر تغییرکرده
es_stubs_set_option('erfansanat_theme_options', ['brand' => ['brand_primary' => '#123456']]);
es_option_reset_cache();
ob_start();
OptionsOutput::print_head();
$head = (string) ob_get_clean();
check(str_contains($head, '--es-brand-primary:#123456'), 'CSS var رنگ تغییرکرده تزریق شد');
check(!str_contains($head, '--es-brand-dark'), 'رنگ تیره (پیش‌فرض) تزریق نشد');
check(str_contains($head, '<style id="es-brand-vars">'), 'تگ استایل با آیدی es-brand-vars');

// کد سفارشی head/footer
es_stubs_set_option('erfansanat_theme_options', [
    'custom' => ['head_js' => '<meta name="verify" content="x">', 'footer_js' => '<script>console.log("es")</script>'],
]);
es_option_reset_cache();
ob_start();
OptionsOutput::print_head();
$head = (string) ob_get_clean();
check(str_contains($head, '<meta name="verify" content="x">'), 'کد سفارشی head چاپ شد');

ob_start();
OptionsOutput::print_footer();
$footer = (string) ob_get_clean();
check(str_contains($footer, 'console.log("es")'), 'کد سفارشی footer چاپ شد');

// اعلان — پیش‌فرض خاموش / روشن
$ann = OptionsOutput::announcement_args();
check($ann['enabled'] === false, 'اعلان پیش‌فرض خاموش');
es_stubs_set_option('erfansanat_theme_options', ['header' => ['ann_enabled' => 1, 'ann_text' => 'ارسال رایگان بالای ۵ میلیون', 'ann_url' => '/shop/', 'ann_bg' => '#065f46']]);
es_option_reset_cache();
$ann = OptionsOutput::announcement_args();
check($ann['enabled'] === true && str_contains($ann['text'], 'ارسال رایگان'), 'اعلان فعال با متن از آپشن');
check($ann['bg'] === '#065f46', 'رنگ اعلان');

// تماس — override ایمیل از آپشن
es_stubs_set_option('erfansanat_theme_options', ['footer' => ['contact_email' => 'sales@erfansanat.com']]);
es_option_reset_cache();
$args = OptionsOutput::contact_args();
check($args['email'] === 'sales@erfansanat.com', 'ایمیل override شد');
check($args['phone'] === '031-91091011', 'بقیه فیلدها پیش‌فرض میرور');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
