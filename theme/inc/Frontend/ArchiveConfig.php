<?php
/** Query and view configuration for product, project and post archives. */

namespace ErfanSanat\Theme\Frontend;

use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Helpers\Options;

defined( 'ABSPATH' ) || exit;

/** Applies stored settings to main archive queries without issuing extra queries. */
final class ArchiveConfig implements ModuleInterface {

	private const DEFAULT_PER_PAGE = 12;
	private const ALLOWED_PER_PAGE = array( 6, 9, 12, 24 );
	private const ALLOWED_COLUMNS = array( '1', '2', '3', '4' );
	private const SORTS = array(
		'date_desc'  => array( 'date', 'DESC' ),
		'date_asc'   => array( 'date', 'ASC' ),
		'title_asc'  => array( 'title', 'ASC' ),
		'title_desc' => array( 'title', 'DESC' ),
		'menu_order' => array( 'menu_order title', 'ASC' ),
	);

	/** Attach one callback to the standard main-query filter. */
	public function register(): void {
		add_action( 'pre_get_posts', array( self::class, 'configure_query' ) );
	}

	/** Apply options to main archive requests only. */
	public static function configure_query( $query ): void {
		if ( is_admin() || ! is_object( $query ) || ! method_exists( $query, 'is_main_query' ) || ! $query->is_main_query() || ! self::is_archive_query( $query ) ) {
			return;
		}
		$query->set( 'posts_per_page', self::posts_per_page() );
		$sort = self::sort_values();
		$query->set( 'orderby', $sort[0] );
		$query->set( 'order', $sort[1] );
	}

	/** Get a safe page size from the explicit allow-list. */
	public static function posts_per_page(): int {
		$value = Options::get( 'archive.posts_per_page', self::DEFAULT_PER_PAGE );
		$value = is_scalar( $value ) && preg_match( '/^[0-9]+$/', (string) $value ) ? (int) $value : 0;
		return in_array( $value, self::ALLOWED_PER_PAGE, true ) ? $value : self::DEFAULT_PER_PAGE;
	}

	/** Return a valid grid count for templates without modifying their class markup. */
	public static function columns(): string {
		$value = (string) Options::get( 'archive.columns', '3' );
		return in_array( $value, self::ALLOWED_COLUMNS, true ) ? $value : '3';
	}

	/** Return only enabled, known filter identifiers for template iteration. */
	public static function visible_filters(): array {
		$keys    = array( 'category', 'ip', 'voltage', 'price', 'rating', 'views' );
		$visible = array();
		foreach ( $keys as $key ) {
			$value = Options::get( 'archive.filters.' . $key, true );
			if ( in_array( $value, array( true, 1, '1', 'on', 'yes' ), true ) ) {
				$visible[] = $key;
			}
		}
		return $visible;
	}

	/** Render a filter via template-owned markup, omitting disabled controls entirely. */
	public static function render_filter( string $key, callable $render ): string {
		return in_array( $key, self::visible_filters(), true ) ? (string) call_user_func( $render ) : '';
	}

	/** Whether the active archive should render its category FAQ block. */
	public static function show_faq(): bool {
		return in_array( Options::get( 'archive.show_faq', true ), array( true, 1, '1', 'on', 'yes' ), true );
	}

	/** Map a saved sort slug to safe WP_Query arguments. */
	private static function sort_values(): array {
		$sort = Options::get( 'archive.order', 'date_desc' );
		$sort = is_scalar( $sort ) ? (string) $sort : '';
		return self::SORTS[ $sort ] ?? self::SORTS['date_desc'];
	}

	/** Recognize public archive requests without running a query of our own. */
	private static function is_archive_query( object $query ): bool {
		foreach ( array( 'is_post_type_archive', 'is_tax', 'is_home', 'is_category', 'is_tag' ) as $method ) {
			if ( method_exists( $query, $method ) && $query->{$method}() ) {
				return true;
			}
		}
		return false;
	}
}
