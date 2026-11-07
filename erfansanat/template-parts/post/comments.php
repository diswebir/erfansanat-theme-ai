<?php
/**
 * Blog comments UI (P07-T017). Core comment list/form with all standard hooks
 * preserved; styling follows the site's emerald card design system. Nested
 * replies use the core walker + comment-reply script; pending comments show
 * the standard moderation notice.
 *
 * @package ErfanSanat
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}

if ( ! have_comments() && ! comments_open() ) {
	?>
	<div class="mt-12 rounded-3xl border border-slate-200 bg-white p-6 text-center shadow-xs">
		<p class="text-[13.5px] font-bold text-slate-500"><?php esc_html_e( 'دیدگاه‌ها بسته شده است.', 'erfansanat' ); ?></p>
	</div>
	<?php
	return;
}

if ( comments_open() && is_singular() ) {
	wp_enqueue_script( 'comment-reply' );
}

$es_comment_count = (int) get_comments_number();
$es_fa_digits     = array( '0' => '۰', '1' => '۱', '2' => '۲', '3' => '۳', '4' => '۴', '5' => '۵', '6' => '۶', '7' => '۷', '8' => '۸', '9' => '۹' );
?>
<section id="comments" class="mt-12 rounded-3xl border border-emerald-100 bg-white p-6 shadow-xs sm:p-8" data-post-comments="true">
	<h3 class="flex items-center gap-2.5 text-xl font-extrabold text-slate-900">
		<span class="grid h-9 w-9 place-items-center rounded-xl bg-emerald-50 text-emerald-700"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-messages-square h-4.5 w-4.5" aria-hidden="true"><path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2z"></path><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"></path></svg></span>
		<?php
		if ( $es_comment_count > 0 ) {
			printf(
				/* translators: %s: comment count (Persian digits). */
				esc_html__( 'دیدگاه‌ها (%s)', 'erfansanat' ),
				esc_html( strtr( (string) $es_comment_count, $es_fa_digits ) )
			);
		} else {
			esc_html_e( 'دیدگاه‌ها', 'erfansanat' );
		}
		?>
	</h3>

	<?php if ( have_comments() ) : ?>
		<div class="comment-list mt-6 space-y-5">
			<?php
			wp_list_comments(
				array(
					'style'        => 'div',
					'avatar_size'  => 44,
					'short_ping'   => true,
					'max_depth'    => (int) get_option( 'thread_comments_depth', 3 ),
					'callback'     => 'es_comment_callback',
				)
			);
			?>
		</div>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
			<nav class="mt-6 flex items-center justify-between text-[13px] font-bold text-emerald-700" aria-label="<?php esc_attr_e( 'صفحه‌بندی دیدگاه‌ها', 'erfansanat' ); ?>">
				<?php previous_comments_link( esc_html__( 'دیدگاه‌های قدیمی‌تر', 'erfansanat' ) ); ?>
				<?php next_comments_link( esc_html__( 'دیدگاه‌های جدیدتر', 'erfansanat' ) ); ?>
			</nav>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ( ! comments_open() ) : ?>
		<p class="mt-6 rounded-2xl bg-slate-50 px-4 py-3 text-[13px] font-bold text-slate-500"><?php esc_html_e( 'دیدگاه‌ها بسته شده است.', 'erfansanat' ); ?></p>
	<?php else : ?>
		<div class="es-comment-form mt-8 border-t border-slate-100 pt-6">
			<?php
			comment_form(
				array(
					'title_reply'         => __( 'دیدگاه خود را بنویسید', 'erfansanat' ),
					'title_reply_before'  => '<h4 id="reply-title" class="comment-reply-title mb-4 text-[15.5px] font-extrabold text-slate-900">',
					'title_reply_after'   => '</h4>',
					'cancel_reply_before' => '<span class="mr-2 text-[12px] font-bold">',
					'cancel_reply_after'  => '</span>',
					'class_form'          => 'comment-form space-y-4',
					'class_submit'        => 'submit inline-flex cursor-pointer items-center gap-2 rounded-xl bg-emerald-700 px-6 py-3 text-[13px] font-extrabold text-white shadow-sm transition-colors hover:bg-emerald-800',
					'label_submit'        => __( 'ارسال دیدگاه', 'erfansanat' ),
					'comment_notes_before' => '<p class="comment-notes mb-3 text-[12px] font-medium text-slate-500">' . esc_html__( 'نشانی ایمیل شما منتشر نخواهد شد.', 'erfansanat' ) . '</p>',
					'comment_field'       => '<p class="comment-form-comment"><label for="comment" class="mb-1.5 block text-[12.5px] font-bold text-slate-700">' . esc_html__( 'دیدگاه شما', 'erfansanat' ) . '</label><textarea id="comment" name="comment" rows="5" required class="w-full rounded-2xl border border-slate-200 bg-slate-50/60 px-4 py-3 text-[13.5px] leading-7 text-slate-800 outline-none transition-all focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"></textarea></p>',
				)
			);
			?>
		</div>
	<?php endif; ?>
</section>
