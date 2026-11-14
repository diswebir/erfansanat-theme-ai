<?php
/**
 * P07-T020 — about page template: editable intro/ISO/principles/history sections.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-020.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Schema;
use ErfanSanat\Theme\Sections\SectionManager;
use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P07-T020', 'برگه درباره ما' );
es_check( 'target: page-templates/about.php', es_file( 'page-templates/about.php' ) );
es_check( 'AC-01: از SectionManager/Renderer استفاده می‌کند', es_contains( 'page-templates/about.php', 'Renderer::render' ) && es_contains( 'page-templates/about.php', "'about'" ) );
es_check( 'AC-03: CTA تماس گلوبال (بدون تکرار)', ! es_contains( 'page-templates/about.php', 'id="contact"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$sections = Schema::sections( 'about' );
	es_check( 'AC-01: سکشن‌های معرفی/ISO/اصول/تاریخچه در اسکیما', isset( $sections['intro'], $sections['iso'], $sections['principles'], $sections['history'] ) );

	$page_id = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'درباره ما', 'post_name' => 'about-020-' . uniqid() ) );
	update_post_meta( $page_id, '_wp_page_template', 'page-templates/about.php' );

	$render = static function ( $post_id ) {
		Renderer::invalidate( $post_id );
		$q = new WP_Query( array( 'page_id' => $post_id, 'post_type' => 'page' ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
		ob_start();
		include ES_THEME . '/page-templates/about.php';
		return ob_get_clean();
	};
	$html = $render( $page_id );
	es_check( 'AC-02: عنوان/سکشن‌های مرجع رندر می‌شوند', false !== strpos( $html, 'درباره شرکت' ) && false !== strpos( $html, 'ISO 9001:2015' ) && false !== strpos( $html, 'اصول بنیادین' ) );
	es_check( 'هر سکشن data-es-section-type دارد', false !== strpos( $html, 'data-es-section-type="hero"' ) && false !== strpos( $html, 'data-es-section-type="iso"' ) && false !== strpos( $html, 'data-es-section-type="principles"' ) );

	// SC-01: editing the intro text is reflected.
	$cfg = SectionManager::get_sections( $page_id, 'about' );
	foreach ( $cfg as &$sec ) {
		if ( 'intro' === $sec['type'] ) { $sec['fields']['content'] = '<p>متن سفارشی درباره شرکت آزمایشی</p>'; }
	}
	unset( $sec );
	SectionManager::save_sections( $page_id, $cfg, 'about' );
	$edited = $render( $page_id );
	es_check( 'SC-01: ویرایش متن درباره در خروجی دیده می‌شود', false !== strpos( $edited, 'متن سفارشی درباره شرکت آزمایشی' ) );

	// SC-02: replacing the factory image.
	$att = (int) wp_insert_attachment( array( 'post_title' => 'factory-test', 'post_mime_type' => 'image/jpeg', 'post_status' => 'inherit' ), 'factory-test.jpg', 0 );
	update_post_meta( $att, '_wp_attached_file', 'factory-test.jpg' );
	$cfg = SectionManager::get_sections( $page_id, 'about' );
	foreach ( $cfg as &$sec ) {
		if ( 'hero' === $sec['type'] ) { $sec['fields']['image'] = $att; }
	}
	unset( $sec );
	SectionManager::save_sections( $page_id, $cfg, 'about' );
	$img_html = $render( $page_id );
	es_check( 'SC-02: تصویر کارخانه جایگزین‌پذیر است', false !== strpos( $img_html, 'factory-test.jpg' ) );

	// Disabled section is not rendered.
	$cfg = SectionManager::get_sections( $page_id, 'about' );
	foreach ( $cfg as &$sec ) { if ( 'iso' === $sec['type'] ) { $sec['enabled'] = false; } }
	unset( $sec );
	SectionManager::save_sections( $page_id, $cfg, 'about' );
	$no_iso = $render( $page_id );
	es_check( 'AC-01: خاموش‌کردن ISO آن را حذف می‌کند', false === strpos( $no_iso, 'data-es-section-type="iso"' ) );

	// NOTE: get_footer()/locate_template use require_once, so in a single PHP
	// process only the first full render carries the footer. Production is one
	// request per process; assert the CTA on the first render instead.
	es_check( 'AC-03: CTA تماس یک‌بار از فوتر می‌آید', 1 === substr_count( $html, 'id="contact"' ) );

	wp_reset_postdata();
	wp_delete_post( $page_id, true );
	wp_delete_attachment( $att, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
