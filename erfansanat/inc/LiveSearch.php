<?php
/**
 * Live search endpoint.
 *
 * The header panel is a plain GET form, so search works with JavaScript off.
 * This handler is the enhancement: it returns the same results the search
 * template renders, as JSON, so the panel can show them without a page load.
 *
 * Two decisions worth stating:
 *
 * 1. It reads `$_GET`, not `$_POST`, because the endpoint is a *read*. That
 *    keeps it cacheable and means a bookmark or a back button still works.
 * 2. It searches only published, publicly viewable content via `WP_Query`, so
 *    drafts, private pages and password-protected posts can never leak through
 *    the live panel even though they are excluded from the main query everywhere
 *    else. A search endpoint that leaks drafts is a real vulnerability, not a
 *    cosmetic bug.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

/**
 * The live-search AJAX handler and its nonce.
 */
final class LiveSearch {

	/**
	 * Action name, shared by the PHP handler and the script.
	 */
	public const ACTION = 'erfansanat_live_search';

	/**
	 * Shortest query the endpoint will answer.
	 */
	private const MIN_LENGTH = 2;

	/**
	 * Post types searched, in the order the panel groups them.
	 *
	 * @var array<string, string>
	 */
	private const TYPES = array(
		'product'  => 'محصولات',
		'projects' => 'پروژه‌ها',
		'services' => 'خدمات',
		'post'     => 'مقالات',
		'page'     => 'صفحه‌ها',
	);

	/**
	 * Hook the endpoint.
	 *
	 * `wp_ajax_nopriv_` is registered explicitly: a logged-out visitor is the
	 * common case, and without it the panel would only work for signed-in users.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'wp_ajax_' . self::ACTION, array( self::class, 'handle' ) );
		add_action( 'wp_ajax_nopriv_' . self::ACTION, array( self::class, 'handle' ) );
	}

	/**
	 * Configuration the script needs, localised onto the theme bundle.
	 *
	 * @return array<string, mixed>
	 */
	public static function script_data(): array {
		return array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'action'   => self::ACTION,
			'nonce'    => wp_create_nonce( self::ACTION ),
			'minChars' => self::MIN_LENGTH,
			'groups'   => self::TYPES,
			'strings'  => array(
				'empty'     => __( 'نتیجه‌ای پیدا نشد.', 'erfansanat' ),
				'searching' => __( 'در حال جستجو…', 'erfansanat' ),
				'all'       => __( 'دیدن همهٔ نتایج', 'erfansanat' ),
				'error'     => __( 'جستجو در دسترس نیست. از دکمهٔ جستجو استفاده کنید.', 'erfansanat' ),
			),
		);
	}

	/**
	 * Answer one search request.
	 *
	 * @return void
	 */
	public static function handle(): void {
		// The nonce protects against a third-party page driving this endpoint in
		// a visitor's browser; it is not an authorisation check.
		if ( ! check_ajax_referer( self::ACTION, 'nonce', false ) ) {
			wp_send_json_error( array( 'message' => __( 'درخواست معتبر نیست.', 'erfansanat' ) ), 403 );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- verified above.
		$raw  = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['q'] ) ) : '';
		$term = trim( $raw );

		// A one-letter query matches almost every row and costs a full scan.
		if ( mb_strlen( $term ) < self::MIN_LENGTH ) {
			wp_send_json_success(
				array(
					'query'   => $term,
					'total'   => 0,
					'groups'  => array(),
					'message' => __( 'دست‌کم دو حرف بنویسید.', 'erfansanat' ),
				)
			);
		}

		$query = new \WP_Query(
			array(
				's'                      => $term,
				'post_type'              => array_keys( self::TYPES ),
				'post_status'            => 'publish',
				'posts_per_page'         => 24,
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => false,
				'suppress_filters'       => false,
				'update_post_term_cache' => true,
				'update_post_meta_cache' => true,
			)
		);

		$groups = array();
		$total  = 0;

		foreach ( $query->posts as $result ) {
			if ( ! $result instanceof \WP_Post ) {
				continue;
			}

			$type = (string) $result->post_type;
			if ( ! isset( self::TYPES[ $type ] ) ) {
				continue;
			}

			// Never return something the visitor could not open.
			if ( 'publish' !== $result->post_status || post_password_required( $result ) ) {
				continue;
			}

			$groups[ $type ][] = array(
				'title'     => html_entity_decode( (string) get_the_title( $result ), ENT_QUOTES, 'UTF-8' ),
				'url'       => (string) get_permalink( $result ),
				'excerpt'   => self::excerpt( $result ),
				'thumbnail' => self::thumbnail( $result ),
				'price'     => self::price( $result ),
			);
			++$total;
		}

		// Keep the grouping order stable regardless of the query's result order.
		$ordered = array();
		foreach ( self::TYPES as $type => $label ) {
			if ( empty( $groups[ $type ] ) ) {
				continue;
			}
			$ordered[] = array(
				'type'  => $type,
				'label' => $label,
				'items' => $groups[ $type ],
				'count' => count( $groups[ $type ] ),
			);
		}

		wp_send_json_success(
			array(
				'query'     => $term,
				'total'     => $total,
				'groups'    => $ordered,
				'searchUrl' => add_query_arg( 's', rawurlencode( $term ), home_url( '/' ) ),
				'message'   => 0 === $total ? __( 'نتیجه‌ای پیدا نشد.', 'erfansanat' ) : '',
			)
		);
	}

	/**
	 * A short, plain-text excerpt for one result.
	 *
	 * @param \WP_Post $post Result.
	 * @return string
	 */
	private static function excerpt( \WP_Post $post ): string {
		$text = trim( wp_strip_all_tags( (string) $post->post_excerpt ) );
		if ( '' === $text ) {
			$text = wp_strip_all_tags( (string) $post->post_content );
		}
		return wp_trim_words( $text, 16, '…' );
	}

	/**
	 * A card-sized thumbnail, or the theme's own fallback.
	 *
	 * @param \WP_Post $post Result.
	 * @return string
	 */
	private static function thumbnail( \WP_Post $post ): string {
		$image = (string) get_the_post_thumbnail_url( $post, 'erfansanat-card' );
		if ( '' !== $image ) {
			return $image;
		}
		if ( 'product' === $post->post_type && function_exists( 'wc_placeholder_img_src' ) ) {
			return (string) wc_placeholder_img_src( 'erfansanat-card' );
		}
		return '';
	}

	/**
	 * The product price as text, or an empty string for other types.
	 *
	 * @param \WP_Post $post Result.
	 * @return string
	 */
	private static function price( \WP_Post $post ): string {
		if ( 'product' !== $post->post_type || ! function_exists( 'wc_get_product' ) ) {
			return '';
		}
		$product = wc_get_product( (int) $post->ID );
		if ( ! $product instanceof \WC_Product ) {
			return '';
		}
		return (string) $product->get_price() > '' ? wp_strip_all_tags( (string) $product->get_price_html() ) : '';
	}
}
