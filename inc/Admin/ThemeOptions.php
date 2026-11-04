<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class ThemeOptions implements ServiceInterface
{
    public const PAGE_SLUG = 'erfansanat-theme';
    public const CAPABILITY = 'manage_options';
    public function register(): void { add_action('admin_menu', [$this, 'addMenu']); }
    /** @return array<string,string> */
    public static function tabs(): array {
        return [
            'header' => 'هدر و نوار اعلان',
            'footer-contact' => 'فوتر و تماس',
            'appearance' => 'ظاهر و اسکریپت‌ها',
            'archives' => 'آرشیوها و فیلترها',
            'sections' => 'سکشن‌های صفحات',
            'tools' => 'ابزارها',
        ];
    }
    public function addMenu(): void {
        add_menu_page('داشبورد عرفان صنعت','داشبورد عرفان صنعت',self::CAPABILITY,self::PAGE_SLUG,[$this,'renderPage'],'dashicons-admin-customizer',58);
    }
    public function renderPage(): void {
        if (!current_user_can(self::CAPABILITY)) { wp_die(esc_html__('دسترسی غیرمجاز.','erfansanat')); }
        $tabs = self::tabs(); $active = isset($_GET['tab']) ? sanitize_key(wp_unslash($_GET['tab'])) : 'header'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (!isset($tabs[$active])) $active = 'header';
        echo '<div class="wrap es-admin-wrap" dir="rtl">';
        echo '<h1>'.esc_html__('داشبورد عرفان صنعت اصفهان','erfansanat').'</h1>';
        echo '<nav class="es-admin-tabs" data-es-tabs>';
        foreach ($tabs as $id=>$label) {
            $url = admin_url('admin.php?page='.self::PAGE_SLUG.'&tab='.$id);
            printf('<a class="es-admin-tab%s" data-es-tab="%s" href="%s">%s</a>', $id===$active?' is-active':'', esc_attr($id), esc_url($url), esc_html($label));
        }
        echo '</nav>';
        printf('<section class="es-admin-panel" data-es-panel="%s">', esc_attr($active));
        do_action('erfansanat_admin_tab_content_'.$active);
        echo '</section></div>';
    }
}
