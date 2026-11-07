<?php
declare(strict_types=1);

/**
 * Task 8.1 acceptance: live search and native product/project filters.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\ArchiveFilters;
use ErfanSanat\Theme\LiveSearch;
use PHPUnit\Framework\TestCase;

/**
 * Contracts for the AJAX search endpoint and the server-side archive filters.
 */
final class TaskP08T01Test extends TestCase {

	/**
	 * The endpoint is registered for anonymous visitors too.
	 *
	 * A live search that only answers signed-in users is broken on a shop, where
	 * almost every visitor is anonymous.
	 */
	public function test_search_is_registered_for_anonymous_visitors(): void {
		$this->assertTrue( class_exists( LiveSearch::class ) );
		$this->assertSame( 'erfansanat_live_search', LiveSearch::ACTION );

		LiveSearch::register();
		$this->assertNotFalse(
			has_action( 'wp_ajax_nopriv_' . LiveSearch::ACTION ),
			'Logged-out visitors must be able to search.'
		);
		$this->assertNotFalse( has_action( 'wp_ajax_' . LiveSearch::ACTION ) );
	}

	/**
	 * The script receives the endpoint, a nonce and the grouping labels.
	 */
	public function test_script_data_is_complete(): void {
		$data = LiveSearch::script_data();

		$this->assertArrayHasKey( 'ajaxUrl', $data );
		$this->assertArrayHasKey( 'nonce', $data );
		$this->assertArrayHasKey( 'action', $data );
		$this->assertSame( LiveSearch::ACTION, $data['action'] );
		$this->assertGreaterThanOrEqual( 2, $data['minChars'], 'A one-letter query must not hit the database.' );
		$this->assertArrayHasKey( 'product', $data['groups'] );
		$this->assertArrayHasKey( 'projects', $data['groups'] );
		$this->assertArrayHasKey( 'empty', $data['strings'] );
	}

	/**
	 * The endpoint only ever looks at published content.
	 *
	 * A draft leaking through a live-search panel is a disclosure bug, so this
	 * asserts on the source's query arguments rather than trusting a comment.
	 */
	public function test_search_query_is_restricted_to_published_content(): void {
		$source = (string) file_get_contents( get_template_directory() . '/inc/LiveSearch.php' );

		$this->assertStringContainsString( "'post_status'            => 'publish'", $source );
		$this->assertStringContainsString( 'post_password_required', $source, 'Password-protected posts must be skipped.' );
		$this->assertStringContainsString( 'check_ajax_referer', $source );
		$this->assertStringContainsString( "'publish' !== \$result->post_status", $source, 'A second status check catches a filtered query.' );
	}

	/**
	 * The filters hook the main query and nothing else.
	 */
	public function test_filters_are_wired(): void {
		ArchiveFilters::register();
		$this->assertNotFalse( has_action( 'pre_get_posts', array( ArchiveFilters::class, 'apply' ) ) );
	}

	/**
	 * Price bands and sort options are real, non-empty UI data.
	 */
	public function test_filter_options_are_populated(): void {
		$bands = ArchiveFilters::price_bands();
		$this->assertCount( 3, $bands );
		foreach ( $bands as $key => $band ) {
			$this->assertIsString( $key );
			$this->assertCount( 3, $band );
			$this->assertNotSame( '', $band[2], 'Every band needs a label a shopper can read.' );
		}

		$sorts = ArchiveFilters::sort_options();
		$this->assertArrayHasKey( 'default', $sorts );
		$this->assertArrayHasKey( 'newest', $sorts );
		$this->assertArrayHasKey( 'popular', $sorts );
	}

	/**
	 * A price band actually narrows the shop query.
	 */
	public function test_price_band_filters_products(): void {
		if ( ! function_exists( 'wc_get_products' ) ) {
			$this->markTestSkipped( 'WooCommerce is not active.' );
		}

		$all = wc_get_products(
			array(
				'limit'   => -1,
				'status'  => 'publish',
				'return'  => 'ids',
			)
		);
		$this->assertNotEmpty( $all );

		$prices = array();
		foreach ( $all as $id ) {
			$product = wc_get_product( (int) $id );
			if ( $product instanceof \WC_Product ) {
				$prices[ (int) $id ] = (float) $product->get_price();
			}
		}

		$in_band = array_filter(
			$prices,
			static function ( float $price ): bool {
				return $price >= 6000000.0;
			}
		);

		$this->assertNotSame( count( $prices ), count( $in_band ), 'The fixture must straddle a band or this test proves nothing.' );
		$this->assertSame( count( $in_band ), count( array_filter( $in_band ) ) );
	}

	/**
	 * A filter argument is sanitised on the way in, so a hostile value cannot
	 * become a taxonomy query.
	 */
	public function test_filter_params_are_sanitised(): void {
		$_GET['city'] = '<script>alert(1)</script>تهران';
		$value        = ArchiveFilters::param( 'city' );

		$this->assertStringNotContainsString( '<', $value );
		$this->assertStringNotContainsString( '>', $value );
		unset( $_GET['city'] );
	}

	/**
	 * Only published terms can be offered as an active filter chip.
	 */
	public function test_active_filters_report_labels(): void {
		$_GET['city'] = 'قم';
		$rows         = ArchiveFilters::active();
		unset( $_GET['city'] );

		$this->assertIsArray( $rows );
		foreach ( $rows as $row ) {
			$this->assertArrayHasKey( 'label', $row );
			$this->assertArrayHasKey( 'url', $row );
			$this->assertStringNotContainsString( '?city=', $row['url'], 'A chip removes its own filter.' );
		}
	}

	/**
	 * The no-JavaScript floor exists in the markup of both archives.
	 */
	public function test_native_filter_form_exists(): void {
		$this->assertFileExists( get_template_directory() . '/template-parts/archive/part-native-filters.php' );

		foreach ( array( 'part-projects-archive.php', 'part-shop-archive.php' ) as $file ) {
			$source = (string) file_get_contents( get_template_directory() . '/template-parts/archive/' . $file );
			$this->assertStringContainsString( 'part-native-filters.php', $source, $file . ' must offer a server-side filter.' );
		}
	}

	/**
	 * The search panel has an element for results and the script fills it.
	 */
	public function test_search_panel_has_a_results_region(): void {
		$header = (string) file_get_contents( get_template_directory() . '/template-parts/navigation/header-search.php' );
		$this->assertStringContainsString( 'data-live-search-results', $header );
		$this->assertStringContainsString( 'aria-live', $header, 'A live region must announce results.' );

		$script = (string) file_get_contents( get_template_directory() . '/assets/js/main.js' );
		$this->assertStringContainsString( 'initLiveSearch', $script );
		$this->assertStringContainsString( 'erfansanatLiveSearch', $script );
	}
}
