<?php
/**
 * Standard project card (P05-T002). DOM parity with static-mirror/projects.html.
 *
 * data-project-* attributes carry real term/meta values for the client-side
 * filter/sort/search in main.js. Link/image/city come from the DB; optional
 * data (date/rating/client) degrades gracefully without breaking the DOM
 * (AC-03). Rating/client meta keys are filterable for live-site mapping.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Admin\ProjectMetaboxes;
use ErfanSanat\Theme\Core\ViewCounter;

$es_project_id = isset( $args['project_id'] ) ? (int) $args['project_id'] : (int) get_the_ID();
if ( ! $es_project_id || 'projects' !== get_post_type( $es_project_id ) ) {
	return;
}

$es_title = get_the_title( $es_project_id );
$es_link  = (string) get_permalink( $es_project_id );

// Image: featured -> first gallery image -> theme fallback.
$es_thumb_id = (int) get_post_thumbnail_id( $es_project_id );
if ( ! $es_thumb_id ) {
	$es_gallery  = ProjectMetaboxes::get_gallery( $es_project_id );
	$es_thumb_id = isset( $es_gallery[0] ) ? (int) $es_gallery[0] : 0;
}
$es_img = $es_thumb_id ? (string) wp_get_attachment_image_url( $es_thumb_id, 'large' ) : '';
if ( ! $es_img ) {
	$es_img = ES_ASSETS_URI . 'images/light-element.jpg';
}

// Project type terms (primary + pipe-separated list).
$es_type_terms   = get_the_terms( $es_project_id, 'project_type' );
$es_type_names   = ( $es_type_terms && ! is_wp_error( $es_type_terms ) ) ? wp_list_pluck( $es_type_terms, 'name' ) : array();
$es_primary_type = isset( $es_type_names[0] ) ? $es_type_names[0] : '';
$es_types_attr   = implode( '|', $es_type_names );

// City term.
$es_city_terms = get_the_terms( $es_project_id, 'city' );
$es_city       = ( $es_city_terms && ! is_wp_error( $es_city_terms ) ) ? $es_city_terms[0]->name : '';

// Views (legacy counter contract).
$es_views = ViewCounter::get( $es_project_id );

// Optional rating/client (filterable for live-site meta key mapping).
$es_rating = (string) apply_filters( 'es_project_card_rating', (string) get_post_meta( $es_project_id, 'project_rating', true ), $es_project_id );
$es_client = (string) apply_filters( 'es_project_card_client', (string) get_post_meta( $es_project_id, 'project_client', true ), $es_project_id );

// Completion date (Shamsi text).
$es_date = (string) get_post_meta( $es_project_id, 'finish-pro-time', true );

// Search text: title + city + types + client.
$es_search_text = trim( $es_title . ' ' . $es_city . ' ' . implode( ' ', $es_type_names ) . ' ' . $es_client );

// Persian digits for display values.
$es_fa_digits = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_views_fa  = strtr( (string) $es_views, $es_fa_digits );
$es_rating_fa = strtr( $es_rating, $es_fa_digits );
$es_date_fa   = strtr( $es_date, $es_fa_digits );
?>
<div data-project-item="true" data-project-type="<?php echo esc_attr( $es_primary_type ); ?>" data-project-types="<?php echo esc_attr( $es_types_attr ); ?>" data-project-location="<?php echo esc_attr( $es_city ); ?>" data-project-views="<?php echo esc_attr( (string) $es_views ); ?>" data-project-rating="<?php echo esc_attr( '' !== $es_rating ? $es_rating : '0' ); ?>" data-project-date="<?php echo esc_attr( substr( (string) get_post_field( 'post_date', $es_project_id ), 0, 10 ) ); ?>" data-project-search-text="<?php echo esc_attr( $es_search_text ); ?>" class="block">
	<a href="<?php echo esc_url( $es_link ); ?>" class="card-glow group relative block overflow-hidden rounded-[1.5rem] border border-emerald-100 bg-slate-900 shadow-sm">
		<div class="relative h-60 overflow-hidden">
			<img src="<?php echo esc_url( $es_img ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-[1.2s] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110"/>
			<div class="image-overlay absolute inset-0"></div>
			<?php if ( '' !== $es_primary_type ) : ?>
				<span class="absolute right-4 top-4 rounded-full bg-emerald-950/75 px-3.5 py-1.5 text-[11.5px] font-bold text-emerald-100 backdrop-blur-md"><?php echo esc_html( $es_primary_type ); ?></span>
			<?php endif; ?>
			<span class="absolute left-4 top-4 grid h-10 w-10 place-items-center rounded-full border border-white/30 bg-emerald-950/70 backdrop-blur-md transition-all duration-500 group-hover:rotate-45 group-hover:bg-emerald-500"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4.5 w-4.5 text-white" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span>
			<div class="absolute inset-x-0 bottom-0 p-5">
				<h2 class="text-lg font-extrabold leading-7 text-white [text-shadow:0_2px_12px_rgba(0,0,0,.8)]"><?php echo esc_html( $es_title ); ?></h2>
			</div>
		</div>
		<div class="flex flex-wrap items-center justify-between gap-2 bg-white px-5 py-4">
			<?php if ( '' !== $es_city ) : ?>
				<span class="flex items-center gap-1.5 text-[12.5px] font-bold text-slate-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pin h-4 w-4 text-emerald-600" aria-hidden="true"><path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"></path><circle cx="12" cy="10" r="3"></circle></svg><?php echo esc_html( $es_city ); ?></span>
			<?php endif; ?>
			<div class="flex items-center gap-3 text-[11.5px] font-bold text-slate-500">
				<?php if ( '' !== $es_date ) : ?>
					<span class="flex items-center gap-1 text-emerald-800"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-check2 lucide-calendar-check-2 h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M8 2v4"></path><path d="M16 2v4"></path><path d="M21 14V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8"></path><path d="M3 10h18"></path><path d="m16 20 2 2 4-4"></path></svg><?php echo esc_html( $es_date_fa ); ?></span>
				<?php endif; ?>
				<?php if ( '' !== $es_rating ) : ?>
					<span class="flex items-center gap-1 text-amber-600"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-amber-400 text-amber-400" aria-hidden="true"><path d="M11.525 2.295a.53.53 0 0 1 .95 0l2.31 4.679a2.123 2.123 0 0 0 1.595 1.16l5.166.756a.53.53 0 0 1 .294.904l-3.736 3.638a2.123 2.123 0 0 0-.611 1.878l.882 5.14a.53.53 0 0 1-.771.56l-4.618-2.428a2.122 2.122 0 0 0-1.973 0L6.396 21.01a.53.53 0 0 1-.77-.56l.881-5.139a2.122 2.122 0 0 0-.611-1.879L2.16 9.795a.53.53 0 0 1 .294-.906l5.165-.755a2.122 2.122 0 0 0 1.597-1.16z"></path></svg><?php echo esc_html( $es_rating_fa ); ?></span>
				<?php endif; ?>
				<span class="flex items-center gap-1"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye h-3.5 w-3.5 text-slate-400" aria-hidden="true"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path><circle cx="12" cy="12" r="3"></circle></svg><?php echo esc_html( $es_views_fa ); ?></span>
			</div>
		</div>
	</a>
</div>
