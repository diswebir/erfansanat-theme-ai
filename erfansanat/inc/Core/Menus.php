<?php
/**
 * Navigation menu locations and safe fallbacks.
 *
 * Four locations mirror the live site contract: main-mega-menu (desktop),
 * mobile-menu (drawer), tolidat-sherkat (footer/header product links) and
 * landing (section-scroll landing pages). Every location renders real links
 * even before an admin assigns a menu.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class Menus {

	public const LOCATIONS = array(
		'main-mega-menu'  => 'منوی اصلی دسکتاپ (مگامنو)',
		'mobile-menu'     => 'منوی موبایل',
		'tolidat-sherkat' => 'تولیدات شرکت (فوتر و هدر)',
		'landing'         => 'منوی لندینگ (اسکرول بخش‌ها)',
	);

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		if ( did_action( 'after_setup_theme' ) ) {
			self::register();
		} else {
			add_action( 'after_setup_theme', array( __CLASS__, 'register' ) );
		}
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	public static function register(): void {
		register_nav_menus( self::LOCATIONS );
	}

	/**
	 * Render a location with a guaranteed fallback.
	 *
	 * @param array<string,mixed> $args wp_nav_menu args.
	 * @return string|false|null
	 */
	public static function render( string $location, array $args = array() ) {
		$defaults = array(
			'theme_location' => $location,
			'container'      => false,
			'menu_class'     => 'es-menu es-menu-' . $location,
			'fallback_cb'    => array( __CLASS__, 'fallback' ),
			'echo'           => true,
		);

		return wp_nav_menu( array_merge( $defaults, $args ) );
	}

	/**
	 * wp_nav_menu fallback: a plain, escaped list of the location defaults.
	 *
	 * @param array<string,mixed> $args
	 */
	public static function fallback( array $args = array() ) {
		$location = (string) ( $args['theme_location'] ?? '' );
		$items    = self::fallback_items( $location );
		if ( empty( $items ) ) {
			return '';
		}

		$class = isset( $args['menu_class'] ) ? (string) $args['menu_class'] : 'es-menu es-menu-' . $location;

		// Footer product links carry the mirror's hover class (P04-T018).
		$link_class = ( 'tolidat-sherkat' === $location ) ? ' class="transition-colors hover:text-emerald-700"' : '';

		$html = '<ul class="' . esc_attr( $class ) . '">';
		foreach ( $items as $item ) {
			$html .= sprintf(
				'<li><a href="%1$s"%2$s>%3$s</a></li>',
				esc_url( (string) ( $item['url'] ?? '' ) ),
				$link_class,
				esc_html( (string) ( $item['label'] ?? '' ) )
			);
		}
		$html .= '</ul>';

		if ( ! empty( $args['echo'] ) ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built escaped above.
			return null;
		}

		return $html;
	}

	/**
	 * Default items per location (filterable for the live site).
	 *
	 * @return array<int,array{label:string,url:string}>
	 */
	public static function fallback_items( string $location ): array {
		switch ( $location ) {
			case 'main-mega-menu':
			case 'mobile-menu':
				$items = array(
					array( 'label' => 'صفحه اصلی', 'url' => home_url( '/' ) ),
					array( 'label' => 'محصولات', 'url' => self::page_url( 'shop' ) ),
					array( 'label' => 'پروژه‌ها', 'url' => self::page_url( 'projects' ) ),
					array( 'label' => 'خدمات', 'url' => self::page_url( 'services' ) ),
					array( 'label' => 'درباره شرکت', 'url' => self::page_url( 'about' ) ),
					array( 'label' => 'وبلاگ', 'url' => self::page_url( 'blog' ) ),
					array( 'label' => 'تماس با ما', 'url' => self::page_url( 'contact' ) ),
				);
				break;

			case 'tolidat-sherkat':
				$items = self::product_category_items();
				break;

			case 'landing':
				$items = array(
					array( 'label' => 'آغاز', 'url' => '#hero' ),
					array( 'label' => 'محصولات', 'url' => '#products' ),
					array( 'label' => 'پروژه‌ها', 'url' => '#projects' ),
					array( 'label' => 'خدمات', 'url' => '#services' ),
					array( 'label' => 'درباره ما', 'url' => '#about' ),
					array( 'label' => 'تماس', 'url' => '#contact' ),
				);
				break;

			default:
				$items = array();
		}

		/**
		 * Filter the fallback items of a menu location.
		 *
		 * @param array<int,array{label:string,url:string}> $items
		 * @param string $location
		 */
		return apply_filters( 'es_menu_fallback_items', $items, $location );
	}

	/**
	 * Product categories for the tolidat-sherkat fallback. Slug-agnostic:
	 * terms are queried from the taxonomy, never hardcoded (D3).
	 *
	 * @return array<int,array{label:string,url:string}>
	 */
	private static function product_category_items(): array {
		$items = array();

		if ( taxonomy_exists( 'product_cat' ) ) {
			$terms = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'number'     => 8,
					'hide_empty' => false,
					'orderby'    => 'name',
					'order'      => 'ASC',
				)
			);
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$url = get_term_link( $term );
					if ( ! is_wp_error( $url ) ) {
						$items[] = array( 'label' => $term->name, 'url' => $url );
					}
				}
			}
		}

		if ( empty( $items ) ) {
			$items = array(
				array( 'label' => 'پوینت لایت پیکسل LED (IP68)', 'url' => home_url( '/product_cat/point-light/' ) ),
				array( 'label' => 'ریسه و تزئینات نورپردازی شهری', 'url' => home_url( '/product_cat/strings-and-lighting-decorations/' ) ),
				array( 'label' => 'ریسه بلوطی LED هفت‌رنگ', 'url' => home_url( '/product_cat/acorn-thread/' ) ),
				array( 'label' => 'ریسه سوزنی شهری ضدآب (IP68)', 'url' => home_url( '/product_cat/needle-thread/' ) ),
				array( 'label' => 'درخت نوری هوشمند (دلسا و آرتام)', 'url' => home_url( '/product_cat/light-tree/' ) ),
				array( 'label' => 'المان‌های نورپردازی و خورشیدنما', 'url' => home_url( '/product_cat/urban-lighting-elements/' ) ),
				array( 'label' => 'لینیرلایت و والواشر نما', 'url' => home_url( '/product_cat/wall-washer/' ) ),
				array( 'label' => 'باکس کنترل هوشمند نورپردازی', 'url' => home_url( '/product_cat/control-box/' ) ),
			);
		}

		return $items;
	}

	/**
	 * URL of a standard page: real permalink when the page exists, otherwise
	 * the canonical site path (pages are created in Phase 7).
	 */
	public static function page_url( string $slug ): string {
		$page = get_page_by_path( $slug );
		$url   = ( $page instanceof \WP_Post ) ? get_permalink( $page ) : home_url( '/' . $slug . '/' );

		/**
		 * Filter a standard page URL used by menus/CTAs.
		 *
		 * @param string $url
		 * @param string $slug
		 */
		return (string) apply_filters( 'es_menu_page_url', $url, $slug );
	}
}
