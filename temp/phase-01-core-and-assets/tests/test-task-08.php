<?php
/**
 * Test 1.8 — زیرساخت تست: wp-stubs (گارد چندباره، توابع کلیدی، ایزوله‌سازی) + مستند README
 * اجرا: /home/user/tools/php -f temp/phase-01-core-and-assets/tests/test-task-08.php
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
echo "== Test 1.8: زیرساخت wp-stubs ==\n";

$stubs_file = $root . '/temp/harness/wp-stubs.php';
$readme     = $root . '/temp/harness/README.md';

// فایل‌ها
check(is_file($stubs_file), 'temp/harness/wp-stubs.php موجود است');
check(is_file($readme), 'temp/harness/README.md موجود است');

// گارد include چندباره: require مجدد نباید fatal بدهد
$first_load = require $stubs_file;
check($first_load === 1, 'لود اول stubها موفق (return 1)');
$second_load = require $stubs_file;
check($second_load === null, 'require مجدد stubها بدون redeclare و بدون Fatal (early return → null)');

// هلپرهای قالب برای تست نمونه
require $root . '/inc/Helpers.php';

// ریست ایزوله
es_stubs_reset();
es_stubs_set_option('erfansanat_theme_options', ['x' => 'y']);
check(es_option('x') === 'y', 'es_option با stub get_option کار می‌کند');
es_stubs_reset();
check(es_option('x', 'DEF') === 'DEF', 'es_stubs_reset وضعیت آپشن‌ها را پاک می‌کند');

// رفتار فیلترها مانند وردپرس واقعی (اجرا با اولویت)
add_filter('es_test_filter', static fn($v) => $v . '-A', 10);
add_filter('es_test_filter', static fn($v) => $v . '-B', 5);
check(apply_filters('es_test_filter', 'S') === 'S-B-A', 'apply_filters کال‌بک‌ها را به ترتیب اولویت اجرا می‌کند');

// ثبت‌های بازرسی
add_action('es_test_action', '__return_null');
remove_action('es_test_action', 'wp_enqueue_global_styles');
check(isset($GLOBALS['__wp_hooks']['actions']['es_test_action']), 'add_action ثبت و قابل بازرسی است');
$last_removed = end($GLOBALS['__wp_hooks']['removed_actions']);
check($last_removed !== false && $last_removed['hook'] === 'es_test_action', 'remove_action ثبت و قابل بازرسی است');

// انکیو/دی‌انکیو
wp_enqueue_style('t', 'https://test.example/t.css', [], '1.0');
wp_dequeue_style('wp-block-library');
check(isset($GLOBALS['__wp_hooks']['enqueued_styles']['t']), 'wp_enqueue_style ثبت می‌شود');
check(in_array('wp-block-library', $GLOBALS['__wp_hooks']['dequeued_styles'], true), 'wp_dequeue_style ثبت می‌شود');

// زمینه‌ها
$GLOBALS['__wp_is_admin'] = true;
check(is_admin(), 'is_admin از فلگ سراسری می‌خواند');
$GLOBALS['__wp_is_admin'] = false;
$GLOBALS['__wp_current_user_can'] = true;
check(current_user_can('manage_options'), 'current_user_can از فلگ سراسری می‌خواند');
$GLOBALS['__wp_current_user_can'] = false;
check(!current_user_can('manage_options'), 'current_user_can حالت منفی');

// مستندسازی README
$readme_src = (string) file_get_contents($readme);
foreach (['es_stubs_reset', 'get_option', 'apply_filters', 'wp_enqueue_style', 'locate_template', 'ES_SCRIPT_PATH'] as $needle) {
    check(strpos($readme_src, $needle) !== false, "README نحوه {$needle} را مستند کرده");
}

// انتقال‌پذیری: توابع stub در کد قالب require نشده باشند (فقط تست‌ها)
$inc_dir = $root . '/inc';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($inc_dir));
$found = false;
foreach ($iterator as $file) {
    if ($file->isFile() && strpos((string) file_get_contents($file->getPathname()), 'wp-stubs.php') !== false) {
        $found = true;
        break;
    }
}
check(!$found, 'هیچ کلاس قالبی (inc/) stubها را require نمی‌کند');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
