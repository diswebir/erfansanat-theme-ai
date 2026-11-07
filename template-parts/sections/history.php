<?php
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$title = trim((string) ($fields['title'] ?? ''));
$description = trim((string) ($fields['description'] ?? ''));
$milestones = isset($fields['milestones']) && is_array($fields['milestones']) ? array_values(array_filter($fields['milestones'], 'is_array')) : [];
$milestones = array_values(array_filter($milestones, static function (array $row): bool { return '' !== trim((string) ($row['year'] ?? '') . ($row['title'] ?? '') . ($row['description'] ?? '') . ($row['image'] ?? '')); }));
if ('' === $title && '' === $description && !$milestones) { return; }
?>
<section class="border-y border-emerald-100 bg-white py-12 lg:py-16" data-editable-section="history"><div class="mx-auto max-w-7xl px-5 lg:px-8">
    <div class="mb-7"><?php if ($title) : ?><h2 class="text-2xl font-black text-slate-950"><?php echo esc_html($title); ?></h2><?php endif; ?><?php if ($description) : ?><p class="mt-2 max-w-3xl text-sm leading-7 text-slate-600"><?php echo esc_html($description); ?></p><?php endif; ?></div>
    <?php if ($milestones) : ?><ol class="relative grid gap-4 border-r-2 border-emerald-100 pr-5 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($milestones as $milestone) : $year = trim((string) ($milestone['year'] ?? '')); $itemTitle = trim((string) ($milestone['title'] ?? '')); $itemDescription = trim((string) ($milestone['description'] ?? '')); $image = trim((string) ($milestone['image'] ?? '')); ?>
            <li class="relative rounded-2xl border border-slate-200 bg-slate-50 p-4"><span aria-hidden="true" class="absolute -right-[1.72rem] top-5 h-3 w-3 rounded-full border-2 border-white bg-emerald-600 ring-2 ring-emerald-100"></span><?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($itemTitle); ?>" class="mb-3 h-36 w-full rounded-xl object-cover" loading="lazy" /><?php endif; ?><?php if ($year) : ?><p class="text-xs font-black text-emerald-700"><?php echo esc_html($year); ?></p><?php endif; ?><?php if ($itemTitle) : ?><h3 class="mt-1 text-base font-black text-slate-900"><?php echo esc_html($itemTitle); ?></h3><?php endif; ?><?php if ($itemDescription) : ?><p class="mt-2 text-sm leading-7 text-slate-600"><?php echo esc_html($itemDescription); ?></p><?php endif; ?></li>
        <?php endforeach; ?>
    </ol><?php endif; ?>
</div></section>
