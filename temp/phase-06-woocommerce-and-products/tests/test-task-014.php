<?php
/**
 * P06-T014 — دیدگاه‌ها و امتیاز محصول
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P06-T014', 'دیدگاه‌ها و امتیاز' );

es_check( 'target: reviews.php', es_file( 'template-parts/product/reviews.php' ) );
es_check( 'target: review.php', es_file( 'woocommerce/single-product/review.php', ES_THEME ) );
es_check( 'AC-02: هوک‌های فرم مرجع', es_contains( 'template-parts/product/reviews.php', 'data-review-form="true"' ) && es_contains( 'template-parts/product/reviews.php', 'data-review-rating-input' ) );
es_check( 'AC-02: nonce + فیلد rating ووکامرس', es_contains( 'template-parts/product/reviews.php', 'wp_nonce_field' ) && es_contains( 'template-parts/product/reviews.php', 'name="rating"' ) && es_contains( 'template-parts/product/reviews.php', 'name="comment_post_ID"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';

	$render = static function ( $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		$product = wc_get_product( $pid );
		ob_start();
		include ES_THEME . '/template-parts/product/reviews.php';
		return (string) ob_get_clean();
	};
	$fa = static function ( $v ) {
		$map = array();
		for ( $d = 0; $d < 10; $d++ ) {
			$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
		}
		return strtr( (string) $v, $map );
	};
	$mkcomment = static function ( $pid, $author, $text, $rating, $org = '' ) {
		$cid = (int) wp_insert_comment( array(
			'comment_post_ID'      => $pid,
			'comment_author'       => $author,
			'comment_content'      => $text,
			'comment_approved'     => 1,
			'comment_type'         => 'review',
			'comment_author_email' => 'test@example.com',
		) );
		add_comment_meta( $cid, 'rating', $rating );
		if ( '' !== $org ) {
			add_comment_meta( $cid, 'es_review_org', $org );
		}
		return $cid;
	};

	$pid = (int) wp_insert_post( array(
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'post_title'     => 'درخت نوری دلسا',
		'post_name'      => 'reviews-prod',
		'comment_status' => 'open',
	) );

	// Empty state: no comments → average 0, count 0, empty-list message.
	$html0 = $render( $pid );
	es_check( 'AC-01: بدون دیدگاه → میانگین ۰٫۰۰', false !== strpos( $html0, $fa( '0.00' ) ) );
	es_check( 'AC-01: بدون دیدگاه → شمارنده ۰', false !== strpos( $html0, $fa( '۰ دیدگاه ثبت‌شده' ) ) || false !== strpos( $html0, $fa( 0 ) . ' دیدگاه ثبت‌شده' ) );
	es_check( 'AC: پیام لیست خالی', false !== strpos( $html0, 'هنوز دیدگاهی ثبت نشده است' ) );
	es_check( 'AC-02: فرم با nonce رندر می‌شود', false !== strpos( $html0, 'data-review-form="true"' ) && false !== strpos( $html0, 'es_review_nonce' ) );
	es_check( 'AC-02: پنج دکمه ستاره', 5 === substr_count( $html0, 'data-review-star=' ) );

	// SC-01 / SC-02: insert real reviews with ratings.
	$c1 = $mkcomment( $pid, 'مهندس رضایی', 'کیفیت عالی بود.', 5, 'شهرداری اصفهان' );
	$c2 = $mkcomment( $pid, 'مهندس کاظمی', 'خوب بود.', 4, 'پیمانکار' );
	$html = $render( $pid );

	es_check( 'SC-01: دیدگاه‌ها رندر می‌شوند', false !== strpos( $html, 'مهندس رضایی' ) && false !== strpos( $html, 'مهندس کاظمی' ) );
	es_check( 'SC-01: سازمان دیدگاه نمایش داده شد', false !== strpos( $html, 'شهرداری اصفهان' ) );
	es_check( 'AC-01: شمارنده واقعی = ۲', false !== strpos( $html, $fa( 2 ) . ' دیدگاه ثبت‌شده' ) );
	es_check( 'SC-02: میانگین واقعی (۵+۴)/۲ = ۴٫۵۰', false !== strpos( $html, $fa( '4.50' ) ) );
	es_check( 'AC-01: کارت تک‌دیدگاه data-review-item', 2 === substr_count( $html, 'data-review-item' ) );

	// Add a third review → average updates to (5+4+3)/3 = 4.00.
	$c3 = $mkcomment( $pid, 'مهندس شریفی', 'متوسط.', 3 );
	$html2 = $render( $pid );
	es_check( 'SC-02: میانگین پس از دیدگاه سوم = ۴٫۰۰', false !== strpos( $html2, $fa( '4.00' ) ) );
	es_check( 'SC-02: شمارنده = ۳', false !== strpos( $html2, $fa( 3 ) . ' دیدگاه ثبت‌شده' ) );

	// AC-03: closed comments → notice, no form.
	wp_update_post( array( 'ID' => $pid, 'comment_status' => 'closed' ) );
	clean_post_cache( $pid );
	$html_closed = $render( $pid );
	es_check( 'AC-03: دیدگاه بسته → پیام بسته', false !== strpos( $html_closed, 'data-reviews-closed' ) && false !== strpos( $html_closed, 'دیدگاه‌ها برای این محصول بسته است' ) );
	es_check( 'AC-03: دیدگاه بسته → فرم ندارد', false === strpos( $html_closed, 'data-review-form="true"' ) );

	// XSS in comment text is escaped.
	$cx = $mkcomment( $pid, 'کاربر', '<script>alert(1)</script>متن', 5 );
	wp_update_post( array( 'ID' => $pid, 'comment_status' => 'open' ) );
	clean_post_cache( $pid );
	$htmlx = $render( $pid );
	es_check( 'AC: اسکریپت دیدگاه حذف می‌شود', false === strpos( $htmlx, '<script>alert(1)</script>' ) );

	foreach ( array( $c1, $c2, $c3, $cx ) as $cid ) {
		wp_delete_comment( $cid, true );
	}
	wp_delete_post( $pid, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
