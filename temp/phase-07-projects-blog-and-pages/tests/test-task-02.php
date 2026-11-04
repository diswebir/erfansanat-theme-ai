<?php
/**
 * Contract-test scaffold for task 7.2: وبلاگ، جستجو و 404.
 * Run from repository root with: php temp/phase-07-projects-blog-and-pages/tests/test-task-02.php
 * This planning-stage test is expected to fail until its task is implemented.
 */
$root = dirname(__DIR__, 3);
$checks = [
    ['single.php', 'number2-viewc'],
    ['search.php', 'get_search_query'],
    ['404.php', '404']
];
$failed = 0;
foreach ($checks as [$relative, $needle]) {
    $path = $root . DIRECTORY_SEPARATOR . $relative;
    if (!is_file($path)) {
        fwrite(STDERR, "FAIL missing file: {$relative}\n");
        $failed++;
        continue;
    }
    $source = file_get_contents($path);
    if ($source === false || strpos($source, $needle) === false) {
        fwrite(STDERR, "FAIL missing contract `{$needle}` in {$relative}\n");
        $failed++;
    } else {
        fwrite(STDOUT, "PASS {$relative} contains `{$needle}`\n");
    }
}
exit($failed === 0 ? 0 : 1);
