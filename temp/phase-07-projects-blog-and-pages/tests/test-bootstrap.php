<?php
declare(strict_types=1);
$themeRoot = dirname(__DIR__, 3);
$failures = [];
$assert = static function (bool $condition, string $label) use (&$failures): void { echo $condition ? "  [PASS] $label\n" : "  [FAIL] $label\n"; if (!$condition) $failures[] = $label; };
require_once '/opt/wp-testbed/wp-load.php';
$GLOBALS['wp']->main();
$themeWarnings = [];
set_error_handler(static function (int $severity, string $message, string $file) use (&$themeWarnings, $themeRoot): bool { if (!(error_reporting() & $severity)) return false; if (str_starts_with($file, $themeRoot) && !str_starts_with($file, $themeRoot . '/temp/')) $themeWarnings[] = $message; return false; });
$makeProject = static function (string $title = 'پروژه آزمون', string $content = 'شرح پروژه آزمون'): int {
    $id = wp_insert_post(['post_type' => 'projects', 'post_status' => 'publish', 'post_title' => $title, 'post_content' => $content, 'post_excerpt' => 'خلاصه پروژه آزمون']);
    if (is_wp_error($id)) throw new RuntimeException($id->get_error_message());
    wp_set_object_terms($id, 'پل شهری QA', 'project_type'); wp_set_object_terms($id, 'تهران QA', 'city');
    return (int) $id;
};
$renderPart = static function (string $path): string { ob_start(); get_template_part($path); return (string) ob_get_clean(); };
$finishTest = static function () use (&$failures, &$themeWarnings): never { restore_error_handler(); if ($themeWarnings !== []) { $failures[] = 'theme warnings: ' . implode('; ', $themeWarnings); echo "  [FAIL] warnings: " . implode('; ', $themeWarnings) . "\n"; } if ($failures !== []) { fwrite(STDERR, '[FAILED] ' . implode('; ', $failures) . "\n"); exit(1); } echo "[PASSED]\n"; exit(0); };
