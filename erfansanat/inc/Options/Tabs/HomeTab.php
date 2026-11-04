<?php
/**
 * Home / hero tab (P05-T005).
 *
 * The main hero video is a global theme option, independent from the
 * per-page project slides (AC-01). Poster and preload are configurable
 * (AC-03); when no video is selected the hero falls back to an image
 * (AC-02) so removing the video never breaks the hero (SC-02).
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Options\Tabs;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Schema;

final class HomeTab {

	public const ID = 'home';

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		// The hero template part reads these accessors; no front-end hook is
		// needed here. Boot is kept for lifecycle consistency.
	}

	public static function id(): string {
		return self::ID;
	}

	/**
	 * Field keys belonging to this tab.
	 *
	 * @return array<int,string>
	 */
	public static function fields(): array {
		return array_keys(
			array_filter(
				Schema::fields(),
				static function ( $field ) {
					return ( $field['tab'] ?? '' ) === self::ID;
				}
			)
		);
	}

	/**
	 * Mirror-derived defaults for this tab.
	 *
	 * @return array<string,mixed>
	 */
	public static function defaults(): array {
		$defaults = array();
		foreach ( self::fields() as $key ) {
			$defaults[ $key ] = Schema::default( $key );
		}
		return $defaults;
	}

	/**
	 * Selected main hero video attachment ID (0 = none).
	 */
	public static function hero_video_id(): int {
		return (int) es_option( 'hero_video_id' );
	}

	/**
	 * Main hero video file URL, or '' when no valid video is selected.
	 */
	public static function hero_video_url(): string {
		$id = self::hero_video_id();
		if ( $id <= 0 ) {
			return '';
		}
		$url = wp_get_attachment_url( $id );
		return $url ? (string) $url : '';
	}

	/**
	 * Poster image URL (falls back to the bundled hero image).
	 */
	public static function hero_video_poster_url(): string {
		$id = (int) es_option( 'hero_video_poster' );
		if ( $id > 0 ) {
			$url = wp_get_attachment_image_url( $id, 'large' );
			if ( $url ) {
				return (string) $url;
			}
		}
		return ES_ASSETS_URI . 'images/hero.jpg';
	}

	/**
	 * Preload policy, constrained to the three valid HTML values (AC-03).
	 */
	public static function hero_video_preload(): string {
		$value = (string) es_option( 'hero_video_preload' );
		return in_array( $value, array( 'auto', 'metadata', 'none' ), true ) ? $value : 'metadata';
	}

	/**
	 * Fallback image URL used when no video is selected (AC-02). Prefers the
	 * dedicated fallback image, then the poster, then the bundled hero image.
	 */
	public static function hero_video_fallback_url(): string {
		$id = (int) es_option( 'hero_video_fallback' );
		if ( $id > 0 ) {
			$url = wp_get_attachment_image_url( $id, 'large' );
			if ( $url ) {
				return (string) $url;
			}
		}
		return self::hero_video_poster_url();
	}

	/**
	 * Raw hero slide rows (project_id + optional image override).
	 *
	 * @return array<int,array<string,mixed>>
	 */
	public static function hero_slides(): array {
		$raw = es_option( 'hero_slides' );
		return is_array( $raw ) ? $raw : array();
	}

	/**
	 * Resolve slide rows against real projects (P05-T006).
	 *
	 * Only published `projects` posts become slides (AC-01); draft, trashed or
	 * deleted projects are skipped without breaking the slider (AC-03 / SC-02).
	 * Order is preserved (AC-02) and title/image/link come from the project
	 * (AC-04), with an optional per-slide image override.
	 *
	 * @param array<int,mixed> $raw Raw repeater rows.
	 * @return array<int,array{project_id:int,title:string,link:string,image:string}>
	 */
	public static function resolve_slides( array $raw ): array {
		$slides = array();
		foreach ( $raw as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$pid = (int) ( $row['project_id'] ?? 0 );
			if ( $pid <= 0 ) {
				continue;
			}
			$project = get_post( $pid );
			if ( ! $project || 'projects' !== $project->post_type || 'publish' !== $project->post_status ) {
				continue; // AC-01 / AC-03: unpublished or deleted projects are skipped.
			}

			$img_id = (int) ( $row['image'] ?? 0 );
			$image  = $img_id > 0 ? (string) wp_get_attachment_image_url( $img_id, 'large' ) : '';
			if ( ! $image ) {
				$image = (string) get_the_post_thumbnail_url( $pid, 'large' );
			}
			if ( ! $image ) {
				$image = ES_ASSETS_URI . 'images/light-tunnel.jpg';
			}

			$slides[] = array(
				'project_id' => $pid,
				'title'      => get_the_title( $pid ),
				'link'       => (string) get_permalink( $pid ),
				'image'      => $image,
			);
		}
		return $slides;
	}

	/**
	 * Resolved global hero slides.
	 *
	 * @return array<int,array{project_id:int,title:string,link:string,image:string}>
	 */
	public static function hero_slides_resolved(): array {
		return self::resolve_slides( self::hero_slides() );
	}

	/**
	 * Convert ASCII digits to Persian digits.
	 */
	private static function fa( string $value ): string {
		return strtr( $value, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
	}

	/**
	 * Slug => lucide icon name map for the hero category cards (filterable).
	 *
	 * @return array<string,string>
	 */
	public static function category_icon_map(): array {
		return (array) apply_filters(
			'es_hero_category_icons',
			array(
				'point-light'        => 'cpu',
				'needle-thread'      => 'sparkles',
				'light-tree'         => 'trees',
				'acorn-thread'       => 'lightbulb',
				'wall-washer'        => 'waves',
				'sun-light-elements' => 'sun',
				'light-chandelier'   => 'flame',
			)
		);
	}

	/**
	 * Hero category cards (P05-T007) sourced from the product_cat taxonomy.
	 *
	 * Each card carries the term name/description/link, a Persian product
	 * count and a lucide icon name resolved from the (filterable) slug map.
	 *
	 * @return array<int,array{title:string,subtitle:string,href:string,count:string,icon:string}>
	 */
	public static function hero_categories(): array {
		$cap   = (int) apply_filters( 'es_hero_categories_count', 7 );
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'parent'     => 0,
				'number'     => max( 1, $cap ),
				'hide_empty' => false,
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		$map  = self::category_icon_map();
		$cards = array();
		foreach ( $terms as $term ) {
			$link = get_term_link( $term );
			if ( is_wp_error( $link ) ) {
				continue;
			}
			$cards[] = array(
				'title'    => $term->name,
				'subtitle' => wp_strip_all_tags( (string) $term->description ),
				'href'     => (string) $link,
				'count'    => self::fa( (string) (int) $term->count ) . ' مدل',
				'icon'     => $map[ $term->slug ] ?? 'lightbulb',
			);
		}
		return $cards;
	}

	/**
	 * Hero overlay quick links (P05-T007) sourced from product_cat terms.
	 *
	 * @return array<int,array{label:string,href:string}>
	 */
	public static function hero_quick_links(): array {
		$cap   = (int) apply_filters( 'es_hero_quick_links_count', 8 );
		$terms = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'number'     => max( 1, $cap ),
				'hide_empty' => false,
				'orderby'    => 'count',
				'order'      => 'DESC',
			)
		);
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}
		$links = array();
		foreach ( $terms as $term ) {
			$link = get_term_link( $term );
			if ( is_wp_error( $link ) ) {
				continue;
			}
			$links[] = array( 'label' => $term->name, 'href' => (string) $link );
		}
		return (array) apply_filters( 'es_hero_quick_links', $links );
	}
}
