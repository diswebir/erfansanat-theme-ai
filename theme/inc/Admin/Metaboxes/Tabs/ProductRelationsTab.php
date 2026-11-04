<?php
/** Related project and usage taxonomy selectors for products. */

namespace ErfanSanat\Theme\Admin\Metaboxes\Tabs;

use ErfanSanat\Theme\Meta\MetaRepository;

defined( 'ABSPATH' ) || exit;

/** Renders validated relation fields and handles the restricted search endpoint. */
final class ProductRelationsTab {

	public function register(): void {
		add_action( 'wp_ajax_es_search_posts', array( self::class, 'ajax_search' ) );
	}

	public static function render( int $post_id ): string {
		$projects = self::normalise_ids( es_meta( $post_id, 'project-ids', array() ) );
		$usages = self::normalise_ids( es_meta( $post_id, 'usage_countr', array() ) );
		$html = '<div class="es-product-meta__relations">';
		$html .= '<p><label>' . esc_html__( 'پروژه‌های مرتبط', 'erfansanat-theme' ) . '</label><input class="widefat" type="search" data-es-relations-search="projects" placeholder="' . esc_attr__( 'جست‌وجوی پروژه', 'erfansanat-theme' ) . '"><div data-es-relations-results></div><div data-es-relations-selected>';
		foreach ( $projects as $id ) { $post = get_post( $id ); if ( $post && 'projects' === $post->post_type ) { $html .= self::tag( 'project-ids', $id, get_the_title( $id ) ); } }
		$html .= '</div></p><p><label>' . esc_html__( 'کاربردها', 'erfansanat-theme' ) . '</label><select class="widefat" name="es_product_meta[usage_countr][]" multiple>';
		$terms = get_terms( array( 'taxonomy' => 'project_type', 'hide_empty' => false ) );
		if ( ! is_wp_error( $terms ) && is_array( $terms ) ) { foreach ( $terms as $term ) { if ( ! is_object( $term ) || ! isset( $term->term_id, $term->name ) ) { continue; } $selected = in_array( (int) $term->term_id, $usages, true ) ? ' selected="selected"' : ''; $html .= '<option value="' . esc_attr( (string) $term->term_id ) . '"' . $selected . '>' . esc_html( $term->name ) . '</option>'; } }
		$html .= '</select></p><input type="hidden" name="es_relations_nonce" value="' . esc_attr( wp_create_nonce( 'es_product_relations_search' ) ) . '"></div>';
		return $html;
	}

	private static function tag( string $key, int $id, string $label ): string {
		return '<span class="es-relation-tag" data-es-relation-tag><input type="hidden" name="es_product_meta[' . esc_attr( $key ) . '][]" value="' . esc_attr( (string) $id ) . '">' . esc_html( $label ) . ' <button type="button" data-es-relation-remove aria-label="' . esc_attr__( 'حذف', 'erfansanat-theme' ) . '">×</button></span>';
	}

	/** Save only valid project posts and extant project_type terms. */
	public static function save( int $post_id, array $input ): void {
		$raw_projects = isset( $input['project-ids'] ) && is_array( $input['project-ids'] ) ? $input['project-ids'] : array();
		$project_ids = array();
		foreach ( $raw_projects as $raw_id ) { $id = absint( $raw_id ); if ( $id && 'projects' === get_post_type( $id ) && ! in_array( $id, $project_ids, true ) ) { $project_ids[] = $id; } }
		$raw_terms = isset( $input['usage_countr'] ) && is_array( $input['usage_countr'] ) ? $input['usage_countr'] : array();
		$term_ids = array();
		foreach ( $raw_terms as $raw_id ) { $id = absint( $raw_id ); $term = $id ? get_term( $id, 'project_type' ) : null; if ( $id && $term && ! is_wp_error( $term ) && ! in_array( $id, $term_ids, true ) ) { $term_ids[] = $id; } }
		if ( array_key_exists( 'project-ids', $input ) ) { self::store( $post_id, 'project-ids', $project_ids ); }
		if ( array_key_exists( 'usage_countr', $input ) ) { self::store( $post_id, 'usage_countr', $term_ids ); }
	}

	private static function store( int $post_id, string $key, array $values ): void {
		if ( ! $values ) { MetaRepository::delete( $post_id, $key ); return; }
		$existing = MetaRepository::get( $post_id, $key, array() );
		if ( is_array( $existing ) && array_key_exists( 'item-0', $existing ) ) {
			$rows = array(); foreach ( $values as $index => $value ) { $rows[ 'item-' . $index ] = $value; }
			$values = $rows;
		}
		MetaRepository::set( $post_id, $key, $values );
	}

	/** Restrict AJAX results to published projects and require nonce/capability. */
	public static function ajax_search(): void {
		check_ajax_referer( 'es_product_relations_search', 'nonce' );
		if ( ! current_user_can( 'edit_posts' ) ) { wp_send_json_error( array(), 403 ); }
		$term = isset( $_GET['q'] ) ? sanitize_text_field( wp_unslash( $_GET['q'] ) ) : '';
		$query = new \WP_Query( self::search_args( $term ) );
		$items = array();
		foreach ( $query->posts as $id ) { $items[] = array( 'id' => (int) $id, 'title' => get_the_title( $id ) ); }
		wp_send_json_success( $items );
	}

	/** Query contract is intentionally locked to published projects. */
	public static function search_args( string $term ): array {
		return array( 'post_type' => 'projects', 'post_status' => 'publish', 's' => sanitize_text_field( $term ), 'posts_per_page' => 20, 'no_found_rows' => true, 'fields' => 'ids' );
	}

	/** Decode values saved as arrays, JSON, or serialized JetEngine rows. */
	private static function normalise_ids( $values ): array {
		if ( ! is_array( $values ) ) { return array(); }
		if ( isset( $values['item-0'] ) || array_key_exists( 'item-0', $values ) ) { $values = array_values( $values ); }
		$ids = array();
		foreach ( $values as $value ) { if ( is_array( $value ) ) { $value = $value['id'] ?? $value['value'] ?? ''; } $id = absint( $value ); if ( $id && ! in_array( $id, $ids, true ) ) { $ids[] = $id; } }
		return $ids;
	}
}
