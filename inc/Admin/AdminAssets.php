<?php
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

use ErfanSanat\Theme\Contracts\ServiceInterface;

final class AdminAssets implements ServiceInterface
{
    private const THEME_POST_TYPES = ['post', 'page', 'product', 'projects'];
    private const THEME_TAXONOMIES = ['category', 'product_cat', 'product_tag', 'product_brand', 'project_type', 'city'];

    public function register(): void
    {
        add_action('admin_enqueue_scripts', [$this, 'enqueue']);
    }

    /**
     * Decide whether the theme's local admin typography should be loaded for a screen.
     */
    public static function shouldEnqueueStyle(string $hookSuffix, ?string $postType = null, ?string $taxonomy = null): bool
    {
        if ($hookSuffix === 'toplevel_page_' . ThemeOptions::PAGE_SLUG) {
            return true;
        }

        if (in_array($hookSuffix, ['post.php', 'post-new.php'], true)) {
            return in_array($postType, self::THEME_POST_TYPES, true);
        }

        if (in_array($hookSuffix, ['edit-tags.php', 'term.php'], true)) {
            return in_array($taxonomy, self::THEME_TAXONOMIES, true);
        }

        return $hookSuffix === 'edit.php'
            && in_array($postType, ['product', 'projects'], true);
    }

    public function enqueue(string $hookSuffix): void
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        $postType = is_object($screen) && isset($screen->post_type) ? (string) $screen->post_type : null;
        $taxonomy = is_object($screen) && isset($screen->taxonomy) ? (string) $screen->taxonomy : null;

        if (!self::shouldEnqueueStyle($hookSuffix, $postType, $taxonomy)) {
            return;
        }

        wp_enqueue_style(
            'erfansanat-admin',
            ERFANSANAT_THEME_URI . '/assets/admin/css/admin-dashboard.css',
            [],
            (string) filemtime(ERFANSANAT_THEME_DIR . '/assets/admin/css/admin-dashboard.css')
        );

        // Scripts and media frame are exclusive to the theme settings dashboard.
        if ($hookSuffix !== 'toplevel_page_' . ThemeOptions::PAGE_SLUG) {
            return;
        }

        wp_enqueue_script(
            'erfansanat-admin',
            ERFANSANAT_THEME_URI . '/assets/admin/js/admin-dashboard.js',
            [],
            (string) filemtime(ERFANSANAT_THEME_DIR . '/assets/admin/js/admin-dashboard.js'),
            true
        );
        wp_localize_script('erfansanat-admin', 'esAdminData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('erfansanat_save_options'),
        ]);
        wp_enqueue_media();
    }
}
