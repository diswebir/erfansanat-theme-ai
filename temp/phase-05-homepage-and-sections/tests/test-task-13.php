<?php
/** Task 5.13 — hide the client logo shelf when its editable list is empty. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'clients', 'template-parts/home/section-clients.php', 11, 'مشتریان و سازمان‌های همکار' );
$args = array( 'section' => 'clients', 'data' => array( 'content_json' => '[]' ) );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-clients.php'; $empty = (string) ob_get_clean();
check( 'در فهرست خالی، سکشن مشتریان کامل حذف می‌شود', fn() => '' === trim( $empty ) );
summary();
