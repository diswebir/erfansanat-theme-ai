<?php
/**
 * LandingWalker — منوی اسکرول صفحات لندینگ
 *
 * آیتم‌های منوی «landing» (یا fallback آرگومان) به انکرهای سکشن تبدیل می‌شوند:
 *   <a href="#es-sec-{slug}" data-landing-scroll="{slug}" class="{class}">{title}</a>
 * JS میرور اسکرول نرم + active-on-scroll را مدیریت می‌کند (هوک data-landing-scroll حفظ).
 * آیتم فعال اولیه: es-active یا کلاس is_active=true.
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Nav;

final class LandingWalker {

    /** قالب آیتم لندینگ */
    private const ITEM = '<a href="#es-sec-{ES_SLUG}" data-landing-scroll="{ES_SLUG}" class="{ES_CLASS}">{ES_LABEL}</a>';

    /** wrap پیش‌فرض (قالب لندینگ موقعیت را override می‌کند) */
    private const WRAP = '<nav aria-label="فهرست بخش‌ها" class="flex items-center gap-4">{ES_ITEMS}</nav>';

    /**
     * رندر منوی لندینگ
     *
     * @param list<array{label:string,slug:string,active?:bool}> $fallback
     */
    public static function render(array $fallback = [], bool $with_wrap = true): string {
        $links = self::resolve($fallback);
        if ($links === []) {
            return '';
        }
        $items = '';
        foreach ($links as $i => $link) {
            $slug = sanitize_key((string) ($link['slug'] ?? ''));
            if ($slug === '') {
                continue;
            }
            $class = (string) ($link['class'] ?? 'text-xs font-extrabold text-slate-500 transition-colors hover:text-emerald-700');
            if (!empty($link['active']) || $i === 0) {
                $class .= ' is-active text-emerald-700';
            }
            $items .= strtr(self::ITEM, [
                '{ES_SLUG}'  => $slug,
                '{ES_LABEL}' => esc_html((string) ($link['label'] ?? $slug)),
                '{ES_CLASS}' => esc_attr(trim($class)),
            ]);
        }
        if (!$with_wrap) {
            return $items;
        }
        return $items === '' ? '' : str_replace('{ES_ITEMS}', $items, self::WRAP);
    }

    /**
     * منبع: منوی «landing» → fallback (آیتم‌های دارای target سکشن = #slug در URL منو)
     *
     * @param list<array{label:string,slug:string,active?:bool}> $fallback
     * @return list<array{label:string,slug:string,active?:bool,class?:string}>
     */
    public static function resolve(array $fallback = []): array {
        $items = wp_get_nav_menu_items('landing');
        if (!is_array($items)) {
            return $fallback;
        }
        $out = [];
        foreach ($items as $item) {
            if ((int) ($item->menu_item_parent ?? 0) !== 0) {
                continue;
            }
            $url = (string) $item->url;
            $slug = '';
            if (preg_match('/#([A-Za-z0-9_-]+)/', $url, $m)) {
                $slug = $m[1];
            }
            if ($slug === '') {
                continue;
            }
            $out[] = ['label' => (string) $item->title, 'slug' => $slug];
        }
        return $out;
    }
}
