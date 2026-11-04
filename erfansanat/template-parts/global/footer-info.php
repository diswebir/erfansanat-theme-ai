<?php
/**
 * Footer info grid: brand, quick access, product categories and contact.
 *
 * Address/contact come from theme options (SC-01); quick access from the
 * assigned main menu (with a filterable default); categories from the
 * tolidat-sherkat location (TolidatWalker, P04-T018). All links escaped
 * (AC-03); an empty menu never breaks a column (SC-02).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Options\Tabs\FooterTab;
use ErfanSanat\Theme\Options\Tabs\HeaderTab;

$es_contact = FooterTab::contact();
$es_brand   = HeaderTab::brand_name();
$es_desc    = FooterTab::description();
$es_badges  = FooterTab::badges();

$es_tel_phone  = preg_replace( '/\s+/', '', (string) $es_contact['phone'] );
$es_tel_mobile = preg_replace( '/\s+/', '', (string) $es_contact['mobile'] );

// Quick access: top-level items of the assigned main menu, else defaults.
$es_quick_links = array();
$es_locs        = get_nav_menu_locations();
if ( ! empty( $es_locs['main-mega-menu'] ) ) {
	$es_menu_items = wp_get_nav_menu_items( $es_locs['main-mega-menu'] );
	if ( $es_menu_items ) {
		foreach ( $es_menu_items as $es_mi ) {
			if ( 0 === (int) $es_mi->menu_item_parent ) {
				$es_quick_links[] = array( 'label' => $es_mi->title, 'url' => $es_mi->url );
			}
		}
	}
}
if ( empty( $es_quick_links ) ) {
	$es_quick_links = array(
		array( 'label' => 'فروشگاه محصولات', 'url' => Menus::page_url( 'shop' ) ),
		array( 'label' => 'پروژه‌های اجراشده', 'url' => Menus::page_url( 'projects' ) ),
		array( 'label' => 'خدمات نورپردازی', 'url' => Menus::page_url( 'services' ) ),
		array( 'label' => 'مجوزها و افتخارات', 'url' => Menus::page_url( 'licenses-and-honors' ) ),
		array( 'label' => 'سوالات متداول', 'url' => Menus::page_url( 'faq' ) ),
		array( 'label' => 'شرایط گارانتی محصولات', 'url' => Menus::page_url( 'refund_returns' ) ),
		array( 'label' => 'فرصت‌های شغلی و استخدام', 'url' => Menus::page_url( 'recruitment' ) ),
		array( 'label' => 'اخبار و مقالات', 'url' => Menus::page_url( 'blog' ) ),
	);
}
/** Filter the footer quick-access links. */
$es_quick_links = apply_filters( 'es_footer_quick_links', $es_quick_links );

$es_tolidat_walker = class_exists( '\ErfanSanat\Theme\Core\NavWalkers\TolidatWalker' )
	? new \ErfanSanat\Theme\Core\NavWalkers\TolidatWalker()
	: null;

$es_icons = array(
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin mt-1 h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"></path><rect x="2" y="4" width="20" height="16" rx="2"></rect></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><path d="M12 6v6l4 2"></path><circle cx="12" cy="12" r="10"></circle></svg>'
);
?>
<div class="grid gap-10 pb-14 md:grid-cols-2 lg:grid-cols-12">

	<div class="space-y-4 lg:col-span-4">
		<div class="flex items-center gap-3">
			<div class="grid h-11 w-11 place-items-center rounded-xl border border-emerald-200 bg-emerald-50"><span class="text-sm font-black text-emerald-700">ESE</span></div>
			<div>
				<div class="text-base font-extrabold text-slate-900"><?php echo esc_html( $es_brand ); ?></div>
				<div class="text-xs text-emerald-700">شرکت دانش‌بنیان نورپردازی شهری و صنعتی</div>
			</div>
		</div>
		<p class="text-[13px] leading-7 text-slate-600"><?php echo esc_html( $es_desc ); ?></p>
		<?php if ( ! empty( $es_badges ) ) : ?>
			<div class="flex flex-wrap gap-2 pt-1 text-[11px] font-bold text-emerald-800">
				<?php foreach ( $es_badges as $es_badge ) : ?>
					<?php $es_btext = is_array( $es_badge ) ? (string) ( $es_badge['text'] ?? '' ) : (string) $es_badge; ?>
					<?php if ( '' !== $es_btext ) : ?>
						<span class="rounded-full bg-emerald-50 px-3 py-1"><?php echo esc_html( $es_btext ); ?></span>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>

	<div class="lg:col-span-2">
		<h3 class="mb-4 text-sm font-extrabold text-slate-900">دسترسی سریع</h3>
		<?php if ( ! empty( $es_quick_links ) ) : ?>
			<ul class="space-y-2.5 text-[13px] text-slate-600">
				<?php foreach ( $es_quick_links as $es_link ) : ?>
					<li><a href="<?php echo esc_url( $es_link['url'] ); ?>" class="transition-colors hover:text-emerald-700"><?php echo esc_html( $es_link['label'] ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<div class="lg:col-span-3">
		<h3 class="mb-4 text-sm font-extrabold text-slate-900">دسته‌بندی محصولات</h3>
		<?php
		Menus::render(
			'tolidat-sherkat',
			array(
				'walker'     => $es_tolidat_walker,
				'menu_class' => 'space-y-2.5 text-[13px] text-slate-600',
				'items_wrap' => '<ul class="%2$s">%3$s</ul>',
				'container'  => false,
			)
		);
		?>
	</div>

	<div class="space-y-3 text-[13px] text-slate-600 lg:col-span-3">
		<h3 class="mb-4 text-sm font-extrabold text-slate-900">اطلاعات تماس کارخانه</h3>
		<div class="flex items-start gap-2.5"><?php echo $es_icons[0]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="leading-6"><?php echo esc_html( $es_contact['address'] ); ?></span></div>
		<div class="flex items-center gap-2.5"><?php echo $es_icons[1]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( 'tel:' . $es_tel_phone ); ?>" class="font-bold text-slate-800 hover:text-emerald-700"><?php echo esc_html( $es_contact['phone'] ); ?> (۱۰ خط ویژه)</a></div>
		<div class="flex items-center gap-2.5"><?php echo $es_icons[2]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( 'tel:' . $es_tel_mobile ); ?>" class="hover:text-emerald-700">مدیر فروش: <?php echo esc_html( $es_contact['mobile'] ); ?></a></div>
		<div class="flex items-center gap-2.5"><?php echo $es_icons[3]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( 'mailto:' . $es_contact['email'] ); ?>" class="hover:text-emerald-700"><?php echo esc_html( $es_contact['email'] ); ?></a></div>
		<div class="flex items-center gap-2.5"><?php echo $es_icons[4]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر</span></div>
	</div>

</div>
