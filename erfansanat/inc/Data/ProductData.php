<?php
/**
 * Product page data.
 *
 * The single product page is driven by three sources and never invents a
 * fourth: WooCommerce itself (price, stock, attributes, gallery, reviews),
 * the historical meta keys the migration must preserve, and the theme
 * settings. This class is the only place that knows how those three are read,
 * so a template never guesses a key name or a fallback.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Data;

defined( 'ABSPATH' ) || exit;

/**
 * Read side of the product page.
 */
final class ProductData {

	/**
	 * Official identifiers shown as tiles, in reference order.
	 *
	 * The keys are the exact historical meta keys; the labels match the static
	 * reference so an editor recognises them.
	 *
	 * @return array<string, string>
	 */
	public static function identifier_labels(): array {
		return array(
			'product_id'    => __( 'شناسه فنی محصول در کارخانه', 'erfansanat' ),
			'gs1_id'        => __( 'کد بین‌الملل GS1 (GTIN)', 'erfansanat' ),
			'national_code' => __( 'کد ملی کالا (ایران‌کد)', 'erfansanat' ),
			'hs_id'         => __( 'کد تعرفه هماهنگ گمرکی (HS Code)', 'erfansanat' ),
			'isic_id'       => __( 'کد آیسیک صنعتی (ISIC)', 'erfansanat' ),
			'cpc_id'        => __( 'کد طبقه‌بندی مرکزی محصول (CPC)', 'erfansanat' ),
		);
	}

	/**
	 * Identifiers that actually hold a value.
	 *
	 * An absent key is skipped rather than printed as an empty tile, which is
	 * what keeps a product without registration codes from showing six blanks.
	 *
	 * @param int $product_id Product id.
	 * @return array<string, array{label: string, value: string}>
	 */
	public static function identifiers( int $product_id ): array {
		$rows = array();
		foreach ( self::identifier_labels() as $key => $label ) {
			$value = LegacyMeta::text( $product_id, $key );
			if ( '' === trim( $value ) ) {
				continue;
			}
			$rows[ $key ] = array(
				'label' => $label,
				'value' => $value,
			);
		}
		return $rows;
	}

	/**
	 * Visible attribute rows, label first.
	 *
	 * WooCommerce already knows the label and the printed value of every
	 * attribute, including taxonomy attributes such as `pa_ip-rating`, so the
	 * table is built from the product object instead of from `postmeta`.
	 *
	 * @param \WC_Product $product Product.
	 * @return array<int, array{label: string, value: string}>
	 */
	public static function spec_rows( \WC_Product $product ): array {
		$rows = array();
		foreach ( $product->get_attributes() as $attribute ) {
			if ( ! $attribute instanceof \WC_Product_Attribute || ! $attribute->get_visible() ) {
				continue;
			}
			$name  = (string) $attribute->get_name();
			$label = wc_attribute_label( $name, $product );
			$value = '';

			if ( $attribute->is_taxonomy() ) {
				$names = wc_get_product_terms(
					$product->get_id(),
					$name,
					array(
						'fields' => 'names',
					)
				);
				if ( is_array( $names ) ) {
					$value = implode( '، ', array_map( 'strval', $names ) );
				}
			} else {
				$value = implode( '، ', array_map( 'strval', (array) $attribute->get_options() ) );
			}

			$value = trim( wp_strip_all_tags( $value ) );
			if ( '' === $value ) {
				continue;
			}
			$rows[] = array(
				'label' => (string) $label,
				'value' => $value,
			);
		}
		return $rows;
	}

	/**
	 * The protective rating, used for the chip on the gallery.
	 *
	 * @param \WC_Product $product Product.
	 * @return string
	 */
	public static function ip_rating( \WC_Product $product ): string {
		foreach ( array( 'pa_ip-rating', 'ip-rating' ) as $attribute ) {
			$value = trim( wp_strip_all_tags( (string) $product->get_attribute( $attribute ) ) );
			if ( '' !== $value ) {
				return $value;
			}
		}
		return '';
	}

	/**
	 * Short attribute trio for the summary card.
	 *
	 * Ordered by the reference: the taxonomy attributes an editor actually
	 * filled, capped at three so the card never grows past the design.
	 *
	 * @param \WC_Product $product Product.
	 * @param int         $limit   Maximum rows.
	 * @return array<int, array{label: string, value: string}>
	 */
	public static function summary_specs( \WC_Product $product, int $limit = 3 ): array {
		return array_slice( self::spec_rows( $product ), 0, max( 1, $limit ) );
	}

	/**
	 * Feature bullets from the `product-feature` repeater.
	 *
	 * @param int $product_id Product id.
	 * @return array<int, string>
	 */
	public static function features( int $product_id ): array {
		$rows = array();
		foreach ( LegacyMeta::read_repeater( $product_id, 'product-feature' ) as $row ) {
			$value = trim( (string) ( $row['add-new-feature'] ?? '' ) );
			if ( '' !== $value ) {
				$rows[] = $value;
			}
		}
		return $rows;
	}

	/**
	 * Title/value pairs from the `product-feature2_copy` repeater.
	 *
	 * @param int $product_id Product id.
	 * @return array<int, array{title: string, value: string}>
	 */
	public static function highlights( int $product_id ): array {
		$rows = array();
		foreach ( LegacyMeta::read_repeater( $product_id, 'product-feature2_copy' ) as $row ) {
			$title = trim( (string) ( $row['add-new-feature'] ?? '' ) );
			$value = trim( (string) ( $row['vaule_of_variable'] ?? '' ) );
			if ( '' === $title && '' === $value ) {
				continue;
			}
			$rows[] = array(
				'title' => $title,
				'value' => $value,
			);
		}
		return $rows;
	}

	/**
	 * Usage bullets from the `usage_product` repeater.
	 *
	 * @param int $product_id Product id.
	 * @return array<int, string>
	 */
	public static function usages( int $product_id ): array {
		$rows = array();
		foreach ( LegacyMeta::read_repeater( $product_id, 'usage_product' ) as $row ) {
			$value = trim( (string) ( $row['add-new-usage'] ?? '' ) );
			if ( '' !== $value ) {
				$rows[] = $value;
			}
		}
		return $rows;
	}

	/**
	 * Product FAQ rows.
	 *
	 * `answer_question` may contain line breaks, so it is kept as text and
	 * escaped at print time; nothing here is filtered as HTML.
	 *
	 * @param int $product_id Product id.
	 * @return array<int, array{question: string, answer: string}>
	 */
	public static function faq( int $product_id ): array {
		$rows = array();
		foreach ( LegacyMeta::read_repeater( $product_id, 'faq' ) as $row ) {
			$question = trim( (string) ( $row['name_question'] ?? '' ) );
			$answer   = trim( (string) ( $row['answer_question'] ?? '' ) );
			if ( '' === $question ) {
				continue;
			}
			$rows[] = array(
				'question' => $question,
				'answer'   => $answer,
			);
		}
		return $rows;
	}

	/**
	 * `project_type` terms the product is used in.
	 *
	 * `usage_countr` stores term ids, not slugs, and the migration must not
	 * rewrite them, so a missing term is simply dropped.
	 *
	 * @param int $product_id Product id.
	 * @return array<int, \WP_Term>
	 */
	public static function usage_terms( int $product_id ): array {
		$terms = array();
		foreach ( LegacyMeta::read_ids( $product_id, 'usage_countr' ) as $term_id ) {
			$term = get_term( $term_id, 'project_type' );
			if ( $term instanceof \WP_Term ) {
				$terms[] = $term;
			}
		}
		return $terms;
	}

	/**
	 * Projects the product was used in, from the real `project-ids` relation.
	 *
	 * @param int $product_id Product id.
	 * @param int $limit      Maximum projects.
	 * @return array<int, \WP_Post>
	 */
	public static function related_projects( int $product_id, int $limit = 3 ): array {
		$ids = array_slice( LegacyMeta::read_ids( $product_id, 'project-ids' ), 0, max( 1, $limit ) );
		if ( array() === $ids ) {
			return array();
		}

		$posts = get_posts(
			array(
				'post_type'           => 'projects',
				'post_status'         => 'publish',
				'post__in'            => $ids,
				'orderby'             => 'post__in',
				'posts_per_page'      => count( $ids ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);

		if ( ! is_array( $posts ) || array() === $posts ) {
			return array();
		}

		// One primed cache so the shared card never queries per project.
		update_meta_cache( 'post', wp_list_pluck( $posts, 'ID' ) );
		update_post_thumbnail_cache(
			new \WP_Query(
				array(
					'post__in'       => wp_list_pluck( $posts, 'ID' ),
					'post_type'      => 'any',
					'posts_per_page' => count( $posts ),
					'no_found_rows'  => true,
				)
			)
		);
		update_post_caches( $posts, 'post', true, true );

		return $posts;
	}

	/**
	 * Gallery attachment ids: featured image first, then the legacy gallery.
	 *
	 * @param \WC_Product $product Product.
	 * @return array<int, int>
	 */
	public static function gallery_ids( \WC_Product $product ): array {
		$ids  = array();
		$main = (int) $product->get_image_id();
		if ( $main > 0 ) {
			$ids[] = $main;
		}
		foreach ( LegacyMeta::product_gallery( $product->get_id() ) as $id ) {
			if ( ! in_array( $id, $ids, true ) ) {
				$ids[] = $id;
			}
		}
		return $ids;
	}

	/**
	 * Catalogue file URL when the editor attached one.
	 *
	 * @param int $product_id Product id.
	 * @return string
	 */
	public static function catalog_url( int $product_id ): string {
		$value = LegacyMeta::text( $product_id, 'product-catalog-file' );
		return '' === trim( $value ) ? '' : esc_url_raw( $value );
	}

	/**
	 * Average rating and review count, straight from WooCommerce.
	 *
	 * @param \WC_Product $product Product.
	 * @return array{average: float, count: int}
	 */
	public static function rating( \WC_Product $product ): array {
		return array(
			'average' => (float) $product->get_average_rating(),
			'count'   => (int) $product->get_review_count(),
		);
	}

	/**
	 * Approved reviews, newest first, with their rating meta.
	 *
	 * @param int $product_id Product id.
	 * @param int $limit      Maximum reviews.
	 * @return array<int, array{author: string, date: string, rating: int, content: string, verified: bool}>
	 */
	public static function reviews( int $product_id, int $limit = 10 ): array {
		$comments = get_comments(
			array(
				'post_id'      => $product_id,
				'status'       => 'approve',
				'type'         => 'review',
				'number'       => max( 1, $limit ),
				'orderby'      => 'comment_date_gmt',
				'order'        => 'DESC',
				'hierarchical' => 'flat',
			)
		);

		$rows = array();
		foreach ( (array) $comments as $comment ) {
			if ( ! $comment instanceof \WP_Comment ) {
				continue;
			}
			$rows[] = array(
				'author'   => (string) $comment->comment_author,
				'date'     => (string) mysql2date( 'Y/m/d', $comment->comment_date ),
				'rating'   => (int) get_comment_meta( (int) $comment->comment_ID, 'rating', true ),
				'content'  => (string) $comment->comment_content,
				'verified' => function_exists( 'wc_review_is_from_verified_owner' ) && wc_review_is_from_verified_owner( (int) $comment->comment_ID ),
			);
		}
		return $rows;
	}

	/**
	 * Whether the product may be bought online rather than quoted.
	 *
	 * The historical `true_sale` flag wins over the WooCommerce price, because
	 * the live site sells some catalogue items only on request.
	 *
	 * @param \WC_Product $product Product.
	 * @return bool
	 */
	public static function is_direct_sale( \WC_Product $product ): bool {
		if ( ! LegacyMeta::is_direct_sale( $product->get_id() ) ) {
			return false;
		}
		return $product->is_purchasable() && $product->is_in_stock();
	}

	/**
	 * Categories a product belongs to, for the breadcrumb.
	 *
	 * @param \WC_Product $product Product.
	 * @return array<int, \WP_Term>
	 */
	public static function categories( \WC_Product $product ): array {
		$terms = get_the_terms( $product->get_id(), 'product_cat' );
		return is_array( $terms ) ? array_values( array_filter( $terms, static fn( $term ) => $term instanceof \WP_Term ) ) : array();
	}
}
