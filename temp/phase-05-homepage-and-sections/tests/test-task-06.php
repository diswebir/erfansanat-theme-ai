<?php
/** Task 5.6 — تأسیس شرکت. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'establishment', 'template-parts/home/section-about-establishment.php', 4, 'تأسیس شرکت' );
summary();
