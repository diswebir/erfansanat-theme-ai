<?php
/**
 * Brand row: logo, desktop navigation and header actions.
 *
 * Logo/alt/link come from theme options (AC-01). data-header-cart-badge and
 * data-mobile-toggle are preserved (AC-02); the cart badge reads the real
 * WooCommerce cart count (AC-03) and is filterable for tests/integrations.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Options\Tabs\HeaderTab;

$es_brand    = HeaderTab::brand_name();
$es_tagline  = HeaderTab::brand_tagline();
$es_logo_id  = (int) es_option( 'logo_id' );
$es_logo_url = $es_logo_id > 0 ? (string) wp_get_attachment_image_url( $es_logo_id, 'full' ) : '';

$es_cart_count = 0;
if ( function_exists( 'WC' ) && WC()->cart ) {
	$es_cart_count = (int) WC()->cart->get_cart_contents_count();
}
/** Filter the header cart badge count. */
$es_cart_count = (int) apply_filters( 'es_header_cart_count', $es_cart_count );

$es_contact_url = Menus::page_url( 'contact' );

$es_desktop_walker = class_exists( '\ErfanSanat\Theme\Core\NavWalkers\DesktopWalker' )
	? new \ErfanSanat\Theme\Core\NavWalkers\DesktopWalker()
	: null;
?>
<div class="relative mx-auto flex max-w-[1300px] items-center justify-between gap-3 px-4 py-2.5 sm:px-5 lg:px-8">

	<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="group flex shrink-0 items-center gap-2.5 whitespace-nowrap">
		<?php if ( '' !== $es_logo_url ) : ?>
			<img src="<?php echo esc_url( $es_logo_url ); ?>" alt="<?php echo esc_attr( $es_brand ); ?>" class="h-9 w-9 rounded-xl object-contain sm:h-10 sm:w-10">
		<?php else : ?>
			<div class="grid h-9 w-9 place-items-center rounded-xl border border-emerald-200 bg-emerald-50/90 text-xs font-black tracking-tighter text-emerald-800 transition-colors group-hover:bg-emerald-700 group-hover:text-white sm:h-10 sm:w-10 sm:text-sm">ESE</div>
		<?php endif; ?>
		<div class="leading-tight">
			<div class="text-[14px] font-black tracking-tight text-slate-900 sm:text-[15px]"><?php echo esc_html( $es_brand ); ?></div>
			<div class="text-[10px] font-semibold text-slate-500"><?php echo esc_html( $es_tagline ); ?></div>
		</div>
	</a>

	<nav aria-label="منوی اصلی" class="hidden items-center gap-1 lg:flex">
		<?php
		Menus::render(
			'main-mega-menu',
			array(
				'walker'     => $es_desktop_walker,
				'items_wrap' => '%3$s',
				'container'  => false,
			)
		);
		?>
	</nav>

	<div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
		<button type="button" data-search-modal-open="true" aria-label="جستجو در محصولات و پروژه‌ها" aria-expanded="false" class="grid h-9 w-9 place-items-center rounded-xl border transition-all sm:h-10 sm:w-10 cursor-pointer border-slate-200 bg-slate-50/80 text-slate-700 hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg></button>

		<button type="button" data-cart-modal-open="true" aria-label="سبد خرید و پیش‌فاکتور" class="relative inline-flex h-9 w-9 items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50/70 text-xs font-extrabold text-emerald-900 transition-colors hover:bg-emerald-100 sm:h-10 sm:w-10 xl:w-auto xl:px-3.5 cursor-pointer whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag h-4 w-4 shrink-0 text-emerald-700" aria-hidden="true"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg><span class="hidden xl:inline">سبد خرید</span><span data-header-cart-badge="true" class="absolute -top-1.5 -right-1.5 grid h-4.5 min-w-4.5 place-items-center rounded-full bg-emerald-700 px-1 text-[10px] font-black text-white shadow-xs ring-2 ring-white tabular-nums xl:static xl:h-5 xl:min-w-5 xl:text-[10.5px] xl:shadow-none xl:ring-0"><?php echo esc_html( number_format_i18n( $es_cart_count ) ); ?></span></button>

		<a href="<?php echo esc_url( $es_contact_url ); ?>" class="hidden h-9 items-center justify-center gap-1.5 rounded-xl bg-emerald-700 px-3.5 text-xs font-extrabold text-white transition-colors hover:bg-emerald-800 sm:inline-flex sm:h-10 whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-3.5 w-3.5 shrink-0" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><span>استعلام قیمت</span></a>

		<button type="button" data-mobile-toggle="true" class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50/80 px-2.5 text-xs font-extrabold text-emerald-900 shadow-2xs transition-colors hover:bg-emerald-100 sm:h-10 sm:px-3 lg:hidden cursor-pointer" aria-label="باز کردن منوی موبایل" aria-expanded="false"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-4.5 w-4.5 text-emerald-700 sm:h-5 sm:w-5" aria-hidden="true"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg><span class="text-[11.5px] font-black sm:text-[12px]">منو</span></button>
	</div>
</div>
