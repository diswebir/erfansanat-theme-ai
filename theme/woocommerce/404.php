<?php
/** Branded not-found response for WooCommerce product routes. */
defined( 'ABSPATH' ) || exit;
if ( function_exists( 'status_header' ) ) { status_header( 404 ); }
if ( function_exists( 'nocache_headers' ) ) { nocache_headers(); }
get_header( 'shop' );
?>
<main class="mx-auto max-w-7xl px-5 py-16 text-center lg:px-8"><div class="mx-auto max-w-2xl rounded-3xl border border-slate-200 bg-white p-8 shadow-sm sm:p-12"><p class="text-sm font-black text-emerald-700">خطای ۴۰۴</p><h1 class="mt-2 text-3xl font-black text-slate-950">این صفحه پیدا نشد</h1><p class="mt-3 text-sm leading-7 text-slate-600">نشانی محصول یا دسته‌بندی تغییر کرده است. نام محصول را جستجو کنید یا به فروشگاه برگردید.</p><form role="search" method="get" class="mt-6 flex gap-2" action="<?php echo esc_url( home_url( '/' ) ); ?>"><label class="screen-reader-text" for="es-404-product-search">جستجوی محصول</label><input id="es-404-product-search" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="جستجوی محصولات" class="min-w-0 flex-1 rounded-xl border border-slate-200 px-4 py-3 text-sm"><input type="hidden" name="post_type" value="product"><button type="submit" class="rounded-xl bg-emerald-700 px-4 py-3 text-sm font-bold text-white">جستجو</button></form><a class="mt-6 inline-flex rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-bold text-emerald-800" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">بازگشت به فروشگاه</a></div></main>
<?php get_footer( 'shop' ); ?>
