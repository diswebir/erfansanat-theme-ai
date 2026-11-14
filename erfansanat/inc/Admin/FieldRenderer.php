<?php
/**
 * Schema-driven admin field renderer.
 *
 * Renders text/textarea/number/select/toggle/checkbox (P02-T005), then
 * media/link/icon (P02-T006) and repeater (P02-T007). Every field gets a
 * unique id/name/label and escaped values; unknown types render nothing.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Admin;

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Schema;
use ErfanSanat\Theme\Options\Store;

final class FieldRenderer {

	/**
	 * Render every field belonging to a tab.
	 */
	public static function render_tab( string $tab_id ): void {
		$tabs = Schema::tabs();
		if ( ! isset( $tabs[ $tab_id ] ) ) {
			return;
		}

		foreach ( $tabs[ $tab_id ]['fields'] as $key ) {
			echo self::render_field( $key ); // phpcs:ignore WordPress.Security.EscapeOutput -- built escaped.
		}
	}

	/**
	 * Render a single field. Returns '' for unknown keys/unsupported types.
	 *
	 * @param mixed  $value       Current value (null = read from store).
	 * @param string $name_prefix Input name prefix (for repeater subfields).
	 * @param string $id_suffix   Unique id suffix (for repeater subfields).
	 */
	public static function render_field( string $key, $value = null, string $name_prefix = 'es_options', string $id_suffix = '' ): string {
		$field = Schema::get_field( $key );
		if ( null === $field ) {
			return '';
		}

		if ( null === $value ) {
			$value = Store::get( $key );
		}

		$type = $field['type'];
		$id   = self::field_id( $key, $id_suffix );
		$name = $name_prefix . '[' . $key . ']';

		switch ( $type ) {
			case 'text':
			case 'email':
			case 'url':
				return self::wrap( $id, $field, self::input( $type, $id, $name, $key, (string) $value ) );

			case 'number':
				return self::wrap( $id, $field, self::number( $id, $name, $key, $value, $field ) );

			case 'color':
				return self::wrap( $id, $field, self::input( 'text', $id, $name, $key, (string) $value, 'es-color' ) );

			case 'textarea':
			case 'rich':
				return self::wrap( $id, $field, self::textarea( $id, $name, $key, (string) $value ) );

			case 'select':
				return self::wrap( $id, $field, self::select( $id, $name, $key, $value, $field ) );

			case 'toggle':
				return self::toggle( $id, $name, $key, $value, $field );

			case 'checkbox':
				return self::wrap( $id, $field, self::checkbox( $id, $name, $key, $value ) );

			case 'media':
				return self::wrap( $id, $field, self::media( $id, $name, $key, $value ) );

			case 'link':
				return self::wrap( $id, $field, self::link( $id, $name, $key, $value, $field ) );

			case 'icon':
				return self::wrap( $id, $field, self::icon( $id, $name, $key, $value, $field ) );

			case 'repeater':
				return self::repeater( $id, $name, $key, $value, $field );

			case 'code':
				return self::wrap( $id, $field, self::textarea( $id, $name, $key, (string) $value, 'es-code' ) );

			default:
				return '';
		}
	}

	public static function field_id( string $key, string $suffix = '' ): string {
		return 'es-field-' . $key . ( '' !== $suffix ? '-' . $suffix : '' );
	}

	/**
	 * Label + control + optional description wrapper.
	 *
	 * @param array<string,mixed> $field Field definition.
	 */
	private static function wrap( string $id, array $field, string $control ): string {
		$html  = '<div class="es-field" data-es-field="' . esc_attr( $id ) . '">';
		$html .= '<label class="es-field__label" for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ?? '' ) . '</label>';
		$html .= $control;
		if ( ! empty( $field['desc'] ) ) {
			$html .= '<p class="es-field__desc">' . esc_html( $field['desc'] ) . '</p>';
		}
		$html .= '</div>';

		return $html;
	}

	private static function input( string $type, string $id, string $name, string $key, string $value, string $class = '' ): string {
		return sprintf(
			'<input type="%s" id="%s" name="%s" data-es-key="%s" class="%s" value="%s" />',
			esc_attr( $type ),
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $key ),
			esc_attr( trim( 'regular-text ' . $class ) ),
			esc_attr( $value )
		);
	}

	/**
	 * @param array<string,mixed> $field Field definition.
	 */
	private static function number( string $id, string $name, string $key, $value, array $field ): string {
		$min = isset( $field['min'] ) ? ' min="' . esc_attr( (string) $field['min'] ) . '"' : '';
		$max = isset( $field['max'] ) ? ' max="' . esc_attr( (string) $field['max'] ) . '"' : '';

		return sprintf(
			'<input type="number" id="%s" name="%s" data-es-key="%s" class="small-text" value="%s"%s%s />',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $key ),
			esc_attr( (string) $value ),
			$min,
			$max
		);
	}

	private static function textarea( string $id, string $name, string $key, string $value, string $class = '' ): string {
		return sprintf(
			'<textarea id="%s" name="%s" data-es-key="%s" class="%s" rows="4">%s</textarea>',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $key ),
			esc_attr( trim( 'large-text ' . $class ) ),
			esc_textarea( $value )
		);
	}

	/**
	 * @param array<string,mixed> $field Field definition.
	 */
	private static function select( string $id, string $name, string $key, $value, array $field ): string {
		$html = sprintf(
			'<select id="%s" name="%s" data-es-key="%s">',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $key )
		);

		foreach ( (array) ( $field['choices'] ?? array() ) as $choice_value => $choice_label ) {
			$selected = selected( (string) $value, (string) $choice_value, false );
			$html    .= sprintf(
				'<option value="%s"%s>%s</option>',
				esc_attr( (string) $choice_value ),
				$selected,
				esc_html( (string) $choice_label )
			);
		}

		$html .= '</select>';

		return $html;
	}

	/**
	 * @param array<string,mixed> $field Field definition.
	 */
	private static function toggle( string $id, string $name, string $key, $value, array $field ): string {
		$checked = checked( (bool) $value, true, false );

		$html  = '<div class="es-field" data-es-field="' . esc_attr( $id ) . '">';
		$html .= '<label class="es-toggle" for="' . esc_attr( $id ) . '">';
		$html .= sprintf(
			'<input type="checkbox" id="%s" name="%s" data-es-key="%s" value="1"%s />',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $key ),
			$checked
		);
		$html .= '<span class="es-toggle__track" aria-hidden="true"></span>';
		$html .= '<span class="es-field__label">' . esc_html( $field['label'] ?? '' ) . '</span>';
		$html .= '</label></div>';

		return $html;
	}

	private static function checkbox( string $id, string $name, string $key, $value ): string {
		return sprintf(
			'<label class="es-checkbox"><input type="checkbox" id="%s" name="%s" data-es-key="%s" value="1"%s /></label>',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $key ),
			checked( (bool) $value, true, false )
		);
	}

	/**
	 * Media picker (P02-T006). Stores an attachment ID; wp.media wires the UI.
	 *
	 * @param mixed $value Current attachment ID.
	 */
	private static function media( string $id, string $name, string $key, $value ): string {
		$attachment_id = absint( $value );
		$preview       = $attachment_id ? wp_get_attachment_image( $attachment_id, 'thumbnail', false, array( 'class' => 'es-media__img' ) ) : '';
		$has           = $attachment_id && $preview;

		$html  = '<div class="es-media" data-es-media="' . esc_attr( $id ) . '">';
		$html .= sprintf(
			'<input type="hidden" id="%s" name="%s" data-es-key="%s" value="%s" />',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $key ),
			esc_attr( (string) $attachment_id )
		);
		$html .= '<div class="es-media__preview" data-es-media-preview>' . ( $has ? $preview : '' ) . '</div>';
		$html .= '<div class="es-media__actions">';
		$html .= '<button type="button" class="button" data-es-media-select>' . esc_html__( 'انتخاب رسانه', 'erfansanat' ) . '</button> ';
		$html .= '<button type="button" class="button" data-es-media-remove' . ( $has ? '' : ' hidden' ) . '>' . esc_html__( 'حذف', 'erfansanat' ) . '</button>';
		$html .= '</div></div>';

		return $html;
	}

	/**
	 * Link field (P02-T006): url + label + safe target/rel.
	 *
	 * @param mixed             $value Current value (array or URL string).
	 * @param array<string,mixed> $field Field definition.
	 */
	private static function link( string $id, string $name, string $key, $value, array $field ): string {
		$link = self::normalize_link( $value );

		$html  = '<div class="es-link" data-es-link="' . esc_attr( $id ) . '" data-es-link-key="' . esc_attr( $key ) . '">';
		$html .= sprintf(
			'<input type="url" id="%s" name="%s[url]" data-es-link-url class="regular-text" placeholder="https://" value="%s" />',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $link['url'] )
		);
		$html .= sprintf(
			'<input type="text" name="%s[label]" data-es-link-label class="regular-text" placeholder="%s" value="%s" />',
			esc_attr( $name ),
			esc_attr__( 'متن لینک', 'erfansanat' ),
			esc_attr( $link['label'] )
		);
		$html .= '<label class="es-link__target"><input type="checkbox" name="' . esc_attr( $name ) . '[target]" data-es-link-target value="_blank"' . checked( '_blank' === $link['target'], true, false ) . ' /> ' . esc_html__( 'باز شدن در تب جدید', 'erfansanat' ) . '</label>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * Normalize a link value to a safe array (url/label/target/rel).
	 *
	 * @param mixed $value Raw value.
	 * @return array{url:string,label:string,target:string,rel:string}
	 */
	public static function normalize_link( $value ): array {
		$defaults = array(
			'url'    => '',
			'label'  => '',
			'target' => '_self',
			'rel'    => '',
		);

		if ( is_string( $value ) ) {
			$value = array( 'url' => $value );
		}
		if ( ! is_array( $value ) ) {
			$value = array();
		}

		$link = array_merge( $defaults, $value );

		$link['url']    = self::safe_url( (string) $link['url'] );
		$link['target'] = in_array( $link['target'], array( '_blank', '_self' ), true ) ? $link['target'] : '_self';
		$link['rel']    = '_blank' === $link['target'] ? 'noopener noreferrer' : '';

		return $link;
	}

	/**
	 * Reject javascript:/data:/vbscript: URLs; allow http(s), mailto, tel, relative.
	 */
	public static function safe_url( string $url ): string {
		$url = trim( $url );
		if ( '' === $url ) {
			return '';
		}

		if ( preg_match( '/^(javascript|data|vbscript)\s*:/i', $url ) ) {
			return '';
		}

		return esc_url_raw( $url );
	}

	/**
	 * Icon field (P02-T006): choose only from the allowed icon set.
	 *
	 * @param mixed             $value Current icon key.
	 * @param array<string,mixed> $field Field definition.
	 */
	private static function icon( string $id, string $name, string $key, $value, array $field ): string {
		$allowed = self::allowed_icons();
		$value   = is_string( $value ) && isset( $allowed[ $value ] ) ? $value : '';

		$html = sprintf(
			'<select id="%s" name="%s" data-es-key="%s" class="es-icon">',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $key )
		);
		$html .= '<option value="">' . esc_html__( '— بدون آیکون —', 'erfansanat' ) . '</option>';
		foreach ( $allowed as $icon_key => $icon_label ) {
			$html .= sprintf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $icon_key ),
				selected( $value, $icon_key, false ),
				esc_html( $icon_label )
			);
		}
		$html .= '</select>';

		return $html;
	}

	/**
	 * Allowed icon set (key => label). The only icons an admin may pick.
	 *
	 * @return array<string,string>
	 */
	public static function allowed_icons(): array {
		return array(
			'phone'     => 'تلفن',
			'mobile'    => 'تلفن همراه',
			'email'     => 'ایمیل',
			'location'  => 'نشانی',
			'clock'     => 'ساعت کاری',
			'instagram' => 'اینستاگرام',
			'telegram'  => 'تلگرام',
			'linkedin'  => 'لینکدین',
			'whatsapp'  => 'واتس‌اپ',
			'aparat'    => 'آپارات',
			'check'     => 'تیک',
			'star'      => 'ستاره',
			'shield'    => 'گارانتی',
			'truck'     => 'ارسال',
		);
	}

	/**
	 * Repeater field (P02-T007).
	 *
	 * @param mixed             $value Current items.
	 * @param array<string,mixed> $field Field definition.
	 */
	private static function repeater( string $id, string $name, string $key, $value, array $field ): string {
		return self::render_repeater( $id, $name, $key, $value, $field );
	}

	/**
	 * Repeater field (P02-T007): add/remove/reorder items with stable naming.
	 *
	 * @param mixed             $value Current items (list of associative arrays).
	 * @param array<string,mixed> $field Field definition with subfields/min/max.
	 */
	private static function render_repeater( string $id, string $name, string $key, $value, array $field ): string {
		$items     = is_array( $value ) ? array_values( $value ) : array();
		$subfields = (array) ( $field['subfields'] ?? array() );
		$min       = (int) ( $field['min'] ?? 0 );
		$max       = (int) ( $field['max'] ?? 20 );

		$html  = '<div class="es-repeater" id="' . esc_attr( $id ) . '" data-es-repeater="' . esc_attr( $key ) . '" data-min="' . esc_attr( (string) $min ) . '" data-max="' . esc_attr( (string) $max ) . '">';
		$html .= '<div class="es-repeater__items" data-es-repeater-items>';
		foreach ( $items as $index => $item ) {
			$html .= self::repeater_item( $key, $name, (string) $index, is_array( $item ) ? $item : array(), $subfields );
		}
		$html .= '</div>';
		$html .= '<button type="button" class="button" data-es-repeater-add>' . esc_html__( 'افزودن آیتم', 'erfansanat' ) . '</button>';
		$html .= '<template data-es-repeater-template>' . self::repeater_item( $key, $name, '__INDEX__', array(), $subfields ) . '</template>';
		$html .= '</div>';

		return $html;
	}

	/**
	 * One repeater item row.
	 *
	 * @param array<string,mixed> $item      Item values.
	 * @param array<string,mixed> $subfields Subfield definitions.
	 */
	private static function repeater_item( string $key, string $name, string $index, array $item, array $subfields ): string {
		$html  = '<div class="es-repeater__item" data-es-repeater-item data-index="' . esc_attr( $index ) . '">';
		$html .= '<div class="es-repeater__controls">';
		$html .= '<button type="button" class="es-repeater__btn" data-es-repeater-up title="' . esc_attr__( 'انتقال به بالا', 'erfansanat' ) . '">↑</button>';
		$html .= '<button type="button" class="es-repeater__btn" data-es-repeater-down title="' . esc_attr__( 'انتقال به پایین', 'erfansanat' ) . '">↓</button>';
		$html .= '<button type="button" class="es-repeater__btn" data-es-repeater-remove title="' . esc_attr__( 'حذف آیتم', 'erfansanat' ) . '">×</button>';
		$html .= '</div>';
		$html .= '<div class="es-repeater__fields">';

		foreach ( $subfields as $subkey => $subfield ) {
			$val      = $item[ $subkey ] ?? ( $subfield['default'] ?? '' );
			$sub_name = $name . '[' . $index . '][' . $subkey . ']';
			$sub_id   = 'es-field-' . $key . '-' . $index . '-' . $subkey;
			$data_key = $key . '[' . $index . '][' . $subkey . ']';

			$html .= '<div class="es-field es-repeater__subfield">';
			$html .= '<label class="es-field__label" for="' . esc_attr( $sub_id ) . '">' . esc_html( $subfield['label'] ?? $subkey ) . '</label>';
			$html .= self::render_subfield( $subfield, $val, $sub_name, $sub_id, $data_key );
			$html .= '</div>';
		}

		$html .= '</div></div>';

		return $html;
	}

	/**
	 * Render a repeater subfield from its definition (not a top-level schema key).
	 *
	 * @param array<string,mixed> $subfield Subfield definition.
	 * @param mixed             $value    Current value.
	 */
	private static function render_subfield( array $subfield, $value, string $name, string $id, string $data_key ): string {
		$type = $subfield['type'] ?? 'text';

		switch ( $type ) {
			case 'select':
				return self::select( $id, $name, $data_key, $value, $subfield );
			case 'icon':
				return self::icon( $id, $name, $data_key, $value, $subfield );
			case 'textarea':
				return self::textarea( $id, $name, $data_key, (string) $value );
			case 'number':
				return self::number( $id, $name, $data_key, $value, $subfield );
			case 'toggle':
			case 'checkbox':
				return self::checkbox( $id, $name, $data_key, $value );
			case 'media':
				return self::media( $id, $name, $data_key, $value );
			case 'url':
			case 'email':
			case 'text':
			default:
				return self::input( $type, $id, $name, $data_key, (string) $value );
		}
	}
}
