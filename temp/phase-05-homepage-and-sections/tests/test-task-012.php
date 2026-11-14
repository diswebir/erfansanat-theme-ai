<?php
/**
 * P05-T012 — سکشن تاریخچه و دستاوردها (تایم‌لاین)
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-012.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T012', 'سکشن تاریخچه و دستاوردها' );

es_check( 'target: بخش تاریخچه ساخته شده', es_file( 'template-parts/sections/home/section-history.php' ) );
es_check( 'AC-01: data-history-scroll-track در قالب', es_contains( 'template-parts/sections/home/section-history.php', 'data-history-scroll-track' ) );
es_check( 'AC-01: data-history-total در قالب', es_contains( 'template-parts/sections/home/section-history.php', 'data-history-total' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'history', 'type' => 'history', 'enabled' => true, 'fields' => $fields ), 'home' );
	};

	// ── AC-01 / AC-03: default render carries all main.js hooks ───────────.
	$html = $render();
	es_check( 'AC-01: data-history-scroll-track=true رندر می‌شود', false !== strpos( $html, 'data-history-scroll-track="true"' ) );
	es_check( 'AC-01: data-history-total پیش‌فرض ۶ است', false !== strpos( $html, 'data-history-total="6"' ) );
	$hooks = array( 'data-history-stage="true"', 'data-history-step-label="true"', 'data-history-progress-bar="true"', 'data-home2-history-btn="0"', 'data-home2-history-panel="0"', 'data-home2-history-prev="true"', 'data-home2-history-next="true"' );
	$missing = array();
	foreach ( $hooks as $h ) { if ( false === strpos( $html, $h ) ) { $missing[] = $h; } }
	es_check( 'AC-03: همهٔ قلاب‌های تایم‌لاین main.js موجودند', empty( $missing ) );
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html, 'data-es-section="history"' ) );
	es_check( 'AC-02: آیتم پیش‌فرض رندر می‌شود (۱۳۸۰)', false !== strpos( $html, '۱۳۸۰' ) && false !== strpos( $html, 'فاز تاسیس صنعتی' ) );

	// ── SC-02: total counter equals item count ────────────────────────────.
	$custom = array(
		array( 'year' => '۱۴۰۰', 'title' => 'رویداد یک', 'description' => 'توضیح یک' ),
		array( 'year' => '۱۴۱', 'title' => 'رویداد دو', 'description' => 'توضیح دو' ),
		array( 'year' => '۱۴۰۲', 'title' => 'رویداد سه', 'description' => 'توضیح سه' ),
	);
	$html2 = $render( array( 'timeline' => $custom ) );
	es_check( 'SC-02: شمارنده کل با تعداد آیتم برابر است', false !== strpos( $html2, 'data-history-total="3"' ) );
	es_check( 'SC-02: گام‌ها با تعداد آیتم برابرند', 3 === substr_count( $html2, 'data-home2-history-btn=' ) && 3 === substr_count( $html2, 'data-home2-history-panel=' ) );

	// ── SC-01: adding an item renders it ──────────────────────────────────.
	$custom[] = array( 'year' => '۱۴۰۳', 'title' => 'رویداد چهارم افزوده', 'description' => 'توضیح چهارم' );
	$html3 = $render( array( 'timeline' => $custom ) );
	es_check( 'SC-01: افزودن آیتم تاریخچه رندر می‌شود', false !== strpos( $html3, 'رویداد چهارم افزوده' ) && false !== strpos( $html3, 'data-history-total="4"' ) );
	es_check( 'AC-02: عنوان/توضیح آیتم از repeater می‌آید', false !== strpos( $html3, 'رویداد یک' ) && false !== strpos( $html3, 'توضیح یک' ) );

	// ── AC-03: first panel visible, others hidden (scroll state) ──────────.
	es_check( 'AC-03: پنل اول grid و بقیه hidden هستند', false !== strpos( $html3, 'data-home2-history-panel="0" class="grid' ) && false !== strpos( $html3, 'data-home2-history-panel="1" class="hidden' ) );

	// ── Empty timeline falls back to defaults, no fatal ───────────────────.
	$html4 = $render( array( 'timeline' => array() ) );
	es_check( 'حالت خالی fatal نمی‌دهد و به پیش‌فرض بازمی‌گردد', false !== strpos( $html4, 'data-history-total="6"' ) && false !== strpos( $html4, 'data-es-section="history"' ) );

	// ── Heading escaped ───────────────────────────────────────────────────.
	$html5 = $render( array( 'heading' => '<b>bold</b>' ) );
	es_check( 'عنوان escape می‌شود', false === strpos( $html5, '<b>bold</b>' ) && false !== strpos( $html5, '&lt;b&gt;' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
