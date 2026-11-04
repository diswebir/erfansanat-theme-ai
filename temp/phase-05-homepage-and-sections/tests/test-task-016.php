<?php
/**
 * P05-T016 — سکشن استانداردهای کیفیت
 * Run: php temp/phase-05-homepage-and-sections/tests/test-task-016.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Sections\Renderer;

es_task( 'P05-T016', 'سکشن استانداردهای کیفیت' );

es_check( 'target: بخش استانداردها ساخته شده', es_file( 'template-parts/sections/home/section-standards.php' ) );
es_check( 'AC-01: repeater آیتم‌ها در قالب', es_contains( 'template-parts/sections/home/section-standards.php', "\$es_fields['items']" ) );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}

	$render = static function ( array $fields = array() ) {
		return Renderer::render_section( array( 'id' => 'standards', 'type' => 'standards', 'enabled' => true, 'fields' => $fields ), 'home' );
	};
	$card_count = static function ( $html ) {
		return substr_count( $html, 'card-light flex h-full flex-col justify-between rounded-2xl p-5 sm:p-6' );
	};

	// ── AC-02: default DOM parity — 4 reference cards + process box ───────.
	$html = $render();
	es_check( 'قرارداد Renderer: data-es-section حفظ شده', false !== strpos( $html, 'data-es-section="standards"' ) );
	es_check( 'AC-02: چهار کارت مرجع رندر می‌شوند', 4 === $card_count( $html ) );
	es_check( 'AC-02: گرید مرجع حفظ شده', false !== strpos( $html, 'grid gap-4 sm:grid-cols-2 lg:grid-cols-4' ) );
	es_check( 'AC-02: نشان ISO 9001:2015', false !== strpos( $html, 'ISO 9001:2015' ) );
	es_check( 'AC-02: نشان IP67 / IP68', false !== strpos( $html, 'IP67 / IP68' ) );
	es_check( 'AC-02: عنوان کارت عایق‌بندی', false !== strpos( $html, 'عایق‌بندی تزریق مستقیم' ) );
	es_check( 'AC-02: آیکون badge-check کارت اول', false !== strpos( $html, 'lucide-badge-check' ) );
	es_check( 'AC-02: باکس فرآیند ۵ گام', 5 === preg_match_all( '/گام [\x{6F0}-\x{6F5}]/u', $html ) && false !== strpos( $html, 'از ایده تا درخشش در شهر' ) );
	es_check( 'AC-02: گام طراحی سه‌بعدی', false !== strpos( $html, 'طراحی سه‌بعدی اختصاصی' ) );

	// ── AC-01: icon/title/description editable per card ───────────────────.
	$html_ed = $render( array( 'items' => array(
		array( 'icon' => 'shield', 'title' => 'عنوان سفارشی یک', 'description' => 'توضیح سفارشی یک' ),
	) ) );
	es_check( 'AC-01: عنوان قابل ویرایش', false !== strpos( $html_ed, 'عنوان سفارشی یک' ) );
	es_check( 'AC-01: توضیح قابل ویرایش', false !== strpos( $html_ed, 'توضیح سفارشی یک' ) );
	es_check( 'AC-01: آیکون قابل ویرایش (shield → shield-check)', false !== strpos( $html_ed, 'lucide-shield-check' ) );
	es_check( 'آیتم ذخیره‌شده بدون badge نشان ندارد', false === strpos( $html_ed, 'font-mono text-xs font-extrabold text-emerald-700 tabular-nums' ) );

	// ── SC-01: three standards render ─────────────────────────────────────.
	$three = array(
		array( 'icon' => 'check', 'title' => 'استاندارد الف', 'description' => 'توضیح الف' ),
		array( 'icon' => 'star', 'title' => 'استاندارد ب', 'description' => 'توضیح ب' ),
		array( 'icon' => 'truck', 'title' => 'استاندارد ج', 'description' => 'توضیح ج' ),
	);
	$html3 = $render( array( 'items' => $three ) );
	es_check( 'SC-01: سه استاندارد رندر می‌شوند', 3 === $card_count( $html3 ) );
	es_check( 'SC-01: آیکون star resolves', false !== strpos( $html3, 'lucide-star' ) );
	es_check( 'SC-01: آیکون truck resolves', false !== strpos( $html3, 'lucide-truck' ) );

	// ── AC-03: empty item is not rendered ─────────────────────────────────.
	$with_empty = $three;
	$with_empty[] = array( 'icon' => 'check', 'title' => '', 'description' => '' );
	$html_e = $render( array( 'items' => $with_empty ) );
	es_check( 'AC-03: آیتم خالی رندر نمی‌شود', 3 === $card_count( $html_e ) );

	// ── SC-02: removing one item leaves the rest intact ───────────────────.
	$two = array( $three[0], $three[2] );
	$html2 = $render( array( 'items' => $two ) );
	es_check( 'SC-02: حذف یک آیتم بقیه را خراب نمی‌کند', 2 === $card_count( $html2 ) && false !== strpos( $html2, 'استاندارد الف' ) && false !== strpos( $html2, 'استاندارد ج' ) && false === strpos( $html2, 'استاندارد ب' ) );

	// ── Fallbacks + escaping ──────────────────────────────────────────────.
	$html_all_empty = $render( array( 'items' => array( array( 'title' => '', 'description' => '' ) ) ) );
	es_check( 'همه آیتم‌ها خالی → پیام خالی', false !== strpos( $html_all_empty, 'استانداردی برای نمایش وجود ندارد' ) );
	es_check( 'repeater خالی → پیش‌فرض‌های مرجع (۴ کارت)', 4 === $card_count( $render( array( 'items' => array() ) ) ) );
	$html_unknown = $render( array( 'items' => array( array( 'icon' => 'nonexistent-slug', 'title' => 'آیکون ناشناخته' ) ) ) );
	es_check( 'آیکون ناشناخته → fallback امن', false !== strpos( $html_unknown, 'lucide-badge-check' ) );
	$html_esc = $render( array( 'heading' => '<b>bold</b>', 'items' => array( array( 'title' => '<i>x</i>', 'description' => 'ok' ) ) ) );
	es_check( 'عنوان و آیتم escape می‌شوند', false === strpos( $html_esc, '<b>bold</b>' ) && false === strpos( $html_esc, '<i>x</i>' ) && false !== strpos( $html_esc, '&lt;b&gt;' ) );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
