<?php
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$title = trim((string) ($fields['title'] ?? ''));
$description = trim((string) ($fields['description'] ?? ''));
$link = isset($fields['link']) && is_array($fields['link']) ? $fields['link'] : [];
if ('' === $title && '' === $description && empty($link['url'])) { return; }
?>
<section id="contact" class="mx-auto max-w-7xl scroll-mt-24 px-5 py-12 lg:px-8" data-editable-section="contact"><div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm sm:p-9"><?php if ($title) : ?><h2 class="text-2xl font-black text-slate-950"><?php echo esc_html($title); ?></h2><?php endif; ?><?php if ($description) : ?><p class="mt-2 max-w-3xl text-sm leading-7 text-slate-600"><?php echo esc_html($description); ?></p><?php endif; ?><?php if (!empty($link['url']) && !empty($link['label'])) : ?><a href="<?php echo esc_url($link['url']); ?>" <?php echo !empty($link['target_blank']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> class="mt-5 inline-flex rounded-xl bg-emerald-700 px-5 py-3 text-xs font-extrabold text-white"><?php echo esc_html($link['label']); ?></a><?php endif; ?></div></section>
