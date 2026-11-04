<?php
/** Task 5.11 — استانداردهای کیفیت. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'quality', 'template-parts/home/section-quality.php', 9, 'استانداردهای کیفیت' );
summary();
