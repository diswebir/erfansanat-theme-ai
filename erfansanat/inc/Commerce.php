<?php
/**
 * Cart, checkout and account integration.
 *
 * WooCommerce's block cart and checkout render their real interface with
 * JavaScript: the server sends an empty shell and the store API fills it. That
 * is WooCommerce's design and the theme must not replace it, or the store would
 * lose the blocks' payment, coupon and shipping behaviour. What the theme does
 * own is the surrounding page — and one honesty rule the reference breaks: if
 * JavaScript is off, a shopper must see a working alternative instead of a
 * blank panel, and an order confirmation must come from the server, never from
 * script in the browser.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Cart, checkout and account page behaviour.
 */
final class Commerce {

	/**
	 * Attach hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		if ( ! function_exists( 'wc_get_page_id' ) ) {
			return;
		}

		add_filter( 'the_content', array( self::class, 'block_checkout_fallback' ), 9 );
		add_filter( 'woocommerce_order_received_message', array( self::class, 'order_received_message' ), 10, 2 );
	}

	/**
	 * Print a no-JavaScript fallback under a block cart or checkout page.
	 *
	 * The fallback is WooCommerce's own classic form rendered through its
	 * shortcode — not a copy — so cart totals and payment methods are the real
	 * ones. It is only inserted when the page actually holds the block, and it
	 * lives inside `<noscript>`, so a JavaScript browser never sees two forms.
	 *
	 * @param string $content Page content.
	 * @return string
	 */
	public static function block_checkout_fallback( $content ): string {
		if ( ! is_string( $content ) || '' === $content || ! is_singular( 'page' ) ) {
			return (string) $content;
		}
		return self::fallback_for_page( (string) $content, (int) get_queried_object_id() );
	}

	/**
	 * Whether a page is one of the two block commerce pages, and which one.
	 *
	 * @param int $page_id Page id.
	 * @return string `cart`, `checkout`, or '' when the page is neither.
	 */
	public static function commerce_page( int $page_id ): string {
		if ( $page_id <= 0 ) {
			return '';
		}
		if ( has_block( 'woocommerce/cart', $page_id ) && (int) wc_get_page_id( 'cart' ) === $page_id ) {
			return 'cart';
		}
		if ( has_block( 'woocommerce/checkout', $page_id ) && (int) wc_get_page_id( 'checkout' ) === $page_id ) {
			return 'checkout';
		}
		return '';
	}

	/**
	 * Build the no-JavaScript fallback for one page, given its id.
	 *
	 * Split out so the rule can be asserted without a page request; the filter
	 * above only decides *which* page is being viewed.
	 *
	 * @param string $content Page content.
	 * @param int    $page_id Page id.
	 * @return string
	 */
	public static function fallback_for_page( string $content, int $page_id ): string {
		$which = self::commerce_page( $page_id );
		if ( '' === $which ) {
			return $content;
		}
		return $content . self::no_script_fallback( 'checkout' === $which );
	}

	/**
	 * The markup that gives a JavaScript-less shopper a real form.
	 *
	 * @param bool $is_checkout Whether the page is the checkout.
	 * @return string
	 */
	public static function no_script_fallback( bool $is_checkout ): string {
		$shortcode = $is_checkout ? '[woocommerce_checkout]' : '[woocommerce_cart]';

		$notice = $is_checkout
			? __( 'برای تکمیل سفارش، جاوااسکریپت مرورگر را فعال کنید یا با شماره فروش تماس بگیرید.', 'erfansanat' )
			: __( 'برای مدیریت سبد خرید، جاوااسکریپت مرورگر را فعال کنید یا با شماره فروش تماس بگیرید.', 'erfansanat' );

		$fallback  = '<noscript class="erfansanat-nojs"><div class="mx-auto max-w-3xl rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-[13px] font-bold leading-6 text-amber-900">' . esc_html( $notice ) . '</div></noscript>';
		$fallback .= '<noscript class="erfansanat-nojs">' . do_shortcode( $shortcode ) . '</noscript>';

		return $fallback;
	}

	/**
	 * Confirm an order in Persian, using the order's real number and status.
	 *
	 * WooCommerce prints its own received-order text; this only replaces the
	 * wording so it matches the store's language and never implies a payment
	 * that did not happen — a pending order says it is pending.
	 *
	 * @param string          $message Default message.
	 * @param \WC_Order|mixed $order   Order being confirmed.
	 * @return string
	 */
	public static function order_received_message( $message, $order ): string {
		if ( ! $order instanceof \WC_Order ) {
			return (string) $message;
		}

		$number = $order->get_order_number();

		if ( $order->is_paid() ) {
			return sprintf(
				/* translators: %s: order number. */
				esc_html__( 'سفارش شماره %s با موفقیت ثبت و پرداخت آن تایید شد.', 'erfansanat' ),
				esc_html( $number )
			);
		}

		if ( $order->has_status( 'on-hold' ) ) {
			return sprintf(
				/* translators: %s: order number. */
				esc_html__( 'سفارش شماره %s ثبت شد و تا تایید پرداخت در حالت «در انتظار بررسی» است.', 'erfansanat' ),
				esc_html( $number )
			);
		}

		return sprintf(
			/* translators: %s: order number. */
			esc_html__( 'سفارش شماره %s ثبت شد و در حال بررسی توسط واحد فروش است.', 'erfansanat' ),
			esc_html( $number )
		);
	}
}
