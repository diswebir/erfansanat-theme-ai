<?php
/** Builds the tab/column product mega menu from the assigned WordPress menu. */

namespace ErfanSanat\Theme\Nav;

defined( 'ABSPATH' ) || exit;

/** Menu-tree renderer retaining the data hooks consumed by main.js. */
final class MegaMenuWalker {

	/** Render the menu assigned to a theme location; empty means keep static fallback. */
	public static function for_location( string $location = 'tolidat-sherkat' ): string {
		if ( ! function_exists( 'get_nav_menu_locations' ) || ! function_exists( 'wp_get_nav_menu_items' ) ) { return ''; }
		$locations = get_nav_menu_locations();
		$menu_id = is_array( $locations ) ? absint( $locations[ $location ] ?? 0 ) : 0;
		if ( ! $menu_id ) { return ''; }
		$items = wp_get_nav_menu_items( $menu_id, array( 'update_post_term_cache' => false ) );
		return is_array( $items ) ? self::render( $items ) : '';
	}

	/** Render a valid WP nav-menu tree (roots=tabs, children=columns/links). */
	public static function render( array $items ): string {
		self::product_terms(); // Prime one shared, transient-backed term query for related category objects.
		$tree = self::tree( $items );
		if ( ! $tree ) { return ''; }
		$tabs = '';
		$panels = '';
		$index = 0;
		foreach ( $tree as $root ) {
			$key = (string) $index;
			$active = 0 === $index;
			$title = self::text( $root->title ?? '' );
			$url = self::url( $root->url ?? '#' );
			$icon_name = self::meta( $root, 'icon' );
			$icon = Icons::svg( $icon_name, 'h-4 w-4' );
			if ( '' === $icon ) { $icon = Icons::svg( 'grid', 'h-4 w-4' ); }
			$tabs .= '<button type="button" data-mega-prod-tab="' . $key . '" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right transition-all cursor-pointer ' . ( $active ? 'bg-white text-emerald-950 shadow-2xs ring-1 ring-emerald-200' : 'text-slate-700 hover:bg-white/70' ) . '"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg transition-colors ' . ( $active ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-700' ) . '">' . $icon . '</span><span class="min-w-0 flex-1 truncate text-xs font-extrabold">' . $title . '</span>' . Icons::svg( 'chevron-down', 'h-3.5 w-3.5 shrink-0 -rotate-90 text-slate-300' ) . '</button>';
			$panels .= self::panel( $root, $key, $active );
			++$index;
		}

		$trigger = '<button type="button" data-desktop-mega-trigger="products" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700"><span>محصولات</span>' . Icons::svg( 'chevron-down', 'h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400' ) . '</button>';
		return '<div class="group static" data-mega-menu-products="true">' . $trigger . '<div data-desktop-mega-panel="products" class="absolute inset-x-4 top-full z-50 mx-auto max-w-[780px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"><div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]"><div class="grid grid-cols-12"><div class="col-span-5 border-l border-slate-100 bg-slate-50/70 p-3.5"><div class="mb-2 px-2 text-[10.5px] font-extrabold text-slate-400">دسته‌بندی تولیدات</div><div class="space-y-1">' . $tabs . '</div></div><div class="col-span-7 flex flex-col justify-between p-4">' . $panels . '</div></div></div></div></div>';
	}

	/** Cached category lookup: one query at most per cache lifetime. */
	public static function product_terms(): array { return NavTerms::taxonomy( 'product_cat' ); }

	private static function panel( object $root, string $key, bool $active ): string {
		$children = $root->children ?? array();
		$links = '';
		foreach ( $children as $child ) {
			$grandchildren = $child->children ?? array();
			if ( $grandchildren ) {
				$links .= '<div class="min-w-0"><div class="mb-2 border-b border-slate-100 pb-2 text-xs font-black text-slate-900">' . self::text( $child->title ?? '' ) . '</div><ul class="space-y-1.5">';
				foreach ( $grandchildren as $link ) { $links .= self::link( $link, 'text-xs font-bold text-slate-600 hover:text-emerald-700', true ); }
				$links .= '</ul></div>';
			} else {
				$links .= self::link( $child, 'group/cat flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-950', false );
			}
		}
		if ( '' === $links ) { $links = '<p class="text-xs font-medium text-slate-500">موردی برای نمایش ثبت نشده است.</p>'; }
		$layout = count( $children ) > 2 ? 'grid-cols-2' : 'grid-cols-1';
		return '<div data-mega-prod-panel="' . self::attr( $key ) . '" class="' . ( $active ? 'block' : 'hidden' ) . '"><div class="mb-3 flex items-center justify-between border-b border-slate-100 pb-2.5"><a href="' . self::url( $root->url ?? '#' ) . '" class="text-xs font-black text-slate-900 hover:text-emerald-700">' . self::text( $root->title ?? '' ) . '</a><a href="' . self::url( $root->url ?? '#' ) . '" class="inline-flex items-center gap-1 text-[11px] font-extrabold text-emerald-700 hover:text-emerald-800"><span>مشاهده همه</span></a></div><div class="' . $layout . ' grid gap-1.5">' . $links . '</div></div>';
	}

	private static function link( object $item, string $class, bool $list_item ): string {
		$title = self::text( $item->title ?? '' );
		$url = self::url( $item->url ?? '#' );
		return ( $list_item ? '<li>' : '' ) . '<a href="' . $url . '" class="' . self::attr( $class ) . '">' . $title . '</a>' . ( $list_item ? '</li>' : '' );
	}

	private static function tree( array $items ): array {
		$nodes = array();
		foreach ( $items as $item ) {
			if ( ! is_object( $item ) || empty( $item->ID ) ) { continue; }
			$node = new \stdClass();
			foreach ( get_object_vars( $item ) as $property => $value ) { $node->$property = $value; }
			$node->children = array();
			$nodes[ (int) $node->ID ] = $node;
		}
		$roots = array();
		foreach ( $nodes as $node ) {
			$parent = (int) ( $node->menu_item_parent ?? 0 );
			if ( $parent && isset( $nodes[ $parent ] ) ) { $nodes[ $parent ]->children[] = $node; }
			else { $roots[] = $node; }
		}
		return $roots;
	}

	private static function meta( object $item, string $key ): string {
		$value = function_exists( 'get_post_meta' ) ? get_post_meta( (int) $item->ID, '_menu_item_es_' . $key, true ) : '';
		return is_string( $value ) && isset( Icons::allowed()[ $value ] ) ? $value : '';
	}

	private static function text( $value ): string {
		$value = is_scalar( $value ) ? (string) $value : '';
		return function_exists( 'esc_html' ) ? esc_html( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}

	private static function attr( string $value ): string { return function_exists( 'esc_attr' ) ? esc_attr( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
	private static function url( $value ): string {
		$value = is_string( $value ) ? LegacyMarkup::resolve( $value ) : '#';
		return function_exists( 'esc_url' ) ? esc_url( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}
}
