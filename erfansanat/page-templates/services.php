<?php
/**
 * Services overview page — smartelement (P07-T026).
 * Reference: static-mirror/services.html.
 *
 * The service rows come from the services CPT (publish, menu_order/date order;
 * no standalone archive — AC-03). Badge/bullets are per-service meta
 * (_es_service_badge / _es_service_bullets, formalized by the T027 metabox);
 * the description falls back to excerpt/content. Cooperation path and CTA are
 * editable in the pages options tab. Global contact CTA comes from the footer.
 *
 * Template Name: خدمات (اسمارت‌المنت)
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Options\Tabs\PagesTab;

get_header();

$es_intro = (string) es_option( 'services_intro' );
$es_path  = PagesTab::services_path();
$es_cta_text = (string) es_option( 'services_cta_text' );
if ( '' === trim( $es_cta_text ) ) {
	$es_cta_text = 'درخواست مشاوره رایگان';
}
$es_cta_url = (string) es_option( 'services_cta_url' );
if ( '' === trim( $es_cta_url ) ) {
	$es_cta_url = Menus::page_url( 'contact' );
}
$es_fa = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );

$es_services = get_posts(
	array(
		'post_type'      => 'services',
		'post_status'    => 'publish',
		'posts_per_page' => 20,
		'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
	)
);

$es_check_svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check mt-1 h-4.5 w-4.5 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>';
$es_step_icons = array(
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-7 w-7 text-emerald-700" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-pencil-ruler h-7 w-7 text-emerald-700" aria-hidden="true"><path d="M13 7 8.7 2.7a2.41 2.41 0 0 0-3.4 0L2.7 5.3a2.41 2.41 0 0 0 0 3.4L7 13"></path><path d="m8 6 2-2"></path><path d="m18 16 2-2"></path><path d="m17 11 4.3 4.3c.94.94.94 2.46 0 3.4l-2.6 2.6c-.94.94-2.46.94-3.4 0L11 17"></path><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"></path><path d="m15 5 4 4"></path></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-wrench h-7 w-7 text-emerald-700" aria-hidden="true"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.106-3.105c.32-.322.863-.22.983.218a6 6 0 0 1-8.259 7.057l-7.91 7.91a1 1 0 0 1-2.999-3l7.91-7.91a6 6 0 0 1 7.057-8.259c.438.12.54.662.219.984z"></path></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hard-hat h-7 w-7 text-emerald-700" aria-hidden="true"><path d="M2 18a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1z"></path><path d="M10 10V5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5"></path><path d="M4 15v-3a6 6 0 0 1 6-6"></path><path d="M14 6a6 6 0 0 1 6 6v3"></path></svg>',
	'<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lightbulb h-7 w-7 text-emerald-700" aria-hidden="true"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A6 6 0 0 0 6 8c0 1 .2 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"></path><path d="M9 18h6"></path><path d="M10 22h4"></path></svg>',
);
?>
<main class="min-h-screen bg-[#f8faf9]">
	<section class="relative overflow-hidden bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-36">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 20% 0%, rgba(0,177,64,0.10), transparent 60%)"></div><div class="light-dot-grid absolute inset-0 opacity-45"></div><div class="absolute -left-32 top-16 h-80 w-80 rounded-full bg-emerald-200/45 blur-[110px]"></div></div>
		<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
			<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800"><?php esc_html_e( 'خدمات', 'erfansanat' ); ?></span></span></nav>
			<div class="mt-10 grid items-center gap-12 lg:grid-cols-2">
				<div>
					<div class="mb-5 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs"><span class="h-2 w-2 rounded-full bg-emerald-600"></span><span><?php esc_html_e( 'از ایده تا درخشش — مشاوره رایگان', 'erfansanat' ); ?></span></div>
					<h1 class="text-[2.5rem] font-black leading-[1.25] tracking-[-0.015em] text-slate-950 sm:text-[3.3rem] lg:text-[3.9rem] lg:leading-[1.18]"><?php esc_html_e( 'خدمات', 'erfansanat' ); ?><br/><span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent"><?php esc_html_e( 'نورپردازی شهری', 'erfansanat' ); ?></span></h1>
					<?php if ( '' !== trim( $es_intro ) ) : ?><p class="mt-6 max-w-[34rem] text-[15.5px] font-medium leading-9 text-slate-600 lg:text-[16.5px]"><?php echo esc_html( $es_intro ); ?></p><?php endif; ?>
					<?php if ( $es_services ) : ?>
						<div class="mt-8"><span class="mb-3 block text-[12px] font-extrabold tracking-wide text-slate-400"><?php esc_html_e( 'فهرست خدمات', 'erfansanat' ); ?></span>
							<div class="flex flex-wrap gap-2">
								<?php foreach ( $es_services as $es_i => $es_service ) : ?>
									<span class="flex items-center gap-2 rounded-xl border border-emerald-200 bg-white px-3.5 py-2 text-[12.5px] font-bold text-slate-700 shadow-xs"><span class="grid h-5 w-5 place-items-center rounded-md bg-emerald-50 text-[10.5px] font-black text-emerald-700"><?php echo esc_html( strtr( (string) ( $es_i + 1 ), $es_fa ) ); ?></span><?php echo esc_html( get_the_title( $es_service ) ); ?></span>
								<?php endforeach; ?>
							</div>
						</div>
					<?php endif; ?>
					<div class="mt-8 flex flex-wrap items-center gap-3"><a href="#services-list" class="group inline-flex items-center gap-2 rounded-full border border-emerald-300 bg-white px-6 py-3.5 text-[14px] font-extrabold text-emerald-800 shadow-sm transition-all hover:border-emerald-500 hover:bg-emerald-50"><?php esc_html_e( 'مشاهده خدمات', 'erfansanat' ); ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-down h-4 w-4 transition-transform duration-300 group-hover:translate-y-1" aria-hidden="true"><path d="M12 5v14"></path><path d="m19 12-7 7-7-7"></path></svg></a></div>
				</div>
				<div class="relative mx-auto w-full max-w-[30rem]">
					<div aria-hidden="true" class="absolute left-1/2 top-1/3 h-[60%] w-[70%] -translate-x-1/2 rounded-full bg-emerald-200/50 blur-[80px]"></div>
					<div class="relative overflow-hidden rounded-[2.25rem] border border-emerald-100 bg-white shadow-[0_36px_80px_-32px_rgba(0,80,40,0.28)]">
						<div class="relative h-52 overflow-hidden sm:h-60"><img src="<?php echo esc_url( ES_ASSETS_URI . 'images/light-sphere.jpg' ); ?>" alt="" class="h-full w-full object-cover"/><div class="absolute inset-0 bg-gradient-to-t from-emerald-950/70 via-emerald-950/15 to-transparent"></div><div class="absolute bottom-4 right-5 left-5 flex items-end justify-between"><div><p class="text-[11.5px] font-bold text-emerald-200"><?php esc_html_e( 'مسیر همکاری با ما', 'erfansanat' ); ?></p><p class="mt-1 text-[17px] font-black text-white"><?php echo esc_html( sprintf( __( 'از ایده تا درخشش، در %s گام', 'erfansanat' ), strtr( (string) count( $es_path ), $es_fa ) ) ); ?></p></div><img src="<?php echo esc_url( ES_ASSETS_URI . 'images/hero.jpg' ); ?>" alt="" class="hidden h-16 w-16 rounded-2xl border-2 border-white/70 object-cover shadow-lg sm:block"/></div></div>
						<ol class="relative space-y-0 p-5 sm:p-6">
							<span aria-hidden="true" class="absolute bottom-8 right-[2.35rem] top-8 w-0.5 rounded-full bg-gradient-to-b from-emerald-500 via-emerald-300 to-emerald-100 sm:right-[2.6rem]"></span>
							<?php foreach ( $es_path as $es_i => $es_step ) : ?>
								<li class="relative flex items-start gap-4 py-2.5"><span class="relative z-10 grid h-9 w-9 shrink-0 place-items-center rounded-xl border border-emerald-200 bg-white text-[13px] font-black text-emerald-700 shadow-xs"><?php echo esc_html( strtr( (string) ( $es_i + 1 ), $es_fa ) ); ?></span><div class="min-w-0"><p class="text-[13.5px] font-extrabold text-slate-900"><?php echo esc_html( $es_step['title'] ); ?></p><p class="mt-0.5 text-[12px] leading-6 text-slate-500"><?php echo esc_html( $es_step['description'] ); ?></p></div></li>
							<?php endforeach; ?>
						</ol>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section id="services-list" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-16 lg:px-8 lg:py-20">
		<div class="flex flex-col gap-16 lg:gap-24">
			<?php foreach ( $es_services as $es_i => $es_service ) : ?>
				<?php
				$es_title  = get_the_title( $es_service );
				$es_badge  = (string) get_post_meta( $es_service->ID, '_es_service_badge', true );
				$es_desc   = $es_service->post_excerpt;
				if ( '' === trim( $es_desc ) ) {
					$es_desc = wp_trim_words( wp_strip_all_tags( $es_service->post_content ), 40 );
				}
				$es_bullets_raw = (string) get_post_meta( $es_service->ID, '_es_service_bullets', true );
				$es_bullets     = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $es_bullets_raw ) ), static function ( $l ) { return '' !== $l; } ) );
				$es_img    = get_the_post_thumbnail_url( $es_service->ID, 'large' );
				if ( ! $es_img ) {
					$es_img = ES_ASSETS_URI . 'images/light-sphere.jpg';
				}
				$es_ltr = ( 1 === $es_i % 2 );
				?>
				<div>
					<div class="grid items-center gap-8 lg:grid-cols-2 lg:gap-14 <?php echo $es_ltr ? 'lg:[direction:ltr]' : ''; ?>">
						<div class="[direction:rtl]">
							<div class="relative overflow-hidden rounded-[1.75rem] border border-emerald-100 shadow-lg"><img src="<?php echo esc_url( $es_img ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" loading="lazy" class="h-72 w-full object-cover lg:h-80"/><span class="absolute right-5 top-5 grid h-12 w-12 place-items-center rounded-2xl bg-emerald-950/80 text-xl font-black text-emerald-200 backdrop-blur-md"><?php echo esc_html( strtr( sprintf( '%02d', $es_i + 1 ), $es_fa ) ); ?></span></div>
						</div>
						<div class="flex flex-col items-start gap-5 [direction:rtl]">
							<?php if ( '' !== $es_badge ) : ?><span class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-1.5 text-[12.5px] font-bold text-emerald-800"><?php echo esc_html( $es_badge ); ?></span><?php endif; ?>
							<h2 class="text-2xl font-extrabold text-slate-900 lg:text-3xl"><?php echo esc_html( $es_title ); ?></h2>
							<?php if ( '' !== trim( $es_desc ) ) : ?><p class="text-[15px] leading-9 text-slate-600"><?php echo esc_html( $es_desc ); ?></p><?php endif; ?>
							<?php if ( $es_bullets ) : ?>
								<ul class="grid gap-3 sm:grid-cols-2">
									<?php foreach ( $es_bullets as $es_bullet ) : ?>
										<li class="flex items-start gap-2.5 text-[13.5px] font-semibold leading-7 text-slate-700"><?php echo $es_check_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><?php echo esc_html( $es_bullet ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<div class="mt-2 flex flex-wrap gap-3">
								<a href="<?php echo esc_url( get_permalink( $es_service ) ); ?>" class="group inline-flex items-center gap-2.5 rounded-full bg-emerald-700 px-7 py-3.5 text-[14px] font-extrabold text-white shadow-lg shadow-emerald-700/25 transition-all hover:bg-emerald-800"><?php esc_html_e( 'جزئیات این خدمت', 'erfansanat' ); ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a>
								<a href="<?php echo esc_url( Menus::page_url( 'contact' ) ); ?>" class="inline-flex items-center gap-2.5 rounded-full border border-emerald-200 bg-white px-7 py-3.5 text-[14px] font-extrabold text-emerald-800 transition-all hover:border-emerald-400 hover:bg-emerald-50"><?php esc_html_e( 'درخواست مشاوره', 'erfansanat' ); ?></a>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="border-y border-emerald-100 bg-white py-16 lg:py-20">
		<div class="mx-auto max-w-7xl px-5 lg:px-8">
			<div><h2 class="mb-12 text-center text-2xl font-extrabold text-slate-900 lg:text-3xl"><?php esc_html_e( 'مسیر همکاری با ما؛', 'erfansanat' ); ?> <span class="text-emerald-700"><?php esc_html_e( 'از ایده تا درخشش', 'erfansanat' ); ?></span></h2></div>
			<div class="relative grid gap-10 sm:grid-cols-3 lg:grid-cols-5 lg:gap-6">
				<div class="absolute right-[10%] left-[10%] top-8 hidden h-px bg-gradient-to-l from-transparent via-emerald-400/50 to-transparent lg:block"></div>
				<?php foreach ( $es_path as $es_i => $es_step ) : ?>
					<div>
						<div class="relative flex flex-col items-center gap-3.5 text-center">
							<span class="relative z-10 grid h-16 w-16 place-items-center rounded-2xl border border-emerald-200 bg-emerald-50 shadow-sm"><?php echo $es_step_icons[ $es_i % count( $es_step_icons ) ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><span class="absolute -right-2 -top-2 grid h-6 w-6 place-items-center rounded-full bg-emerald-600 text-[11px] font-black text-white"><?php echo esc_html( strtr( (string) ( $es_i + 1 ), $es_fa ) ); ?></span></span>
							<span class="text-[14.5px] font-extrabold text-slate-900"><?php echo esc_html( $es_step['title'] ); ?></span>
							<span class="text-[12px] leading-6 text-slate-500"><?php echo esc_html( $es_step['description'] ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<div class="mt-12 text-center">
				<a href="<?php echo esc_url( $es_cta_url ); ?>" class="group inline-flex items-center gap-2.5 rounded-full bg-emerald-700 px-8 py-4 text-[14.5px] font-extrabold text-white shadow-lg shadow-emerald-700/25 transition-all hover:bg-emerald-800"><?php echo esc_html( $es_cta_text ); ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></a>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
