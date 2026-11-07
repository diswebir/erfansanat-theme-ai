<?php
/**
 * Project archive paging.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Gives the project archives, their city pages and their type pages a real
 * `?page=N` page number.
 *
 * The projects post type is a normal WordPress post type, so its archive runs
 * on the main query with the site-wide `posts_per_page`. That value applies to
 * every archive on the site (including the blog), so the theme cannot change it
 * globally; it overrides it on the project archives only.
 *
 * The per-page number is a filter rather than a constant so a site can raise it
 * without touching the theme, and the template reads the same filter so its
 * pager totals can never disagree with the query that produced the page.
 */
final class Projects {

	/**
	 * Projects per archive page.
	 */
	public const PER_PAGE = 6;

	/**
	 * Attach the hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'pre_get_posts', array( self::class, 'archive_paging' ) );
		add_filter( 'erfansanat_projects_per_page', array( self::class, 'per_page' ) );
	}

	/**
	 * The resolved page size.
	 *
	 * @param int $value Incoming filter value.
	 * @return int
	 */
	public static function per_page( $value ): int {
		$value = (int) $value;
		return $value > 0 ? $value : self::PER_PAGE;
	}

	/**
	 * Apply the page size and page number to a project archive query.
	 *
	 * @param mixed $query Candidate query.
	 * @return void
	 */
	public static function archive_paging( $query ): void {
		if ( ! self::is_project_archive_query( $query ) ) {
			return;
		}

		$per_page = (int) apply_filters( 'erfansanat_projects_per_page', self::PER_PAGE );
		$query->set( 'posts_per_page', max( 1, $per_page ) );
	}

	/**
	 * Whether a query is the main query of a project archive or one of its terms.
	 *
	 * Separate from the hook so the rule is testable without a real page
	 * request, exactly like WooCommerce::is_product_archive_query().
	 *
	 * @param mixed $query Candidate query.
	 * @return bool
	 */
	public static function is_project_archive_query( $query ): bool {
		if ( ! $query instanceof \WP_Query || ! $query->is_main_query() ) {
			return false;
		}

		if ( $query->is_post_type_archive( 'projects' ) ) {
			return true;
		}

		return $query->is_tax( array( 'city', 'project_type' ) );
	}
}
