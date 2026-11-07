<?php
/**
 * P07-T028 — Arya landing: editable hero/benefits/specs/FAQ/CTA sections,
 * working landing scroll menu, no new design system.
 * Run: php temp/phase-07-projects-blog-and-pages/tests/test-task-028.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\NavWalkers\LandingWalker;
use ErfanSanat\Theme\Sections\Renderer;
use ErfanSanat\Theme\Sections\Schema;
use ErfanSanat\Theme\Sections\SectionManager;

es_task( 'P07-T028', 'لندینگ آریا' );

es_check( 'target: page-templates/landing-arya.php', es_file( 'page-templates/landing-arya.php' ) );
es_check( 'سربرگ Template Name', es_contains( 'page-templates/landing-arya.php', 'Template Name:' ) );
es_check( 'AC-03: بدون استایل/اسکریپت جدید در کنترلر', ! es_contains( 'page-templates/landing-arya.php', 'wp_register_style' ) && ! es_contains( 'page-templates/landing-arya.php', '<style' ) && ! es_contains( 'page-templates/landing-arya.php', 'wp_register_script' ) );
es_check( 'پارت‌های پنج‌گانه آریا موجودند', es_file( 'template-parts/sections/arya/section-hero.php' ) && es_file( 'template-parts/sections/arya/section-features.php' ) && es_file( 'template-parts/sections/arya/section-specs.php' ) && es_file( 'template-parts/sections/arya/section-faq.php' ) && es_file( 'template-parts/sections/arya/section-cta.php' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	$arya = Schema::sections( 'arya' );
	es_check( 'AC-01: سکشن‌های specs و faq به schema آریا اضافه شدند', isset( $arya['specs'], $arya['faq'], $arya['hero'], $arya['features'], $arya['cta'] ) );

	$pid = (int) wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'آریا', 'post_name' => 'arya-028-' . uniqid() ) );
	update_post_meta( $pid, '_wp_page_template', 'page-templates/landing-arya.php' );
	es_check( 'نگاشت قالب به context آریا', 'arya' === SectionManager::context_for( $pid ) );

	$render = static function ( $pid ) {
		$q = new WP_Query( array( 'page_id' => $pid, 'post_type' => 'page' ) );
		$GLOBALS['wp_query'] = $q; $GLOBALS['wp_the_query'] = $q; $q->the_post();
		ob_start();
		include ES_THEME . '/page-templates/landing-arya.php';
		return ob_get_clean();
	};

	$old_locs = get_theme_mod( 'nav_menu_locations', array() );
	$locs = $old_locs; unset( $locs['landing'] ); set_theme_mod( 'nav_menu_locations', $locs );

	$html = $render( $pid );
	es_check( 'SC-02: هیرو پیش‌فرض رندر می‌شود', false !== strpos( $html, 'درب بازکن هوشمند آریا' ) );
	es_check( 'AC-01: هر پنج سکشن با شناسه رندر می‌شوند', false !== strpos( $html, 'id="hero"' ) && false !== strpos( $html, 'id="features"' ) && false !== strpos( $html, 'id="specs"' ) && false !== strpos( $html, 'id="faq"' ) && false !== strpos( $html, 'id="cta"' ) );
	es_check( 'مرجع: CTA تماس گلوبال فقط یک‌بار (فوتر، رندر اول)', 1 === substr_count( $html, 'id="contact"' ) );
	es_check( 'AC-02: منوی fallback لندینگ با data-landing-link', false !== strpos( $html, 'data-landing-menu="true"' ) && false !== strpos( $html, 'data-landing-link="true"' ) );

	// SC-01: every landing anchor resolves to a real section id on the page.
	preg_match_all( '/href="#([a-z-]+)" data-landing-link="true"/', $html, $m1 );
	$anchors_ok = ! empty( $m1[1] );
	foreach ( $m1[1] as $anchor ) {
		if ( false === strpos( $html, 'id="' . $anchor . '"' ) ) { $anchors_ok = false; }
	}
	es_check( 'SC-01: همه لنگرهای منو به سکشن موجود اشاره می‌کنند', $anchors_ok );

	// SC-02: hero edit is visible.
	SectionManager::save_section_fields( $pid, 'hero', array( 'heading' => 'آریای ویرایش‌شده T028' ), 'arya' );
	$html2 = $render( $pid );
	es_check( 'SC-02: ویرایش متن هیرو دیده می‌شود', false !== strpos( $html2, 'آریای ویرایش‌شده T028' ) );

	// AC-01: benefits/specs/FAQ/CTA edits are visible.
	SectionManager::save_section_fields( $pid, 'features', array( 'items' => array( array( 'title' => 'مزیت سفارشی', 'description' => 'شرح مزیت', 'icon' => 'check' ) ) ), 'arya' );
	SectionManager::save_section_fields( $pid, 'specs', array( 'rows' => array( array( 'label' => 'مشخصه سفارشی', 'value' => 'مقدار سفارشی' ) ) ), 'arya' );
	SectionManager::save_section_fields( $pid, 'faq', array( 'items' => array( array( 'question' => 'پرسش سفارشی؟', 'answer' => 'پاسخ سفارشی.' ) ) ), 'arya' );
	SectionManager::save_section_fields( $pid, 'cta', array( 'button_label' => 'دکمه سفارشی' ), 'arya' );
	$html3 = $render( $pid );
	es_check( 'AC-01: مزایا/مشخصات/FAQ/CTA ویرایش‌پذیرند', false !== strpos( $html3, 'مزیت سفارشی' ) && false !== strpos( $html3, 'مشخصه سفارشی' ) && false !== strpos( $html3, 'پرسش سفارشی؟' ) && false !== strpos( $html3, 'دکمه سفارشی' ) );
	es_check( 'قرارداد FAQ با main.js (data-faq-list/toggle/body)', false !== strpos( $html3, 'data-faq-list="true"' ) && false !== strpos( $html3, 'data-faq-toggle=' ) && false !== strpos( $html3, 'data-faq-body=' ) );

	// AC-02: assigned landing menu replaces the fallback.
	$menu_id = wp_update_nav_menu_object( 0, array( 'menu-name' => 'لندینگ T028' ) );
	wp_update_nav_menu_item( $menu_id, 0, array( 'menu-item-title' => 'مشخصات', 'menu-item-url' => '#specs', 'menu-item-type' => 'custom', 'menu-item-status' => 'publish' ) );
	$locs['landing'] = $menu_id; set_theme_mod( 'nav_menu_locations', $locs );
	$html4 = $render( $pid );
	es_check( 'AC-02: منوی اختصاصی جایگزین fallback می‌شود', 1 === substr_count( $html4, 'data-landing-menu="true"' ) && false !== strpos( $html4, '<a href="#specs" data-landing-link="true"' ) );
	set_theme_mod( 'nav_menu_locations', $old_locs );
	wp_delete_nav_menu( $menu_id );

	// Disabled section: not rendered and not linked in the fallback menu.
	$sections = SectionManager::get_sections( $pid, 'arya' );
	foreach ( $sections as $i => $s ) { if ( 'features' === $s['type'] ) { $sections[ $i ]['enabled'] = false; } }
	SectionManager::save_sections( $pid, $sections, 'arya' );
	$html5 = $render( $pid );
	es_check( 'سکشن غیرفعال نه رندر می‌شود نه در منو می‌آید', false === strpos( $html5, 'id="features"' ) && false === strpos( $html5, 'href="#features" data-landing-link' ) );

	// SC-01 dependency: native smooth scroll + scroll-spy contracts ship.
	es_check( 'SC-01: اسکرول نرم بومی + scroll-spy موجودند', es_contains( 'assets/css/styles.css', 'scroll-behavior:smooth' ) && es_contains( 'assets/js/navigation.js', '[data-landing-link]' ) );

	wp_reset_postdata();
	wp_delete_post( $pid, true );
} else {
	es_check( 'بارگذاری WordPress', false );
}
es_summary();
