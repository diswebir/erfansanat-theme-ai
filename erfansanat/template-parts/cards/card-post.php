<?php
/**
 * Shared post card for the home blog grid.
 *
 * @package ErfanSanat
 *
 * @var \WP_Post $erfansanat_post Post to render.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Data\PostData;
use ErfanSanat\Theme\Frontend\SectionParts;

if ( ! isset( $erfansanat_post ) || ! $erfansanat_post instanceof \WP_Post ) {
	return;
}

$erfansanat_id    = (int) $erfansanat_post->ID;
$erfansanat_title = get_the_title( $erfansanat_id );
$erfansanat_url   = (string) get_permalink( $erfansanat_id );
$erfansanat_image = (string) get_the_post_thumbnail_url( $erfansanat_id, 'erfansanat-card' );

// A post with no featured image gets one of the theme's own photographs. The
// fixture posts are all in that position, and a coloured card without any
// artwork looks broken next to the product and project cards.
$erfansanat_fallback = PostData::fallback_thumb( $erfansanat_id );
if ( '' === $erfansanat_image ) {
	$erfansanat_image = $erfansanat_fallback;
}

$erfansanat_categories = PostData::categories( $erfansanat_id );
$erfansanat_cat        = (string) ( $erfansanat_categories[0] ?? '' );

// Reading time from the real word count. `str_word_count()` returns 0 for
// Persian text because it only recognises ASCII letters, so the split lives in
// PostData and is shared with the single post template.
$erfansanat_mins = PostData::reading_time( $erfansanat_id );

$erfansanat_excerpt = get_the_excerpt( $erfansanat_post );
?>
<a href="<?php echo esc_url( $erfansanat_url ); ?>" class="card-light group flex h-full flex-col justify-between rounded-2xl p-3.5">
	<div>
		<div class="relative aspect-[4/3] w-full overflow-hidden rounded-xl bg-slate-900">
			<?php if ( '' !== $erfansanat_image ) : ?>
				<img src="<?php echo esc_url( $erfansanat_image ); ?>" alt="<?php echo esc_attr( $erfansanat_title ); ?>" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
			<?php endif; ?>
			<div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent"></div>
		</div>
		<div class="mt-3 flex items-center gap-2 text-[11px] font-bold text-slate-500 tabular-nums">
			<?php if ( '' !== $erfansanat_cat ) : ?>
				<span class="text-emerald-700"><?php echo esc_html( $erfansanat_cat ); ?></span>
				<span>·</span>
			<?php endif; ?>
			<span><?php echo esc_html( get_the_date( 'j F Y', $erfansanat_id ) ); ?></span>
			<span>·</span>
			<span>
				<?php
				printf(
					/* translators: %s: reading time in minutes. */
					esc_html__( '%s دقیقه مطالعه', 'erfansanat' ),
					esc_html( (string) $erfansanat_mins )
				);
				?>
			</span>
		</div>
		<h3 class="mt-1.5 line-clamp-2 text-right text-sm leading-6 font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html( $erfansanat_title ); ?></h3>
		<?php if ( '' !== (string) $erfansanat_excerpt ) : ?>
			<p class="mt-1.5 line-clamp-2 text-right text-xs leading-6 font-medium text-slate-500"><?php echo esc_html( (string) $erfansanat_excerpt ); ?></p>
		<?php endif; ?>
	</div>
	<div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-2.5 text-xs font-extrabold text-emerald-700">
		<span><?php esc_html_e( 'مطالعه مقاله', 'erfansanat' ); ?></span>
		<?php SectionParts::arrow( 'h-3.5 w-3.5' ); ?>
	</div>
</a>
