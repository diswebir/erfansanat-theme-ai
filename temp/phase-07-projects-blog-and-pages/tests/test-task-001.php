<?php
/**
 * P07-T001 — Shell تک‌پروژه (single-projects.php)
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-001.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T001', 'Shell تک‌پروژه' );

es_check( 'target: single-projects.php', es_file( 'single-projects.php', ES_THEME ) );
es_check( 'AC-01: id=project-details و used-products', es_contains( 'single-projects.php', 'id="project-details"' ) && es_contains( 'single-projects.php', 'id="used-products"' ) );
es_check( 'AC-02: هوک‌های محتوا/کامنت و سکشن‌ها', es_contains( 'single-projects.php', 'the_content' ) && es_contains( 'single-projects.php', 'comments_template' ) && es_contains( 'single-projects.php', "do_action( 'es_project_gallery'" ) && es_contains( 'single-projects.php', "do_action( 'es_project_related'" ) );
es_check( 'AC-03: گارد پروژه ناموجود', es_contains( 'single-projects.php', "'projects' !== \$es_post->post_type" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$pid = (int) wp_insert_post( array(
		'post_type'    => 'projects',
		'post_status'  => 'publish',
		'post_title'   => 'نورپردازی میدان آزمایشی',
		'post_name'    => 'test-project-' . uniqid(),
		'post_content' => '<p>شرح کامل پروژه نورپردازی میدان آزمایشی.</p>',
		'post_excerpt' => 'شاهکاری از هنر و فناوری نورپردازی شهری.',
	) );
	$type = es_insert_term( 'نورپردازی میدان', 'project_type', array( 'slug' => 'square-lighting-t001' ) );
	$city = es_insert_term( 'اصفهان', 'city', array( 'slug' => 'isfahan-t001' ) );
	wp_set_object_terms( $pid, array( (int) $type['term_id'] ), 'project_type' );
	wp_set_object_terms( $pid, array( (int) $city['term_id'] ), 'city' );

	// SC-01: render a real project.
	$GLOBALS['wp_query'] = new WP_Query( array( 'post_type' => 'projects', 'p' => $pid ) );
	$GLOBALS['post']     = get_post( $pid );
	setup_postdata( $GLOBALS['post'] );

	ob_start();
	include ES_THEME . '/single-projects.php';
	$html = (string) ob_get_clean();

	es_check( 'SC-01: پروژه واقعی رندر شد', false !== strpos( $html, 'data-project-single="true"' ) && false !== strpos( $html, 'نورپردازی میدان آزمایشی' ) );
	es_check( 'SC-01: محتوای پروژه چاپ شد', false !== strpos( $html, 'شرح کامل پروژه نورپردازی میدان آزمایشی' ) );
	es_check( 'SC-02: hero و breadcrumb و badge', false !== strpos( $html, 'نورپردازی میدان · اصفهان' ) && false !== strpos( $html, 'پروژه‌ها' ) && false !== strpos( $html, 'مسیر صفحه' ) );
	es_check( 'SC-02: سکشن درباره', false !== strpos( $html, 'درباره نورپردازی میدان آزمایشی' ) );
	es_check( 'SC-02: سکشن شناسنامه (id=project-details)', false !== strpos( $html, 'id="project-details"' ) && false !== strpos( $html, 'شناسنامه کامل اجرایی' ) );
	es_check( 'SC-02: سکشن فرآیند', false !== strpos( $html, 'فرآیند مهندسی، تولید و اجرای پروژه' ) );
	es_check( 'SC-02: سکشن محصولات (id=used-products)', false !== strpos( $html, 'id="used-products"' ) && false !== strpos( $html, 'محصولات و تجهیزات استفاده‌شده' ) );
	es_check( 'SC-02: سکشن پروژه‌های مرتبط', false !== strpos( $html, 'سایر پروژه‌های شاخص عرفان صنعت' ) );

	// AC-02: the es_project_* actions fire during render.
	$fired = array();
	foreach ( array( 'es_project_gallery', 'es_project_details', 'es_project_used_products', 'es_project_related' ) as $es_hook ) {
		add_action( $es_hook, static function () use ( &$fired, $es_hook ) { $fired[] = $es_hook; } );
	}
	$GLOBALS['wp_query'] = new WP_Query( array( 'post_type' => 'projects', 'p' => $pid ) );
	$GLOBALS['post']     = get_post( $pid );
	setup_postdata( $GLOBALS['post'] );
	ob_start();
	include ES_THEME . '/single-projects.php';
	ob_get_clean();
	es_check( 'AC-02: هر چهار هوک سکشن اجرا شدند', count( array_unique( $fired ) ) === 4 );

	// AC-03: a non-project post renders the graceful notice (no fatal).
	$page = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'برگه', 'post_name' => 'not-a-project-' . uniqid() ) );
	$GLOBALS['post'] = get_post( $page );
	ob_start();
	include ES_THEME . '/single-projects.php';
	$bad_html = (string) ob_get_clean();
	es_check( 'AC-03: پروژه ناموجود fatal نمی‌دهد', false !== strpos( $bad_html, 'پروژه مورد نظر یافت نشد' ) );

	wp_reset_postdata();
	wp_delete_post( $pid, true );
	wp_delete_post( $page, true );
	wp_delete_term( (int) $type['term_id'], 'project_type' );
	wp_delete_term( (int) $city['term_id'], 'city' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
