<?php
/** Task 5.17 — authorized 16th homepage section: why ESE. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';

$schema = \ErfanSanat\Theme\Sections\Registry::get( 'home' );
$section = $schema['why-ese'] ?? array();
$json_path = ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/why-ese.json';
$json = is_file( $json_path ) ? (string) file_get_contents( $json_path ) : '';
$defaults = json_decode( $json, true );
$data = is_array( $section['default_data'] ?? null ) ? $section['default_data'] : array();
$args = array( 'section' => 'why-ese', 'data' => array_merge( $data, array( 'content_json' => $json ) ) );
$warnings = array();
set_error_handler( static function ( $severity, $message ) use ( &$warnings ) { $warnings[] = $message; return true; } );
$render = static function () use ( &$args ): string { ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-why-ese.php'; return (string) ob_get_clean(); };
$output = $render();

check( 'سکشن مجازشده در جایگاه شانزدهم Registry و با قالب مستقل ثبت است', fn() => 16 === count( $schema ) && 'why-ese' === array_key_last( $schema ) && 'template-parts/home/section-why-ese.php' === ( $section['template'] ?? '' ) && str_contains( $output, 'id="why-ese"' ) && str_contains( $output, 'data-why-ese-section="true"' ) );
check( 'چهار مزیت پشتیبانی‌شده از Repeater و آیکون‌های whitelist رندر می‌شوند', function () use ( $section, $data, $output ) { $field = null; foreach ( $section['fields'] ?? array() as $candidate ) { if ( 'features' === ( $candidate['id'] ?? '' ) ) { $field = $candidate; break; } } return 'repeater' === ( $field['type'] ?? '' ) && array( 'title', 'description', 'icon' ) === array_column( $field['fields'] ?? array(), 'id' ) && array( 'award', 'factory', 'badge-check', 'shield-check' ) === array_keys( $field['fields'][2]['options'] ?? array() ) && 4 === count( $data['features'] ?? array() ) && 4 === substr_count( $output, 'data-why-ese-feature="true"' ) && str_contains( $output, 'ISO 9001:2015' ) && str_contains( $output, '۵ سال خدمات پس از فروش' ); });
$args['data']['features'][0] = array( 'title' => '<script>alert(1)</script>ویرایش داشبورد', 'description' => 'توضیح ویرایش‌شده', 'icon' => 'untrusted-svg' );
$edited = $render();
check( 'ویرایش Repeater از داشبورد اثر می‌گذارد و عنوان را escape می‌کند', fn() => str_contains( $edited, 'ویرایش داشبورد' ) && str_contains( $edited, '&lt;script&gt;' ) && ! str_contains( $edited, '<script>alert(1)</script>' ) && ! str_contains( $edited, 'untrusted-svg' ) && 4 === substr_count( $edited, '<svg' ) && str_contains( $edited, 'data-why-ese-feature="true"' ) );
$args['data']['features'] = array();
$empty = $render();
restore_error_handler();
check( 'Repeater خالی بدون warning رندر می‌شود و placeholder خام باقی نمی‌ماند', fn() => 0 === substr_count( $empty, 'data-why-ese-feature="true"' ) && ! str_contains( $empty, '__ES_FEATURES__' ) && empty( $warnings ) && str_contains( $empty, 'id="why-ese"' ) );
check( 'متن‌های اصلی سکشن از JSON قابل ویرایش می‌آیند، نه از shell PHP', fn() => is_array( $defaults ) && str_contains( $output, (string) ( $defaults['T001']['value'] ?? '' ) ) && ! preg_match( '/[\x{0600}-\x{06FF}]/u', (string) file_get_contents( ES_REPO_ROOT . '/theme/template-parts/home/section-why-ese.php' ) ) );
summary();
