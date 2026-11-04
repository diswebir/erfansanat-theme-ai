<?php
/**
 * Emits a minimal dashboard skeleton with a real link field + a text field +
 * the save bar, for the ADMIN-PRO JSDOM test (link-save bug fix).
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\FieldRenderer;

if ( ! es_load_wp() ) {
	fwrite( STDERR, "WP load failed\n" );
	exit( 1 );
}
require_once ES_THEME . '/functions.php';

$link_ref = new ReflectionMethod( FieldRenderer::class, 'link' );
$link_ref->setAccessible( true );
$link_html = $link_ref->invoke(
	null,
	'es-field-footer_link',
	'es_options[footer_link]',
	'footer_link',
	array( 'url' => 'https://old.example', 'label' => 'قدیمی', 'target' => '_self' ),
	array()
);

echo '<div class="wrap es-dashboard" dir="rtl" data-es-dashboard="true">';
echo '<div class="es-panels"><section role="tabpanel" id="es-panel-footer" class="es-panel" data-es-panel="footer">';
echo '<div class="es-fields">';
echo $link_html; // phpcs:ignore WordPress.Security.EscapeOutput -- built escaped.
echo '<div class="es-field"><label class="es-field__label" for="es-field-brand_name">نام برند</label><input type="text" id="es-field-brand_name" name="es_options[brand_name]" data-es-key="brand_name" value="عرفان صنعت" /></div>';
echo '</div>';
echo '</section></div>';
echo '<div class="es-save-bar" data-es-save-bar="true"><button type="button" class="button button-primary es-save" data-es-save="true">ذخیره تغییرات</button><span class="es-save-status" role="status" data-es-save-status="true"></span></div>';
echo '</div>';
