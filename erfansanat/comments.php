<?php
/**
 * Comments template.
 *
 * The reference mirror has no comment UI at all, so this follows the theme's own
 * card-and-border language rather than copying markup that does not exist. The
 * form is the native WordPress one: the theme owns presentation, WordPress owns
 * validation, spam checks and storage, so a plugin that extends comments keeps
 * working.
 *
 * @package ErfanSanat
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

use ErfanSanat\Theme\Frontend\SectionParts;

if ( post_password_required() ) {
	return;
}

$erfansanat_comment_count = (int) get_comments_number();
?>
<section id="comments" class="mx-auto mt-12 max-w-4xl px-5 lg:px-0" data-comments="true">
	<div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-2xs sm:p-8">
		<div class="mb-6 flex flex-wrap items-center justify-between gap-3 border-b border-slate-200/80 pb-5">
			<h2 class="text-lg font-black text-slate-900">
				<?php
				if ( 0 === $erfansanat_comment_count ) {
					esc_html_e( 'نظرات', 'erfansanat' );
				} else {
					printf(
						/* translators: %s: number of comments. */
						esc_html__( 'نظرات (%s)', 'erfansanat' ),
						esc_html( SectionParts::fa_digits( (string) $erfansanat_comment_count ) )
					);
				}
				?>
			</h2>
			<?php if ( comments_open() ) : ?>
				<a href="#respond" class="inline-flex items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3.5 py-2 text-[12px] font-extrabold text-emerald-800 transition-colors hover:bg-emerald-100">
					<span><?php esc_html_e( 'نوشتن نظر', 'erfansanat' ); ?></span>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( have_comments() ) : ?>
			<ol class="space-y-4" data-comment-list="true">
				<?php
				wp_list_comments(
					array(
						'style'       => 'ol',
						'short_ping'  => true,
						'avatar_size' => 48,
						'callback'    => 'erfansanat_comment',
					)
				);
				?>
			</ol>

			<?php
			the_comments_pagination(
				array(
					'prev_text' => esc_html__( 'نظرات قبلی', 'erfansanat' ),
					'next_text' => esc_html__( 'نظرات بعدی', 'erfansanat' ),
					'class'     => 'mt-6 flex flex-wrap items-center justify-center gap-2 text-[12.5px] font-extrabold text-emerald-800',
				)
			);
			?>
		<?php elseif ( comments_open() ) : ?>
			<p class="rounded-2xl border border-dashed border-slate-300 bg-slate-50/70 px-5 py-8 text-center text-[13px] font-semibold text-slate-600"><?php esc_html_e( 'هنوز نظری ثبت نشده است. اولین نفر باشید.', 'erfansanat' ); ?></p>
		<?php endif; ?>

		<?php if ( ! comments_open() && $erfansanat_comment_count > 0 ) : ?>
			<p class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4 text-center text-[12.5px] font-semibold text-slate-600"><?php esc_html_e( 'بخش نظرات برای این نوشته بسته شده است.', 'erfansanat' ); ?></p>
		<?php endif; ?>

		<?php if ( comments_open() ) : ?>
			<div class="mt-8 border-t border-slate-200/80 pt-7" data-comment-form="true">
				<?php
				comment_form(
					array(
						'class_form'           => 'space-y-4',
						'title_reply'          => esc_html__( 'نظر خود را بنویسید', 'erfansanat' ),
						'title_reply_before'   => '<h3 id="reply-title" class="mb-4 text-base font-black text-slate-900">',
						'title_reply_after'    => '</h3>',
						'comment_notes_before' => '<p class="mb-4 text-[12.5px] leading-7 text-slate-500">' . esc_html__( 'نشانی ایمیل شما منتشر نخواهد شد.', 'erfansanat' ) . '</p>',
						'comment_field'        => '<p class="comment-form-comment"><label for="comment" class="mb-1.5 block text-[12.5px] font-extrabold text-slate-700">' . esc_html__( 'نظر', 'erfansanat' ) . '</label><textarea id="comment" name="comment" cols="45" rows="5" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-[13.5px] leading-8 text-slate-800 outline-none transition-colors focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100"></textarea></p>',
						'class_submit'         => 'inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-5 py-2.5 text-[12.5px] font-extrabold text-white shadow-sm transition-colors hover:bg-emerald-800',
						'label_submit'         => esc_html__( 'ارسال نظر', 'erfansanat' ),
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
/**
 * Render one comment in the theme's card style.
 *
 * @param \WP_Comment $comment Comment being rendered.
 * @param array       $args    Arguments passed to `wp_list_comments()`.
 * @param int         $depth   Nesting depth.
 * @return void
 */
function erfansanat_comment( $comment, array $args, int $depth ): void {
	$erfansanat_is_author = (int) $comment->user_id > 0;
	?>
	<li id="comment-<?php echo esc_attr( (string) $comment->comment_ID ); ?>" <?php comment_class( 'rounded-2xl border border-slate-200/80 bg-slate-50/60 p-4 sm:p-5' ); ?>>
		<div class="flex gap-3.5">
			<?php if ( 0 === (int) $args['avatar_size'] ) : ?>
				<?php else : ?>
				<div class="shrink-0 overflow-hidden rounded-xl border border-white bg-white shadow-2xs">
					<?php echo get_avatar( $comment, (int) $args['avatar_size'], '', '', array( 'class' => 'h-11 w-11' ) ); ?>
				</div>
			<?php endif; ?>
			<div class="min-w-0 flex-1">
				<div class="flex flex-wrap items-center gap-2">
					<span class="text-[13px] font-extrabold text-slate-900"><?php echo esc_html( (string) get_comment_author( $comment ) ); ?></span>
					<?php if ( $erfansanat_is_author ) : ?>
						<span class="rounded-lg bg-emerald-100 px-2 py-0.5 text-[10.5px] font-black text-emerald-800"><?php esc_html_e( 'نویسنده', 'erfansanat' ); ?></span>
					<?php endif; ?>
					<time datetime="<?php echo esc_attr( (string) get_comment_time( 'c', false, false, $comment ) ); ?>" class="text-[11.5px] font-semibold text-slate-400"><?php echo esc_html( (string) get_comment_date( '', $comment ) ); ?></time>
				</div>
				<div class="mt-2 text-[13px] leading-8 text-slate-700">
					<?php if ( '0' === (string) $comment->comment_approved ) : ?>
						<p class="mb-1 text-[12px] font-bold text-amber-700"><?php esc_html_e( 'نظر شما پس از تأیید نمایش داده می‌شود.', 'erfansanat' ); ?></p>
					<?php endif; ?>
					<?php comment_text( $comment ); ?>
				</div>
				<?php
				if ( 'div' !== $args['style'] ) {
					comment_reply_link(
						array_merge(
							$args,
							array(
								'depth'      => $depth,
								'max_depth'  => (int) $args['max_depth'],
								'reply_text' => esc_html__( 'پاسخ', 'erfansanat' ),
								'before'     => '<div class="mt-2.5 text-[12px] font-extrabold text-emerald-700">',
								'after'      => '</div>',
							)
						),
						$comment
					);
				}
				?>
			</div>
		</div>
	<?php
	// `wp_list_comments()` closes the `</li>` itself.
}
