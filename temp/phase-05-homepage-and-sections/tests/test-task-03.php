<?php
/** Task 5.3 — پروژه‌های منتخب. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'projects', 'template-parts/home/section-projects-intro.php', 1, 'پروژه‌های منتخب' );
$markup = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/home/static/projects.html' );
check( 'سه کارت پروژه با لینک و تصویر در مارک‌آپ باقی مانده', fn() => substr_count( $markup, '<a ' ) === 3 && substr_count( $markup, '<img ' ) === 3 );
summary();
