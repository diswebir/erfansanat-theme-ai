<?php
/**
 * P07-T022 — licenses & honors: media repeater, lightbox/alt, order preserved.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-022.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Store;
use ErfanSanat\Theme\Options\Tabs\PagesTab;

es_task( 'P07-T022', 'برگه مجوزها و افتخارات' );
es_check( 'target: page-templates/licenses.php', es_file( 'page-templates/licenses.php' ) );

es_check( 'AC-02: قلاب‌های لایت‌باکس در قالب', es_contains( 'page-templates/licenses.php', 'data-cert-lightbox' ) && es_contains( 'page-templates/licenses.php', 'data-cert-open' ) && es_contains( 'page-templates/licenses.php', 'data-cert-img' ) );
es_check( 'AC-02: اسکریپت لایت‌باکس موجود', es_file( 'assets/js/certificates.js' ) );
es_check( 'AC-03: ترتیب آیتم‌ها حفظ می‌شود (helper)', es_contains( 'inc/Options/Tabs/PagesTab.php', 'array_values( $items )' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$fields = Schema::fields();
	es_check( 'AC-01: گواهینامه‌ها Repeater رسانه‌ای هستند', isset( $fields['licenses_items'] ) && 'repeater' === $fields['licenses_items']['type'] && isset( $fields['licenses_items']['subfields']['image'] ) && 'media' === $fields['licenses_items']['subfields']['image']['type'] );
	$page_id = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'مجوزها و افتخارات', 'post_name' => 'licenses-022-' . uniqid() ) );
	update_post_meta( $page_id, '_wp_page_template', 'page-templates/licenses.php' );
	$render = static function ( $post_id ) {
		$q = new WP_Query( array( 'page_id' => $post_id, 'post_type' => 'page' ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
		ob_start();
		include ES_THEME . '/page-templates/licenses.php';
		return ob_get_clean();
	};

	// Default render matches the reference (6 cards, ISO present, no lightbox without images).
	Store::delete( 'licenses_items' );
	$html = $render( $page_id );
	es_check( 'مرجع: عنوان/کارت‌های پیش‌فرض رندر می‌شوند', false !== strpos( $html, 'مجوزها و افتخارات' ) && false !== strpos( $html, 'ISO 9001:2015' ) && 6 === substr_count( $html, '<article' ) );
	es_check( 'بدون تصویر، لایت‌باکس رندر نمی‌شود', false === strpos( $html, 'data-cert-lightbox' ) );

	// SC-01 + AC-03: add certificates in a specific order, with an image + alt.
	$att = (int) wp_insert_attachment( array( 'post_title' => 'cert-test', 'post_mime_type' => 'image/jpeg', 'post_status' => 'inherit' ), 'cert-test.jpg', 0 );
	update_post_meta( $att, '_wp_attached_file', 'cert-test.jpg' );
	Store::set( 'licenses_items', array(
		array( 'category' => 'الف', 'title' => 'گواهی آزمایشی الف', 'code' => 'A-1', 'description' => 'توضیح الف', 'issuer' => 'صادرکننده الف', 'year' => '۱۴۰۳', 'image' => $att, 'alt' => 'تصویر گواهی الف' ),
		array( 'category' => 'ب', 'title' => 'گواهی آزمایشی ب', 'code' => 'B-2', 'description' => 'توضیح ب', 'issuer' => 'صادرکننده ب', 'year' => '۱۴۰۲', 'image' => 0, 'alt' => '' ),
	) );
	$edited = $render( $page_id );
	es_check( 'SC-01: گواهی جدید رندر می‌شود', false !== strpos( $edited, 'گواهی آزمایشی الف' ) && 2 === substr_count( $edited, '<article' ) );
	$pos_a = strpos( $edited, 'گواهی آزمایشی الف' );
	$pos_b = strpos( $edited, 'گواهی آزمایشی ب' );
	es_check( 'AC-03: ترتیب ذخیره‌شده حفظ می‌شود', false !== $pos_a && false !== $pos_b && $pos_a < $pos_b );
	es_check( 'AC-02: تصویر + alt رندر می‌شود', false !== strpos( $edited, 'cert-test.jpg' ) && false !== strpos( $edited, 'alt="تصویر گواهی الف"' ) );
	es_check( 'SC-02: کلیک تصویر لایت‌باکس را باز می‌کند (DOM)', false !== strpos( $edited, 'data-cert-open="true"' ) && false !== strpos( $edited, 'data-cert-lightbox="true"' ) && false !== strpos( $edited, 'data-cert-src="' ) );
	es_check( 'AC-02: alt خالی به عنوان گواهی fallback می‌شود', false !== strpos( $edited, 'گواهی آزمایشی ب' ) );

	// Reorder → order follows the saved array.
	Store::set( 'licenses_items', array(
		array( 'title' => 'گواهی آزمایشی ب', 'code' => 'B-2', 'image' => 0, 'alt' => '' ),
		array( 'title' => 'گواهی آزمایشی الف', 'code' => 'A-1', 'image' => $att, 'alt' => 'تصویر گواهی الف' ),
	) );
	$reordered = $render( $page_id );
	es_check( 'AC-03: تغییر ترتیب منعکس می‌شود', strpos( $reordered, 'گواهی آزمایشی ب' ) < strpos( $reordered, 'گواهی آزمایشی الف' ) );

	Store::delete( 'licenses_items' );
	wp_reset_postdata();
	wp_delete_post( $page_id, true );
	wp_delete_attachment( $att, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
