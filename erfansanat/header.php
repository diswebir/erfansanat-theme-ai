<?php
/**
 * Header shell: standard head, body open, fixed navbar and global overlays.
 *
 * DOM parity with static-mirror/index.html. The navbar parts (topbar,
 * brand-row, search-modal) and overlays (mobile-menu, cart-modal) are loaded
 * as template parts and filled by P04-T003..T013.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bg-[#f6f8f7] text-[#1e293b] antialiased' ); ?>>
<?php wp_body_open(); ?>
<div id="root">
<div class="min-h-screen overflow-x-clip selection:bg-emerald-600 selection:text-white">

	<div class="pointer-events-none fixed inset-0 z-0 overflow-hidden" aria-hidden="true">
		<div class="absolute -top-36 right-[5%] h-[36rem] w-[36rem] rounded-full bg-emerald-200/40 blur-[130px]"></div>
		<div class="absolute top-[32%] -left-32 h-[32rem] w-[32rem] rounded-full bg-teal-100/50 blur-[130px]"></div>
		<div class="absolute bottom-[10%] right-[18%] h-[34rem] w-[34rem] rounded-full bg-green-100/60 blur-[140px]"></div>
		<div class="light-dot-grid absolute inset-0 opacity-60"></div>
	</div>

	<div class="relative z-10">

		<header data-navbar-header="true" class="fixed inset-x-0 top-0 z-50 transition-all duration-300 border-b border-slate-200/70 bg-white/95 backdrop-blur-md">
			<?php
			get_template_part( 'template-parts/header/topbar' );
			get_template_part( 'template-parts/header/brand-row' );
			get_template_part( 'template-parts/header/search-modal' );
			?>
		</header>

		<?php
		get_template_part( 'template-parts/header/mobile-menu' );
		get_template_part( 'template-parts/header/cart-modal' );
