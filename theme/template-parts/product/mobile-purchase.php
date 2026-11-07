<?php
/** Compact mobile link to the real purchase form or contact action. */

defined( 'ABSPATH' ) || exit;
$product = \ErfanSanat\Theme\Woo\ProductTemplate::product( $args['product'] ?? null );
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $product );
if ( ! $product || ! $product_id ) { return; }
$mode = \ErfanSanat\Theme\Woo\ProductTemplate::text( $product_id, 'true_sale', 'false' );
$can_buy = in_array( strtolower( $mode ), array( 'true', '1', 'yes' ), true );
$phone = class_exists( '\\ErfanSanat\\Theme\\Helpers\\Options' ) ? \ErfanSanat\Theme\Helpers\Options::get( 'header.phone', '' ) : '';
$phone_href = is_scalar( $phone ) ? preg_replace( '/[^0-9+]/', '', (string) $phone ) : '';
?>
<div class="fixed inset-x-0 bottom-0 z-40 flex items-center justify-between gap-3 border-t border-emerald-100 bg-white/95 px-4 py-3 shadow-[0_-8px_30px_-15px_rgba(15,23,42,0.2)] backdrop-blur-xl lg:hidden"><div class="min-w-0"><?php if ( $can_buy && method_exists( $product, 'get_price_html' ) && '' !== (string) $product->get_price_html() ) : ?><span class="block truncate text-sm font-black text-emerald-800"><?php echo wp_kses_post( $product->get_price_html() ); ?></span><?php else : ?><span class="block text-xs font-bold text-slate-600"><?php echo $can_buy ? esc_html__( 'خرید آنلاین', 'erfansanat-theme' ) : esc_html__( 'استعلام قیمت', 'erfansanat-theme' ); ?></span><?php endif; ?></div><?php if ( $can_buy ) : ?><a href="#product-purchase-box" class="rounded-xl bg-emerald-800 px-4 py-2.5 text-xs font-extrabold text-white">انتخاب و خرید</a><?php elseif ( '' !== $phone_href ) : ?><a href="tel:<?php echo esc_attr( $phone_href ); ?>" class="rounded-xl bg-emerald-800 px-4 py-2.5 text-xs font-extrabold text-white">تماس برای استعلام</a><?php endif; ?></div>
