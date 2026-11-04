<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Core;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class ProjectRedirect implements ServiceInterface
{
    public const LEGACY_PAGE_ID=8366;
    public function register(): void { add_action('template_redirect', [$this,'maybeRedirect']); }
    public function maybeRedirect(): void {
        if (is_admin() || !is_page()) return;
        if (!self::shouldRedirect((int)get_queried_object_id(), (string)(get_queried_object()->post_name ?? ''))) return;
        wp_safe_redirect(home_url('/projects/'), 301); exit;
    }
    public static function shouldRedirect(int $pageId, string $slug): bool { return $slug==='project' || $pageId===self::LEGACY_PAGE_ID; }
    public static function target(): string { return '/projects/'; }
}
