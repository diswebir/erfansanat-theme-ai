<?php
/** Task 5.20 — home2/home3 hero variants explicitly excluded by user scope. */
declare(strict_types=1);
require_once __DIR__ . '/../../tests/bootstrap.php';
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', __DIR__ . '/' ); }
require_once __DIR__ . '/section-test-support.php';

$variants = array(
	ES_REPO_ROOT . '/theme/template-parts/home/hero-variants/hero-home2.php',
	ES_REPO_ROOT . '/theme/template-parts/home/hero-variants/hero-home3.php',
);
$task_record = (string) file_get_contents( ES_REPO_ROOT . '/temp/phase-05-homepage-and-sections/tasks/task-20.md' );
$renderer = (string) file_get_contents( ES_REPO_ROOT . '/theme/inc/Sections/Renderer.php' );
$home_schema = \ErfanSanat\Theme\Sections\Registry::get( 'home' );
check( 'تصمیم صریح کاربر برای خارج‌بودن home2/home3 در Task ثبت است', fn() => str_contains( $task_record, 'OUT OF SCOPE — explicit user decision' ) && str_contains( $task_record, 'markup فرضی/ساختگی نساز' ) );
check( 'برای دو variant خارج از محدوده markup تازه‌ای اضافه نشده است', fn() => ! file_exists( $variants[0] ) && ! file_exists( $variants[1] ) && isset( $home_schema['hero'] ) );
check( 'Renderer هنگام نبود variant مستقل به قالب ثبت‌شده‌ی hero برمی‌گردد', fn() => str_contains( $renderer, "\$template = \$sections[ \$key ]['template'];" ) && str_contains( $renderer, "if ( is_string( \$variant_located ) && '' !== \$variant_located ) { \$template = \$variant_template; }" ) && str_contains( $renderer, 'locate_template( array( $template )' ) );
summary();
