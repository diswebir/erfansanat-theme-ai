<?php
/**
 * P03-T010 — ریدایرکت project و aliasهای پروژه
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-010.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\LegacyRedirects;

es_task( 'P03-T010', 'ریدایرکت project و aliasهای پروژه' );

es_check( 'target: inc/Core/LegacyRedirects.php', es_file( 'inc/Core/LegacyRedirects.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$archive = get_post_type_archive_link( 'projects' );

	// AC-01: the project page → /projects/ archive.
	es_check( 'AC-01: /project/ → آرشیو projects', $archive === LegacyRedirects::resolve( '/project/' ) );
	es_check( 'AC-01: project.html → آرشیو projects', $archive === LegacyRedirects::resolve( '/project.html' ) );

	// AC-02: projects/city-* → city/* and projects/type-* → project_type/*.
	es_check( 'AC-02: city-esfahan → /city/esfahan/', home_url( '/city/esfahan/' ) === LegacyRedirects::resolve( '/projects/city-esfahan/' ) );
	es_check( 'AC-02: type-light-tunnel → /project_type/light-tunnel/', home_url( '/project_type/light-tunnel/' ) === LegacyRedirects::resolve( '/projects/type-light-tunnel/' ) );
	es_check( 'AC-02: type-light-element → luminous-elements', home_url( '/project_type/luminous-elements/' ) === LegacyRedirects::resolve( '/projects/type-light-element/' ) );

	// A real single project is NOT redirected (canonical).
	es_check( 'AC-02: تک‌پروژه canonical ریدایرکت نمی‌شود', null === LegacyRedirects::resolve( '/projects/my-real-project/' ) );
	es_check( 'SC-03: آرشیو projects ریدایرکت نمی‌شود', null === LegacyRedirects::resolve( '/projects/' ) );

	// AC-03 + SC-02: Persian destination slug is preserved (live config via filter).
	$cb = function ( $map ) { $map['city-esfahan'] = 'اصفهان'; return $map; };
	add_filter( 'es_legacy_redirects_cities', $cb );
	$persian = LegacyRedirects::resolve( '/projects/city-esfahan/' );
	es_check( 'SC-02: مقصد شهر فارسی درست است', home_url( '/city/اصفهان/' ) === $persian );
	es_check( 'AC-03: اسلاگ فارسی مقصد حفظ می‌شود', false !== strpos( (string) $persian, 'اصفهان' ) );
	remove_filter( 'es_legacy_redirects_cities', $cb );

	// SC-01: handle() issues a 301 from /project/ to /projects/.
	$_SERVER['REQUEST_URI']  = '/project/';
	$_SERVER['QUERY_STRING'] = '';
	$got = null;
	$rcb = function ( $location, $status ) use ( &$got ) {
		$got = array( 'location' => $location, 'status' => $status );
		throw new RuntimeException( 'redirect' );
	};
	add_filter( 'wp_redirect', $rcb, 10, 2 );
	try {
		LegacyRedirects::handle();
	} catch ( RuntimeException $e ) {
		// Expected.
	}
	remove_filter( 'wp_redirect', $rcb );
	es_check( 'SC-01: /project/ یک 301 می‌دهد', is_array( $got ) && 301 === $got['status'] );
	es_check( 'SC-01: مقصد /projects/ است', is_array( $got ) && $archive === $got['location'] );

	unset( $_SERVER['REQUEST_URI'], $_SERVER['QUERY_STRING'] );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
