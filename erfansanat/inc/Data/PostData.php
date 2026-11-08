<?php
/**
 * Blog post reads.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Values the blog templates print for one post.
 *
 * The table of contents is derived from the post's own `h2` headings, so the
 * anchors a reader clicks are the anchors the content actually has. Nothing is
 * invented: a post without headings gets no table of contents, and a post
 * without a category gets no label.
 */
final class PostData {

	/**
	 * Heading levels that become table-of-contents entries.
	 */
	public const TOC_LEVELS = array( 'h2' );

	/**
	 * Reading time in minutes, from the real word count.
	 *
	 * Persian prose runs slower than the common 240 wpm figure, so 200 is used;
	 * the value is rounded up and never below one minute.
	 *
	 * @param int $post_id Post id.
	 * @return int
	 */
	public static function reading_time( int $post_id ): int {
		$content = (string) get_post_field( 'post_content', $post_id );
		$text    = trim( wp_strip_all_tags( strip_shortcodes( $content ) ) );
		if ( '' === $text ) {
			return 1;
		}
		$words = preg_split( '/\s+/u', $text );
		$words = is_array( $words ) ? count( array_filter( $words ) ) : 0;
		return max( 1, (int) ceil( $words / 200 ) );
	}

	/**
	 * Table of contents for the post content.
	 *
	 * Each entry carries the heading text, the anchor it will get, and the
	 * level. The content filter that adds those anchors prints exactly the ids
	 * returned here, so the two can never drift apart.
	 *
	 * @param int $post_id Post id.
	 * @return array<int, array{id: string, text: string, level: string}>
	 */
	public static function word_count( int $post_id ): int {
		$content = (string) get_post_field( 'post_content', $post_id );
		$content = wp_strip_all_tags( strip_shortcodes( $content ) );
		$content = trim( (string) preg_replace( '/\s+/u', ' ', $content ) );

		if ( '' === $content ) {
			return 0;
		}

		// Persian is space-delimited like English, so a split on whitespace is
		// the honest count; `str_word_count()` returns 0 for these characters.
		$words = preg_split( '/\s+/u', $content );
		return is_array( $words ) ? count( array_filter( $words ) ) : 0;
	}

	/**
	 * The table of contents for a post.
	 *
	 * @param int $post_id Post id.
	 * @return array<int, array{id: string, text: string, level: string}>
	 */
	public static function toc( int $post_id ): array {
		$content = (string) get_post_field( 'post_content', $post_id );
		if ( '' === $content ) {
			return array();
		}

		$entries = array();
		$index   = 0;
		foreach ( self::TOC_LEVELS as $level ) {
			if ( ! preg_match_all( '#<' . $level . '[^>]*>(.*?)</' . $level . '>#is', $content, $matches ) ) {
				continue;
			}
			foreach ( $matches[1] as $inner ) {
				$text = trim( wp_strip_all_tags( (string) $inner ) );
				if ( '' === $text ) {
					continue;
				}
				++$index;
				$entries[] = array(
					'id'    => 'sec-' . $index,
					'text'  => $text,
					'level' => $level,
				);
			}
		}

		return $entries;
	}

	/**
	 * Add the table-of-contents anchors to the post content.
	 *
	 * Runs on `the_content` for a single post only, and numbers headings in the
	 * same order `toc()` does, so `#sec-3` is always the third `h2`.
	 *
	 * @param string $content Post content.
	 * @return string
	 */
	public static function add_heading_anchors( string $content ): string {
		if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
			return $content;
		}
		if ( ! in_array( 'h2', self::TOC_LEVELS, true ) ) {
			return $content;
		}

		$index = 0;
		return (string) preg_replace_callback(
			'#<h2([^>]*)>(.*?)</h2>#is',
			static function ( array $hit ) use ( &$index ): string {
				++$index;
				$attributes = (string) $hit[1];
				// An author-supplied id wins; the theme only fills the gap.
				if ( preg_match( '#\sid=("|\')#i', $attributes ) ) {
					return (string) $hit[0];
				}
				return '<h2' . $attributes . ' id="sec-' . $index . '">' . $hit[2] . '</h2>';
			},
			$content
		);
	}

	/**
	 * Category names of a post.
	 *
	 * @param int $post_id Post id.
	 * @return array<int, string>
	 */
	public static function categories( int $post_id ): array {
		$terms = get_the_category( $post_id );
		if ( ! is_array( $terms ) ) {
			return array();
		}
		return array_values( array_map( static fn( $term ): string => (string) $term->name, $terms ) );
	}

	/**
	 * Payload the blog archive script filters on.
	 *
	 * @param \WP_Post $post Post.
	 * @return array{category: string, search_text: string}
	 */
	public static function archive_payload( \WP_Post $post ): array {
		$categories = self::categories( (int) $post->ID );
		$parts      = array_merge(
			array( (string) $post->post_title, trim( wp_strip_all_tags( (string) $post->post_excerpt ) ) ),
			$categories
		);
		return array(
			'category'    => (string) ( $categories[0] ?? '' ),
			'search_text' => trim( implode( ' ', array_filter( $parts ) ) ),
		);
	}

	/**
	 * Fallback background image for a post card.
	 *
	 * The fixture posts have no featured images, and a card with a coloured
	 * placeholder reads better than a card with a blank box. The image is one of
	 * the theme's own, chosen by post id so the same post always looks the same.
	 *
	 * @param int $post_id Post id.
	 * @return string Empty when the post has its own thumbnail.
	 */
	public static function fallback_thumb( int $post_id ): string {
		if ( has_post_thumbnail( $post_id ) ) {
			return '';
		}
		$pool = array(
			'images/light-tree.jpg',
			'images/light-tunnel.jpg',
			'images/light-sphere.jpg',
			'images/light-element.jpg',
			'images/chandelier.jpg',
			'images/sun-light.jpg',
		);
		return (string) \ErfanSanat\Theme\Frontend\SectionParts::asset( $pool[ $post_id % count( $pool ) ] );
	}
}
