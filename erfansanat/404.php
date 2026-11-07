<?php
/**
 * 404 template (P07-T019). Keeps the real 404 status (no redirect/200 hack),
 * offers a working search form and useful links in the site design system.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;

get_header();

$es_shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : Menus::page_url( 'shop' );
$es_projects_url = get_post_type_archive_link( 'projects' ) ?: home_url( '/projects/' );
$es_links        = array(
	array( 'label' => __( 'صفحه اصلی', 'erfansanat' ), 'url' => home_url( '/' ), 'primary' => true ),
	array( 'label' => __( 'فروشگاه محصولات', 'erfansanat' ), 'url' => $es_shop_url, 'primary' => false ),
	array( 'label' => __( 'پروژه‌های اجراشده', 'erfansanat' ), 'url' => $es_projects_url, 'primary' => false ),
	array( 'label' => __( 'اخبار و مقالات', 'erfansanat' ), 'url' => home_url( '/blog/' ), 'primary' => false ),
	array( 'label' => __( 'تماس با ما', 'erfansanat' ), 'url' => Menus::page_url( 'contact' ), 'primary' => false ),
);
?>

<main class="min-h-screen bg-[#f8faf9]">
	<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-50"></div>
			<div class="absolute -left-40 top-1/4 h-96 w-96 rounded-full bg-emerald-200/45 blur-[110px]"></div>
			<div class="absolute -right-32 bottom-0 h-80 w-80 rounded-full bg-teal-100/65 blur-[110px]"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 pb-20 lg:px-8">
			<div class="mx-auto max-w-2xl text-center">
				<div class="mx-auto inline-flex items-center gap-3">
					<span class="h-px w-10 bg-emerald-400/60"></span>
					<span class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[12.5px] font-black text-emerald-800 shadow-xs"><?php esc_html_e( 'خطای ۴۰۴', 'erfansanat' ); ?></span>
					<span class="h-px w-10 bg-emerald-400/60"></span>
				</div>
				<div class="mt-6 bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-[5rem] font-black leading-none tracking-tight text-transparent sm:text-[7rem]">۴۰۴</div>
				<h1 class="mt-4 text-[1.6rem] font-black leading-[1.5] text-slate-950 sm:text-[2rem]"><?php esc_html_e( 'صفحه مورد نظر یافت نشد.', 'erfansanat' ); ?></h1>
				<p class="mx-auto mt-4 max-w-xl text-[14px] font-medium leading-8 text-slate-600"><?php esc_html_e( 'ممکن است نشانی تغییر کرده یا صفحه حذف شده باشد. از جستجو یا لینک‌های زیر استفاده کنید.', 'erfansanat' ); ?></p>

				<form role="search" method="get" class="relative mx-auto mt-8 max-w-xl" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<svg aria-hidden="true" class="lucide lucide-search absolute right-5 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
					<input type="search" name="s" value="" placeholder="<?php esc_attr_e( 'جستجو در مقالات، محصولات و پروژه‌ها…', 'erfansanat' ); ?>" aria-label="<?php esc_attr_e( 'عبارت جستجو', 'erfansanat' ); ?>" class="w-full rounded-2xl border border-emerald-200 bg-white py-4 pl-28 pr-13 text-[14px] font-medium text-slate-800 shadow-[0_18px_40px_-22px_rgba(0,80,40,0.18)] outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15"/>
					<button type="submit" class="absolute left-2.5 top-1/2 -translate-y-1/2 cursor-pointer rounded-xl bg-emerald-700 px-5 py-2.5 text-[12.5px] font-extrabold text-white transition-colors hover:bg-emerald-800"><?php esc_html_e( 'جستجو', 'erfansanat' ); ?></button>
				</form>

				<div class="mt-8 flex flex-wrap items-center justify-center gap-3">
					<?php foreach ( $es_links as $es_link ) : ?>
						<a href="<?php echo esc_url( $es_link['url'] ); ?>" class="<?php echo $es_link['primary'] ? 'rounded-xl bg-emerald-700 px-6 py-3 text-[13px] font-extrabold text-white shadow-sm transition-colors hover:bg-emerald-800' : 'rounded-xl border border-emerald-200 bg-white px-5 py-3 text-[12.5px] font-extrabold text-emerald-800 transition-colors hover:border-emerald-400 hover:bg-emerald-50'; ?>"><?php echo esc_html( $es_link['label'] ); ?></a>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<div aria-hidden="true" class="relative z-10 h-10 rounded-t-[2.5rem] bg-[#f8faf9]"></div>
	</section>
</main>

<?php
get_footer();
