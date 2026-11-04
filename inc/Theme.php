<?php
/**
 * Main theme setup.
 *
 * @package ErfanSanat\Theme
 */

namespace ErfanSanat\Theme;

defined('ABSPATH') || exit;

/**
 * Registers theme-level WordPress features.
 */
final class Theme
{
    /**
     * Attach setup at the correct point in the WordPress lifecycle.
     */
    public static function register(): void
    {
        DeBloat::register();
        AssetManager::register();
        TemplateParts::register();
        HomepageData::register();
        Ajax\Filters::register();
        Forms\Submission::register();
        Admin\Forms::register();
        Admin\ThemeOptions::register();
        Admin\SectionManager::register();
        ContentTypes::register();
        Admin\ProductMetaboxes::register();
        Admin\ProjectMetaboxes::register();
        Admin\TermMetaboxes::register();
        Admin\ContentMeta::register();

        if (function_exists('did_action') && did_action('after_setup_theme')) {
            self::setup();
            return;
        }

        add_action('after_setup_theme', [self::class, 'setup']);
    }

    /**
     * Register core theme supports.
     */
    public static function setup(): void
    {
        Navigation\Navigation::register_menus();
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('woocommerce');
        add_theme_support(
            'html5',
            [
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'style',
                'script',
                'navigation-widgets',
            ]
        );
    }
}
