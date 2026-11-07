<?php
/**
 * Test Task 7.2 — blog templates, single.php (TOC/share/counter/related), search, 404.
 * Run: php-wasm-cli temp/phase-07-projects-blog-and-pages/tests/test-task-7-2.php
 */
$THEME = dirname(__DIR__, 3);
$fail = 0;
function check(string $l, bool $c): void { global $fail; echo ($c ? "[PASS] " : "[FAIL] ") . $l . "\n"; if (!$c) $fail++; }

foreach (['home.php', 'archive.php', 'category.php', 'single.php', 'search.php', '404.php'] as $f) {
    check("$f exists", is_file("$THEME/$f"));
}
$s = file_get_contents("$THEME/single.php");
check('single has TOC container (es-toc)', strpos($s, 'es-toc') !== false);
check('single has share links', strpos($s, 't.me/share') !== false && strpos($s, 'wa.me') !== false);
check('single shows view counter number2-viewc + number-viewc fallback', strpos($s, 'number2-viewc') !== false && strpos($s, 'number-viewc') !== false);
check('single has related posts (WP_Query)', strpos($s, 'WP_Query') !== false);
check('single has comments', strpos($s, 'comments_template') !== false);

$h = file_get_contents("$THEME/home.php");
check('home loops card-post + pagination', strpos($h, "card', 'post") !== false && strpos($h, 'the_posts_pagination') !== false);
$se = file_get_contents("$THEME/search.php");
check('search renders results + pagination', strpos($se, 'get_search_query') !== false && strpos($se, 'have_posts') !== false);
$n = file_get_contents("$THEME/404.php");
check('404 has home link', strpos($n, "home_url('/')") !== false);

echo $fail === 0 ? "\nRESULT: ALL PASS\n" : "\nRESULT: $fail FAILURES\n";
exit($fail > 0 ? 1 : 0);
