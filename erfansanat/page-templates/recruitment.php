<?php
/**
 * Template Name: فرصت‌های شغلی
 * Template Post Type: page
 *
 * Recruitment landing page.
 *
 * The page body is the editor's own content. What the template adds is the
 * structure the reference has and a page cannot express on its own: a hero, the
 * open-positions list read from child pages when they exist, and the contact
 * call to action. There is no application form here — the brief's gate for that
 * ("where does the résumé go, who may read it, how long is it kept") is not
 * decided yet, and a form that silently drops a CV is worse than no form.
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

	// Child pages of this one are shown as open positions when the editor adds them.
	$erfansanat_positions = get_pages(
		array(
			'child_of'    => $erfansanat_page_id,
			'post_status' => 'publish',
			'sort_column' => 'menu_order,post_title',
		)
	);
	$erfansanat_positions = is_array( $erfansanat_positions ) ? $erfansanat_positions : array();
	?>
	<section data-page-hero="true" class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-12 pt-28 lg:pb-14 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(900px 430px at 78% -4%, rgba(0,177,64,0.1), transparent 62%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-45"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-5xl px-5 lg:px-8">
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
					<span><?php esc_html_e( 'همکاری با تیم عرفان صنعت', 'erfansanat' ); ?></span>
				</div>
				<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $erfansanat_title ); ?></h1>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-5xl px-5 py-10 lg:px-8 lg:py-12">
		<div class="erfansanat-prose max-w-none">
			<?php echo wp_kses_post( $erfansanat_body ); ?>
		</div>

		<?php if ( array() !== $erfansanat_positions ) : ?>
			<div class="mt-12">
				<h2 class="mb-5 text-xl font-black text-slate-900"><?php esc_html_e( 'موقعیت‌های شغلی باز', 'erfansanat' ); ?></h2>
				<ul class="grid gap-3">
					<?php foreach ( $erfansanat_positions as $erfansanat_position ) : ?>
						<li>
							<a href="<?php echo esc_url( (string) get_permalink( (int) $erfansanat_position->ID ) ); ?>" class="group flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-5 py-4 transition-all hover:border-emerald-300 hover:shadow-sm">
								<span class="text-[14px] font-extrabold text-slate-800 transition-colors group-hover:text-emerald-700"><?php echo esc_html( (string) $erfansanat_position->post_title ); ?></span>
								<span class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-emerald-50 text-emerald-700 transition-colors group-hover:bg-emerald-700 group-hover:text-white">
									<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
								</span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="mt-12 rounded-3xl border border-emerald-200 bg-emerald-50/50 p-6 sm:p-8">
			<h2 class="text-lg font-black text-slate-900"><?php esc_html_e( 'ارسال رزومه', 'erfansanat' ); ?></h2>
			<p class="mt-2 text-[13.5px] leading-8 text-slate-600"><?php esc_html_e( 'برای ارسال رزومه، آن را به نشانی ایمیل واحد منابع انسانی بفرستید و در عنوان ایمیل، نام موقعیت مورد نظر را بنویسید.', 'erfansanat' ); ?></p>
			<a href="mailto:<?php echo esc_attr( SectionParts::email() ); ?>" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-[12.5px] font-extrabold text-white transition-colors hover:bg-emerald-800">
				<span><?php echo esc_html( SectionParts::email() ); ?></span>
			</a>
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
