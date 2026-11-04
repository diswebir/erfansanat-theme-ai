<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Navigation;
final class MobileMenuWalker extends \Walker_Nav_Menu
{
    public function start_lvl(&$output, $depth = 0, $args = null): void { $output .= '<div class="hidden pr-4" data-mobile-acc-panel><ul class="space-y-1 py-2">'; }
    public function end_lvl(&$output, $depth = 0, $args = null): void { $output .= '</ul></div>'; }
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0): void {
        $hasChildren = in_array('menu-item-has-children', (array)($item->classes ?? []), true);
        if ($depth === 0 && $hasChildren) { $output .= '<div><button type="button" class="flex w-full items-center justify-between rounded-xl px-3 py-3 text-right text-sm font-extrabold" data-mobile-acc-btn><span>' . esc_html($item->title) . '</span><span data-mobile-acc-icon>⌄</span></button>'; return; }
        $output .= ($depth > 0 ? '<li>' : '') . '<a class="block rounded-xl px-3 py-3 text-sm font-bold text-slate-700" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
    }
    public function end_el(&$output, $item, $depth = 0, $args = null): void {
        $hasChildren = in_array('menu-item-has-children', (array)($item->classes ?? []), true);
        if ($depth === 0 && $hasChildren) $output .= '</div>';
        if ($depth > 0) $output .= '</li>';
    }
}
