<?php
/**
 * P05-T020 — یکپارچه‌سازی CTA/فوتر صفحه اصلی
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-020.php
 *
 * get_header()/get_footer() run ONCE per request in the harness, so this task
 * uses a single full-page render that reproduces front-page.php exactly
 * (header + Renderer::render(home) + footer) and asserts the end-of-page
 * structure: sections -> contact CTA -> footer(info) with no duplication.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T020', 'یکپارچه‌سازی CTA/فوتر صفحه اصلی' );

// ── Static: front-page.php delegates CTA/footer to get_footer() once ─────.
$front = (string) file_get_contents( ES_THEME . '/front-page.php' );
es_check( 'target: front-page.php موجود است', es_file( 'front-page.php' ) );
es_check( 'AC-02: get_footer دقیقاً یک‌بار', 1 === substr_count( $front, 'get_footer();' ) );
es_check( 'get_header دقیقاً یک‌بار', 1 === substr_count( $front, 'get_header();' ) );
es_check( 'AC-02: front-page خودش CTA را رندر نمی‌کند', false === strpos( $front, 'cta-contact' ) );
es_check( 'AC-02: front-page خودش footer-info را رندر نمی‌کند', false === strpos( $front, 'footer-info' ) );
es_check( 'AC-02: front-page تگ <footer> ندارد', false === strpos( $front, '<footer' ) );
es_check( 'AC-03: سکشن‌ها از Renderer::render می‌آیند', false !== strpos( $front, "Renderer::render( \$es_front_id, 'home' )" ) );
es_check( 'AC-01: footer.php بخش CTA را قبل از footer می‌آورد', es_contains( 'footer.php', "get_template_part( 'template-parts/global/cta-contact' )" ) );
es_check( 'AC-01: footer.php بخش اطلاعات فوتر را دارد', es_contains( 'footer.php', "get_template_part( 'template-parts/global/footer-info' )" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	// Front-page context: a static front page.
	$front_id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'خانه', 'post_name' => 'home' ) );
	$old_show = get_option( 'show_on_front' );
	$old_page = get_option( 'page_on_front' );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $front_id );

	// Single full-page render reproducing front-page.php.
	ob_start();
	get_header();
	echo Renderer::render( $front_id, 'home' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	get_footer();
	$html = (string) ob_get_clean();

	// ── AC-01 / AC-02: contact CTA + footer present, exactly once ─────────.
	es_check( 'AC-01: id=contact رندر شده', false !== strpos( $html, 'id="contact"' ) );
	es_check( 'AC-02: CTA تماس دقیقاً یک‌بار', 1 === substr_count( $html, 'id="contact"' ) );
	es_check( 'SC-02: تگ <footer> دقیقاً یک‌بار', 1 === substr_count( $html, '<footer' ) );
	es_check( 'AC-01: بخش اطلاعات فوتر رندر شده', false !== strpos( $html, 'اطلاعات تماس کارخانه' ) );
	es_check( 'SC-02: اطلاعات فوتر دوبار نیست', 1 === substr_count( $html, 'اطلاعات تماس کارخانه' ) );
	es_check( 'AC-01: محتوای CTA تماس', false !== strpos( $html, 'مشاوره مهندسی و استعلام قیمت' ) );

	// ── AC-03: end-of-page order matches the reference ────────────────────.
	$p_blog    = strpos( $html, 'id="blog-preview"' );
	$p_contact = strpos( $html, 'id="contact"' );
	$p_footer  = strpos( $html, '<footer' );
	$p_info    = strpos( $html, 'اطلاعات تماس کارخانه' );
	es_check( 'AC-03: ترتیب blog-preview → contact → footer → info', false !== $p_blog && false !== $p_contact && false !== $p_footer && false !== $p_info && $p_blog < $p_contact && $p_contact < $p_footer && $p_footer < $p_info );
	es_check( 'AC-03: CTA قبل از <footer> است', $p_contact < $p_footer );

	// ── SC-01: footer opening tag parity with the mirror ──────────────────.
	$ref   = (string) file_get_contents( ES_MIRROR . '/index.html' );
	$match = array();
	preg_match( '/<footer class="[^"]+">/', $ref, $match );
	es_check( 'SC-01: تگ آغازین footer مطابق مرجع', ! empty( $match[0] ) && false !== strpos( $html, $match[0] ) );
	es_check( 'SC-01: دکمه بازگشت به بالا', false !== strpos( $html, 'data-scroll-top="true"' ) );
	es_check( 'AC-01: wp_footer اسکریپت‌ها را چاپ کرد', false !== strpos( $html, 'main.js' ) );

	// ── Home sections render in the default order before the CTA ──────────.
	$hero = strpos( $html, 'data-es-section="hero"' );
	es_check( 'سکشن hero قبل از CTA تماس', false !== $hero && $hero < $p_contact );
	es_check( 'هر ۱۳ سکشن پیش‌فرض رندر شده‌اند', 13 === preg_match_all( '/data-es-section="/', $html ) );

	// Restore context.
	update_option( 'show_on_front', $old_show );
	update_option( 'page_on_front', $old_page );
	wp_delete_post( $front_id, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
