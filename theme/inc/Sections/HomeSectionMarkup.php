<?php
/** Render static-reference homepage sections with editable, escaped data tokens. */

namespace ErfanSanat\Theme\Sections;

defined( 'ABSPATH' ) || exit;

/** Keeps the reference DOM as a template while values live in Section Manager data. */
final class HomeSectionMarkup {

	/** Render one trusted static fragment with dashboard-controlled text and URLs. */
	public static function render( string $section, array $args = array() ): void {
		if ( ! preg_match( '/^[a-z][a-z0-9-]*$/', $section ) ) { return; }
		$root = dirname( __DIR__, 2 );
		$html_path = $root . '/template-parts/home/static/' . $section . '.html';
		$json_path = __DIR__ . '/Schema/home-content/' . $section . '.json';
		if ( ! is_file( $html_path ) || ! is_file( $json_path ) ) { return; }
		$html = file_get_contents( $html_path );
		$defaults = json_decode( (string) file_get_contents( $json_path ), true );
		if ( ! is_string( $html ) || ! is_array( $defaults ) ) { return; }
		$data = is_array( $args['data'] ?? null ) ? $args['data'] : array();
		$overrides = json_decode( is_scalar( $data['content_json'] ?? null ) ? (string) $data['content_json'] : '', true );
		$values = is_array( $overrides ) ? $overrides : $defaults;
		self::apply_contact_options( $section, $defaults, $values );
		if ( 'catalog' === $section ) { self::apply_catalog_file( $html, $defaults, $values, $data ); }
		if ( 'about' === $section ) { self::apply_about_stats( $defaults, $values, $data ); }
		if ( 'services' === $section ) { self::apply_services_repeater( $html, $defaults, $values, $data ); }
		if ( 'clients' === $section && is_array( $overrides ) && empty( $overrides ) ) { return; }
		if ( 'blog-preview' === $section && class_exists( \ErfanSanat\Theme\Query\HomeQueries::class ) ) {
			$articles = \ErfanSanat\Theme\Query\HomeQueries::articles( 4 );
			if ( ! $articles ) { return; }
			self::apply_recent_articles( $html, $defaults, $values, $articles );
		}
		if ( 'products' === $section && class_exists( \ErfanSanat\Theme\Query\HomeQueries::class ) ) { self::apply_product_term_cards( $html, $defaults, $values ); }
		$video_missing = false;
		foreach ( $defaults as $key => $definition ) {
			if ( ! is_array( $definition ) ) { continue; }
			$entry = is_array( $values[ $key ] ?? null ) ? $values[ $key ] : array();
			$value = $entry['value'] ?? $definition['value'] ?? '';
			$value = is_scalar( $value ) ? (string) $value : '';
			if ( 'hero' === $section && 'src' === ( $definition['attribute'] ?? '' ) && str_contains( (string) ( $definition['value'] ?? '' ), 'hero-video.mp4' ) && '' === trim( (string) $value ) ) { $video_missing = true; }
		}
		if ( 'hero' === $section ) {
			self::apply_product_categories( $html, $defaults, $values );
			if ( $video_missing ) {
				foreach ( $defaults as $key => $definition ) {
					if ( 'data-scene-is-video' === ( $definition['attribute'] ?? '' ) && 'true' === ( $definition['value'] ?? '' ) ) { $values[ $key ]['value'] = 'false'; }
				}
			}
		}
		foreach ( $defaults as $key => $definition ) {
			if ( ! is_array( $definition ) || ! isset( $definition['kind'] ) ) { continue; }
			$entry = is_array( $values[ $key ] ?? null ) ? $values[ $key ] : array();
			$raw = $entry['value'] ?? $definition['value'] ?? '';
			$raw = is_scalar( $raw ) ? (string) $raw : '';
			$escaped = self::escape_value( $raw, (string) $definition['kind'] );
			$html = str_replace( '__ES_' . $key . '__', $escaped, $html );
		}
		$html = (string) preg_replace( '/\s+(?:custom|data-reactroot|data-reactid)=(?:"[^"]*"|\'[^\']*\')/i', '', $html );
		if ( $video_missing ) {
			$html = (string) preg_replace( '/<video\b[^>]*data-home1-inline-video="true"[^>]*>.*?<\/video>/is', '', $html, 1 );
			$html = (string) preg_replace( '/<button\b[^>]*data-home1-play-trigger="true"[^>]*>.*?<\/button>/is', '', $html, 1 );
		}
		if ( class_exists( \ErfanSanat\Theme\Nav\LegacyMarkup::class ) ) { $html = \ErfanSanat\Theme\Nav\LegacyMarkup::rewrite_links( $html ); }
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Every data token is escaped and all remaining template markup is trusted.
	}

	/** Bind service repeater rows to the preserved five-button JS contract. */
	private static function apply_services_repeater( string &$html, array $defaults, array &$values, array $data ): void {
		$rows = is_array( $data['services'] ?? null ) ? array_values( $data['services'] ) : array();
		if ( ! $rows ) { return; }
		$token_map = array(
			array( 'A000', 'A001', 'A002', 'T005', 'T006', 'A018', 'A023' ),
			array( 'A003', 'A004', 'A005', 'T008', 'T009', 'A019', 'A024' ),
			array( 'A006', 'A007', 'A008', 'T011', 'T012', 'A020', 'A025' ),
			array( 'A009', 'A010', 'A011', 'T014', 'T015', 'A021', 'A026' ),
			array( 'A012', 'A013', 'A014', 'T017', 'T018', 'A022', 'A027' ),
		);
		$rows = array_slice( $rows, 0, count( $token_map ) );
		$total = count( $rows );
		foreach ( $token_map as $index => $tokens ) {
			if ( ! isset( $rows[ $index ] ) || ! is_array( $rows[ $index ] ) ) {
				$html = (string) preg_replace( '/<button\\b(?=[^>]*data-service-row="' . $index . '")[^>]*>.*?<\\/button>/is', '', $html, 1 );
				continue;
			}
			$row = $rows[ $index ];
			$title = is_scalar( $row['title'] ?? null ) ? (string) $row['title'] : '';
			$description = is_scalar( $row['description'] ?? null ) ? (string) $row['description'] : '';
			$image = is_scalar( $row['image'] ?? null ) ? (string) $row['image'] : '';
			if ( '' !== $title ) {
				foreach ( array( $tokens[0], $tokens[2], $tokens[3] ) as $key ) { self::set_value( $values, $defaults, $key, $title ); }
			}
			if ( '' !== $description ) {
				foreach ( array( $tokens[4], $tokens[5] ) as $key ) { self::set_value( $values, $defaults, $key, $description ); }
			}
			if ( '' !== $image ) { self::set_value( $values, $defaults, $tokens[1], $image ); }
			$counter = self::persian_digits( sprintf( '%02d', $index + 1 ) . ' / ' . (string) $total );
			self::set_value( $values, $defaults, $tokens[6], $counter );
		}
	}

	/** Only expose the catalog download action when a Media Library file is configured. */
	private static function apply_catalog_file( string &$html, array $defaults, array &$values, array $data ): void {
		$url = is_scalar( $data['catalog_file'] ?? null ) ? trim( (string) $data['catalog_file'] ) : '';
		if ( '' === $url || '0' === $url ) {
			if ( preg_match_all( '/<a\\b[^>]*>.*?<\\/a>/is', $html, $cards ) ) {
				foreach ( $cards[0] as $card ) { if ( str_contains( $card, '__ES_A003__' ) ) { $html = str_replace( $card, '', $html ); break; } }
			}
			return;
		}
		self::set_value( $values, $defaults, 'A003', $url );
		$html = (string) preg_replace_callback( '/<a\\b(?=[^>]*href="__ES_A003__")[^>]*>/i', static function ( $match ) {
			$tag = rtrim( $match[0], '>' );
			return $tag . ' target="_blank" rel="noopener noreferrer">';
		}, $html, 1 );
	}

	/** Apply editable repeater statistics, with a published-project count override. */
	private static function apply_about_stats( array $defaults, array &$values, array $data ): void {
		$stats = is_array( $data['stats'] ?? null ) ? array_values( $data['stats'] ) : array();
		if ( ! empty( $data['use_project_count'] ) && function_exists( 'wp_count_posts' ) ) {
			$counts = wp_count_posts( 'projects' );
			if ( is_object( $counts ) && isset( $counts->publish ) ) {
				$stats[0] = is_array( $stats[0] ?? null ) ? $stats[0] : array();
				$stats[0]['value'] = (string) max( 0, (int) $counts->publish );
			}
		}
		$token_map = array(
			array( 'A003', 'A004', 'T014', 'T015', 'T016' ),
			array( 'A005', 'A006', 'T017', 'T018', 'T019' ),
			array( 'A007', 'A008', 'T020', 'T021', 'T022' ),
			array( 'A009', 'A010', 'T023', 'T024', 'T025' ),
		);
		foreach ( $token_map as $index => $tokens ) {
			$row = $stats[ $index ] ?? null;
			if ( ! is_array( $row ) ) { continue; }
			$value = is_scalar( $row['value'] ?? null ) ? (string) $row['value'] : '';
			$suffix = is_scalar( $row['suffix'] ?? null ) ? (string) $row['suffix'] : '';
			if ( '' !== $value ) {
				self::set_value( $values, $defaults, $tokens[0], $value );
				self::set_value( $values, $defaults, $tokens[2], self::persian_digits( $value ) . $suffix );
			}
			if ( '' !== $suffix ) { self::set_value( $values, $defaults, $tokens[1], $suffix ); }
			foreach ( array( 3 => 'label', 4 => 'description' ) as $token_index => $field ) {
				if ( is_scalar( $row[ $field ] ?? null ) && '' !== (string) $row[ $field ] ) { self::set_value( $values, $defaults, $tokens[ $token_index ], (string) $row[ $field ] ); }
			}
		}
	}

	/** Render ASCII digits in the same Persian form used by the static reference. */
	private static function persian_digits( string $value ): string {
		return strtr( $value, array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' ) );
	}

	/** Keep shared contact details sourced from the global settings tab. */
	private static function apply_contact_options( string $section, array $defaults, array &$values ): void {
		if ( ! function_exists( 'es_option' ) ) { return; }
		$phone = (string) es_option( 'header.phone', '031-91091011' );
		$email = (string) es_option( 'header.email', 'info@erfansanat.com' );
		$address = (string) es_option( 'footer.address', '' );
		foreach ( $defaults as $key => $definition ) {
			if ( ! is_array( $definition ) ) { continue; }
			$value = (string) ( $definition['value'] ?? '' );
			if ( 'contact-cta' === $section ) {
				if ( 'T008' === $key && '' !== $phone ) { self::set_value( $values, $defaults, $key, $phone ); }
				elseif ( 'T011' === $key && '' !== $email ) { self::set_value( $values, $defaults, $key, $email ); }
				elseif ( 'url' === ( $definition['kind'] ?? '' ) && str_starts_with( $value, 'tel:' ) && '' !== $phone ) { self::set_value( $values, $defaults, $key, 'tel:' . $phone ); }
				elseif ( 'url' === ( $definition['kind'] ?? '' ) && str_starts_with( $value, 'mailto:' ) && '' !== $email ) { self::set_value( $values, $defaults, $key, 'mailto:' . $email ); }
			} elseif ( 'map-contact' === $section && 'T002' === $key && '' !== $address ) {
				self::set_value( $values, $defaults, $key, $address );
			}
		}
	}

	/** Replace static product tiles with cached product-category terms when the catalog is available. */
	private static function apply_product_term_cards( string &$html, array $defaults, array &$values ): void {
		if ( ! function_exists( 'get_term_link' ) ) { return; }
		$terms = \ErfanSanat\Theme\Query\HomeQueries::product_categories( 6 );
		if ( ! is_array( $terms ) || ! $terms || ! preg_match_all( '/<a\\b[^>]*>.*?<\\/a>/is', $html, $matches ) ) { return; }
		$term_index = 0;
		foreach ( $matches[0] as $card ) {
			preg_match_all( '/__ES_([AT]\\d+)__/', $card, $tokens );
			$keys = $tokens[1] ?? array();
			$has_image = false; $link_key = '';
			foreach ( $keys as $key ) {
				if ( 'src' === ( $defaults[ $key ]['attribute'] ?? '' ) ) { $has_image = true; }
				if ( 'href' === ( $defaults[ $key ]['attribute'] ?? '' ) ) { $link_key = $key; }
			}
			if ( ! $has_image || '' === $link_key ) { continue; }
			$term = $terms[ $term_index ] ?? null;
			if ( ! is_object( $term ) && ! is_array( $term ) ) { $html = str_replace( $card, '', $html ); continue; }
			++$term_index;
			$name = is_object( $term ) ? (string) ( $term->name ?? '' ) : (string) ( $term['name'] ?? '' );
			$link = get_term_link( $term );
			if ( is_string( $link ) && '' !== $link ) { self::set_value( $values, $defaults, $link_key, $link ); }
			$alt_text = '';
			foreach ( $keys as $key ) {
				$definition = $defaults[ $key ] ?? array();
				if ( 'alt' === ( $definition['attribute'] ?? '' ) ) { $alt_text = (string) ( $definition['value'] ?? '' ); self::set_value( $values, $defaults, $key, $name ); }
				if ( 'src' === ( $definition['attribute'] ?? '' ) && function_exists( 'get_term_meta' ) && function_exists( 'wp_get_attachment_image_url' ) ) {
					$term_id = is_object( $term ) ? (int) ( $term->term_id ?? 0 ) : (int) ( $term['term_id'] ?? 0 );
					$image_id = $term_id ? (int) get_term_meta( $term_id, 'thumbnail_id', true ) : 0;
					$image = $image_id ? wp_get_attachment_image_url( $image_id, 'medium' ) : false;
					if ( is_string( $image ) && '' !== $image ) { self::set_value( $values, $defaults, $key, $image ); }
				}
			}
			if ( '' !== $name ) {
				foreach ( $keys as $key ) {
					if ( 'text' === ( $defaults[ $key ]['kind'] ?? '' ) && (string) ( $defaults[ $key ]['value'] ?? '' ) === $alt_text ) { self::set_value( $values, $defaults, $key, $name ); }
				}
			}
		}
	}

	/** Fill the four blog cards from cached published posts without changing their markup. */
	private static function apply_recent_articles( string $html, array $defaults, array &$values, array $articles ): void {
		if ( ! preg_match_all( '/<a\\b[^>]*>.*?<\\/a>/is', $html, $cards ) ) { return; }
		$card_index = 0;
		foreach ( $cards[0] as $card ) {
			preg_match_all( '/__ES_([AT]\\d+)__/', $card, $tokens );
			$keys = $tokens[1] ?? array();
			$link_key = '';
			foreach ( $keys as $key ) { if ( 'href' === ( $defaults[ $key ]['attribute'] ?? '' ) ) { $link_key = $key; break; } }
			$has_image_token = false;
			foreach ( $keys as $key ) { if ( 'src' === ( $defaults[ $key ]['attribute'] ?? '' ) ) { $has_image_token = true; break; } }
			if ( '' === $link_key || ! $has_image_token ) { continue; }
			$article = $articles[ $card_index ] ?? null;
			if ( ! is_object( $article ) ) { ++$card_index; continue; }
			++$card_index;
			$post_id = (int) ( $article->ID ?? 0 );
			$title = function_exists( 'get_the_title' ) ? (string) get_the_title( $article ) : (string) ( $article->post_title ?? '' );
			$url = function_exists( 'get_permalink' ) ? get_permalink( $article ) : '';
			if ( '' !== $title && is_string( $url ) && '' !== $url ) { self::set_value( $values, $defaults, $link_key, $url ); }
			$thumbnail = $post_id && function_exists( 'get_the_post_thumbnail_url' ) ? get_the_post_thumbnail_url( $post_id, 'large' ) : '';
			$alt_key = '';
			foreach ( $keys as $key ) {
				$definition = $defaults[ $key ] ?? array();
				if ( 'src' === ( $definition['attribute'] ?? '' ) && is_string( $thumbnail ) && '' !== $thumbnail ) { self::set_value( $values, $defaults, $key, $thumbnail ); }
				if ( 'alt' === ( $definition['attribute'] ?? '' ) ) { $alt_key = $key; if ( '' !== $title ) { self::set_value( $values, $defaults, $key, $title ); } }
			}
			$category = '';
			if ( $post_id && function_exists( 'get_the_category' ) ) { $terms = get_the_category( $post_id ); $category = is_array( $terms ) && isset( $terms[0]->name ) ? (string) $terms[0]->name : ''; }
			$date = '';
			if ( function_exists( 'get_the_date' ) ) { $date = (string) get_the_date( 'j F Y', $article ); }
			$excerpt = function_exists( 'get_the_excerpt' ) ? (string) get_the_excerpt( $article ) : '';
			$alt_text = '' !== $alt_key ? (string) ( $defaults[ $alt_key ]['value'] ?? '' ) : '';
			foreach ( $keys as $key ) {
				$definition = $defaults[ $key ] ?? array();
				if ( 'text' !== ( $definition['kind'] ?? '' ) ) { continue; }
				$original = (string) ( $definition['value'] ?? '' );
				if ( '' !== $title && $original === $alt_text ) { self::set_value( $values, $defaults, $key, $title ); }
				elseif ( '' !== $category && 'مجله علمی' === $original ) { self::set_value( $values, $defaults, $key, $category ); }
				elseif ( '' !== $date && preg_match( '/(?:مرداد|تیر|شهریور|مهر|آبان|آذر|دی|بهمن|اسفند|فروردین|اردیبهشت|خرداد)/u', $original ) ) { self::set_value( $values, $defaults, $key, $date ); }
				elseif ( '' !== $excerpt && strlen( $original ) > 80 ) { self::set_value( $values, $defaults, $key, $excerpt ); }
			}
		}
	}

	/** Store a scalar override only when the token exists in the trusted schema map. */
	private static function set_value( array &$values, array $defaults, string $key, string $value ): void {
		if ( ! isset( $defaults[ $key ] ) || ! is_array( $defaults[ $key ] ) ) { return; }
		$values[ $key ] = $defaults[ $key ];
		$values[ $key ]['value'] = $value;
	}

	/** Replace the static category shelf values with live product_cat terms when available. */
	private static function apply_product_categories( string $html, array $defaults, array &$values ): void {
		if ( ! class_exists( \ErfanSanat\Theme\Query\HomeQueries::class ) || ! function_exists( 'get_term_link' ) ) { return; }
		$terms = \ErfanSanat\Theme\Query\HomeQueries::product_categories( 7 );
		if ( ! is_array( $terms ) || ! $terms ) { return; }
		$position = strpos( $html, 'data-home1-categories-grid="true"' );
		$start = false === $position ? false : strrpos( substr( $html, 0, $position ), '<div' );
		if ( false === $start || ! preg_match_all( '/<\\/?div\\b[^>]*>/i', substr( $html, $start ), $tags, PREG_OFFSET_CAPTURE ) ) { return; }
		$depth = 0; $end = null;
		foreach ( $tags[0] as $tag ) {
			$depth += str_starts_with( $tag[0], '</' ) ? -1 : 1;
			if ( 0 === $depth ) { $end = $start + $tag[1] + strlen( $tag[0] ); break; }
		}
		if ( null === $end ) { return; }
		$block = substr( $html, $start, $end - $start );
		if ( ! preg_match_all( '/<a\\b[^>]*>.*?<\\/a>/is', $block, $cards ) ) { return; }
		foreach ( array_slice( $cards[0], 0, min( count( $terms ), 7 ) ) as $index => $card ) {
			$term = $terms[ $index ];
			$name = is_object( $term ) ? (string) ( $term->name ?? '' ) : (string) ( $term['name'] ?? '' );
			$description = is_object( $term ) ? (string) ( $term->description ?? '' ) : (string) ( $term['description'] ?? '' );
			$link = get_term_link( $term );
			if ( '' !== $name && is_string( $link ) && '' !== $link && preg_match( '/href="(__ES_A\\d+__)"/', $card, $href ) ) {
				$key = substr( $href[1], 5, -2 );
				if ( isset( $defaults[ $key ] ) ) { $values[ $key ] = $defaults[ $key ]; $values[ $key ]['value'] = $link; }
			}
			preg_match_all( '/__ES_T\\d+__/', $card, $text_tokens );
			$text_keys = array_map( static fn( $token ) => substr( $token, 5, -2 ), $text_tokens[0] );
			if ( isset( $text_keys[0], $defaults[ $text_keys[0] ] ) && '' !== $name ) { $values[ $text_keys[0] ] = $defaults[ $text_keys[0] ]; $values[ $text_keys[0] ]['value'] = $name; }
			if ( isset( $text_keys[1], $defaults[ $text_keys[1] ] ) && '' !== $description ) { $values[ $text_keys[1] ] = $defaults[ $text_keys[1] ]; $values[ $text_keys[1] ]['value'] = $description; }
			$count = is_object( $term ) ? (int) ( $term->count ?? 0 ) : (int) ( $term['count'] ?? 0 );
			if ( isset( $text_keys[2], $defaults[ $text_keys[2] ] ) && $count > 0 ) { $values[ $text_keys[2] ] = $defaults[ $text_keys[2] ]; $values[ $text_keys[2] ]['value'] = function_exists( 'number_format_i18n' ) ? number_format_i18n( $count, 0 ) : (string) $count; }
		}
	}

	/** Escape text, URL, or non-URL attribute values according to their template context. */
	private static function escape_value( string $value, string $kind ): string {
		if ( 'url' === $kind ) {
			if ( str_starts_with( $value, 'asset:' ) ) {
				$asset = substr( $value, 6 );
				$base = function_exists( 'get_template_directory_uri' ) ? rtrim( get_template_directory_uri(), '/' ) : '';
				$value = $base . '/assets/' . ltrim( $asset, '/' );
			}
			return function_exists( 'esc_url' ) ? esc_url( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
		}
		if ( 'text' === $kind ) {
			return function_exists( 'esc_html' ) ? esc_html( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
		}
		return function_exists( 'esc_attr' ) ? esc_attr( $value ) : htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}
}
