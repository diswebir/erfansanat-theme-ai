<?php
/**
 * P07-T003 — متادیتای تک‌پروژه (template-parts/project/meta.php)
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-003.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T003', 'متادیتای تک‌پروژه' );

es_check( 'target: meta.php', es_file( 'template-parts/project/meta.php', ES_THEME ) );
es_check( 'AC-01: finish-pro-time بدون تبدیل', es_contains( 'template-parts/project/meta.php', "get_post_meta( \$es_pid, 'finish-pro-time'" ) && es_contains( 'template-parts/project/meta.php', 'data-project-finish-time' ) );
es_check( 'AC-02: لینک taxonomy شهر/نوع', es_contains( 'template-parts/project/meta.php', 'get_term_link' ) && es_contains( 'template-parts/project/meta.php', 'data-project-city-link' ) && es_contains( 'template-parts/project/meta.php', 'data-project-type-link' ) );
es_check( 'AC-03: خواندن number2-viewc', es_contains( 'template-parts/project/meta.php', 'ViewCounter::get' ) && es_contains( 'inc/Core/ViewCounter.php', "'number2-viewc'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$pid = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه متا', 'post_name' => 'meta-proj-' . uniqid() ) );
	$city = es_insert_term( 'اصفهان', 'city', array( 'slug' => 'isfahan-t003' ) );
	$type = es_insert_term( 'نورپردازی میدان', 'project_type', array( 'slug' => 'square-t003' ) );
	wp_set_object_terms( $pid, array( (int) $city['term_id'] ), 'city' );
	wp_set_object_terms( $pid, array( (int) $type['term_id'] ), 'project_type' );
	update_post_meta( $pid, 'finish-pro-time', '1404/01/01' );
	update_post_meta( $pid, 'number2-viewc', '321' );
	update_post_meta( $pid, 'karfarma', 'شهرداری اصفهان' );

	$render = static function ( $pid ) {
		ob_start();
		get_template_part( 'template-parts/project/meta', null, array( 'project_id' => $pid ) );
		return (string) ob_get_clean();
	};
	$html = $render( $pid );

	$fa = static function ( $v ) {
		$map = array();
		for ( $d = 0; $d < 10; $d++ ) { $map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d ); }
		return strtr( (string) $v, $map );
	};

	// SC-01 / AC-01: raw date verbatim.
	es_check( 'SC-01: تاریخ 1404/01/01 عیناً نمایش داده شد', false !== strpos( $html, 'data-project-finish-time>1404/01/01</span>' ) );
	es_check( 'AC-01: هیچ تبدیل اجباری تاریخ رخ نداد', false === strpos( $html, '۱۴۰۴/۰۱/۰۱' ) );

	// SC-02 / AC-02: city link is the real term archive.
	$city_link = get_term_link( (int) $city['term_id'], 'city' );
	$type_link = get_term_link( (int) $type['term_id'], 'project_type' );
	es_check( 'SC-02: شهر فارسی لینک صحیح آرشیو گرفت', false !== strpos( $html, 'data-project-city-link' ) && false !== strpos( $html, esc_url( $city_link ) ) && false !== strpos( $html, 'اصفهان' ) );
	es_check( 'AC-02: نوع پروژه لینک صحیح آرشیو گرفت', false !== strpos( $html, 'data-project-type-link' ) && false !== strpos( $html, esc_url( $type_link ) ) );

	// AC-03: views from number2-viewc.
	es_check( 'AC-03: بازدید از number2-viewc (۳۲۱)', false !== strpos( $html, 'data-project-views>' . $fa( '321' ) . '</span>' ) );

	// AC-03: legacy fallback when primary absent.
	delete_post_meta( $pid, 'number2-viewc' );
	update_post_meta( $pid, 'number-viewc', '77' );
	$html2 = $render( $pid );
	es_check( 'AC-03: fallback number-viewc خوانده شد (۷۷)', false !== strpos( $html2, 'data-project-views>' . $fa( '77' ) . '</span>' ) );

	// Optional client field renders.
	es_check( 'فیلد کارفرما رندر شد', false !== strpos( $html, 'شهرداری اصفهان' ) && false !== strpos( $html, 'کارفرمای محترم' ) );

	// Filterable rows.
	add_filter( 'es_project_meta_rows', static function ( $rows ) { $rows[] = array( 'label' => 'سفارشی', 'value' => 'مقدار' ); return $rows; } );
	$html3 = $render( $pid );
	es_check( 'AC: ردیف‌ها قابل فیلتر هستند', false !== strpos( $html3, 'سفارشی' ) );

	wp_delete_post( $pid, true );
	wp_delete_term( (int) $city['term_id'], 'city' );
	wp_delete_term( (int) $type['term_id'], 'project_type' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
