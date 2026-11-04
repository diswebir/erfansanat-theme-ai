<?php
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$title = trim((string) ($fields['title'] ?? ''));
$description = trim((string) ($fields['description'] ?? ''));
$link = isset($fields['link']) && is_array($fields['link']) ? $fields['link'] : [];
if ('' === $title && '' === $description && empty($link['url'])) { return; }
?>
<section class="mx-auto max-w-7xl px-5 py-10 lg:px-8" data-editable-section="cta"><div class="rounded-3xl bg-gradient-to-l from-emerald-900 to-emerald-700 p-6 text-white sm:p-9"><?php if ($title) : ?><h2 class="text-2xl font-black leading-relaxed"><?php echo esc_html($title); ?></h2><?php endif; ?><?php if ($description) : ?><p class="mt-2 max-w-3xl text-sm leading-7 text-emerald-50"><?php echo esc_html($description); ?></p><?php endif; ?><?php if (!empty($link['url']) && !empty($link['label'])) : ?><a href="<?php echo esc_url($link['url']); ?>" <?php echo !empty($link['target_blank']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> class="mt-5 inline-flex rounded-xl bg-white px-5 py-3 text-xs font-extrabold text-emerald-900"><?php echo esc_html($link['label']); ?></a><?php endif; ?></div></section>
