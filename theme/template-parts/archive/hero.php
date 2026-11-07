<?php
/** Dynamic shop/product-taxonomy heading, breadcrumb, and category navigation. */

defined( 'ABSPATH' ) || exit;
$term = is_object( $args['term'] ?? null ) ? $args['term'] : ( function_exists( 'get_queried_object' ) ? get_queried_object() : null );
$is_product_term = is_object( $term ) && isset( $term->taxonomy ) && in_array( $term->taxonomy, array( 'product_cat', 'product_tag', 'product_brand' ), true );
$title = $is_product_term && isset( $term->name ) ? (string) $term->name : 'فروشگاه محصولات';
$description = $is_product_term && isset( $term->description ) ? (string) $term->description : 'تولیدات تخصصی نورپردازی شهری و صنعتی، مستقیم از کارخانه عرفان صنعت اصفهان.';
$categories = function_exists( 'get_terms' ) ? get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0 ) ) : array();
if ( ! is_array( $categories ) || ( function_exists( 'is_wp_error' ) && is_wp_error( $categories ) ) ) { $categories = array(); }
?>
<section class="relative overflow-hidden border-b border-emerald-100 bg-white pb-10 pt-28 lg:pb-12">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div><div class="light-dot-grid absolute inset-0 opacity-45"></div></div>
	<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
		<nav aria-label="مسیر صفحه" class="mb-6 inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)] backdrop-blur"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="transition-colors hover:text-emerald-700">صفحه اصلی</a><span aria-hidden="true" class="px-1 text-slate-300">‹</span><a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : get_post_type_archive_link( 'product' ) ); ?>" class="transition-colors hover:text-emerald-700">محصولات</a><?php if ( $is_product_term ) : ?><span aria-hidden="true" class="px-1 text-slate-300">‹</span><span aria-current="page" class="font-bold text-slate-800"><?php echo esc_html( $title ); ?></span><?php endif; ?></nav>
		<div class="max-w-3xl"><p class="mb-2 text-xs font-extrabold text-emerald-700"><?php echo $is_product_term ? esc_html__( 'دسته‌بندی محصولات', 'erfansanat-theme' ) : esc_html__( 'تولید مستقیم از کارخانه', 'erfansanat-theme' ); ?></p><h1 class="text-3xl font-black leading-tight text-slate-950 sm:text-4xl"><?php echo esc_html( $title ); ?></h1><?php if ( '' !== trim( $description ) ) : ?><div class="mt-3 text-sm leading-7 text-slate-600"><?php echo wp_kses_post( $description ); ?></div><?php endif; ?></div>
		<?php if ( $categories ) : ?><nav aria-label="دسته‌بندی محصولات" class="mt-6 flex flex-wrap gap-2"><?php foreach ( $categories as $category ) : $url = get_term_link( $category ); if ( is_wp_error( $url ) ) { continue; } ?><a href="<?php echo esc_url( $url ); ?>" class="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-bold text-slate-700 transition-colors hover:border-emerald-300 hover:text-emerald-800"><?php echo esc_html( $category->name ); ?></a><?php endforeach; ?></nav><?php endif; ?>
	</div>
</section>
