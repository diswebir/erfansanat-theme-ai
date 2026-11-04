<?php
/**
 * P07-T002 — گالری تک‌پروژه (template-parts/project/gallery.php)
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-002.php
 * Lightbox behaviour: test-task-002-dom.mjs
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P07-T002', 'گالری تک‌پروژه' );

es_check( 'target: gallery.php', es_file( 'template-parts/project/gallery.php', ES_THEME ) );
es_check( 'AC-01: کلید foure-image دقیق', es_contains( 'inc/Admin/ProjectMetaboxes.php', "'foure-image'" ) && es_contains( 'template-parts/project/gallery.php', 'get_gallery' ) );
es_check( 'AC-02: ترتیب (شاخص سپس legacy)', es_contains( 'template-parts/project/gallery.php', 'get_post_thumbnail_id' ) && es_contains( 'template-parts/project/gallery.php', 'ProjectMetaboxes::get_gallery' ) );
es_check( 'AC-03: گارد تصویر خالی/حذف‌شده', es_contains( 'template-parts/project/gallery.php', "'attachment' !== \$es_attachment->post_type" ) );
es_check( 'SC-02: قرارداد لایت‌باکس', es_contains( 'template-parts/project/gallery.php', 'data-gallery-lightbox="true"' ) && es_contains( 'template-parts/project/gallery.php', 'data-gallery-thumb' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$mkatt = static function ( $file ) {
		$id = wp_insert_attachment( array(
			'post_mime_type' => 'image/jpeg',
			'post_title'     => $file,
			'post_status'    => 'inherit',
		), $file );
		return (int) $id;
	};

	$pid = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه گالری', 'post_name' => 'gallery-proj-' . uniqid() ) );
	$imgs = array();
	for ( $i = 0; $i < 6; $i++ ) {
		$imgs[ $i ] = $mkatt( 'gallery-test/img-' . $i . '-' . uniqid() . '.jpg' );
	}
	set_post_thumbnail( $pid, $imgs[0] );
	update_post_meta( $pid, 'second-image', $imgs[1] );
	update_post_meta( $pid, 'three-image', $imgs[2] );
	update_post_meta( $pid, 'foure-image', $imgs[3] );
	update_post_meta( $pid, 'five-image', $imgs[4] );
	update_post_meta( $pid, 'six-image', $imgs[5] );

	$render = static function ( $pid ) {
		ob_start();
		get_template_part( 'template-parts/project/gallery', null, array( 'project_id' => $pid ) );
		return (string) ob_get_clean();
	};

	$html = $render( $pid );

	// SC-01: six images render.
	preg_match_all( '/data-gallery-src="([^"]+)"/', $html, $m );
	$srcs = $m[1];
	es_check( 'SC-01: گالری کامل با ۶ تصویر', 6 === count( $srcs ) && false !== strpos( $html, 'data-gallery-root="true"' ) );
	es_check( 'SC-01: شش بندانگشتی', 6 === substr_count( $html, 'data-gallery-thumb=' ) );

	// AC-02: order = featured, second, three, foure, five, six.
	$expected = array();
	for ( $i = 0; $i < 6; $i++ ) {
		$expected[] = wp_get_attachment_url( $imgs[ $i ] );
	}
	es_check( 'AC-02: ترتیب تصاویر حفظ شد', $srcs === $expected );

	// AC-01: foure-image (4th) is read exactly.
	es_check( 'AC-01: foure-image در جایگاه چهارم', isset( $srcs[3] ) && $srcs[3] === wp_get_attachment_url( $imgs[3] ) );

	// AC-03: delete one attachment → it is skipped.
	wp_delete_attachment( $imgs[2], true );
	$html2 = $render( $pid );
	preg_match_all( '/data-gallery-src="([^"]+)"/', $html2, $m2 );
	es_check( 'AC-03: تصویر حذف‌شده رندر نمی‌شود', 5 === count( $m2[1] ) && ! in_array( wp_get_attachment_url( $imgs[2] ), $m2[1], true ) );

	// AC-03: empty legacy meta is skipped.
	update_post_meta( $pid, 'five-image', '' );
	$html3 = $render( $pid );
	preg_match_all( '/data-gallery-src="([^"]+)"/', $html3, $m3 );
	es_check( 'AC-03: متای خالی رندر نمی‌شود', 4 === count( $m3[1] ) );

	// AC-03: project with no images renders nothing.
	$empty_pid = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'بدون تصویر', 'post_name' => 'noimg-' . uniqid() ) );
	es_check( 'AC-03: پروژه بدون تصویر خروجی ندارد', '' === trim( $render( $empty_pid ) ) );

	// Cleanup.
	wp_delete_post( $pid, true );
	wp_delete_post( $empty_pid, true );
	foreach ( $imgs as $i => $aid ) {
		if ( $i !== 2 && get_post( $aid ) ) {
			wp_delete_attachment( $aid, true );
		}
	}
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
