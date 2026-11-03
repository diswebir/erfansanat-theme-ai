<?php
/**
 * Test 3.1 — ثبت CPT projects و تکسونومی‌های project_type/city مطابق بلوپرینت
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-01.php
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
echo "== Test 3.1: CPT projects و تکسونومی‌ها ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
require $root . '/functions.php';

check(class_exists(\ErfanSanat\Theme\Data\PostTypes::class), 'کلاس PostTypes از اوتولودر resolve شد');

// هوک init ثبت شده؟
$registered = false;
foreach ($GLOBALS['__wp_hooks']['actions']['init'] ?? [] as $entry) {
    if (is_array($entry['cb']) && $entry['cb'] === [\ErfanSanat\Theme\Data\PostTypes::class, 'register']) {
        $registered = true;
    }
}
check($registered, 'init → PostTypes::register ثبت شد');

// اجرای ثبت
\ErfanSanat\Theme\Data\PostTypes::register();

$pt = $GLOBALS['__wp_hooks']['post_types']['projects'] ?? [];
check($pt !== [], 'CPT projects ثبت شد');
check(($pt['public'] ?? null) === true, 'projects: public=true');
check(($pt['has_archive'] ?? null) === true, 'projects: has_archive=true');
check(($pt['rewrite']['slug'] ?? '') === 'projects' && ($pt['rewrite']['with_front'] ?? null) === false, 'projects: rewrite slug=projects با with_front=false');
check(($pt['hierarchical'] ?? null) === false, 'projects: hierarchical=false');
check(($pt['menu_icon'] ?? '') === 'dashicons-screenoptions', 'projects: menu_icon=dashicons-screenoptions');
check(($pt['supports'] ?? []) === ['title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions'], 'projects: supports دقیق بلوپرینت');
check(($pt['show_in_rest'] ?? null) === true, 'projects: show_in_rest=true');

// تکسونومی project_type
$tax = $GLOBALS['__wp_hooks']['taxonomies']['project_type'] ?? [];
check($tax !== [], 'تکسونومی project_type ثبت شد');
check(($tax['object_type'] ?? []) === ['projects'], 'project_type متصل به projects');
check(($tax['hierarchical'] ?? null) === true, 'project_type: hierarchical=true');
check(($tax['rewrite']['slug'] ?? '') === 'project_type' && ($tax['rewrite']['with_front'] ?? null) === false && ($tax['rewrite']['hierarchical'] ?? null) === false, 'project_type: rewrite مطابق بلوپرینت (with_front=false, hierarchical=false)');
check(($tax['show_in_rest'] ?? null) === true, 'project_type: show_in_rest=true');

// تکسونومی city
$city = $GLOBALS['__wp_hooks']['taxonomies']['city'] ?? [];
check($city !== [] && ($city['object_type'] ?? []) === ['projects'], 'تکسونومی city ثبت و متصل به projects');
check(($city['rewrite']['slug'] ?? '') === 'city' && ($city['rewrite']['with_front'] ?? null) === false, 'city: rewrite slug=city با with_front=false');
check(($city['hierarchical'] ?? null) === true, 'city: hierarchical=true');

// لیبل فارسی
check(($pt['labels']['name'] ?? '') === 'پروژه‌ها', 'لیبل فارسی پروژه‌ها');
check(($tax['labels']['name'] ?? '') === 'نوع پروژه' && ($city['labels']['name'] ?? '') === 'محل پروژه', 'لیبل‌های فارسی تکسونومی‌ها');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
