<?php
declare(strict_types=1);
/**
 * Task 2.2 acceptance: every global field reaches the front end, with safe fallbacks.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Frontend\SiteParts;
use ErfanSanat\Theme\Settings;
use PHPUnit\Framework\TestCase;

/**
 * Renders the shared site parts and asserts the exact reference defaults.
 */
final class TaskP02T02Test extends TestCase {

	/**
	 * Start from the shipped defaults.
	 */
	protected function setUp(): void {
		parent::setUp();
		delete_option( Settings::OPTION );
		Settings::flush();
	}

	/**
	 * Leave nothing behind.
	 */
	protected function tearDown(): void {
		delete_option( Settings::OPTION );
		Settings::flush();
		parent::tearDown();
	}

	/**
	 * Capture one SiteParts method.
	 *
	 * @param callable $callable Printer.
	 * @return string
	 */
	private function capture( callable $callable ): string {
		ob_start();
		$callable();
		return (string) ob_get_clean();
	}

	/**
	 * The shipped defaults must match the approved static reference word for word.
	 */
	public function testDefaultsMatchTheStaticReference(): void {
		$this->assertSame( 'عرفان صنعت اصفهان', Settings::get( 'brand_name' ) );
		$this->assertSame( 'نورپردازی شهری و المان‌های نوری', Settings::get( 'brand_tagline' ) );
		$this->assertSame( 'ESE', Settings::get( 'brand_mark' ) );
		$this->assertSame( '031-91091011', Settings::get( 'phone_primary' ) );
		$this->assertSame( '۰۳۱ - ۹۱۰۹۱۰۱۱ (۱۰ خط ویژه)', Settings::get( 'phone_primary_label' ) );
		$this->assertSame( '09137976915', Settings::get( 'phone_sales' ) );
		$this->assertSame( 'info@erfansanat.com', Settings::get( 'email' ) );
		$this->assertSame( 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر', Settings::get( 'hours' ) );
		$this->assertStringContainsString( 'شهرک صنعتی برق و الکترونیک', (string) Settings::get( 'address' ) );
		$this->assertStringContainsString( '۲۴ ماه گارانتی تعویض', (string) Settings::get( 'footer_about' ) );
		$this->assertSame( array( 'ISO 9001:2015', 'IP67 / IP68', 'کد بین‌المللی GS1' ), Settings::get( 'footer_badges' ) );
		$this->assertCount( 8, (array) Settings::get( 'footer_quick_links' ) );
		$this->assertSame( 'تمامی حقوق این وب‌سایت متعلق به شرکت دانش‌بنیان عرفان صنعت اصفهان است.', Settings::get( 'footer_copyright' ) );
	}

	/**
	 * The top strip prints the reference greeting and its three links.
	 */
	public function testTopbarRendersReferenceContent(): void {
		$html = $this->capture( array( SiteParts::class, 'topbar' ) );
		$this->assertStringContainsString( 'شرکت دانش‌بنیان عرفان صنعت اصفهان (ESE)', $html );
		$this->assertStringContainsString( 'ISO 9001:2015', $html );
		$this->assertStringContainsString( '/licenses-and-honors/', $html );
		$this->assertStringContainsString( '/refund_returns/', $html );
		$this->assertStringContainsString( '/recruitment/', $html );
		$this->assertSame( 3, substr_count( $html, '<a ' ) );
	}

	/**
	 * A saved top-strip value replaces the default immediately.
	 */
	public function testTopbarReflectsSavedValues(): void {
		update_option(
			Settings::OPTION,
			array(
				'topbar_title' => 'عنوان تازه',
				'topbar_subtitle' => '',
				'topbar_links' => array(
					array( 'title' => 'یک', 'url' => '/one/' ),
				),
			)
		);
		Settings::flush();
		$html = $this->capture( array( SiteParts::class, 'topbar' ) );
		$this->assertStringContainsString( 'عنوان تازه', $html );
		$this->assertStringNotContainsString( 'ISO 9001:2015', $html );
		$this->assertSame( 1, substr_count( $html, '<a ' ) );
	}

	/**
	 * The announcement bar is opt-in and needs text.
	 */
	public function testNoticeBarNeedsBothFlagAndText(): void {
		$this->assertSame( '', trim( $this->capture( array( SiteParts::class, 'notice_bar' ) ) ) );

		update_option( Settings::OPTION, array( 'notice_enabled' => true ) );
		Settings::flush();
		$this->assertSame( '', trim( $this->capture( array( SiteParts::class, 'notice_bar' ) ) ), 'An empty notice must not render.' );

		update_option(
			Settings::OPTION,
			array(
				'notice_enabled' => true,
				'notice_text' => 'ارسال رایگان تا پایان ماه',
				'notice_link' => '/shop/',
			)
		);
		Settings::flush();
		$html = $this->capture( array( SiteParts::class, 'notice_bar' ) );
		$this->assertStringContainsString( 'ارسال رایگان تا پایان ماه', $html );
		$this->assertStringContainsString( '/shop/', $html );
		$this->assertStringContainsString( 'erfansanat-notice', $html );
	}

	/**
	 * The brand block uses the text mark, then an uploaded logo when set.
	 */
	public function testBrandFallsBackToTextThenUsesLogo(): void {
		$home = home_url( '/' );
		$html = $this->capture( static fn() => SiteParts::brand( $home, 'is-header' ) );
		$this->assertStringContainsString( 'ESE', $html );
		$this->assertStringContainsString( 'عرفان صنعت اصفهان', $html );
		$this->assertStringContainsString( 'is-header', $html );
		$this->assertStringContainsString( 'href="' . esc_url( home_url( '/' ) ) . '"', $html );
		$this->assertStringNotContainsString( '<img', $html );

		require_once ABSPATH . 'wp-admin/includes/image.php';
		$file = get_template_directory() . '/assets/images/ese-logo.svg';
		$attachment = wp_insert_attachment(
			array(
				'post_title' => 'es-test-logo',
				'post_mime_type' => 'image/svg+xml',
				'post_status' => 'inherit',
			),
			$file
		);
		$this->assertIsInt( $attachment );
		wp_update_attachment_metadata( $attachment, wp_generate_attachment_metadata( $attachment, $file ) );
		update_option( Settings::OPTION, array( 'logo_id' => $attachment ) );
		Settings::flush();
		$html = $this->capture( static fn() => SiteParts::brand() );
		$this->assertStringContainsString( '<img', $html, 'An uploaded logo replaces the text mark.' );
		$this->assertStringNotContainsString( 'erfansanat-brand__mark', $html );

		wp_delete_attachment( $attachment, true );
		update_option( Settings::OPTION, array( 'logo_id' => $attachment ) );
		Settings::flush();
		$html = $this->capture( static fn() => SiteParts::brand() );
		$this->assertStringContainsString( 'erfansanat-brand__mark', $html, 'A deleted attachment falls back to the text mark.' );
	}

	/**
	 * Contact details print safe phone and mailto links and skip empty fields.
	 */
	public function testContactDetailsAreEscapedAndLinkable(): void {
		$html = $this->capture( array( SiteParts::class, 'contact_details' ) );
		$this->assertStringContainsString( 'tel:03191091011', $html );
		$this->assertStringContainsString( 'tel:09137976915', $html );
		$this->assertStringContainsString( 'mailto:info@erfansanat.com', $html );
		$this->assertStringContainsString( 'شهرک صنعتی برق و الکترونیک', $html );

		update_option(
			Settings::OPTION,
			array(
				'phone_primary' => '',
				'phone_sales' => '',
				'email' => 'not-an-email',
				'address' => '',
				'hours' => '',
			)
		);
		Settings::flush();
		$empty = $this->capture( array( SiteParts::class, 'contact_details' ) );
		$this->assertStringNotContainsString( 'tel:', $empty );
		$this->assertStringNotContainsString( 'not-an-email', $empty, 'An invalid address is never printed as a mailto link.' );
	}

	/**
	 * Social links are printed only when configured, always with rel/target.
	 */
	public function testSocialLinksAreOptInAndHardened(): void {
		$this->assertSame( '', trim( $this->capture( array( SiteParts::class, 'social_links' ) ) ) );

		update_option(
			Settings::OPTION,
			array(
				'social' => array(
					array( 'title' => 'اینستاگرام', 'url' => 'https://instagram.com/erfansanat' ),
				),
			)
		);
		Settings::flush();
		$html = $this->capture( array( SiteParts::class, 'social_links' ) );
		$this->assertStringContainsString( 'instagram.com/erfansanat', $html );
		$this->assertStringContainsString( 'rel="noopener noreferrer nofollow"', $html );
		$this->assertStringContainsString( 'target="_blank"', $html );
	}

	/**
	 * Footer categories come from the eight reference slugs.
	 */
	public function testFooterCategoriesResolveReferenceSlugs(): void {
		$html = $this->capture( array( SiteParts::class, 'footer_categories' ) );
		if ( ! taxonomy_exists( 'product_cat' ) ) {
			$this->markTestSkipped( 'WooCommerce is not active in this environment.' );
		}
		$found = 0;
		foreach ( SiteParts::reference_category_slugs() as $slug ) {
			if ( get_term_by( 'slug', $slug, 'product_cat' ) instanceof WP_Term ) {
				++$found;
				$this->assertStringContainsString( '/product_cat/' . $slug . '/', $html );
			}
		}
		$this->assertGreaterThan( 0, $found, 'The local fixtures must contain at least one reference category.' );
	}

	/**
	 * Quick links and badges print in order and disappear when emptied.
	 */
	public function testQuickLinksAndBadgesFollowStoredOrder(): void {
		update_option(
			Settings::OPTION,
			array(
				'footer_quick_links' => array(
					array( 'title' => 'دوم', 'url' => '/second/' ),
					array( 'title' => 'اول', 'url' => '/first/' ),
				),
				'footer_badges' => array(),
			)
		);
		Settings::flush();

		$links = $this->capture( static fn() => SiteParts::link_list( 'footer_quick_links' ) );
		$first = mb_strpos( $links, '/second/' );
		$second = mb_strpos( $links, '/first/' );
		$this->assertNotFalse( $first );
		$this->assertNotFalse( $second );
		$this->assertLessThan( $second, $first, 'Stored order must be preserved.' );
		$this->assertSame( '', trim( $this->capture( array( SiteParts::class, 'footer_badges' ) ) ) );
	}

	/**
	 * Unknown keys and malformed rows never reach the printer.
	 */
	public function testUnknownAndMalformedDataIsDropped(): void {
		$result = Settings::sanitize(
			array(
				'social' => array(
					array( 'title' => 'بدون نشانی' ),
					'not-a-row',
					array( 'title' => 'درست', 'url' => 'javascript:alert(1)' ),
					array( 'title' => 'سالم', 'url' => 'https://example.test/' ),
				),
				'brand_name' => array( 'nested' ),
				'evil_key' => 'x',
			)
		);

		$this->assertArrayNotHasKey( 'evil_key', $result['values'] );
		$this->assertSame( 'ESE', $result['values']['brand_mark'] );
		$social = $result['values']['social'];
		$this->assertCount( 1, $social, 'Only the well-formed https row survives.' );
		$this->assertSame( 'https://example.test/', $social[0]['url'] );
	}

	/**
	 * The four menu locations required by the brief are registered.
	 */
	public function testMenuLocationsAreRegistered(): void {
		$locations = get_registered_nav_menus();
		foreach ( array( 'main-mega-menu', 'mobile-menu', 'tolidat-sherkat', 'landing' ) as $slug ) {
			$this->assertArrayHasKey( $slug, $locations, 'Missing menu location: ' . $slug );
		}
	}

	/**
	 * The header stays usable before a menu is assigned.
	 */
	public function testPrimaryNavigationFallsBackToReferenceLinks(): void {
		set_theme_mod( 'nav_menu_locations', array() );
		$html = $this->capture( array( SiteParts::class, 'primary_navigation' ) );
		$this->assertStringContainsString( 'erfansanat-menu', $html );
		$this->assertStringContainsString( '/shop/', $html );
		$this->assertStringContainsString( '/services/', $html );
		$this->assertSame( 7, substr_count( $html, '<li>' ) );
	}

	/**
	 * Script fields only survive for a capability holder.
	 */
	public function testCustomScriptsRequireUnfilteredHtml(): void {
		$administrator = get_user_by( 'id', 1 );
		$this->assertNotFalse( $administrator );

		wp_set_current_user( 1 );
		$allowed = Settings::sanitize(
			array(
				'header_scripts' => '<script>window.esFlag=1;</script>',
				'footer_scripts' => '<script>window.esFlag=2;</script>',
			)
		);
		update_option( Settings::OPTION, $allowed['values'] );
		Settings::flush();
		$scripts = SiteParts::custom_scripts( 'header' );
		$this->assertStringContainsString( 'window.esFlag', $scripts );

		$contributor = wp_insert_user(
			array(
				'user_login' => 'es_scripts_' . wp_rand( 1000, 9999 ),
				'user_pass' => wp_generate_password( 20 ),
				'user_email' => 'scripts_' . wp_rand( 1000, 9999 ) . '@example.test',
				'role' => 'contributor',
			)
		);
		$this->assertIsInt( $contributor );
		wp_set_current_user( $contributor );
		$denied = Settings::sanitize( array( 'header_scripts' => '<script>window.evil=1;</script>' ) );
		$this->assertSame( '', $denied['values']['header_scripts'] );
		wp_set_current_user( 0 );
	}
}
