<?php
/**
 * FAQ page (P07-T023). Reference: static-mirror/faq.html.
 *
 * Groups/questions/answers come from the editable repeater (PagesTab::faq()).
 * The accordion uses the theme's main.js hooks (data-faq-list/toggle/body/icon);
 * a dedicated search input (assets/js/faq-search.js) filters the questions.
 * When the FAQ is explicitly empty no accordion section is built (AC-03).
 * The global contact CTA is rendered by the footer — never duplicated here.
 *
 * Template Name: سوالات متداول
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\PagesTab;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

get_header();

$es_heading = (string) es_option( 'faq_heading' );
if ( '' === trim( $es_heading ) ) {
	$es_heading = 'سوالات متداول';
}
$es_intro   = (string) es_option( 'faq_intro' );
$es_items   = PagesTab::faq();
$es_contact = FooterTab::contact();
$es_fa      = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_phone   = (string) $es_contact['phone'];
$es_tel     = preg_replace( '/\s+/', '', $es_phone );
$es_count   = count( $es_items );

// Unique groups, in first-seen order.
$es_groups = array();
foreach ( $es_items as $es_item ) {
	$g = trim( $es_item['group'] );
	if ( '' !== $g && ! in_array( $g, $es_groups, true ) ) {
		$es_groups[] = $g;
	}
}
$es_warranty = home_url( '/refund_returns/' );
?>
<main class="min-h-screen bg-[#f8faf9]">
	<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div><div class="light-dot-grid absolute inset-0 opacity-45"></div><div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div></div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
			<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800"><?php echo esc_html( $es_heading ); ?></span></span></nav>
			<div class="pt-6">
				<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs"><span class="h-2 w-2 rounded-full bg-emerald-600"></span><span><?php esc_html_e( 'مرکز پاسخگویی فنی و بازرگانی', 'erfansanat' ); ?></span></div>
				<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $es_heading ); ?> <span class="text-emerald-700"><?php esc_html_e( 'نورپردازی شهری و خرید محصولات', 'erfansanat' ); ?></span></h1>
				<?php if ( '' !== trim( $es_intro ) ) : ?><p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]"><?php echo esc_html( $es_intro ); ?></p><?php endif; ?>
			</div>
		</div>
	</section>

	<?php if ( $es_items ) : ?>
		<section class="mx-auto max-w-7xl px-5 py-14 lg:px-8 lg:py-20" data-faq-section="true">
			<div class="grid items-start gap-10 lg:grid-cols-12">
				<aside class="lg:col-span-4">
					<div class="sticky top-28 space-y-5 rounded-[1.75rem] border border-slate-200/90 bg-white p-6 shadow-[0_18px_45px_-28px_rgba(15,23,42,0.10)]">
						<div class="flex items-center gap-3"><span class="grid h-11 w-11 place-items-center rounded-2xl bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-question-mark h-5.5 w-5.5" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><path d="M12 17h.01"></path></svg></span><div><h2 class="text-[15.5px] font-black text-slate-900"><?php esc_html_e( 'دسته‌بندی پرسش‌ها', 'erfansanat' ); ?></h2><p class="text-[12px] font-semibold text-slate-500"><span data-faq-count="true"><?php echo esc_html( strtr( (string) $es_count, $es_fa ) ); ?></span> <?php esc_html_e( 'پرسش و پاسخ تخصصی', 'erfansanat' ); ?></p></div></div>
						<?php if ( $es_groups ) : ?>
							<div class="space-y-2 border-t border-slate-100 pt-4">
								<?php foreach ( $es_groups as $es_group ) : ?>
									<div class="flex items-center gap-2.5 rounded-xl bg-slate-50 px-3.5 py-2.5 text-[12.5px] font-bold text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layers h-4 w-4 text-emerald-700" aria-hidden="true"><path d="M12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83z"></path><path d="M2 12a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 12"></path><path d="M2 17a1 1 0 0 0 .58.91l8.6 3.91a2 2 0 0 0 1.65 0l8.58-3.9A1 1 0 0 0 22 17"></path></svg><span><?php echo esc_html( $es_group ); ?></span></div>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
						<div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5">
							<div class="text-[13.5px] font-black text-emerald-950"><?php esc_html_e( 'پاسخ سوال خود را پیدا نکردید؟', 'erfansanat' ); ?></div>
							<p class="mt-1.5 text-[12.5px] leading-6 text-slate-600"><?php esc_html_e( 'کارشناسان مهندسی و فروش کارخانه آماده پاسخگویی مستقیم و ارائه مشاوره رایگان به شما هستند.', 'erfansanat' ); ?></p>
							<?php if ( '' !== $es_phone ) : ?><a href="<?php echo esc_url( 'tel:' . $es_tel ); ?>" class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-800 px-4 py-3 text-[13px] font-black text-white transition-colors hover:bg-emerald-900 whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-phone-call h-4 w-4" aria-hidden="true"><path d="M13 2a9 9 0 0 1 9 9"></path><path d="M13 6a5 5 0 0 1 5 5"></path><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"></path></svg><span><?php echo esc_html( strtr( str_replace( '-', ' - ', $es_phone ), $es_fa ) ); ?></span></a><?php endif; ?>
							<a href="<?php echo esc_url( $es_warranty ); ?>" class="mt-2.5 flex w-full items-center justify-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-4 py-2.5 text-[12px] font-bold text-emerald-800 transition-colors hover:bg-emerald-100 whitespace-nowrap"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg><span><?php esc_html_e( 'مشاهده شرایط کامل گارانتی ۲۴ ماهه', 'erfansanat' ); ?></span></a>
						</div>
					</div>
				</aside>
				<div class="lg:col-span-8">
					<div class="relative mb-5">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="pointer-events-none absolute right-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.3-4.3"></path></svg>
						<input type="search" data-faq-search="true" placeholder="<?php esc_attr_e( 'جستجو در سوالات متداول…', 'erfansanat' ); ?>" aria-label="<?php esc_attr_e( 'جستجو در سوالات متداول', 'erfansanat' ); ?>" class="w-full rounded-2xl border border-slate-200 bg-white py-3.5 pr-12 pl-4 text-[14px] text-slate-800 outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10"/>
					</div>
					<div data-faq-list="true">
						<div class="space-y-4">
							<?php foreach ( $es_items as $es_idx => $es_item ) : ?>
								<?php
								$es_open = ( 0 === $es_idx );
								$es_text = $es_item['group'] . ' ' . $es_item['question'] . ' ' . $es_item['answer'];
								?>
								<div data-faq-item="true" data-faq-text="<?php echo esc_attr( $es_text ); ?>">
									<div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white transition-all hover:border-emerald-300">
										<button type="button" data-faq-toggle="<?php echo esc_attr( (string) $es_idx ); ?>" class="flex w-full items-center justify-between gap-4 px-6 py-5 text-right cursor-pointer" aria-expanded="<?php echo $es_open ? 'true' : 'false'; ?>">
											<div class="space-y-1">
												<?php if ( '' !== $es_item['group'] ) : ?><span class="inline-block rounded-md bg-emerald-50 px-2.5 py-0.5 text-[11px] font-extrabold text-emerald-800"><?php echo esc_html( $es_item['group'] ); ?></span><?php endif; ?>
												<h3 class="text-[15px] font-black leading-7 text-slate-900 sm:text-[16px]"><?php echo esc_html( $es_item['question'] ); ?></h3>
											</div>
											<span data-faq-icon="<?php echo esc_attr( (string) $es_idx ); ?>" class="grid h-9 w-9 shrink-0 place-items-center rounded-xl transition-transform duration-300 <?php echo $es_open ? 'rotate-45 bg-emerald-800 text-white' : 'bg-emerald-100 text-emerald-800'; ?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-4.5 w-4.5" aria-hidden="true"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg></span>
										</button>
										<div data-faq-body="<?php echo esc_attr( (string) $es_idx ); ?>" class="<?php echo $es_open ? 'block' : 'hidden'; ?> border-t border-slate-100 bg-slate-50/50 px-6 py-5 text-[14px] leading-8 text-slate-600"><?php echo esc_html( $es_item['answer'] ); ?></div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<div data-faq-empty="true" class="hidden rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-12 text-center">
							<p class="text-[14px] font-bold text-slate-600"><?php esc_html_e( 'پرسشی مطابق جستجوی شما یافت نشد.', 'erfansanat' ); ?></p>
							<p class="mt-1.5 text-[12.5px] text-slate-500"><?php esc_html_e( 'عبارت دیگری را امتحان کنید یا با کارشناسان ما تماس بگیرید.', 'erfansanat' ); ?></p>
						</div>
					</div>
				</div>
			</div>
		</section>
	<?php endif; ?>
</main>
<?php
get_footer();
