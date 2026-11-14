<?php
/**
 * P01-T005 — تکمیل Fallback index.php
 * Run: php temp/phase-01-core-and-assets/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P01-T005', 'تکمیل Fallback index.php' );

es_check( 'target: index.php', es_file( 'index.php' ) );
es_check( 'AC-01: get_header', es_contains( 'index.php', 'get_header()' ) );
es_check( 'AC-01: get_footer', es_contains( 'index.php', 'get_footer()' ) );
es_check( 'AC-01: have_posts', es_contains( 'index.php', 'have_posts()' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$post_id = wp_insert_post(
		array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_title'   => 'تست fallback پوسته',
			'post_content' => 'محتوای آزمایشی برای رندر fallback.',
		)
	);

	$render = function ( $query ) {
		global $wp_query;
		$old = $wp_query;
		$wp_query = $query;
		ob_start();
		include ES_THEME . '/index.php';
		$html = ob_get_clean();
		$wp_query = $old;
		return $html;
	};

	$html_single = $render( new WP_Query( array( 'p' => $post_id ) ) );
	es_check( 'SC-01: نوشته آزمایشی رندر می‌شود', false !== strpos( $html_single, 'تست fallback پوسته' ) );

	$html_empty = $render( new WP_Query( array( 'post__in' => array( 0 ) ) ) );
	es_check( 'SC-02: آرشیو خالی fatal نمی‌دهد', is_string( $html_empty ) );
	es_check( 'AC-02: پیام خالی escape‌شده', false !== strpos( $html_empty, 'هیچ محتوایی یافت نشد.' ) );

	wp_delete_post( $post_id, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
