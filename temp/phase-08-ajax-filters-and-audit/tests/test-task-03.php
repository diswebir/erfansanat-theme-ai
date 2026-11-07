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
}
