<?php
/**
 * P07-T025 — recruitment: 4 editable job tabs, data-recruit-tabs, safe form.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-025.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Store;

es_task( 'P07-T025', 'برگه استخدام' );
es_check( 'target: page-templates/recruitment.php', es_file( 'page-templates/recruitment.php' ) );
es_check( 'AC-02: data-recruit-tabs حفظ شده', es_contains( 'page-templates/recruitment.php', 'data-recruit-tabs="true"' ) && es_contains( 'page-templates/recruitment.php', 'data-recruit-tab-btn' ) && es_contains( 'page-templates/recruitment.php', 'data-recruit-tab-panel' ) );
es_check( 'AC-03: فرم بدون action/method/handler', ! es_contains( 'page-templates/recruitment.php', 'admin-post.php' ) && ! es_contains( 'page-templates/recruitment.php', 'method="post"' ) && ! es_contains( 'page-templates/recruitment.php', 'admin-ajax' ) );
es_check( 'AC-03: یادداشت امن تا فاز ۸', es_contains( 'page-templates/recruitment.php', 'به‌زودی فعال می‌شود' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$fields = Schema::fields();
	es_check( 'AC-01: تب‌ها Repeater ویرایش‌پذیر', isset( $fields['recruit_tabs'] ) && 'repeater' === $fields['recruit_tabs']['type'] && isset( $fields['recruit_tabs']['subfields']['key'], $fields['recruit_tabs']['subfields']['label'], $fields['recruit_tabs']['subfields']['description'] ) );
	es_check( 'AC-01: موقعیت‌های شغلی Repeater', isset( $fields['recruit_jobs'] ) && 'repeater' === $fields['recruit_jobs']['type'] && isset( $fields['recruit_jobs']['subfields']['tab'], $fields['recruit_jobs']['subfields']['skills'] ) );

	$page_id = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'استخدام', 'post_name' => 'recruit-025-' . uniqid() ) );
	update_post_meta( $page_id, '_wp_page_template', 'page-templates/recruitment.php' );
	$render = static function ( $post_id ) {
		$q = new WP_Query( array( 'page_id' => $post_id, 'post_type' => 'page' ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
		ob_start();
		include ES_THEME . '/page-templates/recruitment.php';
		return ob_get_clean();
	};

	Store::delete( 'recruit_tabs' ); Store::delete( 'recruit_jobs' );
	$html = $render( $page_id );
	es_check( 'مرجع: ۴ تب R&D/پروژه/حسابداری/IT', 4 === substr_count( $html, 'data-recruit-tab-btn=' ) && false !== strpos( $html, 'data-recruit-tab-btn="rnd"' ) && false !== strpos( $html, 'data-recruit-tab-btn="it"' ) );
	es_check( 'مرجع: ۴ پنل + ۷ موقعیت شغلی', 4 === substr_count( $html, 'data-recruit-tab-panel=' ) && 7 === substr_count( $html, '<article' ) );
	es_check( 'AC-02: پلن‌ها data-recruit-panel هم دارند (main.js)', 4 === substr_count( $html, 'data-recruit-panel=' ) );
	es_check( 'مرجع: پنل اول باز، بقیه hidden', 1 === substr_count( $html, 'data-recruit-tab-panel="rnd" data-recruit-panel="rnd" class="block"' ) && 3 === substr_count( $html, 'class="hidden"' ) );
	es_check( 'AC-03: فرم هیچ name/action برای ارسال ندارد', ! preg_match( '/<form[^>]*(action|method)=/', $html ) && ! preg_match( '/<(input|textarea|select)[^>]*name=/', $html ) );
	es_check( 'مرجع: فرم #apply-form present', false !== strpos( $html, 'id="apply-form"' ) && false !== strpos( $html, 'href="#apply-form"' ) );

	// SC-02: job description from settings.
	Store::set( 'recruit_tabs', array(
		array( 'key' => 'rnd', 'label' => 'واحد آزمایشی نوآوری', 'badge' => 'نشان آزمایشی', 'title' => 'عنوان واحد آزمایشی', 'description' => 'شرح واحد آزمایشی از تنظیمات آمده است.' ),
	) );
	Store::set( 'recruit_jobs', array(
		array( 'tab' => 'rnd', 'title' => 'موقعیت شغلی آزمایشی', 'type' => 'تمام‌وقت', 'experience' => '۲ سال', 'location' => 'اصفهان', 'skills' => "مهارت آزمایشی یک\nمهارت آزمایشی دو" ),
	) );
	$edited = $render( $page_id );
	es_check( 'SC-02: شرح شغل/واحد از تنظیمات می‌آید', false !== strpos( $edited, 'شرح واحد آزمایشی از تنظیمات آمده است.' ) && false !== strpos( $edited, 'موقعیت شغلی آزمایشی' ) && false !== strpos( $edited, 'مهارت آزمایشی یک' ) );
	es_check( 'AC-01: تب/پنل سفارشی جایگزین پیش‌فرض‌ها', 1 === substr_count( $edited, 'data-recruit-tab-btn=' ) && false !== strpos( $edited, 'واحد آزمایشی نوآوری' ) );

	Store::delete( 'recruit_tabs' ); Store::delete( 'recruit_jobs' );
	wp_reset_postdata();
	wp_delete_post( $page_id, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
