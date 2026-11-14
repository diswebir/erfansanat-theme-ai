<?php
/**
 * P04-T002 — Shell هدر و head استاندارد
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-002.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T002', 'Shell هدر و head استاندارد' );

es_check( 'target: header.php', es_file( 'header.php' ) );
es_check( 'AC-02: wp_head فراخوانی می‌شود', es_contains( 'header.php', 'wp_head()' ) );
es_check( 'AC-02: body_class فراخوانی می‌شود', es_contains( 'header.php', 'body_class(' ) );
es_check( 'AC-02: wp_body_open فراخوانی می‌شود', es_contains( 'header.php', 'wp_body_open()' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	ob_start();
	get_header();
	$html = (string) ob_get_clean();

	// AC-01: data-navbar-header + fixed classes preserved.
	es_check( 'AC-01: data-navbar-header="true" حاضر است', false !== strpos( $html, 'data-navbar-header="true"' ) );
	es_check( 'AC-01: کلاس‌های fixed حفظ شده', false !== strpos( $html, 'fixed inset-x-0 top-0 z-50' ) );

	// SC-01: exact header opening tag parity with the mirror.
	$ref    = (string) file_get_contents( ES_MIRROR . '/index.html' );
	$match  = array();
	preg_match( '/<header data-navbar-header="true" class="[^"]+">/', $ref, $match );
	es_check( 'SC-01: تگ آغازین header عیناً مطابق مرجع', ! empty( $match[0] ) && false !== strpos( $html, $match[0] ) );

	// SC-02: body_class outputs the mirror body classes.
	es_check( 'SC-02: body کلاس bg-[#f6f8f7] دارد', 1 === preg_match( '/<body[^>]*class="[^"]*bg-\[#f6f8f7\]/', $html ) );
	es_check( 'SC-02: body کلاس antialiased دارد', 1 === preg_match( '/<body[^>]*class="[^"]*antialiased/', $html ) );

	// Head/lang parity.
	es_check( 'html dir=rtl و lang=fa', false !== strpos( $html, '<html lang="fa" dir="rtl">' ) );
	es_check( 'charset در head', false !== strpos( $html, '<meta charset=' ) );
	es_check( 'wp_head استایل styles.css را تزریق کرد', false !== strpos( $html, 'styles.css' ) );

	// Background decoration parity.
	es_check( 'لایه تزئینی پس‌زمینه حاضر است', false !== strpos( $html, 'light-dot-grid absolute inset-0 opacity-60' ) );
	es_check( 'پوشش relative z-10 باز شده', false !== strpos( $html, '<div class="relative z-10">' ) );

	// AC-03: header does not close main or inject stray closing tags.
	es_check( 'AC-03: هدر تگ main نمی‌سازد/نمی‌بندد', false === strpos( $html, '<main' ) && false === strpos( $html, '</main>' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
