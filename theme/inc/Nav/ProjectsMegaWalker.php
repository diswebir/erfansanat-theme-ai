<?php
/** Builds the two-tab project mega menu from city and project_type terms. */

namespace ErfanSanat\Theme\Nav;

defined( 'ABSPATH' ) || exit;

/** Cached project taxonomy renderer with a no-empty-tabs policy. */
final class ProjectsMegaWalker {

	/** Render the project panel, or empty string when both taxonomies are empty. */
	public static function render(): string {
		if ( ! function_exists( 'get_terms' ) ) { return ''; }
		$types = self::terms( 'project_type' );
		$cities = self::terms( 'city' );
		if ( ! $types && ! $cities ) { return ''; }

		$tabs = '';
		$panels = '';
		$first = true;
		foreach ( array( 'type' => array( 'terms' => $types, 'label' => 'بر اساس نوع پروژه', 'taxonomy' => 'project_type' ), 'city' => array( 'terms' => $cities, 'label' => 'بر اساس شهر', 'taxonomy' => 'city' ) ) as $key => $group ) {
			if ( ! $group['terms'] ) { continue; }
			$links = '';
			foreach ( $group['terms'] as $term ) {
				if ( ! is_object( $term ) || empty( $term->term_id ) ) { continue; }
				$url = get_term_link( $term, $group['taxonomy'] );
				if ( function_exists( 'is_wp_error' ) && is_wp_error( $url ) ) { continue; }
				$href = function_exists( 'esc_url' ) ? esc_url( $url ) : htmlspecialchars( (string) $url, ENT_QUOTES, 'UTF-8' );
				$links .= '<a href="' . $href . '" class="group flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50/60 px-3 py-2.5 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-200 hover:bg-emerald-50/60 hover:text-emerald-950"><span class="truncate">' . self::text( $term->name ?? '' ) . '</span>' . Icons::svg( 'chevron-down', 'h-3.5 w-3.5 -rotate-90 text-slate-300' ) . '</a>';
			}
			if ( '' === $links ) { continue; }
			$active = $first;
			$tabs .= '<button type="button" data-mega-proj-tab="' . $key . '" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right transition-all cursor-pointer ' . ( $active ? 'bg-white text-emerald-950 shadow-2xs ring-1 ring-emerald-200' : 'text-slate-700 hover:bg-white/70' ) . '"><span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg ' . ( $active ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-700' ) . '">' . Icons::svg( 'type' === $key ? 'layers' : 'map-pin', 'h-4 w-4' ) . '</span><span class="min-w-0 flex-1 truncate text-xs font-extrabold">' . self::text( $group['label'] ) . '</span>' . Icons::svg( 'chevron-down', 'h-3.5 w-3.5 shrink-0 -rotate-90 text-slate-300' ) . '</button>';
			$panels .= '<div data-mega-proj-panel="' . $key . '" class="' . ( $active ? 'block' : 'hidden' ) . '"><div class="mb-3 border-b border-slate-100 pb-2.5 text-xs font-black text-slate-900">' . self::text( $group['label'] ) . '</div><div class="grid grid-cols-2 gap-1.5">' . $links . '</div></div>';
			$first = false;
		}
		if ( '' === $panels ) { return ''; }
		$trigger = '<button type="button" data-desktop-mega-trigger="projects" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700"><span>پروژه‌ها</span>' . Icons::svg( 'chevron-down', 'h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400' ) . '</button>';
		return '<div class="group static" data-mega-menu-projects="true">' . $trigger . '<div data-desktop-mega-panel="projects" class="absolute inset-x-4 top-full z-50 mx-auto max-w-[780px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"><div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]"><div class="grid grid-cols-12"><div class="col-span-5 border-l border-slate-100 bg-slate-50/70 p-3.5"><div class="mb-2 px-2 text-[10.5px] font-extrabold text-slate-400">تفکیک پروژه‌ها</div><div class="space-y-1">' . $tabs . '</div></div><div class="col-span-7 flex flex-col justify-between p-4">' . $panels . '</div></div></div></div></div>';
	}

	/** @return array<int,object> */
	public static function terms( string $taxonomy ): array { return NavTerms::taxonomy( $taxonomy ); }

	private static function text( $value ): string {
		$value = is_scalar( $value ) ? (string) $value : '';
		return function_exists( 'esc_html' ) ? esc_html( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}
}
