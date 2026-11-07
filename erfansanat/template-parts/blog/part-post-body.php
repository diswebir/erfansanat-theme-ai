<?php
/**
 * Single post body: the article, the table of contents, the sidebar CTA, tags
 * and the share control.
 *
 * The table of contents is built from the post's own `h2` headings and the
 * matching `id`s are injected by PostData::add_heading_anchors(), so a link in
 * the list always lands on a real heading. No table of contents is printed for
 * a post that has no `h2`s.
 *
 * @package ErfanSanat
 *
 * @var \WP_Post $erfansanat_post Post being viewed.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\PostData;
use ErfanSanat\Theme\Frontend\SectionParts;

if ( ! isset( $erfansanat_post ) || ! $erfansanat_post instanceof \WP_Post ) {
	return;
}

$erfansanat_id   = (int) $erfansanat_post->ID;
$erfansanat_toc  = PostData::toc( $erfansanat_id );
$erfansanat_tags = get_the_tags( $erfansanat_id );
$erfansanat_tags = is_array( $erfansanat_tags ) ? $erfansanat_tags : array();
$erfansanat_body = apply_filters( 'the_content', (string) $erfansanat_post->post_content ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WordPress core filter.
?>
<div class="mx-auto max-w-6xl px-4 pb-16 sm:px-6 lg:px-8">
	<div class="grid items-start gap-10 lg:grid-cols-12">
		<article class="lg:col-span-8">
			<div class="erfansanat-prose max-w-none">
				<?php echo wp_kses_post( $erfansanat_body ); ?>
			</div>

			<div class="mt-12 flex flex-col gap-5 rounded-3xl border border-emerald-100 bg-white p-6 shadow-xs sm:flex-row sm:items-center sm:justify-between">
				<?php if ( array() !== $erfansanat_tags ) : ?>
					<div class="flex flex-wrap items-center gap-2">
						<span class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'برچسب‌ها:', 'erfansanat' ); ?></span>
						<?php foreach ( $erfansanat_tags as $erfansanat_tag ) : ?>
							<a href="<?php echo esc_url( (string) get_tag_link( (int) $erfansanat_tag->term_id ) ); ?>" class="rounded-full bg-emerald-50 px-3 py-1.5 text-[12px] font-bold text-emerald-800 transition-colors hover:bg-emerald-100"><?php echo esc_html( $erfansanat_tag->name ); ?></a>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="text-[13px] font-bold text-slate-700"><?php esc_html_e( 'این مقاله را با همکاران خود به اشتراک بگذارید.', 'erfansanat' ); ?></p>
				<?php endif; ?>
				<button type="button" data-share-btn="true" class="flex cursor-pointer items-center gap-2.5 text-slate-500 transition-colors hover:text-emerald-700">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5" aria-hidden="true"><circle cx="18" cy="5" r="3"></circle><circle cx="6" cy="12" r="3"></circle><circle cx="18" cy="19" r="3"></circle><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"></line><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"></line></svg>
					<span data-share-label="true" class="text-[13px] font-bold"><?php esc_html_e( 'اشتراک‌گذاری مقاله', 'erfansanat' ); ?></span>
				</button>
			</div>
		</article>

		<aside class="flex flex-col gap-6 lg:col-span-4 lg:sticky lg:top-28">
			<?php if ( array() !== $erfansanat_toc ) : ?>
				<nav aria-label="<?php esc_attr_e( 'سرفصل‌های مقاله', 'erfansanat' ); ?>" class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-xs">
					<h2 class="mb-4 flex items-center gap-2 text-[15px] font-extrabold text-slate-900">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4.5 w-4.5 text-emerald-700" aria-hidden="true"><path d="M3 5h.01M3 12h.01M3 19h.01M8 5h13M8 12h13M8 19h13"></path></svg>
						<?php esc_html_e( 'سرفصل‌های مقاله', 'erfansanat' ); ?>
					</h2>
					<ul class="space-y-2.5 border-r border-emerald-100 pr-4">
						<?php foreach ( $erfansanat_toc as $erfansanat_entry ) : ?>
							<li>
								<a href="#<?php echo esc_attr( $erfansanat_entry['id'] ); ?>" class="block text-[13px] font-medium leading-6 text-slate-600 transition-colors hover:text-emerald-700"><?php echo esc_html( $erfansanat_entry['text'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>

			<div class="relative overflow-hidden rounded-3xl border border-emerald-200 bg-gradient-to-br from-emerald-50 via-white to-teal-50/70 p-6 text-center shadow-sm">
				<div aria-hidden="true" class="absolute -top-12 left-1/2 h-32 w-32 -translate-x-1/2 rounded-full bg-emerald-200/40 blur-[50px]"></div>
				<h2 class="relative text-[15.5px] font-extrabold text-slate-900"><?php esc_html_e( 'مشاوره فنی رایگان پروژه', 'erfansanat' ); ?></h2>
				<p class="relative mt-2.5 text-[13px] leading-7 text-slate-600"><?php esc_html_e( 'برای انتخاب تجهیز مناسب پروژه، کارشناسان فنی ما در کنار شما هستند.', 'erfansanat' ); ?></p>
				<a href="<?php echo esc_url( SectionParts::phone_href() ); ?>" class="relative mt-4 inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-700 px-4 py-3 text-[13px] font-black text-white shadow-lg transition-colors hover:bg-emerald-800">
					<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
					<span><?php echo esc_html( SectionParts::phone() ); ?></span>
				</a>
			</div>
		</aside>
	</div>
</div>
