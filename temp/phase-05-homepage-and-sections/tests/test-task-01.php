<?php
/**
 * Test 5.1 — ثبت ۱۵ سکشن صفحه اصلی + رندر پویا از SectionManager (ترتیب/toggle/reorder)
 * اجرا: /home/user/tools/php -f temp/phase-05-homepage-and-sections/tests/test-task-01.php
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
echo "== Test 5.1: HomeSections / front-page ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = false;
require $root . '/functions.php';

use ErfanSanat\Theme\Sections\HomeSections;

// ═══ ۱) ثبت ۱۵ سکشن یکتا با ترتیب میرور (ساختار page_key) ═══
do_action('init');
$registry = apply_filters('es_sections_registry', []);
$items = (array) ($registry['page'] ?? []);
check(count($items) === 15, 'دقیقاً ۱۵ سکشن ثبت (بدون اضافه)');
$ids = array_map(static fn($i): string => (string) ($i['id'] ?? ''), $items);
check($ids === HomeSections::ORDER, 'ترتیب = ترتیب سکشن‌های index.html');
foreach ($items as $item) {
    check(is_array($item['schema'] ?? null) && $item['schema'] !== [], "اسکیم {$item['id']} پر است");
}
check(\ErfanSanat\Theme\Admin\SectionManager::schema_of('hero-home1') !== [], 'اسکیم هیرو در SectionManager');

// ═══ ۲) رندر پویا: markers به ترتیب ═══
// قالب‌های واقعی با فایل‌های marker جایگزین می‌شوند (ورکسریر sections/{id}.php واقعی نمی‌خواهیم)
$GLOBALS['__es_locate_override'] = [];
foreach (HomeSections::ORDER as $id) {
    $GLOBALS['__es_locate_override']['template-parts/sections/' . $id . '.php'] = $root . '/temp/phase-05-homepage-and-sections/tests/fixtures/marker.php';
}
@mkdir($root . '/temp/phase-05-homepage-and-sections/tests/fixtures', 0777, true);
file_put_contents($root . '/temp/phase-05-homepage-and-sections/tests/fixtures/marker.php', '<?php echo "MARK[" . $section_id . "]";');

// front page id
$GLOBALS['__wp_options']['page_on_front'] = 8146;

// حالت خالی: همه رندر می‌شوند
$GLOBALS['__wp_post_meta'] = [];
$GLOBALS['__es_stub_post_types'][8146] = 'page';
ob_start();
es_render_sections(8146);
$out = (string) ob_get_clean();
$pos_ok = true; $prev = -1; $count = 0;
foreach (HomeSections::ORDER as $id) {
    $p = strpos($out, 'MARK[' . $id . ']');
    if ($p === false || $p < $prev) { $pos_ok = false; }
    $prev = (int) $p; $count++;
}
check($count === 15 && $pos_ok, 'حالت خالی → همه ۱۵ سکشن به ترتیب میرور');

// toggle: hero خاموش — متای کامل (متاباکس همه ردیف‌ها را ذخیره می‌کند)
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = array_map(
    static fn(string $id, int $i): array => ['id' => $id, 'enabled' => $id === 'hero-home1' ? 0 : 1, 'order' => $i, 'fields' => []],
    HomeSections::ORDER,
    array_keys(HomeSections::ORDER)
);
ob_start();
es_render_sections(8146);
$out2 = (string) ob_get_clean();
check(!str_contains($out2, 'MARK[hero-home1]'), 'toggle: سکشن غیرفعال رندر نمی‌شود');
check(str_contains($out2, 'MARK[projects]'), 'بقیه سکشن‌ها باقی');

// reorder: projects اول — متای کامل
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = array_map(
    static fn(string $id, int $i): array => ['id' => $id, 'enabled' => 1, 'order' => $id === 'projects' ? 0 : ($id === 'hero-home1' ? 1 : $i + 1), 'fields' => []],
    HomeSections::ORDER,
    array_keys(HomeSections::ORDER)
);
ob_start();
es_render_sections(8146);
$out3 = (string) ob_get_clean();
check(strpos($out3, 'MARK[projects]') < strpos($out3, 'MARK[hero-home1]'), 'reorder از ادمین اعمال');

// ═══ ۳) front-page.php: header + render + footer ═══
$GLOBALS['__wp_options']['page_on_front'] = 8146;
$GLOBALS['__wp_post_meta'][8146]['_es_sections'] = [];
ob_start();
include $root . '/front-page.php';
$fp = (string) ob_get_clean();
check(str_contains($fp, 'MARK[hero-home1]'), 'front-page رندر سکشن‌ها');
check(str_contains($fp, 'wp-head-marker') && str_contains($fp, 'wp-footer-marker'), 'get_header/get_footer');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
