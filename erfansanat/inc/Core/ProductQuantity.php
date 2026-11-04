<?php
/**
 * Quantity rules shared by the PDP stepper and (later) cart validation.
 *
 * Reads the legacy _min_qty/_max_qty/_step_qty product meta used by the
 * static design. On a real WooCommerce store the canonical quantity input is
 * still rendered by Woo/WPC Product Quantity; this helper only normalises the
 * fallback stepper and provides server-side validation for AJAX cart writes.
 *
 * @package ErfanSanat
 */

namespace ErfanSanat\Theme\Core;

defined( 'ABSPATH' ) || exit;

final class ProductQuantity {

	/**
	 * Normalised quantity rules for a product (or a variation when supplied).
	 *
	 * @param object $product      Product object exposing get_meta()/get_id().
	 * @param int    $variation_id Optional variation id.
	 * @return array{min:int,max:int,step:int,initial:int}
	 */
	public static function rules( $product, int $variation_id = 0 ): array {
		$source = $product;
		if ( $variation_id > 0 && function_exists( 'wc_get_product' ) ) {
			$variation = wc_get_product( $variation_id );
			if ( $variation ) {
				$source = $variation;
			}
		}

		$meta = static function ( string $key, $default = 0 ) use ( $source ) {
			if ( is_object( $source ) && method_exists( $source, 'get_meta' ) ) {
				$value = $source->get_meta( $key );
				return ( '' === $value || null === $value ) ? $default : $value;
			}
			return $default;
		};

		$min  = max( 1, (int) $meta( '_min_qty', 1 ) );
		$step = max( 1, (int) $meta( '_step_qty', 1 ) );
		$max  = (int) $meta( '_max_qty', 0 );

		if ( is_object( $source ) && method_exists( $source, 'get_stock_quantity' ) ) {
			$stock = $source->get_stock_quantity();
			if ( null !== $stock && (int) $stock > 0 ) {
				$max = $max > 0 ? min( $max, (int) $stock ) : (int) $stock;
			}
		}

		if ( $max > 0 && $max < $min ) {
			$max = $min;
		}

		$rules = array(
			'min'     => $min,
			'max'     => $max, // 0 = unlimited.
			'step'    => $step,
			'initial' => $min,
		);

		/**
		 * Filter the fallback quantity rules. Real Woo/WPC quantity args are
		 * intentionally not overridden here.
		 *
		 * @param array  $rules        Normalised rules.
		 * @param object $product      Parent product.
		 * @param int    $variation_id Variation id (0 for simple).
		 */
		$rules = apply_filters( 'es_product_quantity_rules', $rules, $product, $variation_id );

		$rules['min']     = max( 1, (int) $rules['min'] );
		$rules['step']    = max( 1, (int) $rules['step'] );
		$rules['max']     = max( 0, (int) $rules['max'] );
		$rules['initial'] = $rules['min'];
		if ( $rules['max'] > 0 && $rules['max'] < $rules['min'] ) {
			$rules['max'] = $rules['min'];
		}

		return $rules;
	}

	/**
	 * Server-side validation. Returns true or an error code string.
	 *
	 * @param object $product      Product object.
	 * @param mixed  $quantity     Raw quantity.
	 * @param int    $variation_id Optional variation id.
	 * @return true|string
	 */
	public static function validate( $product, $quantity, int $variation_id = 0 ) {
		if ( ! is_numeric( $quantity ) || (string) (int) $quantity !== (string) $quantity ) {
			return 'invalid_quantity';
		}

		$qty   = (int) $quantity;
		$rules = self::rules( $product, $variation_id );

		if ( $qty < $rules['min'] ) {
			return 'below_minimum';
		}
		if ( $rules['max'] > 0 && $qty > $rules['max'] ) {
			return 'above_maximum';
		}
		if ( 0 !== ( ( $qty - $rules['min'] ) % $rules['step'] ) ) {
			return 'invalid_step';
		}

		return true;
	}
}
