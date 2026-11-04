<?php
declare(strict_types=1);
/**
 * Task 6.2 acceptance: the shop archive, product categories and product tags.
 *
 * The archive is a real, paginated archive: the main query is rewritten, the
 * grid carries the filter payload the browser script reads, and every visible
 * value comes from WooCommerce or the term tables.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Data\ProductData;
use ErfanSanat\Theme\Frontend\SectionParts;
use ErfanSanat\Theme\WooCommerce;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the archive templates and their data contract.
 */
final class TaskP06T02Test extends TestCase {

	/**
	 * Render a template part with the variables it reads.
	 *
	 * @param string               $part    Path inside theme.
	 * @param array<string, mixed> $context Variables.
	 * @return string
	 */
	private function render( string $part, array $context = array() ): string {
		$file = get_template_directory() . '/' . ltrim( $part, '/' );
		$this->assertFileExists( $file );

		ob_start();
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Test harness passes the exact variables.
		extract( $context, EXTR_SKIP );
		include $file;
		return (string) ob_get_clean();
	}

	/**
	 * The three archive templates exist where WooCommerce looks first.
	 */
	public function testArchiveTemplatesExist(): void {
		foreach ( array( 'archive-product.php', 'taxonomy-product_cat.php', 'taxonomy-product_tag.php' ) as $file ) {
			$this->assertFileExists( get_template_directory() . '/' . $file, $file . ' must override the plugin template.' );
		}
		$this->assertFileExists( get_template_directory() . '/template-parts/archive/part-shop-archive.php' );
	}

	/**
	 * The archive query is paginated with the reference page size.
	 */
	public function testArchivePagingRewritesTheMainQuery(): void {
		$query = new \WP_Query();
		$query->init();
		$query->is_post_type_archive = true;
		$query->set( 'post_type', 'product' );

		WooCommerce::paginate_archive_query( $query );
		$this->assertSame( 9, (int) $query->get( 'posts_per_page' ), 'The reference page holds nine products.' );

		// A second page must move the query, not just the markup.
		$paged = new \WP_Query();
		$paged->init();
		$paged->is_post_type_archive = true;
		$paged->set( 'post_type', 'product' );
		$paged->set( 'page', 2 );
		WooCommerce::paginate_archive_query( $paged );
		$this->assertSame( 2, (int) $paged->get( 'paged' ), '?page=2 must become a real query page.' );
	}

	/**
	 * Only the main query of a product archive is rewritten.
	 */
	public function testOnlyProductArchiveMainQueriesAreTouched(): void {
		global $wp_the_query;

		$original = $wp_the_query;

		$archive = new \WP_Query();
		$archive->init();
		$archive->is_post_type_archive = true;
		$archive->set( 'post_type', 'product' );
		$wp_the_query = $archive; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored below.
		$this->assertTrue( WooCommerce::is_product_archive_query( $archive ), 'The shop archive is rewritten.' );
		$this->assertTrue( WooCommerce::is_product_archive_query( $archive ), 'The category archive is rewritten.' );

		// A secondary query is left alone even when it looks like an archive.
		$secondary = new \WP_Query();
		$secondary->init();
		$secondary->is_post_type_archive = true;
		$secondary->set( 'post_type', 'product' );
		$this->assertFalse( WooCommerce::is_product_archive_query( $secondary ), 'A secondary query must not be rewritten.' );

		// So is anything that is not a product archive.
		$other = new \WP_Query();
		$other->init();
		$other->set( 'posts_per_page', 5 );
		$this->assertFalse( WooCommerce::is_product_archive_query( $other ) );
		WooCommerce::archive_paging( $other );
		$this->assertSame( 5, (int) $other->get( 'posts_per_page' ), 'archive_paging must gate, not just paginate.' );

		$wp_the_query = $original; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restored above.
	}

	/**
	 * The grid payload is real, complete and safe to print in attributes.
	 */
	public function testArchivePayloadCarriesEveryFilterableValue(): void {
		$products = wc_get_products( array( 'limit' => 5, 'status' => 'publish' ) );
		$this->assertNotEmpty( $products );

		$required = array( 'category', 'categories', 'purchasable', 'price', 'ip', 'voltage', 'wattage', 'views', 'rating', 'search_text' );

		foreach ( $products as $product ) {
			$payload = ProductData::archive_payload( $product );

			foreach ( $required as $key ) {
				$this->assertArrayHasKey( $key, $payload, get_class( $product ) . ' payload lacks ' . $key );
			}

			$this->assertContains( $payload['purchasable'], array( 'true', 'false' ), 'The script compares this to the string true.' );
			$this->assertMatchesRegularExpression( '/^\d+$/', $payload['price'], 'Price is filtered numerically in the browser.' );
			$this->assertMatchesRegularExpression( '/^\d+(\.\d+)?$/', $payload['rating'] );

			// The search text must actually contain the name, or search silently fails.
			$this->assertStringContainsString( $product->get_name(), $payload['search_text'] );
			if ( '' !== (string) $product->get_sku() ) {
				$this->assertStringContainsString( $product->get_sku(), $payload['search_text'], 'SKU search must work.' );
			}
		}
	}

	/**
	 * The payload agrees with the product's own data.
	 */
	public function testArchivePayloadMatchesProductState(): void {
		$product = wc_get_product( (int) get_page_by_path( 'industrial-led-bulb', OBJECT, 'product' )->ID );
		$payload = ProductData::archive_payload( $product );

		$this->assertSame( 'false', $payload['purchasable'], 'An out-of-stock product is not directly purchasable.' );
		$this->assertSame( ProductData::attribute_text( $product, 'ip-rating' ), $payload['ip'] );
		$this->assertSame( (string) (int) $product->get_price(), $payload['price'] );
	}

	/**
	 * The grid renders both card and payload, and never fakes a product.
	 */
	public function testGridRendersItemsWithPayload(): void {
		$products = array_slice( wc_get_products( array( 'limit' => 3, 'status' => 'publish' ) ), 0, 3 );

		$html = $this->render(
			'template-parts/archive/part-shop-grid.php',
			array(
				'erfansanat_products'    => $products,
				'erfansanat_total'       => 20,
				'erfansanat_page'        => 1,
				'erfansanat_per_page'    => 9,
				'erfansanat_heading'     => 'همه محصولات',
				'erfansanat_shop_url'    => 'https://example.test/shop/',
				'erfansanat_base_url'    => 'https://example.test/shop/',
				'erfansanat_empty'       => false,
			)
		);

		$this->assertSame( 3, substr_count( $html, 'data-product-item="true"' ) );
		$this->assertSame( 3, substr_count( $html, 'data-product-search-text=' ) );
		$this->assertStringContainsString( 'data-product-grid="true"', $html );
		$this->assertStringContainsString( 'data-pagination-root="true"', $html, 'The script looks the pager up inside the archive root.' );
		$this->assertStringContainsString( 'data-pagination-server="true"', $html, 'Server paging must be declared so in-page paging cannot fight it.' );
		$this->assertStringContainsString( 'data-per-page="9"', $html );

		// Real pager navigation, not dead buttons.
		$this->assertStringNotContainsString( 'data-page-btn', $html, 'The theme script reads data-page-num, matching the reference.' );
		$this->assertStringContainsString( 'data-page-next="true"', $html );
	}

	/**
	 * An archive with no results shows the empty state and no pager.
	 */
	public function testEmptyArchiveRendersEmptyState(): void {
		$html = $this->render(
			'template-parts/archive/part-shop-grid.php',
			array(
				'erfansanat_products' => array(),
				'erfansanat_total'    => 0,
				'erfansanat_page'     => 1,
				'erfansanat_per_page' => 9,
				'erfansanat_heading'  => 'دسته خالی',
				'erfansanat_shop_url' => 'https://example.test/shop/',
				'erfansanat_empty'    => true,
			)
		);

		$this->assertStringContainsString( 'data-product-empty="true"', $html );
		$this->assertStringNotContainsString( 'data-product-grid', $html, 'No grid is printed for an empty result.' );
		$this->assertStringNotContainsString( 'data-pagination-root', $html );
		$this->assertStringContainsString( 'https://example.test/shop/', $html, 'The empty state links back to the shop.' );
	}

	/**
	 * Category FAQ rows come from the term's historical meta.
	 */
	public function testTermFaqReadsHistoricalTermMeta(): void {
		$term = get_term_by( 'slug', 'wall-washer', 'product_cat' );
		$this->assertInstanceOf( \WP_Term::class, $term );

		$faq = ProductData::term_faq( $term );
		$this->assertNotEmpty( $faq, 'The fixture seeds a term FAQ.' );

		foreach ( $faq as $row ) {
			$this->assertArrayHasKey( 'question', $row );
			$this->assertArrayHasKey( 'answer', $row );
			$this->assertNotSame( '', trim( $row['question'] ) );
			$this->assertNotSame( '', trim( $row['answer'] ) );
		}

		// A term without the meta yields an empty list, never a broken row.
		$bare = wp_insert_term( 'es-bare-' . wp_rand( 1000, 9999 ), 'product_cat' );
		$this->assertIsArray( $bare );
		$bare_term = get_term( (int) $bare['term_id'], 'product_cat' );
		$this->assertSame( array(), ProductData::term_faq( $bare_term ) );
		wp_delete_term( (int) $bare['term_id'], 'product_cat' );
	}

	/**
	 * A category thumbnail comes from the term's media id, empty when unset.
	 */
	public function testTermImageReadsThumbnailMeta(): void {
		$term = get_term_by( 'slug', 'wall-washer', 'product_cat' );
		$this->assertInstanceOf( \WP_Term::class, $term );

		$image = ProductData::term_image( $term );
		$this->assertNotSame( '', $image );
		$this->assertStringStartsWith( 'http', $image );

		$bare = wp_insert_term( 'es-nothumb-' . wp_rand( 1000, 9999 ), 'product_cat' );
		$this->assertIsArray( $bare );
		$this->assertSame( '', ProductData::term_image( get_term( (int) $bare['term_id'], 'product_cat' ) ) );
		wp_delete_term( (int) $bare['term_id'], 'product_cat' );
	}

	/**
	 * Nothing in the archive fakes data or a cart.
	 */
	public function testArchiveTemplatesDoNotFakeAnything(): void {
		$files = (array) glob( get_template_directory() . '/template-parts/archive/*.php' );
		$files[] = get_template_directory() . '/archive-product.php';
		$files[] = get_template_directory() . '/taxonomy-product_cat.php';
		$files[] = get_template_directory() . '/taxonomy-product_tag.php';

		foreach ( $files as $file ) {
			$code = (string) file_get_contents( $file );
			$this->assertStringNotContainsString( 'localStorage', $code, basename( $file ) );
			$this->assertStringNotContainsString( 'Lorem', $code, basename( $file ) );
			$this->assertStringNotContainsString( 'data-page-btn', $code, basename( $file ) . ' must use the reference attribute names.' );
		}
	}

	/**
	 * The filter options are derived from the catalogue, not hard-coded.
	 */
	public function testFilterOptionsComeFromTheCatalogue(): void {
		$this->assertNotSame( 0, wp_count_posts( 'product' )->publish );

		$ips = array();
		foreach ( wc_get_products( array( 'limit' => -1, 'status' => 'publish' ) ) as $product ) {
			$value = ProductData::attribute_text( $product, 'ip-rating' );
			if ( '' !== $value ) {
				$ips[ $value ] = true;
			}
		}
		$this->assertNotEmpty( $ips, 'The fixture products carry IP ratings.' );

		$html = $this->render(
			'template-parts/archive/part-shop-filters.php',
			array(
				'erfansanat_category_tree' => array(),
				'erfansanat_total'         => 20,
				'erfansanat_current'       => null,
				'erfansanat_shop_url'      => 'https://example.test/shop/',
				'erfansanat_ips'           => array_keys( $ips ),
				'erfansanat_voltages'      => array( '12V', '220V' ),
				'erfansanat_drawer'        => false,
			)
		);

		foreach ( array_keys( $ips ) as $ip ) {
			$this->assertStringContainsString( 'value="' . $ip . '"', $html, 'IP ' . $ip . ' must be selectable.' );
		}
		$this->assertStringContainsString( 'data-product-ip-filter="true"', $html );
		$this->assertStringContainsString( 'data-product-voltage-filter="true"', $html );
		$this->assertStringContainsString( 'data-product-purchase-mode="all"', $html );
		$this->assertStringContainsString( 'data-product-price-range="true"', $html );
		$this->assertStringContainsString( 'data-product-search="true"', $html );
	}

	/**
	 * Phone numbers on the archive are real links.
	 */
	public function testArchiveUsesTheRealPhone(): void {
		$html = $this->render(
			'template-parts/archive/part-shop-filters.php',
			array(
				'erfansanat_category_tree' => array(),
				'erfansanat_total'         => 20,
				'erfansanat_current'       => null,
				'erfansanat_shop_url'      => 'https://example.test/shop/',
				'erfansanat_ips'           => array(),
				'erfansanat_voltages'      => array(),
				'erfansanat_drawer'        => false,
			)
		);
		$this->assertStringContainsString( SectionParts::phone_href(), $html );
	}

	/**
	 * The hero escapes its input: a hostile title cannot break the page.
	 */
	public function testHeroEscapesItsInput(): void {
		$html = $this->render(
			'template-parts/archive/part-shop-hero.php',
			array(
				'erfansanat_crumbs'    => array( array( 'label' => '<script>x</script>', 'url' => '' ) ),
				'erfansanat_eyebrow'   => '<img src=x onerror=alert(1)>',
				'erfansanat_title'     => 'ok',
				'erfansanat_highlight' => 'ok',
				'erfansanat_lead'      => '<b>bold</b>',
				'erfansanat_badges'    => array(),
				'erfansanat_image'     => '',
				'erfansanat_image_alt' => '',
				'erfansanat_count'     => 0,
			)
		);

		$this->assertStringNotContainsString( '<script>x</script>', $html );
		$this->assertStringNotContainsString( '<img src=x', $html );
		$this->assertStringNotContainsString( '<b>bold</b>', $html );
		$this->assertStringNotContainsString( 'src=""', $html );
	}
}
