<?php
/**
 * Automated QA Test Script — Task 1.1: Core Skeleton, PSR-4 Autoloader & Theme Supports
 * Phase: phase-01-core-and-assets
 */
declare(strict_types=1);

$themeRoot = dirname(__DIR__, 3);
$wpRoot    = '/opt/wp-testbed';
$themeSlug = 'erfansanat-theme-ai';

echo "=== [TEST] Task 1.1: Core Skeleton, PSR-4 Autoloader & Theme Supports ===\n";

$failures = [];
$assert = static function (bool $cond, string $label) use (&$failures): void {
    if ($cond) {
        echo "  [PASS] {$label}\n";
    } else {
        echo "  [FAIL] {$label}\n";
        $failures[] = $label;
    }
};

// 1. Check required files exist
$requiredFiles = [
    'style.css',
    'functions.php',
    'index.php',
    'inc/Autoloader.php',
    'inc/Contracts/ServiceInterface.php',
    'inc/Core/ThemeSetup.php',
    'inc/Core/Plugin.php',
];

foreach ($requiredFiles as $rel) {
    $assert(is_file($themeRoot . '/' . $rel), "Required file exists: {$rel}");
}

// 2. Lint all theme PHP files (excluding temp/ and static-mirror/)
$phpFiles = [];
$rii = new RecursiveIteratorIterator(
    new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator($themeRoot, FilesystemIterator::SKIP_DOTS),
        static function (SplFileInfo $current) use ($themeRoot): bool {
            $rel = ltrim(str_replace($themeRoot, '', $current->getPathname()), '/');
            if (str_starts_with($rel, 'temp') || str_starts_with($rel, 'static-mirror') || str_starts_with($rel, '.git')) {
                return false;
            }
            return true;
        }
    )
);
foreach ($rii as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $phpFiles[] = $file->getPathname();
    }
}

$assert(count($phpFiles) >= 5, 'Found theme PHP files to lint (' . count($phpFiles) . ')');
foreach ($phpFiles as $pf) {
    $out = [];
    $code = 0;
    exec('php -l ' . escapeshellarg($pf) . ' 2>&1', $out, $code);
    $rel = ltrim(str_replace($themeRoot, '', $pf), '/');
    $assert($code === 0, "php -l syntax valid: {$rel}");
}

// 3. Symlink theme into real WordPress testbed and activate it via WP-CLI
$wpThemeLink = $wpRoot . '/wp-content/themes/' . $themeSlug;
if (!is_link($wpThemeLink) && !is_dir($wpThemeLink)) {
    symlink($themeRoot, $wpThemeLink);
}
$cliOut = [];
$cliCode = 0;
exec('wp theme activate ' . escapeshellarg($themeSlug) . ' --path=' . escapeshellarg($wpRoot) . ' 2>&1', $cliOut, $cliCode);
$assert($cliCode === 0, 'Theme activated cleanly in WordPress 7.x via WP-CLI: ' . implode(' | ', $cliOut));

// 4. Boot WordPress in-process with strict error capture
$capturedErrors = [];
set_error_handler(static function (int $errno, string $errstr, string $errfile, int $errline) use (&$capturedErrors, $themeRoot): bool {
    if (str_starts_with($errfile, $themeRoot)) {
        $capturedErrors[] = "[{$errno}] {$errstr} in {$errfile}:{$errline}";
    }
    return false;
});

require_once $wpRoot . '/wp-load.php';

$assert(get_stylesheet() === $themeSlug, "Active stylesheet is '{$themeSlug}'");
$assert(class_exists(\ErfanSanat\Theme\Autoloader::class), 'Autoloader class loaded');
$assert(class_exists(\ErfanSanat\Theme\Core\Plugin::class), 'PSR-4 resolved ErfanSanat\\Theme\\Core\\Plugin');
$assert(class_exists(\ErfanSanat\Theme\Core\ThemeSetup::class), 'PSR-4 resolved ErfanSanat\\Theme\\Core\\ThemeSetup');

// 5. Check theme supports
foreach (['title-tag', 'post-thumbnails', 'woocommerce', 'html5'] as $feature) {
    $assert((bool) current_theme_supports($feature), "Theme supports '{$feature}'");
}

// 6. Check all 4 menu locations from Blueprint Section 6
$menus = get_registered_nav_menus();
foreach (['main-mega-menu', 'mobile-menu', 'tolidat-sherkat', 'landing'] as $loc) {
    $assert(array_key_exists($loc, $menus), "Registered nav menu location: '{$loc}'");
}

// 7. Check body_class includes static design classes
$bodyClasses = apply_filters('body_class', []);
$assert(in_array('bg-[#f6f8f7]', $bodyClasses, true) && in_array('antialiased', $bodyClasses, true), 'body_class preserves static Tailwind base classes');

// 8. Check zero theme PHP warnings/notices
$assert(empty($capturedErrors), 'Zero PHP warnings/notices from theme files (' . implode('; ', $capturedErrors) . ')');

restore_error_handler();

if (!empty($failures)) {
    fwrite(STDERR, "\n[FAILED] Task 1.1 failed " . count($failures) . " check(s).\n");
    exit(1);
}

echo "\n[PASSED] Task 1.1 passed all checks!\n";
exit(0);
