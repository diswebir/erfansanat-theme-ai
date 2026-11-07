<?php
/**
 * Template Name: تماس با ما
 * Template Post Type: page
 *
 * The contact page. The form is a real POST endpoint, so it is the same form on
 * any page; this template adds the hero, the contact details and the map
 * placeholder around it.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

get_header();

while ( have_posts() ) :
	the_post();

	$erfansanat_page_id = (int) get_the_ID();
	$erfansanat_title   = (string) get_the_title( $erfansanat_page_id );
	$erfansanat_body    = apply_filters( 'the_content', (string) get_post_field( 'post_content', $erfansanat_page_id ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core filter.
	?>
	<section data-page-hero="true" class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-12 pt-28 lg:pb-14 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(900px 430px at 76% -6%, rgba(0,177,64,0.1), transparent 62%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-45"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-6xl px-5 lg:px-8">
			<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="truncate rounded-lg px-1.5 py-0.5 hover:bg-emerald-50 hover:text-emerald-700"><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a>
				<span class="flex min-w-0 items-center gap-1.5">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 shrink-0 text-slate-400" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
					<span class="max-w-[16rem] truncate rounded-lg bg-emerald-50 px-2 py-0.5 font-extrabold text-emerald-800"><?php echo esc_html( $erfansanat_title ); ?></span>
				</span>
			</nav>
			<div class="pt-7">
				<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs">
					<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
					<span><?php esc_html_e( 'پاسخگویی در ساعات کاری', 'erfansanat' ); ?></span>
				</div>
				<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $erfansanat_title ); ?></h1>
				<p class="mt-4 max-w-2xl text-[14.5px] font-medium leading-8 text-slate-600"><?php esc_html_e( 'برای مشاورهٔ فنی، استعلام قیمت یا بازدید از کارخانه، فرم زیر را پر کنید یا مستقیم تماس بگیرید.', 'erfansanat' ); ?></p>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-6xl px-5 py-10 lg:px-8 lg:py-12">
		<div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-10">
			<div class="lg:col-span-7">
				<?php if ( '' !== trim( wp_strip_all_tags( $erfansanat_body ) ) ) : ?>
					<div class="erfansanat-prose mb-6 max-w-none"><?php echo wp_kses_post( $erfansanat_body ); ?></div>
				<?php endif; ?>
				<?php $erfansanat_form_kind = 'contact'; ?>
				<?php require get_template_directory() . '/template-parts/forms/part-contact-form.php'; ?>
			</div>

			<aside class="lg:col-span-5">
				<div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-2xs">
					<h2 class="text-[15px] font-black text-slate-900"><?php esc_html_e( 'راه‌های تماس', 'erfansanat' ); ?></h2>
					<ul class="mt-4 space-y-3 text-[13px] font-semibold text-slate-700">
						<li class="flex items-start gap-3 rounded-2xl border border-slate-200/80 bg-slate-50/60 p-3.5">
							<span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-emerald-100 text-emerald-800"><?php SectionParts::icon( 'support', 'h-4 w-4' ); ?></span>
							<span><span class="block text-[11px] font-bold text-slate-500"><?php esc_html_e( 'تلفن', 'erfansanat' ); ?></span>
								<a href="<?php echo esc_url( SectionParts::phone_href() ); ?>" dir="ltr" class="mt-0.5 block font-black text-emerald-800"><?php echo esc_html( SectionParts::phone() ); ?></a>
							</span>
						</li>
						<li class="flex items-start gap-3 rounded-2xl border border-slate-200/80 bg-slate-50/60 p-3.5">
							<span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-emerald-100 text-emerald-800"><?php SectionParts::icon( 'badge', 'h-4 w-4' ); ?></span>
							<span><span class="block text-[11px] font-bold text-slate-500"><?php esc_html_e( 'ایمیل', 'erfansanat' ); ?></span>
								<a href="mailto:<?php echo esc_attr( SectionParts::email() ); ?>" dir="ltr" class="mt-0.5 block font-black text-emerald-800"><?php echo esc_html( SectionParts::email() ); ?></a>
							</span>
						</li>
					</ul>
				</div>
			</aside>
		</div>
	</section>

	<?php
	$erfansanat_section_fields = array();
	?>
	<section id="contact" class="erfansanat-section erfansanat-section--contact-cta" data-section="contact-cta">
		<?php require get_template_directory() . '/template-parts/home/section-contact-cta.php'; ?>
	</section>
	<?php
endwhile;

get_footer();
