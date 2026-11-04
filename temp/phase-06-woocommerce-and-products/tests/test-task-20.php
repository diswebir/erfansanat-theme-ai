<?php
declare(strict_types=1);
require __DIR__ . '/test-bootstrap.php';
echo "=== [TEST] Task 6.20 my account and auth integrations ===\n";
$account = (string) file_get_contents($themeRoot . '/woocommerce/myaccount/my-account.php');
$assert(str_contains($account, 'woocommerce_account_navigation') && str_contains($account, 'woocommerce_account_content'), 'Woo account navigation and endpoint content hooks included');
add_shortcode('fl-panel', static fn(): string => '<div>FL_PANEL_QA</div>');
add_shortcode('voorodak', static fn(): string => '<div>VOORODAK_QA</div>');
ob_start(); get_template_part('template-parts/global/auth-panels'); $html = (string) ob_get_clean();
$templateSource = (string) file_get_contents($themeRoot . '/page-templates/template-auth.php');
$assert(str_contains($templateSource, 'Template Name: ورود کاربران') && str_contains($templateSource, 'data-auth-page="true"'), 'auth page shell and template registration');
foreach (['FL_PANEL_QA', 'VOORODAK_QA', 'data-auth-provider="fl-panel"', 'data-auth-provider="voorodak"'] as $needle) $assert(str_contains($html, $needle), 'auth integration ' . $needle);
$assert(is_file($themeRoot . '/woocommerce/myaccount/my-account.php'), 'Woo account template exists');
$finishTest();
