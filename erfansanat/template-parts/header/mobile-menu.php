<?php
/**
 * Off-canvas mobile menu drawer.
 *
 * data-mobile-sidebar + inset/y/width classes mirror the reference (AC-01).
 * Open/close + scroll-lock are driven by the bundled main.js; navigation.js
 * adds Escape-to-close and focus management (AC-02/AC-03). Menu items use the
 * MobileWalker (P04-T010) with a safe fallback before it lands.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Options\Tabs\FooterTab;
use ErfanSanat\Theme\Options\Tabs\HeaderTab;

// Accessibility enhancements (Escape / focus trap / landing scroll-spy).
wp_enqueue_script(
	'es-navigation',
	ES_ASSETS_URI . 'js/navigation.js',
	array(),
	(string) ( is_file( ES_ASSETS_DIR . 'js/navigation.js' ) ? filemtime( ES_ASSETS_DIR . 'js/navigation.js' ) : ES_VERSION ),
	true
);

$es_brand    = HeaderTab::brand_name();
$es_contact  = FooterTab::contact();
$es_phone    = (string) $es_contact['phone'];
$es_tel      = preg_replace( '/\s+/', '', $es_phone );

$es_mobile_walker = class_exists( '\ErfanSanat\Theme\Core\NavWalkers\MobileWalker' )
	? new \ErfanSanat\Theme\Core\NavWalkers\MobileWalker()
	: null;

$es_x_icon      = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-4.5 w-4.5" aria-hidden="true"><path d="M18 6 6 18"></path><path d="m6 6 12 12"></path></svg>';
$es_search_icon = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-slate-400" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>';
$es_bag_icon    = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg>';
$es_phone_icon  = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>';
?>
<div data-mobile-menu="true" class="fixed inset-0 z-[90] lg:hidden hidden" aria-hidden="true">
	<div data-mobile-backdrop="true" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs transition-opacity"></div>
	<aside data-mobile-sidebar="true" aria-label="منوی موبایل" class="fixed inset-y-0 right-0 z-[95] flex h-dvh max-h-dvh w-[88vw] max-w-[380px] flex-col border-l border-emerald-100 bg-white shadow-2xl">

		<div class="flex shrink-0 items-center justify-between border-b border-slate-100 px-4 py-3.5">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex items-center gap-2.5">
				<div class="grid h-9 w-9 place-items-center rounded-xl border border-emerald-200 bg-emerald-50 text-xs font-black text-emerald-800">ESE</div>
				<div>
					<div class="text-sm font-black text-slate-900"><?php echo esc_html( $es_brand ); ?></div>
					<div class="text-[10px] font-semibold text-emerald-700">شرکت دانش‌بنیان نورپردازی شهری</div>
				</div>
			</a>
			<button type="button" data-mobile-close="true" aria-label="بستن منو" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 bg-slate-50 text-slate-600 hover:bg-slate-100 cursor-pointer"><?php echo $es_x_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</div>

		<div class="shrink-0 border-b border-slate-100 px-4 py-2.5">
			<button type="button" data-search-modal-open="true" class="flex w-full items-center gap-2.5 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-xs font-bold text-slate-600 hover:border-emerald-300 cursor-pointer"><?php echo $es_search_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>جستجو در محصولات و پروژه‌ها...</span></button>
		</div>

		<div class="flex-1 space-y-2.5 overflow-y-auto overscroll-contain px-3.5 py-3.5">
			<?php
			Menus::render(
				'mobile-menu',
				array(
					'walker'     => $es_mobile_walker,
					'items_wrap' => '%3$s',
					'container'  => false,
				)
			);
			?>
		</div>

		<div class="shrink-0 space-y-2 border-t border-slate-100 bg-slate-50 p-3.5">
			<button type="button" data-cart-modal-open="true" class="flex w-full items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-white py-2.5 text-xs font-black text-emerald-900 shadow-2xs cursor-pointer"><?php echo $es_bag_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>مشاهده سبد خرید و پیش‌فاکتور</span></button>
			<a href="<?php echo esc_url( 'tel:' . $es_tel ); ?>" class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-700 py-2.5 text-xs font-black text-white shadow-sm"><?php echo $es_phone_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>تماس با کارخانه: <?php echo esc_html( $es_phone ); ?></span></a>
		</div>

	</aside>
</div>
