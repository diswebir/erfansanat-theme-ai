<?php
/**
 * Recruitment page (P07-T025). Reference: static-mirror/recruitment.html.
 *
 * The 4 organizational tabs (R&D / project / accounting / IT) and their job
 * cards come from editable repeaters (PagesTab::recruitment()). Panels carry
 * BOTH the reference data-recruit-tab-panel and the data-recruit-panel hook
 * that the immutable main.js queries, so tab switching works unchanged.
 * Button state classes follow the main.js toggle contract.
 * The application form is presentation-only until Phase 8: no action/method,
 * no named inputs, visible interim note — nothing can be submitted or stored.
 * The global contact CTA comes from the footer.
 *
 * Template Name: فرصت‌های شغلی و استخدام
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Options\Tabs\PagesTab;
use ErfanSanat\Theme\Options\Tabs\FooterTab;

get_header();

$es_heading = (string) es_option( 'recruit_heading' );
if ( '' === trim( $es_heading ) ) {
	$es_heading = 'فرصت‌های شغلی و استخدام';
}
$es_intro   = (string) es_option( 'recruit_intro' );
$es_data    = PagesTab::recruitment();
$es_tabs    = $es_data['tabs'];
$es_jobs    = $es_data['jobs'];
$es_contact = FooterTab::contact();
$es_first   = $es_tabs ? (string) $es_tabs[0]['key'] : '';

$es_input_cls = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-[13.5px] text-slate-800 focus:border-emerald-500 focus:bg-white focus:outline-none';
?>
<main class="min-h-screen bg-[#f8faf9]">
	<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-10 pt-28 lg:pb-12 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0"><div class="absolute inset-0" style="background-image:radial-gradient(900px 420px at 80% 0%, rgba(0,177,64,0.09), transparent 62%)"></div><div class="light-dot-grid absolute inset-0 opacity-45"></div><div class="absolute -left-24 top-10 h-64 w-64 rounded-full bg-emerald-200/40 blur-[100px]"></div></div>
		<div class="relative z-10 mx-auto max-w-[1300px] px-5 lg:px-8">
			<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl px-4 py-2.5 text-[12.5px] font-semibold backdrop-blur border border-emerald-100 bg-white text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="flex shrink-0 items-center gap-1.5 rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-3.5 w-3.5" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></span><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a><span class="flex min-w-0 items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left h-3.5 w-3.5 shrink-0 text-slate-300" aria-hidden="true"><path d="m15 18-6-6 6-6"></path></svg><span class="max-w-[16rem] truncate rounded-lg px-2 py-0.5 font-extrabold bg-emerald-50 text-emerald-800"><?php echo esc_html( $es_heading ); ?></span></span></nav>
			<div class="pt-6">
				<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs"><span class="h-2 w-2 rounded-full bg-emerald-600"></span><span><?php esc_html_e( 'همکاری با خانواده بزرگ عرفان صنعت اصفهان', 'erfansanat' ); ?></span></div>
				<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $es_heading ); ?> <span class="text-emerald-700"><?php esc_html_e( 'در کارخانه دانش‌بنیان', 'erfansanat' ); ?></span></h1>
				<?php if ( '' !== trim( $es_intro ) ) : ?><p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]"><?php echo esc_html( $es_intro ); ?></p><?php endif; ?>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-7xl px-5 py-14 lg:px-8 lg:py-20" data-recruit-tabs="true">
		<div>
			<div class="mb-10 flex flex-wrap items-center justify-between gap-4">
				<div><h2 class="text-2xl font-black text-slate-950"><?php esc_html_e( 'واحدهای سازمانی و موقعیت‌های شغلی باز', 'erfansanat' ); ?></h2><p class="mt-1.5 text-[13.5px] font-medium text-slate-600"><?php esc_html_e( 'واحد سازمانی مورد نظر خود را انتخاب کنید تا شرح وظایف و مهارت‌های مورد نیاز نمایش داده شود:', 'erfansanat' ); ?></p></div>
				<?php if ( $es_tabs ) : ?>
					<div class="flex flex-wrap gap-2 rounded-2xl border border-slate-200 bg-white p-1.5 shadow-2xs" role="tablist" aria-label="<?php esc_attr_e( 'واحدهای سازمانی', 'erfansanat' ); ?>">
						<?php foreach ( $es_tabs as $es_i => $es_tab ) : ?>
							<?php $es_active = ( 0 === $es_i ); ?>
							<button type="button" data-recruit-tab-btn="<?php echo esc_attr( $es_tab['key'] ); ?>" class="rounded-xl px-4 py-2.5 text-[12.5px] font-extrabold transition-all cursor-pointer whitespace-nowrap <?php echo $es_active ? 'bg-emerald-700 text-white shadow-sm' : 'border border-slate-200 bg-slate-50 text-slate-700 hover:border-emerald-300 hover:text-emerald-800'; ?>"><?php echo esc_html( $es_tab['label'] ); ?></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<?php foreach ( $es_tabs as $es_tab ) : ?>
				<?php $es_visible = ( $es_tab['key'] === $es_first ); ?>
				<div data-recruit-tab-panel="<?php echo esc_attr( $es_tab['key'] ); ?>" data-recruit-panel="<?php echo esc_attr( $es_tab['key'] ); ?>" class="<?php echo $es_visible ? 'block' : 'hidden'; ?>">
					<div class="mb-8 rounded-2xl border border-emerald-100 bg-emerald-50/50 p-6">
						<div class="flex flex-wrap items-center justify-between gap-3">
							<div>
								<?php if ( '' !== $es_tab['badge'] ) : ?><span class="rounded-lg bg-emerald-700 px-3 py-1 text-[11px] font-extrabold text-white"><?php echo esc_html( $es_tab['badge'] ); ?></span><?php endif; ?>
								<h3 class="mt-2.5 text-xl font-black text-slate-900"><?php echo esc_html( '' !== $es_tab['title'] ? $es_tab['title'] : $es_tab['label'] ); ?></h3>
							</div>
						</div>
						<?php if ( '' !== $es_tab['description'] ) : ?><p class="mt-2.5 text-[14px] leading-8 text-slate-600"><?php echo esc_html( $es_tab['description'] ); ?></p><?php endif; ?>
					</div>
					<div class="grid gap-6 md:grid-cols-2">
						<?php foreach ( $es_jobs as $es_job ) : ?>
							<?php if ( $es_job['tab'] !== $es_tab['key'] ) { continue; } ?>
							<article class="card-light flex flex-col justify-between rounded-[1.75rem] border border-slate-200/90 bg-white p-6">
								<div>
									<div class="mb-4 flex flex-wrap items-center gap-2 text-[11.5px] font-bold">
										<?php if ( '' !== $es_job['type'] ) : ?><span class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-2.5 py-1 text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-briefcase h-3.5 w-3.5" aria-hidden="true"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path><rect width="20" height="14" x="2" y="6" rx="2"></rect></svg><?php echo esc_html( $es_job['type'] ); ?></span><?php endif; ?>
										<?php if ( '' !== $es_job['experience'] ) : ?><span class="inline-flex items-center gap-1 rounded-lg bg-slate-100 px-2.5 py-1 text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock h-3.5 w-3.5" aria-hidden="true"><path d="M12 6v6l4 2"></path><circle cx="12" cy="12" r="10"></circle></svg><?php echo esc_html( $es_job['experience'] ); ?></span><?php endif; ?>
									</div>
									<h4 class="text-[17px] font-black text-slate-900"><?php echo esc_html( $es_job['title'] ); ?></h4>
									<?php if ( '' !== $es_job['location'] ) : ?><div class="mt-2 flex items-center gap-1.5 text-[12.5px] font-semibold text-slate-500"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg><span><?php echo esc_html( $es_job['location'] ); ?></span></div><?php endif; ?>
									<?php
									$es_skills = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $es_job['skills'] ) ), static function ( $l ) { return '' !== $l; } ) );
									if ( $es_skills ) :
										?>
										<div class="mt-5 border-t border-slate-100 pt-4">
											<div class="mb-2.5 text-[12.5px] font-extrabold text-slate-800"><?php esc_html_e( 'مهارت‌ها و شرایط احراز:', 'erfansanat' ); ?></div>
											<ul class="space-y-2.5">
												<?php foreach ( $es_skills as $es_skill ) : ?>
													<li class="flex items-start gap-2 text-[12.5px] font-medium leading-6 text-slate-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check mt-1 h-4 w-4 shrink-0 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg><span><?php echo esc_html( $es_skill ); ?></span></li>
												<?php endforeach; ?>
											</ul>
										</div>
									<?php endif; ?>
								</div>
								<a href="#apply-form" class="mt-6 inline-flex items-center justify-center rounded-xl bg-emerald-50 px-4 py-3 text-[13px] font-extrabold text-emerald-900 transition-colors hover:bg-emerald-700 hover:text-white"><?php esc_html_e( 'ارسال رزومه برای این موقعیت', 'erfansanat' ); ?></a>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<div class="mt-16">
			<div id="apply-form" class="scroll-mt-28 rounded-[2rem] border border-slate-200 bg-white p-7 shadow-[0_24px_60px_-28px_rgba(15,23,42,0.10)] sm:p-10">
				<div class="grid gap-10 lg:grid-cols-12">
					<div class="space-y-4 lg:col-span-5">
						<span class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-50 px-3.5 py-1.5 text-[12px] font-extrabold text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-building2 lucide-building-2 h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M10 12h4"></path><path d="M10 8h4"></path><path d="M14 21v-3a2 2 0 0 0-4 0v3"></path><path d="M6 10H4a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-2"></path><path d="M6 21V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v16"></path></svg><?php esc_html_e( 'واحد منابع انسانی عرفان صنعت', 'erfansanat' ); ?></span>
						<h2 class="text-xl font-black text-slate-950 sm:text-2xl"><?php esc_html_e( 'فرم ثبت درخواست همکاری و ارسال رزومه', 'erfansanat' ); ?></h2>
						<p class="text-[14px] leading-8 text-slate-600"><?php esc_html_e( 'مشخصات فردی، واحد سازمانی مورد علاقه و خلاصه سوابق کاری خود را ثبت نمایید. کارشناسان منابع انسانی پس از بررسی، جهت هماهنگی جلسه مصاحبه حضوری با شما تماس خواهند گرفت.', 'erfansanat' ); ?></p>
						<p class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-[12.5px] font-bold leading-6 text-amber-800" data-recruit-note="true"><?php esc_html_e( 'ثبت آنلاین درخواست استخدام فعال است؛ رزومه را در فرم زیر بارگذاری کنید یا با واحد منابع انسانی هماهنگ نمایید.', 'erfansanat' ); ?></p>
						<div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-[12.5px] leading-7 text-slate-600">
							<div><strong class="text-slate-900"><?php esc_html_e( 'محل کارخانه:', 'erfansanat' ); ?></strong> <?php echo esc_html( $es_contact['address'] ); ?></div>
							<div class="mt-1"><strong class="text-slate-900"><?php esc_html_e( 'ساعات کاری:', 'erfansanat' ); ?></strong> <?php esc_html_e( 'شنبه تا پنجشنبه — ۷ صبح تا ۴:۳۰ بعدازظهر', 'erfansanat' ); ?></div>
						</div>
					</div>
					<div class="lg:col-span-7">
						<?php get_template_part( 'template-parts/forms/recruitment', null, array( 'units' => $es_tabs ) ); // P08-T015: real handler (dependency recorded). ?>
					</div>
				</div>
			</div>
		</div>
	</section>
</main>
<?php
get_footer();
