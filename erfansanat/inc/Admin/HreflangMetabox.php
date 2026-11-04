<?php
/**
 * Hreflang metabox: hrefmeta_fa / hrefmeta_en / hrefmeta_ar URLs plus the shared
 * `same_key_hrefl` key, on posts, pages, products, projects and the main
 * taxonomies. URLs are validated; the shared key gets a sortable admin column.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

final class HreflangMetabox {

	public const URL_KEYS   = array( 'hrefmeta_fa', 'hrefmeta_en', 'hrefmeta_ar' );
	public const SHARED_KEY = 'same_key_hrefl';
	public const POST_TYPES = array( 'post', 'page', 'product', 'projects' );
	public const TAXONOMIES = array( 'product_cat', 'project_type', 'city' );

	private static bool $booted = false;

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;

		add_action( 'add_meta_boxes', array( __CLASS__, 'register_metaboxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_post_meta' ), 10, 1 );

		foreach ( self::TAXONOMIES as $taxonomy ) {
			add_action( "{$taxonomy}_add_form_fields", array( __CLASS__, 'render_term_add_fields' ) );
			add_action( "{$taxonomy}_edit_form_fields", array( __CLASS__, 'render_term_edit_fields' ), 10, 2 );
			add_action( "created_{$taxonomy}", array( __CLASS__, 'save_term_meta' ), 10, 1 );
			add_action( "edited_{$taxonomy}", array( __CLASS__, 'save_term_meta' ), 10, 1 );
		}

		foreach ( self::POST_TYPES as $post_type ) {
			add_filter( "manage_{$post_type}_posts_columns", array( __CLASS__, 'columns' ) );
			add_filter( "manage_edit-{$post_type}_sortable_columns", array( __CLASS__, 'sortable_columns' ) );
			add_action( "manage_{$post_type}_posts_custom_column", array( __CLASS__, 'render_column' ), 10, 2 );
		}
		add_action( 'pre_get_posts', array( __CLASS__, 'apply_sort' ) );
	}

	public static function is_booted(): bool {
		return self::$booted;
	}

	public static function register_metaboxes(): void {
		foreach ( self::POST_TYPES as $post_type ) {
			add_meta_box( 'es-hreflang', __( 'Hreflang (چندزبانه)', 'erfansanat' ), array( __CLASS__, 'render_metabox' ), $post_type, 'normal', 'default' );
		}
	}

	/**
	 * @param array<string,string> $columns Columns.
	 * @return array<string,string>
	 */
	public static function columns( $columns ) {
		if ( ! isset( $columns[ self::SHARED_KEY ] ) ) {
			$columns[ self::SHARED_KEY ] = __( 'کلید hreflang', 'erfansanat' );
		}
		return $columns;
	}

	/**
	 * @param array<string,string> $columns Sortable columns.
	 * @return array<string,string>
	 */
	public static function sortable_columns( $columns ) {
		$columns[ self::SHARED_KEY ] = self::SHARED_KEY;
		return $columns;
	}

	/**
	 * @param string $column  Column key.
	 * @param int    $post_id Post id.
	 */
	public static function render_column( $column, $post_id ): void {
		if ( self::SHARED_KEY === $column ) {
			echo esc_html( (string) get_post_meta( $post_id, self::SHARED_KEY, true ) );
		}
	}

	/**
	 * Sort by the shared hreflang key (admin main query).
	 *
	 * @param \WP_Query $query Query.
	 */
	public static function apply_sort( $query ): void {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}
		if ( ! in_array( $query->get( 'post_type' ), self::POST_TYPES, true ) ) {
			return;
		}
		if ( self::SHARED_KEY === $query->get( 'orderby' ) ) {
			$query->set( 'meta_key', self::SHARED_KEY );
			$query->set( 'orderby', 'meta_value' );
		}
	}

	/**
	 * Validate a hreflang URL. Returns null for invalid (reject), '' for empty
	 * (clear), or the sanitized URL.
	 */
	private static function clean_url( $value ): ?string {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return '';
		}
		if ( preg_match( '/^(javascript|data|vbscript)\s*:/i', $value ) ) {
			return null;
		}
		$url = esc_url_raw( $value );
		return '' === $url ? null : $url;
	}

	/**
	 * @param array<string,mixed> $values Raw input.
	 * @return array<string,string> Sanitized values (invalid URLs omitted).
	 */
	private static function sanitize_values( array $values ): array {
		$clean = array();
		foreach ( self::URL_KEYS as $key ) {
			if ( ! array_key_exists( $key, $values ) ) {
				continue;
			}
			$url = self::clean_url( $values[ $key ] );
			if ( null !== $url ) {
				$clean[ $key ] = $url; // AC-02: invalid URLs are rejected.
			}
		}
		if ( array_key_exists( self::SHARED_KEY, $values ) ) {
			$clean[ self::SHARED_KEY ] = sanitize_text_field( (string) $values[ self::SHARED_KEY ] );
		}
		return $clean;
	}

	/**
	 * Render the post metabox.
	 *
	 * @param \WP_Post $post Post.
	 */
	public static function render_metabox( $post ): void {
		wp_nonce_field( 'es_hreflang', 'es_hreflang_nonce' );
		$values = array();
		foreach ( array_merge( self::URL_KEYS, array( self::SHARED_KEY ) ) as $key ) {
			$values[ $key ] = (string) get_post_meta( $post->ID, $key, true );
		}
		self::fields( $values );
	}

	/**
	 * @param array<string,string> $values Current values.
	 */
	private static function fields( array $values ): void {
		$labels = array(
			'hrefmeta_fa'    => __( 'لینک فارسی (fa)', 'erfansanat' ),
			'hrefmeta_en'    => __( 'لینک انگلیسی (en)', 'erfansanat' ),
			'hrefmeta_ar'    => __( 'لینک عربی (ar)', 'erfansanat' ),
			'same_key_hrefl' => __( 'کلید مشترک hreflang', 'erfansanat' ),
		);
		foreach ( $labels as $key => $label ) {
			printf(
				'<p class="es-field"><label for="es-href-%1$s">%2$s</label><input type="text" id="es-href-%1$s" name="es_hreflang[%1$s]" value="%3$s" class="widefat" /></p>',
				esc_attr( $key ),
				esc_html( $label ),
				esc_attr( $values[ $key ] ?? '' )
			);
		}
	}

	/**
	 * Save post hreflang meta (nonce + capability guarded).
	 *
	 * @param int $post_id Post id.
	 */
	public static function save_post_meta( $post_id ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! in_array( get_post_type( $post_id ), self::POST_TYPES, true ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		if ( ! isset( $_POST['es_hreflang_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_hreflang_nonce'] ) ), 'es_hreflang' ) ) {
			return;
		}
		if ( ! isset( $_POST['es_hreflang'] ) || ! is_array( $_POST['es_hreflang'] ) ) {
			return;
		}

		foreach ( self::sanitize_values( wp_unslash( $_POST['es_hreflang'] ) ) as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}
	}

	/**
	 * Term "add" form fields.
	 *
	 * @param string $taxonomy Taxonomy.
	 */
	public static function render_term_add_fields( $taxonomy ): void {
		wp_nonce_field( 'es_hreflang', 'es_hreflang_nonce' );
		self::fields( array() );
	}

	/**
	 * Term "edit" form fields.
	 *
	 * @param \WP_Term $term     Term.
	 * @param string   $taxonomy Taxonomy.
	 */
	public static function render_term_edit_fields( $term, $taxonomy ): void {
		wp_nonce_field( 'es_hreflang', 'es_hreflang_nonce' );
		$values = array();
		foreach ( array_merge( self::URL_KEYS, array( self::SHARED_KEY ) ) as $key ) {
			$values[ $key ] = (string) get_term_meta( $term->term_id, $key, true );
		}
		echo '<table class="form-table"><tbody><tr><td>';
		self::fields( $values );
		echo '</td></tr></tbody></table>';
	}

	/**
	 * Save term hreflang meta (nonce + capability guarded).
	 *
	 * @param int $term_id Term id.
	 */
	public static function save_term_meta( $term_id ): void {
		if ( ! isset( $_POST['es_hreflang_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['es_hreflang_nonce'] ) ), 'es_hreflang' ) ) {
			return;
		}
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}
		if ( ! isset( $_POST['es_hreflang'] ) || ! is_array( $_POST['es_hreflang'] ) ) {
			return;
		}

		foreach ( self::sanitize_values( wp_unslash( $_POST['es_hreflang'] ) ) as $key => $value ) {
			update_term_meta( $term_id, $key, $value );
		}
	}
}
