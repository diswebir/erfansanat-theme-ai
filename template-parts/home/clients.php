<?php
$clientFields = isset($section['fields']) && is_array($section['fields']) ? $section['fields'] : [];
$clientSectionTitle = trim((string) ($clientFields['title'] ?? '')) ?: 'مشتریان و سازمان‌های همکار عرفان صنعت';
$clientSectionDescription = trim((string) ($clientFields['description'] ?? ''));
$clientRows = isset($clientFields['logos']) && is_array($clientFields['logos']) ? $clientFields['logos'] : [];
?>
<section aria-label="مشتریان و سازمان‌های همکار عرفان صنعت" class="mx-auto max-w-[1300px] px-4 py-12 sm:px-6 sm:py-16 lg:px-8"><div class="flex max-w-2xl flex-col gap-3 items-center text-center mx-auto"><div custom="0"><div class="inline-flex items-center gap-2 text-xs font-extrabold tracking-wide text-emerald-700 sm:text-[13px]"><span aria-hidden="true" class="h-3.5 w-1 rounded-full bg-emerald-600"></span><span>شبکه گسترده کارفرمایان</span></div></div><div custom="0.06"><h2 class="text-2xl font-black leading-[1.32] tracking-tight text-slate-900 [text-wrap:balance] sm:text-3xl lg:text-[2.25rem]"><?php echo esc_html($clientSectionTitle); ?></h2></div><div custom="0.12"><p class="text-sm leading-7 font-medium text-slate-600 sm:text-[15px] sm:leading-8"><?php echo esc_html($clientSectionDescription); ?></p></div></div><div class="mt-8 sm:mt-10 grid grid-cols-2 gap-3.5 sm:grid-cols-3 lg:grid-cols-6"><?php foreach ($clientRows as $clientIndex => $client) :
    if (!is_array($client)) { continue; }
    $clientName = trim((string) ($client['title'] ?? ''));
    if ('' === $clientName) { continue; }
    $clientDescription = trim((string) ($client['description'] ?? ''));
    $clientImage = trim((string) ($client['image'] ?? ''));
    $clientLink = is_array($client['link'] ?? null) ? $client['link'] : [];
    $clientUrl = trim((string) ($clientLink['url'] ?? ''));
    $clientTarget = !empty($clientLink['target_blank']) ? ' target="_blank" rel="noopener noreferrer"' : '';
    $clientTag = '' !== $clientUrl ? 'a' : 'div';
    ?>
<<?php echo $clientTag; ?><?php if ('a' === $clientTag) : ?> href="<?php echo esc_url($clientUrl); ?>"<?php echo $clientTarget; ?><?php endif; ?> class="card-light flex flex-col items-start justify-between rounded-2xl p-4 text-right">
<div class="flex w-full items-center justify-between"><span class="grid h-9 w-9 place-items-center overflow-hidden rounded-xl border border-emerald-100 bg-emerald-50/70 text-emerald-700">
<?php if ($clientImage) : ?><img src="<?php echo esc_url($clientImage); ?>" alt="<?php echo esc_attr($clientName); ?>" loading="lazy" class="h-full w-full object-contain" /><?php else : ?><span aria-hidden="true" class="text-lg font-black">E</span><?php endif; ?>
</span><span class="font-mono text-[11px] font-bold text-slate-400 tabular-nums"><?php echo esc_html(sprintf('%02d', $clientIndex + 1)); ?></span></div>
<div class="mt-3"><div class="text-xs sm:text-[13px] font-extrabold text-slate-900"><?php echo esc_html($clientName); ?></div><?php if ($clientDescription) : ?><div class="mt-0.5 line-clamp-2 text-[10.5px] font-medium text-slate-500"><?php echo esc_html($clientDescription); ?></div><?php endif; ?></div>
</<?php echo $clientTag; ?>>
<?php endforeach; ?></div></section>