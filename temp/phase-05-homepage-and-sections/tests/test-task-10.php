<?php
/** Task 5.10 — خدمات تخصصی. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'services', 'template-parts/home/section-services.php', 8, 'خدمات تخصصی' );
$markup = (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/home/static/services.html' );
check( 'counterهای خدمات با ردیف‌ها برابر و hookهای JS موجودند', fn() => substr_count( $markup, 'data-service-row="' ) === substr_count( $markup, 'data-service-counter="' ) && str_contains( $markup, 'data-services-section="true"' ) && str_contains( $markup, 'data-service-preview-img="true"' ) );
$javascript = (string) file_get_contents( ES_REPO_ROOT . '/theme/assets/js/main.js' );
check( 'main.js همان hookهای row و preview را مصرف می‌کند', fn() => str_contains( $javascript, 'querySelectorAll("[data-service-row]")' ) && str_contains( $javascript, 'data-service-preview-img="true"' ) && str_contains( $javascript, 'data-service-counter' ) );
$services_schema = \ErfanSanat\Theme\Sections\Registry::get( 'home' )['services'];
$service_data = (array) $services_schema['default_data'];
check( 'پنج خدمت در فیلد Repeater عنوان/توضیح/تصویر قابل ویرایش تعریف شده‌اند', function () use ( $services_schema, $service_data ) { foreach ( $services_schema['fields'] as $field ) { if ( 'services' === ( $field['id'] ?? '' ) ) { return 'repeater' === $field['type'] && 5 === count( $service_data['services'] ?? array() ) && array( 'title', 'description', 'image' ) === array_column( $field['fields'], 'id' ); } } return false; } );
$json = (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/services.json' );
$service_data['services'][0] = array( 'title' => 'Custom service', 'description' => 'Custom service description', 'image' => 'https://example.test/service-custom.jpg' );
$args = array( 'section' => 'services', 'data' => array( 'content_json' => $json, 'services' => $service_data['services'] ) );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-services.php'; $custom = (string) ob_get_clean();
check( 'Repeater تغییر عنوان، توضیح و تصویر را هم در preview هم در JS data attributes اعمال می‌کند', fn() => str_contains( $custom, 'Custom service' ) && str_contains( $custom, 'Custom service description' ) && str_contains( $custom, 'https://example.test/service-custom.jpg' ) );
$args['data']['services'] = array_slice( $service_data['services'], 0, 3 );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-services.php'; $short = (string) ob_get_clean();
check( 'تعداد ردیف/شمارنده با Repeater کوتاه‌شده هماهنگ می‌شود', fn() => 3 === substr_count( $short, 'data-service-row="' ) && str_contains( $short, 'data-service-counter="۰۱ / ۳"' ) && ! str_contains( $short, 'data-service-row="4"' ) );
summary();
