<?php
/** WordPress-native post comments and comment form. */
if (post_password_required()) {
    return;
}
?>
<div class="space-y-6">
    <?php if (have_comments()) : ?>
        <ol class="space-y-4">
            <?php wp_list_comments(['style' => 'ol', 'short_ping' => true, 'avatar_size' => 40]); ?>
        </ol>
        <?php the_comments_pagination(['prev_text' => __('دیدگاه‌های تازه‌تر', 'erfansanat-theme'), 'next_text' => __('دیدگاه‌های قدیمی‌تر', 'erfansanat-theme')]); ?>
    <?php endif; ?>
    <?php if (comments_open()) : ?>
        <div class="border-t border-slate-100 pt-6">
            <?php comment_form(['class_form' => 'space-y-4', 'title_reply' => __('دیدگاه خود را بنویسید', 'erfansanat-theme'), 'label_submit' => __('ثبت دیدگاه', 'erfansanat-theme')]); ?>
        </div>
    <?php elseif (get_comments_number()) : ?>
        <p class="text-sm text-slate-500"><?php echo esc_html__('ارسال دیدگاه برای این نوشته بسته شده است.', 'erfansanat-theme'); ?></p>
    <?php endif; ?>
</div>
