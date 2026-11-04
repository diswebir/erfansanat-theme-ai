<?php
/**
 * Shared project card.
 *
 * @package ErfanSanat
 *
 * @var \WP_Post $erfansanat_project Project post.
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

if ( ! isset( $erfansanat_project ) || ! $erfansanat_project instanceof \WP_Post ) {
	return;
}

$erfansanat_id    = (int) $erfansanat_project->ID;
$erfansanat_title = get_the_title( $erfansanat_id );
$erfansanat_url   = (string) get_permalink( $erfansanat_id );
$erfansanat_image = (string) get_the_post_thumbnail_url( $erfansanat_id, 'erfansanat-card' );

// City and type come from the real taxonomies, already primed by Queries.
$erfansanat_cities = get_the_terms( $erfansanat_id, 'city' );
$erfansanat_types  = get_the_terms( $erfansanat_id, 'project_type' );
$erfansanat_city   = is_array( $erfansanat_cities ) && array() !== $erfansanat_cities ? (string) $erfansanat_cities[0]->name : '';
$erfansanat_type   = is_array( $erfansanat_types ) && array() !== $erfansanat_types ? (string) $erfansanat_types[0]->name : '';

// The Jalali completion year is stored by the project metabox.
$erfansanat_date = (string) get_post_meta( $erfansanat_id, 'finish-pro-time', true );
$erfansanat_year = '';
if ( '' !== $erfansanat_date && preg_match( '/^(\d{4})/', $erfansanat_date, $erfansanat_match ) ) {
	$erfansanat_year = $erfansanat_match[1];
}
?>
<a href="<?php echo esc_url( $erfansanat_url ); ?>" class="card-light group flex h-full flex-col justify-between rounded-2xl p-3">
	<div>
		<div class="relative aspect-[4/3] w-full overflow-hidden rounded-xl bg-slate-900">
			<?php if ( '' !== $erfansanat_image ) : ?>
				<img src="<?php echo esc_url( $erfansanat_image ); ?>" alt="<?php echo esc_attr( $erfansanat_title ); ?>" loading="lazy" decoding="async" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
			<?php endif; ?>
			<div class="absolute inset-0 bg-gradient-to-t from-slate-950/65 via-transparent to-transparent"></div>
			<?php if ( '' !== $erfansanat_city ) : ?>
				<span class="absolute bottom-2.5 right-2.5 rounded-md bg-slate-950/55 px-2 py-0.5 text-[10.5px] font-extrabold text-white backdrop-blur-xs"><?php echo esc_html( $erfansanat_city ); ?></span>
			<?php endif; ?>
		</div>
		<div class="mt-3 px-1">
			<?php if ( '' !== $erfansanat_type || '' !== $erfansanat_year ) : ?>
				<div class="text-[11px] font-bold text-emerald-700 tabular-nums">
					<?php
					$erfansanat_meta = array_filter(
						array(
							$erfansanat_type,
							'' !== $erfansanat_year ? sprintf(
								/* translators: %s: Jalali year. */
								__( 'سال %s', 'erfansanat' ),
								$erfansanat_year
							) : '',
						)
					);
					echo esc_html( implode( ' · ', $erfansanat_meta ) );
					?>
				</div>
			<?php endif; ?>
			<h4 class="mt-1 line-clamp-2 min-h-[2.5rem] text-right text-xs sm:text-[13px] leading-5 font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html( $erfansanat_title ); ?></h4>
		</div>
	</div>
	<div class="mt-3 flex items-center justify-between border-t border-slate-100 px-1 pt-2.5 text-[11px] font-bold text-slate-500 group-hover:text-emerald-700">
		<span><?php esc_html_e( 'مشاهده جزئیات پروژه', 'erfansanat' ); ?></span>
		<?php SectionParts::arrow( 'h-3 w-3' ); ?>
	</div>
</a>
