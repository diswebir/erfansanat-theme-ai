<?php
/** WooCommerce product attribute normalization in the static-reference order. */

namespace ErfanSanat\Theme\Woo;

defined( 'ABSPATH' ) || exit;

/** Reads product attributes through WooCommerce's product API, never raw post meta. */
final class AttributeData {

	/** @var string[] */
	private const ORDER = array(
		'pa_after-sales-support', 'pa_beam-angle', 'pa_body-material', 'pa_brand', 'pa_bulb-type',
		'pa_cable-material', 'pa_certifications', 'pa_color', 'pa_diameter', 'pa_diffuser-color',
		'pa_input-voltage', 'pa_intensity', 'pa_ip-rating', 'pa_led-chip-type', 'pa_lifespan',
		'pa_size', 'pa_warranty', 'pa_wattage',
	);

	/** Get the stable display order used by the product specification reference. */
	public static function order(): array {
		return self::ORDER;
	}

	/** Return normalized non-empty attribute rows, fixed taxonomies first. */
	public static function rows( $product ): array {
		if ( ! is_object( $product ) || ! method_exists( $product, 'get_attributes' ) ) {
			return array();
		}
		$attributes = $product->get_attributes();
		if ( ! is_array( $attributes ) ) {
			return array();
		}

		$by_name = array();
		foreach ( $attributes as $attribute ) {
			if ( ! is_object( $attribute ) || ! method_exists( $attribute, 'get_name' ) ) {
				continue;
			}
			$name = sanitize_title( (string) $attribute->get_name() );
			if ( '' !== $name ) {
				$by_name[ $name ] = $attribute;
			}
		}

		$ordered = array();
		foreach ( self::ORDER as $name ) {
			if ( isset( $by_name[ $name ] ) ) {
				$ordered[ $name ] = $by_name[ $name ];
			}
		}
		foreach ( $by_name as $name => $attribute ) {
			if ( ! isset( $ordered[ $name ] ) ) {
				$ordered[ $name ] = $attribute;
			}
		}

		$rows = array();
		$product_id = method_exists( $product, 'get_id' ) ? absint( $product->get_id() ) : 0;
		foreach ( $ordered as $name => $attribute ) {
			$value = self::value( $product, $attribute, $name, $product_id );
			if ( '' === $value ) {
				continue;
			}
			$label = function_exists( 'wc_attribute_label' ) ? wc_attribute_label( $name, $product ) : '';
			if ( '' === (string) $label ) {
				$taxonomy = function_exists( 'get_taxonomy' ) ? get_taxonomy( $name ) : null;
				$label = is_object( $taxonomy ) && isset( $taxonomy->labels->singular_name ) ? $taxonomy->labels->singular_name : ( method_exists( $attribute, 'get_name' ) ? $attribute->get_name() : $name );
			}
			$row = array( 'name' => $name, 'label' => (string) $label, 'value' => $value, 'swatch' => '' );
			if ( 'pa_color' === $name ) {
				$row['swatch'] = self::color_swatch( $product, $name );
			}
			$rows[] = $row;
		}
		return $rows;
	}

	/** Read a human-readable value from WooCommerce taxonomy/custom attributes. */
	private static function value( $product, $attribute, string $name, int $product_id ): string {
		if ( method_exists( $attribute, 'is_taxonomy' ) && $attribute->is_taxonomy() && function_exists( 'wc_get_product_terms' ) && $product_id > 0 ) {
			$names = wc_get_product_terms( $product_id, $name, array( 'fields' => 'names' ) );
			if ( is_array( $names ) && ! is_wp_error( $names ) ) {
				return implode( ', ', array_filter( array_map( 'strval', $names ) ) );
			}
		}
		if ( method_exists( $product, 'get_attribute' ) ) {
			$value = $product->get_attribute( $name );
			if ( is_scalar( $value ) ) {
				return trim( (string) $value );
			}
		}
		$options = method_exists( $attribute, 'get_options' ) ? $attribute->get_options() : array();
		if ( ! is_array( $options ) ) {
			return '';
		}
		return implode( ', ', array_filter( array_map( static function ( $option ): string {
			return is_scalar( $option ) ? trim( (string) $option ) : '';
		}, $options ) ) );
	}

	/** Resolve optional color term metadata as a safe CSS color. */
	private static function color_swatch( $product, string $taxonomy ): string {
		if ( ! method_exists( $product, 'get_id' ) || ! function_exists( 'wc_get_product_terms' ) || ! function_exists( 'get_term_meta' ) ) {
			return '';
		}
		$terms = wc_get_product_terms( absint( $product->get_id() ), $taxonomy, array( 'fields' => 'all' ) );
		if ( ! is_array( $terms ) || is_wp_error( $terms ) || empty( $terms[0]->term_id ) ) {
			return '';
		}
		$raw = get_term_meta( (int) $terms[0]->term_id, 'color', true );
		$color = function_exists( 'sanitize_hex_color' ) ? sanitize_hex_color( (string) $raw ) : ( preg_match( '/^#[a-f0-9]{3}(?:[a-f0-9]{3})?$/i', (string) $raw ) ? (string) $raw : '' );
		return is_string( $color ) ? $color : '';
	}
}
