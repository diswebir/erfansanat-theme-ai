<?php
/** Task 5.9 — media-backed catalog download CTA. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'catalog', 'template-parts/home/section-catalog.php', 7, 'بنر کاتالوگ', array( 'catalog_file' => 'https://example.test/catalog-1405.pdf' ) );
$json = (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/catalog.json' );
$args = array( 'section' => 'catalog', 'data' => array( 'content_json' => $json ) );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-catalog.php'; $without = (string) ob_get_clean();
check( 'بدون فایل، دکمه دانلود پنهان است و بنر/لینک اطلاعاتی باقی می‌ماند', fn() => ! str_contains( $without, 'مشاهده و دریافت کاتالوگ محصولات' ) && str_contains( $without, 'کاتالوگ فنی و اجرایی' ) );
$args['data']['catalog_file'] = 'https://example.test/catalog-1405.pdf';
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-catalog.php'; $with_file = (string) ob_get_clean();
check( 'لینک فایل esc_url می‌شود و target جدید با rel امن دارد', fn() => str_contains( $with_file, 'href="https://example.test/catalog-1405.pdf"' ) && str_contains( $with_file, 'target="_blank" rel="noopener noreferrer"' ) );
$schema = \ErfanSanat\Theme\Sections\Registry::get( 'home' )['catalog'];
check( 'فایل کاتالوگ فیلد Media Library از نوع attachment در schema است', function () use ( $schema ) { foreach ( $schema['fields'] as $field ) { if ( 'catalog_file' === ( $field['id'] ?? '' ) ) { return 'media' === $field['type'] && 'file' === $field['media_kind'] && 'url' === $field['return']; } } return false; } );
summary();
