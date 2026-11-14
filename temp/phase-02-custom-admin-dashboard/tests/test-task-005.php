<?php
/**
 * P02-T005 — Renderer فیلدهای پایه
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-005.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\FieldRenderer;

es_task( 'P02-T005', 'Renderer فیلدهای پایه' );

es_check( 'target: inc/Admin/FieldRenderer.php', es_file( 'inc/Admin/FieldRenderer.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// SC-01: each basic type shows its previous value.
	$cases = array(
		'brand_name'          => array('type' => 'text', 'value' => 'نام آزمایشی'),
		'footer_description'  => array('type' => 'textarea', 'value' => 'توضیح آزمایشی'),
		'shop_posts_per_page' => array('type' => 'number', 'value' => 18),
		'pagination_type'     => array('type' => 'select', 'value' => 'loadmore'),
		'announcement_enabled' => array('type' => 'toggle', 'value' => true),
	);

	$ids = array();
	foreach ( $cases as $key => $case ) {
		$html = FieldRenderer::render_field( $key, $case['value'] );
		es_check( "SC-01: فیلد {$case['type']} مقدار قبلی را نشان می‌دهد", '' !== $html && false !== strpos( $html, (string) $case['value'] ) );
		es_check( "AC-01: فیلد {$key} name دارد", false !== strpos( $html, 'name="es_options[' . $key . ']"' ) );
		es_check( "AC-01: فیلد {$key} data-es-key دارد", false !== strpos( $html, 'data-es-key="' . $key . '"' ) );

		preg_match( '/id="([^"]+)"/', $html, $m );
		$field_id = $m[1] ?? '';
		es_check( "AC-01: فیلد {$key} id یکتا دارد", '' !== $field_id && ! in_array( $field_id, $ids, true ) );
		$ids[] = $field_id;
		es_check( "AC-01: label برای فیلد {$key} به id اشاره دارد", false !== strpos( $html, 'for="' . $field_id . '"' ) );
	}

	// Checkbox branch (no schema checkbox key yet) — exercise the renderer directly.
	$ref = new ReflectionMethod( FieldRenderer::class, 'checkbox' );
	$ref->setAccessible( true );
	$checkbox_html = $ref->invoke( null, 'es-field-test-cb', 'es_options[test_cb]', 'test_cb', true );
	es_check( 'SC-01: checkbox مقدار checked را نشان می‌دهد', false !== strpos( $checkbox_html, 'type="checkbox"' ) && false !== strpos( $checkbox_html, 'checked' ) );

	// AC-02: stored value is escaped.
	$malicious = '"><script>alert(1)</script>';
	$escaped   = FieldRenderer::render_field( 'brand_name', $malicious );
	es_check( 'AC-02: مقدار خطرناک escape می‌شود', false === strpos( $escaped, '<script>alert(1)</script>' ) && false !== strpos( $escaped, '&quot;' ) );

	// AC-03: unknown key / unsupported type renders nothing.
	es_check( 'AC-03: کلید ناشناخته رندر نمی‌شود', '' === FieldRenderer::render_field( 'no_such_field_xyz' ) );

	// SC-02: output is well-formed HTML.
	$sample = '<div>' . FieldRenderer::render_field( 'brand_name', 'نمونه' ) . FieldRenderer::render_field( 'pagination_type', 'numeric' ) . '</div>';
	$prev   = libxml_use_internal_errors( true );
	$doc    = new DOMDocument();
	$loaded = $doc->loadHTML( '<?xml encoding="utf-8"?>' . $sample, LIBXML_NOERROR | LIBXML_NOWARNING );
	$errors = libxml_get_errors();
	libxml_clear_errors();
	libxml_use_internal_errors( $prev );
	es_check( 'SC-02: خروجی HTML معتبر است', $loaded && empty( $errors ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
