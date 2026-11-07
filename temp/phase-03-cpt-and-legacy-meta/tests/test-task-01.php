<?php
declare(strict_types=1);
/**
 * Task 3.1 acceptance: native content types, taxonomies and permalinks.
 *
 * The contracts come from temp/DATA_BLUEPRINT.md: slugs, with_front, supports
 * and the "no 404" rule for archives, singles and term archives.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\ContentTypes;
use ErfanSanat\Theme\Rewrites;
use PHPUnit\Framework\TestCase;

/**
 * Verifies the registration contract and that the theme registers it alone.
 */
final class TaskP03T01Test extends TestCase {

	/**
	 * The theme, not a mu-plugin, must own these structures.
	 */
	public function testThemeRegistersTheStructures(): void {
		foreach ( array( 'projects', 'services' ) as $post_type ) {
			$this->assertTrue( post_type_exists( $post_type ), $post_type . ' is not registered.' );
		}
		foreach ( array( 'project_type', 'city', 'product_cat', 'product_tag' ) as $taxonomy ) {
			$this->assertTrue( taxonomy_exists( $taxonomy ), $taxonomy . ' is not registered.' );
		}

		// The environment-only mu-plugin that used to provide these types is gone,
		// so the theme must be the only source of truth.
		$mu = WP_CONTENT_DIR . '/mu-plugins';
		$leftovers = array();
		foreach ( glob( $mu . '/*.php' ) ?: array() as $file ) {
			if ( false !== strpos( (string) file_get_contents( $file ), "register_post_type('projects'" ) ) {
				$leftovers[] = basename( $file );
			}
		}
		$this->assertSame( array(), $leftovers, 'A mu-plugin still registers the fixture types.' );

		$code = (string) file_get_contents( get_template_directory() . '/inc/ContentTypes.php' );
		$this->assertStringContainsString( "register_post_type(", $code );
		$this->assertStringContainsString( "register_taxonomy(", $code );
	}

	/**
	 * projects matches the blueprint argument for argument.
	 */
	public function testProjectsPostTypeMatchesBlueprint(): void {
		$type = get_post_type_object( 'projects' );
		$this->assertNotNull( $type );
		$this->assertTrue( $type->public );
		$this->assertTrue( (bool) $type->has_archive );
		$this->assertFalse( $type->hierarchical );
		$this->assertTrue( $type->show_in_rest );
		$this->assertSame( 'dashicons-screenoptions', $type->menu_icon );
		$this->assertSame( 'projects', $type->rewrite['slug'] );
		$this->assertFalse( $type->rewrite['with_front'] );

		foreach ( array( 'title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions' ) as $feature ) {
			$this->assertTrue( post_type_supports( 'projects', $feature ), 'projects must support ' . $feature );
		}
		$this->assertContains( 'project_type', get_object_taxonomies( 'projects' ) );
		$this->assertContains( 'city', get_object_taxonomies( 'projects' ) );
	}

	/**
	 * services is independent and publicly viewable.
	 */
	public function testServicesPostTypeMatchesBlueprint(): void {
		$type = get_post_type_object( 'services' );
		$this->assertNotNull( $type );
		$this->assertTrue( $type->public );
		$this->assertTrue( (bool) $type->has_archive );
		$this->assertFalse( $type->hierarchical );
		$this->assertTrue( $type->show_in_rest );
		$this->assertSame( 'services', $type->rewrite['slug'] );
		$this->assertFalse( $type->rewrite['with_front'] );
		$this->assertTrue( post_type_supports( 'services', 'excerpt' ) );
	}

	/**
	 * Both project taxonomies are hierarchical, public and non-prefixed.
	 */
	public function testProjectTaxonomiesMatchBlueprint(): void {
		foreach ( array( 'project_type' => 'project_type', 'city' => 'city' ) as $taxonomy => $slug ) {
			$tax = get_taxonomy( $taxonomy );
			$this->assertNotFalse( $tax, $taxonomy . ' is missing.' );
			$this->assertTrue( $tax->hierarchical );
			$this->assertTrue( $tax->public );
			$this->assertTrue( $tax->show_in_rest );
			$this->assertTrue( $tax->show_admin_column );
			$this->assertSame( $slug, $tax->rewrite['slug'] );
			$this->assertFalse( $tax->rewrite['with_front'] );
			$this->assertFalse( $tax->rewrite['hierarchical'] );
			$this->assertSame( array( 'projects' ), $tax->object_type );
		}
	}

	/**
	 * The WooCommerce SEO contract is asserted, not left to an option.
	 */
	public function testWooCommercePermalinkContract(): void {
		$category = get_taxonomy( 'product_cat' );
		$this->assertNotFalse( $category );
		$this->assertTrue( $category->hierarchical );
		$this->assertSame( 'product_cat', $category->rewrite['slug'] );
		$this->assertTrue( $category->rewrite['with_front'], 'product_cat must keep with_front => true.' );
		$this->assertTrue( $category->rewrite['hierarchical'] );

		$tag = get_taxonomy( 'product_tag' );
		$this->assertNotFalse( $tag );
		$this->assertSame( 'product-tag', $tag->rewrite['slug'] );
		$this->assertFalse( $tag->rewrite['with_front'] );

		$product = get_post_type_object( 'product' );
		$this->assertNotNull( $product );
		$this->assertSame( 'product', $product->rewrite['slug'] );
		$this->assertFalse( $product->rewrite['with_front'] );
	}

	/**
	 * Archives and singles produce real links, not WP_Error.
	 */
	public function testArchivesAndSinglesResolve(): void {
		$this->assertIsString( get_post_type_archive_link( 'projects' ) );
		$this->assertIsString( get_post_type_archive_link( 'services' ) );
		$this->assertStringContainsString( '/projects/', (string) get_post_type_archive_link( 'projects' ) );
		$this->assertStringContainsString( '/services/', (string) get_post_type_archive_link( 'services' ) );

		$project = get_posts(
			array(
				'post_type'   => 'projects',
				'numberposts' => 1,
				'fields'      => 'ids',
			)
		);
		$this->assertNotEmpty( $project, 'Fixtures must contain a project.' );
		$this->assertStringContainsString( '/projects/', (string) get_permalink( (int) $project[0] ) );

		$service = get_posts(
			array(
				'post_type'   => 'services',
				'numberposts' => 1,
				'fields'      => 'ids',
			)
		);
		$this->assertNotEmpty( $service, 'Fixtures must contain a service.' );
		$this->assertStringContainsString( '/services/', (string) get_permalink( (int) $service[0] ) );
	}

	/**
	 * Term archives resolve for both project taxonomies.
	 */
	public function testTermArchivesResolve(): void {
		foreach ( array( 'city', 'project_type' ) as $taxonomy ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
					'number'     => 1,
				)
			);
			$this->assertIsArray( $terms );
			$this->assertNotEmpty( $terms, $taxonomy . ' fixtures are missing.' );
			$link = get_term_link( $terms[0]->term_id, $taxonomy );
			$this->assertIsString( $link, $taxonomy . ' term link returned WP_Error.' );
			$this->assertStringContainsString( '/' . $taxonomy . '/', $link );
			$this->assertSame( $link, Rewrites::term_link( (int) $terms[0]->term_id, $taxonomy ) );
		}
	}

	/**
	 * Rewrite rules exist for the critical paths and flushing is not per-request.
	 */
	public function testRewriteRulesAndFlushGuard(): void {
		Rewrites::maybe_flush();
		$rules = (array) get_option( 'rewrite_rules' );
		$this->assertNotEmpty( $rules );

		$patterns = implode( "\n", array_keys( $rules ) );
		$this->assertStringContainsString( 'projects', $patterns );
		$this->assertStringContainsString( 'project_type', $patterns );
		$this->assertStringContainsString( 'city', $patterns );
		$this->assertStringContainsString( 'product_cat', $patterns );

		$this->assertSame( Rewrites::VERSION, get_option( Rewrites::VERSION_OPTION ) );
		$before = get_option( 'rewrite_rules' );
		Rewrites::maybe_flush();
		$this->assertSame( $before, get_option( 'rewrite_rules' ), 'A second call must not flush again.' );

		Rewrites::schedule_flush();
		$this->assertFalse( get_option( Rewrites::VERSION_OPTION ) );
		Rewrites::maybe_flush();
		$this->assertSame( Rewrites::VERSION, get_option( Rewrites::VERSION_OPTION ) );
	}

	/**
	 * Aliases stay inert until an audit enables them.
	 */
	public function testLegacyAliasesAreNotActivated(): void {
		$aliases = Rewrites::candidate_aliases();
		$this->assertArrayHasKey( 'products', $aliases );
		$this->assertSame( 'shop', $aliases['products'] );

		// The theme must not register a redirect on its own.
		$code = (string) file_get_contents( get_template_directory() . '/inc/Rewrites.php' );
		$this->assertStringNotContainsString( 'wp_redirect', $code );
		$this->assertStringNotContainsString( 'template_redirect', $code );

		$this->assertSame( 'shop', Rewrites::audit_path( '/products/lamp/' ) );
		$this->assertSame( '', Rewrites::audit_path( '/projects/artam/' ) );
	}

	/**
	 * The eighteen product attributes and both taxonomies survive theme load.
	 */
	public function testProductAttributesAreCounted(): void {
		$attributes = wc_get_attribute_taxonomies();
		if ( ! is_array( $attributes ) ) {
			$this->markTestSkipped( 'WooCommerce attribute API unavailable.' );
		}
		$this->assertCount( 18, $attributes, 'The blueprint expects 18 pa_* attributes.' );
		foreach ( array( 'pa_ip-rating', 'pa_input-voltage', 'pa_color' ) as $taxonomy ) {
			$this->assertTrue( taxonomy_exists( $taxonomy ), $taxonomy . ' must exist.' );
		}
	}

	/**
	 * The project list table exposes city, type and finish date.
	 */
	public function testProjectAdminColumnsAndFilters(): void {
		$columns = ContentTypes::project_columns(
			array(
				'cb'    => '<input type="checkbox" />',
				'title' => 'Title',
				'date'  => 'Date',
			)
		);
		$this->assertArrayHasKey( 'project_city', $columns );
		$this->assertArrayHasKey( 'project_type_label', $columns );
		$this->assertArrayHasKey( 'finish_time', $columns );
		$this->assertSame( array( 'cb', 'title', 'project_city', 'project_type_label', 'finish_time', 'date' ), array_keys( $columns ) );

		$post_columns = ContentTypes::content_columns( array( 'title' => 'Title' ) );
		$this->assertSame( 'same_key_hrefl', ContentTypes::make_shared_key_sortable( $post_columns )['same_key_hrefl'] );
	}

	/**
	 * Every historical meta key is registered with its real type.
	 */
	public function testHistoricalMetaKeysAreRegistered(): void {
		$expected = array(
			'gs1_id',
			'national_code',
			'hs_id',
			'isic_id',
			'cpc_id',
			'product_id',
			'zireonvan',
			'english_titl',
			'before-sale-c',
			'product-catalog-file',
			'finish-pro-time',
			'hrefmeta_en',
			'hrefmeta_ar',
			'hrefmeta_fa',
			'same_key_hrefl',
		);
		foreach ( $expected as $key ) {
			$this->assertTrue( registered_meta_key_exists( 'post', $key, 'product' ), $key . ' is not registered for products.' );
			$this->assertTrue( registered_meta_key_exists( 'post', $key, 'projects' ), $key . ' is not registered for projects.' );
		}
		$this->assertTrue( registered_meta_key_exists( 'post', 'number2-viewc', 'post' ) );
	}
}
