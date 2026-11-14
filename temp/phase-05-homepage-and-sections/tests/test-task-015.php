<?php
/**
 * P05-T015 — سکشن تب‌های خدمات تخصصی
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-015.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T015', 'سکشن تب‌های خدمات تخصصی' );

es_check( 'target: بخش خدمات ساخته شده', es_file( 'template-parts/sections/home/section-services.php' ) );
es_check( 'AC-01: id=services در قالب', es_contains( 'template-parts/sections/home/section-services.php', 'id="services"' ) );
es_check( 'AC-01: data-services-section در قالب', es_contains( 'template-parts/sections/home/section-services.php', 'data-services-section="true"' ) );
es_check( 'AC-02: کوئری CPT خدمات در قالب', es_contains( 'template-parts/sections/home/section-services.php', "'post_type'      => 'services'" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}
	if ( ! post_type_exists( 'services' ) ) {
		register_post_type( 'services', array( 'public' => true, 'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes' ) ) );
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'services', 'type' => 'services', 'enabled' => true, 'fields' => $fields ), 'home' );
	};

	// Fixtures: 5 published services (menu_order 1..5) + 1 draft.
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'svc', 'post_mime_type' => 'image/jpeg' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/service.jpg' );
	$ids = array();
	for ( $i = 1; $i <= 5; $i++ ) {
		$p = wp_insert_post( array(
			'post_type'    => 'services',
			'post_status'  => 'publish',
			'post_title'   => 'خدمت شماره ' . $i,
			'post_excerpt' => 'توضیح خدمت ' . $i . ' برای تست.',
			'menu_order'   => $i,
		) );
		set_post_thumbnail( $p, $att );
		$ids[] = $p;
	}
	$draft = wp_insert_post( array( 'post_type' => 'services', 'post_status' => 'draft', 'post_title' => 'خدمت پیش‌نویس مخفی' ) );

	// ── AC-01 / SC-01: five services -> five rows ─────────────────────────.
	$html = $render();
	es_check( 'AC-01: id=services حفظ شده', false !== strpos( $html, 'id="services"' ) );
	es_check( 'AC-01: data-services-section حفظ شده', false !== strpos( $html, 'data-services-section="true"' ) );
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html, 'data-es-section="services"' ) );
	es_check( 'SC-01: پنج خدمت پنج ردیف می‌سازند', 5 === preg_match_all( '/data-service-row="\d+"/', $html ) );
	es_check( 'AC-02: خدمت پیش‌نویس نمایش داده نمی‌شود', false === strpos( $html, 'خدمت پیش‌نویس مخفی' ) );
	es_check( 'ترتیب menu_order رعایت شده (خدمت ۱ اول)', strpos( $html, 'خدمت شماره 1' ) < strpos( $html, 'خدمت شماره 2' ) );

	// ── AC-03: every initHomeServices selector exists in the DOM ──────────.
	$js = file_get_contents( ES_THEME . '/assets/js/main.js' );
	$fn_start = strpos( $js, 'data-services-section' );
	$fn_region = substr( $js, $fn_start, 6500 );
	preg_match_all( '/data-service[\w-]*/', $fn_region, $m );
	$selectors = array_values( array_unique( $m[0] ) );
	$missing = array();
	foreach ( $selectors as $sel ) {
		if ( false === strpos( $html, $sel ) ) {
			$missing[] = $sel;
		}
	}
	es_check( 'AC-03: همه هوک‌های initHomeServices در DOM هستند (' . count( $selectors ) . ' سلکتور)', array() === $missing );

	// ── SC-02: rows carry the data the preview switch reads ───────────────.
	es_check( 'SC-02: data-service-title روی ردیف‌ها', 5 === preg_match_all( '/data-service-title="[^"]+"/', $html ) );
	es_check( 'SC-02: data-service-desc-text روی ردیف‌ها', 5 === preg_match_all( '/data-service-desc-text="[^"]+"/', $html ) );
	es_check( 'SC-02: data-service-img روی ردیف‌ها', 5 === preg_match_all( '/data-service-img="[^"]+"/', $html ) );
	es_check( 'SC-02: شمارنده فارسی ردیف اول ۱ / ۵', false !== strpos( $html, 'data-service-counter="۱ / ۵"' ) );
	es_check( 'شمارنده آخرین ردیف ۵ / ۵', false !== strpos( $html, 'data-service-counter="۵ / ۵"' ) );
	es_check( 'پیش‌نمایش عنوان خدمت اول', false !== strpos( $html, 'data-service-preview-title="true" class="text-xl font-black text-white">خدمت شماره 1' ) );
	es_check( 'آیکون پیش‌نمایش: اول block بقیه hidden', 1 === preg_match_all( '/data-service-preview-icon="\d+" class="block"/', $html ) && 4 === preg_match_all( '/data-service-preview-icon="\d+" class="hidden"/', $html ) );

	// ── Initial active/inactive state matches the mirror ──────────────────.
	es_check( 'ردیف اول فعال (border-emerald-500/40)', false !== strpos( $html, 'border-emerald-500/40 bg-white shadow-sm' ) );
	es_check( 'نشانگر ردیف اول h-10 و بقیه h-0', 1 === preg_match_all( '/data-service-indicator="\d+"[^>]*h-10"/', $html ) && 4 === preg_match_all( '/data-service-indicator="\d+"[^>]*h-0"/', $html ) );
	es_check( 'تصویر موبایل: اول block بقیه hidden', 1 === preg_match_all( '/data-service-mobile-img="\d+" class="block/', $html ) && 4 === preg_match_all( '/data-service-mobile-img="\d+" class="hidden/', $html ) );

	// ── Empty state + escaping ────────────────────────────────────────────.
	foreach ( $ids as $p ) { wp_delete_post( $p, true ); }
	wp_delete_post( $draft, true );
	$html_empty = $render();
	es_check( 'حالت خالی DOM را نمی‌شکند', false !== strpos( $html_empty, 'data-services-section="true"' ) && false !== strpos( $html_empty, 'خدمتی برای نمایش وجود ندارد' ) );
	$html_esc = $render( array( 'heading' => '<b>bold</b>' ) );
	es_check( 'عنوان escape می‌شود', false === strpos( $html_esc, '<b>bold</b>' ) && false !== strpos( $html_esc, '&lt;b&gt;' ) );

	wp_delete_post( $att, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
