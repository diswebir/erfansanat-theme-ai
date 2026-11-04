<?php
declare(strict_types=1);
/**
 * Task 6.1 acceptance: the single product page.
 *
 * The page must be driven by real WooCommerce data and the exact historical
 * meta keys, must never fake a purchase, and must degrade to empty markup when
 * a field is absent rather than printing a hole in the layout.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Data\LegacyMeta;
use ErfanSanat\Theme\Data\ProductData;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the product page data contract and its rendering rules.
 */
final class TaskP06T01Test extends TestCase {

	/**
	 * Product id by slug.
	 *
	 * @param string $slug Product slug.
	 * @return int
	 */
	private function product_id( string $slug ): int {
		$page = get_page_by_path( $slug, OBJECT, 'product' );
		$this->assertInstanceOf( \WP_Post::class, $page, 'Fixture product ' . $slug . ' must exist.' );
		return (int) $page->ID;
	}

	/**
	 * Render one product template part in isolation.
	 *
	 * @param string               $part    File name inside template-parts/product.
	 * @param array<string, mixed> $context Variables the part reads.
	 * @return string
	 */
	private function render_part( string $part, array $context ): string {
		$file = get_template_directory() . '/template-parts/product/' . $part;
		$this->assertFileExists( $file );

		ob_start();
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Test harness provides the exact variables.
		extract( $context, EXTR_SKIP );
		include $file;
		return (string) ob_get_clean();
	}

	/**
	 * A simple product is fetched as a real WC_Product from the fixture.
	 *
	 * @return \WC_Product
	 */
	private function simple_product(): \WC_Product {
		$product = wc_get_product( $this->product_id( 'illumina' ) );
		$this->assertInstanceOf( \WC_Product::class, $product );
		return $product;
	}

	/**
	 * The suite only makes sense with WooCommerce active.
	 */
	public function testWooCommerceIsActive(): void {
		$this->assertTrue( function_exists( 'wc_get_product' ), 'WooCommerce must be active for the product page.' );
		$this->assertFileExists( get_template_directory() . '/single-product.php' );
	}

	/**
	 * Spec rows come from WooCommerce attributes, label first, value second.
	 */
	public function testSpecRowsAreRealAttributes(): void {
		$product = $this->simple_product();
		$rows    = ProductData::spec_rows( $product );

		$this->assertNotEmpty( $rows, 'The fixture product has visible attributes.' );

		foreach ( $rows as $row ) {
			$this->assertArrayHasKey( 'label', $row );
			$this->assertArrayHasKey( 'value', $row );
			$this->assertNotSame( '', trim( $row['value'] ), 'An empty attribute must not become a blank row.' );
		}

		$labels = array_column( $rows, 'label' );
		$this->assertContains( 'Ip rating', $labels, 'The protective rating attribute is a visible attribute.' );
	}

	/**
	 * The six official identifiers keep their historical keys, and only the
	 * populated ones are returned.
	 */
	public function testIdentifiersUseLegacyKeys(): void {
		$id = $this->product_id( 'illumina' );

		$this->assertSame( 'ES-7', LegacyMeta::text( $id, 'product_id' ) );
		$this->assertNotSame( '', LegacyMeta::text( $id, 'gs1_id' ) );

		$identifiers = ProductData::identifiers( $id );
		$this->assertArrayHasKey( 'product_id', $identifiers );
		$this->assertArrayHasKey( 'gs1_id', $identifiers );
		$this->assertNotSame( '', $identifiers['gs1_id']['value'] );

		// An identifier the product does not carry must not appear as a blank tile.
		$empty = ProductData::identifiers( $this->product_id( 'baloti-string' ) );
		foreach ( $empty as $key => $row ) {
			$this->assertNotSame( '', trim( $row['value'] ), $key . ' must not be rendered when empty.' );
		}
	}

	/**
	 * Repeater keys are read with their historical spelling.
	 */
	public function testRepeatersKeepHistoricalKeys(): void {
		$id = $this->product_id( 'illumina' );

		$this->assertNotEmpty( ProductData::features( $id ), 'product-feature repeater rows.' );
		$this->assertNotEmpty( ProductData::usages( $id ), 'usage_product repeater rows.' );
		$this->assertNotEmpty( ProductData::faq( $id ), 'faq repeater rows.' );

		$highlights = ProductData::highlights( $id );
		$this->assertNotEmpty( $highlights );
		$this->assertArrayHasKey( 'title', $highlights[0] );
		$this->assertArrayHasKey( 'value', $highlights[0], 'vaule_of_variable is read into the value slot.' );
	}

	/**
	 * The gallery merges the featured image with the legacy gallery keys.
	 */
	public function testGalleryMergesFeaturedImageAndLegacyGallery(): void {
		$product = $this->simple_product();
		$ids     = ProductData::gallery_ids( $product );

		$this->assertNotEmpty( $ids );
		$this->assertSame( (int) $product->get_image_id(), $ids[0], 'The featured image is always first.' );
		$this->assertSame( array_values( array_unique( $ids ) ), $ids, 'No image id may repeat.' );

		foreach ( $ids as $id ) {
			$this->assertSame( 'attachment', get_post_type( $id ) );
		}
	}

	/**
	 * `true_sale = false` must never print an add-to-cart form.
	 */
	public function testInquiryProductHasNoAddToCartForm(): void {
		$product = wc_get_product( $this->product_id( 'foroogh-chandelier' ) );
		$this->assertFalse( LegacyMeta::is_direct_sale( $product->get_id() ), 'Fixture is an inquiry product.' );

		$html = $this->render_part( 'part-summary.php', array( 'erfansanat_product' => $product ) );

		$this->assertStringNotContainsString( 'name="add-to-cart"', $html, 'An inquiry product must not fake a purchase.' );
		$this->assertStringNotContainsString( 'single_add_to_cart_button', $html );
		$this->assertStringContainsString( 'tel:', $html, 'The quote path must offer the real phone number.' );
	}

	/**
	 * A purchasable product uses WooCommerce's own form.
	 */
	public function testPurchasableProductUsesWooCommerceForm(): void {
		$product = $this->simple_product();
		$this->assertTrue( ProductData::is_direct_sale( $product ) );

		$html = $this->render_part( 'part-summary.php', array( 'erfansanat_product' => $product ) );

		$this->assertStringContainsString( 'name="add-to-cart"', $html, 'WooCommerce owns the purchase form.' );
		$this->assertStringContainsString( 'woocommerce-Price-amount', $html, 'The price is real WooCommerce HTML.' );
	}

	/**
	 * An out-of-stock product says so instead of offering a purchase.
	 */
	public function testOutOfStockProductIsReportedAsSuch(): void {
		$product = wc_get_product( $this->product_id( 'industrial-led-bulb' ) );
		$this->assertFalse( $product->is_in_stock() );

		$html = $this->render_part( 'part-summary.php', array( 'erfansanat_product' => $product ) );

		$this->assertStringNotContainsString( 'name="add-to-cart"', $html );
		$this->assertStringContainsString( 'out-of-stock', $html, 'WooCommerce stock markup is shown.' );
	}

	/**
	 * A product with no image still renders the gallery frame.
	 */
	public function testGalleryWithNoImagesStillRenders(): void {
		$product = $this->simple_product();
		$html    = $this->render_part(
			'part-gallery.php',
			array(
				'erfansanat_gallery_ids' => array(),
				'erfansanat_title'       => 'بدون تصویر',
				'erfansanat_badge'       => '',
				'erfansanat_ip'          => '',
			)
		);

		$this->assertStringContainsString( 'data-gallery-root="true"', $html );
		$this->assertStringNotContainsString( 'src=""', $html );
		$this->assertStringNotContainsString( 'data-gallery-thumb', $html, 'No thumbnails without images.' );
		$product->get_id();
	}

	/**
	 * Empty metadata produces no stray section.
	 */
	public function testEmptyFieldsProduceNoEmptySections(): void {
		$this->assertSame( '', $this->render_part( 'part-faq.php', array( 'erfansanat_faq' => array() ) ) );
		$this->assertSame( '', $this->render_part( 'part-related-projects.php', array( 'erfansanat_related_projects' => array() ) ) );
	}

	/**
	 * Reviews are read from real approved comments, with their rating meta.
	 *
	 * The fixture seeds approved reviews on the four oldest products only, so
	 * this asserts against one of those instead of pretending every product has
	 * a review.
	 */
	public function testReviewsComeFromApprovedComments(): void {
		$product = wc_get_product( $this->product_id( 'baloti-string' ) );
		$reviews = ProductData::reviews( $product->get_id() );

		$this->assertNotEmpty( $reviews, 'The fixture seeds approved reviews.' );
		foreach ( $reviews as $review ) {
			$this->assertGreaterThanOrEqual( 1, $review['rating'] );
			$this->assertLessThanOrEqual( 5, $review['rating'] );
			$this->assertNotSame( '', $review['author'] );
		}

		// An unapproved comment must never reach the page.
		$pending = wp_insert_comment(
			array(
				'comment_post_ID'      => $product->get_id(),
				'comment_type'         => 'review',
				'comment_author'       => 'در انتظار تأیید',
				'comment_author_email' => 'pending@example.invalid',
				'comment_content'      => 'این دیدگاه نباید نمایش داده شود.',
				'comment_approved'     => 0,
			)
		);
		$this->assertIsInt( $pending );

		$authors = array_column( ProductData::reviews( $product->get_id() ), 'author' );
		$this->assertNotContains( 'در انتظار تأیید', $authors );

		require_once ABSPATH . 'wp-admin/includes/comment.php';
		wp_delete_comment( (int) $pending, true );
	}

	/**
	 * The related project rail reads the real relation and primes its caches.
	 */
	public function testRelatedProjectsComeFromTheRelation(): void {
		$id = $this->product_id( 'illumina' );
		$ids = LegacyMeta::read_ids( $id, 'project-ids' );

		if ( array() === $ids ) {
			$this->markTestSkipped( 'This fixture product has no project relation.' );
		}

		$projects = ProductData::related_projects( $id, 3 );
		$this->assertNotEmpty( $projects );

		foreach ( $projects as $project ) {
			$this->assertInstanceOf( \WP_Post::class, $project );
			$this->assertSame( 'projects', $project->post_type );
			$this->assertSame( 'publish', $project->post_status );
			$this->assertContains( (int) $project->ID, $ids, 'Only related ids are rendered.' );
		}
	}

	/**
	 * Rendering the project rail costs no per-card queries.
	 */
	public function testRelatedProjectsPrimeTheirCaches(): void {
		$id  = $this->product_id( 'illumina' );
		$ids = LegacyMeta::read_ids( $id, 'project-ids' );
		if ( array() === $ids ) {
			$this->markTestSkipped( 'This fixture product has no project relation.' );
		}

		$projects = ProductData::related_projects( $id, 3 );

		global $wpdb;
		$before = (int) $wpdb->num_queries;
		ob_start();
		$erfansanat_related_projects = $projects;
		$erfansanat_product_name     = 'محصول آزمایشی';
		include get_template_directory() . '/template-parts/product/part-related-projects.php';
		ob_end_clean();
		$queries = (int) $wpdb->num_queries - $before;

		$this->assertSame( 0, $queries, 'Primed project cards must not query again.' );
	}

	/**
	 * The tabs part renders three panels with matching buttons and the real
	 * review form posts to WordPress rather than faking success.
	 */
	public function testTabsRenderRealPanels(): void {
		$product = $this->simple_product();
		$html    = $this->render_part( 'part-tabs.php', array( 'erfansanat_product' => $product ) );

		$this->assertSame( 3, substr_count( $html, 'data-prod-tab-btn=' ) );
		$this->assertSame( 3, substr_count( $html, 'data-prod-tab-panel=' ) );

		foreach ( array( 'desc', 'specs', 'reviews' ) as $tab ) {
			$this->assertStringContainsString( 'data-prod-tab-btn="' . $tab . '"', $html );
			$this->assertStringContainsString( 'data-prod-tab-panel="' . $tab . '"', $html );
		}

		$this->assertStringContainsString( 'id="product-details"', $html );
		$this->assertStringContainsString( 'comment-form', $html, 'The real comment form is printed.' );
		$this->assertStringContainsString( 'name="rating"', $html, 'WooCommerce reads the rating field by this name.' );

		// A fake success message must not exist in the markup.
		$this->assertStringNotContainsString( 'data-review-success', $html );
	}

	/**
	 * The tab part escapes a hostile product name.
	 */
	public function testTabsEscapeHostileProductName(): void {
		$product = $this->simple_product();

		$filter = static function () {
			return 'Hostile <script>alert(1)</script> name';
		};
		add_filter( 'woocommerce_product_get_name', $filter );

		$html = $this->render_part( 'part-tabs.php', array( 'erfansanat_product' => $product ) );

		remove_filter( 'woocommerce_product_get_name', $filter );

		$this->assertStringNotContainsString( '<script>alert(1)</script>', $html );
		$this->assertStringContainsString( '&lt;script&gt;', $html );
	}

	/**
	 * A variable product renders WooCommerce's variation form, not a simple one.
	 */
	public function testVariableProductUsesVariationForm(): void {
		$product = wc_get_product( $this->product_id( 'artam-light-tree' ) );
		$this->assertInstanceOf( \WC_Product_Variable::class, $product );

		$html = $this->render_part( 'part-summary.php', array( 'erfansanat_product' => $product ) );

		$this->assertStringContainsString( 'variations_form', $html );
		$this->assertStringContainsString( 'variation_id', $html );
		$this->assertStringContainsString( 'data-product_variations', $html );
	}

	/**
	 * The product page template never re-implements cart logic.
	 */
	public function testTemplateDoesNotFakeTheCart(): void {
		$files = array( 'single-product.php' );
		foreach ( (array) glob( get_template_directory() . '/template-parts/product/*.php' ) as $file ) {
			$files[] = $file;
		}

		foreach ( $files as $file ) {
			$path = str_starts_with( $file, '/' ) ? $file : get_template_directory() . '/' . $file;
			$code = (string) file_get_contents( $path );
			$this->assertStringNotContainsString( 'localStorage', $code, basename( $path ) . ' must not fake a cart.' );
			$this->assertStringNotContainsString( 'رسید پرداخت', $code, basename( $path ) . ' must not print a fake receipt.' );
		}
	}
}
