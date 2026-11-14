<?php
/**
 * P02-T006 — فیلدهای Media/Link/Icon
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-006.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\FieldRenderer;

es_task( 'P02-T006', 'فیلدهای Media/Link/Icon' );

es_check( 'target: inc/Admin/FieldRenderer.php', es_file( 'inc/Admin/FieldRenderer.php' ) );
es_check( 'target: assets/admin/media-field.js', es_file( 'assets/admin/media-field.js' ) );

$media_js = (string) file_get_contents( ES_THEME . '/assets/admin/media-field.js' );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// AC-01 / SC-01: media field stores attachment ID and offers select/remove.
	$media = FieldRenderer::render_field( 'logo_id', 123 );
	es_check( 'AC-01: media شناسه پیوست را ذخیره می‌کند', false !== strpos( $media, 'value="123"' ) && false !== strpos( $media, 'data-es-key="logo_id"' ) );
	es_check( 'SC-01: دکمه انتخاب رسانه وجود دارد', false !== strpos( $media, 'data-es-media-select' ) );
	es_check( 'SC-01: دکمه حذف رسانه وجود دارد', false !== strpos( $media, 'data-es-media-remove' ) );
	$media_empty = FieldRenderer::render_field( 'logo_id', 0 );
	es_check( 'SC-01: بدون رسانه دکمه حذف پنهان است', false !== strpos( $media_empty, 'data-es-media-remove hidden' ) );
	es_check( 'SC-01: JS از wp.media برای انتخاب استفاده می‌کند', false !== strpos( $media_js, 'wp.media' ) );
	es_check( 'SC-01: JS حذف رسانه را هندل می‌کند', false !== strpos( $media_js, 'data-es-media-remove' ) );

	// AC-02 / SC-02: link normalization + safe URL.
	$blank = FieldRenderer::normalize_link( array( 'url' => 'https://example.com', 'target' => '_blank' ) );
	es_check( 'AC-02: target=_blank با rel امن', '_blank' === $blank['target'] && 'noopener noreferrer' === $blank['rel'] );
	$self = FieldRenderer::normalize_link( array( 'url' => 'https://example.com', 'target' => '_self' ) );
	es_check( 'AC-02: target=_self بدون rel اضافی', '_self' === $self['target'] && '' === $self['rel'] );
	$evil_target = FieldRenderer::normalize_link( array( 'url' => 'https://example.com', 'target' => 'evil' ) );
	es_check( 'AC-02: target نامعتبر به _self برمی‌گردد', '_self' === $evil_target['target'] );
	es_check( 'SC-02: javascript: رد می‌شود', '' === FieldRenderer::safe_url( 'javascript:alert(1)' ) );
	es_check( 'SC-02: data: رد می‌شود', '' === FieldRenderer::safe_url( 'data:text/html,x' ) );
	es_check( 'AC-02: لینک https سالم می‌ماند', 'https://example.com' === FieldRenderer::safe_url( 'https://example.com' ) );
	es_check( 'AC-02: mailto سالم می‌ماند', 'mailto:info@erfansanat.com' === FieldRenderer::safe_url( 'mailto:info@erfansanat.com' ) );

	// Link renderer (reflection — no standalone link schema key yet).
	$link_ref = new ReflectionMethod( FieldRenderer::class, 'link' );
	$link_ref->setAccessible( true );
	$link_html = $link_ref->invoke( null, 'es-field-lnk', 'es_options[lnk]', 'lnk', array( 'url' => 'https://x.com', 'label' => 'کلیک', 'target' => '_blank' ), array() );
	es_check( 'AC-02: فیلد لینک url/label/target دارد', false !== strpos( $link_html, 'data-es-link-url' ) && false !== strpos( $link_html, 'data-es-link-label' ) && false !== strpos( $link_html, 'data-es-link-target' ) );

	// AC-03: icon only from the allowed set.
	$allowed = FieldRenderer::allowed_icons();
	es_check( 'AC-03: مجموعه آیکون مجاز تعریف شده', ! empty( $allowed ) && isset( $allowed['instagram'] ) );
	$icon_ref = new ReflectionMethod( FieldRenderer::class, 'icon' );
	$icon_ref->setAccessible( true );
	$icon_html = $icon_ref->invoke( null, 'es-field-ic', 'es_options[ic]', 'ic', 'instagram', array() );
	es_check( 'AC-03: آیکون مجاز انتخاب می‌شود', false !== strpos( $icon_html, 'value="instagram" selected' ) );
	$icon_bad = $icon_ref->invoke( null, 'es-field-ic', 'es_options[ic]', 'ic', 'evil_icon', array() );
	es_check( 'AC-03: آیکون غیرمجاز انتخاب نمی‌شود', false === strpos( $icon_bad, 'value="evil_icon"' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
