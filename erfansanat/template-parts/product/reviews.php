<?php
/**
 * Product reviews panel (P06-T014). Renders the real average/count, the approved
 * review list (WooCommerce comments) and the reference review form. The form
 * posts to wp-comments-post.php with a nonce and the Woo `rating` field while
 * keeping the data-review-* hooks main.js uses for the star UI (AC-02). Closed
 * comments show a notice instead of the form (AC-03).
 *
 * @package ErfanSanat
 * @var WC_Product|ES_Shim_Product $product
 */

defined( 'ABSPATH' ) || exit;

$es_product = isset( $product ) && is_object( $product ) ? $product : null;
if ( ! $es_product && isset( $args['product'] ) && is_object( $args['product'] ) ) {
	$es_product = $args['product'];
}
if ( ! $es_product || ! method_exists( $es_product, 'get_id' ) ) {
	return;
}
$es_id    = (int) $es_product->get_id();
$es_title = get_the_title( $es_id );

$es_fa = static function ( $v ) {
	$map = array();
	for ( $d = 0; $d < 10; $d++ ) {
		$map[ (string) $d ] = chr( 0xDB ) . chr( 0xB0 + $d );
	}
	return strtr( (string) $v, $map );
};

$es_comments = get_comments(
	array(
		'post_id' => $es_id,
		'status'  => 'approve',
		'type'    => 'comment',
		'orderby' => 'comment_date_gmt',
		'order'   => 'DESC',
	)
);
// WooCommerce stores reviews as comment_type "review"; include them too.
$es_reviews = get_comments(
	array(
		'post_id' => $es_id,
		'status'  => 'approve',
		'type'    => 'review',
		'orderby' => 'comment_date_gmt',
		'order'   => 'DESC',
	)
);
$es_comments = array_merge( $es_reviews, $es_comments );

$es_count  = count( $es_comments );
$es_sum    = 0;
$es_rated  = 0;
foreach ( $es_comments as $es_c ) {
	$es_r = (int) get_comment_meta( $es_c->comment_ID, 'rating', true );
	if ( $es_r > 0 ) {
		$es_sum   += $es_r;
		$es_rated += 1;
	}
}
$es_average = $es_rated ? round( $es_sum / $es_rated, 2 ) : ( method_exists( $es_product, 'get_average_rating' ) ? (float) $es_product->get_average_rating() : 0 );
$es_avg_str = $es_fa( number_format( (float) $es_average, 2, '.', '' ) );
$es_round   = (int) round( $es_average );

$es_star = static function ( $active, $size = 'h-4 w-4' ) {
	$out = '';
	for ( $s = 1; $s <= 5; $s++ ) {
		$cls  = $s <= $active ? 'fill-amber-400 text-amber-400' : 'text-slate-300';
		$out .= '<svg class="' . $size . ' ' . $cls . '" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
	}
	return $out;
};

$es_open = comments_open( $es_id );
?>
<div class="grid items-start gap-8 lg:grid-cols-12" data-product-reviews>
	<div class="space-y-5 lg:col-span-7">
		<div class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-slate-200/80 bg-slate-50/70 p-4 sm:px-5">
			<div class="flex items-center gap-3.5">
				<div class="grid h-13 w-13 place-items-center rounded-2xl bg-emerald-700 text-lg font-black text-white tabular-nums"><?php echo esc_html( $es_avg_str ); ?></div>
				<div>
					<div class="flex items-center gap-1"><?php echo $es_star( $es_round ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<div class="mt-1 text-xs font-bold text-slate-600"><?php echo esc_html( sprintf( 'میانگین امتیاز خریداران برای %s', $es_title ) ); ?></div>
				</div>
			</div>
			<span data-reviews-count-badge="true" class="rounded-xl border border-emerald-200 bg-white px-3 py-1.5 text-xs font-extrabold text-emerald-800 tabular-nums"><?php echo esc_html( sprintf( '%s دیدگاه ثبت‌شده', $es_fa( $es_count ) ) ); ?></span>
		</div>

		<div data-reviews-list="true" class="space-y-3.5">
			<?php if ( $es_comments ) : ?>
				<?php foreach ( $es_comments as $es_comment ) : ?>
					<?php get_template_part( 'woocommerce/single-product/review', null, array( 'comment' => $es_comment ) ); ?>
				<?php endforeach; ?>
			<?php else : ?>
				<p class="rounded-2xl border border-dashed border-slate-200 bg-white p-5 text-center text-xs font-bold text-slate-400"><?php esc_html_e( 'هنوز دیدگاهی ثبت نشده است؛ اولین نفر باشید.', 'erfansanat' ); ?></p>
			<?php endif; ?>
		</div>
	</div>

	<div class="lg:col-span-5">
		<?php if ( $es_open ) : ?>
			<form data-review-form="true" action="<?php echo esc_url( site_url( '/wp-comments-post.php' ) ); ?>" method="post" class="rounded-3xl border border-emerald-200/80 bg-emerald-50/35 p-5 sm:p-6">
				<div class="mb-4 border-b border-emerald-100/80 pb-3.5">
					<h3 class="flex items-center gap-2 text-base font-black text-slate-900">
						<svg class="h-5 w-5 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
						<span><?php esc_html_e( 'ثبت نظر و امتیاز شما', 'erfansanat' ); ?></span>
					</h3>
					<p class="mt-1 text-xs font-medium text-slate-500"><?php esc_html_e( 'تجربه یا دیدگاه خود درباره این محصول را با سایر خریداران به اشتراک بگذارید', 'erfansanat' ); ?></p>
				</div>

				<div data-review-stars-group="true" class="mb-4 rounded-2xl border border-emerald-200/70 bg-white p-3.5">
					<div class="flex items-center justify-between text-xs">
						<span class="font-extrabold text-slate-700"><?php esc_html_e( 'امتیاز شما:', 'erfansanat' ); ?></span>
						<span data-review-rating-label="true" class="font-extrabold text-amber-600 tabular-nums"><?php echo esc_html( $es_fa( '۵ از ۵ ستاره (عالی و درجه یک)' ) ); ?></span>
					</div>
					<input type="hidden" data-review-rating-input="true" name="rating" value="5" />
					<div class="mt-2.5 flex items-center justify-center gap-2" role="radiogroup" aria-label="<?php esc_attr_e( 'انتخاب تعداد ستاره از ۱ تا ', 'erfansanat' ); ?>">
						<?php for ( $es_s = 1; $es_s <= 5; $es_s++ ) : ?>
							<button type="button" data-review-star="<?php echo (int) $es_s; ?>" aria-label="<?php echo esc_attr( sprintf( '%s ستاره', $es_fa( $es_s ) ) ); ?>" class="group/star flex flex-col items-center gap-1 rounded-xl border border-slate-100 bg-slate-50/70 px-2.5 py-2 transition hover:border-amber-200">
								<svg data-review-star-svg="true" class="h-5 w-5 fill-amber-400 text-amber-400" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
								<span class="text-[10px] font-black text-slate-500 tabular-nums"><?php echo esc_html( $es_fa( $es_s ) ); ?></span>
							</button>
						<?php endfor; ?>
					</div>
				</div>

				<div class="space-y-3">
					<input type="hidden" name="comment_post_ID" value="<?php echo (int) $es_id; ?>" />
					<?php wp_nonce_field( 'es_product_review', 'es_review_nonce' ); ?>
					<div>
						<label class="mb-1 block text-xs font-extrabold text-slate-700"><?php esc_html_e( 'نام و نام خانوادگی *', 'erfansanat' ); ?></label>
						<input type="text" name="author" required data-review-name="true" placeholder="<?php esc_attr_e( 'مثلاً: مهندس رضا محمدی', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-800 outline-none focus:border-emerald-400" />
					</div>
					<div>
						<label class="mb-1 block text-xs font-extrabold text-slate-700"><?php esc_html_e( 'مجموعه یا شهر (اختیاری)', 'erfansanat' ); ?></label>
						<input type="text" name="es_review_org" data-review-org="true" placeholder="<?php esc_attr_e( 'مثلاً: شهرداری اصفهان / پیمانکار پروژه', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-bold text-slate-800 outline-none focus:border-emerald-400" />
					</div>
					<div>
						<label class="mb-1 block text-xs font-extrabold text-slate-700"><?php esc_html_e( 'متن دیدگاه شما *', 'erfansanat' ); ?></label>
						<textarea name="comment" rows="3" required data-review-comment="true" placeholder="<?php esc_attr_e( 'نظر خود را درباره کیفیت، نوردهی یا تجربه خرید این محصول بنویسید...', 'erfansanat' ); ?>" class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium leading-6 text-slate-700 outline-none focus:border-emerald-400"></textarea>
					</div>
					<button type="submit" class="group relative inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-l from-emerald-700 via-emerald-600 to-emerald-500 px-5 py-3 text-sm font-extrabold text-white shadow-md transition hover:shadow-lg">
						<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
						<span><?php esc_html_e( 'ثبت نظر و امتیاز', 'erfansanat' ); ?></span>
					</button>
					<div data-review-success="true" class="hidden items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-100/80 px-3.5 py-2.5 text-xs font-extrabold text-emerald-800">
						<svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
						<span><?php esc_html_e( 'دیدگاه شما با موفقیت ثبت و به لیست نظرات اضافه شد.', 'erfansanat' ); ?></span>
					</div>
				</div>
			</form>
		<?php else : ?>
			<div class="rounded-3xl border border-slate-200/80 bg-slate-50/60 p-6 text-center text-xs font-bold text-slate-500" data-reviews-closed>
				<?php esc_html_e( 'دیدگاه‌ها برای این محصول بسته است.', 'erfansanat' ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>
