<?php
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Contracts\ServiceInterface;

final class ThemeOptions implements ServiceInterface
{
    public const PAGE_SLUG = 'erfansanat-theme';
    public const CAPABILITY = 'manage_options';

    public function register(): void
    {
        add_action('admin_menu', [$this, 'addMenu']);
    }

    /** @return array<string,string> */
    public static function tabs(): array
    {
        return [
            'header' => 'هدر و نوار اعلان',
            'footer-contact' => 'فوتر و تماس',
            'appearance' => 'ظاهر و اسکریپت‌ها',
            'archives' => 'آرشیوها و فیلترها',
            'sections' => 'سکشن‌های صفحات',
            'tools' => 'ابزارها',
        ];
    }

    public function addMenu(): void
    {
        add_menu_page(
            'داشبورد عرفان صنعت',
            'داشبورد عرفان صنعت',
            self::CAPABILITY,
            self::PAGE_SLUG,
            [$this, 'renderPage'],
            'dashicons-admin-customizer',
            58
        );
    }

    public function renderPage(): void
    {
        if (!current_user_can(self::CAPABILITY)) {
            wp_die(esc_html__('دسترسی غیرمجاز.', 'erfansanat'));
        }

        $tabs = self::tabs();
        $rawTab = isset($_GET['tab']) && is_scalar($_GET['tab']) ? (string) wp_unslash($_GET['tab']) : 'header'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        $active = sanitize_key($rawTab);
        if (!isset($tabs[$active])) {
            $active = 'header';
        }

        $panelId = 'es-admin-panel-' . $active;
        $headingId = 'es-admin-heading-' . $active;

        echo '<div class="wrap es-admin-wrap" dir="rtl">';
        echo '<header class="es-admin-header">';
        echo '<div class="es-admin-brand"><span class="es-admin-brand__mark dashicons dashicons-admin-customizer" aria-hidden="true"></span><div><p class="es-admin-eyebrow">' . esc_html__('کنترل‌پنل قالب', 'erfansanat') . '</p><h1>' . esc_html__('داشبورد عرفان صنعت اصفهان', 'erfansanat') . '</h1><p class="es-admin-subtitle">' . esc_html__('تنظیمات هویت برند، محتوای سایت و ابزارهای مدیریتی را از یک نقطه کنترل کنید.', 'erfansanat') . '</p></div></div>';
        echo '<div class="es-admin-header__meta"><span class="es-admin-status-dot" aria-hidden="true"></span><span>' . esc_html__('مدیریت امن تنظیمات', 'erfansanat') . '</span></div>';
        echo '</header>';

        echo '<nav class="es-admin-tabs" data-es-tabs aria-label="' . esc_attr__('بخش‌های داشبورد قالب', 'erfansanat') . '">';
        foreach ($tabs as $id => $label) {
            $url = admin_url('admin.php?page=' . self::PAGE_SLUG . '&tab=' . $id);
            $activeClass = $id === $active ? ' is-active' : '';
            $ariaCurrent = $id === $active ? ' aria-current="page"' : '';
            printf(
                '<a class="es-admin-tab%s" data-es-tab="%s" href="%s"%s><span class="es-admin-tab__label">%s</span><span class="es-admin-tab__arrow" aria-hidden="true">&#8592;</span></a>',
                esc_attr($activeClass),
                esc_attr($id),
                esc_url($url),
                $ariaCurrent,
                esc_html($label)
            );
        }
        echo '</nav>';

        printf('<section class="es-admin-panel" id="%s" data-es-panel="%s" aria-labelledby="%s">', esc_attr($panelId), esc_attr($active), esc_attr($headingId));
        echo '<div class="es-admin-panel__heading"><div><p class="es-admin-eyebrow">' . esc_html__('بخش تنظیمات', 'erfansanat') . '</p><h2 id="' . esc_attr($headingId) . '">' . esc_html($tabs[$active]) . '</h2><p>' . esc_html__('مقادیر موردنیاز را بررسی کنید و پس از پایان، تغییرات را ذخیره کنید.', 'erfansanat') . '</p></div><span class="es-admin-panel__badge">' . esc_html__('صفحه فعال', 'erfansanat') . '</span></div>';
        do_action('erfansanat_admin_tab_content_' . $active);
        echo '</section></div>';
    }
}
