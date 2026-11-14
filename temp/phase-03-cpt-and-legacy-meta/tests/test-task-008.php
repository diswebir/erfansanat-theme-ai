<?php
/**
 * P03-T008 — Flush و رگرسیون Permalink
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Rewrites;

es_task( 'P03-T008', 'Flush و رگرسیون Permalink' );

es_check( 'target: inc/Core/Rewrites.php', es_file( 'inc/Core/Rewrites.php' ) );

/** Does any rewrite rule map a base to the expected query fragment? */
function es_rule_for( string $base, string $query_frag ): bool {
	$rules = get_option( 'rewrite_rules' );
	if ( ! is_array( $rules ) ) {
		return false;
	}
	foreach ( $rules as $pattern => $query ) {
		if ( 0 === strpos( $pattern, $base ) && false !== strpos( $query, $query_frag ) ) {
			return true;
		}
	}
	return false;
}

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// AC-01: flush is NOT hooked to run on every front-end request.
	es_check( 'AC-01: flush روی init هر درخواست نیست', false === has_action( 'init', array( Rewrites::class, 'flush' ) ) );
	es_check( 'AC-01: maybe_flush روی admin_init است', false !== has_action( 'admin_init', array( Rewrites::class, 'maybe_flush' ) ) );
	es_check( 'AC-01: flush روی after_switch_theme است', false !== has_action( 'after_switch_theme', array( Rewrites::class, 'flush' ) ) );

	// AC-01: when the stored version matches, maybe_flush is a no-op.
	Rewrites::flush();
	update_option( 'es_rewrite_version', ES_VERSION );
	$rules_before = get_option( 'rewrite_rules' );
	Rewrites::maybe_flush();
	es_check( 'AC-01: نسخه برابر → flush نمی‌شود', get_option( 'rewrite_rules' ) === $rules_before );

	// AC-02: rules for projects / city / project_type are generated.
	es_check( 'AC-02: قانون projects تولید شد', es_rule_for( 'projects', 'post_type=projects' ) );
	es_check( 'AC-02: قانون city تولید شد', es_rule_for( 'city', 'city=' ) );
	es_check( 'AC-02: قانون project_type تولید شد', es_rule_for( 'project_type', 'project_type=' ) );

	// product_cat rule appears when the taxonomy is registered (Woo stub).
	register_taxonomy( 'product_cat', 'product', array( 'hierarchical' => true, 'rewrite' => array( 'slug' => 'product_cat', 'with_front' => true, 'hierarchical' => true ) ) );
	Rewrites::flush();
	es_check( 'AC-02: قانون product_cat تولید شد', es_rule_for( 'product_cat', 'product_cat=' ) );
	unregister_taxonomy( 'product_cat' );
	Rewrites::flush();

	// SC-01: single project & service URLs resolve to the right post (no conflict).
	$proj = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه رگرسیون', 'post_name' => 'regression-proj-t008' ) );
	$serv = wp_insert_post( array( 'post_type' => 'services', 'post_status' => 'publish', 'post_title' => 'خدمت رگرسیون', 'post_name' => 'regression-serv-t008' ) );
	Rewrites::flush();
	es_check( 'SC-01: URL پروژه به خود پروژه resolve می‌شود', url_to_postid( get_permalink( $proj ) ) === $proj );
	es_check( 'SC-01: URL خدمت به خود خدمت resolve می‌شود', url_to_postid( get_permalink( $serv ) ) === $serv );
	es_check( 'AC-03: تداخل resolve بین CPTها نیست', url_to_postid( get_permalink( $proj ) ) !== url_to_postid( get_permalink( $serv ) ) );

	// SC-02: reactivation rebuilds the rules from scratch.
	delete_option( 'rewrite_rules' );
	es_check( 'SC-02: قوانین پاک شدند', empty( get_option( 'rewrite_rules' ) ) );
	Rewrites::flush(); // after_switch_theme callback.
	es_check( 'SC-02: بازفعال‌سازی قوانین را بازسازی می‌کند', es_rule_for( 'projects', 'post_type=projects' ) );

	wp_delete_post( $proj, true );
	wp_delete_post( $serv, true );
	Rewrites::flush();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
