<?php
/**
 * Native archive filters.
 *
 * Both the shop and the project archive carry client-side filters. Those are an
 * enhancement; this class is the part that must work with JavaScript off, on a
 * slow connection, and for a crawler. It reads four query arguments and applies
 * them to the main query:
 *
 *     ?product_cat=<slug>      shop, one category
 *     ?es_price=under-500      shop, price band (`under-500`, `500-2000`, `over-2000`)
 *     ?city=<slug>             projects, one city
 *     ?project_type=<slug>     projects, one project type
 *     ?es_sort=popular|rating|newest   both, ordering
 *
 * Only the main query on a real archive is touched: a widget, a related-posts
 * query or an admin list must never inherit a visitor's filter.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Server-side filtering for the shop and the project archive.
 */
final class ArchiveFilters {

	/**
	 * Price bands offered by the shop. Keys are what the URL carries, values a
	 * `[min, max]` pair in the store currency (max of `0` means unbounded).
	 *
	 * @var array<string, array{0: float, 1: float, 2: string}>
	 */
	private const PRICE_BANDS = array(
		'under-3' => array( 0.0, 3000000.0, 'زیر ۳ میلیون تومان' ),
		'3-6'     => array( 3000000.0, 6000000.0, '۳ تا ۶ میلیون تومان' ),
		'over-6'  => array( 6000000.0, 0.0, 'بالای ۶ میلیون تومان' ),
	);

	/**
	 * Attach the hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'pre_get_posts', array( self::class, 'apply' ) );
	}

	/**
	 * The price bands, for the filter UI.
	 *
	 * @return array<string, array{0: float, 1: float, 2: string}>
	 */
	public static function price_bands(): array {
		return self::PRICE_BANDS;
	}

	/**
	 * The sort options, keyed by the value the URL carries.
	 *
	 * @return array<string, string>
	 */
	public static function sort_options(): array {
		return array(
			'default' => __( 'پیش‌فرض', 'erfansanat' ),
			'newest'  => __( 'جدیدترین', 'erfansanat' ),
			'popular' => __( 'محبوب‌ترین', 'erfansanat' ),
			'rating'  => __( 'بیشترین امتیاز', 'erfansanat' ),
		);
	}

	/**
	 * Read one filter from the query string, sanitised.
	 *
	 * @param string $key Query argument.
	 * @return string
	 */
	public static function param( string $key ): string {
		// Read-only. A filter changes what is displayed, not what is stored, so
		// there is nothing for a nonce to protect; the value is sanitised on the
		// next line and used only inside a query argument.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Read-only filter; sanitised by sanitize_title() below.
		$raw = isset( $_GET[ $key ] ) ? (string) wp_unslash( $_GET[ $key ] ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		return sanitize_title( trim( $raw ) );
	}

	/**
	 * Apply the visitor's filters to the main archive query.
	 *
	 * @param \WP_Query $query Query being prepared.
	 * @return void
	 */
	public static function apply( $query ): void {
		if ( ! $query instanceof \WP_Query || is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( ! $query->is_post_type_archive( 'product' ) && ! $query->is_post_type_archive( 'projects' ) && ! $query->is_tax( array( 'city', 'project_type', 'product_cat' ) ) ) {
			return;
		}

		$is_shop     = $query->is_post_type_archive( 'product' ) || $query->is_tax( 'product_cat' );
		$is_projects = $query->is_post_type_archive( 'projects' ) || $query->is_tax( array( 'city', 'project_type' ) );

		$tax_query = (array) $query->get( 'tax_query' ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Reading an existing clause.

		if ( $is_shop ) {
			$category = self::param( 'product_cat' );
			if ( '' !== $category && ! $query->is_tax( 'product_cat' ) ) {
				$tax_query[] = array(
					'taxonomy' => 'product_cat',
					'field'    => 'slug',
					'terms'    => $category,
				);
			}

			$band = self::param( 'es_price' );
			if ( '' !== $band && isset( self::PRICE_BANDS[ $band ] ) ) {
				$price = self::PRICE_BANDS[ $band ];
				$meta  = array(
					array(
						'key'     => '_price',
						'value'   => $price[0],
						'compare' => '>=',
						'type'    => 'NUMERIC',
					),
				);
				if ( $price[1] > 0 ) {
					$meta[] = array(
						'key'     => '_price',
						'value'   => $price[1],
						'compare' => '<=',
						'type'    => 'NUMERIC',
					);
				}
				$query->set( 'meta_query', array_merge( (array) $query->get( 'meta_query' ), array( $meta ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_query_meta_value -- Deliberate price filter.
			}
		}

		if ( $is_projects ) {
			$city = self::param( 'city' );
			if ( '' !== $city && ! $query->is_tax( 'city' ) ) {
				$tax_query[] = array(
					'taxonomy' => 'city',
					'field'    => 'slug',
					'terms'    => $city,
				);
			}

			$type = self::param( 'project_type' );
			if ( '' !== $type && ! $query->is_tax( 'project_type' ) ) {
				$tax_query[] = array(
					'taxonomy' => 'project_type',
					'field'    => 'slug',
					'terms'    => $type,
				);
			}
		}

		if ( array() !== $tax_query ) {
			$query->set( 'tax_query', $tax_query ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Deliberate archive filter.
		}

		$sort = self::param( 'es_sort' );
		switch ( $sort ) {
			case 'newest':
				$query->set( 'orderby', 'date' );
				$query->set( 'order', 'DESC' );
				break;
			case 'popular':
				if ( $is_projects ) {
					$query->set( 'meta_key', 'number2-viewc' ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_query_meta_value
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'order', 'DESC' );
				} else {
					$query->set( 'meta_key', 'total_sales' ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_query_meta_value
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'order', 'DESC' );
				}
				break;
			case 'rating':
				if ( $is_shop ) {
					$query->set( 'meta_key', '_wc_average_rating' ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_query_meta_value
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'order', 'DESC' );
				} else {
					$query->set( 'meta_key', 'number2-viewc' ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_query_meta_value
					$query->set( 'orderby', 'meta_value_num' );
					$query->set( 'order', 'DESC' );
				}
				break;
		}
	}

	/**
	 * The active filters, so the archive can show removable chips.
	 *
	 * @return array<int, array{key: string, value: string, label: string, url: string}>
	 */
	public static function active(): array {
		$rows = array();

		foreach (
			array(
				'product_cat'  => 'دسته‌بندی',
				'es_price'     => 'قیمت',
				'city'         => 'شهر',
				'project_type' => 'نوع پروژه',
			) as $key => $label
		) {
			$value = self::param( $key );
			if ( '' === $value ) {
				continue;
			}

			$text = $value;
			if ( 'es_price' === $key && isset( self::PRICE_BANDS[ $value ] ) ) {
				$text = self::PRICE_BANDS[ $value ][2];
			} elseif ( 'product_cat' === $key || 'city' === $key || 'project_type' === $key ) {
				$taxonomy = 'product_cat' === $key ? 'product_cat' : $key;
				$term     = get_term_by( 'slug', $value, $taxonomy );
				if ( $term instanceof \WP_Term ) {
					$text = $term->name;
				}
			}

			$rows[] = array(
				'key'   => $key,
				'value' => $value,
				'label' => $label . ': ' . $text,
				'url'   => esc_url( remove_query_arg( $key ) ),
			);
		}

		return $rows;
	}
}
