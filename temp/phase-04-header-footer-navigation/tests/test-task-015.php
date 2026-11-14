<?php
/**
 * P04-T015 — Footer پایانی و کپی‌رایت
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-015.php
 *
 * Note: get_header()/get_footer() may only run once per request in the harness
 * (a second wp_footer() empties the buffer), so all render assertions use a
 * single full-page render; editability defaults are checked at the data layer.
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Options\Store;
use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

es_task( 'P04-T015', 'Footer پایانی و کپی‌رایت' );

es_check( 'target: footer.php', es_file( 'footer.php' ) );
es_check( 'AC-01: wp_footer فراخوانی می‌شود', es_contains( 'footer.php', 'wp_footer()' ) );
es_check( 'AC-03: کپی‌رایت از options خوانده می‌شود', es_contains( 'footer.php', 'FooterTab::copyright()' ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	// Pre-set an editable copyright + a social list with one empty URL.
	Store::set( 'copyright_text', 'کپی‌رایت سفارشی آزمایشی' );
	Store::set( 'social_links', array(
		array( 'network' => 'instagram', 'url' => 'https://instagram.com/test' ),
		array( 'network' => 'telegram', 'url' => '' ),
	) );

	ob_start();
	get_header();
	get_footer();
	$html = (string) ob_get_clean();

	// AC-01: wp_footer fired (footer scripts printed).
	es_check( 'AC-01: wp_footer اسکریپت‌ها را چاپ کرد', false !== strpos( $html, 'main.js' ) );

	// SC-01: footer opening tag parity with the mirror.
	$ref   = (string) file_get_contents( ES_MIRROR . '/index.html' );
	$match = array();
	preg_match( '/<footer class="[^"]+">/', $ref, $match );
	es_check( 'SC-01: تگ آغازین footer مطابق مرجع', ! empty( $match[0] ) && false !== strpos( $html, $match[0] ) );
	es_check( 'SC-01: دکمه بازگشت به بالا حفظ شده', false !== strpos( $html, 'data-scroll-top="true"' ) );
	es_check( 'SC-01: بخش اطلاعات فوتر رندر شد', false !== strpos( $html, 'اطلاعات تماس کارخانه' ) );

	// AC-02: social links carry safe rel/target.
	es_check( 'AC-02: rel امن شبکه اجتماعی', false !== strpos( $html, 'rel="noopener noreferrer"' ) );
	es_check( 'AC-02: target=_blank شبکه اجتماعی', false !== strpos( $html, 'target="_blank"' ) );

	// AC-03: the edited copyright renders.
	es_check( 'AC-03: کپی‌رایت قابل ویرایش است', false !== strpos( $html, 'کپی‌رایت سفارشی آزمایشی' ) );

	// SC-02: the empty social URL is not rendered.
	es_check( 'SC-02: لینک اینستاگرام رندر می‌شود', false !== strpos( $html, 'اینستاگرام' ) );
	es_check( 'SC-02: لینک خالی تلگرام رندر نمی‌شود', false === strpos( $html, '>تلگرام</a>' ) );

	// Data layer: defaults restored after delete; social_links skips empties.
	Store::delete( 'copyright_text' );
	es_check( 'AC-03: پیش‌فرض کپی‌رایت از Schema', FooterTab::copyright() === Schema::default( 'copyright_text' ) );
	Store::delete( 'social_links' );
	es_check( 'SC-02: social_links پیش‌فرض سه شبکه دارد', 3 === count( FooterTab::social_links() ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
