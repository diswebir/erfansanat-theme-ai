<?php
/** Admin list columns, filters and sorting for legacy-compatible content. */

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Adds lightweight, escaped columns and preserves project-list query state. */
final class ListTables implements ModuleInterface {

	/** Register list-table hooks for projects and the remaining legacy screens. */
	public function register(): void {
		add_filter( 'manage_projects_posts_columns', array( self::class, 'project_columns' ) );
		add_action( 'manage_projects_posts_custom_column', array( self::class, 'render_project_column' ), 10, 2 );
		add_action( 'restrict_manage_posts', array( self::class, 'render_project_filters' ), 10, 2 );
		add_filter( 'manage_edit-projects_sortable_columns', array( self::class, 'sortable_project_columns' ) );
		add_action( 'pre_get_posts', array( self::class, 'filter_project_query' ) );

		foreach ( array( 'post', 'page', 'product', 'projects' ) as $post_type ) {
			add_filter(
				'manage_' . $post_type . '_posts_columns',
				static function ( array $columns ) use ( $post_type ): array { return self::columns_for_post_type( $columns, $post_type ); }
			);
			add_action(
				'manage_' . $post_type . '_posts_custom_column',
				static function ( string $column, int $post_id ) use ( $post_type ): void { echo self::render_post_column( $post_type, $column, $post_id ); },
				10,
				2
			);
			add_filter(
				'manage_edit-' . $post_type . '_sortable_columns',
				static function ( array $columns ) use ( $post_type ): array { return self::sortable_columns_for_post_type( $columns, $post_type ); }
			);
		}
		add_filter( 'manage_pages_columns', static function ( array $columns ): array { return self::columns_for_post_type( $columns, 'page' ); } );
		add_action( 'manage_pages_custom_column', static function ( string $column, int $post_id ): void { echo self::render_post_column( 'page', $column, $post_id ); }, 10, 2 );
		add_filter( 'manage_edit-product_cat_columns', array( self::class, 'product_category_columns' ) );
		add_action( 'manage_product_cat_custom_column', array( self::class, 'render_product_category_column' ), 10, 3 );
	}

	/** Add the project-specific columns while preserving WordPress's native ones. */
	public static function project_columns( array $columns ): array {
		return self::columns_for_post_type( $columns, 'projects' );
	}

	/** Return list columns for a post type, inserting theme columns after Title. */
	public static function columns_for_post_type( array $columns, string $post_type ): array {
		$extra = array();
		if ( 'projects' === $post_type ) {
			$extra = array(
				'city'            => __( 'شهر', 'erfansanat-theme' ),
				'project_type'    => __( 'نوع پروژه', 'erfansanat-theme' ),
				'finish-pro-time' => __( 'تاریخ تکمیل', 'erfansanat-theme' ),
				'number2-viewc'   => __( 'بازدید', 'erfansanat-theme' ),
				'same_key_hrefl'  => __( 'کلید زبان', 'erfansanat-theme' ),
			) + $extra;
		} else {
			if ( in_array( $post_type, array( 'post', 'page', 'product' ), true ) ) {
				$extra['same_key_hrefl'] = __( 'کلید زبان', 'erfansanat-theme' );
			}
			if ( in_array( $post_type, array( 'post', 'product' ), true ) ) {
				$extra['number2-viewc'] = __( 'بازدید', 'erfansanat-theme' );
			}
			if ( 'product' === $post_type ) {
				$extra['gs1_id']       = __( 'شناسه GS1', 'erfansanat-theme' );
				$extra['national_code'] = __( 'کد ملی کالا', 'erfansanat-theme' );
			}
		}

		$result = array();
		$inserted = false;
		foreach ( $columns as $key => $label ) {
			$result[ $key ] = $label;
			if ( 'title' === $key ) {
				foreach ( $extra as $extra_key => $extra_label ) { $result[ $extra_key ] = $extra_label; }
				$inserted = true;
			}
		}
		if ( ! $inserted ) { $result += $extra; }
		return $result;
	}

	/** Make completion date and view count sortable in the projects table. */
	public static function sortable_project_columns( array $columns ): array {
		$columns['finish-pro-time'] = 'finish-pro-time';
		$columns['number2-viewc']   = 'number2-viewc';
		return $columns;
	}

	/** Supply the same metadata sorting on the other applicable post-type lists. */
	public static function sortable_columns_for_post_type( array $columns, string $post_type ): array {
		if ( 'projects' === $post_type ) { return self::sortable_project_columns( $columns ); }
		if ( in_array( $post_type, array( 'post', 'product' ), true ) ) { $columns['number2-viewc'] = 'number2-viewc'; }
		return $columns;
	}

	/** Render city and project-type filters only on the projects list screen. */
	public static function render_project_filters( string $post_type, string $which = 'top' ): string {
		if ( 'projects' !== $post_type || 'top' !== $which ) { return ''; }
		$html = '';
		foreach ( array( 'city' => __( 'همه شهرها', 'erfansanat-theme' ), 'project_type' => __( 'همه انواع پروژه', 'erfansanat-theme' ) ) as $taxonomy => $placeholder ) {
			$selected = self::request_slug( $taxonomy );
			$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
			if ( is_wp_error( $terms ) || ! is_array( $terms ) ) { $terms = array(); }
			$html .= '<select name="' . esc_attr( $taxonomy ) . '" id="filter-' . esc_attr( $taxonomy ) . '"><option value="">' . esc_html( $placeholder ) . '</option>';
			foreach ( $terms as $term ) {
				if ( ! is_object( $term ) || ! isset( $term->slug, $term->name ) ) { continue; }
				$is_selected = $selected === (string) $term->slug ? ' selected="selected"' : '';
				$html .= '<option value="' . esc_attr( $term->slug ) . '"' . $is_selected . '>' . esc_html( $term->name ) . '</option>';
			}
			$html .= '</select> ';
		}
		return $html;
	}

	/** Apply only whitelisted taxonomy filters and sort keys; never reset pagination. */
	public static function filter_project_query( $query ): void {
		if ( ! function_exists( 'is_admin' ) || ! is_admin() || ! is_object( $query ) || ! method_exists( $query, 'get' ) || ! method_exists( $query, 'set' ) || ( method_exists( $query, 'is_main_query' ) && ! $query->is_main_query() ) || 'projects' !== $query->get( 'post_type' ) ) {
			return;
		}

		$tax_query = $query->get( 'tax_query' );
		$tax_query = is_array( $tax_query ) ? $tax_query : array();
		$filters = array();
		foreach ( array( 'city', 'project_type' ) as $taxonomy ) {
			$slug = self::request_slug( $taxonomy );
			if ( '' !== $slug ) {
				$filters[] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => array( $slug ) );
			}
		}
		if ( $filters ) {
			if ( ! isset( $tax_query['relation'] ) ) { $tax_query['relation'] = 'AND'; }
			$query->set( 'tax_query', array_merge( $tax_query, $filters ) );
		}

		$orderby = $query->get( 'orderby' );
		if ( in_array( $orderby, array( 'finish-pro-time', 'number2-viewc' ), true ) ) {
			$query->set( 'meta_key', $orderby );
			$query->set( 'orderby', 'number2-viewc' === $orderby ? 'meta_value_num' : 'meta_value' );
		}
	}

	/** Render a project custom column with an empty-value fallback. */
	public static function render_project_column( string $column, int $post_id ): void {
		echo self::render_post_column( 'projects', $column, $post_id );
	}

	/** Return one escaped legacy field/term value for a post list cell. */
	public static function render_post_column( string $post_type, string $column, int $post_id ): string {
		if ( 'city' === $column || 'project_type' === $column ) {
			$terms = get_the_terms( $post_id, $column );
			if ( ! is_array( $terms ) || ! $terms ) { return '&mdash;'; }
			$names = array();
			foreach ( $terms as $term ) { if ( is_object( $term ) && isset( $term->name ) ) { $names[] = esc_html( $term->name ); } }
			return $names ? implode( ', ', $names ) : '&mdash;';
		}
		$allowed = array( 'finish-pro-time', 'number2-viewc', 'same_key_hrefl', 'gs1_id', 'national_code' );
		if ( ! in_array( $column, $allowed, true ) ) { return ''; }
		$value = get_post_meta( $post_id, $column, true );
		if ( 'number2-viewc' === $column && ( ! is_scalar( $value ) || '' === (string) $value ) ) { $value = get_post_meta( $post_id, 'number-viewc', true ); }
		return ! is_scalar( $value ) || '' === trim( (string) $value ) ? '&mdash;' : esc_html( $value );
	}

	/** Add the legacy category thumbnail column without replacing native columns. */
	public static function product_category_columns( array $columns ): array {
		$columns['primary-image'] = __( 'تصویر', 'erfansanat-theme' );
		return $columns;
	}

	/** Output a safely generated thumbnail or a dash for an empty term meta value. */
	public static function render_product_category_column( string $content, string $column, int $term_id ): void {
		if ( 'primary-image' !== $column ) { echo $content; return; }
		$image_id = absint( get_term_meta( $term_id, 'thumbnail', true ) );
		if ( $image_id > 0 && function_exists( 'wp_get_attachment_image' ) ) {
			echo wp_get_attachment_image( $image_id, array( 40, 40 ), false, array( 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Core image helper emits escaped attachment markup.
		} else {
			echo '&mdash;';
		}
	}

	/** Read a GET slug only after unslashing and strict key normalization. */
	private static function request_slug( string $key ): string {
		if ( ! isset( $_GET[ $key ] ) || ! is_string( $_GET[ $key ] ) ) { return ''; }
		$value = wp_unslash( $_GET[ $key ] );
		return sanitize_title( sanitize_text_field( $value ) );
	}
}
