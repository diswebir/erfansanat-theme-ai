<?php
/** Product feature, usage, and FAQ repeaters. */

namespace ErfanSanat\Theme\Admin\Metaboxes\Tabs;

use ErfanSanat\Theme\Meta\MetaRepository;

defined( 'ABSPATH' ) || exit;

/** Handles the exact JetEngine repeater keys and child-field spellings. */
final class ProductRepeatersTab {

	/** Exact historical meta keys and child-field names. */
	public static function schema(): array {
		return array(
			'product-feature' => array( 'title' => 'ویژگی‌های محصول', 'fields' => array( 'add-new-feature' => 'ویژگی' ) ),
			'product-feature2_copy' => array( 'title' => 'مشخصات تکمیلی', 'fields' => array( 'add-new-feature' => 'عنوان', 'vaule_of_variable' => 'مقدار' ) ),
			'usage_product' => array( 'title' => 'موارد کاربرد', 'fields' => array( 'add-new-usage' => 'کاربرد' ) ),
			'faq' => array( 'title' => 'پرسش‌های متداول', 'fields' => array( 'name_question' => 'پرسش', 'answer_question' => 'پاسخ' ) ),
		);
	}

	/** Render all four repeaters with JetEngine-compatible item-N names. */
	public static function render( int $post_id ): string {
		$html = '<div class="es-product-meta__repeaters">';
		foreach ( self::schema() as $key => $definition ) {
			$rows = es_meta( $post_id, $key, array() );
			if ( ! is_array( $rows ) ) { $rows = array(); }
			if ( ! $rows ) { $rows = array( 'item-0' => array() ); }
			$html .= '<fieldset class="es-repeater" data-es-repeater="' . esc_attr( $key ) . '"><legend>' . esc_html( $definition['title'] ) . '</legend><div data-es-repeater-rows>';
			$index = 0;
			foreach ( $rows as $row_key => $row ) {
				$row_key = is_string( $row_key ) && preg_match( '/^item-[0-9]+$/', $row_key ) ? $row_key : 'item-' . $index;
				$html .= self::render_row( $key, $definition['fields'], is_array( $row ) ? $row : array(), $row_key );
				++$index;
			}
			$html .= '</div><template data-es-repeater-template>' . self::render_row( $key, $definition['fields'], array(), 'item-__INDEX__' ) . '</template><button type="button" class="button" data-es-repeater-add>' . esc_html__( 'افزودن ردیف', 'erfansanat-theme' ) . '</button></fieldset>';
		}
		return $html . '</div>';
	}

	private static function render_row( string $key, array $fields, array $row, string $row_key ): string {
		$html = '<div class="es-repeater__row" data-es-repeater-item="' . esc_attr( $row_key ) . '">';
		foreach ( $fields as $subkey => $label ) {
			$value = isset( $row[$subkey] ) && is_scalar( $row[$subkey] ) ? (string) $row[$subkey] : '';
			$name = 'es_product_meta[' . esc_attr( $key ) . '][' . esc_attr( $row_key ) . '][' . esc_attr( $subkey ) . ']';
			$html .= '<label>' . esc_html( $label ) . '</label>';
			if ( 'answer_question' === $subkey ) { $html .= '<textarea class="widefat" name="' . $name . '">' . esc_textarea( $value ) . '</textarea>'; }
			else { $html .= '<input class="widefat" type="text" name="' . $name . '" value="' . esc_attr( $value ) . '">'; }
		}
		return $html . '<button type="button" data-es-repeater-remove>' . esc_html__( 'حذف ردیف', 'erfansanat-theme' ) . '</button></div>';
	}

	/** Sanitize rows, retain old encoding, and remove entirely empty repeaters. */
	public static function save( int $post_id, array $input ): void {
		foreach ( self::schema() as $key => $definition ) {
			if ( ! array_key_exists( $key, $input ) || ! is_array( $input[$key] ) ) { continue; }
			$rows = array();
			foreach ( $input[$key] as $row_key => $row ) {
				if ( ! is_array( $row ) ) { continue; }
				$clean = array(); $has_value = false;
				foreach ( $definition['fields'] as $subkey => $label ) {
					$value = isset( $row[$subkey] ) && is_scalar( $row[$subkey] ) ? (string) $row[$subkey] : '';
					$value = 'answer_question' === $subkey ? wp_kses_post( $value ) : sanitize_text_field( $value );
					$clean[$subkey] = $value;
					if ( '' !== trim( wp_strip_all_tags( $value ) ) ) { $has_value = true; }
				}
				if ( $has_value ) { $rows[ is_string( $row_key ) && preg_match( '/^item-[0-9]+$/', $row_key ) ? $row_key : 'item-' . count( $rows ) ] = $clean; }
			}
			if ( $rows ) { MetaRepository::set( $post_id, $key, $rows ); }
			else { MetaRepository::delete( $post_id, $key ); }
		}
	}
}
