<?php
/**
 * Services archive.
 *
 * The reference's `services.html` is one long page that introduces the five
 * lighting services as large alternating rows. The archive renders the same
 * rows from the `services` post type, so adding a service in the admin adds a
 * row here and nothing needs editing.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

get_header();

$erfansanat_services = get_posts(
	array(
		'post_type'      => 'services',
		'post_status'    => 'publish',
		'posts_per_page' => 24,
		'orderby'        => 'menu_order date',
		'order'          => 'ASC',
	)
);
$erfansanat_services = is_array( $erfansanat_services ) ? $erfansanat_services : array();
$erfansanat_count    = count( $erfansanat_services );
$erfansanat_shop_url = function_exists( 'wc_get_page_permalink' ) ? (string) wc_get_page_permalink( 'shop' ) : '';
?>
<section data-services-hero="true" class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-12 pt-28 lg:pb-16 lg:pt-34">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute inset-0" style="background-image:radial-gradient(960px 480px at 76% -6%, rgba(0,177,64,0.1), transparent 62%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-45"></div>
		<div class="absolute -right-24 top-8 h-72 w-72 rounded-full bg-emerald-200/45 blur-[110px]"></div>
	</div>
	<div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
		<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php esc_html_e( 'صفحه اصلی', 'erfansanat' ); ?></a>
			<span class="flex min-w-0 items-center gap-1.5">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 shrink-0 text-slate-400" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
				<span class="max-w-[16rem] truncate rounded-lg bg-emerald-50 px-2 py-0.5 font-extrabold text-emerald-800"><?php esc_html_e( 'خدمات', 'erfansanat' ); ?></span>
			</span>
		</nav>

		<div class="grid items-center gap-10 pb-4 pt-10 lg:grid-cols-[1.05fr_0.95fr]">
			<div>
				<div class="mb-5 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs">
					<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
					<span>
						<?php
						printf(
							/* translators: %s: number of services. */
							esc_html__( '%s خدمت تخصصی نورپردازی', 'erfansanat' ),
							esc_html( SectionParts::fa_digits( (string) $erfansanat_count ) )
						);
						?>
					</span>
				</div>
				<h1 class="text-[2.2rem] font-black leading-[1.28] tracking-[-0.015em] text-slate-950 sm:text-[2.8rem] lg:text-[3.2rem] lg:leading-[1.22]">
					<?php esc_html_e( 'خدمات', 'erfansanat' ); ?><br />
					<span class="bg-gradient-to-l from-emerald-800 via-emerald-600 to-teal-700 bg-clip-text text-transparent"><?php esc_html_e( 'نورپردازی شهری', 'erfansanat' ); ?></span>
				</h1>
				<p class="mt-5 max-w-[36rem] text-[15px] font-medium leading-9 text-slate-600 lg:text-[16px]"><?php esc_html_e( 'از طراحی تا اجرا و تحویل؛ هر خدمت با تیم فنی، تجهیزات تولید کارخانه و گارانتی رسمی ارائه می‌شود.', 'erfansanat' ); ?></p>

				<div class="mt-7 flex flex-wrap gap-2.5">
					<a href="<?php echo esc_url( SectionParts::phone_href() ); ?>" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-[12.5px] font-extrabold text-white shadow-sm transition-colors hover:bg-emerald-800">
						<span><?php esc_html_e( 'مشاوره فنی رایگان', 'erfansanat' ); ?></span>
					</a>
					<a href="<?php echo esc_url( (string) get_post_type_archive_link( 'projects' ) ); ?>" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-[12.5px] font-extrabold text-slate-700 transition-colors hover:border-emerald-300 hover:text-emerald-800"><?php esc_html_e( 'نمونه پروژه‌ها', 'erfansanat' ); ?></a>
				</div>
			</div>
			<div class="relative mx-auto hidden w-full max-w-[26rem] sm:block">
				<div aria-hidden="true" class="absolute left-1/2 top-1/2 h-[70%] w-[70%] -translate-x-1/2 -translate-y-1/2 rounded-full bg-emerald-200/50 blur-[90px]"></div>
				<div class="relative overflow-hidden rounded-[2.5rem] border-4 border-white shadow-[0_40px_80px_-30px_rgba(0,80,40,0.35)]">
					<img src="<?php echo esc_url( SectionParts::asset( 'images/light-element.jpg' ) ); ?>" alt="" class="h-[22rem] w-full object-cover lg:h-[26rem]" />
				</div>
			</div>
		</div>
	</div>
	<div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div>
</section>

<section class="mx-auto max-w-7xl px-5 py-14 lg:px-8">
	<?php if ( array() === $erfansanat_services ) : ?>
		<p class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 px-6 py-12 text-center text-[14px] font-semibold text-slate-600"><?php esc_html_e( 'هنوز خدمتی ثبت نشده است.', 'erfansanat' ); ?></p>
	<?php else : ?>
		<div class="space-y-8">
			<?php foreach ( $erfansanat_services as $erfansanat_index => $erfansanat_service ) : ?>
				<?php
				$erfansanat_service_id    = (int) $erfansanat_service->ID;
				$erfansanat_service_title = (string) get_the_title( $erfansanat_service_id );
				$erfansanat_service_url   = (string) get_permalink( $erfansanat_service_id );
				$erfansanat_service_image = (string) get_the_post_thumbnail_url( $erfansanat_service_id, 'large' );
				if ( '' === $erfansanat_service_image ) {
					$erfansanat_service_image = SectionParts::asset( 0 === $erfansanat_index % 2 ? 'images/light-element.jpg' : 'images/light-tree.jpg' );
				}
				$erfansanat_service_text = trim( wp_strip_all_tags( (string) $erfansanat_service->post_excerpt ) );
				if ( '' === $erfansanat_service_text ) {
					$erfansanat_service_text = wp_trim_words( wp_strip_all_tags( (string) $erfansanat_service->post_content ), 34, '…' );
				}
				?>
				<article class="grid items-center gap-6 overflow-hidden rounded-[1.75rem] border border-slate-200/80 bg-white p-4 shadow-sm sm:p-5 lg:grid-cols-2 lg:gap-10">
					<div class="<?php echo 0 === $erfansanat_index % 2 ? 'lg:order-1' : 'lg:order-2'; ?>">
						<div class="relative overflow-hidden rounded-[1.4rem] bg-slate-900">
							<img src="<?php echo esc_url( $erfansanat_service_image ); ?>" alt="<?php echo esc_attr( $erfansanat_service_title ); ?>" loading="lazy" class="h-56 w-full object-cover transition-transform duration-700 hover:scale-105 sm:h-72" />
							<div class="absolute inset-0 bg-gradient-to-t from-slate-950/45 via-transparent to-transparent"></div>
							<span class="absolute right-4 top-4 grid h-9 w-9 place-items-center rounded-xl bg-emerald-600 text-[13px] font-black text-white shadow-md"><?php echo esc_html( SectionParts::fa_digits( (string) ( $erfansanat_index + 1 ) ) ); ?></span>
						</div>
					</div>
					<div class="<?php echo 0 === $erfansanat_index % 2 ? 'lg:order-2' : 'lg:order-1'; ?> lg:px-4">
						<h2 class="text-[1.5rem] font-black leading-9 text-slate-900 sm:text-[1.7rem]"><?php echo esc_html( $erfansanat_service_title ); ?></h2>
						<?php if ( '' !== $erfansanat_service_text ) : ?>
							<p class="mt-3 text-[14px] leading-8 text-slate-600"><?php echo esc_html( $erfansanat_service_text ); ?></p>
						<?php endif; ?>
						<a href="<?php echo esc_url( $erfansanat_service_url ); ?>" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-[12.5px] font-extrabold text-white shadow-sm transition-colors hover:bg-emerald-800">
							<span><?php esc_html_e( 'جزئیات خدمت', 'erfansanat' ); ?></span>
							<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
						</a>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</section>

<?php
$erfansanat_section_fields = array();
?>
<section id="contact" class="erfansanat-section erfansanat-section--contact-cta" data-section="contact-cta">
	<?php require get_template_directory() . '/template-parts/home/section-contact-cta.php'; ?>
</section>
<?php

get_footer();
