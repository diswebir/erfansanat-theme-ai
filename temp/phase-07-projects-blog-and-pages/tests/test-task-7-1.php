<?php
/**
 * Test Task 7.1 — project single/archive/taxonomy templates with legacy meta.
 * Run: php-wasm-cli temp/phase-07-projects-blog-and-pages/tests/test-task-7-1.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c ? "[PASS] " : "[FAIL] ") . $l . "\n"; if (!$c) $fail++; }

foreach (['single-projects.php', 'archive-projects.php', 'taxonomy-project_type.php', 'taxonomy-city.php'] as $f) {
    check("$f exists", is_file("$THEME/$f"));
}
$s = file_get_contents("$THEME/single-projects.php");
foreach (['second-image', 'three-image', 'foure-image', 'five-image', 'six-image', 'finish-pro-time', 'mortabet_mahsole_2'] as $k) {
    check("single-projects meta: $k", strpos($s, "'$k'") !== false);
}
check('single-projects uses city + project_type terms', strpos($s, "'city'") !== false && strpos($s, "'project_type'") !== false);
check('single-projects renders related products (card-product)', strpos($s, "card', 'product") !== false);
check('single-projects has comments', strpos($s, 'comments_template') !== false);

$a = file_get_contents("$THEME/archive-projects.php");
check('archive-projects loops card-project + pagination', strpos($a, "card', 'project") !== false && strpos($a, 'the_posts_pagination') !== false);
$ty = file_get_contents("$THEME/taxonomy-project_type.php");
check('taxonomy-project_type loops card-project', strpos($ty, "card', 'project") !== false);
$cy = file_get_contents("$THEME/taxonomy-city.php");
check('taxonomy-city shows project count', strpos($cy, '->count') !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
