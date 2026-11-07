<?php
$section = isset($args['section']) && is_array($args['section']) ? $args['section'] : (isset($section) && is_array($section) ? $section : []);
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$sectionTitle = trim((string) ($fields['title'] ?? ''));
$description = trim((string) ($fields['description'] ?? ''));
$items = isset($fields['items']) && is_array($fields['items']) ? array_values(array_filter($fields['items'], 'is_array')) : [];
$items = array_values(array_filter($items, static function (array $item): bool {
    return '' !== trim((string) ($item['title'] ?? '')) || '' !== trim((string) ($item['description'] ?? '')) || '' !== trim((string) ($item['image'] ?? ''));
}));
if ('' === $sectionTitle && '' === $description && !$items) {
    return;
}
?>
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16" data-editable-section="<?php echo esc_attr(sanitize_key((string) ($section['id'] ?? 'cards'))); ?>">
    <div class="mb-6"><?php if ($sectionTitle) : ?><h2 class="text-2xl font-black leading-relaxed text-slate-950"><?php echo esc_html($sectionTitle); ?></h2><?php endif; ?><?php if ($description) : ?><p class="mt-2 max-w-3xl text-sm leading-7 text-slate-600"><?php echo esc_html($description); ?></p><?php endif; ?></div>
    <?php if ($items) : ?><div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <?php foreach ($items as $item) :
            $itemTitle = trim((string) ($item['title'] ?? ''));
            $itemDescription = trim((string) ($item['description'] ?? ''));
            $itemImage = trim((string) ($item['image'] ?? ''));
            $itemIcon = trim((string) ($item['icon'] ?? ''));
            $itemLink = isset($item['link']) && is_array($item['link']) ? $item['link'] : [];
            $itemUrl = trim((string) ($itemLink['url'] ?? ''));
            ?>
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <?php if ($itemImage) : ?><img src="<?php echo esc_url($itemImage); ?>" alt="<?php echo esc_attr($itemTitle); ?>" class="h-48 w-full object-cover" loading="lazy" /><?php endif; ?>
                <div class="p-5">
                    <?php if ($itemIcon) : ?><span aria-hidden="true" class="mb-3 grid h-10 w-10 place-items-center rounded-xl bg-emerald-50 text-sm font-black text-emerald-700"><?php echo esc_html($itemIcon); ?></span><?php endif; ?>
                    <?php if ($itemTitle) : ?><h3 class="text-base font-black leading-7 text-slate-900"><?php echo esc_html($itemTitle); ?></h3><?php endif; ?>
                    <?php if ($itemDescription) : ?><p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600"><?php echo esc_html($itemDescription); ?></p><?php endif; ?>
                    <?php if ($itemUrl && !empty($itemLink['label'])) : ?><a href="<?php echo esc_url($itemUrl); ?>" <?php echo !empty($itemLink['target_blank']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> class="mt-4 inline-flex text-xs font-extrabold text-emerald-700 hover:text-emerald-900"><?php echo esc_html($itemLink['label']); ?></a><?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div><?php endif; ?>
</section>
