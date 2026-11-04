<?php
/** Task 5.5 — editable statistics with static and real-project-count fallbacks. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
$GLOBALS['es_p5_project_count'] = 321;
function wp_count_posts( $type ) { return (object) array( 'publish' => (int) $GLOBALS['es_p5_project_count'] ); }
require_once __DIR__ . '/section-test-support.php';
run_p5_fragment_checks( 'about', 'template-parts/home/section-about.php', 3, 'درباره و آمار' );
$schema = \ErfanSanat\Theme\Sections\Registry::get( 'home' )['about'];
$defaults = (array) $schema['default_data'];
check( 'Repeater آمار و toggle شمارش پروژه در schema قابل ویرایشند', function () use ( $schema, $defaults ) { $ids = array_column( $schema['fields'], 'id' ); return in_array( 'stats', $ids, true ) && in_array( 'use_project_count', $ids, true ) && is_array( $defaults['stats'] ?? null ) && 4 === count( $defaults['stats'] ); } );
$content_json = (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Schema/home-content/about.json' );
$args = array( 'section' => 'about', 'data' => array( 'content_json' => $content_json, 'stats' => $defaults['stats'], 'use_project_count' => true ) );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-about.php'; $real_count = (string) ob_get_clean();
check( 'شمارنده اول از wp_count_posts projects و DOM/JS target هم‌زمان به‌روز می‌شوند', fn() => str_contains( $real_count, 'data-counter-target="321"' ) && str_contains( $real_count, '۳۲۱+' ) && substr_count( $real_count, 'data-counter-target=' ) === 4 );
$args['data']['use_project_count'] = false;
$args['data']['stats'][0] = array( 'value' => '42', 'suffix' => '+', 'label' => 'Edited metric', 'description' => 'Edited description' );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-about.php'; $edited = (string) ob_get_clean();
check( 'ویرایش repeater مقدار، برچسب و توضیح را از داشبورد اعمال می‌کند', fn() => str_contains( $edited, 'data-counter-target="42"' ) && str_contains( $edited, '۴۲+' ) && str_contains( $edited, 'Edited metric' ) && str_contains( $edited, 'Edited description' ) );
$args = array( 'section' => 'about', 'data' => array( 'content_json' => $content_json ) );
ob_start(); include ES_REPO_ROOT . '/theme/template-parts/home/section-about.php'; $fallback = (string) ob_get_clean();
check( 'در نبود repeater/toggle مقادیر استاتیک fallback می‌شوند', fn() => str_contains( $fallback, 'data-counter-target="850"' ) && str_contains( $fallback, '۸۵۰+' ) );
summary();
