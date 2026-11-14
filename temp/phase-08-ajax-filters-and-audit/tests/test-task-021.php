<?php
/**
 * P08-T021 — responsive & accessibility audit: 375/768/1440 breakpoint
 * contract, keyboard-operable modals/accordions, image alt + form labels,
 * no horizontal-overflow fixed widths, deterministic viewport snapshots.
 * Run: php temp/phase-08-ajax-filters-and-audit/tests/test-task-021.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

ob_start();

es_task( 'P08-T021', 'ممیزی ریسپانسیو و دسترس‌پذیری' );

// ── Source-level scans ────────────────────────────────────────────────────
$es_tpl_files = array();
$es_it        = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( ES_THEME, FilesystemIterator::SKIP_DOTS ) );
foreach ( $es_it as $es_file ) {
	if ( $es_file->isFile() && 'php' === $es_file->getExtension() ) {
		$es_tpl_files[] = $es_file->getPathname();
	}
}

// AC-01: no fixed width ≥1000px (max-w/min-w containers are allowed — they
// shrink, they never overflow).
$es_overflow = array();
foreach ( $es_tpl_files as $es_path ) {
	$es_src = (string) file_get_contents( $es_path );
	if ( preg_match_all( '/(?<!max-)w-\[[0-9]{4,}px\]|min-w-\[[0-9]{4,}px\]/', $es_src, $es_m ) ) {
		$es_overflow[] = substr( $es_path, strlen( ES_THEME ) + 1 ) . ' → ' . implode( ',', $es_m[0] );
	}
}
es_check( 'AC-01: هیچ عرض ثابت ≥۱۰۰۰px (بدون overflow افقی)', array() === $es_overflow );

// SC-01: the Tailwind breakpoint contract for the three audited viewports:
// sm/640 (≥375 phones), md/768 (tablets), lg/1024 (desktop/1440).
$es_css = (string) file_get_contents( ES_THEME . '/assets/css/styles.css' );
es_check( 'SC-01: بریک‌پوینت‌های ۶۴۰/۷۶۸/۱۰۲۴ در CSS', false !== strpos( $es_css, 'width>=40rem' ) && false !== strpos( $es_css, 'width>=48rem' ) && false !== strpos( $es_css, 'width>=64rem' ) );

// SC-01: header parts carry the responsive desktop/mobile split.
$es_lg_header = 0;
foreach ( glob( ES_THEME . '/template-parts/header/*.php' ) as $es_path ) {
	$es_lg_header += (int) preg_match_all( '/\b(lg|md|sm):/', (string) file_get_contents( $es_path ) );
}
es_check( 'SC-01: پارتهای هدر الگوهای ریسپانسیو دارند', $es_lg_header >= 20 );

// AC-03: every <img> carries alt.
$es_imgs = array( 'with' => 0, 'without' => 0 );
foreach ( $es_tpl_files as $es_path ) {
	foreach ( explode( "\n", (string) file_get_contents( $es_path ) ) as $es_line ) {
		if ( preg_match_all( '/<img\b/', $es_line, $es_m ) ) {
			if ( false !== strpos( $es_line, 'alt=' ) ) {
				$es_imgs['with'] += count( $es_m[0] );
			} else {
				$es_imgs['without'] += count( $es_m[0] );
			}
		}
	}
}
es_check( 'AC-03: همه تصاویر alt دارند', 0 === $es_imgs['without'] && $es_imgs['with'] >= 40 );

// AC-03: each visible form field has a label/aria-label
// (hidden honeypots/nonce fields and the submit button are exempt).
$es_unlabeled = array();
foreach ( array( 'contact', 'feedback', 'quote', 'recruitment' ) as $es_form ) {
	$es_src     = (string) file_get_contents( ES_THEME . '/template-parts/forms/' . $es_form . '.php' );
	$es_inputs  = (int) preg_match_all( '/<(input|select|textarea)\b/', $es_src );
	$es_hidden  = (int) preg_match_all( '/type="hidden"/', $es_src );
	$es_submit  = (int) preg_match_all( '/type="submit"|type=\'submit\'/', $es_src );
	$es_labels  = (int) preg_match_all( '/<label\b/', $es_src );
	$es_aria    = (int) preg_match_all( '/aria-label="/', $es_src );
	if ( $es_labels + $es_aria < $es_inputs - $es_hidden - $es_submit ) {
		$es_unlabeled[] = $es_form;
	}
}
es_check( 'AC-03: هر فیلد فرم label دارد', array() === $es_unlabeled );

// AC-02: modals/drawers carry accessible-name semantics (dialog role,
// aria-modal or aria-label) so screen readers/keyboard users see them.
$es_modals = 0;
foreach ( array( 'header/cart-modal.php', 'header/search-modal.php', 'header/mobile-menu.php', 'forms/quote.php' ) as $es_part ) {
	$es_src = (string) @file_get_contents( ES_THEME . '/template-parts/' . $es_part );
	if ( '' !== $es_src && ( false !== strpos( $es_src, 'aria-modal' ) || false !== strpos( $es_src, 'role="dialog"' ) || false !== strpos( $es_src, 'aria-label' ) ) ) {
		$es_modals++;
	}
}
es_check( 'AC-02: مودال/دراور aria-label یا role dialog دارند', 4 === $es_modals );

// AC-02: accordions are real buttons with aria-expanded (keyboard-operable).
$es_accordion_ok = true;
foreach ( array( 'archive/category-faq.php', 'product/faq.php', 'sections/arya/section-faq.php' ) as $es_part ) {
	$es_src = (string) file_get_contents( ES_THEME . '/template-parts/' . $es_part );
	if ( false === strpos( $es_src, 'aria-expanded' ) || ! preg_match( '/<button\b/', $es_src ) ) {
		$es_accordion_ok = false;
	}
}
es_check( 'AC-02: آکاردئون‌ها button + aria-expanded', $es_accordion_ok );

// AC-02: Escape closes overlays — main.js (lightbox + modals), certificates.js
// and the quote modal all listen for Escape.
$es_main   = (string) file_get_contents( ES_THEME . '/assets/js/main.js' );
$es_certs  = (string) file_get_contents( ES_THEME . '/assets/js/certificates.js' );
$es_quote  = (string) file_get_contents( ES_THEME . '/template-parts/forms/quote.php' );
es_check( 'AC-02: Escape در main.js و certificates.js و مودال استعلام', substr_count( $es_main, '"Escape"' ) >= 2 && false !== strpos( $es_certs, '"Escape"' ) && false !== strpos( $es_quote, 'Escape' ) );

// SC-02: onclick only on natively keyboard-operable elements.
$es_bad_click = array();
foreach ( $es_tpl_files as $es_path ) {
	$es_src = (string) file_get_contents( $es_path );
	if ( preg_match_all( '/<(div|span|section|li|p)\b[^>]*onclick=/i', $es_src, $es_m, PREG_SET_ORDER ) ) {
		$es_bad_click[] = substr( $es_path, strlen( ES_THEME ) + 1 );
	}
}
es_check( 'SC-02: onclick روی div/span نیست (فقط button/input/a)', array() === $es_bad_click );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';
	require_once ES_ROOT . '/temp/tests/woocommerce-shim.php';

	// SC-01: snapshot determinism — the DOM the browser lays out at 375, 768
	// or 1440 is one single HTML; two renders must be byte-identical.
	$es_page_id = (int) wp_insert_post( array( 'post_title' => 'T021 خانه', 'post_status' => 'publish', 'post_type' => 'page' ) );
	$es_show    = get_option( 'show_on_front' );
	$es_pof     = get_option( 'page_on_front' );
	update_option( 'show_on_front', 'page' );
	update_option( 'page_on_front', $es_page_id );
	$es_render_front = static function (): string {
		global $wp_query;
		$es_saved        = $wp_query;
		$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => $GLOBALS['es_pid'] ) );
		ErfanSanat\Theme\Core\Seo::reset_printed();
		ob_start();
		include ES_THEME . '/front-page.php';
		$GLOBALS['wp_query'] = $es_saved;
		return (string) ob_get_clean();
	};
	$GLOBALS['es_pid'] = $es_page_id;
	$es_snap1 = $es_render_front();
	// get_header() is require_once (one document per request) — the
	// viewport-relevant DOM is the sections markup, so determinism is
	// asserted on two SectionManager renders.
	$es_front_id = (int) get_option( 'page_on_front' );
	$es_sections_a = \ErfanSanat\Theme\Sections\Renderer::render( $es_front_id, 'home' );
	$es_sections_b = \ErfanSanat\Theme\Sections\Renderer::render( $es_front_id, 'home' );
	es_check( 'SC-01: اسنپ‌شات سکشن‌های خانه پایدار و یکسان', '' !== $es_snap1 && '' !== $es_sections_a && $es_sections_a === $es_sections_b && false !== strpos( $es_snap1, '<!doctype html>' ) );

	// Viewport meta (mobile) + desktop container contract (max-w-[1300px]).
	es_check( 'AC-01: متای viewport و کانتینر ۱۳۰۰px حفظ شده', false !== strpos( $es_snap1, 'width=device-width' ) && false !== strpos( $es_snap1, 'max-w-[1300px]' ) );

	// SC-02/AC-02: the rendered header is keyboard-navigable — expandable
	// controls expose aria-expanded and interactive controls are real buttons.
	$es_head = substr( $es_snap1, 0, (int) strpos( $es_snap1, '</header>' ) + 9 );
	es_check( 'AC-02: هدر رندرشده aria-expanded و button دارد', false !== strpos( $es_head, 'aria-expanded' ) && substr_count( $es_head, '<button' ) >= 3 );
	es_check( 'AC-02: مودال‌های رندرشده aria-modal دارند', false !== strpos( $es_snap1, 'aria-modal' ) );

	// Teardown.
	update_option( 'show_on_front', $es_show );
	update_option( 'page_on_front', $es_pof );
	wp_delete_post( $es_page_id, true );
	unset( $GLOBALS['es_pid'] );
} else {
	es_check( 'بارگذاری WordPress', false );
}

ob_end_flush();
es_summary();
