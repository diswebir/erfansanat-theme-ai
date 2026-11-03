<?php
/**
 * Test 3.3 — ثبت CPT services (تصمیم Q2: CPT اختصاصی با has_archive=false)
 * اجرا: /home/user/tools/php -f temp/phase-03-cpt-and-legacy-meta/tests/test-task-03.php
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
echo "== Test 3.3: CPT services ==\n";

require $root . '/temp/harness/wp-stubs.php';
require $root . '/inc/Helpers.php';
$GLOBALS['__es_theme_dir'] = $root;
require $root . '/functions.php';

\ErfanSanat\Theme\Data\PostTypes::register();

$svc = $GLOBALS['__wp_hooks']['post_types']['services'] ?? [];
check($svc !== [], 'CPT services ثبت شد');
check(($svc['public'] ?? null) === true, 'services: public=true');
check(($svc['has_archive'] ?? null) === false, 'services: has_archive=false (بدون تداخل با برگه فهرست — D-005)');
check(($svc['rewrite']['slug'] ?? '') === 'services' && ($svc['rewrite']['with_front'] ?? null) === false, 'services: rewrite slug=services با with_front=false');
check(($svc['show_in_rest'] ?? null) === true, 'services: show_in_rest=true');
$required_supports = ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'];
check(array_values(array_intersect($required_supports, $svc['supports'] ?? [])) === $required_supports, 'services: supports کامل (title/editor/thumbnail/excerpt/custom-fields/revisions)');
check(($svc['menu_icon'] ?? '') === 'dashicons-lightbulb', 'services: menu_icon=dashicons-lightbulb');
check(($svc['labels']['name'] ?? '') === 'خدمات', 'لیبل فارسی خدمات');

echo "\nنتیجه: {$passes} پاس، " . count($failures) . " رد\n";
exit(empty($failures) ? 0 : 1);
