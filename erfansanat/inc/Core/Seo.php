<?php
/**
 * SEO audit layer (P08-T019).
 *
 * One head-level owner for canonical URLs and JSON-LD structured data
 * (Organization / Article / Product). Everything defers to an active SEO
 * plugin (SEOPress / RankMath): when one is present the theme prints no
 * canonical of its own (AC-01) and the plugin's meta passes through untouched
 * (SC-02). hreflang stays owned by Core\Hreflang (P04-T017) which already
 * defers to plugins, so the two never conflict (AC-03).
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class Seo {

	private static bool $booted = false;

	/** @var bool<string,bool> per-type one-shot guard (AC-01: never duplicate). */
	private static array $printed = array();

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'wp_head', array( __CLASS__, 'render_canonical' ), 2 );
		add_action( 'wp_head', array( __CLASS__, 'render_jsonld' ), 20 );
	}

	/**
	 * Whether an SEO plugin owns the head (AC-01 / SC-02). Filterable so
	 * integrations and tests can force the behaviour.
	 */
	public static function plugin_active(): bool {
		if ( defined( 'SEOPRESS_VERSION' ) || class_exists( 'RankMath' ) || function_exists( 'rank_math' ) ) {
			return true;
		}
		return (bool) apply_filters( 'es_seo_plugin_active', false );
	}

	/**
	 * AC-01: the canonical URL the THEME must print for the current context,
	 * or '' when someone else owns it. WordPress core already prints
	 * rel_canonical() for singular pages (incl. static front pages), so the
	 * theme only fills the gap — front page in posts mode and term archives —
	 * and never duplicates the core tag.
	 */
	public static function canonical_url(): string {
		if ( self::plugin_active() || is_singular() ) {
			return '';
		}
		$url = '';
		if ( is_front_page() || is_home() ) {
			$url = home_url( '/' );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			$link = $term instanceof \WP_Term ? get_term_link( $term ) : '';
			$url  = is_wp_error( $link ) ? '' : (string) $link;
		}
		/**
		 * Filter the canonical URL for the current context.
		 *
		 * @param string $url
		 */
		return (string) apply_filters( 'es_seo_canonical', $url );
	}

	/**
	 * AC-01 / SC-02: exactly one <link rel="canonical"> per head. With an SEO
	 * plugin active the theme stays silent and also unhooks core's tag so the
	 * plugin alone owns the canonical.
	 */
	public static function render_canonical(): void {
		if ( self::plugin_active() ) {
			remove_action( 'wp_head', 'rel_canonical', 10 );
			return;
		}
		$url = self::canonical_url();
		if ( '' === $url ) {
			return;
		}
		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $url ) );
	}

	/**
	 * AC-02 builder: Organization for the front page.
	 *
	 * @param array{name?:string,url?:string,logo?:string,description?:string} $fields Overrides.
	 */
	public static function organization_schema( array $fields = array() ): array {
		return array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Organization',
			'name'        => (string) ( $fields['name'] ?? get_bloginfo( 'name' ) ),
			'url'         => (string) ( $fields['url'] ?? home_url( '/' ) ),
			'description' => (string) ( $fields['description'] ?? get_bloginfo( 'description' ) ),
		);
	}

	/**
	 * AC-02 builder: Article for singular posts.
	 *
	 * @param array{headline?:string,url?:string,datePublished?:string,dateModified?:string,author?:string,image?:string} $fields Overrides.
	 */
	public static function article_schema( array $fields = array() ): array {
		$schema = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'Article',
			'headline'      => (string) ( $fields['headline'] ?? get_the_title() ),
			'url'           => (string) ( $fields['url'] ?? get_permalink() ),
			'datePublished' => (string) ( $fields['datePublished'] ?? get_the_date( DATE_W3C ) ),
			'dateModified'  => (string) ( $fields['dateModified'] ?? get_the_modified_date( DATE_W3C ) ),
			'author'        => array(
				'@type' => 'Person',
				'name'  => (string) ( $fields['author'] ?? get_the_author() ),
			),
			'mainEntityOfPage' => (string) ( $fields['url'] ?? get_permalink() ),
		);
		if ( ! empty( $fields['image'] ) ) {
			$schema['image'] = (string) $fields['image'];
		}
		return $schema;
	}

	/**
	 * AC-02 builder: Product with a valid Offer. Data normally comes from
	 * WooCommerce through the es_seo_product_data filter (method-safe in the
	 * same way the rest of the theme guards optional integrations).
	 *
	 * @param array{name?:string,url?:string,image?:string,sku?:string,price?:string,currency?:string,availability?:string} $fields Data.
	 */
	public static function product_schema( array $fields = array() ): array {
		$schema = array(
			'@context' => 'https://schema.org',
			'@type'    => 'Product',
			'name'     => (string) ( $fields['name'] ?? get_the_title() ),
			'url'      => (string) ( $fields['url'] ?? get_permalink() ),
			'offers'   => array(
				'@type'         => 'Offer',
				'url'           => (string) ( $fields['url'] ?? get_permalink() ),
				'price'         => (string) ( $fields['price'] ?? '' ),
				'priceCurrency' => (string) ( $fields['currency'] ?? 'IRR' ),
				'availability'  => 'https://schema.org/' . ( 'OutofStock' === ( $fields['availability'] ?? '' ) ? 'OutOfStock' : 'InStock' ),
			),
		);
		foreach ( array( 'image', 'sku' ) as $es_key ) {
			if ( ! empty( $fields[ $es_key ] ) ) {
				$schema[ $es_key ] = (string) $fields[ $es_key ];
			}
		}
		return $schema;
	}

	/**
	 * Clear the per-request one-shot guards (CLI/tests render head twice).
	 */
	public static function reset_printed(): void {
		self::$printed = array();
	}

	private static function print_jsonld( string $type, array $schema ): void {
		if ( ! empty( self::$printed[ $type ] ) ) {
			return; // AC-01: one structured-data block per type per page.
		}
		$json = wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
		if ( ! is_string( $json ) || JSON_ERROR_NONE !== json_last_error() ) {
			return;
		}
		self::$printed[ $type ] = true;
		echo '<script type="application/ld+json">' . $json . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- json_encode output.
	}

	/**
	 * AC-02: pick the right schema block for the current context:
	 * front page → Organization; singular product → Product (via
	 * es_seo_product_data / WooCommerce); singular post → Article.
	 */
	public static function render_jsonld(): void {
		if ( is_front_page() || is_home() ) {
			self::print_jsonld( 'Organization', self::organization_schema() );
			return;
		}
		if ( ! is_singular() ) {
			return;
		}
		// Products first: integrations supply validated data through the filter.
		if ( 'product' === get_post_type() || has_filter( 'es_seo_product_data' ) ) {
			$fields = apply_filters( 'es_seo_product_data', array() );
			if ( is_array( $fields ) && ! empty( $fields ) ) {
				self::print_jsonld( 'Product', self::product_schema( $fields ) );
				return;
			}
		}
		if ( 'post' === get_post_type() ) {
			self::print_jsonld( 'Article', self::article_schema( array(
				'datePublished' => (string) get_post_time( DATE_W3C, true ),
				'dateModified'  => (string) get_post_modified_time( DATE_W3C, true ),
			) ) );
		}
	}

	/**
	 * AC-03 audit helper: hreflang tags for the current context must stay
	 * unique and valid. Ownership lives in Core\Hreflang (already
	 * plugin-aware); this only reports conflicts, it never prints.
	 *
	 * @param array<string,string> $langs lang => URL map as Hreflang renders.
	 */
	public static function audit_hreflang( array $langs ): bool {
		$valid = array( 'fa', 'en', 'ar', 'x-default' );
		foreach ( $langs as $lang => $url ) {
			if ( ! in_array( $lang, $valid, true ) || '' === (string) $url ) {
				return false;
			}
		}
		return count( $langs ) === count( array_unique( array_keys( $langs ) ) );
	}
}
