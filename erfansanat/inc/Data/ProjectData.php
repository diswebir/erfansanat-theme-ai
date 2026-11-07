<?php
/**
 * Project reads over the historical JetEngine keys.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Turns a project post into the values the single and archive templates print.
 *
 * The mirror stores a project's gallery in five numbered `-image` slots plus the
 * featured image, the completion date in a Jalali string, and the linked
 * products in a serialised id list. Everything here goes through LegacyMeta, so
 * a value saved by JetEngine, by the theme's metabox, or by hand as a plain
 * comma list reads the same way.
 */
final class ProjectData {

	/**
	 * Number of gallery images the reference shows, featured image included.
	 */
	public const GALLERY_SIZE = 6;

	/**
	 * Gallery image URLs in display order.
	 *
	 * The featured image leads, then the five numbered slots; empty slots are
	 * skipped so a project with three images renders three, not three and three
	 * broken frames. Attachments resolve to `full` so the lightbox is sharp.
	 *
	 * @param int $project_id Project post id.
	 * @return array<int, array{id: int, url: string, alt: string}>
	 */
	public static function gallery( int $project_id ): array {
		$images = array();
		$seen   = array();

		$add = static function ( int $attachment_id, string $alt ) use ( &$images, &$seen ): void {
			if ( $attachment_id <= 0 || isset( $seen[ $attachment_id ] ) ) {
				return;
			}
			$url = wp_get_attachment_image_url( $attachment_id, 'full' );
			if ( ! is_string( $url ) || '' === $url ) {
				return;
			}
			$seen[ $attachment_id ] = true;
			$images[]               = array(
				'id'  => $attachment_id,
				'url' => $url,
				'alt' => $alt,
			);
		};

		$title = (string) get_the_title( $project_id );
		$add( (int) get_post_thumbnail_id( $project_id ), $title );

		foreach ( LegacyMeta::project_image_slots() as $slot ) {
			$value = get_post_meta( $project_id, $slot, true );
			foreach ( LegacyMeta::to_array( $value ) as $candidate ) {
				$add( (int) $candidate, $title );
			}
		}

		return array_slice( $images, 0, self::GALLERY_SIZE );
	}

	/**
	 * Completion date as stored, for example `1404/03/15`.
	 *
	 * @param int $project_id Project post id.
	 * @return string Empty when the project has no date.
	 */
	public static function completion_date( int $project_id ): string {
		return trim( LegacyMeta::text( $project_id, 'finish-pro-time' ) );
	}

	/**
	 * Completion date split into its parts.
	 *
	 * The reference prints the Persian month name and the year, so the string is
	 * parsed once here instead of in three templates.
	 *
	 * @param int $project_id Project post id.
	 * @return array{year: string, month: string, day: string, month_name: string, display: string}
	 */
	public static function completion_parts( int $project_id ): array {
		$raw   = self::completion_date( $project_id );
		$empty = array(
			'year'       => '',
			'month'      => '',
			'day'        => '',
			'month_name' => '',
			'display'    => '',
		);
		if ( '' === $raw ) {
			return $empty;
		}

		$parts = preg_split( '#[/\-.]#', $raw );
		$parts = is_array( $parts ) ? array_values( array_filter( array_map( 'trim', $parts ), 'strlen' ) ) : array();
		if ( array() === $parts ) {
			return $empty;
		}

		$months = array(
			1  => __( 'فروردین', 'erfansanat' ),
			2  => __( 'اردیبهشت', 'erfansanat' ),
			3  => __( 'خرداد', 'erfansanat' ),
			4  => __( 'تیر', 'erfansanat' ),
			5  => __( 'مرداد', 'erfansanat' ),
			6  => __( 'شهریور', 'erfansanat' ),
			7  => __( 'مهر', 'erfansanat' ),
			8  => __( 'آبان', 'erfansanat' ),
			9  => __( 'آذر', 'erfansanat' ),
			10 => __( 'دی', 'erfansanat' ),
			11 => __( 'بهمن', 'erfansanat' ),
			12 => __( 'اسفند', 'erfansanat' ),
		);

		$year  = (string) ( $parts[0] ?? '' );
		$month = (string) ( $parts[1] ?? '' );
		$day   = (string) ( $parts[2] ?? '' );
		$name  = isset( $months[ (int) $month ] ) ? (string) $months[ (int) $month ] : '';

		$display = trim( $name . ' ' . $year );

		return array(
			'year'       => $year,
			'month'      => $month,
			'day'        => $day,
			'month_name' => $name,
			'display'    => $display,
		);
	}

	/**
	 * City names for the project.
	 *
	 * @param int $project_id Project post id.
	 * @return array<int, string>
	 */
	public static function cities( int $project_id ): array {
		return self::term_names( $project_id, 'city' );
	}

	/**
	 * Project-type names for the project.
	 *
	 * @param int $project_id Project post id.
	 * @return array<int, string>
	 */
	public static function types( int $project_id ): array {
		return self::term_names( $project_id, 'project_type' );
	}

	/**
	 * Products used in the project, as `product_id => title`.
	 *
	 * The relation key holds ids in every format JetEngine has used, and a
	 * project may reference a product that was later deleted, so the ids are
	 * filtered through `get_post` before they reach a template.
	 *
	 * @param int $project_id Project post id.
	 * @return array<int, string>
	 */
	public static function products( int $project_id ): array {
		$ids   = LegacyMeta::read_ids( $project_id, 'mortabet_mahsole_2' );
		$out   = array();
		$limit = 0;
		foreach ( $ids as $id ) {
			$post = get_post( (int) $id );
			if ( ! $post instanceof \WP_Post || 'product' !== $post->post_type || 'publish' !== $post->post_status ) {
				continue;
			}
			$out[ (int) $post->ID ] = (string) get_the_title( $post );
			++$limit;
			if ( $limit >= 8 ) {
				break;
			}
		}
		return $out;
	}

	/**
	 * Other projects of the same type, excluding this one.
	 *
	 * @param int $project_id Project post id.
	 * @param int $limit      Maximum number of posts.
	 * @return array<int, \WP_Post>
	 */
	public static function related( int $project_id, int $limit = 3 ): array {
		$types = wp_get_post_terms( $project_id, 'project_type', array( 'fields' => 'ids' ) );
		$args  = array(
			'post_type'           => 'projects',
			'post_status'         => 'publish',
			'posts_per_page'      => max( 1, $limit ),
			'post__not_in'        => array( $project_id ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);
		if ( is_array( $types ) && array() !== $types ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Taxonomy filter, not meta.
				array(
					'taxonomy' => 'project_type',
					'field'    => 'term_id',
					'terms'    => array_map( 'intval', $types ),
				),
			);
		}
		$posts = get_posts( $args );
		if ( count( $posts ) < $limit ) {
			// Top up with the newest projects so the strip is never half empty.
			$fill  = get_posts(
				array(
					'post_type'           => 'projects',
					'post_status'         => 'publish',
					'posts_per_page'      => $limit,
					'post__not_in'        => array_merge( array( $project_id ), wp_list_pluck( $posts, 'ID' ) ),
					'ignore_sticky_posts' => true,
					'no_found_rows'       => true,
				)
			);
			$posts = array_merge( $posts, $fill );
		}
		return array_slice( $posts, 0, max( 1, $limit ) );
	}

	/**
	 * Views counter for the project.
	 *
	 * @param int $project_id Project post id.
	 * @return int
	 */
	public static function views( int $project_id ): int {
		return LegacyMeta::views( $project_id );
	}

	/**
	 * Payload the archive script needs to filter and sort one card.
	 *
	 * The values are the Persian strings the script compares against the filter
	 * options, exactly like the shop archive's `archive_payload()`.
	 *
	 * @param \WP_Post $project Project post.
	 * @return array{type: string, types: string, location: string, search_text: string, views: string, rating: string, date: string}
	 */
	public static function archive_payload( \WP_Post $project ): array {
		$cities = self::cities( (int) $project->ID );
		$types  = self::types( (int) $project->ID );

		$search   = array( (string) $project->post_title );
		$search   = array_merge( $search, $cities, $types );
		$search[] = trim( wp_strip_all_tags( (string) $project->post_excerpt ) );

		return array(
			'type'        => (string) ( $types[0] ?? '' ),
			'types'       => implode( '|', $types ),
			'location'    => (string) ( $cities[0] ?? '' ),
			'search_text' => trim( implode( ' ', array_filter( $search ) ) ),
			'views'       => (string) self::views( (int) $project->ID ),
			'rating'      => '0',
			'date'        => (string) ( $project->post_date ?? '' ),
		);
	}

	/**
	 * Term names of one taxonomy for a post.
	 *
	 * @param int    $post_id  Post id.
	 * @param string $taxonomy Taxonomy.
	 * @return array<int, string>
	 */
	private static function term_names( int $post_id, string $taxonomy ): array {
		$terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'names' ) );
		if ( ! is_array( $terms ) ) {
			return array();
		}
		return array_values( array_filter( array_map( 'strval', $terms ), 'strlen' ) );
	}
}
