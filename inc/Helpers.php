<?php
/**
 * Global theme helpers.
 *
 * @package ErfanSanat\Theme
 */

namespace {
    defined('ABSPATH') || exit;

    if (!function_exists('es_option')) {
        /**
         * Retrieve a nested theme option with schema defaults and a static request cache.
         *
         * @param string $key Dot-separated option path.
         * @param mixed  $default Fallback when neither stored nor schema default exists.
         * @return mixed
         */
        function es_option(string $key, $default = null)
        {
            if (empty($GLOBALS['es_theme_option_cache_loaded'])) {
                $stored = get_option('erfansanat_theme_options', []);
                $schemaDefaults = class_exists(\ErfanSanat\Theme\Admin\ThemeOptions::class)
                    ? \ErfanSanat\Theme\Admin\ThemeOptions::defaults()
                    : [];
                $GLOBALS['es_theme_option_cache'] = array_replace_recursive(
                    is_array($schemaDefaults) ? $schemaDefaults : [],
                    is_array($stored) ? $stored : []
                );
                $GLOBALS['es_theme_option_cache_loaded'] = true;
            }

            $value = $GLOBALS['es_theme_option_cache'];
            foreach (explode('.', $key) as $segment) {
                if (!is_array($value) || !array_key_exists($segment, $value)) {
                    return $default;
                }
                $value = $value[$segment];
            }

            return null === $value ? $default : $value;
        }
    }

    if (!function_exists('es_clear_theme_option_cache')) {
        /**
         * Clear the request cache when the single theme-options record changes.
         *
         * @param string $option Updated option name.
         */
        function es_clear_theme_option_cache(string $option): void
        {
            if ('erfansanat_theme_options' === $option) {
                $GLOBALS['es_theme_option_cache_loaded'] = false;
                $GLOBALS['es_theme_option_cache'] = [];
            }
        }
    }

    add_action('added_option', 'es_clear_theme_option_cache', 10, 1);
    add_action('updated_option', 'es_clear_theme_option_cache', 10, 1);
    add_action('deleted_option', 'es_clear_theme_option_cache', 10, 1);
}
