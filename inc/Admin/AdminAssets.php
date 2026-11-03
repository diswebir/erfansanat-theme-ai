<?php
/**
 * دارایی‌های داشبورد — فقط در صفحه تنظیمات قالب بارگذاری می‌شوند
 *
 * @package ErfanSanat
 */
declare(strict_types=1);

namespace ErfanSanat\Theme\Admin;

final class AdminAssets {

    public static function init(): void {
        // هوک از ThemeOptions::init ثبت می‌شود — این کلاس stateless است
    }

    public static function enqueue(string $hook_suffix): void {
        if (!ThemeOptions::is_options_screen()) {
            return;
        }
        unset($hook_suffix);

        wp_enqueue_style(
            'erfansanat-admin',
            ERFANSANAT_URI . 'assets/admin/admin.css',
            [],
            \ErfanSanat\Theme\Frontend\AssetManager::version('assets/admin/admin.css')
        );
        wp_enqueue_script(
            'erfansanat-admin',
            ERFANSANAT_URI . 'assets/admin/admin.js',
            [],
            \ErfanSanat\Theme\Frontend\AssetManager::version('assets/admin/admin.js'),
            true
        );
        wp_localize_script('erfansanat-admin', 'ESAdmin', [
            'ajaxUrl'    => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce(ThemeOptions::NONCE_ACTION),
            'action'     => ThemeOptions::AJAX_ACTION,
            'savedText'  => __('ذخیره شد ✓', 'erfansanat'),
            'errorText'  => __('خطا در ذخیره‌سازی؛ دوباره تلاش کنید.', 'erfansanat'),
            'networkText' => __('خطای شبکه؛ اتصال اینترنت را بررسی کنید.', 'erfansanat'),
        ]);
    }
}
