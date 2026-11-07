<?php
/** Product breadcrumb, title, subtitle, English title, and status badge. */

defined( 'ABSPATH' ) || exit;

$product = \ErfanSanat\Theme\Woo\ProductTemplate::product( $args['product'] ?? null );
$product_id = \ErfanSanat\Theme\Woo\ProductTemplate::product_id( $product );
if ( ! $product || ! $product_id ) { return; }
$title = method_exists( $product, 'get_name' ) ? (string) $product->get_name() : (string) get_the_title( $product_id );
$subtitle = \ErfanSanat\Theme\Woo\ProductTemplate::text( $product_id, 'zireonvan' );
$english_title = \ErfanSanat\Theme\Woo\ProductTemplate::text( $product_id, 'english_titl' );
$sale_mode = \ErfanSanat\Theme\Woo\ProductTemplate::text( $product_id, 'true_sale', 'false' );
$terms = function_exists( 'get_the_terms' ) ? get_the_terms( $product_id, 'product_cat' ) : array();
$category = is_array( $terms ) && isset( $terms[0] ) && is_object( $terms[0] ) ? $terms[0] : null;
?>
<section class="relative overflow-hidden border-b border-emerald-100 bg-slate-50 pb-10 pt-28 lg:pb-12 lg:pt-32">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div><div class="light-dot-grid absolute inset-0 opacity-45"></div><div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div></div>
	<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
		<?php if ( function_exists( 'rank_math_the_breadcrumbs' ) || function_exists( 'seopress_display_breadcrumbs' ) || function_exists( 'woocommerce_breadcrumb' ) ) : ?>
			<div class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)] backdrop-blur">
				<?php if ( function_exists( 'rank_math_the_breadcrumbs' ) ) : ?><?php rank_math_the_breadcrumbs(); ?>
				<?php elseif ( function_exists( 'seopress_display_breadcrumbs' ) ) : ?><?php seopress_display_breadcrumbs(); ?>
				<?php else : ?><?php woocommerce_breadcrumb( array( 'delimiter' => '<span aria-hidden="true" class="px-1 text-slate-300">‹</span>', 'wrap_before' => '', 'wrap_after' => '' ) ); ?><?php endif; ?>
			</div>
		<?php else : ?>
			<nav aria-label="مسیر صفحه" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)] backdrop-blur">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="transition-colors hover:text-emerald-700">صفحه اصلی</a><span aria-hidden="true" class="px-1 text-slate-300">‹</span>
				<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : get_post_type_archive_link( 'product' ) ); ?>" class="transition-colors hover:text-emerald-700">محصولات</a>
				<?php if ( $category && isset( $category->name ) ) : $category_url = get_term_link( $category ); if ( is_string( $category_url ) ) : ?><span aria-hidden="true" class="px-1 text-slate-300">‹</span><a href="<?php echo esc_url( $category_url ); ?>" class="transition-colors hover:text-emerald-700"><?php echo esc_html( $category->name ); ?></a><?php endif; endif; ?>
				<span aria-hidden="true" class="px-1 text-slate-300">‹</span><span aria-current="page" class="font-bold text-slate-800"><?php echo esc_html( $title ); ?></span>
			</nav>
		<?php endif; ?>
		<div class="mt-7 flex flex-wrap items-start justify-between gap-5">
			<div class="max-w-3xl">
				<?php if ( $category && isset( $category->name ) ) : ?><p class="mb-2 text-xs font-extrabold text-emerald-700"><?php echo esc_html( $category->name ); ?></p><?php endif; ?>
				<h1 class="text-3xl font-black leading-tight text-slate-950 sm:text-4xl"><?php echo esc_html( $title ); ?></h1>
				<?php if ( '' !== $subtitle ) : ?><p class="mt-3 text-base font-bold leading-8 text-slate-700"><?php echo esc_html( $subtitle ); ?></p><?php endif; ?>
				<?php if ( '' !== $english_title ) : ?><p class="mt-2 text-sm font-semibold tracking-wide text-slate-500" dir="ltr" lang="en"><?php echo esc_html( $english_title ); ?></p><?php endif; ?>
			</div>
			<?php if ( method_exists( $product, 'is_in_stock' ) ) : ?><span class="inline-flex items-center gap-2 rounded-full border border-emerald-100 bg-white px-3.5 py-2 text-xs font-extrabold text-emerald-800"><i class="h-2 w-2 rounded-full <?php echo $product->is_in_stock() ? 'bg-emerald-500' : 'bg-amber-500'; ?>" aria-hidden="true"></i><?php if ( in_array( strtolower( $sale_mode ), array( 'true', '1', 'yes' ), true ) ) { echo $product->is_in_stock() ? esc_html__( 'موجود در انبار', 'erfansanat-theme' ) : esc_html__( 'تماس برای موجودی', 'erfansanat-theme' ); } else { echo esc_html__( 'تولید سفارشی', 'erfansanat-theme' ); } ?></span><?php endif; ?>
		</div>
	</div>
</section>
