<?php
declare(strict_types=1);
/**
 * Task 4.1 acceptance: header/footer cut from the reference with live settings.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Settings;
use PHPUnit\Framework\TestCase;

/**
 * Verifies DOM contracts, hook order and the documented duplicate-id decision.
 */
final class TaskP04T01Test extends TestCase {

	/**
	 * Read a theme file.
	 *
	 * @param string $relative Path under the theme root.
	 * @return string
	 */
	private function theme_file( string $relative ): string {
		$path = get_template_directory() . '/' . ltrim( $relative, '/' );
		$this->assertFileExists( $path );
		return (string) file_get_contents( $path );
	}

	/**
	 * Read a theme file with PHP comments stripped.
	 *
	 * Documentation may quote the reference markup (for example the duplicate
	 * `id="home"` this template deliberately drops), so attribute assertions
	 * must look at executable code only.
	 *
	 * @param string $relative Path under the theme root.
	 * @return string
	 */
	private function theme_code( string $relative ): string {
		$tokens = token_get_all( $this->theme_file( $relative ) );
		$out    = '';
		foreach ( $tokens as $token ) {
			if ( is_array( $token ) && in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT ), true ) ) {
				continue;
			}
			$out .= is_array( $token ) ? $token[1] : $token;
		}
		return $out;
	}

	/**
	 * Both templates exist and call the three WordPress hooks in order.
	 */
	public function testHookContract(): void {
		$header = $this->theme_file( 'header.php' );
		$footer = $this->theme_file( 'footer.php' );

		$this->assertStringContainsString( '<?php wp_head(); ?>', $header );
		$this->assertStringContainsString( 'wp_body_open()', $header );
		$this->assertStringContainsString( 'wp_footer()', $footer );
		$this->assertStringContainsString( 'language_attributes()', $header );
		$this->assertStringContainsString( 'body_class()', $header );
		$this->assertStringContainsString( 'name="viewport"', $header );

		// wp_head must come before anything that outputs the body.
		$this->assertLessThan(
			strpos( $header, '<body' ),
			strpos( $header, 'wp_head()' ),
			'wp_head() must run inside <head>.'
		);
		$this->assertLessThan(
			strpos( $footer, 'wp_footer()' ),
			strpos( $footer, '</footer>' ),
			'footer markup must be printed before wp_footer().'
		);
	}

	/**
	 * The structural data-contracts of the reference survive the port.
	 */
	public function testReferenceDataContractsArePreserved(): void {
		$header = $this->theme_file( 'header.php' );
		$footer = $this->theme_file( 'footer.php' );

		foreach ( array( 'data-navbar-header', 'data-desktop-mega-trigger', 'data-desktop-mega-panel', 'data-mobile-toggle', 'data-search-modal', 'data-search-modal-open' ) as $hook ) {
			$found = false !== strpos( $header, $hook ) || false !== strpos( $this->theme_file( 'inc/Navigation/MegaMenu.php' ), $hook ) || false !== strpos( $this->theme_file( 'inc/Navigation/MobileMenu.php' ), $hook ) || false !== strpos( $this->theme_file( 'template-parts/navigation/header-search.php' ), $hook );
			$this->assertTrue( $found, $hook . ' is missing from the header.' );
		}
		$this->assertStringContainsString( 'data-scroll-top', $footer );

		// The reference max-width container is kept.
		$this->assertStringContainsString( 'max-w-[1300px]', $header );
		$this->assertStringContainsString( 'max-w-[1300px]', $footer );
	}

	/**
	 * Brand, contact and links come from settings, not hard-coded copy.
	 */
	public function testSettingsDriveTheHeaderAndFooter(): void {
		$header = $this->theme_file( 'header.php' );
		$footer = $this->theme_file( 'footer.php' );

		$this->assertStringContainsString( 'SiteParts::brand', $header );
		$this->assertStringContainsString( 'SiteParts::topbar', $header );
		$this->assertStringContainsString( 'Settings::get', $footer );
		$this->assertStringContainsString( 'SiteParts::contact_details', $footer );
		$this->assertStringContainsString( 'SiteParts::footer_categories', $footer );
		$this->assertStringContainsString( 'SiteParts::link_list', $footer );

		// The reference copy must not be frozen into the templates.
		$this->assertStringNotContainsString( '۰۳۱ - ۹۱۰۹۱۰۱۱', $footer );
		$this->assertStringNotContainsString( 'شهرک صنعتی برق و الکترونیک', $footer );
	}

	/**
	 * The duplicate `id="home"` of the reference is resolved, not copied.
	 */
	public function testDuplicateHomeIdIsResolved(): void {
		$header = $this->theme_code( 'header.php' );
		$footer = $this->theme_code( 'footer.php' );

		$this->assertStringNotContainsString( 'id="home"', $header, 'The page wrapper must not claim the hero anchor.' );
		$this->assertStringContainsString( 'id="erfansanat-page"', $header );
		$this->assertStringContainsString( '</div>', $footer, 'The wrapper opened in header.php must be closed.' );

		// The decision is documented where the next maintainer will look
		// (a comment, so the raw file is the right place to look).
		$this->assertStringContainsString( 'duplicate id', strtolower( $this->theme_file( 'header.php' ) ) );

		// Exactly one <main> element is opened across header + index.
		$this->assertSame( 1, substr_count( $header, '<main' ) );
		$this->assertStringContainsString( '</main>', $footer );
		$this->assertStringNotContainsString( '<main', $this->theme_code( 'index.php' ) );
	}

	/**
	 * Nothing in the header competes with core or SEOPress for canonical tags.
	 */
	public function testNoDuplicateSeoTags(): void {
		$header = $this->theme_file( 'header.php' );
		$this->assertStringNotContainsString( '<title', $header, 'title-tag support already prints the title.' );
		$this->assertStringNotContainsString( 'rel="canonical"', $header );
		$this->assertStringNotContainsString( 'hreflang=', $header );
		$this->assertStringNotContainsString( 'name="description"', $header );
	}

	/**
	 * The mobile drawer is present and keyboard reachable.
	 */
	public function testMobileDrawerIsAccessible(): void {
		$mobile = $this->theme_file( 'inc/Navigation/MobileMenu.php' );

		foreach ( array( 'data-mobile-menu', 'data-mobile-backdrop', 'data-mobile-sidebar', 'data-mobile-close' ) as $hook ) {
			$this->assertStringContainsString( $hook, $mobile, $hook . ' is missing.' );
		}
		$this->assertStringContainsString( 'role="dialog"', $mobile );
		$this->assertStringContainsString( 'aria-modal="true"', $mobile );
		$this->assertStringContainsString( 'aria-expanded="false"', $mobile );
		$this->assertStringContainsString( 'aria-controls=', $mobile, 'Accordion buttons must point at their panel.' );
		$this->assertStringContainsString( 'data-mobile-acc-btn', $mobile );
		$this->assertStringContainsString( 'data-mobile-acc-panel', $mobile );
	}

	/**
	 * The header stays usable when no menu is assigned at all.
	 */
	public function testHeaderSurvivesWithoutAMenu(): void {
		$locations = get_nav_menu_locations();
		set_theme_mod( 'nav_menu_locations', array() );
		wp_set_current_user( 0 );

		ob_start();
		$tree = \ErfanSanat\Theme\Navigation\MenuTree::from_location( 'main-mega-menu' );
		\ErfanSanat\Theme\Navigation\MegaMenu::render_block( 'corporate' );
		$html = (string) ob_get_clean();

		$this->assertSame( array(), $tree, 'No menu must produce an empty tree, not an error.' );
		$this->assertStringContainsString( 'data-desktop-mega-panel="corporate"', $html );
		$this->assertStringNotContainsString( 'Fatal error', $html );

		set_theme_mod( 'nav_menu_locations', $locations );
	}

	/**
	 * Templates are syntactically valid and free of hard-coded asset paths.
	 */
	public function testTemplatesArePortable(): void {
		foreach ( array( 'header.php', 'footer.php', 'template-parts/navigation/header-search.php' ) as $file ) {
			$code = $this->theme_file( $file );
			$this->assertStringNotContainsString( 'localhost', $code, $file . ' must not hard-code the dev host.' );
			$this->assertStringNotContainsString( '127.0.0.1', $code, $file . ' must not hard-code the dev host.' );
			$this->assertStringNotContainsString( 'http://erfansanat.com', $code, $file . ' must not hard-code the live host.' );
		}
	}

	/**
	 * A keyboard user must be able to skip the header.
	 *
	 * The header carries the top bar, the mega menu and the mobile drawer. Without
	 * a skip link every keyboard and screen-reader user tabs through all of it on
	 * every page load before reaching any content.
	 */
	public function test_header_provides_a_skip_link(): void {
		$header = (string) file_get_contents( get_template_directory() . '/header.php' );

		$this->assertStringContainsString( 'erfansanat-skip-link', $header );
		$this->assertStringContainsString( 'href="#primary"', $header );
		$this->assertStringContainsString( '<main id="primary"', $header );

		// The jump must actually move focus, not only scroll.
		$this->assertStringContainsString( 'tabindex="-1"', $header );
	}

	/**
	 * The fixed header must not cover the first content section.
	 */
	public function test_main_content_reserves_space_for_fixed_header(): void {
		$header = $this->theme_file( 'header.php' );
		$layout = $this->theme_file( 'assets/css/layout.css' );
		$assets = $this->theme_file( 'inc/AssetManager.php' );

		$this->assertStringContainsString( 'erfansanat-main-offset', $header );
		$this->assertStringContainsString( '.erfansanat-main-offset', $layout );
		$this->assertStringContainsString( 'padding-top: 104px', $layout );
		$this->assertStringContainsString( "'css/layout.css'", $assets );
		$this->assertStringContainsString( 'LAYOUT_STYLE_HANDLE', $assets );
		$this->assertStringContainsString( 'array( self::A11Y_STYLE_HANDLE )', $assets );
	}

	/**
	 * `.screen-reader-text` has to be defined somewhere the front end loads.
	 *
	 * The class is WordPress's, but the theme does not load core's stylesheet on
	 * the front end. The search label uses the class, so an undefined rule prints
	 * the word "جستجو" above the input in every browser.
	 */
	public function test_screen_reader_text_is_defined_and_loaded(): void {
		$css = (string) file_get_contents( get_template_directory() . '/assets/css/a11y.css' );

		$this->assertStringContainsString( '.screen-reader-text', $css );
		$this->assertStringContainsString( 'clip-path', $css, 'The modern hiding technique.' );
		// It must become visible when focused, or the text is unreachable.
		$this->assertStringContainsString( '.screen-reader-text:focus', $css );
		$this->assertStringContainsString( '.erfansanat-skip-link', $css );

		$asset = (string) file_get_contents( get_template_directory() . '/inc/AssetManager.php' );
		$this->assertStringContainsString( "'css/a11y.css'", $asset );
		$this->assertStringContainsString( 'A11Y_STYLE_HANDLE', $asset );
	}
}
