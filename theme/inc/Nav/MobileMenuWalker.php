<?php
/** Renders assigned mobile menu items as accessible native accordions. */

namespace ErfanSanat\Theme\Nav;

defined( 'ABSPATH' ) || exit;

/** Small multilevel menu walker preserving main.js mobile hooks. */
final class MobileMenuWalker {

	public static function for_location( string $location = 'mobile-menu' ): string {
		if ( ! function_exists( 'get_nav_menu_locations' ) || ! function_exists( 'wp_get_nav_menu_items' ) ) { return ''; }
		$locations = get_nav_menu_locations();
		$id = is_array( $locations ) ? absint( $locations[ $location ] ?? 0 ) : 0;
		if ( ! $id ) { return ''; }
		$items = wp_get_nav_menu_items( $id, array( 'update_post_term_cache' => false ) );
		return is_array( $items ) ? self::render( $items ) : '';
	}

	/** Return a scroll-region replacement, or empty to keep the static fallback. */
	public static function render( array $items ): string {
		$tree = self::tree( $items );
		if ( ! $tree ) { return ''; }
		$content = '';
		foreach ( $tree as $node ) { $content .= self::item( $node ); }
		return '<div class="flex-1 space-y-2.5 overflow-y-auto overscroll-contain px-3.5 py-3.5" data-mobile-dynamic-menu="true">' . $content . '</div>';
	}

	private static function item( object $node ): string {
		$title = self::text( $node->title ?? '' );
		$url = self::url( $node->url ?? '#' );
		$children = $node->children ?? array();
		if ( ! $children ) {
			return '<a href="' . $url . '" class="flex items-center justify-between rounded-xl border border-slate-200/80 bg-white px-3.5 py-3 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-900"><span>' . $title . '</span></a>';
		}
		$key = (string) (int) $node->ID;
		$children_html = '';
		foreach ( $children as $child ) { $children_html .= self::item( $child ); }
		return '<div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white"><button type="button" data-mobile-acc-btn="' . $key . '" aria-expanded="false" class="flex w-full items-center justify-between px-3.5 py-3 text-right cursor-pointer hover:bg-slate-50"><span class="text-xs font-black text-slate-900">' . $title . '</span>' . Icons::svg( 'chevron-down', 'h-4 w-4 text-slate-500 transition-transform duration-200' ) . '</button><div data-mobile-acc-panel="' . $key . '" class="hidden flex-col gap-1 border-t border-slate-100 bg-slate-50/50 p-2">' . $children_html . '</div></div>';
	}

	private static function tree( array $items ): array {
		$nodes = array();
		foreach ( $items as $item ) {
			if ( ! is_object( $item ) || empty( $item->ID ) ) { continue; }
			$node = new \stdClass();
			foreach ( get_object_vars( $item ) as $key => $value ) { $node->$key = $value; }
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

	private static function text( $value ): string {
		$value = is_scalar( $value ) ? (string) $value : '';
		return function_exists( 'esc_html' ) ? esc_html( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}

	private static function url( $value ): string {
		$value = is_scalar( $value ) ? (string) $value : '';
		return function_exists( 'esc_url' ) ? esc_url( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}
}
