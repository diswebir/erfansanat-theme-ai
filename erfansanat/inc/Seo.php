<?php
/**
 * Search-engine output the theme owns.
 *
 * A WordPress site ships a title, a feed and a sitemap, and nothing else that
 * makes a page findable. This class adds the three things every page needs and
 * is careful about the one thing that matters more than any of them: it never
 * competes with a plugin.
 *
 * - Everything is skipped when Yoast, Rank Math or SEOPress is active. Two
 *   canonical tags or two meta descriptions on one page is worse than none,
 *   because a crawler has to guess which one to believe.
 * - The description comes from the content, not a template: an excerpt if there
 *   is one, otherwise a trimmed lead, otherwise the term description, otherwise
 *   the site tagline. A description that says "welcome to our website" on 14
 *   pages is a duplicate-content signal, not a description.
 * - The canonical is the permalink, with the paginated variant kept on page 2+
 *   so deep archive pages do not all claim to be page 1.
 * - Structured data is only added where it is true: a Product on a product, an
 *   Article on a post, a BreadcrumbList where a trail actually exists.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\PostData;

/**
 * Canonical URLs, meta descriptions and JSON-LD.
 */
final class Seo {

	/**
	 * Plugins that already own the head. If one is active, this class is a no-op.
	 *
	 * @var array<int, string>
	 */
	private const PLUGINS = array(
		'wordpress-seo/wp-seo.php',
		'wordpress-seo-premium/wp-seo-premium.php',
		'seo-by-rank-math/rank-math.php',
		'autodescription/autodescription.php',
		'wp-seopress/seopress.php',
		'all-in-one-seo-pack/all_in_one_seo_pack.php',
	);

	/**
	 * Attach the hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		if ( self::plugin_owns_head() ) {
			return;
		}

		// Core's `rel_canonical` is left in place. Phase 1's contract requires it
		// to stay hooked, and removing it to print an identical tag would be a
		// pointless risk. Core only prints on singular requests, so this fills
		// exactly the gap it leaves: archives, the blog index and search.
		add_action( 'wp_head', array( self::class, 'print_canonical' ), 3 );
		add_action( 'wp_head', array( self::class, 'print_description' ), 4 );
		add_action( 'wp_head', array( self::class, 'print_structured_data' ), 30 );
		add_action( 'wp_head', array( self::class, 'print_open_graph' ), 31 );
	}

	/**
	 * Whether an SEO plugin is handling the head.
	 *
	 * @return bool
	 */
	public static function plugin_owns_head(): bool {
		$active = (array) get_option( 'active_plugins', array() );

		foreach ( self::PLUGINS as $plugin ) {
			if ( in_array( $plugin, $active, true ) ) {
				return true;
			}
		}

		return (bool) apply_filters( 'erfansanat_seo_plugin_active', false );
	}

	/**
	 * Print the canonical link.
	 *
	 * @return void
	 */
	public static function print_canonical(): void {
		// Core already emitted one for this request; a second tag would make a
		// crawler choose between two claims about the same page.
		if ( is_singular() ) {
			return;
		}

		$url = self::canonical_url();
		if ( '' === $url ) {
			return;
		}

		printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $url ) );
	}

	/**
	 * The canonical URL for the current request.
	 *
	 * @return string
	 */
	public static function canonical_url(): string {
		if ( is_front_page() ) {
			return home_url( '/' );
		}

		if ( is_singular() ) {
			// Returned for callers other than the head (Open Graph uses it),
			// even though `print_canonical()` leaves the tag to core here.
			return (string) get_permalink();
		}

		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			return $term instanceof \WP_Term ? (string) \ErfanSanat\Theme\term_url( $term ) : '';
		}

		if ( is_post_type_archive() ) {
			$paged = (int) get_query_var( 'paged' );
			if ( $paged > 1 ) {
				// Page 2 is a different set of items; pointing it at page 1 would
				// tell a crawler the deeper pages are duplicates and drop them.
				return (string) get_pagenum_link( $paged );
			}
			$type = get_query_var( 'post_type' );
			if ( is_array( $type ) ) {
				$type = (string) reset( $type );
			}
			return (string) get_post_type_archive_link( (string) $type );
		}

		if ( is_search() ) {
			// A search result set is not a page anyone should bookmark as canonical.
			return add_query_arg( 's', rawurlencode( (string) get_search_query() ), home_url( '/' ) );
		}

		if ( is_home() ) {
			$page_for_posts = (int) get_option( 'page_for_posts' );
			if ( $page_for_posts > 0 ) {
				$paged = (int) get_query_var( 'paged' );
				$base  = (string) get_permalink( $page_for_posts );
				return $paged > 1 ? trailingslashit( $base ) . user_trailingslashit( 'page/' . $paged ) : $base;
			}
			return home_url( '/' );
		}

		// Archives keep their own paginated URL: page 2 is a different page.
		if ( is_archive() ) {
			$paged = (int) get_query_var( 'paged' );
			if ( $paged > 1 ) {
				return (string) get_pagenum_link( $paged );
			}
		}

		return '';
	}

	/**
	 * Print the meta description.
	 *
	 * @return void
	 */
	public static function print_description(): void {
		$description = self::description();
		if ( '' === $description ) {
			return;
		}

		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $description ) );
	}

	/**
	 * A description that is about this page and nobody else's.
	 *
	 * @return string
	 */
	public static function description(): string {
		$raw = '';

		if ( is_singular() ) {
			$post = get_queried_object();
			if ( $post instanceof \WP_Post ) {
				$raw = trim( (string) $post->post_excerpt );
				if ( '' === $raw ) {
					$raw = (string) $post->post_content;
				}
				$raw = wp_strip_all_tags( strip_shortcodes( $raw ) );
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term instanceof \WP_Term ) {
				$raw = (string) $term->description;
				if ( '' === $raw ) {
					$raw = sprintf(
						/* translators: 1: taxonomy label, 2: term name, 3: site name. */
						__( '%1$s %2$s — نمونه‌کارها و اطلاعات فنی %3$s', 'erfansanat' ),
						(string) get_taxonomy( $term->taxonomy )->labels->singular_name,
						$term->name,
						(string) get_bloginfo( 'name' )
					);
				}
			}
		} elseif ( is_post_type_archive() ) {
			$type = get_queried_object();
			if ( $type instanceof \WP_Post_Type && '' !== (string) $type->description ) {
				$raw = (string) $type->description;
			}
		} elseif ( is_home() ) {
			$page_for_posts = (int) get_option( 'page_for_posts' );
			if ( $page_for_posts > 0 ) {
				$raw = wp_strip_all_tags( (string) get_post_field( 'post_excerpt', $page_for_posts ) );
				if ( '' === $raw ) {
					$raw = wp_strip_all_tags( (string) get_post_field( 'post_content', $page_for_posts ) );
				}
			}
		} elseif ( is_search() ) {
			$raw = sprintf(
				/* translators: %s: search term. */
				__( 'نتایج جستجو برای «%s» در محصولات و پروژه‌های عرفان صنعت.', 'erfansanat' ),
				(string) get_search_query()
			);
		}

		$raw = trim( (string) preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $raw ) ) );
		$raw = wp_trim_words( $raw, 28, '…' );

		// A description too short to say anything is worse than the site tagline.
		if ( mb_strlen( $raw ) < 40 ) {
			$raw = (string) get_bloginfo( 'description' );
		}
		if ( mb_strlen( $raw ) < 40 ) {
			$raw = sprintf(
				/* translators: %s: site name. */
				__( '%s — طراحی، تولید و اجرای نورپردازی شهری و روشنایی معماری.', 'erfansanat' ),
				(string) get_bloginfo( 'name' )
			);
		}

		$raw = mb_substr( $raw, 0, 320 );

		return (string) apply_filters( 'erfansanat_meta_description', $raw );
	}

	/**
	 * Print JSON-LD for the current page.
	 *
	 * @return void
	 */
	public static function print_structured_data(): void {
		$graph = array();

		$org  = array(
			'@type' => 'Organization',
			'@id'   => home_url( '/#organization' ),
			'name'  => (string) get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		);
		$logo = \ErfanSanat\Theme\Frontend\SectionParts::logo_url();
		if ( '' !== $logo ) {
			$org['logo'] = $logo;
		}
		$phone = \ErfanSanat\Theme\Frontend\SectionParts::phone();
		if ( '' !== $phone ) {
			$org['telephone'] = $phone;
		}
		$email = \ErfanSanat\Theme\Frontend\SectionParts::email();
		if ( '' !== $email ) {
			$org['email'] = $email;
		}
		$graph[] = $org;

		$graph[] = array(
			'@type'           => 'WebSite',
			'@id'             => home_url( '/#website' ),
			'url'             => home_url( '/' ),
			'name'            => (string) get_bloginfo( 'name' ),
			'publisher'       => array( '@id' => home_url( '/#organization' ) ),
			'inLanguage'      => 'fa-IR',
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		);

		if ( is_singular( 'product' ) && function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( (int) get_queried_object_id() );
			if ( $product instanceof \WC_Product ) {
				$item = array(
					'@type'       => 'Product',
					'@id'         => (string) get_permalink() . '#product',
					'name'        => $product->get_name(),
					'url'         => (string) get_permalink(),
					'description' => wp_trim_words( wp_strip_all_tags( self::product_text( $product ) ), 40, '…' ),
					'sku'         => (string) $product->get_sku(),
					'brand'       => array( '@id' => home_url( '/#organization' ) ),
					'offers'      => array(
						'@type'         => 'Offer',
						'price'         => (string) $product->get_price(),
						'priceCurrency' => function_exists( 'get_woocommerce_currency' ) ? (string) get_woocommerce_currency() : 'IRR',
						'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
						'url'           => (string) get_permalink(),
					),
				);

				$image = (string) get_the_post_thumbnail_url( $product->get_id(), 'large' );
				if ( '' !== $image ) {
					$item['image'] = $image;
				}
				$rating = (float) $product->get_average_rating();
				if ( $rating > 0 ) {
					$item['aggregateRating'] = array(
						'@type'       => 'AggregateRating',
						'ratingValue' => $rating,
						'reviewCount' => (int) $product->get_review_count(),
					);
				}

				$graph[] = $item;
			}
		}

		if ( is_singular( 'post' ) ) {
			$post = get_queried_object();
			if ( $post instanceof \WP_Post ) {
				$article = array(
					'@type'          => 'Article',
					'@id'            => (string) get_permalink() . '#article',
					'headline'       => wp_trim_words( (string) get_the_title( $post ), 12, '…' ),
					'datePublished'  => (string) get_the_date( 'c', $post ),
					'dateModified'   => (string) get_the_modified_date( 'c', $post ),
					'author'         => array(
						'@type' => 'Person',
						'name'  => (string) get_the_author_meta( 'display_name', (int) $post->post_author ),
					),
					'publisher'      => array( '@id' => home_url( '/#organization' ) ),
					'inLanguage'     => 'fa-IR',
					'wordCount'      => PostData::word_count( (int) $post->ID ),
					'articleSection' => \ErfanSanat\Theme\Data\PostData::categories( (int) $post->ID )[0]['name'] ?? '',
				);

				$image = (string) get_the_post_thumbnail_url( $post, 'large' );
				if ( '' !== $image ) {
					$article['image'] = $image;
				}

				$graph[] = $article;
			}
		}

		if ( is_singular( 'projects' ) || is_singular( 'services' ) ) {
			$post = get_queried_object();
			if ( $post instanceof \WP_Post ) {
				$graph[] = array(
					'@type'       => 'CreativeWork',
					'@id'         => (string) get_permalink() . '#work',
					'name'        => (string) get_the_title( $post ),
					'url'         => (string) get_permalink(),
					'description' => self::description(),
					'creator'     => array( '@id' => home_url( '/#organization' ) ),
					'inLanguage'  => 'fa-IR',
				);
			}
		}

		if ( is_front_page() ) {
			$graph[] = array(
				'@type'           => 'BreadcrumbList',
				'@id'             => home_url( '/#breadcrumb' ),
				'itemListElement' => array(
					array(
						'@type'    => 'ListItem',
						'position' => 1,
						'name'     => (string) get_bloginfo( 'name' ),
						'item'     => home_url( '/' ),
					),
				),
			);
		}

		if ( array() === $graph ) {
			return;
		}

		$payload = array(
			'@context' => 'https://schema.org',
			'@graph'   => $graph,
		);

		echo '<script type="application/ld+json">'
			. wp_json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
			. '</script>' . "\n";
	}

	/**
	 * The best text a product has for a description or a search snippet.
	 *
	 * A short description is written for exactly this purpose, so it wins; the
	 * long description is the fallback rather than the other way round.
	 *
	 * @param \WC_Product $product Product.
	 * @return string
	 */
	private static function product_text( \WC_Product $product ): string {
		$short = (string) $product->get_short_description();
		if ( '' !== trim( wp_strip_all_tags( $short ) ) ) {
			return $short;
		}
		return (string) $product->get_description();
	}

	/**
	 * Open Graph and Twitter tags, so a shared link is not a bare URL.
	 *
	 * @return void
	 */
	public static function print_open_graph(): void {
		$title       = wp_get_document_title();
		$description = self::description();
		$url         = self::canonical_url();
		$image       = '';

		if ( is_singular() ) {
			$image = (string) get_the_post_thumbnail_url( get_queried_object_id(), 'large' );
		}
		if ( '' === $image ) {
			$image = \ErfanSanat\Theme\Frontend\SectionParts::asset( 'images/light-element.jpg' );
		}

		$tags = array(
			'og:type'        => is_singular( 'post' ) ? 'article' : 'website',
			'og:site_name'   => (string) get_bloginfo( 'name' ),
			'og:title'       => $title,
			'og:description' => $description,
			'og:url'         => $url,
			'og:image'       => $image,
			'og:locale'      => 'fa_IR',
			'twitter:card'   => 'summary_large_image',
		);

		foreach ( $tags as $property => $content ) {
			if ( '' === (string) $content ) {
				continue;
			}
			printf(
				'<meta property="%s" content="%s" />' . "\n",
				esc_attr( $property ),
				esc_attr( (string) $content )
			);
		}
	}
}
