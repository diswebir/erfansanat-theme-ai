<?php
/**
 * Test 1.2 — Autoloader PSR-4 و هلپرهای پایه (es_option / es_get_template)
 * اجرا: /home/user/tools/php -f temp/phase-01-core-and-assets/tests/test-task-02.php
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
echo "== Test 1.2: Autoloader و هلپرهای پایه ==\n";

require $root . '/temp/harness/wp-stubs.php';
es_stubs_reset();
$GLOBALS['__es_theme_dir'] = $root;

require $root . '/functions.php';

// اوتولودر
check(class_exists(\ErfanSanat\Theme\Autoloader::class), 'کلاس Autoloader بارگذاری شد');
check(\ErfanSanat\Theme\Autoloader::is_registered(), 'اوتولودر در spl_autoload ثبت شده است');

// resolve واقعی کلاس Theme بدون require دستی (از طریق اوتولودر)
check(class_exists(\ErfanSanat\Theme\Theme::class), 'کلاس Theme بدون require دستی resolve شد');

// نگاشت kebab-case
check(
    \ErfanSanat\Theme\Autoloader::to_kebab('Frontend/AssetManager') === 'frontend/asset-manager',
    'to_kebab: Frontend/AssetManager → frontend/asset-manager'
);
check(
    \ErfanSanat\Theme\Autoloader::to_kebab('Frontend/DeBloat') === 'frontend/de-bloat',
    'to_kebab: Frontend/DeBloat → frontend/de-bloat (الگوی دوم پشتیبانی می‌شود)'
);

// کلاس ناموجود نباید خطا بدهد (fallback نبود فایل)
check(!class_exists(\ErfanSanat\Theme\NonExistent_Fixture_Class::class), 'کلاس ناموجود بدون Fatal رد می‌شود');

// es_option — کش روی get_option + مسیر تودرتو + پیش‌فرض
es_stubs_set_option('erfansanat_theme_options', [
    'site_name'     => 'عرفان صنعت اصفهان',
    'header'        => ['logo' => 'ese-logo.svg'],
]);
check(es_option('site_name') === 'عرفان صنعت اصفهان', 'es_option کلید ساده را برمی‌گرداند');
check(es_option('header.logo') === 'ese-logo.svg', 'es_option مسیر تودرتو با نقطه را پشتیبانی می‌کند');
check(es_option('missing.key', 'پیش‌فرض') === 'پیش‌فرض', 'es_option مقدار پیش‌فرض را در نبود کلید برمی‌گرداند');
check(es_option('missing.key') === null, 'es_option پیش‌فرض null برای کلید ناموجود');

// فیلتر es_default_options (ادغام پیش‌فرض‌های آینده داشبورد) — با خالی‌کردن کش استاتیک
es_option_reset_cache();
$GLOBALS['__wp_hooks']['filters']['es_default_options'] = [
    ['cb' => static fn(array $o): array => array_merge($o, ['filtered_key' => 'مقدار فیلترشده']), 'priority' => 10],
];
check(es_option('filtered_key') === 'مقدار فیلترشده', 'فیلتر es_default_options پس از reset کش اعمال می‌شود');
check(es_option('site_name') === 'عرفان صنعت اصفهان', 'پس از reset کش، مقادیر قبلی همچنان خوانده می‌شوند');

// es_template_path + es_get_template با override و extract امن
$fixture_dir = sys_get_temp_dir() . '/es-tpl-fixture-' . getmypid();
@mkdir($fixture_dir, 0777, true);
$fixture_file = $fixture_dir . '/card-fixture.php';
file_put_contents($fixture_file, "<?php echo 'TPL:' . esc_html(\$es_arg);");
$GLOBALS['__es_locate_override'] = ['template-parts/cards/card-fixture.php' => $fixture_file];

check(es_template_path('template-parts/cards/card-fixture.php') === $fixture_file, 'es_template_path مسیر override را resolve می‌کند');
check(es_template_path('template-parts/missing.php') === '', 'es_template_path برای قالب ناموجود رشته خالی می‌دهد');

ob_start();
es_get_template('template-parts/cards/card-fixture.php', ['es_arg' => 'سلام ۱']);
$rendered = (string) ob_get_clean();
check($rendered === 'TPL:سلام ۱', 'es_get_template قالب را با args رندر می‌کند');

ob_start();
es_get_template('template-parts/missing.php');
$rendered_empty = (string) ob_get_clean();
check($rendered_empty === '', 'es_get_template برای قالب ناموجود بدون خطا خروجی خالی می‌دهد');

@unlink($fixture_file);
@rmdir($fixture_dir);

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
