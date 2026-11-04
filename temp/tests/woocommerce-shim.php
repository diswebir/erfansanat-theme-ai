<?php
/**
 * P06-T001 — WooCommerce compatibility shim for the QA harness.
 *
 * The sandbox cannot download/run real WooCommerce (release assets redirect
 * to a blocked host). This shim simulates ONLY the surface the theme uses:
 * the `product` CPT, `product_cat`/`product_tag`/`product_brand` taxonomies,
 * a basic product object (price/stock/SKU/gallery/rating meta), `wc_price()`,
 * `wc_get_product()`, `wc_get_page_permalink()` and a basic `WC()->cart`.
 *
 * Guards (AC-01 / AC-03):
 *  - Loads ONLY inside the QA bootstrap (`ES_ROOT` defined) — the file lives
 *    in temp/tests/ and is never part of the theme zip, so it cannot run live.
 *  - Stays completely inactive when real WooCommerce is present
 *    (`WC_VERSION` / `WooCommerce` class / `WC()` function) — SC-02.
 *
 * @package ErfanSanatQA
 */

// AC-01: never load outside the QA bootstrap.
if ( ! defined( 'ES_ROOT' ) ) {
	return;
}

/**
 * Whether real WooCommerce is already present (AC-03 / SC-02).
 *
 * Detection uses WC_VERSION / the WooCommerce class — markers the shim itself
 * never defines. (function_exists('WC') is NOT used here because the shim
 * provides its own WC(); that would self-trigger after loading.)
 */
function es_wc_shim_real_woo_present(): bool {
	return defined( 'WC_VERSION' ) || class_exists( 'WooCommerce' );
}

/**
 * Gate for the whole shim: QA environment AND no real WooCommerce.
 */
function es_wc_shim_should_load(): bool {
	return defined( 'ES_ROOT' ) && ! es_wc_shim_real_woo_present();
}

if ( ! es_wc_shim_should_load() ) {
	return; // SC-02: real Woo present -> shim stays inactive.
}

define( 'ES_WC_SHIM_LOADED', true );

/* ─────────────────────────────────────────────────────────────────────────
 * 1. Product CPT + taxonomies (AC-02). Never duplicate existing registrations
 *    (so this also coexists with the P03 test shims / real Woo).
 * ──────────────────────────────────────────────────────────────────────── */

if ( ! post_type_exists( 'product' ) ) {
	register_post_type( 'product', array(
		'labels'       => array( 'name' => 'محصولات', 'singular_name' => 'محصول' ),
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => array( 'slug' => 'product', 'with_front' => false ),
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'comments' ),
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-products',
	) );
}

if ( ! taxonomy_exists( 'product_cat' ) ) {
	register_taxonomy( 'product_cat', 'product', array(
		'labels'       => array( 'name' => 'دسته‌های محصول' ),
		'public'       => true,
		'hierarchical' => true,
		'rewrite'      => array( 'slug' => 'product_cat', 'with_front' => true, 'hierarchical' => true ),
		'show_in_rest' => true,
	) );
}

if ( ! taxonomy_exists( 'product_tag' ) ) {
	register_taxonomy( 'product_tag', 'product', array(
		'labels'       => array( 'name' => 'برچسب‌های محصول' ),
		'public'       => true,
		'hierarchical' => false,
		'rewrite'      => array( 'slug' => 'product-tag', 'with_front' => false ),
		'show_in_rest' => true,
	) );
}

if ( ! taxonomy_exists( 'product_brand' ) ) {
	register_taxonomy( 'product_brand', 'product', array(
		'labels'       => array( 'name' => 'برندهای محصول' ),
		'public'       => true,
		'hierarchical' => false,
		'rewrite'      => array( 'slug' => 'product_brand', 'with_front' => false ),
		'show_in_rest' => true,
	) );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2. Basic product object backed by standard WooCommerce meta.
 * ──────────────────────────────────────────────────────────────────────── */

class ES_Shim_Product {
	protected int $id;
	protected WP_Post $post;

	public function __construct( WP_Post $post ) {
		$this->post = $post;
		$this->id   = (int) $post->ID;
	}

	public function get_id(): int { return $this->id; }
	public function get_name(): string { return get_the_title( $this->id ); }
	public function get_title(): string { return $this->get_name(); }
	public function get_slug(): string { return (string) $this->post->post_name; }
	public function get_status(): string { return (string) $this->post->post_status; }
	public function get_type(): string { return (string) ( get_post_meta( $this->id, '_product_type', true ) ?: 'simple' ); }
	public function get_permalink(): string { return (string) get_permalink( $this->id ); }
	public function get_description(): string { return (string) $this->post->post_content; }
	public function get_short_description(): string { return (string) $this->post->post_excerpt; }

	protected function meta( string $key, $default = '' ) {
		$v = get_post_meta( $this->id, $key, true );
		return ( '' === $v || null === $v ) ? $default : $v;
	}

	public function get_price(): string { return (string) $this->meta( '_price', $this->meta( '_regular_price', '0' ) ); }
	public function get_regular_price(): string { return (string) $this->meta( '_regular_price', $this->meta( '_price', '0' ) ); }
	public function get_sale_price(): string { return (string) $this->meta( '_sale_price', '' ); }
	public function is_on_sale(): bool { return '' !== $this->get_sale_price() && $this->get_sale_price() !== $this->get_regular_price(); }
	public function get_price_html(): string {
		if ( $this->is_on_sale() ) {
			return '<del>' . wc_price( $this->get_regular_price() ) . '</del> <ins>' . wc_price( $this->get_sale_price() ) . '</ins>';
		}
		return wc_price( $this->get_price() );
	}
	public function get_stock_status(): string { return (string) $this->meta( '_stock_status', 'instock' ); }
	public function is_in_stock(): bool { return 'outofstock' !== $this->get_stock_status(); }
	public function is_purchasable(): bool { return 'publish' === $this->get_status() && $this->is_in_stock() && '' !== $this->get_price(); }
	public function is_visible(): bool { return 'publish' === $this->get_status(); }
	public function get_sku(): string { return (string) $this->meta( '_sku', '' ); }
	public function get_stock_quantity() { $v = $this->meta( '_stock', null ); return null === $v ? null : (int) $v; }
	public function get_image_id(): int { return (int) get_post_thumbnail_id( $this->id ); }
	public function get_gallery_image_ids(): array {
		$raw = (string) $this->meta( '_product_image_gallery', '' );
		return array_values( array_filter( array_map( 'intval', explode( ',', $raw ) ) ) );
	}
	public function get_average_rating(): float { return (float) $this->meta( '_wc_average_rating', 0 ); }
	public function get_rating_count(): int { return (int) $this->meta( '_wc_rating_count_total', count( (array) $this->meta( '_wc_rating_count', array() ) ) ); }
	public function get_review_count(): int { return (int) $this->meta( '_wc_review_count', 0 ); }
	public function get_category_ids(): array { return $this->term_ids( 'product_cat' ); }
	public function get_tag_ids(): array { return $this->term_ids( 'product_tag' ); }
	public function get_upsell_ids(): array { return array_map( 'intval', (array) $this->meta( '_upsell_ids', array() ) ); }
	public function get_cross_sell_ids(): array { return array_map( 'intval', (array) $this->meta( '_crosssell_ids', array() ) ); }
	public function get_meta( string $key, bool $single = true ) { return get_post_meta( $this->id, $key, $single ); }

	// ── P06-T007: minimal variable-product surface for QA only ─────────────.
	public function is_type( $type ): bool {
		$types = (array) $type;
		return in_array( $this->get_type(), $types, true );
	}
	public function get_availability(): array {
		return $this->is_in_stock()
			? array( 'availability' => 'موجود', 'class' => 'in-stock' )
			: array( 'availability' => 'ناموجود', 'class' => 'out-of-stock' );
	}
	public function get_variation_attributes(): array {
		$raw = $this->meta( '_es_variation_attributes', array() );
		if ( is_string( $raw ) ) {
			$raw = json_decode( $raw, true );
		}
		return is_array( $raw ) ? $raw : array();
	}
	public function get_available_variations(): array {
		$raw = $this->meta( '_product_variations', array() );
		if ( is_string( $raw ) ) {
			$raw = json_decode( $raw, true );
		}
		return is_array( $raw ) ? $raw : array();
	}
	public function get_children(): array {
		$ids = array();
		foreach ( $this->get_available_variations() as $variation ) {
			if ( isset( $variation['variation_id'] ) ) {
				$ids[] = (int) $variation['variation_id'];
			}
		}
		return $ids;
	}

	protected function term_ids( string $tax ): array {
		$terms = get_the_terms( $this->id, $tax );
		return ( $terms && ! is_wp_error( $terms ) ) ? wp_list_pluck( $terms, 'term_id' ) : array();
	}
}

/* ─────────────────────────────────────────────────────────────────────────
 * 3. Core WC functions used by the theme.
 * ──────────────────────────────────────────────────────────────────────── */

function wc_get_product( $the_product = null ) {
	if ( $the_product instanceof ES_Shim_Product ) {
		return $the_product;
	}
	$post = get_post( $the_product );
	if ( ! $post || 'product' !== $post->post_type ) {
		return false;
	}
	return new ES_Shim_Product( $post );
}

/** Format a price in Iranian Toman (filterable for tests/integrations). */
function wc_price( $amount, array $args = array() ): string {
	$amount   = (float) $amount;
	$decimals = isset( $args['decimals'] ) ? (int) $args['decimals'] : 0;
	$formatted = number_format( $amount, $decimals, '.', ',' );
	$html      = '<span class="woocommerce-Price-amount amount">' . $formatted . '&nbsp;<span class="woocommerce-Price-currencySymbol">تومان</span></span>';
	return (string) apply_filters( 'es_shim_wc_price', $html, $amount, $args );
}

function wc_get_page_permalink( string $page ): string {
	$slugs = array( 'shop' => 'shop', 'cart' => 'cart', 'checkout' => 'checkout', 'myaccount' => 'my-account' );
	$slug  = $slugs[ $page ] ?? $page;
	$p     = get_page_by_path( $slug );
	$url   = ( $p instanceof WP_Post ) ? get_permalink( $p ) : home_url( '/' . $slug . '/' );
	return (string) apply_filters( 'es_shim_wc_page_permalink', $url, $page );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 4. Basic cart + WC() singleton.
 * ──────────────────────────────────────────────────────────────────────── */

class ES_Shim_Cart {
	protected array $items = array();

	public function add_to_cart( int $product_id, int $quantity = 1, int $variation_id = 0 ): string {
		$product = wc_get_product( $product_id );
		if ( ! $product ) {
			return '';
		}
		$key = md5( $product_id . '|' . $variation_id );
		if ( isset( $this->items[ $key ] ) ) {
			$this->items[ $key ]['quantity'] += $quantity;
		} else {
			$this->items[ $key ] = array(
				'key'          => $key,
				'product_id'   => $product_id,
				'variation_id' => $variation_id,
				'quantity'     => $quantity,
				'data'         => $product,
			);
		}
		return $key;
	}

	public function remove_cart_item( string $key ): void { unset( $this->items[ $key ] ); }
	public function set_quantity( string $key, int $quantity ): void {
		if ( isset( $this->items[ $key ] ) ) {
			if ( $quantity <= 0 ) { unset( $this->items[ $key ] ); }
			else { $this->items[ $key ]['quantity'] = $quantity; }
		}
	}
	public function empty_cart(): void { $this->items = array(); }
	public function get_cart(): array { return $this->items; }
	public function get_cart_item( string $key ): array { return $this->items[ $key ] ?? array(); }
	public function is_empty(): bool { return array() === $this->items; }
	public function get_cart_contents_count(): int {
		$n = 0;
		foreach ( $this->items as $item ) { $n += (int) $item['quantity']; }
		return $n;
	}
	public function get_cart_contents_total(): float {
		$t = 0.0;
		foreach ( $this->items as $item ) { $t += (float) $item['data']->get_price() * (int) $item['quantity']; }
		return $t;
	}
	public function get_cart_total(): string { return wc_price( $this->get_cart_contents_total() ); }
	public function get_product_price( $product ): string { return $product->get_price_html(); }
	public function get_product_subtotal( $product, int $quantity ): string { return wc_price( (float) $product->get_price() * $quantity ); }

	// ── Coupons / tax / totals (mirrors the real WC_Cart API used by cart.php) ──.
	protected array $coupons = array();

	public function apply_coupon( string $code ): bool {
		$code   = strtolower( trim( $code ) );
		$valid  = apply_filters( 'es_shim_coupons', array( 'welcome10' => 0.10, 'off5' => 0.05 ) );
		if ( '' === $code || ! isset( $valid[ $code ] ) ) {
			return false;
		}
		$this->coupons[ $code ] = (float) $valid[ $code ];
		return true;
	}
	public function remove_coupon( string $code ): void { unset( $this->coupons[ strtolower( $code ) ] ); }
	public function get_applied_coupons(): array { return array_keys( $this->coupons ); }
	public function get_discount_total(): float {
		$rate = array_sum( $this->coupons );
		return $this->get_cart_contents_total() * $rate;
	}
	public function get_cart_contents_taxes(): float { return $this->get_cart_contents_total() * 0.10; }
	public function get_total(): float {
		return $this->get_cart_contents_total() - $this->get_discount_total() + $this->get_cart_contents_taxes();
	}
}

class ES_Shim_WC {
	public ES_Shim_Cart $cart;
	public function __construct() { $this->cart = new ES_Shim_Cart(); }
	public function cart(): ES_Shim_Cart { return $this->cart; }
}

function WC(): ES_Shim_WC {
	static $instance = null;
	if ( null === $instance ) {
		$instance = new ES_Shim_WC();
	}
	return $instance;
}
