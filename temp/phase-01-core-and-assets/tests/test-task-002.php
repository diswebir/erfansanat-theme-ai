<?php
/**
 * P01-T002 — Bootstrap و ثابت‌های قالب
 * Run: php temp/phase-01-core-and-assets/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T002', 'Bootstrap و ثابت‌های قالب' );

es_check( 'target: functions.php', es_file( 'functions.php' ) );
es_check( 'target: inc/Constants.php', es_file( 'inc/Constants.php' ) );
es_check( 'AC-01: ABSPATH guard در functions.php', es_contains( 'functions.php', "defined( 'ABSPATH' ) || exit" ) );
es_check( 'AC-01: ABSPATH guard در Constants.php', es_contains( 'inc/Constants.php', "defined( 'ABSPATH' ) || exit" ) );

if ( es_load_wp() ) {
	// Load bootstrap twice; require_once + idempotent boot must prevent errors.
	require_once ES_THEME . '/functions.php';
	require_once ES_THEME . '/functions.php';

	es_check( 'AC-02: ES_THEME_DIR تعریف شده', defined( 'ES_THEME_DIR' ) );
	es_check( 'AC-02: ES_THEME_URI تعریف شده', defined( 'ES_THEME_URI' ) );
	es_check( 'AC-02: ES_VERSION تعریف شده', defined( 'ES_VERSION' ) );
	es_check( 'SC-02: ES_THEME_DIR به پوسته resolve می‌شود', realpath( ES_THEME_DIR ) === realpath( ES_THEME ) );
	es_check( 'SC-02: ES_THEME_URI حاوی erfansanat است', false !== strpos( ES_THEME_URI, 'erfansanat' ) );
	es_check( 'AC-02: ES_VERSION با هدر قالب برابر است', ES_VERSION === (string) wp_get_theme( 'erfansanat' )->get( 'Version' ) );
	es_check( 'AC-03: Autoloader یک‌بار register شده', \ErfanSanat\Theme\Autoloader::is_registered() );
	es_check( 'SC-01: بارگذاری دوباره bootstrap خطا نداد', true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
