<?php
/**
 * MegaMenuWalker — renders the 4 registered menu locations with markup
 * matching the static mirror's hooks (data-desktop-mega-trigger, panels,
 * mobile accordion data-mobile-acc-*). All output escaped.
 *
 * @package ErfanSanat\Theme\Nav
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Nav;

if (!defined('ABSPATH')) {
    exit;
}

class MegaMenuWalker extends \Walker_Nav_Menu
{
    public function start_lvl(&$output, $depth = 0, $args = null): void
    {
        $output .= '<div class="mega-panel" data-mega-panel="true" data-desktop-mega-panel="true">';
        $output .= '<button type="button" data-mega-tab="true" class="mega-tab"></button>';
    }

    public function end_lvl(&$output, $depth = 0, $args = null): void
    {
        $output .= '</div>';
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0): void
    {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $hasChildren = in_array('menu-item-has-children', $classes, true);
        $slug = sanitize_title($item->slug ?: $item->post_name ?: $item->ID);

        if ($depth === 0 && $hasChildren) {
            $output .= sprintf(
                '<div class="group static" data-mega-menu-products="true"><button type="button" data-desktop-mega-trigger="%s" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap bg-emerald-50 text-emerald-800"><span>%s</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg></button>',
                esc_attr($slug),
                esc_html($item->title)
            );
        } else {
            $output .= sprintf(
                '<a href="%s" class="rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700">%s</a>',
                esc_url($item->url),
                esc_html($item->title)
            );
        }
    }

    public function end_el(&$output, $item, $depth = 0, $args = null): void
    {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        if ($depth === 0 && in_array('menu-item-has-children', $classes, true)) {
            $output .= '</div>';
        }
    }
}

/**
 * Mobile accordion walker — emits data-mobile-acc-btn / data-mobile-acc-panel.
 */
class MobileMenuWalker extends \Walker_Nav_Menu
{
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0): void
    {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        if ($depth === 0 && in_array('menu-item-has-children', $classes, true)) {
            $output .= sprintf(
                '<button type="button" data-mobile-acc-btn="true" class="mobile-acc-btn">%s</button><div data-mobile-acc-panel="true" class="mobile-acc-panel">',
                esc_html($item->title)
            );
        } else {
            $output .= sprintf(
                '<a href="%s" class="mobile-link">%s</a>',
                esc_url($item->url),
                esc_html($item->title)
            );
        }
    }

    public function end_el(&$output, $item, $depth = 0, $args = null): void
    {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        if ($depth === 0 && in_array('menu-item-has-children', $classes, true)) {
            $output .= '</div>';
        }
    }
}
