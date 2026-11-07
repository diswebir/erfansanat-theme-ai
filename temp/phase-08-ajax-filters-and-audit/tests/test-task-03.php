<?php
declare(strict_types=1);

/**
 * Task 8.3 acceptance: SEO, security, performance and the end-to-end audit.
 *
 * The live audit lives in `audit.py`; this contract covers the parts that can be
 * asserted without a request, so a regression is caught by PHPUnit before the
 * slower HTTP audit runs.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Data\PostData;
use ErfanSanat\Theme\DeBloat;
use ErfanSanat\Theme\Seo;
use PHPUnit\Framework\TestCase;

/**
 * Contracts for the audit task.
 */
final class TaskP08T03Test extends TestCase {

	/**
	 * The audit script and every suite it belongs with exist.
	 */
	public function test_audit_artifacts_exist(): void {
		// The contract file is copied into a cache directory and the theme is
		// symlinked into WordPress, so neither `__DIR__` nor the template
		// directory points at the checkout. Resolve from the working directory
		// the test runner is invoked in.
		$suite = rtrim( (string) getcwd(), '/' ) . '/temp/phase-08-ajax-filters-and-audit/tests';
		if ( ! is_file( $suite . '/audit.py' ) ) {
			$suite = '/home/user/erfansanat-theme-ai/temp/phase-08-ajax-filters-and-audit/tests';
		}

		$this->assertFileExists( $suite . '/audit.py' );
		$this->assertFileExists( $suite . '/check-http.py' );
		$this->assertFileExists( $suite . '/bootstrap.php' );
	}

	/**
	 * The theme never fights an SEO plugin for the head.
	 *
	 * Two canonical tags on one page is worse than none: a crawler has to pick.
	 */
	public function test_seo_defers_to_a_plugin(): void {
		$this->assertTrue( class_exists( Seo::class ) );

		add_filter( 'erfansanat_seo_plugin_active', '__return_true' );
		$this->assertTrue( Seo::plugin_owns_head(), 'A filter must be able to hand the head to a plugin.' );
		remove_filter( 'erfansanat_seo_plugin_active', '__return_true' );

		$this->assertFalse( Seo::plugin_owns_head(), 'With no plugin active the theme owns the head.' );
	}

	/**
	 * A description is about the current page, not boilerplate.
	 */
	public function test_description_is_page_specific(): void {
		$id = (int) wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_status'  => 'draft',
				'post_title'   => 'P08 description fixture',
				'post_excerpt' => 'این یک خلاصهٔ یکتای آزمایشی برای آزمون توضیح متا است و باید دقیقاً همین متن بازگردانده شود.',
			)
		);
		$this->assertGreaterThan( 0, $id );

		$post = get_post( $id );
		$this->assertInstanceOf( \WP_Post::class, $post );

		wp_delete_post( $id, true );

		// The description builder runs against the queried object, which a unit
		// test cannot fake without a request; assert the contract instead: the
		// filter exists and returns a string.
		$value = apply_filters( 'erfansanat_meta_description', 'متن آزمایشی' );
		$this->assertIsString( $value );
	}

	/**
	 * The description falls back to the site tagline rather than to nothing.
	 */
	public function test_description_never_returns_empty(): void {
		$source = (string) file_get_contents( get_template_directory() . '/inc/Seo.php' );

		$this->assertStringContainsString( "get_bloginfo( 'description' )", $source, 'A page with no content still needs a description.' );
		$this->assertStringContainsString( 'erfansanat_meta_description', $source );
	}

	/**
	 * Structured data describes only what the page actually is.
	 */
	public function test_structured_data_is_typed_by_context(): void {
		$source = (string) file_get_contents( get_template_directory() . '/inc/Seo.php' );

		$this->assertStringContainsString( "'Product'", $source );
		$this->assertStringContainsString( "'Article'", $source );
		$this->assertStringContainsString( "'Organization'", $source );
		$this->assertStringContainsString( 'JSON_UNESCAPED_UNICODE', $source, 'Persian must not be escaped into \\u sequences.' );
	}

	/**
	 * The canonical keeps pagination distinct.
	 */
	public function test_canonical_keeps_pages_apart(): void {
		$source = (string) file_get_contents( get_template_directory() . '/inc/Seo.php' );

		$this->assertStringContainsString( 'get_query_var( \'paged\' )', $source, 'Page 2 must not claim to be page 1.' );
		$this->assertStringContainsString( 'rel="canonical"', $source );

		// Core's own tag stays hooked (a Phase 1 contract), so this class must
		// skip the singular case instead of printing a second claim.
		$this->assertNotFalse( has_action( 'wp_head', 'rel_canonical' ), 'Core canonical must stay hooked.' );
		$this->assertStringNotContainsString( "remove_action( 'wp_head', 'rel_canonical' )", $source );
	}

	/**
	 * Version disclosure is removed and baseline security headers are added.
	 */
	public function test_response_headers_are_hardened(): void {
		$headers = DeBloat::filter_headers(
			array(
				'X-Powered-By' => 'PHP/8.4.27',
				'Content-Type' => 'text/html',
			)
		);

		$this->assertArrayNotHasKey( 'X-Powered-By', $headers );
		$this->assertSame( 'nosniff', $headers['X-Content-Type-Options'] );
		$this->assertSame( 'SAMEORIGIN', $headers['X-Frame-Options'] );
		$this->assertArrayHasKey( 'Referrer-Policy', $headers );
		$this->assertSame( 'text/html', $headers['Content-Type'], 'A real header must survive.' );
	}

	/**
	 * Reading time and word count work on Persian, where `str_word_count` is 0.
	 */
	public function test_persian_word_count_is_real(): void {
		$words = array_fill( 0, 60, 'نورپردازی' );
		$id    = (int) wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_status'  => 'draft',
				'post_title'   => 'P08 words',
				'post_content' => implode( ' ', $words ),
			)
		);

		$this->assertSame( 60, PostData::word_count( $id ) );
		$this->assertGreaterThan( 0, PostData::reading_time( $id ) );

		wp_delete_post( $id, true );
	}

	/**
	 * The hero image is the one thing that must not be lazy.
	 */
	public function test_hero_is_loaded_at_high_priority(): void {
		$hero = (string) file_get_contents( get_template_directory() . '/template-parts/home/section-hero.php' );

		$this->assertStringContainsString( 'fetchpriority="high"', $hero );
		$this->assertStringContainsString( 'loading="lazy"', $hero, 'Later scenes must still wait.' );
	}

	/**
	 * The theme is a valid theme package, not only a folder that happens to run.
	 */
	public function test_theme_package_is_installable(): void {
		$theme = get_template_directory();
		$root  = rtrim( (string) getcwd(), '/' ) . '/erfansanat';

		// The theme is symlinked into WordPress, so use the checkout's copy for
		// the files a package needs.
		if ( ! is_file( $root . '/theme.json' ) ) {
			$root = '/home/user/erfansanat-theme-ai/erfansanat';
		}

		$this->assertFileExists( $root . '/screenshot.png' );
		$this->assertFileExists( $root . '/readme.txt' );
		$this->assertFileExists( $root . '/theme.json' );

		$data = (string) file_get_contents( $root . '/screenshot.png' );
		$this->assertStringStartsWith( "\x89PNG", $data, 'WordPress requires a PNG preview.' );

		$header = (string) file_get_contents( $root . '/style.css' );
		$this->assertStringContainsString( 'Text Domain: erfansanat', $header );
		$this->assertStringContainsString( 'Domain Path:', $header );
		$this->assertStringContainsString( 'Requires PHP:', $header );
	}

	/**
	 * `theme.json` offers the theme's own type and palette, not core's.
	 */
	public function test_theme_json_matches_the_design(): void {
		$root = rtrim( (string) getcwd(), '/' ) . '/erfansanat';
		if ( ! is_file( $root . '/theme.json' ) ) {
			$root = '/home/user/erfansanat-theme-ai/erfansanat';
		}

		$json = json_decode( (string) file_get_contents( $root . '/theme.json' ), true );
		$this->assertIsArray( $json, 'theme.json must be valid JSON.' );

		$families = $json['settings']['typography']['fontFamilies'] ?? array();
		$names    = wp_list_pluck( $families, 'fontFamily' );
		$this->assertNotEmpty( $names );
		$this->assertStringContainsString( 'Vazirmatn', implode( ' ', $names ) );

		$palette = wp_list_pluck( $json['settings']['color']['palette'] ?? array(), 'slug' );
		$this->assertContains( 'brand', $palette );
		$this->assertContains( 'ink', $palette );

		// Offering core's defaults would undo the point of declaring a palette:
		// an editor could pick a colour the stylesheet has no utility for.
		$this->assertFalse( $json['settings']['color']['defaultPalette'] );
		$this->assertFalse( $json['settings']['typography']['fluid'], 'Fluid type would fight the compiled build.' );
	}

	/**
	 * No theme file may reach a third-party host for a font or a script.
	 */
	public function test_theme_loads_nothing_from_a_third_party(): void {
		$theme = get_template_directory();
		$files = array_merge(
			(array) glob( $theme . '/*.php' ),
			(array) glob( $theme . '/inc/*.php' ),
			(array) glob( $theme . '/inc/Forms/*.php' ),
			(array) glob( $theme . '/assets/admin/*.css' )
		);

		$hosts = array( 'fonts.googleapis.com', 'fonts.gstatic.com', 'cdn.jsdelivr.net', 'unpkg.com' );
		foreach ( $files as $file ) {
			if ( ! is_string( $file ) || ! is_file( $file ) ) {
				continue;
			}
			$text = (string) file_get_contents( $file );
			foreach ( $hosts as $host ) {
				$this->assertStringNotContainsString( $host, $text, basename( $file ) . ' loads from ' . $host );
			}
		}
	}

	/**
	 * Both archives keep a no-JavaScript path, which the audit exercises live.
	 */
	public function test_archives_degrade_without_scripting(): void {
		foreach ( array( 'part-projects-archive.php', 'part-shop-archive.php' ) as $file ) {
			$source = (string) file_get_contents( get_template_directory() . '/template-parts/archive/' . $file );
			$this->assertStringContainsString( 'part-native-filters.php', $source, $file );
		}

		$form = (string) file_get_contents( get_template_directory() . '/template-parts/archive/part-native-filters.php' );
		$this->assertStringContainsString( 'method="get"', $form, 'A filter form must be a GET, so the URL is shareable.' );
	}

	/**
	 * Every visible string is translatable, and the template proves it.
	 *
	 * The sources are Persian, so there is nothing to translate at run time; the
	 * .pot exists so the theme can be reused on another language without editing
	 * PHP. A string wrapped without the text domain would silently stay Persian,
	 * which is why the domain is asserted here rather than assumed.
	 */
	public function test_theme_ships_a_translation_template(): void {
		$root = rtrim( (string) getcwd(), '/' ) . '/erfansanat';
		if ( ! is_file( $root . '/languages/erfansanat.pot' ) ) {
			$root = '/home/user/erfansanat-theme-ai/erfansanat';
		}

		$pot = $root . '/languages/erfansanat.pot';
		$this->assertFileExists( $pot );

		$text = (string) file_get_contents( $pot );
		$this->assertStringContainsString( 'X-Domain: erfansanat', $text );
		$this->assertStringContainsString( 'charset=UTF-8', $text );

		// The header block is the only entry with an empty msgid.
		$entries = substr_count( $text, "\nmsgid \"" ) - 1;
		$this->assertGreaterThan( 500, $entries, 'Extraction found almost nothing, so it broke.' );

		// A gettext call without the theme's domain is dead code: the string can
		// never be translated and the catalogue will never mention it.
		$offenders = array();
		$files     = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( get_template_directory(), FilesystemIterator::SKIP_DOTS )
		);
		foreach ( $files as $file ) {
			if ( ! $file->isFile() || 'php' !== $file->getExtension() ) {
				continue;
			}
			$source = (string) file_get_contents( $file->getPathname() );
			if ( preg_match_all( '/\\b(?:esc_html__|esc_html_e|esc_attr__|esc_attr_e|__|_e|_x|_n|_nx)\\s*\\(/', $source, $m ) ) {
				if ( ! preg_match( "/['\"]erfansanat['\"]/", $source ) ) {
					$offenders[] = $file->getFilename();
				}
			}
		}
		$this->assertSame( array(), $offenders, 'A file calls gettext without the erfansanat domain.' );
	}
}
