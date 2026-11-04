<?php
/** Bounded, lightweight search-modal seed data; AJAX behavior remains in main.js. */

namespace ErfanSanat\Theme\Search;

defined( 'ABSPATH' ) || exit;

/** Queries at most eight published records per content group and renders escaped cards. */
final class SearchData {

	/** Hydrate the static modal shell with bounded results and add an articles column. */
	public static function hydrate( string $modal ): string {
		$products = self::product_markup();
		$projects = self::post_markup( 'projects', 'پروژه', 'project' );
		$articles = self::post_markup( 'post', 'مقاله', 'article' );
		if ( '' !== $products ) { $modal = self::replace_contents( $modal, 'data-global-search-products="true"', $products ); }
		if ( '' !== $projects ) { $modal = self::replace_contents( $modal, 'data-global-search-projects="true"', $projects ); }
		$modal = self::add_articles_column( $modal, $articles );
		return $modal;
	}

	/** Public count helper used by tests and performance instrumentation. */
	public static function results(): array {
		return array(
			'products' => self::products(),
			'projects' => self::posts( 'projects' ),
			'articles' => self::posts( 'post' ),
		);
	}

	private static function products(): array {
		if ( ! function_exists( 'wc_get_products' ) ) { return array(); }
		$items = wc_get_products( array( 'status' => 'publish', 'limit' => 8, 'return' => 'objects', 'orderby' => 'menu_order', 'order' => 'ASC' ) );
		return is_array( $items ) ? array_slice( array_values( array_filter( $items, 'is_object' ) ), 0, 8 ) : array();
	}

	private static function posts( string $post_type ): array {
		if ( ! in_array( $post_type, array( 'projects', 'post' ), true ) || ! class_exists( '\\WP_Query' ) ) { return array(); }
		$query = new \WP_Query( array(
			'post_type'              => $post_type,
			'post_status'            => 'publish',
			'posts_per_page'         => 8,
			'no_found_rows'          => true,
			'ignore_sticky_posts'    => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'                 => 'all',
		) );
		return isset( $query->posts ) && is_array( $query->posts ) ? array_slice( array_values( array_filter( $query->posts, 'is_object' ) ), 0, 8 ) : array();
	}

	private static function product_markup(): string {
		$items = self::products();
		$html = '';
		foreach ( $items as $product ) {
			if ( ! method_exists( $product, 'get_id' ) || ! method_exists( $product, 'get_name' ) ) { continue; }
			$id = absint( $product->get_id() );
			$title = (string) $product->get_name();
			$url = method_exists( $product, 'get_permalink' ) ? (string) $product->get_permalink() : ( function_exists( 'get_permalink' ) ? (string) get_permalink( $id ) : '' );
			$excerpt = method_exists( $product, 'get_short_description' ) ? wp_strip_all_tags( (string) $product->get_short_description() ) : '';
			$html .= self::card( $title, $excerpt, $url, 'محصول', 'product' );
		}
		return $html;
	}

	private static function post_markup( string $post_type, string $kind, string $class ): string {
		$html = '';
		foreach ( self::posts( $post_type ) as $post ) {
			$id = absint( $post->ID ?? 0 );
			if ( ! $id ) { continue; }
			$title = (string) ( $post->post_title ?? '' );
			$excerpt = isset( $post->post_excerpt ) ? (string) $post->post_excerpt : '';
			if ( '' === $excerpt && function_exists( 'get_the_excerpt' ) ) { $excerpt = (string) get_the_excerpt( $post ); }
			$url = function_exists( 'get_permalink' ) ? (string) get_permalink( $id ) : '';
			$html .= self::card( $title, $excerpt, $url, $kind, $class );
		}
		return $html;
	}

	private static function card( string $title, string $excerpt, string $url, string $kind, string $type ): string {
		if ( '' === $title || '' === $url ) { return ''; }
		$title_html = self::html( $title );
		$summary = trim( preg_replace( '/\\s+/u', ' ', wp_strip_all_tags( $excerpt ) ) );
		$search = self::attr( trim( $title . ' ' . $summary . ' ' . $kind ) );
		return '<a href="' . self::url( $url ) . '" data-search-item="' . $search . '" data-search-kind="' . self::attr( $type ) . '" class="flex items-center justify-between gap-3 rounded-xl border border-slate-100 bg-slate-50/70 p-2.5 transition-colors hover:border-emerald-300 hover:bg-emerald-50/50"><div class="min-w-0"><div class="truncate text-xs font-extrabold text-slate-900">' . $title_html . '</div><div class="truncate text-[10.5px] font-semibold text-slate-500">' . self::html( $kind ) . ( '' !== $summary ? ' · ' . self::html( ( function_exists( 'mb_substr' ) ? mb_substr( $summary, 0, 90 ) : substr( $summary, 0, 90 ) ) ) : '' ) . '</div></div></a>';
	}

	private static function replace_contents( string $html, string $marker, string $contents ): string {
		$pos = strpos( $html, $marker );
		$start = false === $pos ? false : strrpos( substr( $html, 0, $pos ), '<div' );
		if ( false === $start ) { return $html; }
		$end = self::div_end( $html, $start );
		if ( false === $end ) { return $html; }
		$open_end = strpos( $html, '>', $start );
		if ( false === $open_end ) { return $html; }
		return substr( $html, 0, $open_end + 1 ) . $contents . substr( $html, $end );
	}

	private static function add_articles_column( string $modal, string $articles ): string {
		$marker = 'class="mt-4 grid gap-6 md:grid-cols-2"';
		$pos = strpos( $modal, $marker );
		if ( false === $pos ) { return $modal; }
		$start = strrpos( substr( $modal, 0, $pos ), '<div' );
		if ( false === $start ) { return $modal; }
		$end = self::div_end( $modal, $start );
		if ( false === $end ) { return $modal; }
		$grid = substr( $modal, $start, $end - $start );
		$grid = str_replace( 'md:grid-cols-2', 'md:grid-cols-3', $grid );
		$close = strrpos( $grid, '</div>' );
		if ( false === $close ) { return $modal; }
		$empty = '<p class="rounded-xl border border-slate-100 bg-slate-50/70 p-3 text-[11px] font-semibold text-slate-500">مقاله‌ای برای نمایش پیدا نشد.</p>';
		$archive = function_exists( 'get_post_type_archive_link' ) ? get_post_type_archive_link( 'post' ) : false;
		if ( ! is_string( $archive ) || '' === $archive ) { $archive = function_exists( 'home_url' ) ? home_url( '/blog/' ) : '/blog/'; }
		$column = '<div><div class="mb-2 flex items-center justify-between text-xs font-extrabold text-slate-500"><span>مقالات و راهنماها</span><a href="' . self::url( $archive ) . '" class="text-emerald-700 hover:underline">همه مقالات ←</a></div><div data-global-search-articles="true" class="space-y-2">' . ( '' !== $articles ? $articles : $empty ) . '</div></div>';
		$grid = substr( $grid, 0, $close ) . $column . substr( $grid, $close );
		return substr( $modal, 0, $start ) . $grid . substr( $modal, $end );
	}

	private static function div_end( string $html, int $start ) {
		if ( ! preg_match_all( '/<div\\b[^>]*>|<\\/div\\s*>/i', substr( $html, $start ), $tokens, PREG_OFFSET_CAPTURE ) ) { return false; }
		$depth = 0;
		foreach ( $tokens[0] as $token ) {
			$depth += 0 === strpos( $token[0], '</' ) ? -1 : 1;
			if ( 0 === $depth ) { return $start + $token[1] + strlen( $token[0] ); }
		}
		return false;
	}

	private static function html( string $value ): string { return function_exists( 'esc_html' ) ? esc_html( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
	private static function attr( string $value ): string { return function_exists( 'esc_attr' ) ? esc_attr( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
	private static function url( string $value ): string { return function_exists( 'esc_url' ) ? esc_url( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
}
