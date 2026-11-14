<?php
/**
 * P06-T007 — Variation Swatches و Quantity
 * Run: php temp/phase-06-woocommerce-and-products/tests/test-task-007.php
 * DOM behaviour (main.js) is covered by test-task-007-dom.mjs.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\AttributeMap;
use ErfanSanat\Theme\Core\ProductQuantity;

es_task( 'P06-T007', 'Variation Swatches و Quantity' );

es_check( 'target: variable.php', es_file( 'woocommerce/single-product/variable.php' ) );
es_check( 'target: purchase.php', es_file( 'template-parts/product/purchase.php' ) );
es_check( 'AC-01: هوک woocommerce_before_variations_form', es_contains( 'woocommerce/single-product/variable.php', "do_action( 'woocommerce_before_variations_form' )" ) );
es_check( 'AC-01: هوک woocommerce_single_variation', es_contains( 'woocommerce/single-product/variable.php', "do_action( 'woocommerce_single_variation' )" ) );
es_check( 'AC-01: هوک woocommerce_after_variations_form', es_contains( 'woocommerce/single-product/variable.php', "do_action( 'woocommerce_after_variations_form' )" ) );
es_check( 'AC-01: data-product_variations حفظ شده', es_contains( 'woocommerce/single-product/variable.php', 'data-product_variations=' ) );
es_check( 'AC-02: کنترل تعداد data-pdp-qty-root', es_contains( 'template-parts/product/purchase.php', 'data-pdp-qty-root="true"' ) );
es_check( 'AC-03: سواچ data-opt-btn', es_contains( 'template-parts/product/purchase.php', 'data-opt-btn="true"' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require ES_ROOT . '/temp/tests/woocommerce-shim.php';
	AttributeMap::register_missing();

	$fa = static function ( $v ) {
		$map = array();
		for ( $d = 0; $d < 10; $d++ ) { $map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d ); }
		return strtr( (string) $v, $map );
	};
	$render = static function ( $file, $pid ) {
		$GLOBALS['post'] = get_post( $pid );
		setup_postdata( $GLOBALS['post'] );
		ob_start();
		include ES_THEME . '/' . $file;
		return (string) ob_get_clean();
	};

	$pid = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'ریسه فندقی', 'post_name' => 'fandoghi-string' ) );
	update_post_meta( $pid, '_product_type', 'variable' );
	update_post_meta( $pid, 'true_sale', 'true' );
	update_post_meta( $pid, '_price', '100000' );
	update_post_meta( $pid, '_regular_price', '100000' );
	update_post_meta( $pid, '_stock_status', 'instock' );
	update_post_meta( $pid, '_sku', 'ESE-FAN-1' );
	update_post_meta( $pid, '_min_qty', '5' );
	update_post_meta( $pid, '_max_qty', '20' );
	update_post_meta( $pid, '_step_qty', '5' );
	update_post_meta( $pid, '_unit', 'شاخه' );

	$green = es_insert_term( 'سبز', 'pa_color', array( 'slug' => 'green' ) );
	$red   = es_insert_term( 'قرمز', 'pa_color', array( 'slug' => 'red' ) );
	$s10   = es_insert_term( '10m', 'pa_size', array( 'slug' => '10m' ) );
	$s20   = es_insert_term( '20m', 'pa_size', array( 'slug' => '20m' ) );
	wp_set_object_terms( $pid, array( (int) $green['term_id'], (int) $red['term_id'] ), 'pa_color' );
	wp_set_object_terms( $pid, array( (int) $s10['term_id'], (int) $s20['term_id'] ), 'pa_size' );
	update_post_meta( $pid, '_es_variation_attributes', array( 'pa_color' => array( 'green', 'red' ), 'pa_size' => array( '10m', '20m' ) ) );
	update_post_meta( $pid, '_product_variations', array(
		array( 'variation_id' => 101, 'attributes' => array( 'attribute_pa_color' => 'green', 'attribute_pa_size' => '10m' ), 'display_price' => 100000, 'is_in_stock' => true ),
		array( 'variation_id' => 102, 'attributes' => array( 'attribute_pa_color' => 'red', 'attribute_pa_size' => '20m' ), 'display_price' => 120000, 'is_in_stock' => true ),
	) );

	// ── AC-02: quantity rules + SC-02 invalid rejection ───────────────────.
	$product = wc_get_product( $pid );
	$rules   = ProductQuantity::rules( $product );
	es_check( 'AC-02: قواعد min/max/step از متا', 5 === $rules['min'] && 20 === $rules['max'] && 5 === $rules['step'] );
	es_check( 'SC-02: مقدار معتبر ۵ پذیرفته می‌شود', true === ProductQuantity::validate( $product, 5 ) );
	es_check( 'SC-02: مقدار معتبر ۲۰ پذیرفته می‌شود', true === ProductQuantity::validate( $product, 20 ) );
	es_check( 'SC-02: کمتر از حداقل رد می‌شود', 'below_minimum' === ProductQuantity::validate( $product, 4 ) );
	es_check( 'SC-02: بیشتر از حداکثر رد می‌شود', 'above_maximum' === ProductQuantity::validate( $product, 25 ) );
	es_check( 'SC-02: خارج از گام رد می‌شود', 'invalid_step' === ProductQuantity::validate( $product, 7 ) );
	es_check( 'SC-02: مقدار غیرعددی رد می‌شود', 'invalid_quantity' === ProductQuantity::validate( $product, 'abc' ) );
	es_check( 'SC-02: مقدار اعشاری رد می‌شود', 'invalid_quantity' === ProductQuantity::validate( $product, 5.5 ) );

	// ── AC-03 / SC-01: fallback swatches + stepper render ─────────────────.
	$html = $render( 'template-parts/product/purchase.php', $pid );
	es_check( 'AC-03: گروه سواچ رنگ نور', false !== strpos( $html, 'data-opt-group="رنگ نور"' ) );
	es_check( 'AC-03: دو سواچ رنگ', 2 === preg_match_all( '/data-opt-is-color="true"/', $html ) );
	es_check( 'AC-03: اولین رنگ انتخاب‌شده (ring-2)', 1 === preg_match_all( '/data-opt-is-color="true"[^>]*class="[^"]*ring-2/', $html ) );
	es_check( 'AC-03: گروه سواچ طول/تراکم', false !== strpos( $html, 'data-opt-group="طول و تراکم ریسه"' ) );
	es_check( 'AC-02: stepper با min/max/step', false !== strpos( $html, 'data-min-qty="5"' ) && false !== strpos( $html, 'data-max-qty="20"' ) && false !== strpos( $html, 'data-step-qty="5"' ) );
	es_check( 'AC-02: مقدار اولیه فارسی ۵', false !== strpos( $html, 'data-pdp-qty-value="true"' ) && false !== strpos( $html, '>' . $fa( 5 ) . '<' ) );
	es_check( 'SC-01: دکمه افزودن به سبد حالت متغیر fallback', false !== strpos( $html, 'data-pdp-add-to-cart="true"' ) );

	// ── AC-01: variable form fires canonical hooks ────────────────────────.
	$GLOBALS['product'] = $product;
	$markers = array();
	foreach ( array( 'woocommerce_before_variations_form', 'woocommerce_before_single_variation', 'woocommerce_single_variation', 'woocommerce_after_single_variation', 'woocommerce_after_variations_form' ) as $hook ) {
		add_action( $hook, static function () use ( &$markers, $hook ) { $markers[] = $hook; echo '<!--' . $hook . '-->'; } );
	}
	$form = $render( 'woocommerce/single-product/variable.php', $pid );
	es_check( 'AC-01: فرم variations_form رندر می‌شود', false !== strpos( $form, 'class="variations_form cart' ) );
	es_check( 'AC-01: data-product_id درست', false !== strpos( $form, 'data-product_id="' . $pid . '"' ) );
	es_check( 'AC-01: data-product_variations شامل variation_id', false !== strpos( $form, 'variation_id' ) && false !== strpos( $form, '101' ) );
	es_check( 'AC-01: select ویژگی pa_color', false !== strpos( $form, 'name="attribute_pa_color"' ) );
	es_check( 'AC-01: همه هوک‌های variation اجرا شدند', 5 === count( array_intersect( array( 'woocommerce_before_variations_form', 'woocommerce_before_single_variation', 'woocommerce_single_variation', 'woocommerce_after_single_variation', 'woocommerce_after_variations_form' ), $markers ) ) );
	foreach ( array( 'woocommerce_before_variations_form', 'woocommerce_before_single_variation', 'woocommerce_single_variation', 'woocommerce_after_single_variation', 'woocommerce_after_variations_form' ) as $hook ) { remove_all_actions( $hook ); }

	// ── Real-Woo branch: native variation form replaces static stepper ────.
	if ( ! defined( 'WC_VERSION' ) ) { define( 'WC_VERSION', '10.9.0' ); }
	$native = $render( 'template-parts/product/purchase.php', $pid );
	es_check( 'AC-01: با Woo واقعی فرم variation جایگزین stepper می‌شود', false !== strpos( $native, 'variations_form' ) && false === strpos( $native, 'data-pdp-qty-root="true"' ) );
	es_check( 'AC-01: با Woo واقعی دکمه fallback سبد حذف می‌شود', false === strpos( $native, 'data-pdp-add-to-cart="true"' ) );

	// ── Inquiry product has no stepper ────────────────────────────────────.
	$inq = (int) wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'استعلامی' ) );
	update_post_meta( $inq, 'true_sale', 'false' );
	$inq_html = $render( 'template-parts/product/purchase.php', $inq );
	es_check( 'AC-02: محصول استعلامی stepper ندارد', false === strpos( $inq_html, 'data-pdp-qty-root="true"' ) );

	wp_delete_post( $pid, true );
	wp_delete_post( $inq, true );
	wp_delete_term( (int) $green['term_id'], 'pa_color' );
	wp_delete_term( (int) $red['term_id'], 'pa_color' );
	wp_delete_term( (int) $s10['term_id'], 'pa_size' );
	wp_delete_term( (int) $s20['term_id'], 'pa_size' );
	wp_reset_postdata();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
