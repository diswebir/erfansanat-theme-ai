<?php
/**
 * Reproducible WooCommerce fixtures for Phase 6 browser/integration QA.
 * Include this file to obtain fixture metadata, or call
 * es_harness_seed_phase6_products() from a disposable WooCommerce test site.
 */
$fixtures = array(
	'simple' => array(
		'title' => 'ESE Phase 6 Simple Test Product',
		'sku' => 'ESE-P6-SIMPLE',
		'type' => 'simple',
		'price' => '125000',
		'meta' => array( 'true_sale' => 'true', 'zireonvan' => 'نمونه محصول قابل خرید', 'english_titl' => 'Simple test product', 'gs1_id' => '6260491807210', 'pa_color' => 'سفید' ),
	),
	'variable' => array(
		'title' => 'ESE Phase 6 Variable Test Product',
		'sku' => 'ESE-P6-VARIABLE',
		'type' => 'variable',
		'price' => '185000',
		'meta' => array( 'true_sale' => 'true', 'product-feature' => array( 'item-0' => array( 'add-new-feature' => 'دارای دو رنگ' ) ), 'pa_color' => 'سفید، مشکی', 'pa_wattage' => '۱۲ وات' ),
		'variations' => array( array( 'sku' => 'ESE-P6-VARIABLE-WHITE', 'price' => '185000', 'attributes' => array( 'pa_color' => 'white' ) ), array( 'sku' => 'ESE-P6-VARIABLE-BLACK', 'price' => '195000', 'attributes' => array( 'pa_color' => 'black' ) ) ),
	),
	'inquiry' => array(
		'title' => 'ESE Phase 6 Inquiry Test Product',
		'sku' => 'ESE-P6-INQUIRY',
		'type' => 'simple',
		'price' => '',
		'meta' => array( 'true_sale' => 'false', 'before-sale-c' => 'برای ساخت سفارشی با واحد فروش تماس بگیرید.', 'project-ids' => array( 'item-0' => array( 'id' => 0 ) ), 'product-catalog-file' => '' ),
	),
);

if ( ! function_exists( 'es_harness_seed_phase6_products' ) ) {
	/** Create or update three deterministic test products; return their IDs. */
	function es_harness_seed_phase6_products(): array {
		global $fixtures;
		if ( ! class_exists( 'WC_Product_Simple' ) || ! class_exists( 'WC_Product_Variable' ) ) { return array(); }
		$ids = array();
		foreach ( $fixtures as $key => $fixture ) {
			$product_id = function_exists( 'wc_get_product_id_by_sku' ) ? absint( wc_get_product_id_by_sku( $fixture['sku'] ) ) : 0;
			$product = $product_id ? wc_get_product( $product_id ) : null;
			if ( ! is_object( $product ) ) { $product = 'variable' === $fixture['type'] ? new WC_Product_Variable() : new WC_Product_Simple(); }
			$product->set_name( $fixture['title'] );
			$product->set_status( 'publish' );
			$product->set_sku( $fixture['sku'] );
			if ( '' !== $fixture['price'] ) { $product->set_regular_price( $fixture['price'] ); }
			$use_global_color = function_exists( 'taxonomy_exists' ) && taxonomy_exists( 'pa_color' );
			if ( 'variable' === $fixture['type'] && class_exists( 'WC_Product_Attribute' ) ) {
				$attribute = new WC_Product_Attribute();
				$attribute->set_name( $use_global_color ? 'pa_color' : 'Color' );
				$options = array( 'White', 'Black' );
				if ( $use_global_color ) {
					$options = array();
					foreach ( array( 'White', 'Black' ) as $name ) {
						$term = function_exists( 'term_exists' ) ? term_exists( $name, 'pa_color' ) : 0;
						if ( ! $term && function_exists( 'wp_insert_term' ) ) { $term = wp_insert_term( $name, 'pa_color' ); }
						if ( function_exists( 'is_wp_error' ) && is_wp_error( $term ) ) { continue; }
						$term_id = is_array( $term ) ? absint( $term['term_id'] ?? 0 ) : absint( is_numeric( $term ) ? $term : 0 );
						if ( $term_id ) { $options[] = $term_id; }
					}
				}
				if ( $use_global_color && 2 !== count( $options ) ) {
					$use_global_color = false;
					$options = array( 'White', 'Black' );
					$attribute->set_name( 'Color' );
				}
				$attribute->set_options( $options );
				if ( $use_global_color && function_exists( 'wc_attribute_taxonomy_id_by_name' ) ) { $attribute->set_id( absint( wc_attribute_taxonomy_id_by_name( 'pa_color' ) ) ); }
				$attribute->set_position( 0 );
				$attribute->set_visible( true );
				$attribute->set_variation( true );
				$product->set_attributes( array( $attribute ) );
			}
			$product_id = (int) $product->save();
			foreach ( $fixture['meta'] as $meta_key => $meta_value ) { update_post_meta( $product_id, $meta_key, $meta_value ); }
			$ids[ $key ] = $product_id;
			if ( 'variable' !== $fixture['type'] || ! class_exists( 'WC_Product_Variation' ) ) { continue; }
			foreach ( $fixture['variations'] as $variation_data ) {
				$variation_id = function_exists( 'wc_get_product_id_by_sku' ) ? absint( wc_get_product_id_by_sku( $variation_data['sku'] ) ) : 0;
				$variation = $variation_id ? wc_get_product( $variation_id ) : null;
				if ( ! $variation instanceof WC_Product_Variation ) { $variation = new WC_Product_Variation(); }
				$variation->set_parent_id( $product_id );
				$variation->set_sku( $variation_data['sku'] );
				$variation->set_regular_price( $variation_data['price'] );
				$variation_attributes = $variation_data['attributes'];
				if ( ! $use_global_color ) {
					$color = (string) ( $variation_attributes['pa_color'] ?? '' );
					$variation_attributes = array( 'color' => 'white' === $color ? 'White' : 'Black' );
				}
				$variation->set_attributes( $variation_attributes );
				$variation->set_status( 'publish' );
				$variation->save();
			}
		}
		return $ids;
	}
}

if ( ! function_exists( 'es_harness_cleanup_phase6_products' ) ) {
	/** Delete only the deterministic fixture products and their variations. */
	function es_harness_cleanup_phase6_products(): void {
		if ( ! function_exists( 'wc_get_product_id_by_sku' ) || ! function_exists( 'wp_delete_post' ) ) { return; }
		foreach ( array( 'ESE-P6-SIMPLE', 'ESE-P6-VARIABLE', 'ESE-P6-INQUIRY', 'ESE-P6-VARIABLE-WHITE', 'ESE-P6-VARIABLE-BLACK' ) as $sku ) {
			$id = absint( wc_get_product_id_by_sku( $sku ) );
			if ( $id ) { wp_delete_post( $id, true ); }
		}
	}
}

return $fixtures;
