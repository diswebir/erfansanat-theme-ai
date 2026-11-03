<?php
/**
 * P03-T007 — نقشه سازگاری ۱۸ ویژگی pa_*
 * Run: php temp/phase-03-cpt-and-legacy-meta/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\AttributeMap;

es_task( 'P03-T007', 'نقشه سازگاری ۱۸ ویژگی pa_*' );

es_check( 'target: inc/Core/AttributeMap.php', es_file( 'inc/Core/AttributeMap.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$expected = array(
		'pa_after-sales-support' => 'خدمات پس از فروش',
		'pa_beam-angle'          => 'زاویه تابش',
		'pa_body-material'       => 'جنس بدنه',
		'pa_brand'               => 'برند',
		'pa_bulb-type'           => 'جنس حباب',
		'pa_cable-material'      => 'جنس سیم',
		'pa_certifications'      => 'گواهینامه‌ها',
		'pa_color'               => 'رنگ نور',
		'pa_diameter'            => 'قطر',
		'pa_diffuser-color'      => 'رنگ حباب',
		'pa_input-voltage'       => 'ولتاژ ورودی',
		'pa_intensity'           => 'شدت نور',
		'pa_ip-rating'           => 'درجه حفاظت IP',
		'pa_led-chip-type'       => 'نوع چیپ LED',
		'pa_lifespan'            => 'طول عمر مفید',
		'pa_size'                => 'طول/تراکم',
		'pa_warranty'            => 'گارانتی',
		'pa_wattage'             => 'توان مصرفی وات',
	);

	$all = AttributeMap::all();
	es_check( 'AC-01: دقیقاً ۱۸ ویژگی map شده', 18 === count( $all ) );

	$keys_ok = true;
	foreach ( array_keys( $expected ) as $tax ) {
		$keys_ok = $keys_ok && isset( $all[ $tax ] );
	}
	es_check( 'AC-01: نام هر ۱۸ taxonomy دقیق است', $keys_ok );

	// SC-01: labels match the live DB blueprint.
	$labels_ok = true;
	foreach ( $expected as $tax => $label ) {
		$labels_ok = $labels_ok && ( AttributeMap::label( $tax ) === $label );
	}
	es_check( 'SC-01: برچسب‌ها با بلوپرینت DB برابرند', $labels_ok );

	// AC-02: pa_color is a color attribute.
	es_check( 'AC-02: pa_color نوع color دارد', 'color' === AttributeMap::type( 'pa_color' ) );
	es_check( 'AC-02: سایر ویژگی‌ها select هستند', 'select' === AttributeMap::type( 'pa_wattage' ) );

	// SC-02: a missing attribute does not fatal.
	es_check( 'SC-02: ویژگی ناموجود null می‌دهد', null === AttributeMap::get( 'pa_does_not_exist' ) );
	es_check( 'SC-02: is_mapped برای ناموجود false', false === AttributeMap::is_mapped( 'pa_does_not_exist' ) );

	// AC-03: register_missing never re-registers an existing attribute.
	register_taxonomy( 'pa_color', 'product', array( 'label' => 'رنگ سفارشی' ) );
	AttributeMap::register_missing();
	es_check( 'AC-03: ویژگی موجود دوباره register نمی‌شود', 'رنگ سفارشی' === get_taxonomy( 'pa_color' )->label );
	es_check( 'AC-03: ویژگی‌های ناموجود ثبت می‌شوند', taxonomy_exists( 'pa_wattage' ) );

	// Cleanup all registered attribute taxonomies.
	foreach ( array_keys( $expected ) as $tax ) {
		if ( taxonomy_exists( $tax ) ) {
			unregister_taxonomy( $tax );
		}
	}
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
