<?php
/**
 * P02-T018 — SectionManager: API رندر و کش
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;
use ErfanSanat\Theme\Sections\SectionManager;

es_task( 'P02-T018', 'SectionManager: API رندر و کش' );

es_check( 'target: inc/Sections/Renderer.php', es_file( 'inc/Sections/Renderer.php' ) );
es_check( 'AC-02: از locate_template برای بارگذاری امن استفاده می‌شود', es_contains( 'inc/Sections/Renderer.php', 'locate_template' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	Renderer::boot();
	$page_id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'صفحه رندر' ) );

	$sec = function ( string $type, bool $enabled = true ) {
		return array( 'id' => $type, 'type' => $type, 'enabled' => $enabled, 'fields' => array() );
	};

	// SC-01: output order matches the stored meta order.
	SectionManager::save_sections( $page_id, array( $sec( 'hero' ), $sec( 'about' ), $sec( 'clients' ) ), 'home' );
	$out      = Renderer::render( $page_id, 'home' );
	$p_hero   = strpos( $out, 'data-es-section="hero"' );
	$p_about  = strpos( $out, 'data-es-section="about"' );
	$p_client = strpos( $out, 'data-es-section="clients"' );
	es_check( 'SC-01: ترتیب خروجی با متا برابر است', false !== $p_hero && $p_hero < $p_about && $p_about < $p_client );

	// AC-01: disabled sections are skipped, enabled ones remain.
	SectionManager::save_sections( $page_id, array( $sec( 'hero' ), $sec( 'about', false ), $sec( 'clients' ) ), 'home' );
	$out2 = Renderer::render( $page_id, 'home' );
	es_check( 'AC-01: سکشن غیرفعال رندر نمی‌شود', false === strpos( $out2, 'data-es-section="about"' ) && false !== strpos( $out2, 'data-es-section="hero"' ) );

	// AC-02: missing template parts fall back without a fatal.
	es_check( 'AC-02: template-part ناموجود fatal نمی‌دهد', is_string( $out2 ) && '' !== $out2 );

	// AC-03: saving invalidates the cache so a reorder is reflected.
	SectionManager::save_sections( $page_id, array( $sec( 'clients' ), $sec( 'hero' ) ), 'home' );
	$out3 = Renderer::render( $page_id, 'home' );
	es_check( 'AC-03: پس از ذخیره کش invalid می‌شود', strpos( $out3, 'data-es-section="clients"' ) < strpos( $out3, 'data-es-section="hero"' ) );

	// Cache is populated after render and cleared on save.
	$prop = new ReflectionProperty( Renderer::class, 'cache' );
	$prop->setAccessible( true );
	$cache = $prop->getValue();
	es_check( 'کش پس از رندر پر می‌شود', isset( $cache[ $page_id ]['home'] ) );
	SectionManager::save_sections( $page_id, array( $sec( 'hero' ) ), 'home' );
	$cache2 = $prop->getValue();
	es_check( 'AC-03: کش پس از ذخیره پاک می‌شود', ! isset( $cache2[ $page_id ] ) );

	// SC-02: an unknown section type is ignored by the renderer.
	$method = new ReflectionMethod( Renderer::class, 'render_section' );
	$method->setAccessible( true );
	$unknown = $method->invoke( null, array( 'id' => 'x', 'type' => 'totally_unknown', 'enabled' => true, 'fields' => array() ), 'home' );
	es_check( 'SC-02: سکشن ناشناخته نادیده گرفته می‌شود', '' === $unknown );

	// Fallback escapes the heading (no raw HTML injected).
	SectionManager::save_sections( $page_id, array( array( 'id' => 'hero', 'type' => 'hero', 'enabled' => true, 'fields' => array( 'heading' => '<b>bold</b>' ) ) ), 'home' );
	$out4 = Renderer::render( $page_id, 'home' );
	es_check( 'fallback: عنوان escape می‌شود', false === strpos( $out4, '<b>bold</b>' ) && false !== strpos( $out4, '&lt;b&gt;' ) );

	wp_delete_post( $page_id, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
