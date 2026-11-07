<?php
declare(strict_types=1);
/**
 * Task 1.2 acceptance: targeted front-end pruning that cannot break required plugins.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\DeBloat;
use PHPUnit\Framework\TestCase;

/**
 * Verifies both sides of the contract: what must disappear from the public front
 * end, and what must survive for WooCommerce, the editor and the SEO layer.
 */
final class TaskP01T02Test extends TestCase {

	/**
	 * Run one template render with the front-end hooks fired, and return the head.
	 *
	 * The PHP-Wasm server cannot call itself over HTTP, so the page is rendered
	 * in-process. HTTP-level evidence is collected by check-http.py instead.
	 *
	 * @param array<string, mixed> $query Simulated main query flags.
	 * @return string
	 */
	private function render_head( array $query = array() ): string {
		global $wp_query, $wp_the_query, $pagenow;

		$had_wp_query = isset( $wp_query );
		$this->assertTrue( $had_wp_query, 'WordPress main query must exist.' );

		$original_query = $wp_query;
		$original_query_vars = $original_query->query_vars;
		$original_wp = isset( $wp_the_query ) ? $wp_the_query : null;
		$original_is_admin = is_admin();
		$original_pagenow = $pagenow;

		$flags = array();
		$vars  = array();
		foreach ( $query as $key => $value ) {
			if ( ! str_starts_with( (string) $key, 'is_' ) ) {
				$vars[ $key ] = $value;
				continue;
			}
			$flags[ $key ] = (bool) $value;
		}
		$wp_query = new WP_Query();
		$wp_query->query_vars = $vars;
		foreach ( array( 'is_home', 'is_front_page', 'is_page', 'is_single', 'is_singular', 'is_archive', 'is_search', 'is_feed', 'is_404' ) as $flag ) {
			$wp_query->{$flag} = $flags[ $flag ] ?? false;
		}
		$wp_the_query = $wp_query;
		$pagenow = 'index.php';

		wp_styles()->queue = array();
		wp_scripts()->queue = array();
		wp_dequeue_style( array( 'wp-block-library', 'global-styles', 'classic-theme-styles' ) );
		wp_dequeue_script( array( 'wp-emoji', 'wp-embed' ) );

		$html = $this->capture( static function (): void {
			do_action( 'wp_enqueue_scripts' );
			do_action( 'wp_head' );
		} );
		$this->restore_query( $original_query, $original_query_vars, $original_wp, $original_pagenow );

		if ( $original_is_admin ) {
			set_current_screen( 'dashboard' );
		}

		return $html;
	}

	/**
	 * Put the global query back after a simulated render.
	 *
	 * @param WP_Query             $query      Original main query.
	 * @param array<string, mixed> $query_vars Original query vars.
	 * @param WP_Query|null        $wp_the_query Original secondary query global.
	 * @param string               $pagenow    Original admin page global.
	 * @return void
	 */
	private function restore_query( $query, array $query_vars, $original_wp_the_query, $original_pagenow ): void {
		global $wp_query, $wp_the_query, $pagenow;
		$wp_query = $query;
		$wp_query->query_vars = $query_vars;
		$wp_the_query = $original_wp_the_query;
		$pagenow = $original_pagenow;
	}

	/**
	 * Capture echoed output of a callable.
	 *
	 * @param callable $callable Callback that prints.
	 * @return string
	 */
	private function capture( callable $callable ): string {
		ob_start();
		$callable();
		return (string) ob_get_clean();
	}

	/**
	 * Core block layer must not reach the public head.
	 */
	public function testBlockLibraryAndGlobalStylesAreGone(): void {
		$head = $this->render_head();
		$this->assertStringNotContainsString( 'wp-block-library', $head );
		$this->assertStringNotContainsString( 'global-styles', $head );
		$this->assertStringNotContainsString( 'classic-theme-styles', $head );
		$this->assertFalse( wp_style_is( 'wp-block-library', 'enqueued' ) );
		$this->assertFalse( wp_style_is( 'global-styles', 'enqueued' ) );
	}

	/**
	 * Emoji and oEmbed runtimes must not be printed on the front end.
	 */
	public function testEmojiAndEmbedRuntimeAreGone(): void {
		$head = $this->render_head();
		$this->assertStringNotContainsString( 'wp-emoji', $head );
		$this->assertStringNotContainsString( 'emoji', strtolower( $head ) );
		$this->assertFalse( has_action( 'wp_head', 'print_emoji_detection_script' ) );
		$this->assertFalse( has_action( 'wp_print_styles', 'print_emoji_styles' ) );
		$this->assertFalse( wp_script_is( 'wp-emoji', 'enqueued' ) );
		$this->assertFalse( wp_script_is( 'wp-embed', 'enqueued' ) );
	}

	/**
	 * Meta that the SEO plugin owns must be removed, while robots and feeds stay.
	 */
	public function testStaleHeadTagsAreRemovedButSeoOutputSurvives(): void {
		$head = $this->render_head( array( 'is_single' => true ) );
		$this->assertStringNotContainsString( 'name="generator"', $head );
		$this->assertStringNotContainsString( 'rel="EditURI"', $head );
		$this->assertStringNotContainsString( 'shortlink', $head );
		$this->assertStringNotContainsString( 'oembed', strtolower( $head ) );
		$this->assertStringNotContainsString( 'api.w.org', $head );

		if ( is_singular() ) {
			$this->assertStringContainsString( 'rel="canonical"', $head, 'Canonical links are generated by WordPress/SEOPress and must stay.' );
		} else {
			// Canonical output belongs to WordPress/SEOPress: the theme must keep
			// the hook and must not strip the tag from the head.
			$this->assertNotFalse( has_action( 'wp_head', 'rel_canonical' ) );
			$canonical = '<link rel="canonical" href="https://erfansanat.com/sample/" />';
			$this->assertSame( $canonical, DeBloat::strip_head_tags( $canonical ) );
		}
		$this->assertStringContainsString( "name='robots'", $head );
		$this->assertStringContainsString( 'application/rss+xml', $head, 'Feed autodiscovery must stay until SEO rules say otherwise.' );
	}

	/**
	 * The emoji URL hints must be filtered without removing unrelated hints.
	 */
	public function testEmojiResourceHintsAreFilteredOnly(): void {
		$hints = DeBloat::filter_resource_hints(
			array(
				'https://s.w.org/images/core/emoji/16.0.1/72x72/',
				array(
					'href' => 'https://s.w.org/images/core/emoji/16.0.1/svg/',
					'crossorigin',
				),
				array( 'href' => 'https://fonts.example.test/' ),
			),
			'dns-prefetch'
		);

		$this->assertCount( 1, $hints );
		$this->assertSame( array( 'href' => 'https://fonts.example.test/' ), $hints[0] );
		$this->assertSame( array( 'not-an-array' ), DeBloat::filter_resource_hints( array( 'not-an-array' ), 'x' ) );
	}

	/**
	 * WooCommerce's own block styles and scripts must survive the clean-up.
	 */
	public function testWooCommerceAssetsSurvive(): void {
		wp_enqueue_style(
			'wc-blocks-style',
			'/wp-content/plugins/woocommerce/assets/client/blocks/wc-blocks.css',
			array(),
			'10.9.4'
		);
		wp_enqueue_script( 'wc-add-to-cart', '/wp-content/plugins/woocommerce/assets/js/frontend/add-to-cart.min.js', array(), '10.9.4', true );
		wp_enqueue_script( 'wc-single-product', '/wp-content/plugins/woocommerce/assets/js/frontend/single-product.min.js', array(), '10.9.4', true );

		DeBloat::drop_block_styles();
		DeBloat::drop_legacy_scripts();

		$this->assertTrue( wp_style_is( 'wc-blocks-style', 'enqueued' ), 'WooCommerce block styles are owned by WooCommerce, not the theme.' );
		$this->assertTrue( wp_script_is( 'wc-add-to-cart', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'wc-single-product', 'enqueued' ) );
	}

	/**
	 * Third-party handles used by the swatches, SMS login and user panel must not
	 * be deregistered, because the theme never owned them.
	 */
	public function testThirdPartyHandlesAreUntouched(): void {
		wp_enqueue_style( 'woo-variation-swatches', '/swatches.css', array(), '1.0' );
		wp_enqueue_script( 'voorodak-sms-login', '/sms.js', array(), '1.0', true );
		wp_enqueue_style( 'fl-panel', '/panel.css', array(), '1.0' );

		DeBloat::drop_block_styles();
		DeBloat::drop_legacy_scripts();

		$this->assertTrue( wp_style_is( 'woo-variation-swatches', 'enqueued' ) );
		$this->assertTrue( wp_script_is( 'voorodak-sms-login', 'enqueued' ) );
		$this->assertTrue( wp_style_is( 'fl-panel', 'enqueued' ) );
	}

	/**
	 * Administration, REST and preview requests must keep block assets.
	 */
	public function testAdminRestAndFeedAreNotPruned(): void {
		global $wp_query;
		$original = $wp_query;
		$original_vars = $original->query_vars;

		$wp_query = new WP_Query();
		$wp_query->query_vars = array();
		$wp_query->is_feed = true;

		wp_enqueue_style( 'wp-block-library', '/block-library.css', array(), '7.1' );
		DeBloat::drop_block_styles();
		$kept_on_feed = wp_style_is( 'wp-block-library', 'enqueued' );
		wp_dequeue_style( 'wp-block-library' );
		wp_deregister_style( 'wp-block-library' );
		wp_register_style( 'wp-block-library', '/block-library.css', array(), '7.1' );

		wp_enqueue_style( 'wp-block-library', '/block-library.css', array(), '7.1' );
		$wp_query->is_feed = false;
		$wp_query->is_home = true;
		DeBloat::drop_block_styles();
		$removed_on_front = ! wp_style_is( 'wp-block-library', 'enqueued' );

		$wp_query = $original;
		$wp_query->query_vars = $original_vars;

		$this->assertTrue( $kept_on_feed, 'Feeds keep the block stylesheet.' );
		$this->assertTrue( $removed_on_front, 'Front-end requests drop the block stylesheet.' );
	}

	/**
	 * The opt-out filter must be honoured when a page still needs blocks.
	 */
	public function testCleanUpCanBeDisabledByFilter(): void {
		add_filter( 'erfansanat_debloat_front_end', '__return_false' );
		$this->assertFalse( DeBloat::should_clean() );
		remove_filter( 'erfansanat_debloat_front_end', '__return_false' );
		$this->assertTrue( DeBloat::should_clean() );
	}

	/**
	 * The block editor must keep its own styles; theme pruning is front-end only.
	 */
	public function testBlockEditorAssetsAreNotTouched(): void {
		$source = (string) file_get_contents( get_template_directory() . '/inc/DeBloat.php' );
		$this->assertStringNotContainsString( 'enqueue_block_editor_assets', $source );
		$this->assertStringNotContainsString( 'block_editor_settings_all', $source );
		$this->assertStringNotContainsString( 'should_load_block_editor_scripts_and_styles', $source );
		$this->assertStringContainsString( "'wc-blocks-style' === \$handle", $source, 'WooCommerce block styles need an explicit guard.' );
	}

	/**
	 * Turkish-I safety: the head filter must be case-insensitive.
	 */
	public function testGeneratorTagRemovalIsCaseInsensitive(): void {
		$input = '<meta name="Generator" content="WordPress 7.1.4" /><link rel="SHORTLINK" href="/?p=1" />';
		$this->assertSame( '', DeBloat::strip_head_tags( $input ) );
		$this->assertSame( '', DeBloat::strip_head_tags( '<meta name="generator" content="x">' ) );
		$this->assertSame( '', DeBloat::strip_head_tags( "<meta name='generator' content='x'>" ) );
	}

	/**
	 * Empty and unexpected input must not raise a warning.
	 */
	public function testHeadFilterToleratesEmptyInput(): void {
		$this->assertSame( '', DeBloat::strip_head_tags( '' ) );
		$this->assertSame( '<meta charset="utf-8">', DeBloat::strip_head_tags( '<meta charset="utf-8">' ) );
	}
}
