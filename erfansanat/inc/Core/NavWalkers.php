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
