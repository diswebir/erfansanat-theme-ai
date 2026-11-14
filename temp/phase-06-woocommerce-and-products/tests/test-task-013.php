<?php
/**
 * P06-T013 — سوالات متداول محصول (آکاردئون)
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-013.php
 * Behaviour (open/close with real main.js): test-task-013-dom.mjs
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T013', 'سوالات متداول محصول' );

es_check( 'target: faq.php', es_file( 'template-parts/product/faq.php' ) );
es_check( 'AC-01: کلیدهای name_question/answer_question', es_contains( 'template-parts/product/faq.php', 'name_question' ) && es_contains( 'template-parts/product/faq.php', 'answer_question' ) );
es_check( 'AC-02: هوک‌های آکاردئون مرجع', es_contains( 'template-parts/product/faq.php', 'data-faq-list="true"' ) && es_contains( 'template-parts/product/faq.php', 'data-faq-toggle' ) && es_contains( 'template-parts/product/faq.php', 'data-faq-body' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$render = static function ( $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		$product = wc_get_product( $pid );
		ob_start();
		include ES_THEME . '/template-parts/product/faq.php';
		return (string) ob_get_clean();
	};
	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'درخت نوری دلسا', 'post_name' => 'faq-prod' ) );

	es_check( 'AC-03: FAQ خالی → سکشن نمی‌سازد', '' === trim( $render( $pid ) ) );

	$faq = array(
		'item-0' => array( 'name_question' => 'شرایط گارانتی چگونه است؟', 'answer_question' => '۲۴ ماه گارانتی تعویض و <strong>۵ سال</strong> خدمات.' ),
		'item-1' => array( 'name_question' => 'امکان سفارشی‌سازی هست؟', 'answer_question' => 'بله؛ تولیدکننده مستقیم هستیم.' ),
		'item-2' => array( 'name_question' => 'سوال بی‌پاسخ', 'answer_question' => '' ),
	);
	update_post_meta( $pid, 'faq', json_encode( $faq, JSON_UNESCAPED_UNICODE ) );
	$html = $render( $pid );

	es_check( 'SC-01: سکشن FAQ رندر می‌شود', false !== strpos( $html, 'data-product-faq' ) && false !== strpos( $html, 'سوالات متداول درباره درخت نوری دلسا' ) );
	es_check( 'AC-03: آیتم با پاسخ خالی ردیف نساخت', 2 === substr_count( $html, 'data-faq-toggle=' ) );
	es_check( 'AC-01: سوال اول باز است (block)', false !== strpos( $html, 'data-faq-body="0" class="block' ) );
	es_check( 'AC-01: سوال دوم بسته است (hidden)', false !== strpos( $html, 'data-faq-body="1" class="hidden' ) );
	es_check( 'SC-02: HTML مجاز پاسخ حفظ می‌شود', false !== strpos( $html, '<strong>۵ سال</strong>' ) );

	update_post_meta( $pid, 'faq', json_encode( array( 'item-0' => array( 'name_question' => 'سوال', 'answer_question' => '<script>alert(1)</script>متن' ) ), JSON_UNESCAPED_UNICODE ) );
	$html_xss = $render( $pid );
	es_check( 'SC-02: اسکریپت پاسخ حذف می‌شود', false === strpos( $html_xss, '<script>alert(1)</script>' ) && false !== strpos( $html_xss, 'متن' ) );

	wp_delete_post( $pid, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
