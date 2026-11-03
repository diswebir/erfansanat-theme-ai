<?php
/**
 * Test 1.1 — اسکلت پایه قالب و بوت‌استرپ (style.css / functions.php / index.php)
 * اجرا: /home/user/tools/php -f temp/phase-01-core-and-assets/tests/test-task-01.php
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
echo "== Test 1.1: اسکلت پایه قالب و بوت‌استرپ ==\n";

require $root . '/temp/harness/wp-stubs.php';
es_stubs_reset();
$GLOBALS['__es_theme_dir'] = $root;

check(is_file($root . '/style.css'), 'style.css موجود است');
check(is_file($root . '/functions.php'), 'functions.php موجود است');
check(is_file($root . '/index.php'), 'index.php موجود است');

$style = (string) file_get_contents($root . '/style.css');
check(strpos($style, 'Theme Name:') !== false, 'هدر style.css شامل Theme Name است');
check(strpos($style, 'Text Domain: erfansanat') !== false, 'Text Domain: erfansanat ثبت شده');
check(strpos($style, 'Requires PHP: 8.4') !== false, 'Requires PHP: 8.4 ثبت شده');
check(preg_match('/^Version:\s*[0-9]+\./m', $style) === 1, 'نسخه قالب ثبت شده');

$functions = (string) file_get_contents($root . '/functions.php');
check(strpos($functions, "defined('ABSPATH')") !== false, 'گارد ABSPATH در functions.php');
check(strpos($functions, 'ERFANSANAT_VERSION') !== false, 'ثابت ERFANSANAT_VERSION تعریف شده');
check(strpos($functions, 'ERFANSANAT_DIR') !== false, 'ثابت ERFANSANAT_DIR تعریف شده');
check(strpos($functions, 'ERFANSANAT_URI') !== false, 'ثابت ERFANSANAT_URI تعریف شده');

$index_src = (string) file_get_contents($root . '/index.php');
check(strpos($index_src, "defined('ABSPATH')") !== false, 'گارد ABSPATH در index.php');
check(strpos($index_src, 'get_header()') !== false, 'index.php شامل get_header() است');
check(strpos($index_src, 'get_footer()') !== false, 'index.php شامل get_footer() است');

// اجرای واقعی functions.php — بوت بدون Fatal
require $root . '/functions.php';

check(defined('ERFANSANAT_VERSION') && ERFANSANAT_VERSION === '1.0.0', 'ثابت نسخه = 1.0.0');
check(defined('ERFANSANAT_DIR') && ERFANSANAT_DIR === trailingslashit($root), 'ERFANSANAT_DIR به ریشه قالب اشاره می‌کند');
check(defined('ERFANSANAT_URI') && ERFANSANAT_URI !== '', 'ERFANSANAT_URI غیرتهی است');
check(defined('ABSPATH'), 'ABSPATH پیش از بوت تعریف شده است');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
