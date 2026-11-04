<?php
declare(strict_types=1);
/**
 * Task 5.2 acceptance: real queries, shared cards, escaped output, no N+1.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Frontend\SectionParts;
use ErfanSanat\Theme\Queries;
use ErfanSanat\Theme\Sections;
use PHPUnit\Framework\TestCase;

/**
 * Verifies that every home-page list is backed by real data.
 */
final class TaskP05T02Test extends TestCase {

	/**
	 * Render one section by key with its saved fields.
	 *
	 * @param string $key Section key.
	 * @return string
	 */
	private function render_section( string $key ): string {
		$html = '';
		foreach ( Sections::home_layout() as $row ) {
			if ( $key !== $row['key'] ) {
				continue;
			}
			ob_start();
			$erfansanat_section_fields = $row['fields'];
			$registry                  = Sections::registry();
			include get_template_directory() . '/' . $registry[ $key ]['part'];
			$html = (string) ob_get_clean();
		}
		return $html;
	}

	/**
	 * Products come from WooCommerce, with real prices.
	 */
	public function testProductsAreRealWooCommerceObjects(): void {
		if ( ! function_exists( 'wc_get_products' ) ) {
			$this->markTestSkipped( 'WooCommerce is not active.' );
		}

		$products = Queries::products( 3 );
		$this->assertNotEmpty( $products, 'The fixture has published, in-stock products.' );
		$this->assertLessThanOrEqual( 3, count( $products ) );

		foreach ( $products as $product ) {
			$this->assertInstanceOf( \WC_Product::class, $product );
			$this->assertTrue( $product->is_visible(), 'Only visible products are listed.' );
		}
	}

	/**
	 * The product card prints the real price, not a sample number.
	 */
	public function testProductCardPrintsTheRealPrice(): void {
		if ( ! function_exists( 'wc_get_products' ) ) {
			$this->markTestSkipped( 'WooCommerce is not active.' );
		}

		$products = Queries::products( 2 );
		if ( array() === $products ) {
			$this->markTestSkipped( 'No products in the fixture.' );
		}

		$product = $products[0];
		ob_start();
		$erfansanat_product = $product;
		$erfansanat_size    = 'card';
		include get_template_directory() . '/template-parts/cards/card-product.php';
		$html = (string) ob_get_clean();

		$this->assertStringContainsString( esc_html( $product->get_name() ), $html );
		$this->assertStringContainsString( (string) get_permalink( $product->get_id() ), $html );

		// The formatted price must appear; an empty price is only acceptable
		// for a genuinely priceless product.
		$price_html = $product->get_price_html();
		if ( '' !== (string) $price_html ) {
			$this->assertStringContainsString( 'woocommerce-Price-amount', $html, 'The real formatted price must render.' );
		}

		// No hard-coded sample amount can leak in.
		$this->assertStringNotContainsString( '۱۲,۵۰۰,۰۰۰', $html );
		$this->assertStringNotContainsString( 'localStorage', $html );
	}

	/**
	 * The product card survives a product with no thumbnail and no terms.
	 */
	public function testProductCardHandlesMissingImage(): void {
		if ( ! function_exists( 'wc_get_products' ) ) {
			$this->markTestSkipped( 'WooCommerce is not active.' );
		}

		$id = wp_insert_post(
			array(
				'post_type'   => 'product',
				'post_title'  => 'P05 product without image',
				'post_status' => 'publish',
			)
		);
		if ( is_wp_error( $id ) ) {
			$this->markTestSkipped( 'Could not create a product in this environment.' );
		}

		$product = wc_get_product( $id );
		if ( ! $product instanceof \WC_Product ) {
			wp_delete_post( $id, true );
			$this->markTestSkipped( 'WooCommerce did not load the product.' );
		}

		ob_start();
		$erfansanat_product = $product;
		$erfansanat_size    = 'card';
		include get_template_directory() . '/template-parts/cards/card-product.php';
		$html = (string) ob_get_clean();

		$this->assertStringContainsString( esc_html( $product->get_name() ), $html );
		$this->assertStringNotContainsString( '<img', $html, 'No image element without a thumbnail.' );
		$this->assertStringNotContainsString( 'Warning', $html );

		wp_delete_post( $id, true );
	}

	/**
	 * Projects are publish-only and carry their real city/type terms.
	 */
	public function testProjectsArePublishedWithRealTerms(): void {
		$projects = Queries::projects( 5 );
		$this->assertNotEmpty( $projects, 'The fixture has published projects.' );

		foreach ( $projects as $project ) {
			$this->assertSame( 'publish', $project->post_status );
			$this->assertSame( 'projects', $project->post_type );
		}

		// A draft project must never appear in the gallery.
		$draft = wp_insert_post(
			array(
				'post_type'   => 'projects',
				'post_title'  => 'P05 draft project',
				'post_status' => 'draft',
			)
		);
		if ( ! is_wp_error( $draft ) ) {
			$ids = wp_list_pluck( Queries::projects( 30 ), 'ID' );
			$this->assertNotContains( $draft, array_map( 'intval', $ids ), 'Drafts must not leak into the gallery.' );
			wp_delete_post( $draft, true );
		}
	}

	/**
	 * Project filters by type and city slug only return matching posts.
	 */
	public function testProjectFiltersMatchTheirTaxonomy(): void {
		$terms = get_terms( array( 'taxonomy' => 'project_type', 'hide_empty' => true, 'number' => 1 ) );
		if ( ! is_array( $terms ) || array() === $terms ) {
			$this->markTestSkipped( 'No project types in the fixture.' );
		}

		$slug     = (string) $terms[0]->slug;
		$filtered = Queries::projects( 10, $slug );
		$this->assertNotEmpty( $filtered, 'A non-empty type must return its projects.' );

		foreach ( $filtered as $project ) {
			$this->assertTrue(
				has_term( $slug, 'project_type', $project->ID ),
				$project->post_title . ' is not in the requested type.'
			);
		}
	}

	/**
	 * The project card prints the real city label and permalink.
	 */
	public function testProjectCardUsesTheRealCity(): void {
		$projects = Queries::projects( 3 );
		if ( array() === $projects ) {
			$this->markTestSkipped( 'No projects in the fixture.' );
		}

		$project = $projects[0];
		ob_start();
		$erfansanat_project = $project;
		include get_template_directory() . '/template-parts/cards/card-project.php';
		$html = (string) ob_get_clean();

		$this->assertStringContainsString( esc_html( get_the_title( $project ) ), $html );
		$this->assertStringContainsString( esc_url( (string) get_permalink( $project ) ), $html );

		$cities = get_the_terms( $project->ID, 'city' );
		if ( is_array( $cities ) && array() !== $cities ) {
			$this->assertStringContainsString( esc_html( (string) $cities[0]->name ), $html );
		}
	}

	/**
	 * Posts are publish-only, newest first, with a real reading time.
	 */
	public function testPostsArePublishOnly(): void {
		$posts = Queries::posts( 4 );
		$this->assertNotEmpty( $posts );

		$previous = null;
		foreach ( $posts as $post ) {
			$this->assertSame( 'publish', $post->post_status );
			if ( null !== $previous ) {
				$this->assertLessThanOrEqual(
					strtotime( $previous ),
					strtotime( $post->post_date ),
					'Posts must be newest first.'
				);
			}
			$previous = $post->post_date;
		}
	}

	/**
	 * Cards are shared, not duplicated per section.
	 */
	public function testCardsAreSharedParts(): void {
		foreach ( array( 'card-product.php', 'card-project.php', 'card-post.php' ) as $card ) {
			$this->assertFileExists( get_template_directory() . '/template-parts/cards/' . $card );
		}

		foreach ( array( 'section-products.php', 'section-projects.php', 'section-blog.php' ) as $section ) {
			$code = (string) file_get_contents( get_template_directory() . '/template-parts/home/' . $section );
			$this->assertMatchesRegularExpression(
				'/template-parts\/cards\/card-[a-z]+\\.php/',
				$code,
				$section . ' must include a shared card part.'
			);
		}
	}

	/**
	 * Card output is escaped: a hostile title never becomes markup.
	 */
	public function testCardsEscapeHostileTitles(): void {
		// Core strips `unfiltered_html` *before* storage, so a stored title can
		// never carry markup. The escaping path is therefore proven where it
		// actually runs: a filtered `the_title` that returns markup.
		$post_id = wp_insert_post(
			array(
				'post_type'    => 'post',
				'post_title'   => 'P05 hostile title',
				'post_status'  => 'publish',
				'post_content' => 'Body text for the reading time.',
			)
		);
		if ( is_wp_error( $post_id ) ) {
			$this->markTestSkipped( 'Could not create a post.' );
		}

		$inject = static function ( $title, $id = 0 ) use ( $post_id ) {
			return (int) $id === (int) $post_id ? 'P05 <script>alert(1)</script> title' : $title;
		};
		add_filter( 'the_title', $inject, 10, 2 );

		ob_start();
		$erfansanat_post = get_post( $post_id );
		include get_template_directory() . '/template-parts/cards/card-post.php';
		$html = (string) ob_get_clean();
		remove_filter( 'the_title', $inject, 10 );

		$this->assertStringNotContainsString( '<script>alert(1)</script>', $html );
		$this->assertStringContainsString( '&lt;script&gt;', $html );

		wp_delete_post( $post_id, true );
	}

	/**
	 * A list of 12 cards must not cost 12 extra queries (N+1 guard).
	 */
	public function testCardListsDoNotCauseNPlusOne(): void {
		// Warm every cache once so the measured calls below report only what the
		// list itself costs beyond already-cached meta and terms.
		$large = Queries::measure( static fn() => Queries::projects( 12 ) );
		$small = Queries::measure( static fn() => Queries::projects( 3 ) );

		// Rendering 12 project cards must cost no per-card queries at all: the
		// thumbnail and term caches are primed once for the whole list.
		$render = static function (): int {
			$count = 0;
			foreach ( Queries::projects( 12 ) as $erfansanat_project ) {
				ob_start();
				include get_template_directory() . '/template-parts/cards/card-project.php';
				ob_end_clean();
				++$count;
			}
			return $count;
		};

		$measured = Queries::measure( $render );
		$this->assertSame( 12, (int) $measured['result'] );
		$this->assertSame(
			0,
			(int) $measured['queries'],
			'Twelve cards must add zero queries: image and terms are primed for the whole list.'
		);
	}

	/**
	 * Rendering the whole front page stays inside a query budget.
	 */
	public function testFrontPageQueryBudget(): void {
		$measured = Queries::measure(
			static function (): string {
				ob_start();
				Sections::render( Sections::home_layout() );
				return (string) ob_get_clean();
			}
		);

		$html = (string) $measured['result'];
		$this->assertStringContainsString( 'data-section="products"', $html );
		$this->assertLessThan( 60, (int) $measured['queries'], 'The home page must not query per card.' );
	}

	/**
	 * With no data at all every section fails closed instead of fatalling.
	 */
	public function testEmptySiteRendersWithoutErrors(): void {
		$product_ids = get_posts( array( 'post_type' => 'product', 'numberposts' => -1, 'fields' => 'ids' ) );
		$project_ids = get_posts( array( 'post_type' => 'projects', 'numberposts' => -1, 'fields' => 'ids' ) );
		$post_ids    = get_posts( array( 'post_type' => 'post', 'numberposts' => -1, 'fields' => 'ids' ) );

		foreach ( array_merge( (array) $product_ids, (array) $project_ids, (array) $post_ids ) as $id ) {
			wp_update_post(
				array(
					'ID'          => $id,
					'post_status' => 'draft',
				)
			);
		}

		$html = $this->render_section( 'products' ) . $this->render_section( 'projects' ) . $this->render_section( 'blog' );

		foreach ( (array) $product_ids as $id ) {
			wp_update_post(
				array(
					'ID'          => $id,
					'post_status' => 'publish',
				)
			);
		}
		foreach ( (array) $project_ids as $id ) {
			wp_update_post(
				array(
					'ID'          => $id,
					'post_status' => 'publish',
				)
			);
		}
		foreach ( (array) $post_ids as $id ) {
			wp_update_post(
				array(
					'ID'          => $id,
					'post_status' => 'publish',
				)
			);
		}

		$this->assertStringNotContainsString( 'Fatal error', $html );
		$this->assertStringNotContainsString( 'Warning:', $html );
	}

	/**
	 * Every image the sections print resolves to a file that exists.
	 */
	public function testEverySectionImageResolves(): void {
		foreach ( array( 'images/hero.jpg', 'images/light-tunnel.jpg', 'images/light-tree.jpg', 'images/light-sphere.jpg', 'images/sun-light.jpg', 'images/light-element.jpg', 'images/factory.jpg', 'images/string-lights.jpg' ) as $asset ) {
			$this->assertNotSame( '', SectionParts::asset( $asset ), $asset . ' is missing from the theme.' );
		}

		// An escaping or missing path degrades to an empty string, never a URL.
		$this->assertSame( '', SectionParts::asset( '../wp-config.php' ) );
		$this->assertSame( '', SectionParts::asset( 'images/does-not-exist.jpg' ) );

		$this->assertNotSame( '', SectionParts::hero_video() );
	}

	/**
	 * Services keep the editor's menu order.
	 */
	public function testServicesRespectMenuOrder(): void {
		$services = Queries::services( 6 );
		$this->assertNotEmpty( $services );

		$previous = -1;
		foreach ( $services as $service ) {
			$this->assertSame( 'services', $service->post_type );
			$this->assertGreaterThanOrEqual( $previous, (int) $service->menu_order );
			$previous = (int) $service->menu_order;
		}
	}
}
