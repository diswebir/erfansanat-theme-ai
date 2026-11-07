<?php
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$title = trim((string) ($fields['title'] ?? ''));
$content = trim((string) ($fields['content'] ?? ''));
$image = trim((string) ($fields['image'] ?? ''));
if ('' === $title && '' === wp_strip_all_tags($content) && '' === $image) { return; }
?>
<section class="mx-auto max-w-6xl px-5 py-12 lg:px-8 lg:py-16" data-editable-section="content">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-8">
        <?php if ($title) : ?><h2 class="text-2xl font-black leading-relaxed text-slate-950"><?php echo esc_html($title); ?></h2><?php endif; ?>
        <?php if ($image) : ?><img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" class="my-5 max-h-[28rem] w-full rounded-2xl object-cover" loading="lazy" /><?php endif; ?>
        <?php if ('' !== wp_strip_all_tags($content)) : ?><div class="prose prose-slate mt-4 max-w-none text-sm leading-8"><?php echo wp_kses_post(wpautop($content)); ?></div><?php endif; ?>
    </div>
</section>
