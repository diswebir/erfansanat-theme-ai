<?php
/** Task 5.7 — تاریخچه تعاملی. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'timeline', 'template-parts/home/section-timeline.php', 5, 'تاریخچه تعاملی' );
$markup = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/home/static/timeline.html' );
preg_match_all( '/data-home2-history-btn="(\d+)"/', $markup, $history );
$default_data = (array) json_decode( (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/timeline.json' ), true );
$total = ''; foreach ( $default_data as $key => $item ) { if ( 'data-history-total' === ( $item['attribute'] ?? '' ) ) { $total = (string) $item['value']; break; } }
check( 'تعداد گره‌های تاریخچه با data-history-total و selectorهای main.js برابر است', fn() => count( $history[1] ) === (int) $total && count( $history[1] ) === 6 && str_contains( $markup, 'data-history-scroll-track="true"' ) && str_contains( $markup, 'data-home2-history-prev="true"' ) && str_contains( $markup, 'data-home2-history-next="true"' ) );
summary();
