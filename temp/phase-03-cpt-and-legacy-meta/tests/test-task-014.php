<?php
/**
 * P03-T014 — متاباکس عنوان/وضعیت فروش محصول
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-014.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\ProductMetaboxes;

es_task( 'P03-T014', 'متاباکس عنوان/وضعیت فروش محصول' );

es_check( 'target: inc/Admin/ProductMetaboxes.php', es_file( 'inc/Admin/ProductMetaboxes.php' ) );

if ( es_load_wp() ) {
	define( 'WP_ADMIN', true );
	require_once ES_THEME . '/functions.php';

	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	wp_set_current_user( 1 );
	$pid = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول فروش' ) );

	$nonce = wp_create_nonce( 'es_product_titles' );

	// SC-01: inquiry product (true_sale=false) saves with exact keys.
	$_POST['es_titles'] = array(
		'zireonvan'     => 'زیرعنوان محصول',
		'english_titl'  => 'Urban Light Element',
		'before-sale-c' => "اطلاعیه قبل از خرید\nخط دوم",
		'true_sale'     => 'false',
	);
	$_POST['es_product_titles_nonce'] = $nonce;
	ProductMetaboxes::save_titles( $pid );
	es_check( 'AC-01: zireonvan ذخیره شد', 'زیرعنوان محصول' === get_post_meta( $pid, 'zireonvan', true ) );
	es_check( 'AC-03: املای english_titl حفظ شد', 'Urban Light Element' === get_post_meta( $pid, 'english_titl', true ) );
	es_check( 'AC-01: before-sale-c ذخیره شد', false !== strpos( get_post_meta( $pid, 'before-sale-c', true ), 'خط دوم' ) );
	es_check( 'SC-01: محصول استعلامی true_sale=false', 'false' === get_post_meta( $pid, 'true_sale', true ) );

	// SC-01: online product (true_sale=true).
	$_POST['es_titles']['true_sale'] = 'true';
	ProductMetaboxes::save_titles( $pid );
	es_check( 'SC-01: محصول آنلاین true_sale=true', 'true' === get_post_meta( $pid, 'true_sale', true ) );

	// SC-02 / AC-02: invalid true_sale is rejected (keeps previous).
	foreach ( array( '1', 'yes', 'on', '' ) as $bad ) {
		$_POST['es_titles']['true_sale'] = $bad;
		ProductMetaboxes::save_titles( $pid );
	}
	es_check( 'SC-02: مقدار نامعتبر true_sale رد می‌شود', 'true' === get_post_meta( $pid, 'true_sale', true ) );

	// AC-03: nonce guard.
	unset( $_POST['es_product_titles_nonce'] );
	$_POST['es_titles']['zireonvan'] = 'HACKED';
	ProductMetaboxes::save_titles( $pid );
	es_check( 'AC-03: بدون nonce ذخیره نمی‌شود', 'زیرعنوان محصول' === get_post_meta( $pid, 'zireonvan', true ) );

	// Metabox renders the fields + true_sale options.
	ob_start();
	ProductMetaboxes::render_titles( get_post( $pid ) );
	$html = ob_get_clean();
	es_check( 'متاباکس: فیلد english_titl رندر می‌شود', false !== strpos( $html, 'es_titles[english_titl]' ) );
	es_check( 'متاباکس: گزینه‌های true_sale دارد', false !== strpos( $html, 'value="true"' ) && false !== strpos( $html, 'value="false"' ) );

	wp_delete_post( $pid, true );
	unset( $_POST['es_titles'], $_POST['es_product_titles_nonce'] );
	unregister_post_type( 'product' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
