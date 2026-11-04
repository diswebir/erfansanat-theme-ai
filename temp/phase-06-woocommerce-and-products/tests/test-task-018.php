<?php
/**
 * P06-T018 — آرشیو دسته‌بندی محصول (taxonomy-product_cat)
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T018', 'آرشیو دسته‌بندی محصول' );

es_check( 'target: taxonomy-product_cat.php', es_file( 'woocommerce/taxonomy-product_cat.php', ES_THEME ) );
es_check( 'target: category-faq.php', es_file( 'template-parts/archive/category-faq.php' ) );
es_check( 'AC-01: include_children برای زیردسته', es_contains( 'woocommerce/taxonomy-product_cat.php', "'include_children' => true" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$mkprod = static function ( $title, $slug ) {
		return (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => $title, 'post_name' => $slug ) );
	};
	$parent = wp_insert_term( 'ریسه و تزئینات', 'product_cat', array( 'slug' => 'strings-decor', 'description' => 'توضیح دسته ریسه و تزئینات نورپردازی شهری.' ) );
	$child  = wp_insert_term( 'ریسه بلوطی LED', 'product_cat', array( 'slug' => 'acorn-thread', 'parent' => (int) $parent['term_id'] ) );
	$pid    = (int) $parent['term_id'];
	$cid    = (int) $child['term_id'];

	// Term thumbnail + FAQ.
	$att = (int) wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'cat-img', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/cat.jpg' );
	update_term_meta( $pid, 'thumbnail', $att );
	update_term_meta( $pid, 'faq', json_encode( array(
		'item-0' => array( 'name_question' => 'گارانتی ریسه بلوطی چقدر است؟', 'answer_question' => '۲۴ ماه گارانتی تعویض.' ),
		'item-1' => array( 'name_question' => 'امکان سفارشی‌سازی هست؟', 'answer_question' => 'بله.' ),
	), JSON_UNESCAPED_UNICODE ) );

	$p_parent = $mkprod( 'ریسه بلوطی هفت رنگ', 'baloti-parent' );
	$p_child  = $mkprod( 'ریسه بلوطی تک رنگ', 'baloti-child' );
	wp_set_object_terms( $p_parent, array( $pid ), 'product_cat' );
	wp_set_object_terms( $p_child, array( $cid ), 'product_cat' );

	// Render the parent category archive.
	$GLOBALS['wp_query']->queried_object    = get_term( $pid, 'product_cat' );
	$GLOBALS['wp_query']->queried_object_id = $pid;
	$GLOBALS['wp_query']->is_tax            = true;
	$GLOBALS['wp_query']->query_vars['taxonomy'] = 'product_cat';
	$GLOBALS['wp_query']->query_vars['term']     = 'strings-decor';
	ob_start();
	include ES_THEME . '/woocommerce/taxonomy-product_cat.php';
	$html = (string) ob_get_clean();

	es_check( 'AC-03: ترم فارسی resolve می‌شود (نام دسته در hero)', false !== strpos( $html, 'ریسه و تزئینات' ) );
	es_check( 'AC-01: id=archive و data-initial-category دسته', false !== strpos( $html, 'id="archive"' ) && false !== strpos( $html, 'data-initial-category="ریسه و تزئینات"' ) );
	es_check( 'AC-01: محصولات دسته + زیردسته رندر می‌شوند', false !== strpos( $html, 'ریسه بلوطی هفت رنگ' ) && false !== strpos( $html, 'ریسه بلوطی تک رنگ' ) );
	es_check( 'AC-02: توضیحات ترم در hero', false !== strpos( $html, 'توضیح دسته ریسه و تزئینات نورپردازی شهری.' ) );
	es_check( 'AC-02: تصویر ترم در hero', false !== strpos( $html, 'cat.jpg' ) );
	es_check( 'SC-01: FAQ دسته رندر می‌شود', false !== strpos( $html, 'data-category-faq' ) && false !== strpos( $html, 'گارانتی ریسه بلوطی چقدر است؟' ) && false !== strpos( $html, 'data-faq-list="true"' ) );
	es_check( 'SC-01: آکاردئون FAQ (اول باز)', false !== strpos( $html, 'data-faq-body="0" class="block' ) );

	// AC-02 / SC-02: subcategory query resolves (include_children) without fatal.
	$child_q = new WP_Query( array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'tax_query' => array( array( 'taxonomy' => 'product_cat', 'field' => 'term_id', 'terms' => $cid, 'include_children' => true ) ) ) );
	es_check( 'SC-02: زیردسته محصول دارد (404 نمی‌دهد)', $child_q->post_count >= 1 );
	ob_start();
	get_template_part( 'template-parts/archive/product', 'archive-shell', array( 'query' => $child_q, 'initial_category' => 'ریسه بلوطی LED', 'show_hero' => false ) );
	$child_html = (string) ob_get_clean();
	es_check( 'SC-02: آرشیو زیردسته رندر می‌شود', false !== strpos( $child_html, 'data-initial-category="ریسه بلوطی LED"' ) && false !== strpos( $child_html, 'ریسه بلوطی تک رنگ' ) );

	// AC-01: template_include routes product_cat to the theme template.
	$routed = apply_filters( 'template_include', '/default/template.php' );
	es_check( 'AC-01: template_include → taxonomy-product_cat.php', false !== strpos( $routed, 'woocommerce/taxonomy-product_cat.php' ) );

	foreach ( array( $p_parent, $p_child, $att ) as $p ) {
		wp_delete_post( $p, true );
	}
	wp_delete_term( $cid, 'product_cat' );
	wp_delete_term( $pid, 'product_cat' );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
