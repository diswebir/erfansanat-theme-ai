<?php
/**
 * Variable product form (P06-T007). WooCommerce template override for
 * `single-product/variable.php`. Keeps the canonical variation lifecycle and
 * data attributes so WooCommerce + Variation Swatches for WooCommerce own the
 * selection/add-to-cart behaviour on a real store (AC-01). The static mirror
 * swatches + fallback quantity stepper are rendered by purchase.php only when
 * WooCommerce is absent (QA / static parity).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\AttributeMap;

global $product;

$es_product = ( isset( $product ) && is_object( $product ) && method_exists( $product, 'get_id' ) )
	? $product
	: ( function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : false );

if ( ! $es_product ) {
	return;
}

$es_id          = $es_product->get_id();
$es_is_variable = method_exists( $es_product, 'is_type' ) && $es_product->is_type( 'variable' );
$es_attributes  = ( $es_is_variable && method_exists( $es_product, 'get_variation_attributes' ) )
	? $es_product->get_variation_attributes()
	: array();
$es_variations  = ( $es_is_variable && method_exists( $es_product, 'get_available_variations' ) )
	? $es_product->get_available_variations()
	: array();

$es_attr_index = 0;
$es_attr_count = count( $es_attributes );
?>
<form
	class="variations_form cart es-variable-product"
	action="<?php echo esc_url( $es_product->get_permalink() ); ?>"
	method="post"
	enctype="multipart/form-data"
	data-product_id="<?php echo esc_attr( (string) $es_id ); ?>"
	data-product_variations="<?php echo esc_attr( wp_json_encode( $es_variations ) ); ?>"
>
	<?php
	/** Hook: woocommerce_before_variations_form. */
	do_action( 'woocommerce_before_variations_form' );
	?>

	<?php if ( empty( $es_variations ) && false !== $es_variations ) : ?>
		<p class="stock out-of-stock rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-bold text-rose-700">
			<?php echo esc_html__( 'این محصول در حال حاضر موجود نیست.', 'erfansanat' ); ?>
		</p>
	<?php else : ?>
		<table class="variations" cellspacing="0" role="presentation">
			<tbody>
				<?php foreach ( $es_attributes as $es_attr_name => $es_options ) : ?>
					<?php
					$es_attr_index++;
					$es_field_id = sanitize_title( $es_attr_name );
					$es_label    = function_exists( 'wc_attribute_label' ) ? wc_attribute_label( $es_attr_name ) : AttributeMap::label( $es_attr_name );
					$es_selected = isset( $_REQUEST[ 'attribute_' . $es_field_id ] ) ? sanitize_title( wp_unslash( $_REQUEST[ 'attribute_' . $es_field_id ] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					?>
					<tr>
						<th class="label">
							<label for="<?php echo esc_attr( $es_field_id ); ?>"><?php echo esc_html( $es_label ); ?></label>
						</th>
						<td class="value">
							<?php
							if ( function_exists( 'wc_dropdown_variation_attribute_options' ) ) {
								wc_dropdown_variation_attribute_options(
									array(
										'options'   => $es_options,
										'attribute' => $es_attr_name,
										'product'   => $es_product,
										'selected'  => $es_selected,
									)
								);
							} else {
								// QA fallback: same name/id contract as Woo so swatches can bind.
								echo '<select id="' . esc_attr( $es_field_id ) . '" name="attribute_' . esc_attr( $es_field_id ) . '" data-attribute_name="attribute_' . esc_attr( $es_field_id ) . '">';
								echo '<option value="">' . esc_html__( 'یک گزینه را انتخاب کنید', 'erfansanat' ) . '</option>';
								foreach ( (array) $es_options as $es_option ) {
									$es_term  = taxonomy_exists( $es_attr_name ) ? get_term_by( 'slug', $es_option, $es_attr_name ) : false;
									$es_label_option = ( $es_term && ! is_wp_error( $es_term ) ) ? $es_term->name : $es_option;
									echo '<option value="' . esc_attr( $es_option ) . '"' . selected( $es_selected, $es_option, false ) . '>' . esc_html( $es_label_option ) . '</option>';
								}
								echo '</select>';
							}

							if ( $es_attr_index === $es_attr_count ) {
								echo '<a class="reset_variations" href="#">' . esc_html__( 'پاک‌کردن انتخاب‌ها', 'erfansanat' ) . '</a>';
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<?php
		/** Hook: woocommerce_after_variations_table. */
		do_action( 'woocommerce_after_variations_table' );
		?>

		<div class="single_variation_wrap">
			<?php
			/** Hook: woocommerce_before_single_variation. */
			do_action( 'woocommerce_before_single_variation' );

			/** Hook: woocommerce_single_variation — variation price + add-to-cart. */
			do_action( 'woocommerce_single_variation' );

			/** Hook: woocommerce_after_single_variation. */
			do_action( 'woocommerce_after_single_variation' );
			?>
		</div>
	<?php endif; ?>

	<?php
	/** Hook: woocommerce_after_variations_form. */
	do_action( 'woocommerce_after_variations_form' );
	?>
</form>
