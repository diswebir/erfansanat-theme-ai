<?php
/**
 * Standard post/article card (P05-T004). DOM parity with static-mirror/blog.html.
 *
 * Image/category/date/excerpt/author come from the DB. The date uses
 * get_the_date() so WP-Parsidate can convert it to Shamsi (AC-02); the excerpt
 * is escaped and length-controlled (AC-03). Missing featured image falls back
 * to a theme asset (SC-02).
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Core\Calendar;

$es_post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : (int) get_the_ID();
if ( ! $es_post_id || 'post' !== get_post_type( $es_post_id ) ) {
	return;
}

$es_archive_item = ! empty( $args['archive_item'] );
$es_search_result = ! empty( $args['search_result'] );
$es_title = get_the_title( $es_post_id );
$es_link  = (string) get_permalink( $es_post_id );

// Image: featured -> theme fallback (SC-02).
$es_thumb_id = (int) get_post_thumbnail_id( $es_post_id );
$es_img      = $es_thumb_id ? (string) wp_get_attachment_image_url( $es_thumb_id, 'large' ) : '';
if ( ! $es_img ) {
	$es_img = ES_ASSETS_URI . 'images/light-tunnel.jpg';
}

// First category.
$es_cats = get_the_category( $es_post_id );
$es_cat  = $es_cats ? $es_cats[0]->name : '';

// Featured badge (sticky, filterable).
$es_featured = (bool) apply_filters( 'es_post_card_featured', is_sticky( $es_post_id ), $es_post_id );

// Persian digits.
$es_fa_digits = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );

// Date: get_the_date() is WP-Parsidate-compatible (AC-02).
$es_date    = Calendar::post_date( $es_post_id );
$es_date_fa = strtr( (string) $es_date, $es_fa_digits );

// Reading time estimate from content word count (filterable).
$es_content = (string) get_post_field( 'post_content', $es_post_id );
$es_words   = (int) preg_match_all( '/\S+/u', wp_strip_all_tags( $es_content ) );
$es_minutes = max( 1, (int) ceil( $es_words / 200 ) );
$es_minutes = (int) apply_filters( 'es_post_card_read_minutes', $es_minutes, $es_post_id );
$es_minutes_fa = strtr( (string) $es_minutes, $es_fa_digits );

// Excerpt: escaped + length-controlled (AC-03).
$es_excerpt = (string) get_post_field( 'post_excerpt', $es_post_id );
if ( '' === trim( $es_excerpt ) ) {
	$es_excerpt = wp_strip_all_tags( $es_content );
}
$es_excerpt = wp_trim_words( $es_excerpt, 35, '…' );

// Author.
$es_author_id = (int) get_post_field( 'post_author', $es_post_id );
$es_author    = (string) get_the_author_meta( 'display_name', $es_author_id );
$es_search_raw = $es_title . ' ' . $es_excerpt . ' ' . $es_cat;
foreach ( $es_cats as $es_search_cat ) { $es_search_raw .= ' ' . $es_search_cat->name; }
foreach ( get_the_tags( $es_post_id ) ?: array() as $es_search_tag ) { $es_search_raw .= ' ' . $es_search_tag->name; }
$es_search_raw = wp_strip_all_tags( $es_search_raw );
$es_search_norm = mb_strtolower( strtr( $es_search_raw, array( 'ي' => 'ی', 'ك' => 'ک', 'ة' => 'ه', 'ؤ' => 'و', "\u{200c}" => ' ' ) ), 'UTF-8' );
$es_search_ar = strtr( $es_search_norm, array( 'ی' => 'ي', 'ک' => 'ك' ) );
$es_digits = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
$es_search_text = preg_replace( '/\s+/u', ' ', $es_search_raw . ' ' . $es_search_norm . ' ' . $es_search_ar . ' ' . strtr( $es_search_norm, $es_digits ) . ' ' . strtr( $es_search_ar, $es_digits ) );
// Compact variant (P07-T016): DOM parity with the related-articles grid in
// blog/types-of-light-tunnel.html (h-44 image, date/title/excerpt/read-more).
if ( ! empty( $args['compact'] ) ) {
	$es_compact_excerpt = wp_trim_words( $es_excerpt, 24, '…' );
	?>
	<a href="<?php echo esc_url( $es_link ); ?>" class="card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]">
		<div class="relative h-44 overflow-hidden">
			<img src="<?php echo esc_url( $es_img ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-1000 group-hover:scale-108"/>
			<?php if ( '' !== $es_cat ) : ?><span class="absolute right-4 top-4 rounded-full bg-emerald-950/75 px-3 py-1 text-[11px] font-bold text-emerald-100 backdrop-blur-md"><?php echo esc_html( $es_cat ); ?></span><?php endif; ?>
		</div>
		<div class="flex flex-1 flex-col gap-2.5 p-5">
			<span class="text-[11.5px] font-semibold text-slate-500"><?php echo esc_html( $es_date_fa ); ?></span>
			<h4 class="text-[15.5px] font-extrabold leading-7 text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html( $es_title ); ?></h4>
			<?php if ( '' !== trim( $es_compact_excerpt ) ) : ?><p class="line-clamp-2 text-[12.5px] leading-6 text-slate-500"><?php echo esc_html( $es_compact_excerpt ); ?></p><?php endif; ?>
			<span class="mt-auto flex items-center gap-1.5 pt-2 text-[12.5px] font-bold text-emerald-700"><?php esc_html_e( 'ادامه مطلب', 'erfansanat' ); ?><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform duration-400 group-hover:-translate-x-0.5 group-hover:-translate-y-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span>
		</div>
	</a>
	<?php
	return;
}

if ( $es_archive_item ) : ?>
<div class="block" data-blog-item="true" data-blog-category="<?php echo esc_attr( $es_cat ); ?>" data-blog-search-text="<?php echo esc_attr( $es_search_text ); ?>"><div>
<?php endif; ?>
<a <?php echo $es_search_result ? 'data-search-item="' . esc_attr( $es_search_text ) . '"' : ''; ?> href="<?php echo esc_url( $es_link ); ?>" class="card-light group flex h-full flex-col overflow-hidden rounded-[1.5rem]">
	<div class="relative h-52 overflow-hidden">
		<img src="<?php echo esc_url( $es_img ); ?>" alt="<?php echo esc_attr( $es_title ); ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-[1.1s] ease-[cubic-bezier(0.22,1,0.36,1)] group-hover:scale-110"/>
		<?php if ( '' !== $es_cat ) : ?>
			<span class="absolute right-4 top-4 rounded-full bg-emerald-950/80 px-3.5 py-1.5 text-[11px] font-bold text-emerald-100 backdrop-blur-md"><?php echo esc_html( $es_cat ); ?></span>
		<?php endif; ?>
		<?php if ( $es_featured ) : ?>
			<span class="absolute left-4 top-4 rounded-full bg-emerald-600 px-3 py-1.5 text-[10.5px] font-bold text-white shadow-lg">★ ویژه</span>
		<?php endif; ?>
		<div class="absolute inset-x-0 bottom-0 h-24 bg-gradient-to-t from-emerald-950/70 to-transparent"></div>
	</div>
	<div class="flex flex-1 flex-col gap-3.5 p-6">
		<div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-[11.5px] font-semibold text-slate-500">
			<span class="flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-days h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path><path d="M8 14h.01"></path><path d="M12 14h.01"></path><path d="M16 14h.01"></path><path d="M8 18h.01"></path><path d="M12 18h.01"></path><path d="M16 18h.01"></path></svg><?php echo esc_html( $es_date_fa ); ?></span>
			<span class="flex items-center gap-1.5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock3 lucide-clock-3 h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><path d="M12 6v6h4"></path><circle cx="12" cy="12" r="10"></circle></svg><?php echo esc_html( $es_minutes_fa ); ?> دقیقه مطالعه</span>
		</div>
		<h3 class="text-[16.5px] font-extrabold leading-8 text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html( $es_title ); ?></h3>
		<?php if ( '' !== trim( $es_excerpt ) ) : ?>
			<p class="line-clamp-3 text-[13px] leading-7 text-slate-600"><?php echo esc_html( $es_excerpt ); ?></p>
		<?php endif; ?>
		<div class="mt-auto flex items-center justify-between border-t border-slate-100 pt-4">
			<span class="flex items-center gap-1.5 text-[11.5px] font-semibold text-slate-500"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round h-3.5 w-3.5 text-emerald-600" aria-hidden="true"><circle cx="12" cy="8" r="5"></circle><path d="M20 21a8 8 0 0 0-16 0"></path></svg><?php echo esc_html( $es_author ); ?></span>
			<span class="flex items-center gap-1.5 text-[12.5px] font-bold text-emerald-700">ادامه مطلب<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-up-left h-4 w-4 transition-transform duration-400 group-hover:-translate-y-0.5 group-hover:-translate-x-0.5" aria-hidden="true"><path d="M7 17V7h10"></path><path d="M17 17 7 7"></path></svg></span>
		</div>
	</div>
</a>

<?php if ( $es_archive_item ) : ?></div></div><?php endif; ?>
