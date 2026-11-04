<?php
/**
 * واکرهای منوی ساده — تولیدات شرکت (تک‌سطح) و منوی اسکرول لندینگ
 *
 * SimpleWalker: li/a با کلاس‌های میرور فوتر — جایگزین ستون‌های فوتر یا هر فهرست تک‌سطحی
 * LandingWalker: انکرهای data-landing-scroll به سکشن‌های قالب لندینگ (page-landing.php فاز ۷)
 * + active on scroll با JS میرور (هوک حفظ می‌شود)
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Nav;

final class SimpleWalker {

    /** قالب آیتم تک‌سطحی — عین li میرور فوتر */
    private const ITEM = '<li><a href="{ES_HREF}" class="transition-colors hover:text-emerald-700">{ES_LABEL}</a></li>';

    /** لیست کامل با wrap میرور فوتر */
    private const WRAP = '<ul class="space-y-2.5 text-[13px] text-slate-600">{ES_ITEMS}</ul>';

    /**
     * رندر لیست تک‌سطحی از آرایه لینک‌ها
     *
     * @param list<array{label:string,href:string}> $links
     * @param bool $with_wrap wrap میرور فوتر (false = فقط liها)
     */
    public static function render(array $links, bool $with_wrap = true): string {
        $items = '';
        foreach ($links as $link) {
            $items .= strtr(self::ITEM, [
                '{ES_HREF}'  => esc_url((string) ($link['href'] ?? '')),
                '{ES_LABEL}' => esc_html((string) ($link['label'] ?? '')),
            ]);
        }
        if (!$with_wrap) {
            return $items;
        }
        return $items === '' ? '' : str_replace('{ES_ITEMS}', $items, self::WRAP);
    }

    /**
     * رندر از موقعیت منو با fallback
     *
     * @param list<array{label:string,href:string}> $fallback
     */
    public static function render_menu(string $location, array $fallback, bool $with_wrap = true): string {
        $items = wp_get_nav_menu_items($location);
        $links = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                if ((int) ($item->menu_item_parent ?? 0) === 0) {
                    $links[] = ['label' => (string) $item->title, 'href' => (string) $item->url];
                }
            }
        }
        return self::render($links === [] ? $fallback : $links, $with_wrap);
    }
}
