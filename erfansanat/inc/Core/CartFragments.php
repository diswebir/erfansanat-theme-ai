<?php
/**
 * Real WooCommerce cart fragments + AJAX (P06-T023).
 *
 * Connects the header cart badge and the cart modal to the REAL WC()->cart via
 * the standard `woocommerce_add_to_cart_fragments` filter (AC-02) and nonce-
 * guarded AJAX add/remove/update endpoints (AC-01). The server always renders
 * from WC()->cart, so the browser localStorage demo cart can never replace the
 * real cart totals (AC-03).
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class CartFragments {

	public const NONCE_ACTION = 'es_cart_fragments';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_filter( 'woocommerce_add_to_cart_fragments', array( __CLASS__, 'add_to_cart_fragments' ) );

		foreach ( array( 'add', 'remove', 'update' ) as $es_action ) {
			add_action( 'wp_ajax_es_cart_' . $es_action, array( __CLASS__, 'ajax_' . $es_action ) );
			add_action( 'wp_ajax_nopriv_es_cart_' . $es_action, array( __CLASS__, 'ajax_' . $es_action ) );
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'inline_config' ), 30 );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/** Real cart count (filterable, mirrors the header badge). */
	public static function count(): int {
		$count = ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
		return (int) apply_filters( 'es_header_cart_count', $count );
	}

	public static function badge_html(): string {
		return '<span data-header-cart-badge="true" class="absolute -top-1.5 -right-1.5 grid h-4.5 min-w-4.5 place-items-center rounded-full bg-emerald-700 px-1 text-[10px] font-black text-white shadow-xs ring-2 ring-white tabular-nums xl:static xl:h-5 xl:min-w-5 xl:text-[10.5px] xl:shadow-none xl:ring-0">' . esc_html( number_format_i18n( self::count() ) ) . '</span>';
	}

	public static function modal_count_html(): string {
		return '<span data-cart-modal-count="true" class="rounded-lg bg-emerald-100 px-2 py-0.5 text-[11px] font-black text-emerald-900 tabular-nums">' . esc_html( number_format_i18n( self::count() ) . ' کالا' ) . '</span>';
	}

	public static function modal_total_html(): string {
		$total = ( function_exists( 'WC' ) && WC()->cart ) ? (string) WC()->cart->get_cart_total() : number_format_i18n( 0 ) . ' تومان';
		return '<div data-cart-modal-total="true" class="text-base font-black text-emerald-800 sm:text-lg tabular-nums">' . wp_kses_post( $total ) . '</div>';
	}

	/**
	 * Standard Woo fragments: badge + modal count/total refresh without reload.
	 *
	 * @param array<string,string> $fragments Existing fragments.
	 * @return array<string,string>
	 */
	public static function add_to_cart_fragments( $fragments ): array {
		$fragments = is_array( $fragments ) ? $fragments : array();
		$fragments['span[data-header-cart-badge="true"]'] = self::badge_html();
		$fragments['span[data-cart-modal-count="true"]']  = self::modal_count_html();
		$fragments['div[data-cart-modal-total="true"]']   = self::modal_total_html();
		return $fragments;
	}

	/** Fragment payload returned by the AJAX endpoints. */
	public static function get_fragments(): array {
		return array(
			'count'     => self::count(),
			'fragments' => self::add_to_cart_fragments( array() ),
		);
	}

	public static function verify_nonce_string( string $nonce ): bool {
		return (bool) wp_verify_nonce( $nonce, self::NONCE_ACTION );
	}

	private static function verify_nonce(): bool {
		$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
		return self::verify_nonce_string( $nonce );
	}

	/** Pure add logic (no die) — returns a result array. */
	public static function add_item( int $product_id, int $quantity ): array {
		if ( ! $product_id || ! function_exists( 'WC' ) || ! WC()->cart ) {
			return array( 'success' => false, 'message' => 'invalid_product' );
		}
		// Inquiry products never enter the real cart (AC-03).
		if ( 'true' !== (string) get_post_meta( $product_id, 'true_sale', true ) ) {
			return array( 'success' => false, 'message' => 'inquiry_only' );
		}
		WC()->cart->add_to_cart( $product_id, max( 1, $quantity ) );
		return array( 'success' => true ) + self::get_fragments();
	}

	public static function remove_item( string $key ): array {
		if ( '' === $key || ! function_exists( 'WC' ) || ! WC()->cart ) {
			return array( 'success' => false, 'message' => 'invalid_key' );
		}
		WC()->cart->remove_cart_item( $key );
		return array( 'success' => true ) + self::get_fragments();
	}

	public static function update_item( string $key, int $quantity ): array {
		if ( '' === $key || ! function_exists( 'WC' ) || ! WC()->cart ) {
			return array( 'success' => false, 'message' => 'invalid_key' );
		}
		WC()->cart->set_quantity( $key, $quantity );
		return array( 'success' => true ) + self::get_fragments();
	}

	private static function respond( array $result ): void {
		if ( empty( $result['success'] ) ) {
			wp_send_json_error( array( 'message' => $result['message'] ?? 'error' ), 400 );
		}
		unset( $result['success'] );
		wp_send_json_success( $result );
	}

	public static function ajax_add(): void {
		if ( ! self::verify_nonce() ) {
			wp_send_json_error( array( 'message' => 'nonce_invalid' ), 403 );
		}
		$product_id = isset( $_REQUEST['product_id'] ) ? (int) $_REQUEST['product_id'] : 0;
		$quantity   = isset( $_REQUEST['quantity'] ) ? (int) $_REQUEST['quantity'] : 1;
		self::respond( self::add_item( $product_id, $quantity ) );
	}

	public static function ajax_remove(): void {
		if ( ! self::verify_nonce() ) {
			wp_send_json_error( array( 'message' => 'nonce_invalid' ), 403 );
		}
		$key = isset( $_REQUEST['key'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['key'] ) ) : '';
		self::respond( self::remove_item( $key ) );
	}

	public static function ajax_update(): void {
		if ( ! self::verify_nonce() ) {
			wp_send_json_error( array( 'message' => 'nonce_invalid' ), 403 );
		}
		$key      = isset( $_REQUEST['key'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['key'] ) ) : '';
		$quantity = isset( $_REQUEST['quantity'] ) ? (int) $_REQUEST['quantity'] : 0;
		self::respond( self::update_item( $key, $quantity ) );
	}

	/** Expose the AJAX URL + a real-cart nonce to the front-end. */
	public static function inline_config(): void {
		if ( ! wp_script_is( 'es-main', 'enqueued' ) ) {
			return;
		}
		$config = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( self::NONCE_ACTION ),
			'realCart'=> true, // AC-03: server cart is authoritative.
		);
		wp_add_inline_script( 'es-main', 'window.esCart = ' . wp_json_encode( $config ) . ';', 'before' );
	}
}
