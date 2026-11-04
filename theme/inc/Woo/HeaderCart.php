<?php
/** WooCommerce fragment integration for the shared header cart quantity. */

namespace ErfanSanat\Theme\Woo;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Keeps the static badge styling and exposes one escaped Woo cart count. */
final class HeaderCart implements ModuleInterface {

	public function register(): void {
		add_filter( 'woocommerce_add_to_cart_fragments', array( self::class, 'fragments' ), 20 );
		add_action( 'wp_enqueue_scripts', array( self::class, 'enqueue_assets' ), 30 );
	}

	public static function fragments( array $fragments ): array {
		$fragments['span[data-header-cart-badge="true"]'] = self::badge_markup();
		$fragments['[data-cart-modal-count="true"]'] = self::modal_count_markup();
		return $fragments;
	}

	/** Replace the static cart-modal count span with the live server count. */
	public static function hydrate_modal( string $html ): string {
		return (string) preg_replace( '#<span(?=[^>]*data-cart-modal-count="true")[^>]*>.*?</span>#s', self::modal_count_markup(), $html, 1 );
	}

	/** Fragment markup for the modal's count label. */
	public static function modal_count_markup( ?int $count = null ): string {
		$count = null === $count ? self::cart_count() : max( 0, $count );
		$display = function_exists( 'number_format_i18n' ) ? number_format_i18n( $count, 0 ) : (string) $count;
		$safe = function_exists( 'esc_html' ) ? esc_html( (string) $display ) : htmlspecialchars( (string) $display, ENT_QUOTES, 'UTF-8' );
		$data_count = function_exists( 'esc_attr' ) ? esc_attr( (string) $display ) : htmlspecialchars( (string) $display, ENT_QUOTES, 'UTF-8' );
		return '<span data-cart-modal-count="true" data-wc-cart-count="' . $data_count . '" data-wc-cart-suffix=" کالا" class="rounded-lg bg-emerald-100 px-2 py-0.5 text-[11px] font-black text-emerald-900 tabular-nums">' . $safe . ' کالا</span>';
	}

	/** Render the exact static badge class list with a server-derived count. */
	public static function badge_markup( ?int $count = null ): string {
		$count = null === $count ? self::cart_count() : max( 0, $count );
		$display = function_exists( 'number_format_i18n' ) ? number_format_i18n( $count, 0 ) : (string) $count;
		$safe = function_exists( 'esc_html' ) ? esc_html( (string) $display ) : htmlspecialchars( (string) $display, ENT_QUOTES, 'UTF-8' );
		$data_count = function_exists( 'esc_attr' ) ? esc_attr( (string) $display ) : htmlspecialchars( (string) $display, ENT_QUOTES, 'UTF-8' );
		return '<span data-header-cart-badge="true" data-wc-cart-count="' . $data_count . '" class="absolute -top-1.5 -right-1.5 grid h-4.5 min-w-4.5 place-items-center rounded-full bg-emerald-700 px-1 text-[10px] font-black text-white shadow-xs ring-2 ring-white tabular-nums xl:static xl:h-5 xl:min-w-5 xl:text-[10.5px] xl:shadow-none xl:ring-0">' . $safe . '</span>';
	}

	/** Return zero safely when WooCommerce or its cart is unavailable. */
	public static function cart_count(): int {
		if ( ! function_exists( 'WC' ) ) { return 0; }
		$woocommerce = WC();
		if ( ! is_object( $woocommerce ) || ! isset( $woocommerce->cart ) || ! is_object( $woocommerce->cart ) || ! method_exists( $woocommerce->cart, 'get_cart_contents_count' ) ) { return 0; }
		return max( 0, absint( $woocommerce->cart->get_cart_contents_count() ) );
	}

	/** Enqueue Woo's fragment script once and keep the server count after legacy JS init. */
	public static function enqueue_assets(): void {
		if ( ! function_exists( 'WC' ) || ( function_exists( 'is_admin' ) && is_admin() ) ) { return; }
		$base = function_exists( 'get_template_directory_uri' ) ? get_template_directory_uri() : '';
		$version = defined( 'ES_THEME_VERSION' ) ? ES_THEME_VERSION : '1.0.0';
		if ( function_exists( 'wp_script_is' ) && wp_script_is( 'wc-cart-fragments', 'registered' ) && ! wp_script_is( 'wc-cart-fragments', 'enqueued' ) ) {
			wp_enqueue_script( 'wc-cart-fragments' );
		}
		if ( function_exists( 'wp_enqueue_script' ) ) {
			wp_enqueue_script( 'es-header-cart', $base . '/assets/js/header-cart.js', array( 'es-main' ), $version, true );
		}
	}
}
