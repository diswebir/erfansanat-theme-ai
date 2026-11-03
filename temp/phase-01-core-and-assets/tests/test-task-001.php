<?php
/**
 * P01-T001 — هدر قالب و style.css (+ حداقل index.php برای اعتبار پوسته کلاسیک)
 * Atomic acceptance test.
 *
 * Self-correction: WordPress classic themes require index.php to appear in the
 * theme list and activate. This task therefore creates a minimal validity-only
 * index.php; the functional fallback template remains P01-T005.
 *
 * Run: php temp/phase-01-core-and-assets/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T001', 'هدر قالب و style.css' );

$style_path = ES_THEME . '/style.css';
$index_path = ES_THEME . '/index.php';
es_check( 'target: erfansanat/style.css', is_file( $style_path ) );
es_check( 'target: erfansanat/index.php (حداقل اعتبار پوسته)', is_file( $index_path ) );

$style = is_file( $style_path ) ? (string) file_get_contents( $style_path ) : '';

// AC-02: the file must contain only the theme header comment, no static CSS.
es_check(
	'AC-02: style.css فقط هدر قالب است و CSS ندارد',
	1 === preg_match( '/^\s*\/\*.*?\*\/\s*$/s', $style )
);

if ( es_load_wp() ) {
	wp_clean_themes_cache();
	$themes = wp_get_themes();
	$theme  = wp_get_theme( 'erfansanat' );

	// SC-01: the in-repo theme is listed by WordPress and has no validation error.
	es_check( 'SC-01: WP قالب erfansanat را در فهرست پوسته‌ها دارد', isset( $themes['erfansanat'] ) );
	es_check( 'SC-01: قالب خطای اعتبارسنجی ندارد', $theme->exists() && ! $theme->errors() );

	$name         = (string) $theme->get( 'Name' );
	$version      = (string) $theme->get( 'Version' );
	$text_domain  = (string) $theme->get( 'TextDomain' );
	$requires_php = (string) $theme->get( 'RequiresPHP' );
	$requires_wp  = (string) $theme->get( 'RequiresWP' );
	$domain_path  = (string) $theme->get( 'DomainPath' );

	es_check( 'AC-01: Theme Name معتبر', '' !== $name );
	es_check( 'AC-01: Version معتبر', 1 === preg_match( '/^\d+\.\d+\.\d+/', $version ) );
	es_check( 'AC-01/AC-03: Text Domain = erfansanat', 'erfansanat' === $text_domain );
	es_check( 'AC-01: Requires PHP >= 8.4', '' !== $requires_php && version_compare( $requires_php, '8.4', '>=' ) );
	es_check( 'AC-01: Requires WP >= 7.1', '' !== $requires_wp && version_compare( $requires_wp, '7.1', '>=' ) );
	es_check( 'AC-03: Domain Path = /languages', '/languages' === $domain_path );

	// SC-02: activate the real theme, then restore the previous QA theme.
	$previous_stylesheet      = get_option( 'stylesheet' );
	$previous_stylesheet_root = get_option( 'stylesheet_root' );
	$previous_template_root   = get_option( 'template_root' );
	switch_theme( 'erfansanat' );
	$activated = get_option( 'stylesheet' ) === 'erfansanat';
	es_check( 'SC-02: فعال‌سازی قالب بدون fatal', $activated );
	switch_theme( $previous_stylesheet );
	update_option( 'stylesheet_root', $previous_stylesheet_root );
	update_option( 'template_root', $previous_template_root );
	wp_clean_themes_cache();
} else {
	es_check( 'SC-01/SC-02: بارگذاری WordPress تست', false );
}

es_summary();
