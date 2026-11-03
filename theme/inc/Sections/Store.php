<?php
/**
 * Read/write section order, visibility, and field data in post meta.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Sections;

defined( 'ABSPATH' ) || exit;

/** Normalizes the `_es_sections` state against the current section registry. */
final class Store {

	public const META_KEY = '_es_sections';

	/** Return the complete state, merging stored values over current defaults. */
	public static function get( int $post_id, string $page = 'home' ): array {
		$defaults = self::defaults( $page );
		if ( $post_id < 1 || ! function_exists( 'get_post_meta' ) ) {
			return $defaults;
		}
		$stored = get_post_meta( $post_id, self::META_KEY, true );
		return self::normalize( is_array( $stored ) ? $stored : array(), $page );
	}

	/** Save only recognized sections and schema-defined field data. */
	public static function save( int $post_id, array $state, string $page = 'home' ): bool {
		if ( $post_id < 1 || ! function_exists( 'update_post_meta' ) ) {
			return false;
		}
		return (bool) update_post_meta( $post_id, self::META_KEY, self::normalize( $state, $page ) );
	}

	/** Return registry order with all sections enabled and their default data. */
	public static function defaults( string $page = 'home' ): array {
		$sections = Registry::get( $page );
		$order = array_keys( $sections );
		$enabled = array();
		$data = array();
		foreach ( $sections as $key => $section ) {
			$enabled[ $key ] = true;
			$data[ $key ] = is_array( $section['default_data'] ?? null ) ? $section['default_data'] : array();
		}
		return array( 'order' => $order, 'enabled' => $enabled, 'data' => $data );
	}

	/** Merge and clean stored state; unregistered keys are discarded. */
	private static function normalize( array $state, string $page ): array {
		$sections = Registry::get( $page );
		$defaults = self::defaults( $page );
		$keys = array_fill_keys( array_keys( $sections ), true );

		$order = array();
		$seen = array();
		$requested_order = is_array( $state['order'] ?? null ) ? $state['order'] : array();
		foreach ( $requested_order as $key ) {
			if ( is_string( $key ) && isset( $keys[ $key ] ) && ! isset( $seen[ $key ] ) ) {
				$order[] = $key;
				$seen[ $key ] = true;
			}
		}
		foreach ( $defaults['order'] as $key ) {
			if ( ! isset( $seen[ $key ] ) ) {
				$order[] = $key;
			}
		}

		$enabled_values = is_array( $state['enabled'] ?? null ) ? $state['enabled'] : array();
		$data_values = is_array( $state['data'] ?? null ) ? $state['data'] : array();
		$enabled = array();
		$data = array();
		foreach ( $sections as $key => $section ) {
			$enabled[ $key ] = array_key_exists( $key, $enabled_values )
				? self::to_bool( $enabled_values[ $key ] )
				: true;
			$defaults_for_section = $defaults['data'][ $key ];
			$submitted = is_array( $data_values[ $key ] ?? null ) ? $data_values[ $key ] : array();
			$data[ $key ] = self::sanitize_data( $section, $defaults_for_section, $submitted );
		}
		return array( 'order' => $order, 'enabled' => $enabled, 'data' => $data );
	}

	/** Sanitize known fields and preserve defaults for absent or invalid data. */
	private static function sanitize_data( array $section, array $defaults, array $submitted ): array {
		$data = $defaults;
		foreach ( $section['fields'] as $field ) {
			$id = $field['id'] ?? '';
			if ( ! is_string( $id ) || ! array_key_exists( $id, $submitted ) ) {
				continue;
			}
			if ( class_exists( '\\ErfanSanat\\Theme\\Admin\\Fields\\Sanitizer' ) ) {
				$data[ $id ] = \ErfanSanat\Theme\Admin\Fields\Sanitizer::sanitize( $field, $submitted[ $id ] );
			} else {
				$data[ $id ] = is_scalar( $submitted[ $id ] ) ? sanitize_text_field( (string) $submitted[ $id ] ) : ( $field['default'] ?? ( $defaults[ $id ] ?? '' ) );
			}
		}
		return $data;
	}

	/** Interpret common form booleans without treating the string "false" as true. */
	private static function to_bool( $value ): bool {
		if ( is_bool( $value ) ) {
			return $value;
		}
		return in_array( $value, array( 1, '1', 'on', 'yes', 'true' ), true );
	}
}
