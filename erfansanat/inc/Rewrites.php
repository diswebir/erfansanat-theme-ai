<?php
/**
 * Permalink aliases and rewrite maintenance.
 *
 * The blueprint marks the legacy `/products/` family as a redirect candidate
 * only after an audit, so nothing here rewrites a path automatically. This
 * class instead:
 *
 * - keeps the `projects`/`city`/`project_type`/`services` rules registered,
 * - flushes rules only when the theme's own rule version changes,
 * - exposes an explicit, administrator-triggered alias table.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Rewrite rules and opt-in aliases.
 */
final class Rewrites {

	/**
	 * Option that records which rule set is already flushed.
	 */
	public const VERSION_OPTION = 'erfansanat_rewrite_version';

	/**
	 * Current rule set version. Bump when a slug changes.
	 */
	public const VERSION = '3.1';

	/**
	 * Legacy aliases that may be enabled after a live-path audit.
	 *
	 * Keys are the shipped alias, values the canonical destination. Nothing is
	 * active by default: the blueprint requires an audit before enabling.
	 *
	 * @return array<string, string>
	 */
	public static function candidate_aliases(): array {
		return array(
			'products'    => 'shop',
			'product-cat' => 'product_cat',
			'project_cat' => 'project_type',
		);
	}

	/**
	 * Attach hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'init', array( self::class, 'maybe_flush' ), 99 );
	}

	/**
	 * Flush rewrite rules once per rule version.
	 *
	 * `flush_rewrite_rules()` is expensive; running it on every request is a
	 * classic WordPress performance bug. A version option keeps it to one call
	 * per deployment while still refreshing after a slug change.
	 *
	 * @return void
	 */
	public static function maybe_flush(): void {
		if ( get_option( self::VERSION_OPTION ) === self::VERSION ) {
			return;
		}
		flush_rewrite_rules( false );
		update_option( self::VERSION_OPTION, self::VERSION, false );
	}

	/**
	 * Force a flush on the next request (used after activation/imports).
	 *
	 * @return void
	 */
	public static function schedule_flush(): void {
		delete_option( self::VERSION_OPTION );
	}

	/**
	 * Canonical permalink for a project.
	 *
	 * @param int $post_id Project id.
	 * @return string
	 */
	public static function project_link( int $post_id ): string {
		$link = get_permalink( $post_id );
		return is_string( $link ) ? $link : '';
	}

	/**
	 * Term archive link that never returns a WP_Error.
	 *
	 * @param int    $term_id  Term id.
	 * @param string $taxonomy Taxonomy.
	 * @return string
	 */
	public static function term_link( int $term_id, string $taxonomy ): string {
		$link = get_term_link( $term_id, $taxonomy );
		return is_string( $link ) ? $link : '';
	}

	/**
	 * The alias currently requested, if it matches a candidate.
	 *
	 * Kept for the audit tooling: it reports what would be redirected without
	 * enabling anything.
	 *
	 * @param string $request_path Request path without query string.
	 * @return string Destination path, or an empty string.
	 */
	public static function audit_path( string $request_path ): string {
		$path  = trim( (string) wp_parse_url( $request_path, PHP_URL_PATH ), '/' );
		$first = explode( '/', $path )[0];
		return self::candidate_aliases()[ $first ] ?? '';
	}
}
