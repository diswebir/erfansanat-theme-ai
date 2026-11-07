<?php
/** Reusable card renderer that preserves classes from the static homepage reference. */

namespace ErfanSanat\Theme\Sections;

defined( 'ABSPATH' ) || exit;

/** Resolves post/term presentation values, then safely fills trusted card fragments. */
final class HomeCardMarkup {

	/** Render a product, project, or post card using explicit args or a WordPress object. */
	public static function render( string $type, array $args = array() ): void {
		if ( ! in_array( $type, array( 'product', 'project', 'post' ), true ) ) { return; }
		$path = dirname( __DIR__, 2 ) . '/template-parts/cards/static/card-' . $type . '.html';
		$html = is_file( $path ) ? file_get_contents( $path ) : false;
		if ( ! is_string( $html ) ) { return; }
		$post = is_object( $args['post'] ?? null ) ? $args['post'] : null;
		$term = is_object( $args['term'] ?? null ) ? $args['term'] : null;
		$post_id = (int) ( $args['post_id'] ?? ( $post->ID ?? 0 ) );
		$term_id = (int) ( $args['term_id'] ?? ( $term->term_id ?? 0 ) );
		$title = self::text( $args['title'] ?? ( $term->name ?? '' ) );
		if ( '' === $title && $post_id && function_exists( 'get_the_title' ) ) { $title = (string) get_the_title( $post_id ); }
		$url = self::text( $args['url'] ?? '' );
		if ( '' === $url && $term && function_exists( 'get_term_link' ) ) { $link = get_term_link( $term ); $url = is_string( $link ) ? $link : ''; }
		if ( '' === $url && $post_id && function_exists( 'get_permalink' ) ) { $url = (string) get_permalink( $post_id ); }
		$image = self::text( $args['image'] ?? '' );
		if ( '' === $image && $post_id && function_exists( 'get_the_post_thumbnail_url' ) ) { $image = (string) get_the_post_thumbnail_url( $post_id, 'large' ); }
		if ( '' === $image && $term_id && function_exists( 'get_term_meta' ) && function_exists( 'wp_get_attachment_image_url' ) ) {
			$thumbnail_id = (int) get_term_meta( $term_id, 'thumbnail_id', true );
			$image = $thumbnail_id ? (string) wp_get_attachment_image_url( $thumbnail_id, 'large' ) : '';
		}
		if ( '' === $image ) { $image = function_exists( 'get_template_directory_uri' ) ? rtrim( get_template_directory_uri(), '/' ) . '/assets/img/light-tunnel.jpg' : ''; }
		$excerpt = self::text( $args['excerpt'] ?? '' );
		if ( '' === $excerpt && $post_id && function_exists( 'get_the_excerpt' ) ) { $excerpt = (string) get_the_excerpt( $post_id ); }
		$date = self::text( $args['date'] ?? '' );
		if ( '' === $date && $post_id && function_exists( 'get_the_date' ) ) { $date = (string) get_the_date( 'j F Y', $post_id ); }
		$values = array(
			'URL' => '' !== $url ? $url : '#',
			'IMAGE' => $image,
			'ALT' => self::text( $args['alt'] ?? $title ),
			'TITLE' => $title,
			'EXCERPT' => $excerpt,
			'CATEGORY' => self::text( $args['category'] ?? '' ),
			'BADGE' => self::text( $args['badge'] ?? '' ),
			'CITY' => self::text( $args['city'] ?? '' ),
			'META' => self::text( $args['meta'] ?? '' ),
			'DATE' => $date,
			'READING_TIME' => self::text( $args['reading_time'] ?? '' ),
			'CTA' => self::text( $args['cta'] ?? '' ),
			'SEPARATOR' => '·',
		);
		foreach ( $values as $key => $value ) {
			$escaped = in_array( $key, array( 'URL', 'IMAGE' ), true )
				? ( function_exists( 'esc_url' ) ? esc_url( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ) )
				: ( 'ALT' === $key ? ( function_exists( 'esc_attr' ) ? esc_attr( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ) ) : ( function_exists( 'esc_html' ) ? esc_html( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ) ) );
			$html = str_replace( '__CARD_' . $key . '__', $escaped, $html );
		}
		if ( 'product' === $type && empty( $args['_omit_product_data_attributes'] ) ) {
			$product_data = array(
				'data-product-item' => 'true',
				'data-product-id' => (string) $post_id,
				'data-product-category' => self::text( $args['product_category'] ?? $args['category'] ?? '' ),
				'data-product-price' => self::text( $args['price'] ?? '' ),
				'data-product-stock' => self::text( $args['stock_status'] ?? '' ),
			);
			$attributes = '';
			foreach ( $product_data as $name => $value ) { $attributes .= ' ' . $name . '="' . ( function_exists( 'esc_attr' ) ? esc_attr( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ) ) . '"'; }
			$html = (string) preg_replace( '/<a\b/', '<a' . $attributes, $html, 1 );
		}
		$extra_class = self::text( $args['class'] ?? '' );
		if ( '' !== $extra_class ) {
			$extra_class = function_exists( 'sanitize_html_class' ) ? sanitize_html_class( $extra_class ) : preg_replace( '/[^A-Za-z0-9_-]/', '', $extra_class );
			$html = (string) preg_replace( '/(<a\b[^>]*class="[^"]*)"/i', '$1 ' . $extra_class . '"', $html, 1 );
		}
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- All card values are escaped before substitution.
	}

	/** Convert an untrusted scalar-like input into a safe source string. */
	private static function text( $value ): string {
		return is_scalar( $value ) ? (string) $value : '';
	}
}
