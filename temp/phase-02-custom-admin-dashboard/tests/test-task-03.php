<?php
declare(strict_types=1);
/**
 * Task 2.3 acceptance: ordered, enable-able, repeatable home sections.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Admin\SectionManager;
use ErfanSanat\Theme\Sections;
use ErfanSanat\Theme\Settings;
use PHPUnit\Framework\TestCase;

/**
 * Exercises the section registry, layout sanitisation and ordered rendering.
 */
final class TaskP02T03Test extends TestCase {

	/**
	 * Start from shipped defaults.
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
	 * Capture the renderer.
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
	 * Pull the rendered section keys out of markup, in order.
	 *
	 * @param string $html Rendered markup.
	 * @return array<int, string>
	 */
	private function rendered_keys( string $html ): array {
		preg_match_all( '/data-section="([a-z0-9-]+)"/', $html, $matches );
		return $matches[1];
	}

	/**
	 * The default home layout covers all fifteen reference sections in order.
	 */
	public function testDefaultHomeCoversFifteenReferenceSections(): void {
		$default = Sections::default_home();
		$this->assertCount( 15, $default );
		$this->assertSame(
			array(
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
			),
			array_column( $default, 'key' )
		);
		foreach ( $default as $row ) {
			$this->assertTrue( $row['enabled'], $row['key'] . ' must start enabled.' );
		}

		$html = $this->render( $default );
		$this->assertSame( array_column( $default, 'key' ), $this->rendered_keys( $html ) );
		$this->assertSame( 15, substr_count( $html, 'erfansanat-section--' ) );
	}

	/**
	 * The seven anchors of the static reference are the only ids printed.
	 */
	public function testReferenceAnchorsAreReproduced(): void {
		$html = $this->render( Sections::default_home() );
		preg_match_all( '/<section id="([^"]+)"/', $html, $matches );
		$this->assertSame( array( 'home', 'projects', 'about', 'products', 'services', 'blog-preview', 'contact' ), $matches[1] );
	}

	/**
	 * Render order is exactly the saved order.
	 */
	public function testRenderOrderEqualsSavedOrder(): void {
		$layout = Sections::default_home();
		// Reverse the layout: this is what a drag to the top of the last row does.
		$reversed = array_reverse( $layout );
		$saved    = Sections::sanitize_layout( $reversed );
		$this->assertSame( array_column( $reversed, 'key' ), array_column( $saved, 'key' ) );

		$html = $this->render( $saved );
		$this->assertSame( array_column( $reversed, 'key' ), $this->rendered_keys( $html ) );
		$this->assertSame( 'location', $this->rendered_keys( $html )[0] );
	}

	/**
	 * A disabled section is not rendered, but keeps its place in the layout.
	 */
	public function testDisabledSectionsAreNotRendered(): void {
		$layout = Sections::default_home();
		foreach ( $layout as &$row ) {
			if ( 'history' === $row['key'] || 'clients' === $row['key'] ) {
				$row['enabled'] = false;
			}
		}
		unset( $row );

		$saved = Sections::sanitize_layout( $layout );
		$this->assertCount( 15, $saved, 'Disabled sections stay in the layout.' );
		$this->assertSame(
			array( 'hero', 'project-types', 'projects', 'about', 'knowledge', 'products', 'catalog', 'services', 'quality', 'support', 'blog', 'contact-cta', 'location' ),
			Sections::enabled_keys( $saved )
		);

		$html = $this->render( $saved );
		$this->assertCount( 13, $this->rendered_keys( $html ) );
		$this->assertStringNotContainsString( 'erfansanat-section--history', $html );
		$this->assertStringNotContainsString( 'erfansanat-section--clients', $html );
	}

	/**
	 * Repeater rows survive a save byte for byte, in order, including removals.
	 */
	public function testRepeaterSavesWithoutLosingItems(): void {
		$layout = Sections::default_home();
		$chips  = array();
		foreach ( $layout as &$row ) {
			if ( 'hero' !== $row['key'] ) {
				continue;
			}
			$row['fields']['chips'] = array(
				array(
					'title' => 'سوم',
					'icon'  => 'city',
				),
				array(
					'title' => 'اول',
					'icon'  => 'star',
				),
				array(
					'title' => 'دوم',
					'icon'  => 'shield',
				),
			);
			$chips = $row['fields']['chips'];
		}
		unset( $row );

		$saved = Sections::sanitize_layout( $layout );
		$hero  = array_values(
			array_filter(
				$saved,
				static fn( array $row ): bool => 'hero' === $row['key']
			)
		)[0];
		$this->assertSame( $chips, $hero['fields']['chips'], 'Repeater order and content must round-trip.' );

		// Removing the middle row must not shift or drop the remaining ones.
		array_splice( $chips, 1, 1 );
		$layout2 = $layout;
		foreach ( $layout2 as &$row ) {
			if ( 'hero' === $row['key'] ) {
				$row['fields']['chips'] = $chips;
			}
		}
		unset( $row );
		$saved2 = Sections::sanitize_layout( $layout2 );
		$hero2  = array_values(
			array_filter(
				$saved2,
				static fn( array $row ): bool => 'hero' === $row['key']
			)
		)[0];
		$this->assertSame( $chips, $hero2['fields']['chips'] );
		$this->assertCount( 2, $hero2['fields']['chips'] );
	}

	/**
	 * A layout posted as JSON (what the browser sends) is decoded and sanitised.
	 */
	public function testJsonLayoutFromTheBrowserIsAccepted(): void {
		$layout = array(
			array(
				'key'     => 'location',
				'enabled' => 1,
				'fields'  => array( 'title' => 'نشانی آزمایشی' ),
			),
			array(
				'key'     => 'hero',
				'enabled' => 0,
				'fields'  => array( 'title' => 'تیتر آزمایشی' ),
			),
		);
		$result = Settings::sanitize( array( 'home_sections' => (string) wp_json_encode( $layout ) ) );

		$this->assertIsArray( $result['values']['home_sections'] );
		$this->assertCount( 15, $result['values']['home_sections'], 'Missing sections are appended.' );
		$keys = array_column( $result['values']['home_sections'], 'key' );
		$this->assertSame( 'location', $keys[0], 'Saved order wins.' );
		$this->assertSame( 'hero', $keys[1] );
		$this->assertSame( 'نشانی آزمایشی', $result['values']['home_sections'][0]['fields']['title'] );
		$this->assertFalse( $result['values']['home_sections'][1]['enabled'] );
		$this->assertSame( $result['errors'], array() );
	}

	/**
	 * Unknown section ids, duplicates and hostile payloads are rejected.
	 */
	public function testUnknownAndHostileDataIsRejected(): void {
		$raw = array(
			array(
				'key'     => 'does-not-exist',
				'enabled' => 1,
				'fields'  => array( 'title' => 'evil' ),
			),
			array(
				'key'     => 'hero',
				'enabled' => 1,
				'fields'  => array(
					'title'    => '<script>alert(1)</script>تیتر',
					'media_id' => '999999',
					'cta'      => array(
						'title' => 'دکمه',
						'url'   => 'javascript:alert(1)',
					),
					'chips'    => array(
						array(
							'title' => 'آیتم',
							'icon'  => '../../etc/passwd',
						),
						array(
							'title' => 'javascript:alert(1)',
							'icon'  => 'city',
						),
					),
					'unknown'  => 'drop me',
				),
			),
			array(
				'key'     => 'hero',
				'enabled' => 1,
				'fields'  => array(),
			),
			'not-a-row',
		);

		$saved = Sections::sanitize_layout( $raw );
		$this->assertCount( 15, $saved );
		$this->assertSame( 'hero', $saved[0]['key'], 'Unknown keys are dropped, hero stays first.' );
		$this->assertSame( 1, count( array_filter( $saved, static fn( array $r ): bool => 'hero' === $r['key'] ) ), 'Duplicates collapse.' );

		$fields = $saved[0]['fields'];
		$this->assertStringNotContainsString( '<script>', $fields['title'] );
		$this->assertSame( 0, $fields['media_id'], 'A non-attachment id is discarded.' );
		$this->assertSame( 'دکمه', $fields['cta']['title'], 'A submitted title is kept.' );
		$this->assertSame( '', $fields['cta']['url'], 'javascript: URLs are rejected instead of reverting to the default.' );
		$this->assertArrayNotHasKey( 'unknown', $fields );
		$this->assertSame( 'lamp', $fields['chips'][0]['icon'], 'A hostile icon name falls back to the default.' );
		$this->assertCount( 2, $fields['chips'], 'A hostile URL alone does not delete a titled row.' );
	}

	/**
	 * A corrupted stored layout falls back to the default instead of fatalling.
	 */
	public function testCorruptedStoredLayoutFallsBackToDefault(): void {
		update_option( Settings::OPTION, array( 'home_sections' => 'not-a-layout' ) );
		Settings::flush();
		$this->assertCount( 15, Sections::home_layout() );

		update_option( Settings::OPTION, array( 'home_sections' => array( array( 'no_key' => true ) ) ) );
		Settings::flush();
		$this->assertCount( 15, Sections::home_layout() );

		update_option( Settings::OPTION, array( 'home_sections' => array() ) );
		Settings::flush();
		$this->assertSame( 'hero', Sections::home_layout()[0]['key'] );
	}

	/**
	 * A saved layout is used verbatim on the next request.
	 */
	public function testStoredLayoutDrivesTheRenderer(): void {
		$layout = Sections::default_home();
		$layout = array_reverse( $layout );
		update_option( Settings::OPTION, array( 'home_sections' => $layout ) );
		Settings::flush();

		$keys = array_column( Sections::home_layout(), 'key' );
		$this->assertSame( 'location', $keys[0] );
		$this->assertSame( array_column( $layout, 'key' ), $keys );
	}

	/**
	 * The manager exposes drag handles, keyboard movers and every field type.
	 */
	public function testManagerExposesDragKeyboardAndAllFieldTypes(): void {
		wp_set_current_user( 1 );
		ob_start();
		SectionManager::render_panel();
		$html = (string) ob_get_clean();
		wp_set_current_user( 0 );

		$this->assertSame( 15, substr_count( $html, 'class="erfansanat-sections__item"' ) );
		$this->assertSame( 15, substr_count( $html, 'data-move="up"' ) );
		$this->assertSame( 15, substr_count( $html, 'data-move="down"' ) );
		$this->assertSame( 15, substr_count( $html, 'draggable="true"' ) );
		$this->assertStringContainsString( 'data-sections-input', $html );
		$this->assertStringContainsString( 'data-media-select', $html, 'Media field type is rendered.' );
		$this->assertStringContainsString( 'data-type="icon"', $html, 'Icon field type is rendered.' );
		$this->assertStringContainsString( 'data-section-repeater', $html, 'Repeater field type is rendered.' );
		$this->assertStringContainsString( 'type="url"', $html, 'Link/URL field type is rendered.' );
		$this->assertStringContainsString( 'aria-label="', $html, 'Move buttons carry accessible names.' );
	}

	/**
	 * The script ships keyboard reordering and JSON collection, not only drag.
	 */
	public function testAdminScriptImplementsKeyboardAndDragReordering(): void {
		$js = (string) file_get_contents( get_template_directory() . '/assets/admin/options.js' );
		$this->assertStringContainsString( "'dragstart'", $js );
		$this->assertStringContainsString( "'dragover'", $js );
		$this->assertStringContainsString( "event.altKey", $js, 'Alt+Arrow moves a row without a mouse.' );
		$this->assertStringContainsString( "data-sections-input", $js );
		$this->assertStringContainsString( 'collectSections', $js );
	}

	/**
	 * A page override narrows the home layout without breaking unknown ids.
	 */
	public function testPageOverrideReordersAndIgnoresUnknownKeys(): void {
		$page = wp_insert_post(
			array(
				'post_title'   => 'es-test-sections',
				'post_type'    => 'page',
				'post_status'  => 'draft',
				'post_content' => '',
			)
		);
		$this->assertIsInt( $page );

		update_post_meta( $page, SectionManager::META_KEY, array( 'contact-cta', 'not-a-section', 'hero', 'hero' ) );
		$keys = array_column( Sections::layout_for_page( (int) $page ), 'key' );
		$this->assertSame( array( 'contact-cta', 'hero' ), $keys );

		delete_post_meta( $page, SectionManager::META_KEY );
		$this->assertCount( 15, Sections::layout_for_page( (int) $page ), 'Without an override the home layout applies.' );
		wp_delete_post( (int) $page, true );
	}

	/**
	 * The section tab appears in the panel schema and the layout never leaves the single option.
	 */
	public function testLayoutIsStoredInTheSingleOptionRow(): void {
		global $wpdb;
		$layout = Sections::default_home();
		update_option( Settings::OPTION, array( 'home_sections' => $layout ) );
		Settings::flush();

		$rows = (int) $wpdb->get_var(
			$wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s", '%erfansanat_section%' )
		);
		$this->assertSame( 0, $rows, 'The layout must not create its own option row.' );
		$this->assertIsArray( get_option( Settings::OPTION )['home_sections'] );
		$this->assertArrayHasKey( 'sections', Settings::schema() );
		$this->assertArrayHasKey( 'home_sections', Settings::fields() );
	}
}
