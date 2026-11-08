<?php
declare(strict_types=1);
/**
 * Task 2.1 acceptance: settings storage, capability and nonce enforcement.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Admin\ThemeOptions;
use ErfanSanat\Theme\Settings;
use PHPUnit\Framework\TestCase;

/**
 * The panel, the AJAX endpoint and the read-through cache.
 */
final class TaskP02T01Test extends TestCase {

	/**
	 * Restore a clean option state around every test.
	 */
	protected function setUp(): void {
		parent::setUp();
		delete_option( Settings::OPTION );
		Settings::flush();
	}

	/**
	 * Leave no test option behind.
	 */
	protected function tearDown(): void {
		delete_option( Settings::OPTION );
		Settings::flush();
		wp_set_current_user( 0 );
		parent::tearDown();
	}

	/**
	 * Register the panel on the admin_menu hook once.
	 *
	 * @return void
	 */
	private function boot_admin(): void {
		require_once ABSPATH . 'wp-admin/includes/admin.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		foreach ( array( 'menu', 'submenu', 'admin_page_hooks', 'menu_order', '_registered_pages', '_parent_pages' ) as $global ) {
			if ( ! isset( $GLOBALS[ $global ] ) ) {
				$GLOBALS[ $global ] = array();
			}
		}
	}

	/**
	 * The screen and the AJAX action must be registered for administrators.
	 */
	public function testPanelAndAjaxActionAreRegistered(): void {
		wp_set_current_user( 1 );
		$this->boot_admin();
		ThemeOptions::register();
		ThemeOptions::register_ajax();
		do_action( 'admin_menu' );

		$this->assertNotFalse( has_action( 'wp_ajax_' . ThemeOptions::AJAX_ACTION ) );
		$this->assertNotFalse( has_action( 'admin_menu' ) );
		$this->assertSame( 'erfansanat-options', ThemeOptions::SLUG );
		$this->assertSame( 'manage_options', ThemeOptions::CAPABILITY );
	}

	/**
	 * A contributor and a logged-out visitor cannot read the defaults.
	 */
	public function testUsersWithoutCapabilityCannotSave(): void {
		$contributor = wp_insert_user(
			array(
				'user_login' => 'es_contributor_' . wp_rand( 1000, 9999 ),
				'user_pass' => wp_generate_password( 20 ),
				'user_email' => 'contributor_' . wp_rand( 1000, 9999 ) . '@example.test',
				'role' => 'contributor',
			)
		);
		$this->assertIsInt( $contributor );
		wp_set_current_user( $contributor );
		$this->assertFalse( current_user_can( ThemeOptions::CAPABILITY ) );

		$saved = $this->call_save(
			array( 'brand_name' => 'hacked' ),
			wp_create_nonce( ThemeOptions::NONCE_ACTION )
		);
		$this->assertFalse( $saved['success'] );
		$this->assertSame( 403, $saved['status'] );
		$this->assertFalse( get_option( Settings::OPTION ) );

		wp_set_current_user( 0 );
		$saved = $this->call_save( array( 'brand_name' => 'hacked' ), wp_create_nonce( ThemeOptions::NONCE_ACTION ) );
		$this->assertFalse( $saved['success'] );
		$this->assertSame( 403, $saved['status'] );
	}

	/**
	 * An administrator with a bad or missing nonce must be rejected.
	 */
	public function testInvalidOrMissingNonceIsRejected(): void {
		wp_set_current_user( 1 );
		foreach ( array( 'not-a-nonce', '' ) as $nonce ) {
			$saved = $this->call_save( array( 'brand_name' => 'hacked' ), $nonce );
			$this->assertFalse( $saved['success'] );
			$this->assertSame( 403, $saved['status'] );
			$this->assertFalse( get_option( Settings::OPTION ) );
		}
	}

	/**
	 * A valid administrator request writes exactly one option row.
	 */
	public function testValidSaveWritesSingleOption(): void {
		wp_set_current_user( 1 );
		$before = $this->option_rows();
		$saved = $this->call_save(
			array(
				'brand_name' => 'عرفان صنعت — آزمایشی',
				'phone_primary' => '031-12345678',
				'products_per_page' => '24',
				'notice_enabled' => '1',
			),
			wp_create_nonce( ThemeOptions::NONCE_ACTION )
		);

		$this->assertTrue( $saved['success'], (string) ( $saved['message'] ?? '' ) );
		$after = $this->option_rows();
		$this->assertSame( $before + 1, $after, 'Saving must create exactly one option row.' );

		$stored = get_option( Settings::OPTION );
		$this->assertIsArray( $stored );
		$this->assertSame( 'عرفان صنعت — آزمایشی', $stored['brand_name'] );
		$this->assertSame( 24, $stored['products_per_page'] );
		$this->assertTrue( $stored['notice_enabled'] );
		$this->assertArrayNotHasKey( 'unknown_key', $stored );
	}

	/**
	 * A partial save keeps unrelated stored values.
	 */
	public function testPartialSaveKeepsOtherValues(): void {
		wp_set_current_user( 1 );
		$nonce = wp_create_nonce( ThemeOptions::NONCE_ACTION );
		$this->call_save( array( 'brand_name' => 'نام اول', 'brand_tagline' => 'شعار اول' ), $nonce );
		$this->call_save( array( 'brand_tagline' => 'شعار دوم' ), $nonce );

		$stored = get_option( Settings::OPTION );
		$this->assertSame( 'نام اول', $stored['brand_name'] );
		$this->assertSame( 'شعار دوم', $stored['brand_tagline'] );
	}

	/**
	 * es_option() prefers the stored value and falls back to the schema default.
	 */
	public function testEsOptionReturnsStoredValueAndDefault(): void {
		$this->assertSame( 'ESE', \ErfanSanat\Theme\es_option( 'brand_mark' ) );
		$this->assertTrue( function_exists( 'ErfanSanat\\Theme\\es_option' ) );

		update_option(
			Settings::OPTION,
			array(
				'brand_mark' => 'XYZ',
				'unknown' => 'ignored',
			)
		);
		Settings::flush();

		$this->assertSame( 'XYZ', \ErfanSanat\Theme\es_option( 'brand_mark' ) );
		$this->assertSame( 'عرفان صنعت اصفهان', \ErfanSanat\Theme\es_option( 'brand_name' ), 'A missing key keeps its documented default.' );
		$this->assertSame( 'fallback', \ErfanSanat\Theme\es_option( 'nope', 'fallback' ) );
	}

	/**
	 * A corrupted option row must not break the site.
	 */
	public function testCorruptedOptionFallsBackToDefaults(): void {
		update_option( Settings::OPTION, 'not-an-array' );
		Settings::flush();
		$all = Settings::all();
		$this->assertIsArray( $all );
		$this->assertSame( 'ESE', $all['brand_mark'] );

		update_option( Settings::OPTION, array( 'brand_mark' => array( 'nested' ) ) );
		Settings::flush();
		$this->assertSame( 'ESE', \ErfanSanat\Theme\es_option( 'brand_mark' ) );
	}

	/**
	 * Empty payloads are rejected without touching the stored option.
	 */
	public function testEmptyPayloadIsRejected(): void {
		wp_set_current_user( 1 );
		$_POST = array(
			'action' => ThemeOptions::AJAX_ACTION,
			'nonce' => wp_create_nonce( ThemeOptions::NONCE_ACTION ),
		);
		$response = $this->capture_ajax();
		unset( $_POST );
		$this->assertFalse( $response['success'] );
		$this->assertSame( 400, $response['status'] );
		$this->assertFalse( get_option( Settings::OPTION ) );
	}

	/**
	 * The panel template exposes its tabs and never prints an unescaped value.
	 */
	public function testPanelRendersEscapedFields(): void {
		wp_set_current_user( 1 );
		update_option(
			Settings::OPTION,
			array(
				'brand_name' => '<script>alert(1)</script>',
			)
		);
		Settings::flush();

		ob_start();
		ThemeOptions::render();
		$html = (string) ob_get_clean();

		$this->assertStringContainsString( 'erfansanat-admin__overview', $html );
		$this->assertStringContainsString( 'محصولات منتشرشده', $html );
		$this->assertStringContainsString( 'پروژه‌های منتشرشده', $html );
		$this->assertStringContainsString( 'خدمات منتشرشده', $html );
		$this->assertStringContainsString( 'پیام‌های خوانده‌نشده', $html );
		$this->assertStringContainsString( 'post-new.php?post_type=product', $html );
		$this->assertStringContainsString( 'مشاهدهٔ وب‌سایت', $html );
		$this->assertStringContainsString( 'چک‌لیست راه‌اندازی', $html );
		$this->assertStringContainsString( 'فهرست اصلی به جایگاه هدر متصل است', $html );
		$this->assertStringContainsString( 'data-check-status="ready"', $html );
		$this->assertStringContainsString( 'data-check-status="attention"', $html );
		$this->assertStringContainsString( 'data-tab="brand"', $html );
		$this->assertStringContainsString( 'data-tab="advanced"', $html );
		$this->assertStringNotContainsString( '<script>alert(1)</script>', $html );
		$this->assertStringContainsString( 'erfansanat-options-form', $html );
		$this->assertStringContainsString( 'aria-selected="true"', $html );
	}

	/**
	 * Process an AJAX call and return the decoded envelope.
	 *
	 * @param array<string, mixed> $options Options payload.
	 * @param string               $nonce   Nonce to send.
	 * @return array<string, mixed>
	 */
	private function call_save( array $options, string $nonce ): array {
		$_POST = array(
			'action' => ThemeOptions::AJAX_ACTION,
			'nonce' => $nonce,
			'options' => $options,
		);
		$response = $this->capture_ajax();
		unset( $_POST );
		return $response;
	}

	/**
	 * Run the AJAX handler and capture its premature JSON exit.
	 *
	 * @return array<string, mixed>
	 */
	private function capture_ajax(): array {
		$response = array(
			'success' => null,
			'status' => 0,
			'message' => '',
			'data' => array(),
		);

		ThemeOptions::set_sender(
			static function ( array $payload, int $status, bool $success ) use ( &$response ): void {
				$response['success'] = $success;
				$response['status'] = $status;
				$response['data'] = $payload;
				$response['message'] = (string) ( $payload['message'] ?? '' );
			}
		);

		ThemeOptions::handle_save();
		ThemeOptions::set_sender( null );

		if ( null === $response['success'] ) {
			throw new RuntimeException( 'The AJAX handler did not answer.' );
		}
		return $response;
	}

	/**
	 * Count rows currently stored in the options table.
	 *
	 * @return int
	 */
	private function option_rows(): int {
		global $wpdb;
		return (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name = %s", Settings::OPTION )
		);
	}
}
