<?php
/**
 * Context-sensitive walkers for the theme's desktop and mobile navigation.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme\Navigation;

defined('ABSPATH') || exit;

/**
 * Renders nested WordPress menus with the selectors consumed by main.js.
 */
final class Walker extends \Walker_Nav_Menu
{
    /** @var string */
    private $context;

    /** @var array<int, string> */
    private $panelKeys = [];

    /** @var array<int, string> */
    private $panelKinds = [];

    /** @var string */
    private $rootKind = 'generic';

    /** @var int */
    private $productTabIndex = 0;

    /** @var array<int, bool> */
    private $childrenByDepth = [];

    /** @var array<int, bool> */
    private $panelDefaults = [];

    /**
     * @param string $context desktop, mobile, products, or landing.
     */
    public function __construct(string $context = 'desktop')
    {
        $this->context = $context;
    }

    public function start_lvl(&$output, $depth = 0, $args = null): void
    {
        $indent = str_repeat("\t", (int) $depth);
        if ('mobile' === $this->context) {
            $key = $this->panelKeys[$depth] ?? '';
            $output .= $indent . '<div data-mobile-acc-panel="' . esc_attr($key) . '" class="hidden flex-col gap-1 border-t border-slate-100 bg-slate-50/50 p-2">';
            return;
        }
        if ('desktop' === $this->context && 0 === (int) $depth) {
            $key = $this->panelKeys[0] ?? '';
            $output .= $indent . '<div data-desktop-mega-panel="' . esc_attr($key) . '" class="absolute inset-x-4 top-full z-50 mx-auto max-w-[780px] pt-2.5 transition-all duration-200 lg:inset-x-8 invisible translate-y-1 opacity-0 group-hover:visible group-hover:translate-y-0 group-hover:opacity-100"><div class="overflow-hidden rounded-3xl border border-slate-200/90 bg-white p-4 shadow-[0_24px_60px_-18px_rgba(15,23,42,0.22)]">';
            return;
        }
        if ('desktop' === $this->context && 1 === (int) $depth) {
            $key = $this->panelKeys[1] ?? '';
            $kind = $this->panelKinds[1] ?? 'generic';
            $attribute = 'projects' === $kind ? 'data-mega-proj-panel' : 'data-mega-prod-panel';
            $visible = !empty($this->panelDefaults[1]) ? 'block' : 'hidden';
            $output .= $indent . '<div ' . $attribute . '="' . esc_attr($key) . '" class="' . esc_attr($visible) . ' space-y-1.5">';
            return;
        }

        $class = 'landing' === $this->context
            ? 'class="flex flex-wrap items-center gap-3"'
            : 'class="space-y-1 border-r border-emerald-100 pr-2"';
        $output .= $indent . '<ul ' . $class . '>';
    }

    public function end_lvl(&$output, $depth = 0, $args = null): void
    {
        if ('mobile' === $this->context || ('desktop' === $this->context && (int) $depth <= 1)) {
            $output .= str_repeat("\t", (int) $depth) . '</div>';
            return;
        }
        $output .= str_repeat("\t", (int) $depth) . '</ul>';
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $current_object_id = 0): void
    {
        $depth = (int) $depth;
        $title = esc_html(wp_strip_all_tags((string) $item->title));
        $url = esc_url((string) $item->url);
        $hasChildren = !empty($this->has_children);
        $this->childrenByDepth[$depth] = $hasChildren;
        $key = 'menu-' . absint($item->ID);
        $target = !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $rel = !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';

        if ('mobile' === $this->context) {
            if ($hasChildren) {
                $this->panelKeys[$depth] = $key;
                $output .= '<div class="overflow-hidden rounded-xl border border-slate-200/80 bg-white"><button type="button" data-mobile-acc-btn="' . esc_attr($key) . '" aria-expanded="false" class="flex w-full items-center justify-between px-3.5 py-3 text-right cursor-pointer hover:bg-slate-50"><span class="text-xs font-black text-slate-900">' . $title . '</span><svg data-mobile-acc-icon="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 text-slate-500 transition-transform duration-200" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg></button>';
            } else {
                $output .= '<a href="' . $url . '"' . $target . $rel . ' class="flex items-center justify-between rounded-xl border border-slate-200/80 px-3.5 py-3 text-xs font-black text-slate-800 transition-colors hover:border-emerald-200 hover:bg-emerald-50"><span>' . $title . '</span><span aria-hidden="true" class="text-emerald-700">←</span></a>';
            }
            return;
        }

        if ('products' === $this->context || 'landing' === $this->context) {
            $class = 'landing' === $this->context
                ? 'inline-flex rounded-xl px-3 py-2 text-xs font-bold text-slate-700 hover:bg-emerald-50 hover:text-emerald-800'
                : 'text-xs font-bold text-slate-700 transition-colors hover:text-emerald-800';
            $output .= '<li><a href="' . $url . '"' . $target . $rel . ' class="' . esc_attr($class) . '">' . $title . '</a>';
            return;
        }

        if (0 === $depth) {
            $this->productTabIndex = 0;
            if ($hasChildren) {
                $this->panelKeys[0] = $key;
                $this->rootKind = self::detect_root_kind($item);
                $output .= '<div class="group static" data-mega-menu-custom="' . esc_attr($key) . '"><button type="button" data-desktop-mega-trigger="' . esc_attr($key) . '" aria-expanded="false" class="inline-flex items-center gap-1.5 rounded-xl px-3 py-2 text-[13px] font-extrabold transition-all cursor-pointer whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700"><span>' . $title . '</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5 transition-transform duration-200 group-hover:rotate-180 text-slate-400" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>';
            } else {
                $output .= '<a href="' . $url . '"' . $target . $rel . ' class="rounded-xl px-3 py-2 text-[13px] font-extrabold transition-colors whitespace-nowrap text-slate-700 hover:bg-slate-50 hover:text-emerald-700">' . $title . '</a>';
            }
            return;
        }

        if (1 === $depth && $hasChildren) {
            $key = 'menu-group-' . absint($item->ID);
            $this->panelKeys[1] = $key;
            $this->panelKinds[1] = $this->rootKind;
            if ('projects' === $this->rootKind) {
                $this->panelDefaults[1] = 0 === $this->productTabIndex;
                $tabKey = self::project_tab_key($item);
                $this->panelKeys[1] = $tabKey;
                $this->productTabIndex++;
                $attribute = 'data-mega-proj-tab="' . esc_attr($tabKey) . '"';
            } elseif ('products' === $this->rootKind) {
                $this->panelDefaults[1] = 0 === $this->productTabIndex;
                $tabKey = (string) $this->productTabIndex++;
                $this->panelKeys[1] = $tabKey;
                $attribute = 'data-mega-prod-tab="' . esc_attr($tabKey) . '"';
            } else {
                $attribute = 'data-desktop-submenu-tab="' . esc_attr($key) . '"';
            }
            $output .= '<section class="rounded-xl border border-slate-100 bg-slate-50/60 p-2"><button type="button" ' . $attribute . ' aria-expanded="true" class="mb-1 w-full rounded-lg px-2 py-1.5 text-right text-xs font-extrabold text-slate-800 hover:bg-white">' . $title . '</button>';
            return;
        }

        if ($hasChildren) {
            $key = 'menu-group-' . absint($item->ID);
            $this->panelKeys[$depth] = $key;
            $output .= '<div class="space-y-1"><span class="block text-xs font-bold text-slate-700">' . $title . '</span>';
            return;
        }

        $output .= '<a href="' . $url . '"' . $target . $rel . ' class="flex items-center justify-between rounded-lg px-3 py-2 text-xs font-bold text-slate-700 transition-colors hover:bg-emerald-50 hover:text-emerald-800"><span>' . $title . '</span><span aria-hidden="true" class="text-slate-400">←</span></a>';
    }

    public function end_el(&$output, $item, $depth = 0, $args = null): void
    {
        $depth = (int) $depth;
        if ('mobile' === $this->context) {
            if (!empty($this->childrenByDepth[$depth])) {
                $output .= '</div>';
            }
            return;
        }
        if ('products' === $this->context || 'landing' === $this->context) {
            $output .= '</li>';
            return;
        }
        if (0 === $depth && !empty($this->childrenByDepth[$depth])) {
            $output .= '</div>';
        } elseif (1 === $depth && !empty($this->childrenByDepth[$depth])) {
            $output .= '</section>';
        } elseif ($depth > 1 && !empty($this->childrenByDepth[$depth])) {
            $output .= '</div>';
        }
    }

    /** Determine whether a root menu item is the products or projects panel. */
    private static function detect_root_kind($item): string
    {
        $url = strtolower((string) $item->url);
        $title = wp_strip_all_tags((string) $item->title);
        if (false !== strpos($url, 'project') || false !== strpos($title, 'پروژه')) {
            return 'projects';
        }
        if (false !== strpos($url, 'product') || false !== strpos($url, 'shop') || false !== strpos($title, 'محصول')) {
            return 'products';
        }
        return 'generic';
    }

    /** Get the stable JS mode key for a project taxonomy submenu. */
    private static function project_tab_key($item): string
    {
        if (isset($item->object) && 'city' === $item->object) {
            return 'city';
        }
        if (isset($item->object) && 'project_type' === $item->object) {
            return 'type';
        }
        $url = strtolower((string) $item->url);
        if (false !== strpos($url, 'city/')) {
            return 'city';
        }
        if (false !== strpos($url, 'project_type/')) {
            return 'type';
        }
        return 'group-' . absint($item->ID);
    }
}
