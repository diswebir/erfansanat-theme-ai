<?php
/**
 * Template Name: پروژه‌های برگزیده
 * Template Post Type: page
 *
 * The featured-projects landing page.
 *
 * Task 7.1 keeps this page separate from the `/projects/` archive: the archive
 * is every project with filters, while this page is the handful the site owner
 * chose. Selection is a real flag — the `featured` custom field, which the page
 * checks per project — with the newest projects as the fallback so the page is
 * never empty on a site that has not flagged anything yet.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

$erfansanat_featured   = get_posts(
	array(
		'post_type'      => 'projects',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'no_found_rows'  => true,
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- Deliberate featured-flag lookup.
			array(
				'key'     => 'featured',
				'value'   => '1',
				'compare' => '=',
			),
		),
	)
);
$erfansanat_is_flagged = is_array( $erfansanat_featured ) && array() !== $erfansanat_featured;

if ( ! $erfansanat_is_flagged ) {
	$erfansanat_featured = get_posts(
		array(
			'post_type'      => 'projects',
			'post_status'    => 'publish',
			'posts_per_page' => 6,
			'no_found_rows'  => true,
		)
	);
}
$erfansanat_featured = is_array( $erfansanat_featured ) ? $erfansanat_featured : array();

get_header();
?>
<section data-featured-hero="true" class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-12 pt-28 lg:pb-14 lg:pt-34">
	<div aria-hidden="true" class="pointer-events-none absolute inset-0">
		<div class="absolute inset-0" style="background-image:radial-gradient(900px 430px at 50% -6%, rgba(0,177,64,0.1), transparent 60%)"></div>
		<div class="light-dot-grid absolute inset-0 opacity-45"></div>
	</div>
	<div class="relative z-10 mx-auto max-w-6xl px-5 text-center lg:px-8">
		<div class="mb-4 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-white px-4 py-1.5 text-[12px] font-extrabold text-emerald-800 shadow-2xs">
			<span class="h-2 w-2 rounded-full bg-emerald-600"></span>
			<span><?php esc_html_e( 'منتخب کارنامهٔ اجرایی', 'erfansanat' ); ?></span>
		</div>
		<h1 class="text-[2rem] font-black leading-[1.3] tracking-[-0.015em] text-slate-950 sm:text-[2.5rem] lg:text-[2.9rem]"><?php esc_html_e( 'پروژه‌های برگزیده عرفان صنعت', 'erfansanat' ); ?></h1>
		<p class="mx-auto mt-4 max-w-[36rem] text-[14.5px] font-medium leading-8 text-slate-600"><?php esc_html_e( 'چند نمونه از پروژه‌هایی که بیشترین بازخورد را از کارفرمایان گرفته‌اند؛ برای دیدن همهٔ پروژه‌ها، آرشیو کامل را ببینید.', 'erfansanat' ); ?></p>
		<div class="mt-6 flex flex-wrap justify-center gap-2.5">
			<a href="<?php echo esc_url( (string) get_post_type_archive_link( 'projects' ) ); ?>" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2.5 text-[12.5px] font-extrabold text-white shadow-sm transition-colors hover:bg-emerald-800">
				<span><?php esc_html_e( 'آرشیو کامل پروژه‌ها', 'erfansanat' ); ?></span>
				<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
			</a>
		</div>
	</div>
	<div aria-hidden="true" class="relative z-10 -mb-px h-10 rounded-t-[2.5rem] bg-white sm:h-12"></div>
</section>

<section class="mx-auto max-w-6xl px-5 py-12 lg:px-8 lg:py-16">
	<?php if ( array() === $erfansanat_featured ) : ?>
		<p class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 px-6 py-12 text-center text-[14px] font-semibold text-slate-600"><?php esc_html_e( 'هنوز پروژه‌ای ثبت نشده است.', 'erfansanat' ); ?></p>
	<?php else : ?>
		<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
			<?php foreach ( $erfansanat_featured as $erfansanat_project ) : ?>
				<?php include get_template_directory() . '/template-parts/cards/card-project.php'; ?>
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
