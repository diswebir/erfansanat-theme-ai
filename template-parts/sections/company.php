<?php
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$title = trim((string) ($fields['title'] ?? ''));
$content = trim((string) ($fields['content'] ?? ''));
$image = trim((string) ($fields['image'] ?? ''));
$link = isset($fields['link']) && is_array($fields['link']) ? $fields['link'] : [];
if ('' === $title && '' === wp_strip_all_tags($content) && '' === $image && empty($link['url'])) { return; }
?>
<section class="mx-auto grid max-w-7xl items-center gap-8 px-5 py-12 lg:grid-cols-12 lg:px-8 lg:py-16" data-editable-section="company">
    <div class="<?php echo $image ? 'lg:col-span-7' : 'lg:col-span-12'; ?>">
        <?php if ($title) : ?><h2 class="text-2xl font-black leading-relaxed text-slate-950"><?php echo esc_html($title); ?></h2><?php endif; ?>
        <?php if ('' !== wp_strip_all_tags($content)) : ?><div class="prose prose-slate mt-4 max-w-none text-sm leading-8"><?php echo wp_kses_post(wpautop($content)); ?></div><?php endif; ?>
        <?php if (!empty($link['url']) && !empty($link['label'])) : ?><a href="<?php echo esc_url($link['url']); ?>" <?php echo !empty($link['target_blank']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> class="mt-5 inline-flex rounded-xl bg-emerald-700 px-5 py-3 text-xs font-extrabold text-white"><?php echo esc_html($link['label']); ?></a><?php endif; ?>
    </div>
    <?php if ($image) : ?><div class="lg:col-span-5"><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="w-full rounded-3xl border border-slate-200 object-cover shadow-sm" loading="lazy" /></div><?php endif; ?>
</section>
