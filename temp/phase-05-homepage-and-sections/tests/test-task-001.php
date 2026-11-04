<?php
/**
 * P05-T001 — Controller صفحه اصلی
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-001.php
 *
 * Section parts are sliced in P05-T007..T019; until then the Renderer emits its
 * safe structural fallback (data-es-section-type), so ordering/enable logic is
 * fully testable now. One full-page render (single wp_footer per request).
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;
use ErfanSanat\Theme\Sections\Schema;
use ErfanSanat\Theme\Sections\SectionManager;

es_task( 'P05-T001', 'Controller صفحه اصلی' );

es_check( 'target: front-page.php', es_file( 'front-page.php' ) );
es_check( 'AC-01: حلقه Renderer فراخوانی می‌شود', es_contains( 'front-page.php', 'Renderer::render' ) );
es_check( 'ساختار: get_header/get_footer و main', es_contains( 'front-page.php', 'get_header()' ) && es_contains( 'front-page.php', 'get_footer()' ) && es_contains( 'front-page.php', '<main>' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	// Home page fixture + front-page options (saved/restored).
	$pid          = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'خانه تست T001', 'post_name' => 'home-t001' ) );
	$old_show     = get_option( 'show_on_front' );
	$old_front    = get_option( 'page_on_front' );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $pid );

	$types_in = static function ( $html ) {
		preg_match_all( '/data-es-section-type="([^"]+)"/', $html, $m );
		return $m[1];
	};
	$default_types = wp_list_pluck( Schema::defaults( 'home' ), 'type' );

	// ── AC-01: default order = 13 content sections ────────────────────────.
	es_check( 'AC-01: schema خانه ۱۳ سکشن دارد', 13 === count( $default_types ) );
	$html = Renderer::render( $pid, 'home' );
	es_check( 'AC-01: ترتیب پیش‌فرض ۱۳ سکشن به‌ترتیب schema', $types_in( $html ) === $default_types );
	es_check( 'AC-01: context خانه خودکار تشخیص داده می‌شود', 'home' === SectionManager::context_for( $pid ) );

	// ── AC-02: a disabled section is not rendered ─────────────────────────.
	$sections = Schema::defaults( 'home' );
	foreach ( $sections as $i => $s ) {
		if ( 'history' === $s['type'] ) {
			$sections[ $i ]['enabled'] = false;
		}
	}
	SectionManager::save_sections( $pid, $sections, 'home' );
	$html = Renderer::render( $pid, 'home' );
	es_check( 'AC-02: سکشن خاموش رندر نمی‌شود', ! in_array( 'history', $types_in( $html ), true ) && 12 === count( $types_in( $html ) ) );

	// ── AC-03 / SC-02: admin order is applied (swap two sections) ─────────.
	$sections = Schema::defaults( 'home' );
	// swap hero (0) and project-types (1).
	$tmp          = $sections[0];
	$sections[0]  = $sections[1];
	$sections[1]  = $tmp;
	SectionManager::save_sections( $pid, $sections, 'home' );
	$html    = Renderer::render( $pid, 'home' );
	$ordered = $types_in( $html );
	es_check( 'SC-02: جابجایی دو سکشن در خروجی دیده می‌شود', 'project-types' === $ordered[0] && 'hero' === $ordered[1] );
	es_check( 'AC-03: بقیه ترتیب حفظ می‌شود', array_slice( $ordered, 2 ) === array_slice( $default_types, 2 ) );

	// Restore defaults for the full-page render.
	delete_post_meta( $pid, Schema::meta_key() );
	Renderer::invalidate( $pid );

	// ── SC-01: full-page structural parity (single render) ────────────────.
	$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => $pid ) );
	ob_start();
	include ES_THEME . '/front-page.php';
	$page = (string) ob_get_clean();

	es_check( 'SC-01: هدر کامل رندر شد', false !== strpos( $page, 'data-navbar-header="true"' ) );
	es_check( 'SC-01: main با ۱۳ سکشن پیش‌فرض', false !== strpos( $page, '<main>' ) && $types_in( $page ) === $default_types );
	es_check( 'SC-01: CTA جهانی تماس حاضر است', false !== strpos( $page, 'id="contact"' ) );
	es_check( 'SC-01: فوتر جهانی حاضر است', false !== strpos( $page, '<footer' ) );
	es_check( 'SC-01: CTA/فوتر در front-page تکرار نشده‌اند', 1 === substr_count( $page, 'id="contact"' ) && 1 === substr_count( $page, '<footer' ) );

	// Cleanup.
	update_option( 'show_on_front', $old_show );
	update_option( 'page_on_front', $old_front );
	wp_delete_post( $pid, true );
	$GLOBALS['wp_query'] = new WP_Query();
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
