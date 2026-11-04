<?php
/**
 * Blog preview section (P05-T019). DOM parity with static-mirror
 * (id=blog-preview — AC-01). Latest published posts come from WP_Query
 * (AC-02); the count follows the section setting, clamped 1–12 (AC-03).
 * Cards reuse the standard post card (P05-T004). Drafts never appear
 * (SC-02).
 *
 * Receives $es_section and $es_fields from the Renderer.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Menus;

$es_section = isset( $es_section ) && is_array( $es_section ) ? $es_section : array();
$es_fields  = isset( $es_fields ) && is_array( $es_fields ) ? $es_fields : array();

$es_id   = (string) ( $es_section['id'] ?? 'blog-preview' );
$es_type = (string) ( $es_section['type'] ?? 'blog-preview' );

$es_heading    = (string) ( $es_fields['heading'] ?? 'جدیدترین مقالات' );
$es_count      = (int) ( $es_fields['count'] ?? 3 );
$es_count      = max( 1, min( 12, $es_count ) );
$es_eyebrow    = (string) apply_filters( 'es_blog_preview_eyebrow', 'دانشنامه و مجله فنی' );
$es_subheading = (string) apply_filters( 'es_blog_preview_subheading', 'بررسی فنی تجهیزات LED، استانداردهای حفاظتی IP68 و راهنمای خرید المان‌های نوری شهری' );
$es_archive_label = (string) apply_filters( 'es_blog_preview_archive_label', 'مشاهده آرشیو مقالات' );
$es_archive_url   = (string) apply_filters( 'es_blog_preview_archive_url', Menus::page_url( 'blog' ) );

$es_query = new WP_Query( array(
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => $es_count,
	'no_found_rows'       => true,
	'ignore_sticky_posts' => true,
	'orderby'             => 'date',
	'order'               => 'DESC',
) );
?>
<section id="blog-preview" aria-label="ویترین مقالات کاربردی عرفان صنعت" class="es-section es-section--blog mx-auto max-w-[1300px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8" data-es-section="<?php echo esc_attr( $es_id ); ?>" data-es-section-type="<?php echo esc_attr( $es_type ); ?>">
	<div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
		<div class="flex max-w-2xl flex-col gap-3 items-start text-right">
			<div>
				<div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]">
					<span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span>
					<span><?php echo esc_html( $es_eyebrow ); ?></span>
				</div>
			</div>
			<div>
				<h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]"><?php echo esc_html( $es_heading ); ?></h2>
			</div>
			<?php if ( '' !== trim( $es_subheading ) ) : ?>
				<div>
					<p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8"><?php echo esc_html( $es_subheading ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<a href="<?php echo esc_url( $es_archive_url ); ?>" class="group inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200/90 bg-white px-5 py-2.5 text-xs font-extrabold text-slate-800 transition-colors hover:border-emerald-400 hover:text-emerald-800 sm:text-sm">
			<span><?php echo esc_html( $es_archive_label ); ?></span>
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-left h-4 w-4 text-emerald-700 transition-transform group-hover:-translate-x-0.5" aria-hidden="true"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
		</a>
	</div>
	<div class="mt-8 sm:mt-10 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
		<?php if ( $es_query->have_posts() ) : ?>
			<?php while ( $es_query->have_posts() ) : $es_query->the_post(); ?>
				<?php get_template_part( 'template-parts/cards/card-post', null, array( 'post_id' => get_the_ID() ) ); ?>
			<?php endwhile; ?>
		<?php else : ?>
			<p class="col-span-full text-center text-sm font-medium text-slate-500">مقاله‌ای برای نمایش وجود ندارد.</p>
		<?php endif; ?>
	</div>
</section>
<?php
wp_reset_postdata();
