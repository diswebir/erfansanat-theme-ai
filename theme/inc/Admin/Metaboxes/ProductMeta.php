<?php
/** Product metadata box registration and nonce-guarded persistence. */

namespace ErfanSanat\Theme\Admin\Metaboxes;

use ErfanSanat\Theme\Admin\Metaboxes\Tabs\ProductIdentityTab;
use ErfanSanat\Theme\Admin\Metaboxes\Tabs\ProductMediaTab;
use ErfanSanat\Theme\Admin\Metaboxes\Tabs\ProductRelationsTab;
use ErfanSanat\Theme\Admin\Metaboxes\Tabs\ProductRepeatersTab;
use ErfanSanat\Theme\Contracts\ModuleInterface;

defined( 'ABSPATH' ) || exit;

/** Owns the product meta box save boundary shared by its tabs. */
final class ProductMeta implements ModuleInterface {

	private const NONCE_NAME = 'es_product_meta_nonce';
	private const INPUT_NAME = 'es_product_meta';

	/** Register the product edit box and its post-type-specific save callback. */
	public function register(): void {
		add_action( 'add_meta_boxes_product', array( self::class, 'register_box' ) );
		add_action( 'save_post_product', array( self::class, 'save' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_media' ) );
	}

	/** Register one native meta box on WooCommerce products. */
	public static function register_box(): void {
		add_meta_box(
			'es-product-information',
			__( 'اطلاعات محصولات', 'erfansanat-theme' ),
			array( self::class, 'render' ),
			'product',
			'normal',
			'high'
		);
	}

	/** Render only for users who may edit the current product. */
	public static function render( $post ): void {
		if ( ! is_object( $post ) || empty( $post->ID ) || ! current_user_can( 'edit_post', (int) $post->ID ) ) { return; }
		echo self::render_content( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- renderer escapes all field values.
	}

	/** Return nonce-protected identity markup for the meta-box callback and tests. */
	public static function render_content( $post ): string {
		if ( ! is_object( $post ) || empty( $post->ID ) ) { return ''; }
		$nonce = wp_nonce_field( 'es_product_meta_' . (int) $post->ID, self::NONCE_NAME, false, false );
		$panels = array(
			'identity' => array( 'شناسه‌ها', ProductIdentityTab::render( (int) $post->ID ) ),
			'media' => array( 'فایل و گالری', ProductMediaTab::render( (int) $post->ID ) ),
			'relations' => array( 'روابط', ProductRelationsTab::render( (int) $post->ID ) ),
			'repeaters' => array( 'ویژگی‌ها و پرسش‌ها', ProductRepeatersTab::render( (int) $post->ID ) ),
		);
		$html = (string) $nonce . '<div class="es-product-meta__tabs"><div role="tablist" aria-label="' . esc_attr__( 'اطلاعات محصول', 'erfansanat-theme' ) . '">';
		foreach ( $panels as $id => $panel ) { $selected = 'identity' === $id; $html .= '<button type="button" role="tab" id="es-product-tab-' . esc_attr( $id ) . '" aria-controls="es-product-panel-' . esc_attr( $id ) . '" aria-selected="' . ( $selected ? 'true' : 'false' ) . '" tabindex="' . ( $selected ? '0' : '-1' ) . '" data-es-product-tab="' . esc_attr( $id ) . '">' . esc_html( $panel[0] ) . '</button>'; }
		$html .= '</div>';
		foreach ( $panels as $id => $panel ) { $selected = 'identity' === $id; $html .= '<section role="tabpanel" id="es-product-panel-' . esc_attr( $id ) . '" aria-labelledby="es-product-tab-' . esc_attr( $id ) . '" data-es-product-panel="' . esc_attr( $id ) . '"' . ( $selected ? '' : ' hidden' ) . '>' . $panel[1] . '</section>'; }
		return $html . '</div>';
	}

	/** Validate post state, capability and nonce before saving exact meta keys. */
	public static function save( int $post_id, $post = null ): bool {
		if ( $post_id < 1 || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( function_exists( 'wp_is_post_revision' ) && wp_is_post_revision( $post_id ) ) || ( function_exists( 'wp_is_post_autosave' ) && wp_is_post_autosave( $post_id ) ) ) { return false; }
		$submitted = isset( $_POST ) && is_array( $_POST ) ? wp_unslash( $_POST ) : array();
		if ( ! current_user_can( 'edit_post', $post_id ) || ! isset( $submitted[ self::NONCE_NAME ] ) || ! is_string( $submitted[ self::NONCE_NAME ] ) ) { return false; }
		$nonce = $submitted[ self::NONCE_NAME ];
		if ( ! is_string( $nonce ) || ! wp_verify_nonce( $nonce, 'es_product_meta_' . $post_id ) ) {
			return false;
		}
		if ( ! isset( $submitted[ self::INPUT_NAME ] ) || ! is_array( $submitted[ self::INPUT_NAME ] ) ) { return false; }
		$input = $submitted[ self::INPUT_NAME ];
		ProductMediaTab::save( $post_id, $input );
		if ( array_key_exists( 'project-ids', $input ) || array_key_exists( 'usage_countr', $input ) ) { ProductRelationsTab::save( $post_id, $input ); }
		ProductRepeatersTab::save( $post_id, $input );
		$keys  = array_keys( ProductIdentityTab::text_fields() );
		foreach ( $keys as $key ) {
			if ( ! array_key_exists( $key, $input ) || ! is_scalar( $input[ $key ] ) ) { continue; }
			update_post_meta( $post_id, $key, sanitize_text_field( (string) $input[ $key ] ) );
		}
		if ( isset( $input['true_sale'] ) && in_array( $input['true_sale'], array( 'true', 'false' ), true ) ) {
			update_post_meta( $post_id, 'true_sale', $input['true_sale'] );
		}
		return true;
	}

	/** Load WordPress's picker only on product editor screens. */
	public static function enqueue_media( string $hook ): void {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! function_exists( 'get_current_screen' ) ) { return; }
		$screen = get_current_screen();
		if ( ! is_object( $screen ) || 'product' !== $screen->post_type ) { return; }
		$version = defined( 'ES_THEME_VERSION' ) ? ES_THEME_VERSION : '1.0.0';
		wp_enqueue_style( 'es-admin-fonts', get_template_directory_uri() . '/assets/admin/fonts.css', array(), $version );
		wp_enqueue_media();
		wp_enqueue_script( 'es-admin-media', get_template_directory_uri() . '/assets/admin/media.js', array(), $version, true );
		wp_enqueue_script( 'es-admin-repeater', get_template_directory_uri() . '/assets/admin/repeater.js', array(), $version, true );
		wp_enqueue_script( 'es-admin-relations', get_template_directory_uri() . '/assets/admin/relations.js', array(), $version, true );
		wp_enqueue_script( 'es-product-meta-tabs', get_template_directory_uri() . '/assets/admin/product-meta-tabs.js', array(), $version, true );
	}
}
