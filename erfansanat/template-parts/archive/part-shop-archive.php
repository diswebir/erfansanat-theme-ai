<?php
/**
 * Shared body of the shop archive and the product category archive.
 *
 * `archive-product.php` and `taxonomy-product_cat.php` both call this, because
 * the two references differ only in the hero wording and the breadcrumb tail.
 * Products, terms, counts and the FAQ are all read from the database; nothing
 * here repeats one of the mirror's sample values.
 *
 * @package ErfanSanat
 *
 * @var \WP_Term|null $erfansanat_term Current category term, null on the shop.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\ProductData;
use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_term     = isset( $erfansanat_term ) && $erfansanat_term instanceof \WP_Term ? $erfansanat_term : null;
$erfansanat_shop_url = function_exists( 'wc_get_page_permalink' ) ? (string) wc_get_page_permalink( 'shop' ) : home_url( '/' );
$erfansanat_per_page = (int) apply_filters( 'erfansanat_archive_per_page', 9 );
$erfansanat_per_page = max( 1, $erfansanat_per_page );

// Paging is real: `?page=N` rewrites the main query before it runs.
$erfansanat_page = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );

$erfansanat_query = $GLOBALS['wp_query'] ?? null;
$erfansanat_total = $erfansanat_query instanceof \WP_Query ? (int) $erfansanat_query->found_posts : 0;

// The main query yields posts; the grid and the filter payload need products.
$erfansanat_products = array();
if ( $erfansanat_query instanceof \WP_Query ) {
	foreach ( (array) $erfansanat_query->posts as $erfansanat_post ) {
		$erfansanat_product = $erfansanat_post instanceof \WP_Post ? wc_get_product( $erfansanat_post->ID ) : null;
		if ( $erfansanat_product instanceof \WC_Product ) {
			$erfansanat_products[] = $erfansanat_product;
		}
	}
	unset( $erfansanat_product );
}

$erfansanat_total_products = (int) wp_count_posts( 'product' )->publish;

$erfansanat_terms = get_terms(
	array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'orderby'    => 'name',
	)
);
$erfansanat_terms = is_array( $erfansanat_terms ) ? $erfansanat_terms : array();

// Flat list ordered parent-first so a child always follows its parent.
$erfansanat_tree      = array();
$erfansanat_by_parent = array();
foreach ( $erfansanat_terms as $erfansanat_row ) {
	$erfansanat_by_parent[ (int) $erfansanat_row->parent ][] = $erfansanat_row;
}
$erfansanat_walk = static function ( int $parent_id ) use ( &$erfansanat_walk, &$erfansanat_tree, $erfansanat_by_parent ): void {
	foreach ( $erfansanat_by_parent[ $parent_id ] ?? array() as $erfansanat_child ) {
		$erfansanat_tree[] = $erfansanat_child;
		$erfansanat_walk( (int) $erfansanat_child->term_id );
	}
};
$erfansanat_walk( 0 );

// The filter options are the values the catalogue actually uses.
$erfansanat_ips      = array();
$erfansanat_voltages = array();
foreach ( ProductData::archive_products() as $erfansanat_product ) {
	$erfansanat_ip = ProductData::attribute_text( $erfansanat_product, 'ip-rating' );
	if ( '' !== $erfansanat_ip && ! in_array( $erfansanat_ip, $erfansanat_ips, true ) ) {
		$erfansanat_ips[] = $erfansanat_ip;
	}
	$erfansanat_voltage = ProductData::attribute_text( $erfansanat_product, 'input-voltage' );
	if ( '' !== $erfansanat_voltage && ! in_array( $erfansanat_voltage, $erfansanat_voltages, true ) ) {
		$erfansanat_voltages[] = $erfansanat_voltage;
	}
}
sort( $erfansanat_ips );
sort( $erfansanat_voltages );

// Breadcrumb: home -> shop -> ancestors -> current.
$erfansanat_crumb_trail = array(
	array(
		'label' => __( 'صفحه اصلی', 'erfansanat' ),
		'url'   => home_url( '/' ),
	),
);
if ( null === $erfansanat_term ) {
	$erfansanat_crumb_trail[] = array(
		'label' => __( 'فروشگاه محصولات', 'erfansanat' ),
		'url'   => '',
	);
} else {
	$erfansanat_crumb_trail[] = array(
		'label' => __( 'فروشگاه محصولات', 'erfansanat' ),
		'url'   => $erfansanat_shop_url,
	);
	$erfansanat_ancestors     = array_reverse( get_ancestors( $erfansanat_term->term_id, 'product_cat', 'taxonomy' ) );
	foreach ( $erfansanat_ancestors as $erfansanat_ancestor_id ) {
		$erfansanat_ancestor = get_term( (int) $erfansanat_ancestor_id, 'product_cat' );
		if ( $erfansanat_ancestor instanceof \WP_Term ) {
			$erfansanat_crumb_trail[] = array(
				'label' => $erfansanat_ancestor->name,
				'url'   => (string) get_term_link( $erfansanat_ancestor ),
			);
		}
	}
	$erfansanat_crumb_trail[] = array(
		'label' => $erfansanat_term->name,
		'url'   => '',
	);
}

$erfansanat_badges = array(
	array(
		'icon'  => 'shield',
		'label' => __( 'استاندارد IP67 / IP68', 'erfansanat' ),
	),
	array(
		'icon'  => 'badge-check',
		'label' => __( 'دارای کد GS1 و ستاد', 'erfansanat' ),
	),
	array(
		'icon'  => 'wrench',
		'label' => __( '۲۴ ماه گارانتی تعویض', 'erfansanat' ),
	),
	array(
		'icon'  => 'headset',
		'label' => __( '۵ سال خدمات پس از فروش', 'erfansanat' ),
	),
);

$erfansanat_hero = null === $erfansanat_term
	? array(
		'eyebrow'   => __( 'فروشگاه رسمی تولیدات کارخانه عرفان صنعت', 'erfansanat' ),
		'title'     => __( 'فروشگاه محصولات', 'erfansanat' ),
		'highlight' => __( 'نورپردازی شهری و صنعتی', 'erfansanat' ),
		'lead'      => __( 'مجموعه‌ای کامل از تجهیزات نورپردازی پیشرفته؛ از پوینت لایت پیکسل و ریسه‌های بلوطی، فندقی و سوزنی IP68 تا درختان نوری هوشمند، خورشیدنماها و باکس کنترل مرکزی.', 'erfansanat' ),
		'image'     => SectionParts::asset( 'images/light-tree.jpg' ),
	)
	: array(
		'eyebrow'   => __( 'دسته‌بندی تخصصی محصولات · تولید کارخانه عرفان صنعت', 'erfansanat' ),
		'title'     => __( 'تولید و قیمت کارخانه', 'erfansanat' ),
		'highlight' => $erfansanat_term->name,
		'lead'      => trim( wp_strip_all_tags( (string) $erfansanat_term->description ) ),
		'image'     => ProductData::term_image( $erfansanat_term ),
	);

$erfansanat_faq = null === $erfansanat_term ? array() : ProductData::term_faq( $erfansanat_term );

$erfansanat_heading = null === $erfansanat_term
	? __( 'همه محصولات تولیدی عرفان صنعت', 'erfansanat' )
	: $erfansanat_term->name;

do_action( 'woocommerce_before_main_content' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce's own hook.
?>

<?php
$erfansanat_crumbs    = $erfansanat_crumb_trail;
$erfansanat_eyebrow   = (string) $erfansanat_hero['eyebrow'];
$erfansanat_title     = (string) $erfansanat_hero['title'];
$erfansanat_highlight = (string) $erfansanat_hero['highlight'];
$erfansanat_lead      = (string) $erfansanat_hero['lead'];
$erfansanat_image     = (string) $erfansanat_hero['image'];
$erfansanat_image_alt = null === $erfansanat_term ? __( 'نمای درخت نوری هوشمند تولید عرفان صنعت اصفهان', 'erfansanat' ) : $erfansanat_term->name;
$erfansanat_count     = $erfansanat_total_products;
require get_template_directory() . '/template-parts/archive/part-shop-hero.php';
?>

<section
	id="archive"
	data-products-archive="true"
	data-initial-category="<?php echo esc_attr( null === $erfansanat_term ? __( 'همه', 'erfansanat' ) : $erfansanat_term->name ); ?>"
	data-items-per-page="<?php echo esc_attr( (string) $erfansanat_per_page ); ?>"
	data-archive-page="<?php echo esc_attr( (string) $erfansanat_page ); ?>"
	class="mx-auto max-w-7xl scroll-mt-24 px-5 py-12 lg:px-8 lg:py-16"
>
	<div class="grid items-start gap-8 lg:grid-cols-12">
		<aside aria-label="<?php esc_attr_e( 'فیلتر و دسته‌بندی محصولات', 'erfansanat' ); ?>" class="hidden lg:col-span-4 lg:block xl:col-span-3">
			<?php
			$erfansanat_drawer = false;
			require get_template_directory() . '/template-parts/archive/part-shop-filters.php';
			?>
		</aside>

		<div class="lg:col-span-8 xl:col-span-9">
			<?php
			$erfansanat_heading  = $erfansanat_heading;
			$erfansanat_total    = $erfansanat_total;
			$erfansanat_page     = $erfansanat_page;
			$erfansanat_per_page = $erfansanat_per_page;
			$erfansanat_shop_url = $erfansanat_shop_url;
			$erfansanat_empty    = 0 === (int) $erfansanat_total;
			// Pagination URLs are built from the archive's own base, so a
			// category page pages inside the category, not the whole shop.
			$erfansanat_base_url = null === $erfansanat_term
				? trailingslashit( $erfansanat_shop_url )
				: trailingslashit( (string) get_term_link( $erfansanat_term ) );
			require get_template_directory() . '/template-parts/archive/part-shop-grid.php';
			?>
		</div>
	</div>

	<?php
	require get_template_directory() . '/template-parts/archive/part-shop-drawer.php';
	?>

	<?php if ( array() !== $erfansanat_faq ) : ?>
		<article class="mt-10 border-t border-slate-100 pt-8">
			<?php
			$erfansanat_faq_rows  = $erfansanat_faq;
			$erfansanat_faq_title = sprintf(
				/* translators: %s: category name. */
				__( 'سوالات متداول درباره %s', 'erfansanat' ),
				$erfansanat_term instanceof \WP_Term ? $erfansanat_term->name : ''
			);
			include get_template_directory() . '/template-parts/archive/part-term-faq.php';
			?>
		</article>
	<?php endif; ?>
</section>

<?php
require get_template_directory() . '/template-parts/archive/part-shop-closing.php';

do_action( 'woocommerce_after_main_content' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WooCommerce's own hook.
