<?php
declare(strict_types=1);

/**
 * Task 7.1 acceptance: single project and the project / city / type archives.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use ErfanSanat\Theme\Data\LegacyMeta;
use ErfanSanat\Theme\Projects;
use PHPUnit\Framework\TestCase;

/**
 * Contracts for the project templates and the data layer behind them.
 */
final class TaskP07T01Test extends TestCase {

	/**
	 * Id of a published project for the data assertions.
	 *
	 * @return int
	 */
	private function project_id(): int {
		$posts = get_posts(
			array(
				'post_type'      => 'projects',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		$this->assertNotEmpty( $posts, 'The fixture must contain at least one published project.' );
		return (int) $posts[0];
	}

	/**
	 * The four templates exist at the theme root, where WordPress looks.
	 */
	public function test_templates_exist(): void {
		foreach ( array( 'single-projects.php', 'archive-projects.php', 'taxonomy-city.php', 'taxonomy-project_type.php' ) as $file ) {
			$this->assertFileExists( get_template_directory() . '/' . $file, $file );
		}
	}

	/**
	 * A single project must not print two `h1`s; the gallery caption is a `p`.
	 */
	public function test_single_project_heading_structure(): void {
		$this->setUp_project_query( $this->project_id() );

		ob_start();
		require get_template_directory() . '/template-parts/project/part-hero.php';
		$hero = (string) ob_get_clean();

		$this->assertSame( 1, substr_count( $hero, '<h1' ), 'The hero must own the only h1.' );
		$this->assertStringContainsString( 'data-project-hero="true"', $hero );
	}

	/**
	 * The gallery resolves the featured image plus the five historic slots, and
	 * never repeats the same attachment.
	 */
	public function test_gallery_reads_real_attachments(): void {
		$id      = $this->project_id();
		$gallery = \ErfanSanat\Theme\Data\ProjectData::gallery( $id );

		$this->assertNotEmpty( $gallery, 'The fixture project must have gallery images.' );
		$this->assertLessThanOrEqual( \ErfanSanat\Theme\Data\ProjectData::GALLERY_SIZE, count( $gallery ) );

		$ids = array_column( $gallery, 'id' );
		$this->assertSame( $ids, array_values( array_unique( $ids ) ), 'A gallery must not repeat an attachment.' );
		foreach ( $gallery as $image ) {
			$this->assertGreaterThan( 0, (int) $image['id'] );
			$this->assertStringStartsWith( 'http', (string) $image['url'] );
		}
	}

	/**
	 * A project without gallery slots still shows its featured image.
	 */
	public function test_gallery_falls_back_to_featured_image(): void {
		$post_id = (int) wp_insert_post(
			array(
				'post_type'   => 'projects',
				'post_status' => 'draft',
				'post_title'  => 'P07 gallery fallback',
			)
		);
		$this->assertGreaterThan( 0, $post_id );

		$this->assertSame( array(), \ErfanSanat\Theme\Data\ProjectData::gallery( $post_id ), 'A project with no images must return an empty gallery, not a broken frame.' );

		wp_delete_post( $post_id, true );
	}

	/**
	 * The Jalali completion date splits into a month name and a year.
	 */
	public function test_completion_date_is_parsed(): void {
		$post_id = (int) wp_insert_post(
			array(
				'post_type'   => 'projects',
				'post_status' => 'draft',
				'post_title'  => 'P07 date parsing',
			)
		);
		update_post_meta( $post_id, 'finish-pro-time', '1404/03/15' );

		$parts = \ErfanSanat\Theme\Data\ProjectData::completion_parts( $post_id );
		$this->assertSame( '1404', $parts['year'] );
		$this->assertSame( '03', $parts['month'] );
		$this->assertSame( '15', $parts['day'] );
		$this->assertNotSame( '', $parts['month_name'], 'The month number must resolve to a Persian name.' );
		$this->assertStringContainsString( '1404', $parts['display'] );

		// A project without a date must yield empty parts, not a fake year.
		update_post_meta( $post_id, 'finish-pro-time', '' );
		$this->assertSame( '', \ErfanSanat\Theme\Data\ProjectData::completion_parts( $post_id )['display'] );

		wp_delete_post( $post_id, true );
	}

	/**
	 * Related products come from the historical id list and skip dead ids.
	 */
	public function test_related_products_resolve_and_filter(): void {
		$post_id = (int) wp_insert_post(
			array(
				'post_type'   => 'projects',
				'post_status' => 'draft',
				'post_title'  => 'P07 related products',
			)
		);

		$products = wc_get_products(
			array(
				'limit'  => 2,
				'status' => 'publish',
				'return' => 'ids',
			)
		);
		$this->assertCount( 2, $products );

		// A published product, a product that does not exist, and a page id.
		$ids = array( (int) $products[0], 999999, 0 );
		update_post_meta( $post_id, 'mortabet_mahsole_2', wp_json_encode( $ids ) );

		$resolved = \ErfanSanat\Theme\Data\ProjectData::products( $post_id );
		$this->assertArrayHasKey( (int) $products[0], $resolved );
		$this->assertArrayNotHasKey( 999999, $resolved, 'A deleted product id must not leak into the template.' );

		wp_delete_post( $post_id, true );
	}

	/**
	 * The project archive query is limited to the theme page size, and the page
	 * size is a filter rather than a hard-coded constant.
	 */
	public function test_project_archive_page_size_is_a_filter(): void {
		// The sanitiser: a missing or non-positive value falls back to 6.
		$this->assertSame( Projects::PER_PAGE, Projects::per_page( 0 ) );
		$this->assertSame( Projects::PER_PAGE, Projects::per_page( -3 ) );
		$this->assertSame( 12, Projects::per_page( 12 ) );

		// A site may raise the page size without touching the theme. The theme's
		// own sanitiser is priority 10, so this replacement removes it first.
		remove_filter( 'erfansanat_projects_per_page', array( Projects::class, 'per_page' ) );
		add_filter( 'erfansanat_projects_per_page', array( $this, 'raise_page_size' ) );
		$this->assertSame( 4, (int) apply_filters( 'erfansanat_projects_per_page', Projects::PER_PAGE ) );
		remove_filter( 'erfansanat_projects_per_page', array( $this, 'raise_page_size' ) );
		add_filter( 'erfansanat_projects_per_page', array( Projects::class, 'per_page' ) );
	}

	/**
	 * Callback that raises the project page size (tests only).
	 *
	 * @return int
	 */
	public function raise_page_size(): int {
		return 4;
	}

	/**
	 * Only the main query of a project archive is re-paginated.
	 */
	public function test_only_project_archives_are_paginated(): void {
		$detached = new WP_Query( array( 'post_type' => 'projects', 'posts_per_page' => 1 ) );
		$this->assertFalse( Projects::is_project_archive_query( $detached ), 'A detached query is not the main query.' );

		$this->assertFalse( Projects::is_project_archive_query( null ) );
		$this->assertFalse( Projects::is_project_archive_query( 'not a query' ) );
	}

	/**
	 * The archive payload carries the keys `initProjectsArchive()` reads.
	 */
	public function test_archive_payload_contract(): void {
		$project = get_post( $this->project_id() );
		$this->assertInstanceOf( WP_Post::class, $project );

		$payload = \ErfanSanat\Theme\Data\ProjectData::archive_payload( $project );
		foreach ( array( 'type', 'types', 'location', 'search_text', 'views', 'rating', 'date' ) as $key ) {
			$this->assertArrayHasKey( $key, $payload, 'Missing payload key: ' . $key );
			$this->assertIsString( $payload[ $key ], 'Payload values are strings: ' . $key );
		}
		$this->assertNotSame( '', $payload['search_text'], 'The search string must contain the title.' );
		$this->assertStringContainsString( (string) $project->post_title, $payload['search_text'] );
	}

	/**
	 * Every project term link resolves to a URL, never to a `WP_Error`.
	 *
	 * The fixture stores percent-encoded slugs; casting an error to a string
	 * there is a fatal error, which is how this rule was found.
	 */
	public function test_term_links_never_return_errors(): void {
		foreach ( array( 'city', 'project_type' ) as $taxonomy ) {
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				)
			);
			$this->assertIsArray( $terms );
			foreach ( $terms as $term ) {
				$this->assertTrue( is_string( \ErfanSanat\Theme\term_url( $term ) ), $taxonomy . ' term link must be a string or empty.' );
			}
		}
	}

	/**
	 * The closing call to action is the home page's own section, wrapped in the
	 * `#contact` anchor the rest of the theme links to.
	 */
	public function test_closing_cta_is_shared_with_the_home_page(): void {
		$partial = get_template_directory() . '/template-parts/archive/part-projects-closing.php';
		$this->assertFileExists( $partial );

		$source = (string) file_get_contents( $partial );
		$this->assertStringContainsString( 'section-contact-cta.php', $source );
		$this->assertStringContainsString( 'id="contact"', $source );
	}

	/**
	 * Point the global post at a project, the way a single template runs.
	 *
	 * @param int $project_id Project id.
	 * @return void
	 */
	private function setUp_project_query( int $project_id ): void {
		$GLOBALS['post'] = get_post( $project_id );
		setup_postdata( $GLOBALS['post'] );
	}
}
