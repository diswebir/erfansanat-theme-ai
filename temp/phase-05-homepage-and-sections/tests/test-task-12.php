<?php
/** Task 5.12 — پشتیبانی و گارانتی. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'support', 'template-parts/home/section-support.php', 10, 'پشتیبانی و گارانتی' );
$markup = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/home/static/support.html' );
check( 'بخش پشتیبانی بدون iframe یا اسکریپت شخص ثالث رندر می‌شود', fn() => ! preg_match( '/<script\\b|<iframe\\b/i', $markup ) && str_contains( $markup, '<section' ) );
summary();
