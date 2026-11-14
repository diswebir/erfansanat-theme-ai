<?php
/**
 * P06-T008 — اطلاعیه قبل از خرید و کاتالوگ
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-008.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T008', 'اطلاعیه قبل از خرید و کاتالوگ' );

es_check( 'target: notices.php', es_file( 'template-parts/product/notices.php' ) );
es_check( 'hook: es_product_summary_after → notices', es_contains( 'functions.php', 'product/notices' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	es_check( 'AC: notices به هوک after متصل است', (bool) has_action( 'es_product_summary_after' ) );

	$render = static function ( $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		$product = wc_get_product( $pid );
		ob_start();
		include ES_THEME . '/template-parts/product/notices.php';
		return (string) ob_get_clean();
	};
	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول اطلاعیه', 'post_name' => 'notice-prod' ) );

	// AC-03: empty → no DOM.
	es_check( 'AC-03: خالی → DOM نمی‌سازد', '' === trim( $render( $pid ) ) );

	// SC-01: notice + catalog URL.
	update_post_meta( $pid, 'before-sale-c', "حداقل سفارش ۵ شاخه است.\nارسال با باربری بیمه‌شده." );
	update_post_meta( $pid, 'product-catalog-file', home_url( '/wp-content/uploads/delsa.pdf' ) );
	$html = $render( $pid );
	es_check( 'SC-01: اطلاعیه رندر می‌شود', false !== strpos( $html, 'data-before-sale-notice' ) && false !== strpos( $html, 'حداقل سفارش ۵ شاخه است.' ) );
	es_check( 'AC-01: اطلاعیه escape شده (خط جدید حفظ)', false !== strpos( $html, 'whitespace-pre-line' ) );
	es_check( 'SC-01: لینک کاتالوگ رندر می‌شود', false !== strpos( $html, 'data-product-catalog' ) && false !== strpos( $html, 'delsa.pdf' ) );
	es_check( 'AC-02: لینک دانلودی است', false !== strpos( $html, 'download' ) );
	es_check( 'AC-02: لینک داخلی target ندارد', false === strpos( $html, 'target="_blank"' ) );

	// AC-02: external catalog hardened.
	update_post_meta( $pid, 'product-catalog-file', 'https://cdn.example.com/cat.pdf' );
	$html_ext = $render( $pid );
	es_check( 'AC-02: لینک خارجی rel امن دارد', false !== strpos( $html_ext, 'rel="noopener noreferrer"' ) && false !== strpos( $html_ext, 'target="_blank"' ) );

	// SC-02: removing catalog removes the link, notice stays.
	delete_post_meta( $pid, 'product-catalog-file' );
	$html_no = $render( $pid );
	es_check( 'SC-02: حذف فایل → لینک حذف می‌شود', false === strpos( $html_no, 'data-product-catalog' ) );
	es_check( 'SC-02: اطلاعیه باقی می‌ماند', false !== strpos( $html_no, 'data-before-sale-notice' ) );

	// Catalog stored as attachment ID resolves to URL.
	$att = (int) wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'cat', 'post_mime_type' => 'application/pdf' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/catalog.pdf' );
	update_post_meta( $pid, 'product-catalog-file', (string) $att );
	$html_id = $render( $pid );
	es_check( 'AC-02: شناسه رسانه به URL تبدیل می‌شود', false !== strpos( $html_id, 'catalog.pdf' ) );

	// XSS in notice is escaped.
	update_post_meta( $pid, 'before-sale-c', '<script>alert(1)</script>' );
	es_check( 'AC-01: XSS اطلاعیه escape می‌شود', false === strpos( $render( $pid ), '<script>alert(1)</script>' ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $att, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
