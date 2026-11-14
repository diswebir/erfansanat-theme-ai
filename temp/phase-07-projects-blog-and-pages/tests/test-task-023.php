<?php
/**
 * P07-T023 — FAQ page: group/question/answer repeater, accordion + search,
 * empty FAQ builds no section.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-023.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Store;

es_task( 'P07-T023', 'برگه سوالات متداول' );
es_check( 'target: page-templates/faq.php', es_file( 'page-templates/faq.php' ) );
es_check( 'AC-02: قلاب‌های آکاردئون در قالب', es_contains( 'page-templates/faq.php', 'data-faq-list="true"' ) && es_contains( 'page-templates/faq.php', 'data-faq-toggle' ) && es_contains( 'page-templates/faq.php', 'data-faq-body' ) && es_contains( 'page-templates/faq.php', 'data-faq-icon' ) );
es_check( 'AC-02: جستجوی FAQ در قالب', es_contains( 'page-templates/faq.php', 'data-faq-search' ) && es_contains( 'page-templates/faq.php', 'data-faq-item' ) );
es_check( 'AC-02: اسکریپت جستجوی FAQ موجود', es_file( 'assets/js/faq-search.js' ) );
es_check( 'AC-03: گارد سکشن خالی در قالب', es_contains( 'page-templates/faq.php', 'if ( $es_items )' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	$fields = Schema::fields();
	es_check( 'AC-01: گروه/سوال/جواب از Repeater می‌آیند', isset( $fields['faq_items'] ) && 'repeater' === $fields['faq_items']['type'] && isset( $fields['faq_items']['subfields']['group'], $fields['faq_items']['subfields']['question'], $fields['faq_items']['subfields']['answer'] ) );

	$page_id = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'سوالات متداول', 'post_name' => 'faq-023-' . uniqid() ) );
	update_post_meta( $page_id, '_wp_page_template', 'page-templates/faq.php' );
	$render = static function ( $post_id ) {
		$q = new WP_Query( array( 'page_id' => $post_id, 'post_type' => 'page' ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
		ob_start();
		include ES_THEME . '/page-templates/faq.php';
		return ob_get_clean();
	};

	// Default render: reference questions, accordion present, first item open.
	Store::delete( 'faq_items' );
	$html = $render( $page_id );
	es_check( 'مرجع: سوالات پیش‌فرض رندر می‌شوند', false !== strpos( $html, 'سوالات متداول' ) && 6 === substr_count( $html, 'data-faq-toggle=' ) );
	es_check( 'SC-01: آیتم اول باز و بقیه بسته‌اند', 1 === substr_count( $html, 'data-faq-body="0" class="block' ) && 5 === substr_count( $html, 'class="hidden border-t border-slate-100' ) );
	es_check( 'AC-02: فیلد جستجو رندر می‌شود', false !== strpos( $html, 'data-faq-search="true"' ) );

	// AC-01 + SC-02: custom repeater items render with searchable text.
	Store::set( 'faq_items', array(
		array( 'group' => 'گروه آزمایشی', 'question' => 'سوال آزمایشی درباره گارانتی چیست؟', 'answer' => 'پاسخ آزمایشی گارانتی بیست و چهار ماهه است.' ),
		array( 'group' => 'گروه دوم', 'question' => 'سوال دوم درباره نصب چگونه است؟', 'answer' => 'پاسخ دوم درباره نصب و اجرا.' ),
	) );
	$edited = $render( $page_id );
	es_check( 'AC-01: سوال/جواب سفارشی رندر می‌شود', false !== strpos( $edited, 'سوال آزمایشی درباره گارانتی چیست؟' ) && false !== strpos( $edited, 'پاسخ آزمایشی گارانتی بیست و چهار ماهه است.' ) && 2 === substr_count( $edited, 'data-faq-toggle=' ) );
	es_check( 'SC-02: متن قابل جستجو روی آیتم‌ها هست', false !== strpos( $edited, 'data-faq-text=' ) && false !== strpos( $edited, 'data-faq-empty="true"' ) );
	es_check( 'AC-01: گروه‌ها در نوار کناری می‌آیند', false !== strpos( $edited, 'گروه آزمایشی' ) && false !== strpos( $edited, 'گروه دوم' ) );

	// AC-03: explicitly empty FAQ builds no accordion section.
	Store::set( 'faq_items', array() );
	$empty = $render( $page_id );
	es_check( 'AC-03: FAQ خالی سکشن نمی‌سازد', false === strpos( $empty, 'data-faq-section="true"' ) && false === strpos( $empty, 'data-faq-list="true"' ) );

	Store::delete( 'faq_items' );
	wp_reset_postdata();
	wp_delete_post( $page_id, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
