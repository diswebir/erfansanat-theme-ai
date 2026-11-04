<?php
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$title = trim((string) ($fields['title'] ?? ''));
$description = trim((string) ($fields['description'] ?? ''));
$items = isset($fields['items']) && is_array($fields['items']) ? array_values(array_filter($fields['items'], 'is_array')) : [];
$items = array_values(array_filter($items, static function (array $item): bool { return '' !== trim((string) ($item['question'] ?? '') . ($item['answer'] ?? '')); }));
if ('' === $title && '' === $description && !$items) { return; }
?>
<section class="mx-auto max-w-5xl px-5 py-12 lg:px-8 lg:py-16" data-editable-section="faq"><header class="mb-6"><?php if ($title) : ?><h2 class="text-2xl font-black leading-relaxed text-slate-950"><?php echo esc_html($title); ?></h2><?php endif; ?><?php if ($description) : ?><p class="mt-2 text-sm leading-7 text-slate-600"><?php echo esc_html($description); ?></p><?php endif; ?></header>
    <?php if ($items) : ?><div class="space-y-3"><?php foreach ($items as $index => $item) : $question = trim((string) ($item['question'] ?? '')); $answer = trim((string) ($item['answer'] ?? '')); if ('' === $question || '' === $answer) { continue; } ?><details class="group rounded-2xl border border-slate-200 bg-white p-4 shadow-sm" <?php echo 0 === $index ? 'open' : ''; ?>><summary class="cursor-pointer list-none text-sm font-extrabold text-slate-900"><span class="flex items-center justify-between gap-4"><span><?php echo esc_html($question); ?></span><span aria-hidden="true" class="text-emerald-700">＋</span></span></summary><div class="mt-3 border-t border-slate-100 pt-3 text-sm leading-7 text-slate-600"><?php echo wp_kses_post(wpautop($answer)); ?></div></details><?php endforeach; ?></div><?php endif; ?>
</section>
