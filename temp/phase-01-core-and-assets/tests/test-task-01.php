<?php
/**
 * Contract test for task 1.1: base theme scaffold and namespaced bootstrap.
 * Run from repository root with: php temp/phase-01-core-and-assets/tests/test-task-01.php
 * WordPress activation must also be verified in a WordPress-enabled environment.
 */
$root = dirname(__DIR__, 3);
$checks = [
    ['style.css', 'Theme Name: ErfanSanat Theme'],
    ['functions.php', 'Autoloader::register'],
    ['functions.php', 'Theme::register'],
    ['inc/Autoloader.php', 'spl_autoload_register'],
    ['inc/Autoloader.php', 'namespace ErfanSanat\\Theme;'],
    ['inc/Autoloader.php', 'private const PREFIX'],
    ['inc/Theme.php', "add_theme_support('title-tag')"],
    ['inc/Theme.php', "add_theme_support('post-thumbnails')"],
    ['inc/Theme.php', "add_theme_support('woocommerce')"],
    ['inc/Theme.php', "'html5',"],
    ['index.php', 'wp_head()'],
    ['index.php', 'wp_footer()'],
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
