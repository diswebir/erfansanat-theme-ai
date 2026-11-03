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
                '<button type="button" data-desktop-mega-trigger="%s" aria-expanded="false" class="mega-trigger">%s</button>',
                esc_attr($slug),
                esc_html($item->title)
            );
        } else {
            $output .= sprintf(
                '<a href="%s" class="menu-link">%s</a>',
                esc_url($item->url),
                esc_html($item->title)
            );
        }
    }

    public function end_el(&$output, $item, $depth = 0, $args = null): void
    {
        // panels are closed via end_lvl; nothing per-item
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
