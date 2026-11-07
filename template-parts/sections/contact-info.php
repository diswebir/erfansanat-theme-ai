<?php
$fields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$title = trim((string) ($fields['title'] ?? ''));
$description = trim((string) ($fields['description'] ?? ''));
$phone = trim((string) ($fields['phone'] ?? ''));
$salesPhone = trim((string) ($fields['sales_phone'] ?? ''));
$email = sanitize_email((string) ($fields['email'] ?? ''));
$address = trim((string) ($fields['address'] ?? ''));
$hours = trim((string) ($fields['hours'] ?? ''));
$link = isset($fields['link']) && is_array($fields['link']) ? $fields['link'] : [];
if ('' === $title && '' === $description && '' === $phone && '' === $salesPhone && '' === $email && '' === $address && '' === $hours) { return; }
?>
<section class="mx-auto max-w-7xl px-5 py-12 lg:px-8 lg:py-16" data-editable-section="contact-info"><header class="mb-6"><?php if ($title) : ?><h2 class="text-2xl font-black text-slate-950"><?php echo esc_html($title); ?></h2><?php endif; ?><?php if ($description) : ?><p class="mt-2 max-w-3xl text-sm leading-7 text-slate-600"><?php echo esc_html($description); ?></p><?php endif; ?></header><dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <?php foreach (['تلفن' => $phone, 'تلفن فروش' => $salesPhone, 'ایمیل' => $email, 'نشانی' => $address, 'ساعات کاری' => $hours] as $label => $value) : if ('' === $value) { continue; } ?><div class="rounded-2xl border border-slate-200 bg-white p-5"><dt class="text-xs font-bold text-slate-500"><?php echo esc_html($label); ?></dt><dd class="mt-2 text-sm font-extrabold leading-7 text-slate-900"><?php if ('ایمیل' === $label) : ?><a href="<?php echo esc_url('mailto:' . $email); ?>" class="hover:text-emerald-700"><?php echo esc_html($email); ?></a><?php elseif (in_array($label, ['تلفن', 'تلفن فروش'], true)) : ?><a href="<?php echo esc_url('tel:' . preg_replace('/[^0-9+]/', '', $value)); ?>" class="hover:text-emerald-700"><?php echo esc_html($value); ?></a><?php else : echo esc_html($value); endif; ?></dd></div><?php endforeach; ?>
</dl><?php if (!empty($link['url']) && !empty($link['label'])) : ?><a href="<?php echo esc_url($link['url']); ?>" <?php echo !empty($link['target_blank']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?> class="mt-5 inline-flex rounded-xl bg-emerald-700 px-5 py-3 text-xs font-extrabold text-white"><?php echo esc_html($link['label']); ?></a><?php endif; ?></section>
