<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Helpers;
final class ViewCounter
{
    public const KEY='number2-viewc'; public const LEGACY_KEY='number-viewc';
    public static function get(int $postId): int { $v=get_post_meta($postId,self::KEY,true); if($v===''||$v===false) $v=get_post_meta($postId,self::LEGACY_KEY,true); return max(0,(int)$v); }
    public static function increment(int $postId): int { $next=self::get($postId)+1; update_post_meta($postId,self::KEY,$next); return $next; }
    /** Count a frontend singular view, skipping admins/previews/bots by user-agent heuristics. */
    public static function maybeCount(int $postId): ?int {
        if(is_admin()||is_preview()||current_user_can('manage_options')) return null;
        $ua=isset($_SERVER['HTTP_USER_AGENT'])?sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])):'';
        if($ua===''||preg_match('/bot|crawl|spider|slurp/i',$ua)) return null;
        return self::increment($postId);
    }
}
