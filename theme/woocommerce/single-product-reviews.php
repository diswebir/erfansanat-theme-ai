<?php
/** WooCommerce single product reviews, rating distribution, and native review form. */
/** @version 11.2.0 */

defined( 'ABSPATH' ) || exit;

global $product;
$product = function_exists( 'wc_get_product' ) ? wc_get_product( get_the_ID() ) : $product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
if ( ! is_object( $product ) ) { return; }
$product_id = method_exists( $product, 'get_id' ) ? (int) $product->get_id() : (int) get_the_ID();
$reviews_enabled = ! function_exists( 'get_option' ) || 'yes' === get_option( 'woocommerce_enable_reviews', 'yes' );
if ( ! $reviews_enabled ) { return; }
$comments = function_exists( 'get_comments' ) ? get_comments( array( 'post_id' => $product_id, 'status' => 'approve', 'type' => 'review', 'number' => 0 ) ) : array();
$rating_counts = array_fill( 1, 5, 0 );
foreach ( is_array( $comments ) ? $comments : array() as $comment ) {
	$rating = function_exists( 'get_comment_meta' ) ? absint( get_comment_meta( $comment->comment_ID ?? 0, 'rating', true ) ) : 0;
	if ( $rating >= 1 && $rating <= 5 ) { $rating_counts[ $rating ]++; }
}
$total_ratings = array_sum( $rating_counts );
$average = method_exists( $product, 'get_average_rating' ) ? (float) $product->get_average_rating() : 0;
?>
<div id="reviews" class="woocommerce-Reviews space-y-8">
	<div id="comments" class="grid gap-8 lg:grid-cols-2">
		<div class="rounded-2xl border border-slate-200/80 bg-slate-50/60 p-5 sm:p-6">
			<h3 class="mb-5 text-base font-extrabold text-slate-900">امتیاز و دیدگاه خریداران</h3>
			<div class="mb-5 flex items-center gap-4"><span class="text-4xl font-black text-slate-900"><?php echo esc_html( number_format_i18n( $average, 1 ) ); ?></span><div><?php if ( function_exists( 'wc_get_rating_html' ) ) { echo wp_kses_post( wc_get_rating_html( $average, $total_ratings ) ); } ?><p class="mt-1 text-xs text-slate-500"><?php echo esc_html( number_format_i18n( $total_ratings ) ); ?> دیدگاه ثبت‌شده</p></div></div>
			<div class="space-y-2" aria-label="توزیع امتیازها"><?php for ( $rating = 5; $rating >= 1; $rating-- ) : $count = $rating_counts[ $rating ]; $percent = $total_ratings ? (int) round( 100 * $count / $total_ratings ) : 0; ?><div class="flex items-center gap-2 text-xs"><span class="w-10 shrink-0 font-bold text-slate-600"><?php echo esc_html( number_format_i18n( $rating ) ); ?> ستاره</span><div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-200"><span class="block h-full rounded-full bg-amber-400" style="width:<?php echo esc_attr( (string) $percent ); ?>%"></span></div><span class="w-7 text-left text-slate-500"><?php echo esc_html( number_format_i18n( $count ) ); ?></span></div><?php endfor; ?></div>
		</div>
		<div class="product-reviews-list">
			<?php if ( have_comments() ) : ?><ol class="commentlist space-y-3"><?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments', 'style' => 'ol', 'short_ping' => true ) ) ); ?></ol><?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?><nav class="woocommerce-pagination" aria-label="صفحه‌های دیدگاه"><?php paginate_comments_links( apply_filters( 'woocommerce_comment_pagination_args', array( 'prev_text' => '‹', 'next_text' => '›', 'type' => 'list' ) ) ); ?></nav><?php endif; ?><?php else : ?><p class="rounded-2xl border border-slate-200 bg-white p-5 text-sm text-slate-600"><?php echo esc_html__( 'هنوز دیدگاهی برای این محصول ثبت نشده است.', 'erfansanat-theme' ); ?></p><?php endif; ?>
		</div>
	</div>
	<div id="review_form_wrapper" class="rounded-2xl border border-slate-200/80 bg-white p-5 sm:p-7">
		<?php if ( comments_open( $product_id ) ) : ?>
			<div id="review_form">
				<?php
				$commenter = wp_get_current_commenter();
				$required  = (bool) get_option( 'require_name_email' );
				$rating_enabled = ! function_exists( 'wc_review_ratings_enabled' ) || wc_review_ratings_enabled();
				$comment_field = '';
				if ( $rating_enabled ) {
					$comment_field .= '<fieldset class="mb-4 rounded-2xl border border-emerald-200/70 bg-white p-3.5"><legend class="mb-3 text-xs font-extrabold text-slate-700">امتیاز شما <span class="required">*</span></legend><div data-review-stars-group="true" class="grid grid-cols-5 gap-2">';
					for ( $star = 1; $star <= 5; $star++ ) {
						$comment_field .= '<button type="button" data-review-star="' . esc_attr( (string) $star ) . '" aria-pressed="false" aria-label="' . esc_attr( number_format_i18n( $star ) . ' ستاره' ) . '" class="group/star flex flex-col items-center gap-1 rounded-xl border border-slate-100 bg-slate-50/70 px-2.5 py-2 transition-all hover:border-amber-300 hover:bg-amber-50/60 cursor-pointer"><span data-review-star-svg="true" aria-hidden="true" class="h-5 w-5 text-slate-300">★</span><span class="text-[10px] font-bold text-slate-500">' . esc_html( number_format_i18n( $star ) ) . '</span></button>';
					}
					$comment_field .= '</div><p data-review-rating-label="true" class="mt-2 text-xs text-slate-500">برای ثبت، امتیاز خود را انتخاب کنید.</p><select name="rating" id="rating" data-review-rating-input="true" aria-label="امتیاز از یک تا پنج" required style="position:absolute;opacity:0;width:1px;height:1px"><option value="">انتخاب امتیاز</option>';
					for ( $star = 5; $star >= 1; $star-- ) { $comment_field .= '<option value="' . esc_attr( (string) $star ) . '">' . esc_html( number_format_i18n( $star ) . ' ستاره' ) . '</option>'; }
					$comment_field .= '</select></fieldset>';
				}
				$comment_field .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'دیدگاه شما', 'erfansanat-theme' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="6" required></textarea></p>';
				$comment_form = array(
					'title_reply'        => have_comments() ? esc_html__( 'دیدگاه خود را ثبت کنید', 'erfansanat-theme' ) : esc_html__( 'اولین دیدگاه را ثبت کنید', 'erfansanat-theme' ),
					'title_reply_to'     => esc_html__( 'پاسخ به %s', 'erfansanat-theme' ),
					'comment_notes_before' => '',
					'comment_notes_after'  => '',
					'label_submit'       => esc_html__( 'ثبت دیدگاه', 'erfansanat-theme' ),
					'logged_in_as'       => '',
					'comment_field'      => $comment_field,
					'fields'             => array(
						'author' => '<p class="comment-form-author"><label for="author">' . esc_html__( 'نام', 'erfansanat-theme' ) . ( $required ? ' <span class="required">*</span>' : '' ) . '</label><input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ?? '' ) . '" size="30" ' . ( $required ? 'required' : '' ) . ' /></p>',
						'email'  => '<p class="comment-form-email"><label for="email">' . esc_html__( 'ایمیل', 'erfansanat-theme' ) . ( $required ? ' <span class="required">*</span>' : '' ) . '</label><input id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ?? '' ) . '" size="30" ' . ( $required ? 'required' : '' ) . ' /></p>',
					),
				);
				comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		<?php else : ?><p class="text-sm text-slate-600"><?php echo esc_html__( 'ثبت دیدگاه برای این محصول بسته شده است.', 'erfansanat-theme' ); ?></p><?php endif; ?>
	</div>
</div>
