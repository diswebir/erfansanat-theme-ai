<?php
/**
 * Single service.
 *
 * Mirrors `services/*.html`: hero, the service description, the execution path,
 * related projects and the other services. The related projects come from the
 * `projects` post type filtered by the project type that shares the service's
 * slug when one exists, and fall back to the newest projects otherwise.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

get_header();

while ( have_posts() ) :
	the_post();

	$erfansanat_service    = get_post();
	$erfansanat_service_id = $erfansanat_service instanceof \WP_Post ? (int) $erfansanat_service->ID : 0;
	$erfansanat_title      = (string) get_the_title( $erfansanat_service_id );
	$erfansanat_body       = trim( (string) apply_filters( 'the_content', (string) $erfansanat_service->post_content ) ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core filter.
	$erfansanat_lead       = trim( wp_strip_all_tags( (string) $erfansanat_service->post_excerpt ) );
	$erfansanat_cover      = (string) get_the_post_thumbnail_url( $erfansanat_service_id, 'full' );
	$erfansanat_other      = get_posts(
		array(
			'post_type'      => 'services',
			'post_status'    => 'publish',
			'posts_per_page' => 5,
			'post__not_in'   => array( $erfansanat_service_id ),
			'orderby'        => 'menu_order date',
			'order'          => 'ASC',
		)
	);
	$erfansanat_other      = is_array( $erfansanat_other ) ? $erfansanat_other : array();

	// Related projects: the project type whose slug matches this service wins.
	$erfansanat_slug    = (string) $erfansanat_service->post_name;
	$erfansanat_type    = get_term_by( 'slug', $erfansanat_slug, 'project_type' );
	$erfansanat_related = array();
	$erfansanat_args    = array(
		'post_type'      => 'projects',
		'post_status'    => 'publish',
		'posts_per_page' => 3,
		'no_found_rows'  => true,
	);
	if ( $erfansanat_type instanceof \WP_Term ) {
		$erfansanat_args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Taxonomy filter, not meta.
			array(
				'taxonomy' => 'project_type',
				'field'    => 'term_id',
				'terms'    => array( (int) $erfansanat_type->term_id ),
			),
		);
	}
	$erfansanat_related = get_posts( $erfansanat_args );
	$erfansanat_related = is_array( $erfansanat_related ) ? $erfansanat_related : array();

	$erfansanat_steps = array(
		array(
			'title' => __( 'بازدید میدانی و برداشت فنی', 'erfansanat' ),
			'text'  => __( 'بررسی محل اجرا، سازه و شبکهٔ برق، و ثبت اندازه‌های دقیق پیش از طراحی.', 'erfansanat' ),
		),
		array(
			'title' => __( 'طراحی و ارائهٔ طرح نوری', 'erfansanat' ),
			'text'  => __( 'طرح سه‌بعدی، شبیه‌سازی نور و انتخاب تجهیزات متناسب با بودجه و کاربری.', 'erfansanat' ),
		),
		array(
			'title' => __( 'تولید، نصب و تحویل', 'erfansanat' ),
			'text'  => __( 'ساخت تجهیزات در کارخانه، نصب توسط تیم اجرایی و تحویل با آموزش بهره‌برداری.', 'erfansanat' ),
		),
	);
	?>
	<section data-service-hero="true" class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-12 pt-28 lg:pb-14 lg:pt-34">
		<div aria-hidden="true" class="pointer-events-none absolute inset-0">
			<div class="absolute inset-0" style="background-image:radial-gradient(900px 430px at 78% -4%, rgba(0,177,64,0.1), transparent 62%)"></div>
			<div class="light-dot-grid absolute inset-0 opacity-45"></div>
		</div>
		<div class="relative z-10 mx-auto max-w-6xl px-5 lg:px-8">
			<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="truncate rounded-lg px-1.5 py-0.5 hover:bg-emerald-50 hover:text-emerald-700"><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a>
				<span class="flex min-w-0 items-center gap-1.5">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 shrink-0 text-slate-400" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
					<a href="<?php echo esc_url( (string) get_post_type_archive_link( 'services' ) ); ?>" class="truncate rounded-lg px-1.5 py-0.5 hover:bg-emerald-50 hover:text-emerald-700"><?php esc_html_e( 'خدمات', 'erfansanat' ); ?></a>
				</span>
				<span class="flex min-w-0 items-center gap-1.5">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 shrink-0 text-slate-400" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
					<span class="max-w-[16rem] truncate rounded-lg bg-emerald-50 px-2 py-0.5 font-extrabold text-emerald-800"><?php echo esc_html( $erfansanat_title ); ?></span>
				</span>
			</nav>

			<div class="pt-7">
				<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs">
					<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
					<span><?php esc_html_e( 'خدمت تخصصی عرفان صنعت', 'erfansanat' ); ?></span>
				</div>
				<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem] lg:leading-[1.24]"><?php echo esc_html( $erfansanat_title ); ?></h1>
				<?php if ( '' !== $erfansanat_lead ) : ?>
					<p class="mt-4 max-w-3xl border-r-2 border-emerald-500/60 pr-4 text-[14.5px] font-medium leading-8 text-slate-600 lg:text-[15.5px]"><?php echo esc_html( $erfansanat_lead ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="mx-auto max-w-6xl px-5 py-10 lg:px-8 lg:py-12">
		<div class="grid items-start gap-8 lg:grid-cols-12 lg:gap-10">
			<div class="lg:col-span-8">
				<?php if ( '' !== $erfansanat_cover ) : ?>
					<div class="mb-8 overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-900">
						<img src="<?php echo esc_url( $erfansanat_cover ); ?>" alt="<?php echo esc_attr( $erfansanat_title ); ?>" class="h-[16rem] w-full object-cover sm:h-[22rem]" />
					</div>
				<?php endif; ?>

				<?php if ( '' !== $erfansanat_body ) : ?>
					<div class="erfansanat-prose max-w-none">
						<?php echo wp_kses_post( $erfansanat_body ); ?>
					</div>
				<?php else : ?>
					<p class="text-[14.5px] leading-8 text-slate-600"><?php esc_html_e( 'توضیحات تکمیلی این خدمت هنوز ثبت نشده است. برای دریافت جزئیات فنی و قیمت با واحد فروش تماس بگیرید.', 'erfansanat' ); ?></p>
				<?php endif; ?>
			</div>

			<aside class="lg:col-span-4 lg:sticky lg:top-28">
				<div class="relative overflow-hidden rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-teal-50/70 p-6 text-center shadow-sm">
					<div aria-hidden="true" class="absolute -top-12 left-1/2 h-32 w-32 -translate-x-1/2 rounded-full bg-emerald-200/40 blur-[50px]"></div>
					<h2 class="relative text-[15.5px] font-extrabold text-slate-900"><?php esc_html_e( 'استعلام قیمت و مشاوره', 'erfansanat' ); ?></h2>
					<p class="relative mt-2.5 text-[13px] leading-7 text-slate-600"><?php esc_html_e( 'برای دریافت برآورد هزینهٔ این خدمت، کارشناسان فنی ما آمادهٔ پاسخگویی هستند.', 'erfansanat' ); ?></p>
					<a href="<?php echo esc_url( SectionParts::phone_href() ); ?>" class="relative mt-4 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-4 py-3 text-[13px] font-black text-white shadow-lg transition-colors hover:bg-emerald-800">
						<span><?php echo esc_html( SectionParts::phone() ); ?></span>
					</a>
				</div>
			</aside>
		</div>
	</section>

	<section aria-labelledby="service-process-title" class="border-t border-slate-200/80 bg-white py-14">
		<div class="mx-auto max-w-6xl px-5 lg:px-8">
			<h2 id="service-process-title" class="mb-6 text-xl font-black text-slate-900 sm:text-2xl"><?php esc_html_e( 'مسیر اجرای خدمت', 'erfansanat' ); ?></h2>
			<ol class="grid gap-5 sm:grid-cols-3">
				<?php foreach ( $erfansanat_steps as $erfansanat_step_index => $erfansanat_step ) : ?>
					<li class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
						<span class="mb-3 grid h-9 w-9 place-items-center rounded-xl bg-emerald-100 text-sm font-black text-emerald-800"><?php echo esc_html( SectionParts::fa_digits( (string) ( $erfansanat_step_index + 1 ) ) ); ?></span>
						<h3 class="text-sm font-extrabold text-slate-900"><?php echo esc_html( (string) $erfansanat_step['title'] ); ?></h3>
						<p class="mt-2 text-[12.5px] leading-7 text-slate-600"><?php echo esc_html( (string) $erfansanat_step['text'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>
	</section>

	<?php if ( array() !== $erfansanat_related ) : ?>
		<section aria-labelledby="service-projects-title" class="mx-auto max-w-6xl px-5 py-14 lg:px-8">
			<div class="mb-6 flex flex-wrap items-end justify-between gap-3">
				<h2 id="service-projects-title" class="text-xl font-black text-slate-900 sm:text-2xl"><?php esc_html_e( 'نمونه پروژه‌های مرتبط', 'erfansanat' ); ?></h2>
				<a href="<?php echo esc_url( (string) get_post_type_archive_link( 'projects' ) ); ?>" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-white px-3.5 py-2 text-[12px] font-extrabold text-emerald-800 transition-colors hover:bg-emerald-50">
					<span><?php esc_html_e( 'همه پروژه‌ها', 'erfansanat' ); ?></span>
					<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
				</a>
			</div>
			<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
				<?php foreach ( $erfansanat_related as $erfansanat_project ) : ?>
					<?php include get_template_directory() . '/template-parts/cards/card-project.php'; ?>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( array() !== $erfansanat_other ) : ?>
		<section aria-labelledby="service-other-title" class="border-t border-slate-200/80 bg-slate-50/60 py-14">
			<div class="mx-auto max-w-6xl px-5 lg:px-8">
				<h2 id="service-other-title" class="mb-6 text-xl font-black text-slate-900 sm:text-2xl"><?php esc_html_e( 'سایر خدمات نورپردازی شهری', 'erfansanat' ); ?></h2>
				<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
					<?php foreach ( $erfansanat_other as $erfansanat_item ) : ?>
						<a href="<?php echo esc_url( (string) get_permalink( (int) $erfansanat_item->ID ) ); ?>" class="group flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-5 py-4 transition-all hover:border-emerald-300 hover:shadow-sm">
							<span class="text-[13px] font-extrabold text-slate-800 transition-colors group-hover:text-emerald-700"><?php echo esc_html( (string) get_the_title( (int) $erfansanat_item->ID ) ); ?></span>
							<span class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-emerald-50 text-emerald-700 transition-colors group-hover:bg-emerald-700 group-hover:text-white">
								<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
							</span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php
	$erfansanat_section_fields = array();
	?>
	<section id="contact" class="erfansanat-section erfansanat-section--contact-cta" data-section="contact-cta">
		<?php require get_template_directory() . '/template-parts/home/section-contact-cta.php'; ?>
	</section>
	<?php

endwhile;

get_footer();
