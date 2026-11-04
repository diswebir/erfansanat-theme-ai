<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 7.11 not-found page ===\n";
ob_start(); include $themeRoot . '/404.php'; $html = (string) ob_get_clean();
foreach (['data-error-page="404"', 'صفحه مورد نظر پیدا نشد', 'data-404-search="true"', 'name="s"', 'href="' . esc_url(home_url('/projects/')) . '"', 'href="' . esc_url(home_url('/shop/')) . '"', 'href="' . esc_url(home_url('/contact/')) . '"'] as $needle) $assert(str_contains($html, $needle), '404 page contract ' . $needle);
$assert(is_file($themeRoot . '/404.php'), '404 template exists');
$finishTest();
