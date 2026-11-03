<?php
/**
 * P02-T007 — فیلد Repeater
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-007.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\FieldRenderer;

es_task( 'P02-T007', 'فیلد Repeater' );

es_check( 'target: inc/Admin/FieldRenderer.php', es_file( 'inc/Admin/FieldRenderer.php' ) );
es_check( 'target: assets/admin/repeater.js', es_file( 'assets/admin/repeater.js' ) );

$repeater_js = (string) file_get_contents( ES_THEME . '/assets/admin/repeater.js' );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$items = array(
		array( 'network' => 'instagram', 'url' => 'https://instagram.com/a' ),
		array( 'network' => 'telegram', 'url' => 'https://t.me/b' ),
		array( 'network' => 'linkedin', 'url' => 'https://linkedin.com/c' ),
	);

	$html = FieldRenderer::render_field( 'social_links', $items );

	// SC-01: three items render and are re-readable.
	es_check( 'SC-01: سه آیتم رندر می‌شود', false !== strpos( $html, 'data-index="0"' ) && false !== strpos( $html, 'data-index="1"' ) && false !== strpos( $html, 'data-index="2"' ) );
	es_check( 'SC-01: مقادیر هر سه آیتم موجود است', false !== strpos( $html, 'https://instagram.com/a' ) && false !== strpos( $html, 'https://t.me/b' ) && false !== strpos( $html, 'https://linkedin.com/c' ) );
	$again = FieldRenderer::render_field( 'social_links', $items );
	es_check( 'SC-01: خواندن مجدد پایدار است', $html === $again );

	// AC-02: subfields come from the schema.
	es_check( 'AC-02: زیرفیلد network از schema', false !== strpos( $html, 'name="es_options[social_links][0][network]"' ) );
	es_check( 'AC-02: زیرفیلد url از schema', false !== strpos( $html, 'name="es_options[social_links][0][url]"' ) );
	es_check( 'AC-02: data-es-key مسیردار برای جمع‌آوری', false !== strpos( $html, 'data-es-key="social_links[0][url]"' ) );

	// AC-03: min/max are exposed.
	es_check( 'AC-03: data-min اعمال شده', false !== strpos( $html, 'data-min="0"' ) );
	es_check( 'AC-03: data-max اعمال شده', false !== strpos( $html, 'data-max="8"' ) );

	// AC-01: add/remove/reorder + stable reindex in JS; prototype template present.
	es_check( 'AC-01: قالب آیتم جدید با __INDEX__', false !== strpos( $html, '__INDEX__' ) && false !== strpos( $html, 'data-es-repeater-template' ) );
	es_check( 'AC-01: JS افزودن آیتم', false !== strpos( $repeater_js, 'data-es-repeater-add' ) );
	es_check( 'AC-01: JS حذف آیتم', false !== strpos( $repeater_js, 'data-es-repeater-remove' ) );
	es_check( 'AC-01: JS جابجایی بالا/پایین', false !== strpos( $repeater_js, 'data-es-repeater-up' ) && false !== strpos( $repeater_js, 'data-es-repeater-down' ) );
	es_check( 'AC-01/SC-02: JS بازایندکس پایدار', false !== strpos( $repeater_js, 'reindex' ) );
	es_check( 'AC-03: JS حداقل/حداکثر را اعمال می‌کند', false !== strpos( $repeater_js, 'data-min' ) && false !== strpos( $repeater_js, 'data-max' ) );

	// SC-02: removing the middle item leaves the others intact.
	$after_remove = FieldRenderer::render_field( 'social_links', array( $items[0], $items[2] ) );
	es_check( 'SC-02: حذف آیتم وسط، بقیه را خراب نمی‌کند', false !== strpos( $after_remove, 'https://instagram.com/a' ) && false !== strpos( $after_remove, 'https://linkedin.com/c' ) && false === strpos( $after_remove, 'https://t.me/b' ) );
	es_check( 'SC-02: ایندکس‌ها پس از حذف پیوسته‌اند', false !== strpos( $after_remove, 'data-index="0"' ) && false !== strpos( $after_remove, 'data-index="1"' ) && false === strpos( $after_remove, 'data-index="2"' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
