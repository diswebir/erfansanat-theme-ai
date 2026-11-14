<?php
/**
 * Admin dashboard professional upgrade — overview panel, link-save bug fix,
 * comprehensive Persian font and unsaved-changes guard.
 * Run: php temp/phase-02-custom-admin-dashboard/tests/test-admin-dashboard-pro.php
 * DOM behaviour: test-admin-dashboard-pro-dom.mjs
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Admin\DashboardOverview;
use ErfanSanat\Theme\Admin\FieldRenderer;
use ErfanSanat\Theme\Admin\ThemeOptions;
use ErfanSanat\Theme\Options\Schema;

es_task( 'ADMIN-PRO', 'ارتقای حرفه‌ای داشبورد ادمین' );

es_check( 'target: DashboardOverview.php', es_file( 'inc/Admin/DashboardOverview.php' ) );
es_check( 'target: assets/admin/font.css', es_file( 'assets/admin/font.css' ) );

// ── Persian font completeness (source) ───────────────────────────────────────.
$font = (string) file_get_contents( ES_THEME . '/assets/admin/font.css' );
es_check( 'فونت: هم‌پوشانی ZWNJ رفع شد (latin بدون 200C-200E)', false !== strpos( $font, 'U+2000-200B, U+200F-206F' ) );
es_check( 'فونت: پوشش پنل پیشخوان', false !== strpos( $font, '.es-overview' ) && false !== strpos( $font, '.es-stat-card__value' ) );
es_check( 'فونت: رندرینگ خواناتر فارسی', false !== strpos( $font, 'optimizeLegibility' ) && false !== strpos( $font, 'antialiased' ) );
es_check( 'فونت: اعداد آماری tabular', false !== strpos( $font, 'font-variant-numeric: tabular-nums' ) );

// ── Unsaved-changes guard + link collection (source) ─────────────────────────.
$js = (string) file_get_contents( ES_THEME . '/assets/admin/options.js' );
es_check( 'JS: جمع‌آوری گروه لینک', false !== strpos( $js, 'collectLinkGroups' ) && false !== strpos( $js, 'data-es-link-key' ) );
es_check( 'JS: هشدار تغییرات ذخیره‌نشده', false !== strpos( $js, 'beforeunload' ) && false !== strpos( $js, 'dirty' ) );
es_check( 'ThemeOptions پیشخوان را رندر می‌کند', es_contains( 'inc/Admin/ThemeOptions.php', 'DashboardOverview::render()' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	wp_set_current_user( 1 );

	// ── Overview tab wiring ──────────────────────────────────────────────────.
	$tabs = Schema::tabs();
	es_check( 'پیشخوان اولین تب است', array_key_first( $tabs ) === 'overview' );
	es_check( 'تب پیشخوان عنوان/آیکون دارد', isset( $tabs['overview']['title'] ) && 'پیشخوان' === $tabs['overview']['title'] && ! empty( $tabs['overview']['icon'] ) );

	// ── Link-save bug fix (renderer) ─────────────────────────────────────────.
	$link_ref = new ReflectionMethod( FieldRenderer::class, 'link' );
	$link_ref->setAccessible( true );
	$link_html = $link_ref->invoke( null, 'es-field-lnk', 'es_options[lnk]', 'lnk', array( 'url' => 'https://x.com', 'label' => 'کلیک', 'target' => '_blank' ), array() );
	es_check( 'باگ لینک: گروه data-es-link-key دارد', false !== strpos( $link_html, 'data-es-link-key="lnk"' ) );
	es_check( 'باگ لینک: ورودی url دیگر data-es-key تکی ندارد', false === strpos( $link_html, 'data-es-key="lnk"' ) );
	es_check( 'باگ لینک: url/label/target حفظ شده‌اند', false !== strpos( $link_html, 'data-es-link-url' ) && false !== strpos( $link_html, 'data-es-link-label' ) && false !== strpos( $link_html, 'data-es-link-target' ) );

	// ── Overview helpers ─────────────────────────────────────────────────────.
	es_check( 'fa_digits: تبدیل ارقام فارسی', '۱۲۳۴۵۶۷۸۹۰' === DashboardOverview::fa_digits( '1234567890' ) );

	$fix   = array();
	$fix[] = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه تست', 'post_name' => 'ov-p-' . uniqid() ) );
	$fix[] = (int) wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'pending', 'post_title' => 'پروژه در انتظار', 'post_name' => 'ov-p2-' . uniqid() ) );
	$fix[] = (int) wp_insert_post( array( 'post_type' => 'services', 'post_status' => 'publish', 'post_title' => 'خدمت تست', 'post_name' => 'ov-s-' . uniqid() ) );
	$fix[] = (int) wp_insert_post( array( 'post_type' => 'post', 'post_status' => 'publish', 'post_title' => 'نوشته تست', 'post_name' => 'ov-b-' . uniqid() ) );

	$stats = DashboardOverview::stats();
	es_check( 'آمار: نوع پروژه شمارش شد', isset( $stats['projects'] ) && $stats['projects']['publish'] >= 1 && $stats['projects']['pending'] >= 1 );
	es_check( 'آمار: لینک ویرایش/افزودن دارد', ! empty( $stats['projects']['edit_link'] ) && ! empty( $stats['projects']['add_link'] ) );
	es_check( 'آمار: خدمت و نوشته شمارش شد', isset( $stats['services'] ) && isset( $stats['post'] ) );

	$system = DashboardOverview::system_info();
	es_check( 'وضعیت سیستم: ردیف‌های کامل', count( $system ) >= 6 && isset( $system[0]['label'], $system[0]['value'], $system[0]['status'] ) );

	// ── Full dashboard render ────────────────────────────────────────────────.
	ob_start();
	ThemeOptions::render_dashboard();
	$html = ob_get_clean();
	es_check( 'رندر: پنل پیشخوان حاضر است', false !== strpos( $html, 'data-es-overview="true"' ) );
	es_check( 'رندر: کارت‌های آماری', substr_count( $html, 'es-stat-card' ) >= 4 );
	es_check( 'رندر: دسترسی سریع', false !== strpos( $html, 'es-quick-action' ) );
	es_check( 'رندر: جدول وضعیت سیستم', false !== strpos( $html, 'es-status-table' ) );
	es_check( 'رندر: پیشخوان تب انتخاب‌شده است', false !== strpos( $html, 'id="es-tab-overview"' ) && 1 === preg_match_all( '/aria-selected="true"/', $html ) );
	es_check( 'رندر: اعداد فارسی در کارت آمار', 1 === preg_match( '/es-stat-card__value">[۰-۹]+</u', $html ) );

	foreach ( $fix as $pid ) {
		wp_delete_post( $pid, true );
	}
} else {
	es_check( 'بارگذاری WordPress', false );
}

es_summary();
