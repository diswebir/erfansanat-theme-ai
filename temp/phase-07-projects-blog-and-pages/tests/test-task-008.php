<?php
/**
 * P07-T008 — آرشیو شهر (taxonomy-city.php)
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T008', 'آرشیو شهر' );

es_check( 'target: taxonomy-city.php', es_file( 'taxonomy-city.php', ES_THEME ) );
es_check( 'AC-01: از shell مشترک استفاده می‌کند', es_contains( 'taxonomy-city.php', "get_template_part( 'template-parts/archive/project-archive-shell'" ) );
es_check( 'AC-01: tax_query شهر بدون اسلاگ انگلیسی هاردکد', es_contains( 'taxonomy-city.php', "'taxonomy'         => 'city'" ) && es_contains( 'taxonomy-city.php', 'get_queried_object()' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// Persian slug — proves no hardcoded English slug is required (AC-01).
	$city_term  = es_insert_term( 'اصفهان', 'city', array( 'slug' => 'اصفهان-' . uniqid() ) )['term_id'];
	$empty_term = es_insert_term( 'شهر خالی', 'city', array( 'slug' => 'empty-city-' . uniqid() ) )['term_id'];
	wp_update_term( $city_term, 'city', array( 'description' => 'پروژه‌های نورپردازی عرفان صنعت در اصفهان' ) );

	$p1 = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه اصفهان یک', 'post_name' => 'esf1-' . uniqid() ) );
	$p2 = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه اصفهان دو', 'post_name' => 'esf2-' . uniqid() ) );
	wp_set_object_terms( $p1, array( (int) $city_term ), 'city' );
	wp_set_object_terms( $p2, array( (int) $city_term ), 'city' );

	$render_city = static function ( $term_id ) {
		$q = new WP_Query( array( 'post_type' => 'projects', 'tax_query' => array( array( 'taxonomy' => 'city', 'field' => 'term_id', 'terms' => (int) $term_id ) ) ) );
		$GLOBALS['wp_query']     = $q;
		$GLOBALS['wp_the_query'] = $q;
		ob_start();
		include ES_THEME . '/taxonomy-city.php';
		return (string) ob_get_clean();
	};

	$html = $render_city( $city_term );
	$gs   = strpos( $html, 'data-project-grid="true"' );
	$ge   = strpos( $html, 'data-project-empty="true"' );
	$grid = ( false !== $gs && false !== $ge ) ? substr( $html, $gs, $ge - $gs ) : '';

	es_check( 'AC-01: ترم فارسی اصفهان resolve شد', get_queried_object() instanceof WP_Term );
	es_check( 'SC-01: شهر اصفهان رندر شد', false !== strpos( $html, 'id="archive"' ) && false !== strpos( $html, 'اصفهان' ) );
	es_check( 'AC-02: توضیح ترم نمایش داده شد', false !== strpos( $html, 'data-term-description="true"' ) && false !== strpos( $html, 'پروژه‌های نورپردازی عرفان صنعت در اصفهان' ) );
	es_check( 'AC-01: فیلتر اولیه شهر = نام ترم', false !== strpos( $html, 'data-initial-location="اصفهان"' ) );
	es_check( 'SC-01: گرید ۲ کارت استاندارد دارد', 2 === substr_count( $grid, 'data-project-item="true"' ) );

	// SC-02: a city with no projects renders the safe empty state.
	$empty_html = $render_city( $empty_term );
	$egs        = strpos( $empty_html, 'data-project-grid="true"' );
	$ege        = strpos( $empty_html, 'data-project-empty="true"' );
	$empty_grid = ( false !== $egs && false !== $ege ) ? substr( $empty_html, $egs, $ege - $egs ) : '';
	es_check( 'AC-03/SC-02: شهر خالی حالت خالی می‌دهد', false !== strpos( $empty_html, 'data-project-empty="true" class="block' ) && 0 === substr_count( $empty_grid, 'data-project-item="true"' ) );
	es_check( 'AC-03: شهر خالی fatal ندارد', false !== strpos( $empty_html, 'id="archive"' ) );

	foreach ( array( $p1, $p2 ) as $pid ) {
		wp_delete_post( $pid, true );
	}
	wp_delete_term( $city_term, 'city' );
	wp_delete_term( $empty_term, 'city' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
