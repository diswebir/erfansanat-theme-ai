<?php
/**
 * Related products: the reference's "similar and complementary" rail.
 *
 * Uses WooCommerce's own related-product selection (shared categories/tags) so
 * the shop owner controls it through the product editor, rather than inventing
 * a second recommendation rule. Renders nothing when there are no related
 * products.
 *
 * @package ErfanSanat
 *
 * @var \WC_Product $erfansanat_product Product being viewed.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

if ( ! isset( $erfansanat_product ) || ! $erfansanat_product instanceof \WC_Product ) {
	return;
}

$erfansanat_related_ids = wc_get_related_products( $erfansanat_product->get_id(), 3 );
if ( array() === $erfansanat_related_ids ) {
	return;
}

$erfansanat_related = wc_get_products(
	array(
		'include' => array_map( 'absint', $erfansanat_related_ids ),
		'limit'   => 3,
		'status'  => 'publish',
		'return'  => 'objects',
	)
);

if ( ! is_array( $erfansanat_related ) || array() === $erfansanat_related ) {
	return;
}

// One thumbnail + term priming for the whole rail keeps the cards query-free.
$erfansanat_related_posts = array();
foreach ( $erfansanat_related as $erfansanat_related_product ) {
	$erfansanat_related_post = get_post( $erfansanat_related_product->get_id() );
	if ( $erfansanat_related_post instanceof \WP_Post ) {
		$erfansanat_related_posts[] = $erfansanat_related_post;
	}
}
if ( array() !== $erfansanat_related_posts ) {
	update_post_thumbnail_cache(
		new \WP_Query(
			array(
				'post__in'       => wp_list_pluck( $erfansanat_related_posts, 'ID' ),
				'post_type'      => 'any',
				'posts_per_page' => count( $erfansanat_related_posts ),
				'no_found_rows'  => true,
			)
		)
	);
	update_post_caches( $erfansanat_related_posts, 'product', true, true );
}

$erfansanat_shop_url = function_exists( 'wc_get_page_permalink' ) ? (string) wc_get_page_permalink( 'shop' ) : '';
?>
<section class="border-t border-slate-200/80 bg-white py-14">
	<div class="mx-auto max-w-6xl px-5 lg:px-8">
		<div class="mb-8 flex flex-wrap items-end justify-between gap-4">
			<div>
				<div class="mb-1.5 flex items-center gap-2 text-xs font-bold text-emerald-700">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true"><path d="M3 21V10l6 4V10l6 4V7l6 3v11z"></path></svg>
					<span><?php esc_html_e( 'سایر تولیدات عرفان صنعت', 'erfansanat' ); ?></span>
				</div>
				<h2 class="text-2xl font-extrabold text-slate-900"><?php esc_html_e( 'محصولات مشابه و مکمل', 'erfansanat' ); ?></h2>
			</div>
			<?php if ( '' !== $erfansanat_shop_url ) : ?>
				<a href="<?php echo esc_url( $erfansanat_shop_url ); ?>" class="flex items-center gap-1.5 text-xs font-extrabold text-emerald-700 hover:text-emerald-900">
					<span><?php esc_html_e( 'مشاهده همه محصولات', 'erfansanat' ); ?></span>
					<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
				</a>
			<?php endif; ?>
		</div>

		<div class="grid gap-6 md:grid-cols-3">
			<?php foreach ( $erfansanat_related as $erfansanat_related_product ) : ?>
				<?php
				$erfansanat_product = $erfansanat_related_product;
				include get_template_directory() . '/template-parts/cards/card-product-tile.php';
				?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
