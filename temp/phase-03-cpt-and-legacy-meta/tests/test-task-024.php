<?php
/**
 * P03-T024 — ذخیره‌گاه شمارنده بازدید
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-024.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\ViewCounter;

es_task( 'P03-T024', 'ذخیره‌گاه شمارنده بازدید' );

es_check( 'target: inc/Core/ViewCounter.php', es_file( 'inc/Core/ViewCounter.php' ) );
es_check( 'AC-04: افزایش اتمیک با یک UPDATE است', es_contains( 'inc/Core/ViewCounter.php', 'UPDATE' ) && es_contains( 'inc/Core/ViewCounter.php', 'meta_value + 1' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$pid = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه بازدید' ) );

	// SC-01 / AC-02: fallback read from number-viewc when primary is absent.
	update_post_meta( $pid, 'number-viewc', 7 );
	es_check( 'SC-01: fallback number-viewc خوانده می‌شود', 7 === ViewCounter::get( $pid ) );
	es_check( 'AC-01: هنوز primary ساخته نشده', ! metadata_exists( 'post', $pid, 'number2-viewc' ) );

	// AC-01: primary takes precedence once present.
	update_post_meta( $pid, 'number2-viewc', 20 );
	es_check( 'AC-01: number2-viewc منبع اصلی است', 20 === ViewCounter::get( $pid ) );

	// SC-02: two increments never lose a count (atomic).
	ViewCounter::increment( $pid );
	ViewCounter::increment( $pid );
	es_check( 'SC-02: دو افزایش مقدار را گم نمی‌کند', 22 === ViewCounter::get( $pid ) );

	// AC-02: the fallback key is read-only (never written by increment).
	es_check( 'AC-02: number-viewc دست‌نخورده می‌ماند', '7' === (string) get_post_meta( $pid, 'number-viewc', true ) );

	// AC-03: non-numeric primary is safe.
	update_post_meta( $pid, 'number2-viewc', 'abc' );
	es_check( 'AC-03: مقدار غیرعددی امن خوانده می‌شود', 0 === ViewCounter::get( $pid ) );
	ViewCounter::increment( $pid );
	es_check( 'AC-03: افزایش روی مقدار غیرعددی امن است', 1 === ViewCounter::get( $pid ) );

	// Seeding from fallback on first increment (fresh post).
	$p2 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه تازه' ) );
	update_post_meta( $p2, 'number-viewc', 10 );
	ViewCounter::increment( $p2 );
	ViewCounter::increment( $p2 );
	es_check( 'SC-02: seed از fallback + دو افزایش', 12 === ViewCounter::get( $p2 ) );
	es_check( 'AC-02: fallback پست تازه دست‌نخورده', '10' === (string) get_post_meta( $p2, 'number-viewc', true ) );

	// Fresh post with no counters starts at 0 and increments to 1.
	$p3 = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه صفر' ) );
	es_check( 'شمارنده خالی صفر است', 0 === ViewCounter::get( $p3 ) );
	ViewCounter::increment( $p3 );
	es_check( 'اولین افزایش یک می‌شود', 1 === ViewCounter::get( $p3 ) );

	// The counter is hooked to the front-end wp action.
	es_check( 'شمارنده به اکشن wp متصل است', false !== has_action( 'wp', array( ViewCounter::class, 'maybe_count' ) ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $p2, true );
	wp_delete_post( $p3, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
