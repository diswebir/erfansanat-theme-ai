<?php
/**
 * Test 1.4 — DeBloat بخش ۱: حذف کامل دارایی‌های گوتنبرگ از فرانت
 * اجرا: /home/user/tools/php -f temp/phase-01-core-and-assets/tests/test-task-04.php
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
echo "== Test 1.4: DeBloat — حذف دارایی‌های گوتنبرگ ==\n";

require $root . '/temp/harness/wp-stubs.php';
es_stubs_reset();
$GLOBALS['__es_theme_dir'] = $root;

// ---- حالت فرانت (is_admin = false) ----
$GLOBALS['__wp_is_admin'] = false;
require $root . '/functions.php';

check(class_exists(\ErfanSanat\Theme\Frontend\DeBloat::class), 'کلاس DeBloat از اوتولودر resolve شد');

$removed = $GLOBALS['__wp_hooks']['removed_actions'];
$has = static function (string $hook, string $cb) use ($removed): bool {
    foreach ($removed as $entry) {
        if ($entry['hook'] === $hook && $entry['cb'] === $cb) {
            return true;
        }
    }
    return false;
};

check($has('wp_enqueue_scripts', 'wp_enqueue_global_styles'), 'حذف wp_enqueue_global_styles از wp_enqueue_scripts');
check($has('wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles'), 'حذف wp_enqueue_classic_theme_styles از wp_enqueue_scripts');
check($has('wp_body_open', 'wp_global_styles_render_svg_filters'), 'حذف SVG فیلترهای global-styles از wp_body_open');

// کال‌بک صف‌خارج‌کننده ثبت شده باشد
$dequeuer_registered = false;
foreach ($GLOBALS['__wp_hooks']['actions']['wp_enqueue_scripts'] ?? [] as $entry) {
    if (is_array($entry['cb']) && $entry['cb'] === [\ErfanSanat\Theme\Frontend\DeBloat::class, 'dequeue_block_assets']) {
        $dequeuer_registered = true;
    }
}
check($dequeuer_registered, 'کال‌بک dequeue_block_assets روی wp_enqueue_scripts (اولویت ۱۰۰) ثبت شده');

// اجرای واقعی کال‌بک
\ErfanSanat\Theme\Frontend\DeBloat::dequeue_block_assets();
$dequeued = $GLOBALS['__wp_hooks']['dequeued_styles'];
foreach (['wp-block-library', 'wp-block-library-theme', 'wc-blocks-style', 'global-styles', 'classic-theme-styles'] as $handle) {
    check(in_array($handle, $dequeued, true), "صف‌خارجی: {$handle}");
}

// ---- گارد ادمین: در پیشخوان هیچ remove_action نباید اجرا شود ----
es_stubs_reset();
$GLOBALS['__es_theme_dir'] = $root;
$GLOBALS['__wp_is_admin']  = true;
require $root . '/functions.php';
// بوت قالب در ادمین اجرا شده؛ چون DeBloat::init گارد دارد نباید چیزی حذف کرده باشد
$removed_admin = $GLOBALS['__wp_hooks']['removed_actions'];
$dequeuer_in_admin = false;
foreach ($GLOBALS['__wp_hooks']['actions']['wp_enqueue_scripts'] ?? [] as $entry) {
    if (is_array($entry['cb']) && $entry['cb'][1] === 'dequeue_block_assets') {
        $dequeuer_in_admin = true;
    }
}
check($removed_admin === [] && !$dequeuer_in_admin, 'در زمینه ادمین هیچ حذف/انکیویی از DeBloat ثبت نشده');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
