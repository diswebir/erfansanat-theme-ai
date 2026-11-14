<?php
/**
 * P05-T018 — سکشن مشتریان و سازمان‌های همکار
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T018', 'سکشن مشتریان و سازمان‌های همکار' );

es_check( 'target: بخش مشتریان ساخته شده', es_file( 'template-parts/sections/home/section-clients.php' ) );
es_check( 'AC-02: loading=lazy در قالب', es_contains( 'template-parts/sections/home/section-clients.php', 'loading="lazy"' ) );
es_check( 'AC-01: repeater لوگوها در قالب', es_contains( 'template-parts/sections/home/section-clients.php', "\$es_fields['logos']" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'clients', 'type' => 'clients', 'enabled' => true, 'fields' => $fields ), 'home' );
	};
	$card_count = static function ( $html ) {
		return substr_count( $html, 'card-light flex flex-col items-start justify-between rounded-2xl p-4 text-right' );
	};

	// ── AC-02 / reference DOM: 12 default icon cards ──────────────────────.
	$html = $render();
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html, 'data-es-section="clients"' ) );
	es_check( 'AC-02: دوازده کارت مرجع', 12 === $card_count( $html ) );
	es_check( 'AC-02: گرید مرجع حفظ شده', false !== strpos( $html, 'grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-6' ) );
	es_check( 'AC-02: آیکون landmark پیش‌فرض', false !== strpos( $html, 'lucide-landmark' ) );
	es_check( 'AC-02: نام مشتری مرجع', false !== strpos( $html, 'شهرداری اصفهان' ) && false !== strpos( $html, 'سازمان زیباسازی' ) );
	es_check( 'AC-02: شماره‌گذاری فارسی ۰۱ تا ۱۲', false !== strpos( $html, '>۰۱<' ) && false !== strpos( $html, '>۱۲<' ) );
	es_check( 'پیش‌فرض بدون لینک → anchor ندارد', false === strpos( $html, '<a href=' ) );

	// ── AC-01 / SC-01: new logo with image + name + link ──────────────────.
	$att = wp_insert_post( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'post_title' => 'client-logo', 'post_mime_type' => 'image/png' ) );
	update_post_meta( $att, '_wp_attached_file', '2026/11/client-logo.png' );
	$logo_url = wp_get_attachment_image_url( $att, 'medium' );
	$html_new = $render( array( 'logos' => array(
		array( 'image' => $att, 'name' => 'سازمان جدید تست', 'url' => 'https://example.org/new-client' ),
	) ) );
	es_check( 'SC-01: لوگوی جدید اضافه می‌شود', 1 === $card_count( $html_new ) && false !== strpos( $html_new, 'سازمان جدید تست' ) );
	es_check( 'AC-01: تصویر لوگو رندر می‌شود', false !== strpos( $html_new, 'src="' . esc_url( $logo_url ) . '"' ) );
	es_check( 'AC-02: لوگو lazy-load دارد', false !== strpos( $html_new, 'loading="lazy"' ) );
	es_check( 'AC-02: لوگو alt برابر نام سازمان', false !== strpos( $html_new, 'alt="سازمان جدید تست"' ) );
	es_check( 'AC-01: لینک مشتری قابل ویرایش', false !== strpos( $html_new, 'href="https://example.org/new-client"' ) );

	// ── AC-03 / SC-02: empty link -> no anchor, image only ────────────────.
	$html_nolink = $render( array( 'logos' => array(
		array( 'image' => $att, 'name' => 'مشتری بدون لینک' ),
	) ) );
	es_check( 'SC-02: مشتری بدون لینک فقط تصویر است', false !== strpos( $html_nolink, 'src="' . esc_url( $logo_url ) . '"' ) && false === strpos( $html_nolink, '<a href=' ) );
	es_check( 'AC-03: لینک خالی رندر نمی‌شود', false === strpos( $html_nolink, '<a href=' ) );
	$html_emptyurl = $render( array( 'logos' => array(
		array( 'image' => $att, 'name' => 'لینک خالی', 'url' => '' ),
	) ) );
	es_check( 'AC-03: url تهی هم anchor نمی‌سازد', false === strpos( $html_emptyurl, '<a href=' ) );

	// ── Empty repeater fallback + empty-item skip + escaping ──────────────.
	es_check( 'repeater خالی → ۱۲ پیش‌فرض مرجع', 12 === $card_count( $render( array( 'logos' => array() ) ) ) );
	$html_skip = $render( array( 'logos' => array(
		array( 'name' => 'مشتری معتبر' ),
		array( 'name' => '', 'image' => 0 ),
	) ) );
	es_check( 'آیتم خالی رندر نمی‌شود', 1 === $card_count( $html_skip ) );
	$html_esc = $render( array( 'heading' => '<b>bold</b>', 'logos' => array( array( 'name' => '<i>x</i>' ) ) ) );
	es_check( 'عنوان و نام escape می‌شوند', false === strpos( $html_esc, '<b>bold</b>' ) && false === strpos( $html_esc, '<i>x</i>' ) && false !== strpos( $html_esc, '&lt;b&gt;' ) );

	wp_delete_post( $att, true );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
