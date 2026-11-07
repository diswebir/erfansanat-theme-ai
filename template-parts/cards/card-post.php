<?php
/**
 * Shared article card for the homepage blog preview.
 *
 * @package ErfanSanat\Theme
 *
 * @var array<string, mixed> $args Template arguments.
 */

$postId = isset($args['post_id']) ? absint($args['post_id']) : 0;
$article = $postId ? get_post($postId) : null;
if (!$article instanceof WP_Post || 'post' !== $article->post_type) {
    return;
}

$title = get_the_title($postId);
$permalink = get_permalink($postId);
$imageUrl = \ErfanSanat\Theme\HomepageData::get_post_image_url($postId, 'medium_large');
$excerpt = get_the_excerpt($postId);
$category = get_the_category($postId);
$categoryName = is_array($category) && isset($category[0]) ? $category[0]->name : __('مجله علمی', 'erfansanat-theme');
$readingTime = max(1, (int) ceil(str_word_count(wp_strip_all_tags((string) $article->post_content)) / 200));
?>
<a href="<?php echo esc_url($permalink); ?>" class="card-light group flex h-full flex-col justify-between rounded-2xl p-3.5">
    <div>
        <div class="relative aspect-[4/3] w-full overflow-hidden rounded-xl bg-slate-900">
            <?php if ($imageUrl) : ?><img src="<?php echo esc_url($imageUrl); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" /><?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent" aria-hidden="true"></div>
        </div>
        <div class="mt-3 flex flex-wrap items-center gap-2 text-[11px] font-bold text-slate-500 tabular-nums">
            <span class="text-emerald-700"><?php echo esc_html($categoryName); ?></span><span aria-hidden="true">·</span>
            <span><?php echo esc_html(get_the_date(get_option('date_format'), $postId)); ?></span><span aria-hidden="true">·</span>
            <span><?php echo esc_html(sprintf(__('%d دقیقه مطالعه', 'erfansanat-theme'), $readingTime)); ?></span>
        </div>
        <h3 class="mt-1.5 line-clamp-2 text-right text-sm leading-6 font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html($title); ?></h3>
        <?php if ($excerpt) : ?><p class="mt-1.5 line-clamp-2 text-right text-xs leading-6 font-medium text-slate-500"><?php echo esc_html($excerpt); ?></p><?php endif; ?>
    </div>
    <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-2.5 text-xs font-extrabold text-emerald-700"><span><?php echo esc_html__('مطالعه مقاله', 'erfansanat-theme'); ?></span><span aria-hidden="true">←</span></div>
</a>
