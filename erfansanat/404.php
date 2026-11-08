<?php
/**
 * Not found.
 *
 * Offers the three things a lost visitor actually needs: a search box, the shop
 * and the projects archive. The suggested products come from the catalogue, so
 * a site with an empty shop simply drops that strip instead of printing a
 * heading over nothing.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

get_header();

$erfansanat_shop_url     = function_exists( 'wc_get_page_permalink' ) ? (string) wc_get_page_permalink( 'shop' ) : '';
$erfansanat_projects_url = (string) get_post_type_archive_link( 'projects' );
$erfansanat_blog_id      = (int) get_option( 'page_for_posts' );
$erfansanat_blog_url     = $erfansanat_blog_id > 0 ? (string) get_permalink( $erfansanat_blog_id ) : '';

$erfansanat_suggestions = array();
if ( function_exists( 'wc_get_products' ) ) {
	$erfansanat_suggestions = wc_get_products(
		array(
			'limit'    => 3,
			'status'   => 'publish',
			'orderby'  => 'date',
			'order'    => 'DESC',
			'paginate' => false,
		)
	);
	$erfansanat_suggestions = is_array( $erfansanat_suggestions ) ? $erfansanat_suggestions : array();
}
?>
<section data-not-found="true" class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-14 pt-28 lg:pb-20 lg:pt-36">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute inset-0" style="background-image:radial-gradient(900px 460px at 50% 0%, rgba(0,177,64,0.1), transparent 62%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-45"></div>
		<div class="absolute -left-24 top-16 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div>
		<div class="absolute -right-24 bottom-0 h-64 w-64 rounded-full bg-teal-100/60 blur-[100px]"></div>
	</div>
	<div class="relative z-10 mx-auto max-w-3xl px-5 text-center lg:px-8">
		<span class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs">
			<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
			<span><?php esc_html_e( 'خطای ۴۰۴', 'erfansanat' ); ?></span>
		</span>

		<p aria-hidden="true" class="mt-6 bg-gradient-to-b from-emerald-600 to-emerald-800 bg-clip-text text-[5rem] font-black leading-none text-transparent sm:text-[7rem]">۴۰۴</p>

		<h1 class="mt-4 text-[1.8rem] font-black leading-[1.35] text-slate-950 sm:text-[2.3rem]"><?php esc_html_e( 'این صفحه پیدا نشد', 'erfansanat' ); ?></h1>
		<p class="mx-auto mt-4 max-w-[34rem] text-[14.5px] font-medium leading-8 text-slate-600">
			<?php esc_html_e( 'نشانی واردشده وجود ندارد یا صفحه جابه‌جا شده است. می‌توانید از جستجوی زیر استفاده کنید یا از یکی از مسیرهای پیشنهادی ادامه دهید.', 'erfansanat' ); ?>
		</p>

		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="relative mx-auto mt-7 max-w-[30rem]">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute right-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
			<label for="not-found-search" class="sr-only"><?php esc_html_e( 'جستجو در سایت', 'erfansanat' ); ?></label>
			<input id="not-found-search" type="search" name="s" value="" placeholder="<?php esc_attr_e( 'محصول، پروژه یا مقاله…', 'erfansanat' ); ?>" class="w-full rounded-2xl border border-emerald-200 bg-white py-4 pl-5 pr-13 text-[14px] font-medium text-slate-800 shadow-[0_18px_40px_-22px_rgba(0,80,40,0.18)] outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15" />
		</form>

		<div class="mt-6 flex flex-wrap justify-center gap-2.5">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-[12.5px] font-extrabold text-white shadow-sm transition-colors hover:bg-emerald-800">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path></svg>
				<span><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></span>
			</a>
			<?php if ( '' !== $erfansanat_shop_url ) : ?>
				<a href="<?php echo esc_url( $erfansanat_shop_url ); ?>" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[12.5px] font-extrabold text-slate-700 transition-colors hover:border-emerald-300 hover:text-emerald-800"><?php esc_html_e( 'فروشگاه محصولات', 'erfansanat' ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $erfansanat_projects_url ) : ?>
				<a href="<?php echo esc_url( $erfansanat_projects_url ); ?>" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[12.5px] font-extrabold text-slate-700 transition-colors hover:border-emerald-300 hover:text-emerald-800"><?php esc_html_e( 'پروژه‌ها', 'erfansanat' ); ?></a>
			<?php endif; ?>
			<?php if ( '' !== $erfansanat_blog_url ) : ?>
				<a href="<?php echo esc_url( $erfansanat_blog_url ); ?>" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[12.5px] font-extrabold text-slate-700 transition-colors hover:border-emerald-300 hover:text-emerald-800"><?php esc_html_e( 'مقالات', 'erfansanat' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php if ( array() !== $erfansanat_suggestions ) : ?>
	<section class="mx-auto max-w-6xl px-5 py-14 lg:px-8">
		<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
			<h2 class="text-xl font-black text-slate-900 sm:text-2xl"><?php esc_html_e( 'پیشنهاد ما برای شما', 'erfansanat' ); ?></h2>
			<?php if ( '' !== $erfansanat_shop_url ) : ?>
				<a href="<?php echo esc_url( $erfansanat_shop_url ); ?>" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-3.5 py-2 text-[12px] font-extrabold text-emerald-800 transition-colors hover:bg-emerald-50">
					<span><?php esc_html_e( 'همه محصولات', 'erfansanat' ); ?></span>
					<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $erfansanat_suggestions as $erfansanat_suggestion ) : ?>
				<?php
				if ( ! $erfansanat_suggestion instanceof \WC_Product ) {
					continue;
				}
				$erfansanat_product = $erfansanat_suggestion;
				include get_template_directory() . '/template-parts/cards/card-product.php';
				?>
			<?php endforeach; ?>
		</div>
	</section>
<?php endif; ?>

<?php
get_footer();
