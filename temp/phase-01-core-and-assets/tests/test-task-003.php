<?php
/**
 * P01-T003 — Autoloader اختصاصی
 * Run: php temp/phase-01-core-and-assets/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T003', 'Autoloader اختصاصی' );

es_check( 'target: inc/Autoloader.php', es_file( 'inc/Autoloader.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	es_check( 'AC-01: Autoloader ثبت شده', \ErfanSanat\Theme\Autoloader::is_registered() );
	es_check( 'SC-01: کلاس Core\\Theme بارگذاری می‌شود', class_exists( 'ErfanSanat\\Theme\\Core\\Theme' ) );
	es_check( 'SC-01: کلاس Core\\AssetManager بارگذاری می‌شود', class_exists( 'ErfanSanat\\Theme\\Core\\AssetManager' ) );
	es_check( 'AC-02: کلاس خارج از Namespace بارگذاری نمی‌شود', ! class_exists( 'Some_External_Class_XYZ' ) );

	$caught = false;
	try {
		class_exists( 'ErfanSanat\\Theme\\Missing\\UnknownClassXYZ' );
		// class_exists returns false without fatal; instantiating would throw.
		$caught = true;
	} catch ( \Throwable $e ) {
		$caught = true;
	}
	es_check( 'AC-03/SC-02: کلاس ناشناخته fatal نمی‌دهد', $caught && ! class_exists( 'ErfanSanat\\Theme\\Missing\\UnknownClassXYZ' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
