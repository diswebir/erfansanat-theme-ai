<?php
/**
 * P04-T020 — Parity و دسترس‌پذیری ناوبری (ممیزی نهایی فاز ۴)
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-020.php
 *
 * Full-page integration audit with real fixtures (menus, terms, products,
 * projects, injected cart). One get_header()+get_footer() render (harness
 * allows a single wp_footer per request). Static DOM/JS assertions stand in
 * for browser checks at 375/768/1440 (structural overflow guards).
 *
 * Documented deviation: data-cart-modal-restore (mirror's sample-items
 * restore button) is intentionally replaced by a real shop link (P04-T013).
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

es_task( 'P04-T020', 'Parity و دسترس‌پذیری ناوبری' );

if ( es_load_wp() ) {
	require_once ES_THEME . '/functions.php';

	foreach ( array_keys( $_POST ) as $k ) {
		if ( 0 === strpos( $k, 'es_' ) ) {
			unset( $_POST[ $k ] );
		}
	}
	if ( ! post_type_exists( 'product' ) ) {
		register_post_type( 'product', array( 'public' => true ) );
	}
	if ( ! taxonomy_exists( 'product_cat' ) ) {
		register_taxonomy( 'product_cat', 'product', array( 'hierarchical' => true, 'public' => true ) );
	}

	// ── Fixtures ──────────────────────────────────────────────────────────.
	$top  = wp_insert_term( 'پوینت لایت T020', 'product_cat' );
	$tid  = $top['term_id'];
	$c1   = wp_insert_term( 'آلفا T020', 'product_cat', array( 'parent' => $tid ) );
	$c2   = wp_insert_term( 'بتا T020', 'product_cat', array( 'parent' => $tid ) );
	$pt   = wp_insert_term( 'تونل T020', 'project_type' );
	$ct   = wp_insert_term( 'شهر T020', 'city' );
	$prod = wp_insert_post( array( 'post_type' => 'product', 'post_status' => 'publish', 'post_title' => 'محصول ممیزی T020' ) );
	$proj = wp_insert_post( array( 'post_type' => 'projects', 'post_status' => 'publish', 'post_title' => 'پروژه ممیزی T020' ) );

	$menu_id = wp_update_nav_menu_object( 0, array( 'menu-name' => 'ممیزی ناوبری T020' ) );
	$mk      = static function ( $menu_id, $title, $url, $parent = 0 ) {
		return wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'     => $title,
			'menu-item-url'       => $url,
			'menu-item-type'      => 'custom',
			'menu-item-parent-id' => $parent,
			'menu-item-status'    => 'publish',
		) );
	};
	$mk( $menu_id, 'صفحه اصلی', home_url( '/' ) );
	$mk( $menu_id, 'محصولات', home_url( '/shop/' ) );
	$mk( $menu_id, 'پروژه‌ها', home_url( '/projects/' ) );
	$mk( $menu_id, 'درباره شرکت', home_url( '/about/' ) );
	$srv = $mk( $menu_id, 'خدمات', home_url( '/services/' ) );
	$mk( $menu_id, 'نورپردازی شهری', home_url( '/services/urban/' ), $srv );
	$mk( $menu_id, 'نورپردازی صنعتی', home_url( '/services/industrial/' ), $srv );

	$old_locs                  = get_theme_mod( 'nav_menu_locations', array() );
	$locs                      = $old_locs;
	$locs['main-mega-menu']    = $menu_id;
	$locs['mobile-menu']       = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locs );

	$payload = array(
		'items'      => array(
			array(
				'key' => 'audit-key', 'title' => 'ریسه ممیزی', 'permalink' => home_url( '/product/audit/' ),
				'thumb' => '', 'qty' => 2, 'price_html' => '۱۰ تومان', 'row_total_html' => '۲۰ تومان',
			),
		),
		'count'      => 2,
		'total_html' => '۲۰ تومان',
	);
	$cb = static function () use ( $payload ) { return $payload; };
	add_filter( 'es_cart_modal_data', $cb );

	// ── Single full-page render ───────────────────────────────────────────.
	ob_start();
	get_header();
	get_footer();
	$html = (string) ob_get_clean();
	es_check( 'صفحه کامل رندر شد', strlen( $html ) > 10000 );

	$mirror = (string) file_get_contents( ES_MIRROR . '/index.html' );

	// ══ AC-01 / SC-01: DOM parity with the mirror ═════════════════════════.
	preg_match( '/<header[^>]*>/', $mirror, $m );
	es_check( 'AC-01: تگ آغازین header عیناً مطابق مرجع', ! empty( $m[0] ) && false !== strpos( $html, $m[0] ) );
	preg_match( '/<footer[^>]*>/', $mirror, $m );
	es_check( 'AC-01: تگ آغازین footer عیناً مطابق مرجع', ! empty( $m[0] ) && false !== strpos( $html, $m[0] ) );
	preg_match( '/<aside data-mobile-sidebar[^>]*>/', $mirror, $m );
	es_check( 'AC-01: aside کشوی موبایل عیناً مطابق مرجع', ! empty( $m[0] ) && false !== strpos( $html, $m[0] ) );
	es_check( 'AC-01: پوشش ریشه min-h-screen overflow-x-clip', false !== strpos( $html, 'min-h-screen overflow-x-clip selection:bg-emerald-600 selection:text-white' ) );
	es_check( 'AC-01: CTA با id=contact و scroll-mt-24', false !== strpos( $html, 'id="contact"' ) && false !== strpos( $html, 'scroll-mt-24' ) );
	es_check( 'SC-01: ساختار header/footer بسته و متوازن است', 1 === substr_count( $html, '<header' ) && 1 === substr_count( $html, '</header>' ) && 1 === substr_count( $html, '<footer' ) && 1 === substr_count( $html, '</footer>' ) );

	$groups = array(
		'هدر/مگامنو' => array( 'data-navbar-header', 'data-desktop-mega-trigger', 'data-desktop-mega-panel', 'data-mega-menu-products', 'data-mega-menu-projects', 'data-mega-menu-corporate', 'data-mega-prod-tab', 'data-mega-prod-panel', 'data-mega-proj-tab', 'data-mega-proj-panel', 'data-mobile-toggle', 'data-header-cart-badge' ),
		'جستجو'     => array( 'data-search-modal', 'data-search-modal-open', 'data-search-modal-close', 'data-global-search-input', 'data-global-search-products', 'data-global-search-projects', 'data-search-item' ),
		'کشوی موبایل' => array( 'data-mobile-menu', 'data-mobile-sidebar', 'data-mobile-backdrop', 'data-mobile-close', 'data-mobile-acc-btn', 'data-mobile-acc-icon', 'data-mobile-acc-panel', 'data-mobile-prod-tab', 'data-mobile-prod-panel', 'data-mobile-proj-tab', 'data-mobile-proj-panel' ),
		'مودال سبد'  => array( 'data-cart-modal', 'data-cart-modal-open', 'data-cart-modal-backdrop', 'data-cart-modal-close', 'data-cart-modal-count', 'data-cart-modal-total', 'data-cart-modal-items-list', 'data-cart-modal-empty', 'data-cart-modal-add', 'data-cart-modal-row', 'data-cart-modal-dec', 'data-cart-modal-inc', 'data-cart-modal-qty', 'data-cart-modal-remove', 'data-cart-modal-row-total' ),
		'فوتر'      => array( 'data-scroll-top' ),
	);
	foreach ( $groups as $label => $attrs ) {
		$missing = array();
		foreach ( $attrs as $attr ) {
			if ( false === strpos( $html, $attr . '=' ) ) {
				$missing[] = $attr;
			}
		}
		if ( $missing ) {
			es_note( 'گروه ' . $label . ' کمبود: ' . implode( ', ', $missing ) );
		}
		es_check( 'AC-01: همه data-*های مرجع در گروه «' . $label . '» حاضرند', empty( $missing ) );
	}

	// ══ AC-02: focus / Escape / ARIA ══════════════════════════════════════.
	es_check( 'AC-02: دکمه منوی موبایل aria-expanded + aria-label', false !== strpos( $html, 'data-mobile-toggle="true"' ) && false !== strpos( $html, 'aria-label="باز کردن منوی موبایل"' ) );
	es_check( 'AC-02: کشو aria-hidden + aside با aria-label', false !== strpos( $html, 'data-mobile-menu="true" class="fixed inset-0 z-[90] lg:hidden hidden" aria-hidden="true"' ) && false !== strpos( $html, 'aria-label="منوی موبایل"' ) );
	$nav_js = (string) file_get_contents( ES_THEME . '/assets/js/navigation.js' );
	es_check( 'AC-02: Escape کشو را می‌بندد', false !== strpos( $nav_js, '"Escape"' ) && false !== strpos( $nav_js, 'closeMenu(true)' ) );
	es_check( 'AC-02: focus trap + بازگردانی فوکوس', false !== strpos( $nav_js, 'e.shiftKey' ) && false !== strpos( $nav_js, 'toggle.focus()' ) && false !== strpos( $nav_js, 'closeBtn.focus()' ) );
	es_check( 'AC-02: تریگرهای مگامنو aria-expanded دارند', substr_count( $html, 'aria-expanded="false"' ) >= 4 );
	es_check( 'AC-02: دکمه بستن جستجو aria-label دارد', false !== strpos( $html, 'aria-label="بستن جستجو"' ) );
	es_check( 'AC-02: مودال سبد role=dialog + aria-modal', false !== strpos( $html, 'role="dialog"' ) && false !== strpos( $html, 'aria-modal="true"' ) );
	$main_js = (string) file_get_contents( ES_THEME . '/assets/js/main.js' );
	es_check( 'AC-02: main.js جستجو را با Escape می‌بندد و فوکوس می‌دهد', false !== strpos( $main_js, 'Escape' ) && false !== strpos( $main_js, 'focus()' ) );
	es_check( 'AC-02: main.js مودال سبد را باز/بسته می‌کند', false !== strpos( $main_js, 'openCartModal' ) && false !== strpos( $main_js, 'closeCartModal' ) );

	// ══ AC-03: no horizontal overflow at 375/768/1440 (structural guards) ═.
	es_check( 'AC-03: overflow-x-clip روی پوشش ریشه (همه عرض‌ها)', false !== strpos( $html, 'overflow-x-clip' ) );
	es_check( 'AC-03: کشو w-[88vw] max-w-[380px] (در 375 سرریز ندارد)', false !== strpos( $html, 'w-[88vw] max-w-[380px]' ) );
	es_check( 'AC-03: ناوبری دسکتاپ فقط lg (hidden … lg:flex)', false !== strpos( $html, '<nav aria-label="منوی اصلی" class="hidden items-center gap-1 lg:flex"' ) );
	es_check( 'AC-03: کشو فقط موبایل (lg:hidden)', false !== strpos( $html, 'data-mobile-menu="true" class="fixed inset-0 z-[90] lg:hidden' ) );
	es_check( 'AC-03: دیالوگ سبد w-full max-w-[680px] (در 375 جا می‌شود)', false !== strpos( $html, 'w-full max-w-[680px]' ) );
	es_check( 'AC-03: کانتینرها سیال‌اند (max-w-[1300px] + px-5)', substr_count( $html, 'max-w-[1300px]' ) >= 3 && false !== strpos( $html, 'px-5' ) );

	// ══ SC-02: full keyboard navigation ═══════════════════════════════════.
	es_check( 'SC-02: tabindex مثبت وجود ندارد', 1 !== preg_match( '/tabindex="[1-9]/', $html ) );
	es_check( 'SC-02: کلیک روی div/span غیرتعاملی نیست', 1 !== preg_match( '/<(div|span)[^>]*onclick=/i', $html ) );
	es_check( 'SC-02: دکمه‌های آیکونی سبد aria-label دارند', false !== strpos( $html, 'aria-label="کاهش تعداد"' ) && false !== strpos( $html, 'aria-label="افزایش تعداد"' ) && false !== strpos( $html, 'aria-label="حذف از سبد"' ) && false !== strpos( $html, 'aria-label="بستن مودال سبد خرید"' ) );
	es_check( 'SC-02: بستن کشو aria-label دارد', false !== strpos( $html, 'aria-label="بستن منو"' ) );
	es_check( 'SC-02: trap همه عناصر تعاملی را پوشش می‌دهد', false !== strpos( $nav_js, 'a[href], button:not([disabled]), input, select, textarea' ) );
	es_check( 'SC-02: لینک‌های ناوبری href واقعی دارند', substr_count( $html, '<a href=' ) > 20 );

	// ── Cleanup ───────────────────────────────────────────────────────────.
	remove_filter( 'es_cart_modal_data', $cb );
	set_theme_mod( 'nav_menu_locations', $old_locs );
	wp_delete_nav_menu( $menu_id );
	wp_delete_post( $prod, true );
	wp_delete_post( $proj, true );
	foreach ( array( $c1, $c2, $top ) as $t ) { wp_delete_term( $t['term_id'], 'product_cat' ); }
	wp_delete_term( $pt['term_id'], 'project_type' );
	wp_delete_term( $ct['term_id'], 'city' );
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
