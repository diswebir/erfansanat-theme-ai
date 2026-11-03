<?php
/**
 * Projects admin list: city / project-type filter dropdowns, taxonomy columns
 * and a sortable `same_key_hrefl` (hreflang key) column. Queries are kept light
 * and all output is escaped.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

final class ProjectColumns {

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'restrict_manage_posts', array( __CLASS__, 'filter_dropdowns' ), 10, 1 );
		add_filter( 'manage_projects_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_projects_posts_custom_column', array( __CLASS__, 'render_column' ), 10, 2 );
		add_filter( 'manage_edit-projects_sortable_columns', array( __CLASS__, 'sortable_columns' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'apply_sort' ) );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	/**
	 * Add taxonomy + hreflang columns.
	 *
	 * @param array<string,string> $columns Existing columns.
	 * @return array<string,string>
	 */
	public static function columns( $columns ) {
		$columns['city']           = __( 'شهر', 'erfansanat' );
		$columns['project_type']   = __( 'نوع پروژه', 'erfansanat' );
		$columns['same_key_hrefl'] = __( 'کلید hreflang', 'erfansanat' );
		return $columns;
	}

	/**
	 * Make the hreflang key column sortable (AC-02).
	 *
	 * @param array<string,string> $columns Sortable columns.
	 * @return array<string,string>
	 */
	public static function sortable_columns( $columns ) {
		$columns['same_key_hrefl'] = 'same_key_hrefl';
		return $columns;
	}

	/**
	 * Render city / project-type filter dropdowns on the projects screen (AC-01).
	 *
	 * @param string $post_type Current list-table post type.
	 */
	public static function filter_dropdowns( $post_type ): void {
		if ( 'projects' !== $post_type ) {
			return;
		}

		self::taxonomy_dropdown( 'city', __( 'همه شهرها', 'erfansanat' ) );
		self::taxonomy_dropdown( 'project_type', __( 'همه انواع پروژه', 'erfansanat' ) );
	}

	private static function taxonomy_dropdown( string $taxonomy, string $all_label ): void {
		$selected = isset( $_GET[ $taxonomy ] ) ? sanitize_text_field( wp_unslash( $_GET[ $taxonomy ] ) ) : '';

		wp_dropdown_categories(
			array(
				'taxonomy'        => $taxonomy,
				'name'            => $taxonomy,
				'show_option_all' => $all_label,
				'value_field'     => 'slug',
				'selected'        => $selected,
				'hide_empty'      => false,
				'hierarchical'    => true,
				'orderby'         => 'name',
			)
		);
	}

	/**
	 * Render a custom column value (escaped).
	 *
	 * @param string $column  Column key.
	 * @param int    $post_id Post id.
	 */
	public static function render_column( $column, $post_id ): void {
		if ( 'city' === $column || 'project_type' === $column ) {
			$terms = get_the_terms( $post_id, $column );
			if ( is_array( $terms ) ) {
				$names = array_map(
					static function ( $term ) {
						return esc_html( $term->name );
					},
					$terms
				);
				echo implode( '، ', $names );
			}
			return;
		}

		if ( 'same_key_hrefl' === $column ) {
			echo esc_html( (string) get_post_meta( $post_id, 'same_key_hrefl', true ) );
		}
	}

	/**
	 * Translate the hreflang sortable orderby into a meta query (AC-03, light).
	 *
	 * @param \WP_Query $query Current query.
	 */
	public static function apply_sort( $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( 'projects' !== $query->get( 'post_type' ) ) {
			return;
		}
		if ( 'same_key_hrefl' === $query->get( 'orderby' ) ) {
			$query->set( 'meta_key', 'same_key_hrefl' );
			$query->set( 'orderby', 'meta_value' );
		}
	}
}
