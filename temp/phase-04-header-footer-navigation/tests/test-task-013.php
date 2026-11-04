<?php
/**
 * P04-T013 — Markup مودال سبد خرید
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-013.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T013', 'Markup مودال سبد خرید' );

es_check( 'target: template-parts/header/cart-modal.php', es_file( 'template-parts/header/cart-modal.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$render = static function () {
		ob_start();
		get_template_part( 'template-parts/header/cart-modal' );
		return (string) ob_get_clean();
	};

	$html = $render();

	// AC-01: preserved data-cart-modal-* attributes.
	foreach ( array( 'data-cart-modal="true"', 'data-cart-modal-backdrop="true"', 'data-cart-modal-close="true"', 'data-cart-modal-count="true"', 'data-cart-modal-total="true"', 'data-cart-modal-items-list="true"', 'data-cart-modal-empty="true"' ) as $attr ) {
		es_check( "AC-01: {$attr} حفظ شده", false !== strpos( $html, $attr ) );
	}
	es_check( 'AC-01: role=dialog و aria-modal', false !== strpos( $html, 'role="dialog" aria-modal="true"' ) );

	// AC-02 / SC-01: empty cart renders the genuine empty state.
	es_check( 'AC-02: حالت خالی نمایان است', false !== strpos( $html, 'data-cart-modal-empty="true" class="flex' ) );
	es_check( 'SC-01: پیام سبد خالی درست است', false !== strpos( $html, 'سبد خرید شما در حال حاضر خالی است' ) );
	es_check( 'AC-03: شمارنده پیش‌فرض صفر کالا', false !== strpos( $html, '0 کالا' ) );

	// AC-03 / SC-02: a real (injected) cart payload drives count/total/rows.
	$payload = array(
		'items'      => array(
			array(
				'key' => 'test-key-1', 'title' => 'ریسه تست', 'permalink' => home_url( '/product/test/' ),
				'thumb' => ES_ASSETS_URI . 'images/hero.jpg', 'qty' => 3,
				'price_html' => '۱۰۰ تومان', 'row_total_html' => '۳۰۰ تومان',
			),
		),
		'count'      => 3,
		'total_html' => '۳۰ تومان',
	);
	$cb = static function () use ( $payload ) { return $payload; };
	add_filter( 'es_cart_modal_data', $cb );
	$html = $render();
	es_check( 'AC-03: شمارنده از داده واقعی سبد', false !== strpos( $html, '3 کالا' ) );
	es_check( 'AC-03: جمع کل از داده واقعی سبد', false !== strpos( $html, '۳۰ تومان' ) );
	es_check( 'AC-01: ردیف سبد با data-cart-modal-row', false !== strpos( $html, 'data-cart-modal-row="test-key-1"' ) );
	es_check( 'AC-01: دکمه‌های dec/inc/remove ردیف', false !== strpos( $html, 'data-cart-modal-dec="test-key-1"' ) && false !== strpos( $html, 'data-cart-modal-inc="test-key-1"' ) && false !== strpos( $html, 'data-cart-modal-remove="test-key-1"' ) );
	es_check( 'AC-02: با سبد پر، حالت خالی hidden می‌شود', false !== strpos( $html, 'data-cart-modal-empty="true" class="hidden' ) );
	remove_filter( 'es_cart_modal_data', $cb );

	// SC-02: main.js opens/closes the cart modal.
	$js = (string) file_get_contents( ES_THEME . '/assets/js/main.js' );
	es_check( 'SC-02: باز/بسته‌شدن مودال (main.js)', false !== strpos( $js, 'openCartModal' ) && false !== strpos( $js, 'closeCartModal' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
