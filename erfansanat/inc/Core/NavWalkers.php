<?php
/**
 * Navigation walkers (desktop mega menu, mobile accordion, footer, landing).
 *
 * All walkers live in one file under the NavWalkers sub-namespace and are
 * required from functions.php (the PSR-4 autoloader maps one class per file).
 * DOM/data-attributes mirror static-mirror/index.html so the bundled main.js
 * wires the interactions without changes.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core\NavWalkers;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\MegaMenuTab;

/**
 * Desktop navigation walker: simple links, dropdowns and mega panels.
 */
class DesktopWalker extends \Walker_Nav_Menu {

	/** @var string|null Mega type currently being rendered (suppresses children). */
	private ?string $mega_type = null;

	/** @var array<int,bool> Top-level items that opened a wrapper div. */
	private array $wrapper = array();

	/** @var string Current dropdown panel id. */
	private string $dropdown_id = '';

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( null !== $this->mega_type ) {
			return; // Mega panels are taxonomy-driven, not menu children.
		}
		if ( 0 === $depth ) {
			$output .= '<div data-desktop-mega-panel="' . esc_attr( $this->dropdown_id ) . '" class="absolute right-0 top-full z-50 w-[340px] pt-2.5 transition-all duration-200 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"><div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-2 shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]"><div class="space-y-1">';
		}
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( null !== $this->mega_type ) {
			return;
		}
		if ( 0 === $depth ) {
			$output .= '</div></div></div>';
		}
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		// Suppress menu children inside a mega item.
		if ( $depth > 0 && null !== $this->mega_type ) {
			return;
		}

		$title = trim( wp_strip_all_tags( (string) $item->title ) );
		$url   = (string) $item->url;

		// Dropdown / mega children render as compact links.
		if ( $depth > 0 ) {
			$output .= '<a href="' . esc_url( $url ) . '" class="group/srv flex items-center justify-between rounded-xl px-3 py-2 text-xs font-extrabold text-slate-700 transition-colors hover:bg-emerald-50/70 hover:text-emerald-900"><span class="truncate">' . esc_html( $title ) . '</span>' . self::arrow_left_svg() . '</a>';
			return;
		}

		$type         = $this->mega_type_for( $item );
		$has_children = ! empty( $this->has_children ) || ( isset( $args->has_children ) && (bool) $args->has_children );
		$classes      = (array) $item->classes;
		$is_current   = in_array( 'current-menu-item', $classes, true )
			|| in_array( 'current_page_item', $classes, true )
			|| in_array( 'current-menu-ancestor', $classes, true );

		// Mega panel items (products / projects / corporate).
		if ( in_array( $type, array( 'products', 'projects', 'corporate' ), true ) && $this->is_mega_enabled( $type ) ) {
			$this->mega_type = $type;

			$output .= '<div class="group static" data-mega-menu-' . esc_attr( $type ) . '="true">';
			$output .= '<button type="button" data-desktop-mega-trigger="' . esc_attr( $type ) . '" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700"><span>' . esc_html( $title ) . '</span>' . self::chevron_down_svg() . '</button>';

			ob_start();
			get_template_part( 'template-parts/header/mega-' . $type );
			$output .= (string) ob_get_clean();

			$output .= '</div>';
			return;
		}

		// Simple dropdown (e.g. services) when the item has children.
		if ( $has_children ) {
			$this->dropdown_id    = ( 'services' === $type ) ? 'services' : 'dropdown-' . (int) $item->ID;
			$this->wrapper[ (int) $item->ID ] = true;

			$output .= '<div class="group relative">';
			$output .= '<button type="button" data-desktop-mega-trigger="' . esc_attr( $this->dropdown_id ) . '" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700"><span>' . esc_html( $title ) . '</span>' . self::chevron_down_svg() . '</button>';
			return;
		}

		// Plain link.
		$link_class = $is_current
			? 'rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap bg-emerald-50 text-emerald-800'
			: 'rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700';

		$output .= '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $link_class ) . '"' . ( $is_current ? ' aria-current="page"' : '' ) . '>' . esc_html( $title ) . '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( 0 !== $depth ) {
			return;
		}
		if ( null !== $this->mega_type ) {
			$this->mega_type = null; // Wrapper already closed in start_el.
			return;
		}
		if ( isset( $this->wrapper[ (int) $item->ID ] ) ) {
			$output .= '</div>';
			unset( $this->wrapper[ (int) $item->ID ] );
		}
	}

	/**
	 * Resolve a mega type from the item CSS class or its title.
	 */
	private function mega_type_for( $item ): ?string {
		$classes = (array) $item->classes;
		foreach ( array( 'products', 'projects', 'corporate', 'services' ) as $type ) {
			if ( in_array( 'mega-' . $type, $classes, true ) ) {
				return $type;
			}
		}
		$title = trim( wp_strip_all_tags( (string) $item->title ) );
		$map   = array(
			'محصولات'      => 'products',
			'پروژه‌ها'     => 'projects',
			'درباره شرکت'  => 'corporate',
			'خدمات'        => 'services',
		);
		return $map[ $title ] ?? null;
	}

	private function is_mega_enabled( string $type ): bool {
		switch ( $type ) {
			case 'products':
				$config = MegaMenuTab::products_config();
				return ! empty( $config['enabled'] );
			case 'projects':
				$config = MegaMenuTab::projects_config();
				return ! empty( $config['enabled'] );
			case 'corporate':
				return (bool) es_option( 'mega_corporate_enabled' );
		}
		return false;
	}

	public static function chevron_down_svg(): string {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>';
	}

	public static function arrow_left_svg(): string {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3 w-3 shrink-0 text-slate-300 transition-transform group-hover/cat:-translate-x-0.5 group-hover/cat:text-emerald-700" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>';
	}
}

/**
 * Mobile accordion walker: collapsible drawer navigation with mega-menu
 * inner tabs (products / projects) sharing the desktop data source.
 */
class MobileWalker extends \Walker_Nav_Menu {

	/** @var array<int,bool> Top-level items that opened an accordion wrapper. */
	private array $wrapper = array();

	/** @var string Current accordion panel key. */
	private string $acc_key = '';

	/** @var string|null Mega type whose children are suppressed (tabs used). */
	private ?string $mega_type = null;

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		if ( null !== $this->mega_type ) {
			return;
		}
		if ( 0 === $depth ) {
			$output .= '<div data-mobile-acc-panel="' . esc_attr( $this->acc_key ) . '" class="hidden flex-col gap-2.5 border-t border-slate-100 bg-slate-50/50 p-2.5">';
		}
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		if ( null !== $this->mega_type ) {
			return;
		}
		if ( 0 === $depth ) {
			$output .= '</div>';
		}
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		if ( $depth > 0 && null !== $this->mega_type ) {
			return;
		}

		$title = trim( wp_strip_all_tags( (string) $item->title ) );
		$url   = (string) $item->url;

		// Sub-menu children render as compact rows.
		if ( $depth > 0 ) {
			$output .= '<a href="' . esc_url( $url ) . '" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-[11.5px] font-bold text-slate-700 hover:text-emerald-800"><span class="truncate">' . esc_html( $title ) . '</span>' . self::child_arrow() . '</a>';
			return;
		}

		$type         = $this->mega_type_for( $item );
		$has_children = ! empty( $this->has_children ) || ( isset( $args->has_children ) && (bool) $args->has_children );
		$classes      = (array) $item->classes;
		$is_current   = in_array( 'current-menu-item', $classes, true )
			|| in_array( 'current_page_item', $classes, true )
			|| in_array( 'current-menu-ancestor', $classes, true );

		// Mega items with inner tabs (products / projects).
		if ( in_array( $type, array( 'products', 'projects' ), true ) && $this->is_mega_enabled( $type ) ) {
			$this->mega_type = $type;
			$this->acc_key   = $type;

			$inner = ( 'products' === $type ) ? self::render_products_tabs() : self::render_projects_tabs();

			$output .= '<div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white">';
			$output .= '<button type="button" data-mobile-acc-btn="' . esc_attr( $type ) . '" aria-expanded="false" class="flex w-full items-center justify-between px-3.5 py-3 text-right cursor-pointer hover:bg-slate-50"><span class="text-xs font-black text-slate-900">' . esc_html( $title ) . '</span>' . self::acc_icon() . '</button>';
			$output .= '<div data-mobile-acc-panel="' . esc_attr( $type ) . '" class="hidden flex-col gap-2.5 border-t border-slate-100 bg-slate-50/50 p-2.5">' . $inner . '</div>';
			$output .= '</div>';
			return;
		}

		if ( $has_children ) {
			$this->acc_key = $type ? $type : 'item-' . (int) $item->ID;
			$this->wrapper[ (int) $item->ID ] = true;

			$output .= '<div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white">';
			$output .= '<button type="button" data-mobile-acc-btn="' . esc_attr( $this->acc_key ) . '" aria-expanded="false" class="flex w-full items-center justify-between px-3.5 py-3 text-right cursor-pointer hover:bg-slate-50"><span class="text-xs font-black text-slate-900">' . esc_html( $title ) . '</span>' . self::acc_icon() . '</button>';
			return;
		}

		$link_class = $is_current
			? 'flex items-center justify-between rounded-xl border px-3.5 py-3 text-xs font-black transition-colors border-emerald-200 bg-emerald-50 text-emerald-900'
			: 'flex items-center justify-between rounded-xl border px-3.5 py-3 text-xs font-black transition-colors border-slate-200 bg-white text-slate-800';

		$output .= '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $link_class ) . '"' . ( $is_current ? ' aria-current="page"' : '' ) . '><span>' . esc_html( $title ) . '</span>' . self::top_arrow() . '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		if ( 0 !== $depth ) {
			return;
		}
		if ( null !== $this->mega_type ) {
			$this->mega_type = null;
			return;
		}
		if ( isset( $this->wrapper[ (int) $item->ID ] ) ) {
			$output .= '</div>';
			unset( $this->wrapper[ (int) $item->ID ] );
		}
	}

	private function mega_type_for( $item ): ?string {
		$classes = (array) $item->classes;
		foreach ( array( 'products', 'projects', 'corporate', 'services' ) as $type ) {
			if ( in_array( 'mega-' . $type, $classes, true ) ) {
				return $type;
			}
		}
		$title = trim( wp_strip_all_tags( (string) $item->title ) );
		$map   = array(
			'محصولات'     => 'products',
			'پروژه‌ها'    => 'projects',
			'درباره شرکت' => 'corporate',
			'خدمات'       => 'services',
		);
		return $map[ $title ] ?? null;
	}

	private function is_mega_enabled( string $type ): bool {
		if ( 'products' === $type ) {
			$config = MegaMenuTab::products_config();
			return ! empty( $config['enabled'] );
		}
		if ( 'projects' === $type ) {
			$config = MegaMenuTab::projects_config();
			return ! empty( $config['enabled'] );
		}
		return false;
	}

	/**
	 * Products inner tabs — same source/options as the desktop mega panel.
	 */
	public static function render_products_tabs(): string {
		$config  = MegaMenuTab::products_config();
		$per_tab = max( 1, (int) $config['per_tab'] );
		$tab_cap = (int) apply_filters( 'es_mega_products_tabs', 6 );

		$top = get_terms( array(
			'taxonomy'   => 'product_cat',
			'parent'     => 0,
			'hide_empty' => false,
			'orderby'    => 'name',
			'order'      => 'ASC',
			'number'     => $tab_cap,
		) );
		if ( is_wp_error( $top ) || empty( $top ) ) {
			return '<div class="space-y-1">' . self::term_row( 'همه محصولات', home_url( '/shop/' ) ) . '</div>';
		}

		$html = '<div class="grid grid-cols-2 gap-1">';
		foreach ( $top as $i => $term ) {
			$active = ( 0 === $i );
			$html  .= '<button type="button" data-mobile-prod-tab="' . esc_attr( (string) $i ) . '" class="truncate rounded-lg border px-2.5 py-1.5 text-center text-[11px] font-extrabold transition-all cursor-pointer ' . ( $active ? 'border-emerald-600 bg-emerald-700 text-white' : 'border-slate-200 bg-white text-slate-700' ) . '">' . esc_html( $term->name ) . '</button>';
		}
		$html .= '</div>';

		foreach ( $top as $i => $term ) {
			$active   = ( 0 === $i );
			$children = get_terms( array(
				'taxonomy'   => 'product_cat',
				'parent'     => $term->term_id,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'number'     => $per_tab,
			) );
			if ( is_wp_error( $children ) ) {
				$children = array();
			}
			$html .= '<div data-mobile-prod-panel="' . esc_attr( (string) $i ) . '" class="' . ( $active ? 'space-y-1 block' : 'hidden' ) . '">';
			if ( empty( $children ) ) {
				$url   = get_term_link( $term );
				$html .= self::term_row( $term->name, is_wp_error( $url ) ? home_url( '/shop/' ) : $url );
			} else {
				foreach ( $children as $child ) {
					$url   = get_term_link( $child );
					$html .= self::term_row( $child->name, is_wp_error( $url ) ? home_url( '/shop/' ) : $url );
				}
			}
			$html .= '</div>';
		}

		return $html;
	}

	/**
	 * Projects inner tabs (type / city) — same source as the desktop panel.
	 */
	public static function render_projects_tabs(): string {
		$config  = MegaMenuTab::projects_config();
		$per_tab = max( 1, (int) $config['per_tab'] );
		$group   = ( 'city' === $config['group'] ) ? 'city' : 'type';

		$fetch = static function ( $taxonomy, $limit ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				return array();
			}
			$terms = get_terms( array(
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'number'     => $limit,
			) );
			return is_wp_error( $terms ) ? array() : $terms;
		};

		$type_terms = $fetch( 'project_type', $per_tab );
		$city_terms = $fetch( 'city', $per_tab );

		$html  = '<div class="grid grid-cols-2 gap-1 rounded-lg bg-slate-200/70 p-1">';
		$html .= '<button type="button" data-mobile-proj-tab="type" class="rounded-md py-1.5 text-[11px] font-black transition-all cursor-pointer ' . ( 'type' === $group ? 'bg-white text-emerald-800 shadow-2xs' : 'text-slate-600' ) . '">بر اساس نوع سازه</button>';
		$html .= '<button type="button" data-mobile-proj-tab="city" class="rounded-md py-1.5 text-[11px] font-black transition-all cursor-pointer ' . ( 'city' === $group ? 'bg-white text-emerald-800 shadow-2xs' : 'text-slate-600' ) . '">بر اساس شهر اجرا</button>';
		$html .= '</div>';

		$html .= '<div data-mobile-proj-panel="type" class="' . ( 'type' === $group ? 'grid grid-cols-2 gap-1' : 'hidden' ) . '">';
		foreach ( $type_terms as $term ) {
			$url   = get_term_link( $term );
			$html .= self::term_row( $term->name, is_wp_error( $url ) ? home_url( '/projects/' ) : $url );
		}
		$html .= '</div>';

		$html .= '<div data-mobile-proj-panel="city" class="' . ( 'city' === $group ? 'grid grid-cols-2 gap-1' : 'hidden' ) . '">';
		foreach ( $city_terms as $term ) {
			$url   = get_term_link( $term );
			$html .= self::term_row( $term->name, is_wp_error( $url ) ? home_url( '/projects/' ) : $url );
		}
		$html .= '</div>';

		return $html;
	}

	private static function term_row( string $name, string $url ): string {
		return '<a href="' . esc_url( $url ) . '" class="flex items-center justify-between rounded-lg bg-white px-3 py-2 text-[11.5px] font-bold text-slate-700 hover:text-emerald-800"><span class="truncate">' . esc_html( $name ) . '</span>' . self::child_arrow() . '</a>';
	}

	public static function acc_icon(): string {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 text-slate-500 transition-transform duration-200" aria-hidden="true" data-mobile-acc-icon="true"><path d="m6 9 6 6 6-6"></path></svg>';
	}

	public static function child_arrow(): string {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3 w-3 shrink-0 text-slate-300" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>';
	}

	public static function top_arrow(): string {
		return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-3.5 w-3.5 shrink-0 text-slate-400" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>';
	}
}

/**
 * Footer "تولیدات شرکت" walker (P04-T018): a flat, escaped list of real
 * category/product links matching the mirror footer DOM. The surrounding
 * `<ul class="space-y-2.5 text-[13px] text-slate-600">` comes from the
 * menu_class/items_wrap passed by the footer template (AC-03).
 */
class TolidatWalker extends \Walker_Nav_Menu {

	public function start_lvl( &$output, $depth = 0, $args = null ) {
		// Flat footer list — no nested levels are rendered.
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
	}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$title      = trim( wp_strip_all_tags( (string) $item->title ) );
		$url        = (string) $item->url;
		$classes    = (array) $item->classes;
		$is_current = in_array( 'current-menu-item', $classes, true )
			|| in_array( 'current_page_item', $classes, true )
			|| in_array( 'current-menu-ancestor', $classes, true );

		$output .= '<li><a href="' . esc_url( $url ) . '" class="transition-colors hover:text-emerald-700"'
			. ( $is_current ? ' aria-current="page"' : '' ) . '>' . esc_html( $title ) . '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= '</li>';
	}
}
