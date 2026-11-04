<?php
/**
 * Automated QA Test Script — Task 1.2: Zero-Bloat DeBloat Service (Gutenberg, Emojis, wp_head Cleanup)
 * Phase: phase-01-core-and-assets
 */
declare(strict_types=1);

$themeRoot = dirname(__DIR__, 3);
$wpRoot    = '/opt/wp-testbed';

echo "=== [TEST] Task 1.2: DeBloat Service (Zero Block/Emoji/Head Bloat) ===\n";

$failures = [];
$assert = static function (bool $cond, string $label) use (&$failures): void {
    if ($cond) {
        echo "  [PASS] {$label}\n";
    } else {
        echo "  [FAIL] {$label}\n";
        $failures[] = $label;
    }
};

$debloatFile = $themeRoot . '/inc/Core/DeBloat.php';
$assert(is_file($debloatFile), 'Required file exists: inc/Core/DeBloat.php');

if (is_file($debloatFile)) {
    $lintOut = [];
    $lintCode = 0;
    exec('php -l ' . escapeshellarg($debloatFile) . ' 2>&1', $lintOut, $lintCode);
    $assert($lintCode === 0, 'php -l syntax valid: inc/Core/DeBloat.php');
}

$capturedErrors = [];
set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline) use (&$capturedErrors, $themeRoot): bool {
    if (str_starts_with($errfile, $themeRoot)) {
        $capturedErrors[] = "[{$errno}] {$errstr} in {$errfile}:{$errline}";
    }
    return false;
});

require_once $wpRoot . '/wp-load.php';

$assert(class_exists(\ErfanSanat\Theme\Core\DeBloat::class), 'DeBloat class loaded via PSR-4');
$assert(
    \ErfanSanat\Theme\Core\Plugin::instance()->getService(\ErfanSanat\Theme\Core\DeBloat::class) !== null,
    'DeBloat service registered in Plugin container'
);

// Run frontend main query and capture wp_head + wp_footer output
$GLOBALS['wp']->main();
ob_start();
wp_head();
$headHtml = (string) ob_get_clean();

ob_start();
wp_footer();
$footerHtml = (string) ob_get_clean();

$combinedHtml = $headHtml . "\n" . $footerHtml;

$forbiddenSnippets = [
    'wp-block-library'            => 'No wp-block-library CSS in output',
    'global-styles'               => 'No global-styles inline CSS in output',
    'classic-theme-styles'        => 'No classic-theme-styles CSS in output',
    'wp-block-styles-placeholder' => 'No WP 7.x block-styles placeholder in output',
    'wp-emoji'                    => 'No WordPress emoji scripts/styles in output',
    's.w.org/images/core/emoji'   => 'No external s.w.org emoji CDN references',
    'name="generator"'            => 'No <meta name="generator"> tag in <head>',
    'wlwmanifest'                 => 'No wlwmanifest link in <head>',
    'EditURI'                     => 'No RSD EditURI link in <head>',
    'https://api.w.org/'          => 'No REST discovery link bloat in <head>',
    'wp-oembed'                   => 'No oEmbed host JS/discovery bloat in <head>',
];

foreach ($forbiddenSnippets as $needle => $label) {
    $assert(!str_contains($combinedHtml, $needle), $label);
}

// Verify Gutenberg block editor is disabled for posts
$assert(apply_filters('use_block_editor_for_post', true, null) === false, 'Block editor disabled via use_block_editor_for_post');
$assert(apply_filters('use_widgets_block_editor', true) === false, 'Block widgets editor disabled via use_widgets_block_editor');

$assert(empty($capturedErrors), 'Zero PHP warnings/notices from theme files (' . implode('; ', $capturedErrors) . ')');

restore_error_handler();

if (!empty($failures)) {
    fwrite(STDERR, "\n[FAILED] Task 1.2 failed " . count($failures) . " check(s).\n");
    exit(1);
}

echo "\n[PASSED] Task 1.2 passed all checks!\n";
exit(0);
