<?php
/** Product identity and title fields using the site's exact legacy meta keys. */

namespace ErfanSanat\Theme\Admin\Metaboxes\Tabs;

defined( 'ABSPATH' ) || exit;

/** Renders the first tab of the product information meta box. */
final class ProductIdentityTab {

	/** Field keys are historical database contracts; never normalize their spelling. */
	public static function text_fields(): array {
		return array(
			'gs1_id'        => 'شناسه GS1',
			'national_code' => 'کد ملی کالا',
			'hs_id'         => 'شناسه HS',
			'isic_id'       => 'شناسه ISIC',
			'cpc_id'        => 'شناسه CPC',
			'product_id'    => 'شناسه محصول',
			'zireonvan'     => 'زیرعنوان محصول',
			'english_titl'  => 'عنوان انگلیسی',
			'before-sale-c' => 'اطلاعیه پیش از خرید',
		);
	}

	/** Render escaped text inputs and the strict true_sale radio group. */
	public static function render( int $post_id ): string {
		$html = '<div class="es-product-meta__identity">';
		foreach ( self::text_fields() as $key => $label ) {
			$value = get_post_meta( $post_id, $key, true );
			$value = is_scalar( $value ) ? (string) $value : '';
			$html .= '<p class="es-product-meta__field"><label for="es-product-meta-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label>';
			$html .= '<input class="widefat" type="text" id="es-product-meta-' . esc_attr( $key ) . '" name="es_product_meta[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '"></p>';
		}
		$sale = get_post_meta( $post_id, 'true_sale', true );
		$sale = in_array( $sale, array( 'true', 'false' ), true ) ? $sale : 'false';
		$html .= '<fieldset class="es-product-meta__field"><legend>' . esc_html( 'نوع فروش' ) . '</legend>';
		foreach ( array( 'true' => 'خرید آنلاین', 'false' => 'استعلام / تماس' ) as $value => $label ) {
			$checked = $sale === $value ? ' checked="checked"' : '';
			$html .= '<label><input type="radio" name="es_product_meta[true_sale]" value="' . esc_attr( $value ) . '"' . $checked . '> ' . esc_html( $label ) . '</label> ';
		}
		$html .= '</fieldset></div>';
		return $html;
	}
}
