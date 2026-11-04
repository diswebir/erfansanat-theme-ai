<?php
/**
 * P06-T019 — بخش FAQ/توضیحات دسته
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-019.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T019', 'FAQ/توضیحات دسته' );

es_check( 'target: category-faq.php', es_file( 'template-parts/archive/category-faq.php' ) );
es_check( 'AC-02: wpautop + kses برای توضیحات', es_contains( 'template-parts/archive/category-faq.php', 'wp_kses_post( wpautop(' ) );
es_check( 'AC-03: نبود FAQ/توضیحات → return', es_contains( 'template-parts/archive/category-faq.php', "'' === \$es_description && ! \$es_faq" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$render = static function ( $term ) {
		ob_start();
		get_template_part( 'template-parts/archive/category', 'faq', array( 'term' => $term ) );
		return (string) ob_get_clean();
	};
	$mkterm = static function ( $name, $slug, $desc = '' ) {
		$t = es_insert_term( $name, 'product_cat', array( 'slug' => $slug, 'description' => $desc ) );
		return get_term( (int) $t['term_id'], 'product_cat' );
	};

	// SC-01: with description + FAQ (stored order).
	$term = $mkterm( 'ریسه بلوطی', 'catfaq-full', "پاراگراف اول توضیحات.\n\nپاراگراف دوم توضیحات." );
	update_term_meta( $term->term_id, 'faq', json_encode( array(
		'item-0' => array( 'name_question' => 'سوال اول', 'answer_question' => 'پاسخ <strong>اول</strong>' ),
		'item-1' => array( 'name_question' => 'سوال دوم', 'answer_question' => 'پاسخ دوم' ),
		'item-2' => array( 'name_question' => 'سوال سوم', 'answer_question' => 'پاسخ سوم' ),
	), JSON_UNESCAPED_UNICODE ) );
	// Re-fetch so description is current.
	$term = get_term( $term->term_id, 'product_cat' );
	$html = $render( $term );

	es_check( 'SC-01: سکشن با FAQ رندر می‌شود', false !== strpos( $html, 'data-category-faq' ) && false !== strpos( $html, 'data-faq-list="true"' ) );
	$p1 = strpos( $html, 'سوال اول' );
	$p2 = strpos( $html, 'سوال دوم' );
	$p3 = strpos( $html, 'سوال سوم' );
	es_check( 'AC-01: ترتیب ذخیره‌شده FAQ حفظ می‌شود', false !== $p1 && false !== $p2 && false !== $p3 && $p1 < $p2 && $p2 < $p3 );
	es_check( 'AC-02: توضیحات wpautop (پاراگراف‌بندی)', false !== strpos( $html, 'data-category-description' ) && substr_count( $html, '<p>' ) >= 2 );
	es_check( 'AC-02: HTML مجاز پاسخ حفظ می‌شود', false !== strpos( $html, '<strong>اول</strong>' ) );
	es_check( 'AC-01: اولین سوال باز است', false !== strpos( $html, 'data-faq-body="0" class="block' ) && false !== strpos( $html, 'data-faq-body="1" class="hidden' ) );

	// Description only (no FAQ).
	$term_desc = $mkterm( 'فقط توضیح', 'catfaq-desc', 'توضیح تنها.' );
	$html_desc = $render( $term_desc );
	es_check( 'SC-01: فقط توضیحات → سکشن بدون FAQ', false !== strpos( $html_desc, 'data-category-description' ) && false === strpos( $html_desc, 'data-faq-list' ) );

	// FAQ only (no description).
	$term_faq = $mkterm( 'فقط FAQ', 'catfaq-onlyfaq', '' );
	update_term_meta( $term_faq->term_id, 'faq', json_encode( array( 'item-0' => array( 'name_question' => 'سوال تنها', 'answer_question' => 'پاسخ تنها' ) ), JSON_UNESCAPED_UNICODE ) );
	$html_faq = $render( $term_faq );
	es_check( 'SC-01: فقط FAQ → سکشن بدون توضیحات', false !== strpos( $html_faq, 'data-faq-list' ) && false === strpos( $html_faq, 'data-category-description' ) );

	// AC-03: neither → no section.
	$term_empty = $mkterm( 'خالی', 'catfaq-empty', '' );
	es_check( 'AC-03: بدون FAQ/توضیحات → سکشن نمی‌سازد', '' === trim( $render( $term_empty ) ) );

	// XSS escaped in description + answer.
	$term_xss = $mkterm( 'XSS', 'catfaq-xss', '<script>alert(1)</script>متن' );
	update_term_meta( $term_xss->term_id, 'faq', json_encode( array( 'item-0' => array( 'name_question' => '<img src=x onerror=alert(1)>', 'answer_question' => '<script>bad()</script>خوب' ) ), JSON_UNESCAPED_UNICODE ) );
	$html_xss = $render( $term_xss );
	es_check( 'AC-02: اسکریپت توضیحات/پاسخ حذف می‌شود', false === strpos( $html_xss, '<script>alert(1)</script>' ) && false === strpos( $html_xss, '<script>bad()</script>' ) && false === strpos( $html_xss, '<img src=x onerror=alert(1)>' ) );

	foreach ( array( $term, $term_desc, $term_faq, $term_empty, $term_xss ) as $t ) {
		wp_delete_term( $t->term_id, 'product_cat' );
	}
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
