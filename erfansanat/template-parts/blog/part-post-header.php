<?php
/**
 * Single post header: breadcrumb, category, title, meta and cover image.
 *
 * @package ErfanSanat
 *
 * @var \WP_Post             $erfansanat_post       Post being viewed.
 * @var array<int, array{label: string, url: string}> $erfansanat_crumbs Breadcrumb trail.
 * @var array<int, string>   $erfansanat_categories Category names.
 * @var int                  $erfansanat_reading    Reading time in minutes.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\PostData;
use ErfanSanat\Theme\Frontend\SectionParts;

if ( ! isset( $erfansanat_post ) || ! $erfansanat_post instanceof \WP_Post ) {
	return;
}

$erfansanat_id         = (int) $erfansanat_post->ID;
$erfansanat_crumbs     = isset( $erfansanat_crumbs ) && is_array( $erfansanat_crumbs ) ? $erfansanat_crumbs : array();
$erfansanat_categories = isset( $erfansanat_categories ) && is_array( $erfansanat_categories ) ? $erfansanat_categories : array();
$erfansanat_reading    = isset( $erfansanat_reading ) ? (int) $erfansanat_reading : PostData::reading_time( $erfansanat_id );
$erfansanat_cover      = (string) get_the_post_thumbnail_url( $erfansanat_id, 'full' );
if ( '' === $erfansanat_cover ) {
	$erfansanat_cover = PostData::fallback_thumb( $erfansanat_id );
}
?>
<article <?php post_class( 'mx-auto max-w-6xl px-4 pb-10 pt-6 sm:px-6 lg:px-8' ); ?>>
	<nav aria-label="<?php esc_attr_e( 'مسیر صفحه', 'erfansanat' ); ?>" class="mt-4 inline-flex max-w-full flex-wrap items-center gap-x-1.5 gap-y-1.5 rounded-2xl border border-emerald-100 bg-white px-4 py-2.5 text-[12.5px] font-semibold text-slate-500 shadow-[0_8px_24px_-16px_rgba(5,150,105,0.35)]">
		<?php foreach ( $erfansanat_crumbs as $erfansanat_index => $erfansanat_crumb ) : ?>
			<?php if ( $erfansanat_index > 0 ) : ?>
				<span class="flex min-w-0 items-center gap-1.5">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3 shrink-0 text-slate-400" aria-hidden="true"><polyline points="15 18 9 12 15 6"></polyline></svg>
			<?php endif; ?>
			<?php if ( '' !== (string) $erfansanat_crumb['url'] ) : ?>
				<a href="<?php echo esc_url( (string) $erfansanat_crumb['url'] ); ?>" class="truncate rounded-lg px-1.5 py-0.5 transition-colors hover:bg-emerald-50 hover:text-emerald-700"><?php echo esc_html( (string) $erfansanat_crumb['label'] ); ?></a>
			<?php else : ?>
				<span class="max-w-[18rem] truncate rounded-lg bg-emerald-50 px-2 py-0.5 font-extrabold text-emerald-800"><?php echo esc_html( (string) $erfansanat_crumb['label'] ); ?></span>
			<?php endif; ?>
			<?php if ( $erfansanat_index > 0 ) : ?>
				</span>
			<?php endif; ?>
		<?php endforeach; ?>
	</nav>

	<div class="mt-7 grid items-start gap-8 lg:grid-cols-12 lg:gap-10">
		<div class="lg:col-span-8">
			<?php if ( array() !== $erfansanat_categories ) : ?>
				<div class="mb-3 flex flex-wrap items-center gap-2">
					<?php foreach ( $erfansanat_categories as $erfansanat_category ) : ?>
						<span class="rounded-full bg-emerald-50 px-3 py-1 text-[11.5px] font-extrabold text-emerald-800"><?php echo esc_html( $erfansanat_category ); ?></span>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<h1 class="text-[1.9rem] font-black leading-[1.35] tracking-[-0.015em] text-slate-950 sm:text-[2.4rem] lg:text-[2.7rem] lg:leading-[1.28]"><?php echo esc_html( get_the_title( $erfansanat_id ) ); ?></h1>

			<div class="mt-4 flex flex-wrap items-center gap-3 text-[12.5px] font-bold text-slate-500 tabular-nums">
				<span class="inline-flex items-center gap-1.5">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-emerald-600" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
					<?php echo esc_html( get_the_date( 'j F Y', $erfansanat_id ) ); ?>
				</span>
				<span aria-hidden="true">·</span>
				<span class="inline-flex items-center gap-1.5">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-emerald-600" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
					<?php
					printf(
						/* translators: %s: reading time in minutes. */
						esc_html__( '%s دقیقه مطالعه', 'erfansanat' ),
						esc_html( SectionParts::fa_digits( (string) $erfansanat_reading ) )
					);
					?>
				</span>
			</div>

			<?php if ( '' !== $erfansanat_cover ) : ?>
				<div class="mt-7 overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-900">
					<img src="<?php echo esc_url( $erfansanat_cover ); ?>" alt="<?php echo esc_attr( get_the_title( $erfansanat_id ) ); ?>" class="h-[16rem] w-full object-cover sm:h-[22rem] lg:h-[26rem]" />
				</div>
			<?php endif; ?>
		</div>
	</div>
</article>
