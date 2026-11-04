<?php
/** Product category term metadata editor. */

namespace ErfanSanat\Theme\Admin\Metaboxes;

use ErfanSanat\Theme\Contracts\ModuleInterface;
use ErfanSanat\Theme\Meta\MetaRepository;

defined( 'ABSPATH' ) || exit;

/** Adds the legacy thumbnail and FAQ repeater to product categories. */
final class TermMeta implements ModuleInterface {

	public function register(): void {
		add_action( 'product_cat_edit_form_fields', array( self::class, 'edit_fields' ) );
		add_action( 'product_cat_add_form_fields', array( self::class, 'add_fields' ) );
		add_action( 'edited_product_cat', array( self::class, 'save' ) );
		add_action( 'created_product_cat', array( self::class, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( self::class, 'enqueue_media' ) );
	}

	public static function edit_fields( $term ): void {
		if ( ! is_object( $term ) || empty( $term->term_id ) ) { return; }
		echo '<tr class="form-field"><th><label for="es-product-cat-thumbnail">' . esc_html__( 'تصویر دسته', 'erfansanat-theme' ) . '</label></th><td>' . self::fields( (int) $term->term_id ) . '</td></tr>';
		echo '<tr class="form-field"><th><label>' . esc_html__( 'پرسش‌های متداول', 'erfansanat-theme' ) . '</label></th><td>' . self::faq_fields( (int) $term->term_id ) . '</td></tr>';
	}

	public static function add_fields(): void {
		echo '<div class="form-field"><label for="es-product-cat-thumbnail">' . esc_html__( 'تصویر دسته', 'erfansanat-theme' ) . '</label>' . self::fields( 0 ) . '</div>';
		echo '<div class="form-field"><label>' . esc_html__( 'پرسش‌های متداول', 'erfansanat-theme' ) . '</label>' . self::faq_fields( 0 ) . '</div>';
	}

	private static function fields( int $term_id ): string {
		$id = $term_id ? absint( get_term_meta( $term_id, 'thumbnail', true ) ) : 0;
		$preview = $id ? wp_get_attachment_image( $id, 'thumbnail' ) : '<span class="es-term-meta__placeholder">' . esc_html__( 'تصویری انتخاب نشده است', 'erfansanat-theme' ) . '</span>';
		return '<input type="hidden" name="es_product_cat_meta_nonce" value="' . esc_attr( wp_create_nonce( 'es_product_cat_meta' ) ) . '"><div class="es-media" data-es-media data-es-media-mode="single" data-es-media-kind="image"><input id="es-product-cat-thumbnail" class="es-media__value" type="hidden" name="es_product_cat_meta[thumbnail]" value="' . esc_attr( (string) $id ) . '"><div class="es-media__preview">' . $preview . '</div><button type="button" class="button" data-es-media-select>' . esc_html__( 'انتخاب تصویر', 'erfansanat-theme' ) . '</button><button type="button" class="button" data-es-media-remove>' . esc_html__( 'حذف', 'erfansanat-theme' ) . '</button></div>';
	}

	private static function faq_fields( int $term_id ): string {
		$rows = $term_id ? es_term_meta( $term_id, 'faq', array() ) : array();
		if ( ! is_array( $rows ) || ! $rows ) { $rows = array( 'item-0' => array() ); }
		$html = '<div class="es-repeater" data-es-repeater="term-faq">';
		foreach ( $rows as $key => $row ) { if ( ! is_array( $row ) ) { continue; } $row_key = is_string( $key ) && preg_match( '/^item-[0-9]+$/', $key ) ? $key : 'item-0'; $html .= self::faq_row( $row_key, $row ); }
		return $html . '<template data-es-repeater-template>' . self::faq_row( 'item-__INDEX__', array() ) . '</template><button type="button" class="button" data-es-repeater-add>' . esc_html__( 'افزودن پرسش', 'erfansanat-theme' ) . '</button></div>';
	}

	private static function faq_row( string $key, array $row ): string {
		$q = isset( $row['name_question'] ) && is_scalar( $row['name_question'] ) ? (string) $row['name_question'] : '';
		$a = isset( $row['answer_question'] ) && is_scalar( $row['answer_question'] ) ? (string) $row['answer_question'] : '';
		return '<div data-es-repeater-item="' . esc_attr( $key ) . '"><label>' . esc_html__( 'پرسش', 'erfansanat-theme' ) . '<input type="text" name="es_product_cat_meta[faq][' . esc_attr( $key ) . '][name_question]" value="' . esc_attr( $q ) . '"></label><label>' . esc_html__( 'پاسخ', 'erfansanat-theme' ) . '<textarea name="es_product_cat_meta[faq][' . esc_attr( $key ) . '][answer_question]">' . esc_textarea( $a ) . '</textarea></label><button type="button" data-es-repeater-remove>' . esc_html__( 'حذف', 'erfansanat-theme' ) . '</button></div>';
	}

	public static function save( int $term_id ): bool {
		if ( ! isset( $_POST['es_product_cat_meta_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['es_product_cat_meta_nonce'] ), 'es_product_cat_meta' ) || ! current_user_can( 'manage_product_terms' ) || ! isset( $_POST['es_product_cat_meta'] ) || ! is_array( $_POST['es_product_cat_meta'] ) ) { return false; }
		$input = wp_unslash( $_POST['es_product_cat_meta'] );
		if ( isset( $input['thumbnail'] ) ) { $id = absint( $input['thumbnail'] ); if ( $id && 'attachment' === get_post_type( $id ) && wp_attachment_is_image( $id ) ) { update_term_meta( $term_id, 'thumbnail', $id ); } elseif ( ! $id ) { delete_term_meta( $term_id, 'thumbnail' ); } }
		if ( isset( $input['faq'] ) && is_array( $input['faq'] ) ) {
			$rows = array(); foreach ( $input['faq'] as $row_key => $row ) { if ( ! is_array( $row ) ) { continue; } $q = isset( $row['name_question'] ) && is_scalar( $row['name_question'] ) ? sanitize_text_field( $row['name_question'] ) : ''; $a = isset( $row['answer_question'] ) && is_scalar( $row['answer_question'] ) ? wp_kses_post( $row['answer_question'] ) : ''; if ( '' === trim( $q ) && '' === trim( wp_strip_all_tags( $a ) ) ) { continue; } $key = is_string( $row_key ) && preg_match( '/^item-[0-9]+$/', $row_key ) ? $row_key : 'item-' . count( $rows ); $rows[$key] = array( 'name_question' => $q, 'answer_question' => $a ); }
			if ( $rows ) { MetaRepository::set_term( $term_id, 'faq', $rows ); } else { MetaRepository::delete_term( $term_id, 'faq' ); }
		}
		return true;
	}

	public static function enqueue_media( string $hook ): void {
		if ( ! in_array( $hook, array( 'edit-tags.php', 'term.php' ), true ) || ! function_exists( 'get_current_screen' ) ) { return; }
		$screen = get_current_screen(); if ( ! is_object( $screen ) || 'product_cat' !== $screen->taxonomy ) { return; }
		$version = defined( 'ES_THEME_VERSION' ) ? ES_THEME_VERSION : '1.0.0';
		wp_enqueue_style( 'es-admin-fonts', get_template_directory_uri() . '/assets/admin/fonts.css', array(), $version );
		wp_enqueue_media();
		wp_enqueue_script( 'es-admin-media', get_template_directory_uri() . '/assets/admin/media.js', array(), $version, true ); wp_enqueue_script( 'es-admin-repeater', get_template_directory_uri() . '/assets/admin/repeater.js', array(), $version, true );
	}
}
