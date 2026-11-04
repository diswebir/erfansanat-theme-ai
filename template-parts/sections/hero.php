<?php
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$sectionPostId = isset($args['post_id']) ? absint($args['post_id']) : 0;
$postTitle = $sectionPostId ? get_the_title($sectionPostId) : '';
$eyebrow = trim((string) ($fields['eyebrow'] ?? ''));
$title = trim((string) ($fields['title'] ?? ''));
if ('' === $title || 'وقتی کیفیت و ماندگاری مهمه' === $title) {
    $title = $postTitle;
}
$description = trim((string) ($fields['description'] ?? ''));
$image = trim((string) ($fields['background_image'] ?? ''));
$link = isset($fields['primary_link']) && is_array($fields['primary_link']) ? $fields['primary_link'] : [];
$secondaryLink = isset($fields['secondary_link']) && is_array($fields['secondary_link']) ? $fields['secondary_link'] : [];
?>
<section class="relative overflow-hidden border-b border-emerald-100 bg-[#f6f8f7] pb-12 pt-12 lg:pb-16">
    <?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="" aria-hidden="true" class="absolute inset-0 h-full w-full object-cover opacity-[0.12]" loading="eager" /><?php endif; ?>
    <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-l from-white/90 via-white/85 to-emerald-50/70"></div>
    <div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
        <?php if ($eyebrow) : ?><p class="text-xs font-extrabold text-emerald-700"><?php echo esc_html($eyebrow); ?></p><?php endif; ?>
        <?php if ($title) : ?><h1 class="mt-2 max-w-4xl text-3xl font-black leading-relaxed text-slate-950 sm:text-4xl"><?php echo esc_html($title); ?></h1><?php endif; ?>
        <?php if ($description) : ?><p class="mt-3 max-w-3xl text-sm leading-7 text-slate-600"><?php echo esc_html($description); ?></p><?php endif; ?>
        <?php if (!empty($link['url']) || !empty($secondaryLink['url'])) : ?><div class="mt-6 flex flex-wrap gap-2.5"><?php foreach ([$link, $secondaryLink] as $heroLink) : if (empty($heroLink['url']) || empty($heroLink['label'])) { continue; } ?><a href="<?php echo esc_url($heroLink['url']); ?>" <?php echo !empty($heroLink['target_blank']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> class="rounded-xl bg-emerald-700 px-5 py-3 text-xs font-extrabold text-white hover:bg-emerald-800"><?php echo esc_html($heroLink['label']); ?></a><?php endforeach; ?></div><?php endif; ?>
    </div>
</section>
