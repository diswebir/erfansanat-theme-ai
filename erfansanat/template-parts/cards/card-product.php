<?php
/**
 * Standard product card (P05-T003). DOM parity with static-mirror/shop.html.
 *
 * data-product-* attributes carry real values for main.js filter/sort/search.
 * `true_sale` decides the purchase mode (online vs inquiry, AC-02); price,
 * image and category come from the DB (AC-03). IP/voltage are read from the
 * WooCommerce attribute taxonomies; rating/min-qty are filterable for the live
 * site. Missing optional data degrades gracefully.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\ViewCounter;

$es_product_id = isset( $args['product_id'] ) ? (int) $args['product_id'] : (int) get_the_ID();
if ( ! $es_product_id || 'product' !== get_post_type( $es_product_id ) ) {
	return;
}

$es_title = get_the_title( $es_product_id );
$es_link  = (string) get_permalink( $es_product_id );

// Image: featured -> theme fallback.
$es_thumb_id = (int) get_post_thumbnail_id( $es_product_id );
$es_img      = $es_thumb_id ? (string) wp_get_attachment_image_url( $es_thumb_id, 'large' ) : '';
if ( ! $es_img ) {
	$es_img = ES_ASSETS_URI . 'images/light-tunnel.jpg';
}

// Category terms.
$es_cat_terms   = get_the_terms( $es_product_id, 'product_cat' );
$es_cat_names   = ( $es_cat_terms && ! is_wp_error( $es_cat_terms ) ) ? wp_list_pluck( $es_cat_terms, 'name' ) : array();
$es_primary_cat = isset( $es_cat_names[0] ) ? $es_cat_names[0] : '';
$es_cats_attr   = implode( '|', $es_cat_names );

// Purchase mode from true_sale ("true" = online, else inquiry).
$es_true_sale   = (string) get_post_meta( $es_product_id, 'true_sale', true );
$es_purchasable = ( 'true' === $es_true_sale );

// Price (standard WooCommerce meta).
$es_price_raw   = (string) get_post_meta( $es_product_id, '_price', true );
$es_price_num   = is_numeric( $es_price_raw ) ? ( 0 + $es_price_raw ) : 0;
$es_regular_raw = (string) get_post_meta( $es_product_id, '_regular_price', true );
$es_regular_num = is_numeric( $es_regular_raw ) ? ( 0 + $es_regular_raw ) : 0;
$es_price_attr  = $es_purchasable ? (string) $es_price_num : '0';
$es_has_discount = $es_purchasable && $es_regular_num > $es_price_num && $es_price_num > 0;

// IP / voltage from WooCommerce attribute taxonomies.
$es_ip_terms = taxonomy_exists( 'pa_ip-rating' ) ? get_the_terms( $es_product_id, 'pa_ip-rating' ) : false;
$es_ip       = ( $es_ip_terms && ! is_wp_error( $es_ip_terms ) ) ? $es_ip_terms[0]->name : '';
$es_v_terms  = taxonomy_exists( 'pa_input-voltage' ) ? get_the_terms( $es_product_id, 'pa_input-voltage' ) : false;
$es_voltage  = ( $es_v_terms && ! is_wp_error( $es_v_terms ) ) ? $es_v_terms[0]->name : '';

// Views / rating.
$es_views  = ViewCounter::get( $es_product_id );
$es_rating = (string) apply_filters( 'es_product_card_rating', (string) get_post_meta( $es_product_id, '_wc_average_rating', true ), $es_product_id );

// SKU: industrial product_id meta, else WooCommerce _sku.
$es_sku = (string) get_post_meta( $es_product_id, 'product_id', true );
if ( '' === $es_sku ) {
	$es_sku = (string) get_post_meta( $es_product_id, '_sku', true );
}

// Short description: excerpt, else subtitle meta.
$es_excerpt = (string) get_post_field( 'post_excerpt', $es_product_id );
if ( '' === trim( $es_excerpt ) ) {
	$es_excerpt = (string) get_post_meta( $es_product_id, 'zireonvan', true );
}

// Minimum order quantity (filterable for the live WPC Quantity key).
$es_min_qty = (string) apply_filters( 'es_product_card_min_qty', (string) get_post_meta( $es_product_id, '_min_qty', true ), $es_product_id );

// Search text: title + description + categories + sku + ip + voltage.
$es_search_text = trim( $es_title . ' ' . $es_excerpt . ' ' . implode( ' ', $es_cat_names ) . ' ' . $es_sku . ' ' . $es_ip . ' ' . $es_voltage );

// Persian digits + price formatting.
$es_fa_digits = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_fa        = static function ( $s ) use ( $es_fa_digits ) { return strtr( (string) $s, $es_fa_digits ); };
$es_price_fa  = static function ( $n ) use ( $es_fa ) { return $es_fa( number_format( (float) $n ) ) . ' تومان'; };
$es_views_fa  = $es_fa( (string) $es_views );
$es_rating_fa = $es_fa( $es_rating );
?>
<div data-product-item="true" data-product-category="<?php echo esc_attr( $es_primary_cat ); ?>" data-product-categories="<?php echo esc_attr( $es_cats_attr ); ?>" data-product-purchasable="<?php echo esc_attr( $es_purchasable ? 'true' : 'false' ); ?>" data-product-price="<?php echo esc_attr( $es_price_attr ); ?>" data-product-ip="<?php echo esc_attr( $es_ip ); ?>" data-product-voltage="<?php echo esc_attr( $es_voltage ); ?>" data-product-views="<?php echo esc_attr( (string) $es_views ); ?>" data-product-rating="<?php echo esc_attr( '' !== $es_rating ? $es_rating : '0' ); ?>" data-product-search-text="<?php echo esc_attr( $es_search_text ); ?>" class="block">
	<a href="<?php echo esc_url( $es_link ); ?>" class="card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]">
		<div class="relative h-52 overflow-hidden">
			<img src="<?php echo esc_url( $es_img ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-[1.2s] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110"/>
			<?php if ( '' !== $es_primary_cat ) : ?>
				<span class="absolute right-3.5 top-3.5 rounded-full bg-emerald-950/80 px-3 py-1 text-[10.5px] font-bold text-emerald-100 backdrop-blur-md"><?php echo esc_html( $es_primary_cat ); ?></span>
			<?php endif; ?>
			<?php if ( $es_purchasable ) : ?>
				<span class="absolute left-3.5 top-3.5 rounded-full bg-emerald-600 px-3 py-1 text-[10.5px] font-bold text-white shadow-lg shadow-emerald-600/25 backdrop-blur-md">🛒 خرید آنلاین</span>
			<?php else : ?>
				<span class="absolute left-3.5 top-3.5 rounded-full bg-slate-900/75 px-3 py-1 text-[10.5px] font-bold text-amber-200 backdrop-blur-md">استعلام پروژه</span>
			<?php endif; ?>
			<?php if ( '' !== $es_ip ) : ?>
				<span dir="ltr" class="absolute bottom-3 left-3.5 rounded-lg bg-white/95 px-2 py-0.5 font-mono text-[10.5px] font-black text-emerald-900 shadow-xs"><?php echo esc_html( $es_ip ); ?></span>
			<?php endif; ?>
		</div>
		<div class="flex flex-1 flex-col gap-2.5 p-5">
			<div class="flex items-center justify-between text-[11px] font-bold text-slate-400">
				<?php if ( '' !== $es_sku ) : ?>
					<span dir="ltr" class="font-mono text-slate-500"><?php echo esc_html( $es_sku ); ?></span>
				<?php else : ?>
					<span></span>
				<?php endif; ?>
				<span class="flex items-center gap-2.5">
					<?php if ( '' !== $es_rating ) : ?>
						<span class="flex items-center gap-1 text-amber-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg><?php echo esc_html( $es_rating_fa ); ?></span>
					<?php endif; ?>
					<span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5 text-slate-400" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path><circle cx="12" cy="12" r="3"></circle></svg><?php echo esc_html( $es_views_fa ); ?></span>
				</span>
			</div>
			<div class="flex items-start justify-between gap-2">
				<h2 class="text-[16px] font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html( $es_title ); ?></h2>
				<span class="grid h-8 w-8 shrink-0 place-items-center rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700 transition-all duration-400 group-hover:rotate-45 group-hover:bg-emerald-600 group-hover:text-white"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span>
			</div>
			<?php if ( '' !== trim( $es_excerpt ) ) : ?>
				<p class="line-clamp-2 text-[12.5px] leading-6 text-slate-600"><?php echo esc_html( $es_excerpt ); ?></p>
			<?php endif; ?>
			<div class="mt-auto border-t border-slate-100 pt-3.5">
				<div class="flex items-center justify-between">
					<?php if ( $es_purchasable && $es_price_num > 0 ) : ?>
						<div>
							<?php if ( $es_has_discount ) : ?>
								<div class="text-[11px] font-semibold text-slate-400 line-through"><?php echo esc_html( $es_price_fa( $es_regular_num ) ); ?></div>
							<?php endif; ?>
							<div class="text-[14px] font-black text-emerald-700"><?php echo esc_html( $es_price_fa( $es_price_num ) ); ?></div>
						</div>
						<?php if ( '' !== $es_min_qty ) : ?>
							<span class="rounded-lg bg-slate-100 px-2 py-1 text-[10.5px] font-bold text-slate-600">حداقل: <?php echo esc_html( $es_fa( $es_min_qty ) ); ?> عدد</span>
						<?php endif; ?>
					<?php else : ?>
						<span class="text-[12.5px] font-extrabold text-emerald-800">تولید سفارشی / استعلام قیمت</span>
						<span class="rounded-lg bg-emerald-50 px-2.5 py-1 text-[11px] font-bold text-emerald-800">مشاوره فنی</span>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</a>
</div>
