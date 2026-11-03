<?php
/**
 * DeBloat — strip Gutenberg block styles, emojis and extra <head> tags
 * so the front-end ships only the static theme's own assets.
 *
 * @package ErfanSanat\Theme
 */

declare(strict_types=1);

namespace ErfanSanat\Theme;

if (!defined('ABSPATH')) {
    exit;
}

final class DeBloat
{
    public static function hooks(): void
    {
        // 1. Dequeue block-library CSS/JS and inline global styles.
        add_action('wp_enqueue_scripts', [self::class, 'dequeue_blocks'], 100);
        add_action('wp_enqueue_scripts', [self::class, 'dequeue_emoji'], 100);
        add_filter('wp_print_styles', [self::class, 'remove_block_css'], 100);

        // 2. Kill emojis entirely.
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        add_filter('emoji_svg_url', '__return_false');

        // 3. Clean up <head> noise.
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'wlwmanifest');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'rest_output_link_wp_head');
        remove_action('wp_head', 'wp_oembed_add_discovery_links');
        remove_action('wp_head', 'rel_canonical');
        remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
        remove_action('wp_head', 'feed_links_extra', 3);

        // 4. Disable wp-embed script on the front-end.
        add_action('wp_footer', [self::class, 'deregister_embed']);
    }

    public static function dequeue_blocks(): void
    {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('global-styles');
        wp_dequeue_style('classic-theme-styles');
    }

    public static function dequeue_emoji(): void
    {
        wp_deregister_script('emoji');
    }

    public static function remove_block_css(): void
    {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('global-styles');
    }

    public static function deregister_embed(): void
    {
        wp_deregister_script('wp-embed');
    }
}
