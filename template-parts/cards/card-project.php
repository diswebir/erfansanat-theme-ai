<?php
/**
 * Shared project card for homepage feature and archive grids.
 *
 * @package ErfanSanat\Theme
 *
 * @var array<string, mixed> $args Template arguments.
 */

$projectId = isset($args['post_id']) ? absint($args['post_id']) : 0;
$project = $projectId ? get_post($projectId) : null;
if (!$project instanceof WP_Post || 'projects' !== $project->post_type) {
    return;
}

$variant = isset($args['variant']) ? sanitize_key((string) $args['variant']) : 'archive';
$index = isset($args['index']) ? absint($args['index']) : 0;
$imageUrl = \ErfanSanat\Theme\HomepageData::get_post_image_url($projectId);
$title = get_the_title($projectId);
$permalink = get_permalink($projectId);
$location = \ErfanSanat\Theme\HomepageData::get_project_location($projectId);
$dateLabel = \ErfanSanat\Theme\HomepageData::get_project_date_label($projectId);
$terms = get_the_terms($projectId, 'project_type');
$typeLabel = is_array($terms) && isset($terms[0]) ? $terms[0]->name : '';
$excerpt = get_the_excerpt($projectId);
if ('' === trim($excerpt)) {
    $excerpt = wp_trim_words(wp_strip_all_tags((string) $project->post_content), 24);
}
?>
<?php if ('featured' === $variant) : ?>
    <?php $spanClass = 0 === $index ? 'lg:col-span-3' : 'lg:col-span-2'; ?>
    <?php $featureSize = 0 === $index ? 'min-h-[340px] sm:min-h-[420px] lg:min-h-[540px]' : 'min-h-[235px] sm:min-h-[258px]'; ?>
    <div class="flex h-full flex-col <?php echo esc_attr($spanClass); ?>">
        <a href="<?php echo esc_url($permalink); ?>" class="card-glow group relative block h-full w-full overflow-hidden rounded-3xl border border-slate-200/80 bg-slate-950 <?php echo esc_attr($featureSize); ?>">
            <?php if ($imageUrl) : ?><img src="<?php echo esc_url($imageUrl); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" /><?php endif; ?>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/95 via-slate-950/35 to-slate-950/15" aria-hidden="true"></div>
            <div class="absolute inset-x-0 top-0 flex items-center justify-between p-5 sm:p-6"><span class="grid h-10 w-10 place-items-center rounded-2xl border border-white/25 bg-slate-950/60 text-white backdrop-blur-md transition-colors duration-300 group-hover:border-emerald-400 group-hover:bg-emerald-600" aria-hidden="true">↗</span><span class="text-sm font-black tracking-wider text-white/75 tabular-nums"><?php echo esc_html(sprintf('%02d', $index + 1)); ?></span></div>
            <div class="absolute inset-x-0 bottom-0 flex flex-col gap-1.5 p-5 sm:p-7">
                <?php if ($typeLabel || $dateLabel) : ?><span class="text-xs font-bold text-emerald-300"><?php echo esc_html(trim($typeLabel . ('' !== $typeLabel && '' !== $dateLabel ? ' · ' : '') . $dateLabel)); ?></span><?php endif; ?>
                <h3 class="text-xl font-black leading-8 text-white sm:text-2xl"><?php echo esc_html($title); ?></h3>
                <?php if ($excerpt) : ?><p class="mt-1 line-clamp-2 text-sm leading-7 font-medium text-white/75"><?php echo esc_html($excerpt); ?></p><?php endif; ?>
                <?php if ($location) : ?><span class="mt-2 text-xs font-bold text-white/75"><?php echo esc_html($location); ?></span><?php endif; ?>
            </div>
        </a>
    </div>
<?php else : ?>
    <a href="<?php echo esc_url($permalink); ?>" class="card-light group flex h-full flex-col justify-between rounded-2xl p-3">
        <div>
            <div class="relative aspect-[4/3] w-full overflow-hidden rounded-xl bg-slate-900">
                <?php if ($imageUrl) : ?><img src="<?php echo esc_url($imageUrl); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" /><?php endif; ?>
                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/65 via-transparent to-transparent" aria-hidden="true"></div>
                <?php if ($location) : ?><span class="absolute bottom-2.5 right-2.5 rounded-md bg-slate-950/55 px-2 py-0.5 text-[10.5px] font-extrabold text-white backdrop-blur-xs"><?php echo esc_html($location); ?></span><?php endif; ?>
            </div>
            <div class="mt-3 px-1">
                <div class="text-[11px] font-bold text-emerald-700 tabular-nums"><?php echo esc_html(trim($typeLabel . ('' !== $typeLabel && '' !== $dateLabel ? ' · ' : '') . $dateLabel)); ?></div>
                <h4 class="mt-1 line-clamp-2 min-h-[2.5rem] text-right text-xs sm:text-[13px] leading-5 font-extrabold text-slate-900 transition-colors group-hover:text-emerald-700"><?php echo esc_html($title); ?></h4>
            </div>
        </div>
        <div class="mt-3 flex items-center justify-between border-t border-slate-100 px-1 pt-2.5 text-[11px] font-bold text-slate-500 group-hover:text-emerald-700"><span><?php echo esc_html__('مشاهده جزئیات پروژه', 'erfansanat-theme'); ?></span><span aria-hidden="true">←</span></div>
    </a>
<?php endif; ?>
