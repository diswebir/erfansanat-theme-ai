<?php
declare(strict_types=1);
namespace ErfanSanat\Theme\Home;
use ErfanSanat\Theme\Contracts\ServiceInterface;
final class DefaultSections implements ServiceInterface
{
    /** @return array<int,string> ordered default home section template types */
    public static function types(): array {
        return ['section-01-hero','section-02-projects-intro','section-03-featured-projects','section-04-about-stats','section-05-banner','section-06-timeline','section-07-featured-products','section-08-catalog-banner','section-09-services-tabs','section-10-quality','section-11-warranty-banner','section-12-clients','section-13-latest-posts','section-14-cta','section-15-contact-card'];
    }
    public function register(): void { add_filter('erfansanat_default_sections', [$this, 'defaults'], 10, 2); }
    /** @param array<int,mixed> $defaults @return array<int,array<string,mixed>> */
    public function defaults(array $defaults, int $postId): array {
        if (!is_front_page() && (int) get_option('page_on_front') !== $postId) return $defaults;
        return array_map(static fn(string $t, int $i): array => ['id' => $t, 'type' => $t, 'enabled' => true, 'order' => $i, 'fields' => []], self::types(), array_keys(self::types()));
    }
}
