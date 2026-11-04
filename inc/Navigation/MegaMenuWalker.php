<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Navigation;
final class MegaMenuWalker extends \Walker_Nav_Menu
{
    public function start_lvl(&$output, $depth = 0, $args = null): void {
        $key = sanitize_key((string)($GLOBALS['es_mega_current_key'] ?? 'menu'));
        $output .= '<div class="absolute inset-x-4 top-full z-50 mx-auto max-w-[780px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100" data-desktop-mega-panel="' . esc_attr($key) . '"><div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]"><ul class="grid gap-1 p-3.5">';
    }
    public function end_lvl(&$output, $depth = 0, $args = null): void { $output .= '</ul></div></div>'; }
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0): void {
        $hasChildren = in_array('menu-item-has-children', (array)($item->classes ?? []), true);
        if ($depth === 0) {
            $key = sanitize_key((string)($item->post_name ?: $item->title));
            $GLOBALS['es_mega_current_key'] = $key;
            if ($hasChildren) { $output .= '<div class="group static" data-mega-menu-item="' . esc_attr($key) . '"><button type="button" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700" data-desktop-mega-trigger="' . esc_attr($key) . '"><span>' . esc_html($item->title) . '</span></button>'; return; }
            $output .= '<a class="rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>'; return;
        }
        $output .= '<li><a class="flex w-full items-center gap-2.5 rounded-xl px-3 py-2.5 text-right transition-all hover:bg-emerald-50" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
    }
    public function end_el(&$output, $item, $depth = 0, $args = null): void {
        $hasChildren = in_array('menu-item-has-children', (array)($item->classes ?? []), true);
        if ($depth === 0 && $hasChildren) $output .= '</div>';
        if ($depth > 0) $output .= '</li>';
    }
}
