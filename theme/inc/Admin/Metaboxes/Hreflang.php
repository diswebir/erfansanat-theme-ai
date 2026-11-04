<?php
/** Legacy hreflang and language-cluster metadata. */

namespace ErfanSanat\Theme\Admin\Metaboxes;

use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Meta\MetaRepository;

 defined( 'ABSPATH' ) || exit;

/** Stores validated post/term hreflang URLs and emits alternate links. */
final class Hreflang implements ModuleInterface {

	private const POST_TYPES = array( 'post', 'page', 'product', 'projects' );
	private const TAXONOMIES = array( 'category', 'post_tag', 'product_cat', 'product_tag', 'project_type', 'city', 'product_brand' );
	private const URL_KEYS = array( 'hrefmeta_fa', 'hrefmeta_en', 'hrefmeta_ar' );

	public function register(): void {
		foreach ( self::POST_TYPES as $post_type ) { add_action( 'add_meta_boxes_' . $post_type, array( self::class, 'register_box' ) ); }
		add_action( 'save_post', array( self::class, 'save_post' ), 20, 2 );
		foreach ( self::TAXONOMIES as $taxonomy ) { add_action( $taxonomy . '_edit_form_fields', array( self::class, 'term_edit_fields' ), 10, 2 ); add_action( $taxonomy . '_add_form_fields', array( self::class, 'term_add_fields' ) ); add_action( 'edited_' . $taxonomy, array( self::class, 'save_term' ) ); add_action( 'created_' . $taxonomy, array( self::class, 'save_term' ) ); }
		add_action( 'wp_head', array( self::class, 'output_alternates' ), 1 );
		add_action( 'pre_get_posts', array( self::class, 'sort_query' ) );
		add_action( 'pre_get_terms', array( self::class, 'sort_term_query' ) );
		foreach ( self::TAXONOMIES as $taxonomy ) { add_filter( 'manage_edit-' . $taxonomy . '_columns', array( self::class, 'term_columns' ) ); add_filter( 'manage_edit-' . $taxonomy . '_sortable_columns', array( self::class, 'sortable_term_columns' ) ); add_action( 'manage_' . $taxonomy . '_custom_column', array( self::class, 'render_term_column' ), 10, 3 ); }
	}

	public static function register_box( $post ): void {
		$post_type = is_object( $post ) && isset( $post->post_type ) ? $post->post_type : ( function_exists( 'get_current_screen' ) ? get_current_screen()->post_type : '' );
		if ( in_array( $post_type, self::POST_TYPES, true ) ) { add_meta_box( 'es-hreflang', __( 'پیوندهای زبان و خوشه ترجمه', 'erfansanat-theme' ), array( self::class, 'render_post_box' ), $post_type, 'side', 'default' ); }
	}

	public static function render_post_box( $post ): void {
		$id = (int) $post->ID; echo '<input type="hidden" name="es_hreflang_nonce" value="' . esc_attr( wp_create_nonce( 'es_hreflang_' . $id ) ) . '">';
		foreach ( self::URL_KEYS as $key ) { $value = get_post_meta( $id, $key, true ); $value = is_scalar( $value ) ? (string) $value : ''; echo '<p><label>' . esc_html( $key ) . '<input class="widefat" type="url" name="es_hreflang[' . esc_attr( $key ) . ']" value="' . esc_url( $value ) . '"></label></p>'; }
		$value = get_post_meta( $id, 'same_key_hrefl', true ); echo '<p><label>' . esc_html__( 'کلید مشترک ترجمه', 'erfansanat-theme' ) . '<input class="widefat" maxlength="100" name="es_hreflang[same_key_hrefl]" value="' . esc_attr( is_scalar( $value ) ? $value : '' ) . '"></label></p>';
	}

	public static function save_post( int $post_id, $post = null ): bool {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( function_exists( 'wp_is_post_revision' ) && wp_is_post_revision( $post_id ) ) || ! isset( $_POST['es_hreflang_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['es_hreflang_nonce'] ), 'es_hreflang_' . $post_id ) || ! current_user_can( 'edit_post', $post_id ) || ! isset( $_POST['es_hreflang'] ) || ! is_array( $_POST['es_hreflang'] ) ) { return false; }
		$input = wp_unslash( $_POST['es_hreflang'] ); self::save_values( static fn( $key, $value ) => update_post_meta( $post_id, $key, $value ), static fn( $key ) => delete_post_meta( $post_id, $key ), $input ); return true;
	}

	public static function term_edit_fields( $term, $taxonomy = '' ): void {
		if ( ! is_object( $term ) || empty( $term->term_id ) ) { return; }
		echo '<tr class="form-field"><th><label>' . esc_html__( 'پیوندهای زبان', 'erfansanat-theme' ) . '</label></th><td>' . self::term_fields( (int) $term->term_id ) . '</td></tr>';
	}

	public static function term_add_fields(): void { echo '<div class="form-field"><label>' . esc_html__( 'پیوندهای زبان', 'erfansanat-theme' ) . '</label>' . self::term_fields( 0 ) . '</div>'; }

	private static function term_fields( int $term_id ): string {
		$html = '<input type="hidden" name="es_hreflang_term_nonce" value="' . esc_attr( wp_create_nonce( 'es_hreflang_term' ) ) . '">';
		foreach ( self::URL_KEYS as $key ) { $value = $term_id ? get_term_meta( $term_id, $key, true ) : ''; $html .= '<p><label>' . esc_html( $key ) . '<input type="url" name="es_hreflang_term[' . esc_attr( $key ) . ']" value="' . esc_url( is_scalar( $value ) ? $value : '' ) . '"></label></p>'; }
		$value = $term_id ? get_term_meta( $term_id, 'same_key_hrefl', true ) : ''; return $html . '<p><label>same_key_hrefl<input maxlength="100" name="es_hreflang_term[same_key_hrefl]" value="' . esc_attr( is_scalar( $value ) ? $value : '' ) . '"></label></p>';
	}

	public static function save_term( int $term_id ): bool {
		if ( ! isset( $_POST['es_hreflang_term_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['es_hreflang_term_nonce'] ), 'es_hreflang_term' ) || ! current_user_can( 'edit_terms' ) || ! isset( $_POST['es_hreflang_term'] ) || ! is_array( $_POST['es_hreflang_term'] ) ) { return false; }
		$input = wp_unslash( $_POST['es_hreflang_term'] ); self::save_values( static fn( $key, $value ) => MetaRepository::set_term( $term_id, $key, $value ), static fn( $key ) => MetaRepository::delete_term( $term_id, $key ), $input ); return true;
	}

	private static function save_values( callable $update, callable $delete, array $input ): void {
		foreach ( self::URL_KEYS as $key ) { if ( ! array_key_exists( $key, $input ) || ! is_scalar( $input[$key] ) ) { continue; } $raw = trim( (string) $input[$key] ); if ( '' === $raw ) { $delete( $key ); continue; } $url = esc_url_raw( $raw ); if ( self::valid_url( $url ) ) { $update( $key, $url ); } }
		if ( array_key_exists( 'same_key_hrefl', $input ) && is_scalar( $input['same_key_hrefl'] ) ) { $key = sanitize_text_field( (string) $input['same_key_hrefl'] ); if ( '' === $key ) { $delete( 'same_key_hrefl' ); } else { $update( 'same_key_hrefl', function_exists( 'mb_substr' ) ? mb_substr( $key, 0, 100 ) : substr( $key, 0, 100 ) ); } }
	}

	public static function valid_url( string $url ): bool {
		if ( '' === $url || ! function_exists( 'wp_parse_url' ) ) { $parts = parse_url( $url ); } else { $parts = wp_parse_url( $url ); }
		return is_array( $parts ) && isset( $parts['scheme'], $parts['host'] ) && in_array( strtolower( (string) $parts['scheme'] ), array( 'http', 'https' ), true ) && '' !== (string) $parts['host'];
	}

	public static function render_alternates( int $post_id ): string {
		$codes = array( 'hrefmeta_fa' => 'fa', 'hrefmeta_en' => 'en', 'hrefmeta_ar' => 'ar' ); $html = '';
		foreach ( $codes as $key => $lang ) { $url = get_post_meta( $post_id, $key, true ); if ( is_scalar( $url ) && self::valid_url( (string) $url ) ) { $html .= '<link rel="alternate" hreflang="' . esc_attr( $lang ) . '" href="' . esc_url( $url ) . '">' . "\n"; } }
		return $html;
	}

	public static function output_alternates(): void { if ( is_singular( self::POST_TYPES ) ) { echo self::render_alternates( (int) get_queried_object_id() ); } }
	public static function term_columns( array $columns ): array { $columns['same_key_hrefl'] = __( 'کلید ترجمه', 'erfansanat-theme' ); return $columns; }
	public static function sortable_term_columns( array $columns ): array { $columns['same_key_hrefl'] = 'same_key_hrefl'; return $columns; }
	public static function render_term_column( string $content, string $column, int $term_id ): void { if ( 'same_key_hrefl' === $column ) { $value = get_term_meta( $term_id, $column, true ); echo is_scalar( $value ) && '' !== (string) $value ? esc_html( $value ) : '&mdash;'; } }

	public static function sort_query( $query ): void {
		if ( ! is_object( $query ) || ! method_exists( $query, 'get' ) || ! method_exists( $query, 'set' ) || ( function_exists( 'is_admin' ) && ! is_admin() ) || ( method_exists( $query, 'is_main_query' ) && ! $query->is_main_query() ) ) { return; }
		if ( 'same_key_hrefl' === $query->get( 'orderby' ) ) { $query->set( 'meta_key', 'same_key_hrefl' ); $query->set( 'orderby', 'meta_value' ); }
	}

	/** Translate the term-table sortable column into WP_Term_Query meta sorting. */
	public static function sort_term_query( $query ): void {
		if ( ! is_object( $query ) || ! isset( $query->query_vars ) || ! is_array( $query->query_vars ) || 'same_key_hrefl' !== ( $query->query_vars['orderby'] ?? '' ) ) { return; }
		$query->query_vars['meta_key'] = 'same_key_hrefl';
		$query->query_vars['orderby'] = 'meta_value';
	}
}
