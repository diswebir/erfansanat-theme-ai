<?php
/**
 * comments.php — comments template (required to avoid WP deprecation).
 *
 * @package ErfanSanat\Theme
 */

if (!defined('ABSPATH')) { exit; }
if (post_password_required()) { return; }
?>
<section id="comments" class="comments-area mx-auto max-w-[900px] px-4 pb-16 sm:px-6 lg:px-8">
    <?php if (have_comments()) : ?>
        <h2 class="text-lg font-black text-slate-900"><?php comments_number('', __('یک دیدگاه', 'erfansanat'), __('% دیدگاه', 'erfansanat')); ?></h2>
        <ol class="mt-4 space-y-4"><?php wp_list_comments(['style' => 'ol']); ?></ol>
        <?php the_comments_navigation(); ?>
    <?php endif; ?>
    <?php if (comments_open()) : ?>
        <?php comment_form(); ?>
    <?php endif; ?>
</section>
