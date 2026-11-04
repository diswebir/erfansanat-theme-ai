<?php
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$title = trim((string) ($fields['title'] ?? ''));
$mapUrl = trim((string) ($fields['map_url'] ?? ''));
$mapEmbedUrl = trim((string) ($fields['map_embed_url'] ?? ''));
if ('' === $title && '' === $mapUrl && '' === $mapEmbedUrl) { return; }
?>
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8" data-editable-section="location"><div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"><?php if ($title) : ?><h2 class="text-xl font-black text-slate-950"><?php echo esc_html($title); ?></h2><?php endif; ?><?php $safeMapUrl = $mapUrl ?: $mapEmbedUrl; if ($safeMapUrl) : ?><a href="<?php echo esc_url($safeMapUrl); ?>" target="_blank" rel="noopener noreferrer" class="mt-4 inline-flex rounded-xl bg-emerald-700 px-5 py-3 text-xs font-extrabold text-white"><?php echo esc_html__('مشاهده موقعیت روی نقشه', 'erfansanat-theme'); ?></a><?php endif; ?></div></section>
