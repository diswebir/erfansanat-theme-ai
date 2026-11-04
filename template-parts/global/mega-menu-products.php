<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
$esTax = taxonomy_exists('product_cat') ? 'product_cat' : (taxonomy_exists('category') ? 'category' : '');
$esTerms = $esTax !== '' ? get_terms(['taxonomy' => $esTax, 'hide_empty' => false, 'number' => 15]) : [];
if (is_wp_error($esTerms)) $esTerms = [];
?>
<div class="group static" data-mega-menu-products="true"><button type="button" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700" data-desktop-mega-trigger="products"><span>محصولات</span></button>
<div class="absolute inset-x-4 top-full z-50 mx-auto max-w-[780px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100" data-desktop-mega-panel="products"><div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]"><div class="grid grid-cols-12"><div class="col-span-5 border-l border-slate-100 bg-slate-50/70 p-3.5"><div class="mb-2 px-2 text-[10.5px] font-extrabold text-slate-400">دسته‌بندی تولیدات</div><div class="space-y-1">
<?php foreach (array_values($esTerms) as $i => $term) : ?><button type="button" class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right transition-all cursor-pointer <?php echo $i === 0 ? 'bg-white text-emerald-950 shadow-2xs ring-1 ring-emerald-100' : 'text-slate-600 hover:bg-white'; ?>" data-mega-prod-tab="<?php echo esc_attr((string) $i); ?>"><?php echo esc_html($term->name); ?></button><?php endforeach; ?>
</div></div><div class="col-span-7 p-3.5">
<?php foreach (array_values($esTerms) as $i => $term) : $link = get_term_link($term); ?>
<div data-mega-prod-panel="<?php echo esc_attr((string) $i); ?>" class="<?php echo $i === 0 ? '' : 'hidden'; ?>"><a class="block rounded-xl px-3 py-2.5 font-extrabold text-emerald-800 hover:bg-emerald-50" href="<?php echo esc_url(is_wp_error($link) ? '#' : $link); ?>"><?php echo esc_html($term->name); ?></a><p class="px-3 text-xs leading-6 text-slate-500"><?php echo esc_html($term->description); ?></p></div>
<?php endforeach; ?>
</div></div></div></div></div>
