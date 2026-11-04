<?php
declare(strict_types=1);
if (!defined('ABSPATH')) exit;
?>
<div data-mobile-menu class="fixed inset-0 z-[90] lg:hidden hidden"><div data-mobile-backdrop class="absolute inset-0 bg-slate-950/50"></div><aside data-mobile-sidebar class="absolute right-0 top-0 h-full w-[86%] max-w-sm overflow-y-auto bg-white p-5 shadow-2xl"><div class="mb-5 flex items-center justify-between"><strong class="text-sm font-black">منوی دسترسی</strong><button type="button" data-mobile-close class="grid h-9 w-9 place-items-center rounded-xl border" aria-label="بستن">×</button></div>
<div class="mb-4 flex gap-2"><button type="button" data-search-modal-open class="flex-1 rounded-xl border px-3 py-2.5 text-xs font-extrabold">جستجو</button><button type="button" data-cart-modal-open class="flex-1 rounded-xl border px-3 py-2.5 text-xs font-extrabold">سبد خرید</button></div>
<?php
if (has_nav_menu('mobile-menu')) {
    wp_nav_menu(['theme_location' => 'mobile-menu', 'container' => false, 'items_wrap' => '%3$s', 'fallback_cb' => false, 'walker' => new \ErfanSanat\Theme\Navigation\MobileMenuWalker()]);
} else {
    $tax = taxonomy_exists('product_cat') ? 'product_cat' : 'category';
    $terms = taxonomy_exists($tax) ? get_terms(['taxonomy' => $tax, 'hide_empty' => false, 'number' => 10]) : [];
    if (!is_wp_error($terms) && $terms) {
        echo '<div><button type="button" data-mobile-acc-btn class="flex w-full justify-between rounded-xl px-3 py-3 text-right text-sm font-extrabold"><span>محصولات</span><span data-mobile-acc-icon>⌄</span></button><div data-mobile-acc-panel class="hidden pr-4">';
        foreach (array_values($terms) as $i => $term) { $l = get_term_link($term); echo '<button type="button" data-mobile-prod-tab="' . esc_attr((string) $i) . '" class="block w-full rounded-lg px-2 py-2 text-right text-xs font-bold">' . esc_html($term->name) . '</button><div data-mobile-prod-panel="' . esc_attr((string) $i) . '" class="hidden pr-3"><a class="text-xs text-emerald-700" href="' . esc_url(is_wp_error($l) ? '#' : $l) . '">مشاهده دسته</a></div>'; }
        echo '</div></div>';
    }
    foreach (['project_type' => 'نوع پروژه', 'city' => 'شهر پروژه'] as $tax2 => $label) {
        $terms2 = taxonomy_exists($tax2) ? get_terms(['taxonomy' => $tax2, 'hide_empty' => false]) : [];
        if (is_wp_error($terms2) || !$terms2) continue;
        echo '<div><button type="button" data-mobile-acc-btn class="flex w-full justify-between rounded-xl px-3 py-3 text-right text-sm font-extrabold"><span>' . esc_html($label) . '</span><span data-mobile-acc-icon>⌄</span></button><div data-mobile-acc-panel class="hidden pr-4">';
        foreach ($terms2 as $term) { $l = get_term_link($term); echo '<a data-mobile-proj-tab="' . esc_attr($term->slug) . '" class="block rounded-lg px-2 py-2 text-xs font-bold" href="' . esc_url(is_wp_error($l) ? '#' : $l) . '">' . esc_html($term->name) . '</a>'; }
        echo '</div></div>';
    }
    echo '<a class="block rounded-xl px-3 py-3 text-sm font-bold" href="' . esc_url(home_url('/projects/')) . '">پروژه‌ها</a><a class="block rounded-xl px-3 py-3 text-sm font-bold" href="' . esc_url(home_url('/contact/')) . '">تماس با ما</a>';
}
?>
</aside></div>
