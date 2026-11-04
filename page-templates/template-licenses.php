<?php
/** Template Name: مجوزها و افتخارات */
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
get_header();
$postId = (int) get_the_ID();
$defaults = [
    'licenses-hero' => ['title' => 'مجوزها و افتخارات عرفان صنعت', 'body' => 'گواهینامه‌ها و تقدیرنامه‌های ثبت‌شده در مسیر فعالیت مجموعه.', 'order' => 0],
    'licenses-certificates' => ['title' => 'گواهینامه‌های دانش‌بنیان و ISO', 'body' => '', 'items' => [], 'order' => 1],
    'licenses-honors' => ['title' => 'تقدیرنامه‌ها و افتخارات', 'body' => '', 'items' => [], 'order' => 2],
];
$byType = [];
foreach (es_get_sections($postId) as $section) if (isset($section['type'], $defaults[$section['type']])) $byType[$section['type']] = $section;
$sections = [];
foreach ($defaults as $type => $fields) {
    $section = $byType[$type] ?? ['type' => $type, 'enabled' => true, 'order' => $fields['order'], 'fields' => []];
    $section['fields'] = array_merge($fields, is_array($section['fields'] ?? null) ? $section['fields'] : []);
    $sections[] = $section;
}
usort($sections, static fn(array $a, array $b): int => (int) ($a['order'] ?? 0) <=> (int) ($b['order'] ?? 0));
?>
<main id="primary" class="site-main mx-auto max-w-7xl space-y-7 px-4 py-8 sm:px-6 lg:px-8">
<?php foreach ($sections as $section) : if (empty($section['enabled'])) continue; $type = (string) $section['type']; $fields = $section['fields']; ?>
<?php if ($type === 'licenses-hero') : ?><section data-license-section="hero" class="rounded-3xl bg-slate-900 p-7 text-white md:p-11"><p class="text-xs font-bold text-emerald-300">اعتماد و دستاورد</p><h1 class="mt-3 text-3xl font-black leading-relaxed md:text-5xl"><?php echo esc_html((string) $fields['title']); ?></h1><div class="mt-4 max-w-3xl text-sm leading-8 text-slate-200"><?php echo wp_kses_post((string) $fields['body']); ?></div></section>
<?php elseif (in_array($type, ['licenses-certificates', 'licenses-honors'], true)) : $kind = $type === 'licenses-certificates' ? 'certificates' : 'honors'; $items = is_array($fields['items'] ?? null) ? $fields['items'] : []; $validItems = []; foreach ($items as $item) { if (!is_array($item)) continue; $image = $item['image'] ?? ''; if (is_numeric($image) && (int) $image > 0) $image = wp_get_attachment_image_url((int) $image, 'large') ?: ''; else $image = esc_url((string) $image); if ($image === '') continue; $validItems[] = ['image' => $image, 'title' => (string) ($item['title'] ?? ''), 'alt' => (string) ($item['alt'] ?? $item['title'] ?? '')]; } ?>
<section data-license-section="<?php echo esc_attr($kind); ?>" class="rounded-3xl bg-white p-6 md:p-9"><header class="mb-6"><h2 class="text-2xl font-black text-slate-900"><?php echo esc_html((string) $fields['title']); ?></h2><?php if (trim((string) $fields['body']) !== '') : ?><p class="mt-3 text-sm leading-7 text-slate-600"><?php echo wp_kses_post((string) $fields['body']); ?></p><?php endif; ?></header>
<?php if ($validItems !== []) : $first = $validItems[0]; ?><div data-gallery-root="true" class="grid gap-4 md:grid-cols-[minmax(0,1fr)_140px]"><div class="relative overflow-hidden rounded-2xl bg-slate-100"><img data-gallery-main-img="true" src="<?php echo esc_url($first['image']); ?>" alt="<?php echo esc_attr($first['alt']); ?>" class="max-h-[560px] w-full cursor-zoom-in object-contain"/><button type="button" data-gallery-expand="true" class="absolute bottom-3 left-3 rounded-lg bg-slate-950/80 px-3 py-2 text-xs font-bold text-white">بزرگ‌نمایی مدرک</button></div><div class="flex gap-3 overflow-x-auto md:flex-col"><?php foreach ($validItems as $item) : ?><button type="button" data-gallery-thumb data-gallery-src="<?php echo esc_url($item['image']); ?>" class="relative shrink-0 overflow-hidden rounded-xl border border-transparent p-1"><img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['alt']); ?>" class="h-24 w-20 rounded-lg object-cover md:h-28 md:w-full"/><span data-gallery-thumb-ring class="pointer-events-none absolute inset-0 hidden rounded-xl border-2 border-emerald-500"></span><?php if ($item['title'] !== '') : ?><span class="mt-1 block text-[10px] font-bold leading-4 text-slate-700"><?php echo esc_html($item['title']); ?></span><?php endif; ?></button><?php endforeach; ?></div>
<div data-gallery-lightbox="true" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/95 p-4"><button type="button" data-gallery-lightbox-close="true" class="absolute right-5 top-5 rounded-full bg-white/10 px-4 py-3 text-white" aria-label="بستن">بستن</button><button type="button" data-gallery-lightbox-prev="true" class="absolute right-4 rounded-full bg-white/10 px-4 py-3 text-white" aria-label="قبلی">‹</button><img data-gallery-lightbox-img="true" src="<?php echo esc_url($first['image']); ?>" alt="<?php echo esc_attr($first['alt']); ?>" class="max-h-[85vh] max-w-[85vw] object-contain"/><button type="button" data-gallery-lightbox-next="true" class="absolute left-4 rounded-full bg-white/10 px-4 py-3 text-white" aria-label="بعدی">›</button><div class="absolute bottom-5 flex gap-2"><?php foreach ($validItems as $index => $item) : ?><button type="button" data-gallery-lightbox-dot="<?php echo esc_attr((string) $index); ?>" class="h-2 w-2 rounded-full bg-white/40" aria-label="مدرک <?php echo esc_attr((string) ($index + 1)); ?>"></button><?php endforeach; ?></div></div></div>
<?php else : ?><p class="rounded-2xl bg-slate-50 p-6 text-sm leading-7 text-slate-600">مدرکی برای نمایش در این بخش ثبت نشده است.</p><?php endif; ?></section><?php endif; ?>
<?php endforeach; ?>
</main>
<?php get_footer(); ?>
