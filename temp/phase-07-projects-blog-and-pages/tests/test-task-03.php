<?php
declare(strict_types=1);

/**
 * Task 7.3 acceptance: services, public page templates and the featured page.
 *
 * @package ErfanSanat
 */

require_once __DIR__ . '/bootstrap.php';

use PHPUnit\Framework\TestCase;

/**
 * Contracts for the service templates and the page templates.
 */
final class TaskP07T03Test extends TestCase {

	/**
	 * The templates this task owns exist at the paths WordPress looks in.
	 */
	public function test_templates_exist(): void {
		foreach (
			array(
				'archive-services.php',
				'single-services.php',
				'page-templates/recruitment.php',
				'page-templates/featured-projects.php',
			) as $file
		) {
			$this->assertFileExists( get_template_directory() . '/' . $file, $file );
		}
	}

	/**
	 * Every service in the fixture resolves to a page the theme can render.
	 */
	public function test_every_service_has_a_template(): void {
		$services = get_posts(
			array(
				'post_type'      => 'services',
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);
		$this->assertNotEmpty( $services, 'The fixture must contain services.' );

		// `single-services.php` in the theme root is what WordPress picks; the
		// assertion above covers existence, this one the permalink shape.
		foreach ( $services as $service_id ) {
			$link = get_permalink( (int) $service_id );
			$this->assertIsString( $link );
			$this->assertStringContainsString( '/services/', (string) $link );
		}
	}

	/**
	 * Both page templates declare themselves to WordPress with a real header.
	 */
	public function test_page_templates_are_registered(): void {
		$templates = wp_get_theme()->get_page_templates();
		$this->assertArrayHasKey( 'page-templates/recruitment.php', $templates, 'The recruitment template must be selectable.' );
		$this->assertArrayHasKey( 'page-templates/featured-projects.php', $templates, 'The featured-projects template must be selectable.' );
	}

	/**
	 * The services archive renders one row per published service, not a fixed
	 * list of five headings copied from the mirror.
	 */
	public function test_services_archive_reads_the_post_type(): void {
		$source = (string) file_get_contents( get_template_directory() . '/archive-services.php' );

		$this->assertStringContainsString( "'post_type'      => 'services'", $source );
		$this->assertStringContainsString( 'foreach ( $erfansanat_services', $source );
		$this->assertStringNotContainsString( 'street-lighting', $source, 'No hard-coded service slug.' );
	}

	/**
	 * Related projects on a service page come from a real query.
	 */
	public function test_single_service_queries_related_projects(): void {
		$source = (string) file_get_contents( get_template_directory() . '/single-services.php' );

		$this->assertStringContainsString( "'post_type'      => 'projects'", $source );
		$this->assertStringContainsString( 'project_type', $source );
		$this->assertStringContainsString( 'card-project.php', $source );
	}

	/**
	 * The recruitment template deliberately has no application form.
	 *
	 * The brief's gate for a CV form — destination, access, retention — is not
	 * decided, and a form that silently drops a résumé is worse than none.
	 */
	public function test_recruitment_does_not_collect_cvs_yet(): void {
		$source = (string) file_get_contents( get_template_directory() . '/page-templates/recruitment.php' );

		$this->assertStringNotContainsString( '<form', $source, 'No résumé upload until its gate is decided.' );
		$this->assertStringNotContainsString( 'type="file"', $source );
		$this->assertStringContainsString( 'mailto:', $source, 'It must still give a real way to apply.' );
	}

	/**
	 * The featured-projects page asks for a real flag and falls back rather than
	 * rendering an empty grid on a site that has flagged nothing.
	 */
	public function test_featured_projects_uses_a_real_flag(): void {
		$source = (string) file_get_contents( get_template_directory() . '/page-templates/featured-projects.php' );

		$this->assertStringContainsString( "'featured'", $source );
		$this->assertStringContainsString( 'erfansanat_is_flagged', $source, 'The fallback must be explicit.' );
		$this->assertStringContainsString( 'card-project.php', $source );
	}

	/**
	 * Every theme template keeps a single `h1`, which the HTTP suite also checks.
	 */
	public function test_service_templates_have_one_heading(): void {
		foreach ( array( 'archive-services.php', 'single-services.php' ) as $file ) {
			$source = (string) file_get_contents( get_template_directory() . '/' . $file );
			$this->assertSame( 1, substr_count( $source, '<h1' ), $file . ' must print exactly one h1.' );
		}
	}
}
