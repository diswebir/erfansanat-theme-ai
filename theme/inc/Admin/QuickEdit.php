<?php
/** Secure quick-edit controls for legacy product identity metadata. */

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Contracts\ModuleInterface;

 defined( 'ABSPATH' ) || exit;

/** Adds one compact custom Quick Edit panel without changing existing columns. */
final class QuickEdit implements ModuleInterface {

	private const FIELDS = array( 'gs1_id', 'national_code', 'hs_id', 'isic_id', 'cpc_id', 'product_id', 'zireonvan', 'english_titl', 'true_sale' );

	public function register(): void {
		add_action( 'quick_edit_custom_box', array( self::class, 'render_box' ), 10, 2 );
		add_filter( 'post_row_actions', array( self::class, 'row_data' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue' ) );
		add_action( 'save_post_product', array( self::class, 'save' ), 20, 2 );
	}

	/** Render the fields only in a product's inline edit row. */
	public static function render_box( string $column, string $post_type ): void {
		if ( 'product' !== $post_type || 'title' !== $column ) { return; }
		echo '<fieldset class="inline-edit-col-right"><div class="inline-edit-col"><strong>' . esc_html__( 'شناسه‌ها و وضعیت فروش', 'erfansanat-theme' ) . '</strong>';
		echo '<input type="hidden" name="es_product_quick_edit_nonce" value="' . esc_attr( wp_create_nonce( 'es_product_quick_edit' ) ) . '">';
		foreach ( self::FIELDS as $key ) {
			$label = $key;
			if ( 'true_sale' === $key ) { echo '<label><span class="title">' . esc_html__( 'نوع فروش', 'erfansanat-theme' ) . '</span><select name="es_product_quick_edit[true_sale]"><option value="false">' . esc_html__( 'استعلام / تماس', 'erfansanat-theme' ) . '</option><option value="true">' . esc_html__( 'خرید آنلاین', 'erfansanat-theme' ) . '</option></select></label>'; continue; }
			echo '<label><span class="title">' . esc_html( $label ) . '</span><span class="input-text-wrap"><input type="text" name="es_product_quick_edit[' . esc_attr( $key ) . ']" value=""></span></label>';
		}
		echo '</div></fieldset>';
	}

	/** Embed escaped current values for the inline editor's small JS adapter. */
	public static function row_data( array $actions, $post ): array {
		if ( ! is_object( $post ) || 'product' !== $post->post_type ) { return $actions; }
		$values = array();
		foreach ( self::FIELDS as $key ) { $value = get_post_meta( (int) $post->ID, $key, true ); $values[$key] = is_scalar( $value ) ? (string) $value : ''; }
		$actions['es-quick-edit-values'] = '<span class="es-product-quick-edit-data" data-es-product-quick-edit="' . esc_attr( wp_json_encode( $values ) ) . '" hidden></span>';
		return $actions;
	}

	/** Save only submitted whitelisted fields after the quick-edit nonce check. */
	public static function save( int $post_id, $post = null ): bool {
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( function_exists( 'wp_is_post_revision' ) && wp_is_post_revision( $post_id ) ) ) { return false; }
		if ( ! is_object( $post ) && function_exists( 'get_post' ) ) { $post = get_post( $post_id ); }
		if ( ! is_object( $post ) || ! isset( $post->post_type ) || 'product' !== $post->post_type ) { return false; }
		$submitted = isset( $_POST ) && is_array( $_POST ) ? wp_unslash( $_POST ) : array();
		if ( ! isset( $submitted['es_product_quick_edit_nonce'] ) || ! is_string( $submitted['es_product_quick_edit_nonce'] ) || ! wp_verify_nonce( $submitted['es_product_quick_edit_nonce'], 'es_product_quick_edit' ) || ! current_user_can( 'edit_post', $post_id ) ) { return false; }
		$input = isset( $submitted['es_product_quick_edit'] ) && is_array( $submitted['es_product_quick_edit'] ) ? $submitted['es_product_quick_edit'] : array();
		foreach ( self::FIELDS as $key ) {
			if ( ! array_key_exists( $key, $input ) || ! is_scalar( $input[$key] ) ) { continue; }
			$value = (string) $input[$key];
			if ( 'true_sale' === $key ) { if ( in_array( $value, array( 'true', 'false' ), true ) ) { update_post_meta( $post_id, $key, $value ); } continue; }
			update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
		}
		return true;
	}

	public static function enqueue( string $hook ): void {
		if ( ! in_array( $hook, array( 'edit.php', 'post.php' ), true ) || ! function_exists( 'get_current_screen' ) ) { return; }
		$screen = get_current_screen();
		if ( ! is_object( $screen ) || 'product' !== $screen->post_type || 'edit.php' !== $hook ) { return; }
		wp_enqueue_script( 'es-product-quick-edit', get_template_directory_uri() . '/assets/admin/quick-edit.js', array( 'inline-edit-post' ), defined( 'ES_THEME_VERSION' ) ? ES_THEME_VERSION : '1.0.0', true );
	}
}
