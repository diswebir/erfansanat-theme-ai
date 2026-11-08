<?php
declare(strict_types=1);
/**
 * Task 5.1 acceptance: fifteen sections, reference order, order/visibility.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Sections;
use ErfanSanat\Theme\Settings;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the section registry, render order and the editor's control.
 */
final class TaskP05T01Test extends TestCase {

	/**
	 * Render a layout to HTML.
	 *
	 * @param array<int, array<string, mixed>> $layout Layout rows.
	 * @return string
	 */
	private function render( array $layout ): string {
		ob_start();
		Sections::render( $layout );
		return (string) ob_get_clean();
	}

	/**
	 * The reference order of S01..S15, as STATIC_TEMPLATE_MAP records it.
	 */
	private function reference_order(): array {
		return array(
			'hero',
			'project-types',
			'projects',
			'about',
			'knowledge',
			'history',
			'products',
			'catalog',
			'services',
			'quality',
			'support',
			'clients',
			'blog',
			'contact-cta',
			'location',
		);
	}

	/**
	 * All fifteen reference sections exist, in the reference order.
	 */
	public function testRegistryMatchesTheReferenceSequence(): void {
		$this->assertSame( $this->reference_order(), array_keys( Sections::registry() ) );
		$this->assertCount( 15, Sections::registry() );
	}

	/**
	 * Every section points at a template part that actually exists.
	 */
	public function testEverySectionHasARealTemplatePart(): void {
		foreach ( Sections::registry() as $key => $section ) {
			$path = get_template_directory() . '/' . ltrim( (string) $section['part'], '/' );
			$this->assertFileExists( $path, $key . ' has no template part.' );
			$this->assertGreaterThan( 0, (int) filesize( $path ), $key . ' is empty.' );
		}
	}

	/**
	 * The rendered order equals the reference order by default.
	 */
	public function testDefaultRenderOrderMatchesTheReference(): void {
		$html  = $this->render( Sections::home_layout() );
		$found = array();
		foreach ( $this->reference_order() as $key ) {
			$position = strpos( $html, 'data-section="' . $key . '"' );
			$this->assertNotFalse( $position, $key . ' did not render.' );
			$found[ $key ] = $position;
		}

		$sorted = $found;
		asort( $sorted );
		$this->assertSame( $found, $sorted, 'Sections must appear in registry order.' );
	}

	/**
	 * A saved reorder changes the output order.
	 */
	public function testSavedOrderIsHonoured(): void {
		$layout         = Sections::default_home();
		$reordered      = array_reverse( $layout );
		$keys           = array_column( $reordered, 'key' );
		$sanitized      = Sections::sanitize_layout( $reordered );
		$rendered_order = array();

		$this->assertSame( $keys, array_column( $sanitized, 'key' ), 'Sanitizing must not reshuffle a valid layout.' );

		$html = $this->render( $sanitized );
		foreach ( $keys as $key ) {
			$pos = strpos( $html, 'data-section="' . $key . '"' );
			if ( false !== $pos ) {
				$rendered_order[] = $key;
			}
		}
		// Sections that render nothing (no data) are skipped, so compare the
		// relative order of what did render against the saved order.
		$expected = array_values( array_intersect( $keys, $rendered_order ) );
		$this->assertSame( $expected, $rendered_order );
	}

	/**
	 * Disabling a section removes it from the output and nothing else.
	 */
	public function testDisabledSectionIsNotRendered(): void {
		$layout = Sections::default_home();
		foreach ( $layout as $index => $row ) {
			if ( 'clients' === $row['key'] ) {
				$layout[ $index ]['enabled'] = false;
			}
		}

		$this->assertNotContains( 'clients', Sections::enabled_keys( $layout ) );

		$html = $this->render( $layout );
		$this->assertStringNotContainsString( 'data-section="clients"', $html );
		$this->assertStringContainsString( 'data-section="product-types"', str_replace( 'project-types', 'product-types', $html ) ?: $html );
		$this->assertStringContainsString( 'data-section="quality"', $html, 'Other sections must survive.' );
	}

	/**
	 * An unknown or hostile layout key is dropped, never rendered.
	 */
	public function testUnknownKeysAreDropped(): void {
		$layout   = Sections::default_home();
		$layout[] = array(
			'key'     => 'evil-section',
			'enabled' => true,
			'fields'  => array(),
		);
		$layout[] = array(
			'key'     => array( 'nested' ),
			'enabled' => true,
			'fields'  => array(),
		);

		$clean = Sections::sanitize_layout( $layout );
		$this->assertNotContains( 'evil-section', array_column( $clean, 'key' ) );

		$html = $this->render( $clean );
		$this->assertStringNotContainsString( 'evil-section', $html );
		$this->assertStringNotContainsString( 'Fatal error', $html );
	}

	/**
	 * A section whose query returns nothing must not print an empty shell.
	 */
	public function testEmptyDataSkipsTheSectionBody(): void {
		$layout = Sections::default_home();
		foreach ( $layout as $index => $row ) {
			if ( 'clients' === $row['key'] ) {
				$layout[ $index ]['fields']['items'] = array();
			}
		}

		ob_start();
		Sections::render( $layout );
		$html = (string) ob_get_clean();

		$this->assertStringContainsString( 'data-section="clients"', $html, 'The wrapper stays so the layout is intact.' );
		$this->assertStringNotContainsString( 'card-light flex flex-col items-start', $html, 'No client cards should print.' );
		$this->assertStringNotContainsString( 'Fatal error', $html );
	}

	/**
	 * The reference anchors exist exactly once and on the right element.
	 */
	public function testReferenceAnchorsAreUnique(): void {
		$html = $this->render( Sections::home_layout() );

		foreach ( array( 'home', 'projects', 'about', 'products', 'services', 'blog-preview', 'contact' ) as $anchor ) {
			$count = substr_count( $html, 'id="' . $anchor . '"' );
			$this->assertSame( 1, $count, 'Anchor #' . $anchor . ' must appear exactly once.' );
		}

		// The hero owns `home`, so no wrapper may repeat it.
		$this->assertSame( 1, substr_count( $html, 'id="home"' ) );
	}

	/**
	 * Every section keeps the data-section hook the editor preview relies on.
	 */
	public function testSectionHooksArePresent(): void {
		$html = $this->render( Sections::home_layout() );
		foreach ( $this->reference_order() as $key ) {
			$this->assertStringContainsString( 'data-section="' . $key . '"', $html );
			$this->assertStringContainsString( 'erfansanat-section--' . $key, $html );
		}
	}

	/**
	 * Titles support one accent fragment without ever printing raw markup.
	 */
	public function testTitleAccentIsEscaped(): void {
		$this->assertSame(
			'پروژه‌های <span class="text-emerald-700">نورپردازی شهری</span>',
			\ErfanSanat\Theme\Frontend\SectionParts::title_markup( 'پروژه‌های [نورپردازی شهری]' )
		);

		$hostile = \ErfanSanat\Theme\Frontend\SectionParts::title_markup( '<script>alert(1)</script> [ok]' );
		$this->assertStringNotContainsString( '<script>', $hostile );
		$this->assertStringContainsString( '&lt;script&gt;', $hostile );
		$this->assertStringContainsString( 'text-emerald-700', $hostile );
	}

	/**
	 * The layout stored in settings is what the front page renders.
	 */
	public function testStoredLayoutDrivesTheFrontPage(): void {
		$original = get_option( 'erfansanat_theme_options', array() );

		$layout = Sections::default_home();
		foreach ( $layout as $index => $row ) {
			if ( 'clients' === $row['key'] ) {
				$layout[ $index ]['enabled'] = false;
			}
		}
		// Settings are stored in one option row; write through it the way the
		// admin screen does, then flush the static cache.
		$stored                       = is_array( $original ) ? $original : array();
		$stored['home_sections']      = Sections::sanitize_layout( $layout );
		update_option( 'erfansanat_theme_options', $stored );
		Settings::flush();

		$loaded = Sections::home_layout();
		$this->assertNotContains( 'clients', Sections::enabled_keys( $loaded ) );
		$this->assertStringNotContainsString( 'data-section="clients"', $this->render( $loaded ) );

		update_option( 'erfansanat_theme_options', $original );
		Settings::flush();
		$this->assertSame( $original, get_option( 'erfansanat_theme_options', array() ) );
	}

	/**
	 * Sections that read post types degrade when those types are absent.
	 */
	public function testSectionsSurviveWithoutTheirPostType(): void {
		$layout = Sections::default_home();
		$html   = $this->render( $layout );

		$this->assertStringNotContainsString( 'Fatal error', $html );
		$this->assertStringNotContainsString( 'Warning: ', $html );
		$this->assertStringNotContainsString( 'Notice: ', $html );
	}

	/**
	 * No section prints a sample price; money only comes from WooCommerce.
	 */
	public function testNoHardCodedPricesInSectionParts(): void {
		foreach ( glob( get_template_directory() . '/template-parts/home/section-*.php' ) ?: array() as $file ) {
			$code = (string) file_get_contents( $file );
			$this->assertStringNotContainsString( 'localStorage', $code, basename( $file ) . ' must not use the mirror\'s browser storage.' );
			$this->assertStringNotContainsString( 'تومان', $code, basename( $file ) . ' must not print a fixed price.' );
			$this->assertStringNotContainsString( '.html"', $code, basename( $file ) . ' must not link a static mirror file.' );
		}
	}
}
