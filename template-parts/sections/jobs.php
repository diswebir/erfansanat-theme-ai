<?php
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$title = trim((string) ($fields['title'] ?? ''));
$description = trim((string) ($fields['description'] ?? ''));
$jobs = isset($fields['items']) && is_array($fields['items']) ? array_values(array_filter($fields['items'], 'is_array')) : [];
$jobs = array_values(array_filter($jobs, static function (array $job): bool {
    return '' !== trim((string) ($job['title'] ?? '')) || '' !== trim((string) ($job['description'] ?? ''));
}));
$categories = [];
foreach ($jobs as $job) {
    $category = trim((string) ($job['category'] ?? ''));
    if ('' !== $category && !in_array($category, $categories, true)) {
        $categories[] = $category;
    }
}
$categoryKeys = [];
foreach ($categories as $index => $category) {
    $categoryKeys[$category] = 'department-' . ($index + 1);
}
if ('' === $title && '' === $description && !$jobs) {
    return;
}
?>
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16" data-editable-section="jobs" <?php echo $jobs ? 'data-recruit-tabs="true"' : ''; ?>>
    <div class="mb-6">
        <?php if ($title) : ?><h2 class="text-2xl font-black leading-relaxed text-slate-950"><?php echo esc_html($title); ?></h2><?php endif; ?>
        <?php if ($description) : ?><p class="mt-2 max-w-3xl text-sm leading-7 text-slate-600"><?php echo esc_html($description); ?></p><?php endif; ?>
    </div>
    <?php if ($jobs) : ?>
        <div class="mb-6 flex flex-wrap gap-2" role="group" aria-label="<?php echo esc_attr__('دسته‌بندی فرصت‌های شغلی', 'erfansanat-theme'); ?>">
            <button type="button" data-recruit-tab-btn="all" class="rounded-xl bg-emerald-700 px-4 py-2 text-xs font-extrabold text-white shadow-sm"><?php echo esc_html__('همه فرصت‌ها', 'erfansanat-theme'); ?></button>
            <?php foreach ($categories as $category) : ?><button type="button" data-recruit-tab-btn="<?php echo esc_attr($categoryKeys[$category]); ?>" class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-xs font-extrabold text-slate-700 hover:border-emerald-300 hover:text-emerald-800"><?php echo esc_html($category); ?></button><?php endforeach; ?>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($jobs as $job) :
                $jobTitle = trim((string) ($job['title'] ?? ''));
                $jobCategory = trim((string) ($job['category'] ?? ''));
                $jobDescription = trim((string) ($job['description'] ?? ''));
                $image = trim((string) ($job['image'] ?? ''));
                $link = isset($job['link']) && is_array($job['link']) ? $job['link'] : [];
                $url = trim((string) ($link['url'] ?? ''));
                $panelKey = $jobCategory && isset($categoryKeys[$jobCategory]) ? $categoryKeys[$jobCategory] : 'other';
                ?>
                <article data-recruit-panel="<?php echo esc_attr($panelKey); ?>" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($jobTitle); ?>" class="h-44 w-full object-cover" loading="lazy" /><?php endif; ?>
                    <div class="p-5">
                        <?php if ($jobCategory) : ?><p class="mb-2 text-xs font-extrabold text-emerald-700"><?php echo esc_html($jobCategory); ?></p><?php endif; ?>
                        <?php if ($jobTitle) : ?><h3 class="text-base font-black leading-7 text-slate-900"><?php echo esc_html($jobTitle); ?></h3><?php endif; ?>
                        <?php if ($jobDescription) : ?><p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600"><?php echo esc_html($jobDescription); ?></p><?php endif; ?>
                        <?php if ($url && !empty($link['label'])) : ?><a href="<?php echo esc_url($url); ?>" <?php echo !empty($link['target_blank']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> class="mt-4 inline-flex rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-extrabold text-white"><?php echo esc_html($link['label']); ?></a><?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
