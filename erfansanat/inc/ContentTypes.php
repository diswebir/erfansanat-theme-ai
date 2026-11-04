<?php
/**
 * Native content types and taxonomies.
 *
 * The production site used JetEngine for projects, project types and cities.
 * JetEngine is being removed, so the theme registers those structures itself
 * with the exact permalink settings recorded in DATA_BLUEPRINT.md. Slugs are
 * part of the SEO contract and must not drift.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

defined( 'ABSPATH' ) || exit;

/**
 * Registers `projects`, `services`, `project_type`, `city` and the WooCommerce
 * permalink contract.
 */
final class ContentTypes {

	/**
	 * Projects post type.
	 */
	public const PROJECTS = 'projects';

	/**
	 * Services post type (approved by the client; `smartelement` is one service).
	 */
	public const SERVICES = 'services';

	/**
	 * Project type taxonomy.
	 */
	public const PROJECT_TYPE = 'project_type';

	/**
	 * City taxonomy.
	 */
	public const CITY = 'city';

	/**
	 * Attach hooks.
	 *
	 * @return void
	 */
	public static function register(): void {
		add_action( 'init', array( self::class, 'register_post_types' ), 5 );
		add_action( 'init', array( self::class, 'register_taxonomies' ), 6 );
		add_action( 'init', array( self::class, 'register_meta_keys' ), 7 );
		add_filter( 'woocommerce_taxonomy_args_product_cat', array( self::class, 'filter_product_cat_args' ) );
		add_filter( 'woocommerce_taxonomy_args_product_tag', array( self::class, 'filter_product_tag_args' ) );
		add_filter( 'woocommerce_register_post_type_product', array( self::class, 'filter_product_args' ) );
		add_filter( 'manage_projects_posts_columns', array( self::class, 'project_columns' ) );
		add_action( 'manage_projects_posts_custom_column', array( self::class, 'render_project_column' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( self::class, 'project_filters' ) );
		add_filter( 'parse_query', array( self::class, 'apply_project_filters' ) );
		add_filter( 'manage_post_posts_columns', array( self::class, 'content_columns' ) );
		add_filter( 'manage_page_posts_columns', array( self::class, 'content_columns' ) );
		add_action( 'manage_post_posts_custom_column', array( self::class, 'render_shared_key_column' ), 10, 2 );
		add_action( 'manage_page_posts_custom_column', array( self::class, 'render_shared_key_column' ), 10, 2 );
		add_action( 'manage_post_posts_columns', array( self::class, 'make_shared_key_sortable' ) );
	}

	/**
	 * Register the two custom post types.
	 *
	 * @return void
	 */
	public static function register_post_types(): void {
		if ( ! post_type_exists( self::PROJECTS ) ) {
			register_post_type(
				self::PROJECTS,
				array(
					'labels'          => self::labels( __( 'پروژه‌ها', 'erfansanat' ), __( 'پروژه', 'erfansanat' ) ),
					'public'          => true,
					'has_archive'     => true,
					'rewrite'         => array(
						'slug'       => 'projects',
						'with_front' => false,
					),
					'hierarchical'    => false,
					'menu_icon'       => 'dashicons-screenoptions',
					'supports'        => array( 'title', 'editor', 'thumbnail', 'comments', 'custom-fields', 'revisions' ),
					'show_in_rest'    => true,
					'taxonomies'      => array( self::PROJECT_TYPE, self::CITY ),
					'capability_type' => 'post',
				)
			);
		}

		if ( ! post_type_exists( self::SERVICES ) ) {
			register_post_type(
				self::SERVICES,
				array(
					'labels'          => self::labels( __( 'خدمات', 'erfansanat' ), __( 'خدمت', 'erfansanat' ) ),
					'public'          => true,
					'has_archive'     => true,
					'rewrite'         => array(
						'slug'       => 'services',
						'with_front' => false,
					),
					'hierarchical'    => false,
					'menu_icon'       => 'dashicons-hammer',
					'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions', 'page-attributes' ),
					'show_in_rest'    => true,
					'capability_type' => 'post',
				)
			);
		}
	}

	/**
	 * Register `project_type` and `city` exactly as the blueprint specifies.
	 *
	 * @return void
	 */
	public static function register_taxonomies(): void {
		$shared = array(
			'hierarchical'          => true,
			'public'                => true,
			'show_admin_column'     => true,
			'show_in_rest'          => true,
			'show_in_quick_edit'    => true,
			'update_count_callback' => '_update_post_term_count',
		);

		if ( ! taxonomy_exists( self::PROJECT_TYPE ) ) {
			register_taxonomy(
				self::PROJECT_TYPE,
				array( self::PROJECTS ),
				array_merge(
					$shared,
					array(
						'labels'  => self::taxonomy_labels( __( 'نوع پروژه', 'erfansanat' ) ),
						'rewrite' => array(
							'slug'         => 'project_type',
							'with_front'   => false,
							'hierarchical' => false,
						),
					)
				)
			);
		}

		if ( ! taxonomy_exists( self::CITY ) ) {
			register_taxonomy(
				self::CITY,
				array( self::PROJECTS ),
				array_merge(
					$shared,
					array(
						'labels'  => self::taxonomy_labels( __( 'شهر', 'erfansanat' ) ),
						'rewrite' => array(
							'slug'         => 'city',
							'with_front'   => false,
							'hierarchical' => false,
						),
					)
				)
			);
		}
	}

	/**
	 * Register the historical meta keys with their real types.
	 *
	 * Registering them makes them visible to REST, to the block editor and to
	 * `register_post_meta()`-aware plugins. The spelling of every key is the
	 * historical one and must never be "corrected".
	 *
	 * @return void
	 */
	public static function register_meta_keys(): void {
		$strings = array(
			'gs1_id',
			'national_code',
			'hs_id',
			'isic_id',
			'cpc_id',
			'product_id',
			'zireonvan',
			'english_titl',
			'before-sale-c',
			'product-catalog-file',
			'finish-pro-time',
			'hrefmeta_en',
			'hrefmeta_ar',
			'hrefmeta_fa',
			'same_key_hrefl',
		);

		$types = array(
			'projects' => array( self::PROJECTS, 'post', 'page', 'product' ),
			'post'     => array( 'post', 'page' ),
		);

		foreach ( array( self::PROJECTS, 'post', 'page', 'product' ) as $post_type ) {
			if ( ! post_type_exists( $post_type ) ) {
				continue;
			}
			foreach ( $strings as $key ) {
				register_post_meta(
					$post_type,
					$key,
					array(
						'type'              => 'string',
						'single'            => true,
						'show_in_rest'      => false,
						'sanitize_callback' => 'sanitize_text_field',
						'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
					)
				);
			}
			register_post_meta(
				$post_type,
				'number2-viewc',
				array(
					'type'              => 'integer',
					'single'            => true,
					'show_in_rest'      => false,
					'sanitize_callback' => 'absint',
					'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
				)
			);
		}

		unset( $types );
	}

	/**
	 * Force `product_cat` to keep the live slug and `with_front => true`.
	 *
	 * WooCommerce stores this in its own option; the theme asserts the contract
	 * so a settings change cannot silently break thousands of indexed URLs.
	 *
	 * @param array<string, mixed> $args Taxonomy arguments.
	 * @return array<string, mixed>
	 */
	public static function filter_product_cat_args( array $args ): array {
		$args['hierarchical'] = true;
		$args['rewrite']      = array(
			'slug'         => 'product_cat',
			'with_front'   => true,
			'hierarchical' => true,
		);
		return $args;
	}

	/**
	 * Keep `product-tag` with `with_front => false`.
	 *
	 * @param array<string, mixed> $args Taxonomy arguments.
	 * @return array<string, mixed>
	 */
	public static function filter_product_tag_args( array $args ): array {
		$args['hierarchical'] = false;
		$args['rewrite']      = array(
			'slug'       => 'product-tag',
			'with_front' => false,
		);
		return $args;
	}

	/**
	 * Keep the product permalink at `product` with `with_front => false`.
	 *
	 * @param array<string, mixed> $args Post type arguments.
	 * @return array<string, mixed>
	 */
	public static function filter_product_args( array $args ): array {
		$args['rewrite'] = array(
			'slug'       => 'product',
			'with_front' => false,
		);
		return $args;
	}

	/**
	 * Standard labels for a content type.
	 *
	 * @param string $plural   Plural label.
	 * @param string $singular Singular label.
	 * @return array<string, string>
	 */
	private static function labels( string $plural, string $singular ): array {
		return array(
			'name'               => $plural,
			/* translators: %s: singular content type name. */
			'singular_name'      => $singular,
			/* translators: %s: singular content type name. */
			'add_new_item'       => sprintf( __( 'افزودن %s', 'erfansanat' ), $singular ),
			/* translators: %s: singular content type name. */
			'edit_item'          => sprintf( __( 'ویرایش %s', 'erfansanat' ), $singular ),
			/* translators: %s: singular content type name. */
			'new_item'           => sprintf( __( '%s جدید', 'erfansanat' ), $singular ),
			/* translators: %s: plural content type name. */
			'view_item'          => sprintf( __( 'مشاهدهٔ %s', 'erfansanat' ), $singular ),
			/* translators: %s: plural content type name. */
			'search_items'       => sprintf( __( 'جستجوی %s', 'erfansanat' ), $plural ),
			/* translators: %s: plural content type name. */
			'not_found'          => sprintf( __( '%s یافت نشد', 'erfansanat' ), $plural ),
			/* translators: %s: plural content type name. */
			'not_found_in_trash' => sprintf( __( '%s در زباله‌دان یافت نشد', 'erfansanat' ), $plural ),
			'all_items'          => $plural,
			'menu_name'          => $plural,
		);
	}

	/**
	 * Standard labels for a taxonomy.
	 *
	 * @param string $label Taxonomy label.
	 * @return array<string, string>
	 */
	private static function taxonomy_labels( string $label ): array {
		return array(
			'name'          => $label,
			'singular_name' => $label,
			'menu_name'     => $label,
			'all_items'     => $label,
			'edit_item'     => __( 'ویرایش', 'erfansanat' ),
			'add_new_item'  => __( 'افزودن مورد جدید', 'erfansanat' ),
			'search_items'  => __( 'جستجو', 'erfansanat' ),
		);
	}

	/**
	 * Add city/type columns to the projects list table.
	 *
	 * @param array<string, string> $columns Existing columns.
	 * @return array<string, string>
	 */
	public static function project_columns( array $columns ): array {
		$inserted = array();
		foreach ( $columns as $key => $label ) {
			$inserted[ $key ] = $label;
			if ( 'title' === $key ) {
				$inserted['project_city']       = __( 'شهر', 'erfansanat' );
				$inserted['project_type_label'] = __( 'نوع پروژه', 'erfansanat' );
				$inserted['finish_time']        = __( 'تاریخ تکمیل', 'erfansanat' );
			}
		}
		return $inserted;
	}

	/**
	 * Print the extra project columns.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post id.
	 * @return void
	 */
	public static function render_project_column( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'project_city':
				echo esc_html( self::term_names( $post_id, self::CITY ) );
				break;
			case 'project_type_label':
				echo esc_html( self::term_names( $post_id, self::PROJECT_TYPE ) );
				break;
			case 'finish_time':
				echo esc_html( (string) get_post_meta( $post_id, 'finish-pro-time', true ) );
				break;
		}
	}

	/**
	 * Comma-separated term names for one taxonomy.
	 *
	 * @param int    $post_id  Post id.
	 * @param string $taxonomy Taxonomy name.
	 * @return string
	 */
	private static function term_names( int $post_id, string $taxonomy ): string {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( ! is_array( $terms ) || array() === $terms ) {
			return '—';
		}
		return implode( '، ', wp_list_pluck( $terms, 'name' ) );
	}

	/**
	 * City and project-type dropdowns above the projects list.
	 *
	 * @return void
	 */
	public static function project_filters(): void {
		global $typenow;
		if ( self::PROJECTS !== $typenow ) {
			return;
		}
		foreach ( array( self::CITY, self::PROJECT_TYPE ) as $taxonomy ) {
			$tax = get_taxonomy( $taxonomy );
			if ( ! $tax ) {
				continue;
			}
			$current = isset( $_GET[ $taxonomy ] ) ? sanitize_title( wp_unslash( (string) $_GET[ $taxonomy ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only list filter.
			$terms   = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				)
			);
			if ( ! is_array( $terms ) ) {
				continue;
			}
			printf(
				'<select name="%s" id="%s" class="postform"><option value="">%s</option>',
				esc_attr( $taxonomy ),
				esc_attr( $taxonomy ),
				esc_html( (string) $tax->labels->all_items )
			);
			foreach ( $terms as $term ) {
				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $term->slug ),
					selected( $current, $term->slug, false ),
					esc_html( $term->name )
				);
			}
			echo '</select>';
		}
	}

	/**
	 * Turn the list-table dropdown into a taxonomy query.
	 *
	 * @param \WP_Query $query Current query.
	 * @return void
	 */
	public static function apply_project_filters( $query ): void {
		global $pagenow;
		if ( ! is_admin() || 'edit.php' !== $pagenow || ! $query instanceof \WP_Query ) {
			return;
		}
		$tax_query = array();
		foreach ( array( self::CITY, self::PROJECT_TYPE ) as $taxonomy ) {
			if ( empty( $_GET[ $taxonomy ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only list filter.
				continue;
			}
			$tax_query[] = array(
				'taxonomy' => $taxonomy,
				'field'    => 'slug',
				'terms'    => sanitize_title( wp_unslash( (string) $_GET[ $taxonomy ] ) ), // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			);
		}
		if ( array() !== $tax_query ) {
			$query->set( 'tax_query', $tax_query );
		}
	}

	/**
	 * Add the `same_key_hrefl` column to posts and pages.
	 *
	 * @param array<string, string> $columns Existing columns.
	 * @return array<string, string>
	 */
	public static function content_columns( array $columns ): array {
		$columns['same_key_hrefl'] = __( 'کلید hreflang', 'erfansanat' );
		return $columns;
	}

	/**
	 * Print the shared hreflang key.
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post id.
	 * @return void
	 */
	public static function render_shared_key_column( string $column, int $post_id ): void {
		if ( 'same_key_hrefl' !== $column ) {
			return;
		}
		echo esc_html( (string) get_post_meta( $post_id, 'same_key_hrefl', true ) );
	}

	/**
	 * Mark the shared key column as sortable.
	 *
	 * @param array<string, string> $columns Existing columns.
	 * @return array<string, string>
	 */
	public static function make_shared_key_sortable( array $columns ): array {
		$columns['same_key_hrefl'] = 'same_key_hrefl';
		return $columns;
	}
}
