<?php
declare(strict_types=1);
/**
 * Task 1.1 acceptance: theme skeleton, secure autoloader and native feature support.
 *
 * Runs against the isolated local WordPress installation. Anything that needs a
 * real HTTP request is covered by check-phase-01-http.sh instead, because the
 * PHP-Wasm development server cannot answer a request from inside its own PHP
 * process.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

/**
 * Theme activation, supports, autoloader safety and missing-plugin tolerance.
 */
final class TaskP01T01Test extends TestCase {

	/**
	 * The theme must be installed under its own slug and active in the test site.
	 */
	public function testThemeIsActiveAndNamed(): void {
		$theme = wp_get_theme();
		$this->assertSame( 'erfansanat', $theme->get_template() );
		$this->assertSame( 'Erfan Sanat Esfahan', $theme->get( 'Name' ) );
		$this->assertSame( 'erfansanat', $theme->get( 'TextDomain' ) );
		$this->assertTrue( $theme->exists() );
		$this->assertSame( get_template_directory(), wp_normalize_path( ERFANSANAT_THEME_DIR ) );
	}

	/**
	 * Native supports required by the brief, including all three Woo galleries.
	 */
	public function testNativeFeatureSupport(): void {
		$this->assertTrue( current_theme_supports( 'title-tag' ) );
		$this->assertTrue( current_theme_supports( 'post-thumbnails' ) );
		$this->assertTrue( current_theme_supports( 'woocommerce' ) );
		$this->assertTrue( current_theme_supports( 'html5' ) );
		$this->assertTrue( current_theme_supports( 'automatic-feed-links' ) );
		$this->assertTrue( current_theme_supports( 'wc-product-gallery-zoom' ) );
		$this->assertTrue( current_theme_supports( 'wc-product-gallery-lightbox' ) );
		$this->assertTrue( current_theme_supports( 'wc-product-gallery-slider' ) );
	}

	/**
	 * HTML5 groups used by comment and gallery markup must be selected, otherwise
	 * WordPress falls back to XHTML output inside the static-derived markup.
	 */
	public function testHtml5GroupsCoverFormsAndGalleries(): void {
		$html5 = get_theme_support( 'html5' );
		$this->assertIsArray( $html5 );
		$this->assertIsArray( $html5[0] );
		foreach ( array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) as $group ) {
			$this->assertContains( $group, $html5[0] );
		}
	}

	/**
	 * Classes resolve through the theme namespace and the singleton is stable.
	 */
	public function testAutoloaderLoadsThemeClasses(): void {
		$this->assertTrue( class_exists( ErfanSanat\Theme\Autoloader::class ) );
		$this->assertTrue( class_exists( ErfanSanat\Theme\Theme::class ) );
		$this->assertSame( ErfanSanat\Theme\Theme::instance(), ErfanSanat\Theme\Theme::instance() );
	}

	/**
	 * A crafted or foreign class name must never include an outside file.
	 */
	public function testAutoloaderRejectsForeignTraversalAndEmptyNames(): void {
		$before = get_declared_classes();
		ErfanSanat\Theme\Autoloader::load( 'Vendor\\Outside' );
		ErfanSanat\Theme\Autoloader::load( 'ErfanSanat\\Theme\\..\\..\\outside' );
		ErfanSanat\Theme\Autoloader::load( 'ErfanSanat\\Theme\\' );
		ErfanSanat\Theme\Autoloader::load( 'ErfanSanat\\Theme\\Missing_Class_Name' );
		ErfanSanat\Theme\Autoloader::load( 'ErfanSanat\\Theme\\Theme' );
		$this->assertSame( $before, get_declared_classes() );
	}

	/**
	 * The theme must not depend on any page-builder runtime.
	 */
	public function testNoPageBuilderDependencyInThemeBootstrap(): void {
		$bootstrap = file_get_contents( get_template_directory() . '/functions.php' )
			. file_get_contents( get_template_directory() . '/inc/Theme.php' );
		$this->assertDoesNotMatchRegularExpression(
			'/(?:require|include)(?:_once)?\s*\(?[^;]*(?:elementor|jet-engine|jet-smart-filters|jet-search|jet-tabs)/i',
			$bootstrap
		);
		$this->assertDoesNotMatchRegularExpression(
			'/new\s+\\\\?(?:Elementor|Jet_Engine|Jet_Smart_Filters|Jet_Search|Jet_Tabs)/i',
			$bootstrap
		);
		$this->assertFileDoesNotExist( get_template_directory() . '/elementor.php' );
	}

	/**
	 * Deactivating WooCommerce must not fatal the theme: no WooCommerce API call
	 * is allowed while the theme files are loaded.
	 */
	public function testThemeLoadsWithoutWooCommerceClasses(): void {
		$files = array( '/functions.php', '/inc/Theme.php', '/inc/Autoloader.php' );
		foreach ( $files as $file ) {
			$code = token_get_all( (string) file_get_contents( get_template_directory() . $file ) );
			$code = array_filter(
				$code,
				static fn( $token ) => is_array( $token ) && ! in_array( $token[0], array( T_COMMENT, T_DOC_COMMENT, T_INLINE_HTML ), true )
			);
			$code = implode( '', array_map( static fn( $token ) => is_array( $token ) ? $token[1] : $token, $code ) );
			$this->assertStringNotContainsString( 'WC()', $code );
			$this->assertStringNotContainsString( 'new WC_', $code );
		}
	}

	/**
	 * Required files exist, carry the theme header and expose print-safe templates.
	 */
	public function testRequiredFilesAndFallbackTemplate(): void {
		foreach ( array( 'style.css', 'functions.php', 'index.php', 'header.php', 'footer.php', 'inc/Autoloader.php', 'inc/Theme.php' ) as $file ) {
			$this->assertFileExists( get_template_directory() . '/' . $file, $file . ' is missing.' );
		}
		$style = (string) file_get_contents( get_template_directory() . '/style.css' );
		$this->assertStringContainsString( 'Theme Name:', $style );
		$this->assertStringContainsString( 'Text Domain: erfansanat', $style );
		$this->assertStringContainsString( 'Requires PHP: 8.4', $style );

		$index = (string) file_get_contents( get_template_directory() . '/index.php' );
		$this->assertStringContainsString( 'get_header()', $index );
		$this->assertStringContainsString( 'get_footer()', $index );
		$this->assertStringContainsString( 'no-results', $index );
	}

	/**
	 * Mobile and RTL baseline: viewport meta and language attributes come from
	 * core helpers rather than hard-coded values.
	 */
	public function testHeaderProvidesViewportAndLanguageAttributes(): void {
		$header = (string) file_get_contents( get_template_directory() . '/header.php' );
		$this->assertStringContainsString( 'name="viewport"', $header );
		$this->assertStringContainsString( 'language_attributes()', $header );
		$this->assertStringContainsString( '<?php wp_head(); ?>', $header );
		$this->assertIsRtl();
	}

	/**
	 * The site under test really is Persian RTL, so later template work is judged
	 * against the right direction.
	 */
	private function assertIsRtl(): void {
		$this->assertTrue( is_rtl() );
		$this->assertSame( 'fa_IR', determine_locale() );
	}
}
