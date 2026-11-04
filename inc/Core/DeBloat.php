<?php
/**
 * Zero-Bloat WordPress Optimization Service.
 *
 * Strips Gutenberg block styles/scripts, global-styles, emojis, and unnecessary wp_head tags
 * to guarantee 100% clean DOM output and maximum Lighthouse/Core Web Vitals speed.
 *
 * @package ErfanSanat\Theme\Core
 */

declare(strict_types=1);

namespace ErfanSanat\Theme\Core;

use ErfanSanat\Theme\Contracts\ServiceInterface;

final class DeBloat implements ServiceInterface
{
    public function register(): void
    {
        // Enforce Classic (Non-Block) editing experience
        add_filter('use_block_editor_for_post', '__return_false', 100);
        add_filter('use_block_editor_for_post_type', '__return_false', 100);
        add_filter('use_widgets_block_editor', '__return_false', 100);

        add_action('init', [$this, 'cleanupHeadAndEmojis']);
        add_action('wp_enqueue_scripts', [$this, 'dequeueBlockAndBloatAssets'], 100);
        add_action('wp_footer', [$this, 'dequeueFooterBlockAssets'], 1);
    }

    /**
     * Remove unnecessary wp_head meta/link tags, emojis, and WordPress 6.x/7.x block style hooks.
     */
    public function cleanupHeadAndEmojis(): void
    {
        // Emojis removal (WP 6.x & WP 7.x)
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_action('wp_enqueue_scripts', 'wp_enqueue_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        add_filter('emoji_svg_url', '__return_false');

        // Head discovery & generator metadata cleanup
        remove_action('wp_head', 'wp_generator');
        add_filter('the_generator', '__return_empty_string');
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wp_shortlink_wp_head', 10);
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('template_redirect', 'rest_output_link_header', 11);
        remove_action('wp_head', 'wp_oembed_add_discovery_links', 4);
        remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
        remove_action('wp_head', 'wp_oembed_add_host_js');

        // Frontend-only Gutenberg / Global Styles / Auto-sizes bloat cleanup
        if (!is_admin()) {
            remove_action('wp_head', 'wp_enqueue_img_auto_sizes_contain_css_fix', 0);
            remove_action('wp_head', 'wp_print_auto_sizes_contain_css_fix', 1);
            remove_action('wp_head', 'wp_print_font_faces', 50);

            remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
            remove_action('wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles');
            remove_action('wp_enqueue_scripts', 'wp_common_block_scripts_and_styles');
            remove_action('wp_enqueue_scripts', 'wp_enqueue_block_style_variation_styles', 1);
            remove_action('wp_enqueue_scripts', 'wp_enqueue_block_custom_css', 1);
            remove_action('wp_enqueue_scripts', 'wp_enqueue_stored_styles');
            remove_action('wp_enqueue_scripts', 'wp_enqueue_block_template_skip_link');

            remove_action('enqueue_block_assets', 'wp_enqueue_registered_block_scripts_and_styles');
            remove_action('wp_template_enhancement_output_buffer_started', 'wp_hoist_late_printed_styles');

            remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
            remove_action('wp_footer', 'wp_enqueue_stored_styles', 1);
            remove_action('wp_footer', 'the_block_template_skip_link');
            remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');
        }
    }

    /**
     * Explicitly dequeue & deregister any remaining Gutenberg / WooCommerce Blocks CSS on frontend.
     */
    public function dequeueBlockAndBloatAssets(): void
    {
        if (is_admin()) {
            return;
        }

        $handlesToRemove = [
            'wp-block-library',
            'wp-block-library-theme',
            'wc-blocks-style',
            'wc-blocks-vendors-style',
            'wc-all-blocks-style',
            'global-styles',
            'classic-theme-styles',
            'wp-block-styles-placeholder',
            'wp-global-styles-placeholder',
            'wp-emoji-styles',
            'wp-img-auto-sizes-contain',
            'core-block-supports',
        ];

        foreach ($handlesToRemove as $handle) {
            wp_dequeue_style($handle);
            wp_deregister_style($handle);
        }
    }

    /**
     * Prevent late-enqueued block supports / global styles in wp_footer.
     */
    public function dequeueFooterBlockAssets(): void
    {
        if (is_admin()) {
            return;
        }

        wp_dequeue_style('core-block-supports');
        wp_dequeue_style('global-styles');
    }
}
