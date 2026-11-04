<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esGroups = ['type' => ['label' => 'بر اساس نوع پروژه', 'tax' => 'project_type'], 'city' => ['label' => 'بر اساس شهر', 'tax' => 'city']];
?>
<div class="group static" data-mega-menu-projects="true"><button type="button" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700" data-desktop-mega-trigger="projects"><span>پروژه‌ها</span></button>
<div class="absolute inset-x-4 top-full z-50 mx-auto max-w-[780px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100" data-desktop-mega-panel="projects"><div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]"><div class="p-3.5"><div class="mb-3 flex gap-2">
<?php foreach ($esGroups as $key => $g) : ?><button type="button" class="rounded-xl px-3 py-2 text-xs font-extrabold" data-mega-proj-tab="<?php echo esc_attr($key); ?>"><?php echo esc_html($g['label']); ?></button><?php endforeach; ?>
</div>
<?php foreach ($esGroups as $key => $g) : $terms = taxonomy_exists($g['tax']) ? get_terms(['taxonomy' => $g['tax'], 'hide_empty' => false]) : []; if (is_wp_error($terms)) $terms = []; ?>
<div data-mega-proj-panel="<?php echo esc_attr($key); ?>" class="<?php echo $key === 'type' ? '' : 'hidden'; ?>"><ul class="grid grid-cols-2 gap-1"><?php foreach ($terms as $term) : $l = get_term_link($term); ?><li><a class="block rounded-xl px-3 py-2.5 text-sm font-bold hover:bg-emerald-50" href="<?php echo esc_url(is_wp_error($l) ? '#' : $l); ?>"><?php echo esc_html($term->name); ?></a></li><?php endforeach; ?></ul></div>
<?php endforeach; ?>
</div></div></div></div>
