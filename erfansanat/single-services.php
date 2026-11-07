<?php
/**
 * Single service (P07-T027). Reference: services/square-lighting.html.
 *
 * Hero (badge, position, prev/next service links) + the details part
 * (deliverables/execution path/CTA/related projects). Missing/invalid service
 * renders a safe notice instead of a fatal (AC-03). Breadcrumb/back links go
 * to the smartelement page. Global contact CTA comes from the footer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

get_header();

$es_service_id = (int) get_the_ID();
$es_valid      = $es_service_id > 0 && 'services' === get_post_type( $es_service_id ) && 'publish' === get_post_status( $es_service_id );
$es_services_url = Menus::page_url( 'smartelement' );
?>
<main class="min-h-screen bg-[#f8faf9]">
	<?php if ( ! $es_valid ) : ?>
		<section class="mx-auto max-w-3xl px-5 pb-24 pt-40 text-center">
			<h1 class="text-2xl font-black text-slate-900"><?php esc_html_e( 'خدمت مورد نظر یافت نشد', 'erfansanat' ); ?></h1>
			<p class="mt-4 text-[14px] leading-8 text-slate-600"><?php esc_html_e( 'ممکن است این خدمت حذف شده باشد. فهرست کامل خدمات را در برگه خدمات ببینید.', 'erfansanat' ); ?></p>
			<a href="<?php echo esc_url( $es_services_url ); ?>" class="mt-8 inline-flex items-center gap-2 rounded-full bg-emerald-700 px-7 py-3.5 text-[14px] font-extrabold text-white transition-colors hover:bg-emerald-800"><?php esc_html_e( 'بازگشت به برگه خدمات', 'erfansanat' ); ?></a>
		</section>
	<?php else : ?>
		<?php
		$es_title   = get_the_title( $es_service_id );
		$es_badge   = (string) get_post_meta( $es_service_id, '_es_service_badge', true );
		$es_excerpt = (string) get_post_field( 'post_excerpt', $es_service_id );
		if ( '' === trim( $es_excerpt ) ) {
			$es_excerpt = wp_trim_words( wp_strip_all_tags( (string) get_post_field( 'post_content', $es_service_id ) ), 40 );
		}
		$es_img = get_the_post_thumbnail_url( $es_service_id, 'large' );
		if ( ! $es_img ) {
			$es_img = ES_ASSETS_URI . 'images/light-sphere.jpg';
		}
		$es_contact = FooterTab::contact();
		$es_phone   = (string) $es_contact['phone'];
		$es_tel     = preg_replace( '/\s+/', '', $es_phone );
		$es_fa      = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );

		// Position + prev/next among published services (menu_order/date order).
		$es_all = get_posts( array( 'post_type' => 'services', 'post_status' => 'publish', 'posts_per_page' => 50, 'orderby' => array( 'menu_order' => 'ASC', 'date' => 'ASC' ) ) );
		$es_ids = wp_list_pluck( $es_all, 'ID' );
		$es_pos = array_search( $es_service_id, $es_ids, true );
		$es_pos = false === $es_pos ? 0 : (int) $es_pos;
		$es_count = count( $es_ids );
		$es_prev  = $es_all[ ( $es_pos - 1 + $es_count ) % max( 1, $es_count ) ] ?? null;
		$es_next  = $es_all[ ( $es_pos + 1 ) % max( 1, $es_count ) ] ?? null;
		?>
		<section class="relative overflow-hidden bg-[#f6f8f7] pt-28 lg:pt-36">
			<div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(1000px 520px at 50% -10%, rgba(0,177,64,0.10), transparent 60%)"></div><div class="light-dot-grid absolute inset-0 opacity-50"></div><div class="absolute left-[12%] top-20 h-64 w-64 rounded-full bg-emerald-200/50 blur-[100px]"></div><div class="absolute bottom-10 right-[8%] h-56 w-56 rounded-full bg-teal-100/70 blur-[90px]"></div></div>
			<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
				<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><a href="<?php echo esc_url( $es_services_url ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php esc_html_e( 'خدمات', 'erfansanat' ); ?></a></span><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800"><?php echo esc_html( $es_title ); ?></span></span></nav>
				<div class="grid items-end gap-10 pb-14 pt-10 lg:grid-cols-[1.1fr_0.9fr] lg:pb-20 lg:pt-14">
					<div>
						<div class="mb-6 flex flex-wrap items-center gap-3">
							<span class="inline-flex items-center gap-2 rounded-full bg-emerald-700 px-4 py-1.5 text-[12px] font-black text-white shadow-[0_0_24px_rgba(0,125,45,0.3)]"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-list h-3.5 w-3.5" aria-hidden="true"><rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><path d="M12 11h4"></path><path d="M12 16h4"></path><path d="M8 11h.01"></path><path d="M8 16h.01"></path></svg><?php echo esc_html( sprintf( __( 'خدمت %1$s از %2$s', 'erfansanat' ), strtr( (string) ( $es_pos + 1 ), $es_fa ), strtr( (string) $es_count, $es_fa ) ) ); ?></span>
							<?php if ( '' !== $es_badge ) : ?><span class="rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-bold text-emerald-800 shadow-2xs"><?php echo esc_html( $es_badge ); ?></span><?php endif; ?>
						</div>
						<h1 class="text-[2.4rem] font-black leading-[1.28] text-slate-950 sm:text-[3.1rem] lg:text-[3.7rem] lg:leading-[1.22]"><?php echo esc_html( $es_title ); ?></h1>
						<?php if ( '' !== trim( $es_excerpt ) ) : ?><p class="mt-6 max-w-[36rem] border-r-2 border-emerald-500/60 pr-5 text-[15px] font-medium leading-9 text-slate-600 lg:text-[16px]"><?php echo esc_html( $es_excerpt ); ?></p><?php endif; ?>
						<div class="mt-8 flex flex-wrap gap-3">
							<?php if ( '' !== $es_phone ) : ?><a href="<?php echo esc_url( 'tel:' . $es_tel ); ?>" class="flex items-center gap-2.5 rounded-2xl bg-emerald-700 px-7 py-3.5 text-[14.5px] font-black text-white shadow-[0_16px_40px_-12px_rgba(0,125,45,0.45)] transition-all hover:bg-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4.5 w-4.5" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><?php esc_html_e( 'درخواست اجرای این خدمت', 'erfansanat' ); ?></a><?php endif; ?>
							<a href="#service-details" class="flex items-center gap-2 rounded-2xl border border-emerald-200 bg-white px-7 py-3.5 text-[14px] font-bold text-emerald-800 shadow-sm transition-colors hover:border-emerald-400 hover:bg-emerald-50"><?php esc_html_e( 'جزئیات خدمت', 'erfansanat' ); ?></a>
						</div>
						<?php if ( $es_count > 1 && $es_prev && $es_next ) : ?>
							<div class="mt-10 flex flex-wrap items-center gap-3 border-t border-emerald-200/70 pt-6">
								<a href="<?php echo esc_url( get_permalink( $es_prev ) ); ?>" class="group flex items-center gap-2 text-[13px] font-bold text-slate-600 transition-colors hover:text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4 rotate-180 transition-transform group-hover:-translate-x-1" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg><?php echo esc_html( get_the_title( $es_prev ) ); ?></a>
								<span class="h-4 w-px bg-emerald-200"></span>
								<a href="<?php echo esc_url( get_permalink( $es_next ) ); ?>" class="group flex items-center gap-2 text-[13px] font-bold text-slate-600 transition-colors hover:text-emerald-700"><?php echo esc_html( get_the_title( $es_next ) ); ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4 transition-transform group-hover:-translate-x-1" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg></a>
							</div>
						<?php endif; ?>
					</div>
					<div class="relative mx-auto w-full max-w-[30rem]">
						<div aria-hidden="true" class="absolute -inset-3 rounded-[2.5rem] border border-emerald-200/80 bg-emerald-100/35"></div>
						<div class="relative overflow-hidden rounded-[2.25rem] border border-emerald-100 bg-white p-2 shadow-[0_36px_80px_-30px_rgba(0,80,40,0.28)]">
							<div class="relative overflow-hidden rounded-[1.85rem]"><img src="<?php echo esc_url( $es_img ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" class="h-[22rem] w-full object-cover sm:h-[25rem]"/><div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div><div class="absolute bottom-4 inset-x-4 flex items-center justify-between rounded-2xl border border-white/80 bg-white/95 px-4 py-3 shadow-lg backdrop-blur-md"><span class="text-[12.5px] font-bold text-slate-900"><?php echo esc_html( '' !== $es_badge ? $es_badge : $es_title ); ?></span><span class="rounded-lg bg-emerald-50 border border-emerald-200 px-3 py-1 text-[11px] font-black text-emerald-800"><?php esc_html_e( 'IP67/IP68', 'erfansanat' ); ?></span></div></div>
						</div>
					</div>
				</div>
			</div>
			<div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-[#f8faf9]"></div>
		</section>

		<?php get_template_part( 'template-parts/service/details', null, array( 'service_id' => $es_service_id ) ); ?>
	<?php endif; ?>
</main>
<?php
get_footer();
