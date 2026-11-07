<?php
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$title = trim((string) ($fields['title'] ?? ''));
$description = trim((string) ($fields['description'] ?? ''));
$images = isset($fields['images']) && is_array($fields['images']) ? array_values(array_filter($fields['images'], 'is_array')) : [];
$images = array_values(array_filter($images, static function (array $item): bool { return '' !== trim((string) ($item['image'] ?? '')); }));
if ('' === $title && '' === $description && !$images) { return; }
?>
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8" data-editable-section="gallery"><?php if ($title) : ?><h2 class="text-2xl font-black text-slate-950"><?php echo esc_html($title); ?></h2><?php endif; ?><?php if ($description) : ?><p class="mt-2 text-sm leading-7 text-slate-600"><?php echo esc_html($description); ?></p><?php endif; ?><?php if ($images) : ?><div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3"><?php foreach ($images as $item) : $image = trim((string) ($item['image'] ?? '')); $caption = trim((string) ($item['caption'] ?? '')); ?><figure class="overflow-hidden rounded-2xl border border-slate-200 bg-white"><?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($caption); ?>" class="h-56 w-full object-cover" loading="lazy" /><?php endif; ?><?php if ($caption) : ?><figcaption class="p-3 text-xs font-semibold text-slate-600"><?php echo esc_html($caption); ?></figcaption><?php endif; ?></figure><?php endforeach; ?></div><?php endif; ?></section>
