<?php
/**
 * P04-T018 — Walker تولیدات شرکت
 * Run: php temp/phase-04-header-footer-navigation/tests/test-task-018.php
 */
require dirname( __DIR__, 2 ) . '/tests/bootstrap.php';

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Core\NavWalkers\TolidatWalker;

es_task( 'P04-T018', 'Walker تولیدات شرکت' );

es_check( 'target: TolidatWalker در NavWalkers.php', es_contains( 'inc/Core/NavWalkers.php', 'class TolidatWalker' ) );
es_check( 'AC-01: footer-info مکان tolidat-sherkat را مصرف می‌کند', es_contains( 'template-parts/global/footer-info.php', "'tolidat-sherkat'" ) );
es_check( 'AC-01: footer-info واکر TolidatWalker را پاس می‌دهد', es_contains( 'template-parts/global/footer-info.php', 'TolidatWalker' ) );

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

	$args = array(
		'walker'     => new TolidatWalker(),
		'menu_class' => 'space-y-2.5 text-[13px] text-slate-600',
		'items_wrap' => '<ul class="%2$s">%3$s</ul>',
		'container'  => false,
		'echo'       => false,
	);

	$old_locs = get_theme_mod( 'nav_menu_locations', array() );
	$locs     = $old_locs;
	unset( $locs['tolidat-sherkat'] );
	set_theme_mod( 'nav_menu_locations', $locs );

	// ── SC-02: fallback to real product categories (no menu assigned) ─────.
	$term = wp_insert_term( 'آزمون تولیدات T018', 'product_cat', array( 'slug' => 't018-cat' ) );
	$tid  = is_array( $term ) ? (int) $term['term_id'] : 0;
	$turl = $tid ? get_term_link( $tid, 'product_cat' ) : '';

	$fallback = Menus::render( 'tolidat-sherkat', $args );
	es_check( 'SC-02: fallback بدون منوی اختصاصی خروجی دارد', is_string( $fallback ) && '' !== trim( $fallback ) );
	es_check( 'AC-03: کلاس لیست فوتر در fallback حفظ شده', false !== strpos( (string) $fallback, '<ul class="space-y-2.5 text-[13px] text-slate-600">' ) );
	es_check( 'AC-03: کلاس لینک فوتر در fallback حفظ شده', false !== strpos( (string) $fallback, 'class="transition-colors hover:text-emerald-700"' ) );
	es_check( 'SC-02: دسته واقعی محصول در fallback می‌آید', $tid && false !== strpos( (string) $fallback, 'آزمون تولیدات T018' ) );
	es_check( 'AC-02: لینک دسته واقعی است (term link)', is_string( $turl ) && '' !== $turl && false !== strpos( (string) $fallback, esc_url( $turl ) ) );

	// ── SC-01: assigned menu renders through TolidatWalker ────────────────.
	$menu_id = wp_update_nav_menu_object( 0, array( 'menu-name' => 'منوی تولیدات T018' ) );
	$parent  = wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'  => 'لینک دسته آزمایشی',
		'menu-item-url'    => $turl,
		'menu-item-type'   => 'custom',
		'menu-item-status' => 'publish',
	) );
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => 'زیرآیتم تخت',
		'menu-item-url'       => home_url( '/shop/' ),
		'menu-item-type'      => 'custom',
		'menu-item-parent-id' => $parent,
		'menu-item-status'    => 'publish',
	) );
	$locs['tolidat-sherkat'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locs );

	es_check( 'AC-01: مکان پس از اختصاص مصرف می‌شود', has_nav_menu( 'tolidat-sherkat' ) );
	$assigned = (string) Menus::render( 'tolidat-sherkat', $args );
	es_check( 'SC-01: منوی اختصاص‌داده‌شده رندر می‌شود', false !== strpos( $assigned, 'لینک دسته آزمایشی' ) );
	es_check( 'AC-02: لینک واقعی منو در خروجی است', false !== strpos( $assigned, esc_url( (string) $turl ) ) );
	es_check( 'AC-03: کلاس لیست فوتر در منوی اختصاصی حفظ شده', false !== strpos( $assigned, '<ul class="space-y-2.5 text-[13px] text-slate-600">' ) );
	es_check( 'AC-03: DOM مرجع <li><a class=…> حفظ شده', false !== strpos( $assigned, '<li><a href="' . esc_url( (string) $turl ) . '" class="transition-colors hover:text-emerald-700"' ) );
	es_check( 'DOM تخت است: زیرمنو تودرتو رندر نمی‌شود', 1 === substr_count( $assigned, '<ul' ) && false !== strpos( $assigned, 'زیرآیتم تخت' ) );

	// ── Escape: menu title markup is neutralized ──────────────────────────.
	wp_update_nav_menu_item( $menu_id, $parent, array(
		'menu-item-title'  => '<b>تیتر</b><script>x</script>',
		'menu-item-url'    => $turl,
		'menu-item-type'   => 'custom',
		'menu-item-status' => 'publish',
	) );
	$assigned = (string) Menus::render( 'tolidat-sherkat', $args );
	es_check( 'خروجی escape می‌شود', false === strpos( $assigned, '<script>x</script>' ) );

	// Cleanup.
	set_theme_mod( 'nav_menu_locations', $old_locs );
	wp_delete_nav_menu( $menu_id );
	if ( $tid ) {
		wp_delete_term( $tid, 'product_cat' );
	}
} else {
	es_check( 'بارگذاری WordPress تست', false );
}

es_summary();
