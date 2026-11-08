<?php
declare(strict_types=1);
/**
 * Task 6.3 acceptance: cart, checkout and my-account.
 *
 * The rule that shapes this task: the theme keeps WooCommerce's own block and
 * shortcode commerce — it never re-implements a cart, never prints a receipt
 * from browser state, and never claims a payment happened. What it adds is the
 * page shell, a no-JavaScript path that uses WooCommerce's classic forms, and
 * Persian confirmation wording taken from the order itself.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Commerce;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the commerce pages and the honesty rules around them.
 */
final class TaskP06T03Test extends TestCase {

	/**
	 * Order ids created by a test, removed afterwards even on failure.
	 *
	 * @var array<int, int>
	 */
	private array $created_orders = array();

	/**
	 * Remove anything a test created.
	 */
	protected function tearDown(): void {
		foreach ( $this->created_orders as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( $order instanceof \WC_Order ) {
				$order->delete( true );
			}
		}
		$this->created_orders = array();
		parent::tearDown();
	}

	/**
	 * The three commerce pages point at real, published pages.
	 */
	public function testCommercePagesAreWired(): void {
		foreach ( array( 'cart', 'checkout', 'myaccount' ) as $page ) {
			$page_id = (int) wc_get_page_id( $page );
			$this->assertGreaterThan( 0, $page_id, $page . ' page must be configured.' );
			$this->assertSame( 'publish', get_post_status( $page_id ), $page . ' page must be published.' );
		}
	}

	/**
	 * The cart page holds WooCommerce's block, not a hand-built cart.
	 */
	public function testCartPageUsesTheWooCommerceBlock(): void {
		$cart_id = (int) wc_get_page_id( 'cart' );
		$this->assertTrue( has_block( 'woocommerce/cart', $cart_id ), 'The cart page must keep the WooCommerce cart block.' );

		// The block needs its inner tree, or it renders an empty page.
		$content = (string) get_post( $cart_id )->post_content;
		foreach ( array( 'wp:woocommerce/filled-cart-block', 'wp:woocommerce/cart-items-block', 'wp:woocommerce/empty-cart-block' ) as $inner ) {
			$this->assertStringContainsString( $inner, $content, 'The cart block template is missing ' . $inner . '.' );
		}
	}

	/**
	 * Cart maths come from WooCommerce, not from the template.
	 */
	public function testCartTotalsComeFromWooCommerce(): void {
		$product = wc_get_product( (int) get_page_by_path( 'illumina', OBJECT, 'product' )->ID );
		$this->assertInstanceOf( \WC_Product::class, $product );

		$cart = new \WC_Cart();
		$cart->empty_cart();
		$cart->add_to_cart( $product->get_id(), 2 );

		$this->assertSame( 2, $cart->get_cart_contents_count() );

		$expected = (float) $product->get_price() * 2;
		$this->assertEqualsWithDelta( $expected, (float) $cart->get_subtotal(), 0.01, 'The subtotal must be price times quantity.' );
		$this->assertEqualsWithDelta( $expected, (float) $cart->get_total( 'edit' ), 1.0, 'The total must include the same line.' );

		$cart->empty_cart();
		$this->assertSame( 0, $cart->get_cart_contents_count() );
	}

	/**
	 * The cart page offers a real, working path without JavaScript.
	 */
	public function testCartAndCheckoutHaveANoScriptPath(): void {
		foreach ( array( 'cart', 'checkout' ) as $page ) {
			$page_id = (int) wc_get_page_id( $page );
			$this->assertTrue( has_block( 'woocommerce/' . $page, $page_id ), $page . ' must still use the WooCommerce block.' );

			$html = Commerce::fallback_for_page( (string) get_post( $page_id )->post_content, $page_id );
			$this->assertStringContainsString( '<noscript', $html, $page . ' must offer a no-JavaScript path.' );
			$this->assertStringContainsString( 'erfansanat-nojs', $html );

			// The shortcode is rendered, so the real classic form must be present.
			$expected_markup = 'checkout' === $page ? 'woocommerce-checkout' : 'woocommerce-cart';
			$this->assertStringContainsString( $expected_markup, $html, 'The fallback must render WooCommerce\'s own classic form.' );
			$this->assertStringContainsString( 'شماره فروش', $html, 'The shopper must be told what to do next.' );
		}
	}

	/**
	 * The fallback never touches a page that is not the cart or checkout.
	 */
	public function testFallbackLeavesOtherPagesAlone(): void {
		$about_id = (int) get_page_by_path( 'about', OBJECT, 'page' )->ID;
		$content  = '<p>محتوای برگه معرفی</p>';
		$this->assertSame( $content, Commerce::fallback_for_page( $content, $about_id ), 'An ordinary page must not gain a cart.' );

		$this->assertSame( 'cart', Commerce::commerce_page( (int) wc_get_page_id( 'cart' ) ) );
		$this->assertSame( 'checkout', Commerce::commerce_page( (int) wc_get_page_id( 'checkout' ) ) );
		$this->assertSame( '', Commerce::commerce_page( 0 ) );
		$this->assertSame( '', Commerce::fallback_for_page( '', 0 ) );
		$this->assertSame( '', Commerce::block_checkout_fallback( '' ) );
	}

	/**
	 * A pending order is described as pending, never as paid.
	 */
	public function testOrderConfirmationNeverClaimsAFalsePayment(): void {
		foreach ( array( 'pending', 'on-hold', 'processing', 'completed' ) as $status ) {
			$order = wc_create_order( array( 'status' => $status ) );
			$this->assertInstanceOf( \WC_Order::class, $order );
			$this->created_orders[] = (int) $order->get_id();

			$message = Commerce::order_received_message( 'default', $order );

			$this->assertStringContainsString( (string) $order->get_order_number(), $message, 'The confirmation names the real order.' );
			$this->assertStringNotContainsString( 'default', $message );

			if ( $order->is_paid() ) {
				$this->assertStringContainsString( 'پرداخت آن تایید شد', $message );
			} else {
				$this->assertStringNotContainsString( 'پرداخت آن تایید شد', $message, $status . ' must not claim a payment.' );
			}
		}
	}

	/**
	 * A non-order value is passed through untouched.
	 */
	public function testOrderMessageIsSafeForUnexpectedInput(): void {
		$this->assertSame( 'keep', Commerce::order_received_message( 'keep', null ) );
		$this->assertSame( 'keep', Commerce::order_received_message( 'keep', 'not-an-order' ) );
	}

	/**
	 * The account page uses WooCommerce's shortcode and its real forms.
	 */
	public function testAccountPageUsesWooCommerceForms(): void {
		$account_id = (int) wc_get_page_id( 'myaccount' );
		$content    = (string) get_post( $account_id )->post_content;
		$this->assertStringContainsString( 'woocommerce_my_account', $content, 'The account page must keep the WooCommerce shortcode.' );

		// wpautop wraps a standalone shortcode in <p>; the rendered form is what counts.
		$rendered = do_shortcode( $content );
		$this->assertStringContainsString( 'woocommerce-form-login', $rendered, 'Logged out, WooCommerce must print its own login form.' );
		$this->assertStringContainsString( 'name="login"', $rendered );
		$this->assertStringContainsString( 'نام کاربری', $rendered, 'The login form must be in the store language.' );
	}

	/**
	 * No commerce template fakes a cart, a receipt or a payment.
	 */
	public function testNoCommerceTemplateFakesAnything(): void {
		$files = array( get_template_directory() . '/page.php' );
		$commerce = get_template_directory() . '/inc/Commerce.php';
		if ( file_exists( $commerce ) ) {
			$files[] = $commerce;
		}

		$forbidden = array(
			'localStorage'    => 'a cart is not browser state',
			'data-checkout-receipt' => 'a receipt must come from the server',
			'ESE-1403'        => 'a hard-coded tracking code is a fake receipt',
			'data-receipt-code' => 'a receipt code must come from the order',
		);

		foreach ( $files as $file ) {
			$code = (string) file_get_contents( $file );
			foreach ( $forbidden as $needle => $why ) {
				$this->assertStringNotContainsString( $needle, $code, basename( $file ) . ': ' . $why );
			}
		}
	}

	/**
	 * The page shell renders real titles and never a placeholder.
	 */
	public function testPageShellRendersTheRealTitle(): void {
		$file = get_template_directory() . '/page.php';
		$this->assertFileExists( $file, 'Commerce pages need a page template.' );

		$code = (string) file_get_contents( $file );
		$this->assertStringContainsString( 'the_content()', $code, 'The page body must be printed by WordPress so blocks and shortcodes work.' );
		$this->assertStringContainsString( 'get_the_title()', $code );
		$this->assertStringNotContainsString( 'lorem', strtolower( $code ) );
	}

	/**
	 * A payment gateway exists for the local test flow, and it is clearly test-only.
	 */
	public function testALocalTestGatewayIsAvailable(): void {
		WC()->payment_gateways()->init();
		$available = array_keys( WC()->payment_gateways()->get_available_payment_gateways() );

		$this->assertContains( 'cod', $available, 'The fixture must offer one gateway, or checkout cannot be tested.' );

		$title = (string) ( WC()->payment_gateways()->payment_gateways()['cod']->get_title() );
		$this->assertStringContainsString( 'آزمایشی', $title, 'The local gateway must say it is a test method.' );
	}

	/**
	 * Cart and checkout keep the theme's page shell rather than WooCommerce's.
	 */
	public function testCommercePagesUseTheThemeShell(): void {
		foreach ( array( 'cart', 'checkout' ) as $page ) {
			$page_id   = (int) wc_get_page_id( $page );
			$templates = array( get_page_template_slug( $page_id ) );
			$this->assertContains( end( $templates ), array( '', false ), $page . ' must use the default page template.' );
		}
	}
}
