<?php
/** Native WooCommerce purchase form or truthful quote/contact actions. */

defined( 'ABSPATH' ) || exit;

$product = \ErfanSanat\Theme\Woo\ProductTemplate::product( $args['product'] ?? null );
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $product );
if ( ! $product || ! $product_id ) { return; }
$sale_mode = \ErfanSanat\Theme\Woo\ProductTemplate::text( $product_id, 'true_sale', 'false' );
$can_buy = in_array( strtolower( $sale_mode ), array( 'true', '1', 'yes' ), true );
$notice = \ErfanSanat\Theme\Woo\ProductTemplate::text( $product_id, 'before-sale-c' );
$title = method_exists( $product, 'get_name' ) ? (string) $product->get_name() : '';
$phone = class_exists( '\\ErfanSanat\\Theme\\Helpers\\Options' ) ? \ErfanSanat\Theme\Helpers\Options::get( 'header.phone', '' ) : '';
$phone = is_scalar( $phone ) ? trim( (string) $phone ) : '';
$phone_href = preg_replace( '/[^0-9+]/', '', $phone );
$price_html = $can_buy && method_exists( $product, 'get_price_html' ) ? (string) $product->get_price_html() : '';
?>
<div id="product-purchase-box" class="rounded-3xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-7">
	<div class="mb-5 flex items-start justify-between gap-4"><div><p class="mb-1 text-xs font-bold text-emerald-700"><?php echo $can_buy ? esc_html__( 'خرید محصول', 'erfansanat-theme' ) : esc_html__( 'راهنمای خرید', 'erfansanat-theme' ); ?></p><h2 class="text-lg font-extrabold leading-7 text-slate-900"><?php echo esc_html( $title ); ?></h2></div><?php if ( '' !== $price_html ) : ?><div class="text-left text-lg font-black text-emerald-800"><?php echo wp_kses_post( $price_html ); ?></div><?php endif; ?></div>
	<?php if ( $can_buy ) : ?>
		<div class="woocommerce-product-add-to-cart product-purchase-form"><?php if ( function_exists( 'woocommerce_template_single_add_to_cart' ) ) { woocommerce_template_single_add_to_cart(); } ?></div>
	<?php else : ?>
		<div class="flex flex-col gap-3"><p class="text-sm leading-7 text-slate-600">برای دریافت قیمت و زمان تحویل، با واحد فروش تماس بگیرید.</p><?php if ( '' !== $phone_href ) : ?><a class="flex min-h-[46px] items-center justify-center gap-2 rounded-xl bg-emerald-800 px-4 py-3 text-sm font-extrabold text-white transition hover:bg-emerald-900" href="tel:<?php echo esc_attr( $phone_href ); ?>">استعلام قیمت تلفنی <span dir="ltr"><?php echo esc_html( $phone ); ?></span></a><?php elseif ( function_exists( 'home_url' ) ) : ?><a class="flex min-h-[46px] items-center justify-center rounded-xl bg-emerald-800 px-4 py-3 text-sm font-extrabold text-white" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">درخواست راهنمایی</a><?php endif; ?><?php if ( function_exists( 'is_product' ) ) : ?><a href="#product-details" data-scroll-to-tab="desc" class="text-center text-xs font-bold text-emerald-800">مشاهده توضیحات محصول</a><?php endif; ?></div>
	<?php endif; ?>
	<?php if ( '' !== $notice ) : ?><div class="mt-5 rounded-2xl border border-amber-200/80 bg-amber-50 px-4 py-3 text-sm leading-7 text-amber-950"><?php echo wp_kses_post( $notice ); ?></div><?php endif; ?>
	<?php if ( method_exists( $product, 'get_average_rating' ) && (float) $product->get_average_rating() > 0 && function_exists( 'wc_get_rating_html' ) ) : ?><div class="mb-4 flex items-center gap-2"><?php echo wp_kses_post( wc_get_rating_html( $product->get_average_rating(), method_exists( $product, 'get_rating_count' ) ? $product->get_rating_count() : 0 ) ); ?><?php if ( method_exists( $product, 'get_review_count' ) ) : ?><span class="text-xs text-slate-500"><?php echo esc_html( number_format_i18n( (int) $product->get_review_count() ) ); ?> دیدگاه</span><?php endif; ?></div><?php endif; ?>
	<?php do_action( 'woocommerce_product_meta_start' ); ?>
	<?php if ( method_exists( $product, 'get_sku' ) && '' !== (string) $product->get_sku() ) : ?><p class="mt-5 border-t border-slate-100 pt-4 text-xs text-slate-500">کد محصول: <span dir="ltr" class="font-bold"><?php echo esc_html( (string) $product->get_sku() ); ?></span></p><?php endif; ?>
	<?php do_action( 'woocommerce_product_meta_end' ); ?>
</div>
