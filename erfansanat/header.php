<?php
/**
 * Site header, cut from the approved static reference.
 *
 * Structure, class names and `data-*` contracts follow `static-mirror/` so the
 * transferred main.js keeps powering the mega menu, the mobile drawer and the
 * search panel. Every value (brand, contact, links) comes from theme settings.
 *
 * Documented deviation from the reference (one deviation, one reason):
 * the reference wraps the page in `<div id="root"><div id="home">` and the home
 * page repeats `id="home"` on its hero `<section>`, producing a duplicate id.
 * The theme opens a single wrapper `<div id="erfansanat-page">` here instead and
 * lets the hero section own `id="home"`, so the anchor still works and no id is
 * ever duplicated.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Navigation\MegaMenu;
use ErfanSanat\Theme\Navigation\MenuTree;
use ErfanSanat\Theme\Navigation\MobileMenu;
use ErfanSanat\Theme\Frontend\SiteParts;

$erfansanat_menu_tree = MenuTree::from_location( 'main-mega-menu' );

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php wp_head(); ?>
	<?php
	$erfansanat_header_scripts = SiteParts::custom_scripts( 'header' );
	if ( '' !== $erfansanat_header_scripts ) {
		echo $erfansanat_header_scripts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Stored only for holders of unfiltered_html and re-checked at print time.
	}
	?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="erfansanat-page" class="erfansanat-page relative min-h-screen overflow-x-clip selection:bg-emerald-600 selection:text-white">

	<?php SiteParts::notice_bar(); ?>

	<header data-navbar-header="true"
		class="fixed inset-x-0 top-0 z-50 transition-all duration-300 border-b border-slate-200/70 bg-white/95 backdrop-blur-md">

		<?php SiteParts::topbar(); ?>

		<div class="relative mx-auto flex max-w-[1300px] items-center justify-between gap-3 px-4 py-2.5 sm:px-5 lg:px-8">

			<?php SiteParts::brand( home_url( '/' ) ); ?>

			<nav aria-label="<?php esc_attr_e( 'منوی اصلی', 'erfansanat' ); ?>" class="hidden items-center gap-1 lg:flex">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
					class="rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap <?php echo is_front_page() ? 'bg-emerald-50 text-emerald-800' : 'text-slate-700 hover:bg-slate-50 hover:text-emerald-700'; ?>">
					<?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?>
				</a>

				<?php
				foreach ( MenuTree::blocks() as $erfansanat_block ) {
					if ( MenuTree::block_has_content( $erfansanat_block ) ) {
						MegaMenu::render_block( $erfansanat_block );
					}
				}

				// Real menu items become plain links; their children are shown
				// inline rather than invented as a second-level panel.
				foreach ( $erfansanat_menu_tree as $erfansanat_node ) {
					if ( ! is_array( $erfansanat_node ) || '' === (string) ( $erfansanat_node['url'] ?? '' ) ) {
						continue;
					}
					printf(
						'<a href="%s" class="rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700">%s</a>',
						esc_url( (string) $erfansanat_node['url'] ),
						esc_html( (string) ( $erfansanat_node['title'] ?? '' ) )
					);
				}
				?>
			</nav>

			<div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
				<?php get_template_part( 'template-parts/navigation/header-search' ); ?>

				<?php if ( function_exists( 'wc_get_cart_url' ) ) : ?>
					<a href="<?php echo esc_url( wc_get_cart_url() ); ?>"
						aria-label="<?php esc_attr_e( 'سبد خرید و پیش‌فاکتور', 'erfansanat' ); ?>"
						class="relative inline-flex h-9 w-9 items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50/70 text-xs font-extrabold text-emerald-900 transition-colors hover:bg-emerald-100 sm:h-10 sm:w-10">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
						<span class="absolute -left-1 -top-1 hidden min-w-4 rounded-full bg-emerald-700 px-1 text-center text-[10px] leading-4 text-white" data-header-cart-badge="true">
							<?php echo esc_html( (string) ( function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ) ); ?>
						</span>
					</a>
				<?php endif; ?>

				<button type="button" data-mobile-toggle="true"
					aria-label="<?php esc_attr_e( 'باز کردن منو', 'erfansanat' ); ?>"
					aria-expanded="false"
					class="inline-flex h-9 items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50/80 px-2.5 text-xs font-extrabold text-emerald-900 shadow-2xs transition-colors hover:bg-emerald-100 sm:h-10 sm:px-3 lg:hidden cursor-pointer">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M4 6h16"></path><path d="M4 12h16"></path><path d="M4 18h16"></path></svg>
					<span><?php esc_html_e( 'منو', 'erfansanat' ); ?></span>
				</button>
			</div>
		</div>
	</header>

	<?php MobileMenu::render( $erfansanat_menu_tree ); ?>

	<main id="primary" class="site-main pt-[104px]">
