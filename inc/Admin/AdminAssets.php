<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Admin;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class AdminAssets implements ServiceInterface
{
    public function register(): void { add_action('admin_enqueue_scripts', [$this,'enqueue']); }
    public function enqueue(string $hookSuffix): void {
        if ($hookSuffix !== 'toplevel_page_'.ThemeOptions::PAGE_SLUG) return;
        wp_enqueue_style('erfansanat-admin', ERFANSANAT_THEME_URI.'/assets/admin/css/admin-dashboard.css', [], (string)filemtime(ERFANSANAT_THEME_DIR.'/assets/admin/css/admin-dashboard.css'));
        wp_enqueue_script('erfansanat-admin', ERFANSANAT_THEME_URI.'/assets/admin/js/admin-dashboard.js', [], (string)filemtime(ERFANSANAT_THEME_DIR.'/assets/admin/js/admin-dashboard.js'), true);
        wp_localize_script('erfansanat-admin','esAdminData',['ajaxUrl'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('erfansanat_save_options')]);
        wp_enqueue_media();
    }
}
